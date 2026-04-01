"""업무시간 기반 로그 API 시뮬레이터 (prompt.md §4)."""

from __future__ import annotations

import logging
import os
import re
import random
import sys
import threading
import time
import urllib.parse
import urllib.request
import xml.etree.ElementTree as ET
from datetime import date, datetime, time as dtime, timedelta
from pathlib import Path
from typing import Any

from .caller import (
    USE_CREATE,
    USE_DELETE,
    USE_INOUT_TIME,
    USE_LOGIN,
    USE_LOGOUT,
    USE_READ,
    USE_UPDATE,
    LogApiCaller,
    format_log_dt,
)

# 업무·점심 (로컬 시각)
WORK_START = dtime(9, 20)
WORK_END = dtime(17, 30)
LUNCH_START = dtime(12, 0)
LUNCH_END = dtime(13, 30)
# 운영 종료(로그아웃 강제 및 시뮬레이터 종료)
STOP_TIME = dtime(18, 0)

# 세션: 로그인 후 유지 시간(초)
SESSION_MIN_SEC = 5 * 60
SESSION_MAX_SEC = 5 * 60 * 60

# CRUD 주기(초): 15~20분 (최소 10분 간격 조건보다 큼)
CRUD_MIN_SEC = 15 * 60
CRUD_MAX_SEC = 20 * 60

# API 키당 전송 최소 간격(초) — prompt.md 주의사항
MIN_INTERVAL_BETWEEN_SENDS_SEC = 10 * 60

# 첫 로그인 대기 범위(초): 1~3분
FIRST_LOGIN_DELAY_MIN_SEC = 1 * 60
FIRST_LOGIN_DELAY_MAX_SEC = 3 * 60

# 여러 사용자가 동시에 로그인하는(거의 같은 시각 호출) 현상을 줄이기 위한
# 최소 로그인 시작 간격(초).
MIN_LOGIN_SEPARATION_SEC = 3 * 60

# 로그아웃 후 같은 사용자의 다음 로그인 재시도 간격(초)
RELOGIN_MIN_SEC = 5 * 60
RELOGIN_MAX_SEC = 2 * 60 * 60

# 휴일 목록 (ISO: YYYY-MM-DD). holidayInfo.md 기반으로 실행 1회 갱신합니다.
HOLIDAY_DATES: set[str] = set()


def _iso_date(d: date) -> str:
    return d.strftime("%Y-%m-%d")


def _locdate_to_iso(locdate: str) -> str:
    locdate = (locdate or "").strip()
    if len(locdate) != 8 or not locdate.isdigit():
        return ""
    return f"{locdate[0:4]}-{locdate[4:6]}-{locdate[6:8]}"


def load_holiday_service_info(holiday_info_path: Path) -> tuple[str, str]:
    """holidayInfo.md에서 서비스 Endpoint와 serviceKey를 파싱합니다."""
    raw = holiday_info_path.read_text(encoding="utf-8")
    m_endp = re.search(r"##\s*EndPoint:\s*(\S+)", raw)
    m_key = re.search(r"##\s*일반 인증키:\s*([0-9a-fA-F]+)", raw)
    if not m_endp or not m_key:
        raise RuntimeError(f"holidayInfo.md 파싱 실패: {holiday_info_path}")
    return m_endp.group(1).strip(), m_key.group(1).strip()


def fetch_month_holidays_from_kasi(
    service_endpoint: str,
    service_key: str,
    year: int,
    month: int,
) -> set[str]:
    """KASI SpcdeInfoService.getRestDeInfo에서 isHoliday=Y 날짜를 가져옵니다."""
    base = service_endpoint.replace("https://", "http://").rstrip("/")
    op = "getRestDeInfo"
    # 가이드 예시(REST URI)처럼 ServiceKey / solYear / solMonth / numOfRows 사용
    url = (
        f"{base}/{op}"
        f"?solYear={year}&solMonth={month:02d}"
        f"&ServiceKey={urllib.parse.quote(service_key)}"
        f"&pageNo=1&numOfRows=1000"
    )
    req = urllib.request.Request(url, method="GET")
    with urllib.request.urlopen(req, timeout=40) as r:
        xml_text = r.read().decode("utf-8", errors="replace")

    root = ET.fromstring(xml_text)
    out: set[str] = set()
    for item in root.findall(".//item"):
        locdate = (item.findtext("locdate") or "").strip()
        is_hol = (item.findtext("isHoliday") or "").strip().upper()
        if is_hol == "Y":
            iso = _locdate_to_iso(locdate)
            if iso:
                out.add(iso)
    return out


def update_holiday_file_once(*, log_dir: Path, holiday_info_path: Path, year: int) -> set[str]:
    """프로세스 시작 1회: 공휴일 파일 갱신 + 메모리에 로드."""
    service_endpoint, service_key = load_holiday_service_info(holiday_info_path)
    out: set[str] = set()
    # 1~12월 전체를 갱신 (연중 휴일 기준이면 안전)
    for month in range(1, 13):
        out |= fetch_month_holidays_from_kasi(service_endpoint, service_key, year, month)

    holiday_dir = log_dir / "holidays"
    holiday_dir.mkdir(parents=True, exist_ok=True)
    holiday_file = holiday_dir / f"holidays_{year}.txt"
    tmp = holiday_file.with_suffix(".txt.tmp")
    tmp.write_text("\n".join(sorted(out)) + ("\n" if out else ""), encoding="utf-8")
    os.replace(tmp, holiday_file)
    return out


def load_holiday_file(holiday_file: Path) -> set[str]:
    """이미 생성된 공휴일 파일을 로드합니다."""
    if not holiday_file.is_file():
        return set()
    txt = holiday_file.read_text(encoding="utf-8")
    return {line.strip() for line in txt.splitlines() if line.strip()}


def is_holiday_date(d: date) -> bool:
    if not HOLIDAY_DATES:
        return False
    return _iso_date(d) in HOLIDAY_DATES

# 조회/비조회(= CRUD/입출고) 비율
# "조회 10회당 1~2회 정도"를 만족하도록, 세션 주기마다
# DO6003(조회)를 1회 호출하고, 그 사이에 추가로 비조회 액션을 낮은 확률로 0~1회 호출합니다.
NON_READ_PROB_MIN = 0.1
NON_READ_PROB_MAX = 0.2

# "비조회 액션" 중 DO6007(입출고)을 선택할 비중
INOUT_PROB_WITHIN_NON_READ = 0.2


class UserSendLimiter:
    """사용자(=sysUser)별 최소 전송 간격 제한."""

    def __init__(self, min_sec: float) -> None:
        self._min = min_sec
        self._global_lock = threading.Lock()
        self._last_by_user: dict[str, float] = {}
        self._lock_by_user: dict[str, threading.Lock] = {}

    def wait_and_mark(
        self,
        user_id: str,
        *,
        ignore_min_interval: bool = False,
        stop_event: threading.Event | None = None,
    ) -> None:
        with self._global_lock:
            lock = self._lock_by_user.get(user_id)
            if lock is None:
                lock = threading.Lock()
                self._lock_by_user[user_id] = lock

        with lock:
            now = time.monotonic()
            if ignore_min_interval:
                self._last_by_user[user_id] = now
                return

            last = self._last_by_user.get(user_id)
            if last is not None:
                need = self._min - (now - last)
                if need > 0:
                    # 중지 요청이 오면 기다리지 않고 즉시 마킹
                    if stop_event is not None and stop_event.is_set():
                        self._last_by_user[user_id] = now
                        return
                    if stop_event is not None:
                        stop_event.wait(timeout=need)
                    else:
                        time.sleep(need)
            self._last_by_user[user_id] = time.monotonic()


def _today_at(t: dtime) -> datetime:
    d = date.today()
    return datetime.combine(d, t)


def is_lunch(dt: datetime) -> bool:
    t = dt.time()
    return LUNCH_START <= t < LUNCH_END


def is_work_time(dt: datetime) -> bool:
    t = dt.time()
    return WORK_START <= t <= WORK_END


def can_send_api(dt: datetime) -> bool:
    return is_work_time(dt) and not is_lunch(dt) and not is_holiday_date(dt.date())


def max_login_stagger_seconds(now: datetime | None = None) -> float:
    """오늘 업무 종료 시각까지 남은 시간 기반 로그인 지연 상한(초)."""
    now = now or datetime.now()
    day_end = datetime.combine(now.date(), WORK_END)
    room = (day_end - now).total_seconds()
    return max(0.0, min(float(FIRST_LOGIN_DELAY_MAX_SEC), room))


def random_login_delay_seconds(now: datetime | None = None) -> float:
    """첫 로그인 대기: 기본 1~3분, 업무 종료 임박 시 남은 시간으로 자동 축소."""
    now = now or datetime.now()
    upper = max_login_stagger_seconds(now)
    if upper <= 0:
        return 0.0
    lower = min(float(FIRST_LOGIN_DELAY_MIN_SEC), upper)
    return random.uniform(lower, upper)


def next_relogin_at(now: datetime) -> datetime:
    sec = random.uniform(RELOGIN_MIN_SEC, RELOGIN_MAX_SEC)
    return now + timedelta(seconds=sec)


def sleep_until_send_window(
    log: logging.Logger,
    stop_event: threading.Event | None = None,
) -> None:
    """API를 보내도 될 때까지 대기 (짧은 슬립으로 종료 가능)."""
    while True:
        if stop_event is not None and stop_event.is_set():
            return
        now = datetime.now()
        if can_send_api(now):
            return
        if now.time() < WORK_START:
            target = _today_at(WORK_START)
        elif now.time() > WORK_END:
            target = _today_at(WORK_START) + timedelta(days=1)
        elif is_lunch(now):
            target = datetime.combine(now.date(), LUNCH_END)
        else:
            return
        wait = (target - now).total_seconds()
        log.debug("API 대기 %.0f초 (다음 가능 시각 근처)", max(1, wait))
        timeout = min(30.0, max(1.0, wait))
        if stop_event is not None:
            stop_event.wait(timeout=timeout)
        else:
            time.sleep(timeout)


def random_data_ugqty(use_se: str) -> str:
    """prompt.md: 로그인/아웃 0, 조회 수십kb, 등/수/삭/입출고 수kb."""
    if use_se in (USE_LOGIN, USE_LOGOUT):
        return "0"
    if use_se == USE_READ:
        return str(random.randint(10_000, 99_999))
    # DO6004~6006, DO6007
    return str(random.randint(1_000, 9_999))


def send_scheduled(
    caller: LogApiCaller,
    limiter: UserSendLimiter,
    user: dict[str, str],
    use_se: str,
    log: logging.Logger,
    *,
    require_ap1002: bool = True,
    stop_event: threading.Event | None = None,
    bypass_business_window: bool = False,
    ignore_user_min_interval: bool = False,
) -> bool:
    """API 전송.

    require_ap1002=True: recptnRsltCd=AP1002 일 때만 True.
    require_ap1002=False: DO6001 등 «호출이 끝까지 도달»하면 True(응답 코드와 무관). 예외 시만 False.
    """
    # 휴일에는 어떤 useSe도 호출하지 않습니다(로그인/CRUD/로그아웃 모두 스킵).
    if is_holiday_date(date.today()):
        log.info("휴일(%s) — send 스킵: useSe=%s user=%s", _iso_date(date.today()), use_se, user["id"])
        return False

    if stop_event is not None and stop_event.is_set() and not bypass_business_window:
        return False

    if not bypass_business_window:
        sleep_until_send_window(log, stop_event=stop_event)

    limiter.wait_and_mark(
        user["id"],
        ignore_min_interval=ignore_user_min_interval,
        stop_event=stop_event,
    )
    payload = {
        "logDt": format_log_dt(),
        "useSe": use_se,
        "sysUser": user["id"],
        "conectIp": user["ip"],
        "dataUsgqty": random_data_ugqty(use_se),
    }
    try:
        res = caller.send(payload)
        ok = caller.is_success(res)
        log.info("send %s %s ap1002=%s", use_se, user["id"], ok)
        if require_ap1002:
            return ok
        return True
    except Exception as e:
        log.warning("send %s %s 실패: %s", use_se, user["id"], e)
        return False


def run_session(
    caller: LogApiCaller,
    limiter: UserSendLimiter,
    user: dict[str, str],
    phase: str,
    login_delay_sec: float,
    log: logging.Logger,
    stop_event: threading.Event,
) -> None:
    """DO6001 호출 선행 후 DO6003~DO6007 → 종료 시 DO6002."""
    did_login = False
    if login_delay_sec > 0:
        log.info(
            "[%s] %s DO6001까지 %.0f초(%.1f분) 대기(사용자별 분산)",
            phase,
            user["id"],
            login_delay_sec,
            login_delay_sec / 60.0,
        )
        stop_event.wait(timeout=login_delay_sec)
        if stop_event.is_set() or datetime.now().time() >= STOP_TIME:
            return

    log.info("[%s] %s DO6001(로그인) 호출", phase, user["id"])
    if not send_scheduled(
        caller,
        limiter,
        user,
        USE_LOGIN,
        log,
        require_ap1002=False,
        stop_event=stop_event,
    ):
        log.warning(
            "[%s] %s DO6001 호출 실패(예외) — DO6003~DO6007·DO6002 생략",
            phase,
            user["id"],
        )
        return
    did_login = True

    session_len = random.uniform(SESSION_MIN_SEC, SESSION_MAX_SEC)
    end_at = datetime.now() + timedelta(seconds=session_len)
    cap = datetime.combine(date.today(), WORK_END)
    if end_at > cap:
        end_at = cap - timedelta(seconds=30)

    next_crud = datetime.now() + timedelta(
        seconds=random.uniform(CRUD_MIN_SEC, CRUD_MAX_SEC)
    )

    while datetime.now() < end_at:
        if stop_event.is_set() or datetime.now().time() >= STOP_TIME:
            break
        now = datetime.now()
        if not can_send_api(now):
            sleep_until_send_window(log, stop_event=stop_event)
            continue
        if now >= next_crud and now < end_at:
            # 세션 주기마다 조회(DO6003)는 항상 1회 호출
            if stop_event.is_set():
                break
            send_scheduled(caller, limiter, user, USE_READ, log, stop_event=stop_event)

            # "조회 10회당 1~2회 비조회" 목표: READ 1회 호출마다 낮은 확률로 CRUD/입출고를 1회 추가
            if random.random() < random.uniform(NON_READ_PROB_MIN, NON_READ_PROB_MAX):
                if random.random() < INOUT_PROB_WITHIN_NON_READ:
                    use_se = USE_INOUT_TIME  # DO6007
                else:
                    use_se = random.choice([USE_CREATE, USE_UPDATE, USE_DELETE])  # DO6004~DO6006
                if stop_event.is_set():
                    break
                send_scheduled(
                    caller,
                    limiter,
                    user,
                    use_se,
                    log,
                    stop_event=stop_event,
                )

            next_crud = datetime.now() + timedelta(
                seconds=random.uniform(CRUD_MIN_SEC, CRUD_MAX_SEC)
            )
        sleep_sec = min(
            15.0,
            max(
                1.0,
                min(
                    (next_crud - datetime.now()).total_seconds(),
                    (end_at - datetime.now()).total_seconds(),
                ),
            ),
        )
        stop_event.wait(timeout=sleep_sec)

    if did_login:
        force = stop_event.is_set() or datetime.now().time() >= STOP_TIME
        send_scheduled(
            caller,
            limiter,
            user,
            USE_LOGOUT,
            log,
            require_ap1002=False,
            stop_event=stop_event,
            bypass_business_window=force,
            ignore_user_min_interval=force,
        )


def pick_random_users(users: list[dict[str, str]]) -> list[dict[str, str]]:
    k = random.randint(1, len(users))
    return random.sample(users, k)


def spawn_sessions(
    caller: LogApiCaller,
    limiter: UserSendLimiter,
    users: list[dict[str, str]],
    phase: str,
    max_login_delay_sec: float,
    log: logging.Logger,
    stop_event: threading.Event,
    *,
    now: datetime | None = None,
) -> list[threading.Thread]:
    chosen = pick_random_users(users)
    threads: list[threading.Thread] = []
    now = now or datetime.now()
    upper = min(max_login_delay_sec, max_login_stagger_seconds(now))

    # 동시 로그인 확률을 낮추기 위해 로그인 호출 시작 시간을 분산
    if upper <= 0 or len(chosen) <= 1:
        delays = [0.0 for _ in chosen]
    else:
        delays = sorted(random.uniform(0, upper) for _ in chosen)
        # 인접한 로그인 시작 시간이 너무 가까우면 밀어넣기
        for i in range(1, len(delays)):
            if delays[i] - delays[i - 1] < MIN_LOGIN_SEPARATION_SEC:
                delays[i] = delays[i - 1] + MIN_LOGIN_SEPARATION_SEC
        if delays[-1] > upper:
            # 상한을 넘으면 균등 분산으로 폴백
            step = upper / (len(chosen) - 1)
            delays = [i * step for i in range(len(chosen))]

    for u, delay in zip(chosen, delays):

        def _run(user: dict[str, str] = u, d: float = delay) -> None:
            try:
                run_session(caller, limiter, user, phase, d, log, stop_event)
            except Exception as e:
                log.exception("세션 오류 %s: %s", user["id"], e)

        t = threading.Thread(target=_run, name=f"session-{phase}-{u['id']}", daemon=False)
        t.start()
        threads.append(t)
    log.info(
        "%s 세션 시작: %d명 %s (로그인 지연 최대 %.0f초)",
        phase,
        len(chosen),
        [x["id"] for x in chosen],
        upper,
    )
    return threads


def run_simulator_loop(
    cfg: dict[str, Any],
    config_path: Path,
    log_dir: Path | None,
    log: logging.Logger,
) -> None:
    users = cfg["users"]
    if not users:
        raise RuntimeError("사용자 목록이 비어 있습니다.")

    caller = LogApiCaller(cfg["crtfcKey"], local_log_dir=log_dir)
    limiter = UserSendLimiter(MIN_INTERVAL_BETWEEN_SENDS_SEC)

    stop_event = threading.Event()
    sessions_threads: list[threading.Thread] = []

    # 연 1회: 휴일 파일이 없을 때만 갱신 (있으면 로드만)
    project_root = Path(__file__).resolve().parent.parent
    holiday_info_path = project_root / "holidayInfo.md"
    effective_log_dir = log_dir if log_dir is not None else (project_root / "logs")
    year = date.today().year
    holiday_file = (effective_log_dir / "holidays" / f"holidays_{year}.txt")

    global HOLIDAY_DATES
    if holiday_file.is_file() and holiday_file.stat().st_size > 0:
        HOLIDAY_DATES = load_holiday_file(holiday_file)
        log.info("휴일 파일 로드 완료: %s (%d건)", holiday_file, len(HOLIDAY_DATES))
    else:
        try:
            HOLIDAY_DATES = update_holiday_file_once(
                log_dir=effective_log_dir,
                holiday_info_path=holiday_info_path,
                year=year,
            )
            log.info("휴일 파일 갱신 완료: %s (%d건)", holiday_file, len(HOLIDAY_DATES))
        except Exception as e:
            # 갱신이 필요한데 실패했으면 휴일 판단을 신뢰할 수 없으므로 중단
            log.error("휴일 파일 갱신 실패 — 시뮬레이터 중단: %s", e)
            return

    active_users: set[str] = set()
    next_login_map: dict[str, datetime] = {}
    state_lock = threading.Lock()

    now0 = datetime.now()
    for user in users:
        # 시작 직후 모두 동시에 붙지 않도록 각 사용자 첫 로그인 시각을 분산
        initial_delay = random_login_delay_seconds(now0)
        next_login_map[user["id"]] = now0 + timedelta(seconds=initial_delay)

    log.info(
        "시뮬레이터 시작 (업무 %s~%s, 점심 %s~%s, config=%s)",
        WORK_START,
        WORK_END,
        LUNCH_START,
        LUNCH_END,
        config_path,
    )

    while True:
        now = datetime.now()
        today = now.date()
        t = now.time()

        if t >= STOP_TIME and not stop_event.is_set():
            log.info("STOP_TIME(%s) 도달 — 살아있는 세션 로그아웃 후 종료", STOP_TIME)
            stop_event.set()
            break

        # 휴일이면 세션 생성 자체를 스킵 (API 호출 금지)
        if is_holiday_date(today):
            stop_event.wait(timeout=20)
            continue

        # 업무시간 중 아무때나 사용자별 로그인 가능
        if can_send_api(now):
            for user in users:
                uid = user["id"]
                with state_lock:
                    if uid in active_users:
                        continue
                    due = next_login_map.get(uid, now)
                    if now < due:
                        continue
                    active_users.add(uid)
                    # 로그인 호출 직전 짧은 지연을 사용자별로 랜덤 분산
                    login_delay = random_login_delay_seconds(now)

                def _run_user_session(
                    user_obj: dict[str, str] = user,
                    login_delay_sec: float = login_delay,
                ) -> None:
                    try:
                        run_session(
                            caller,
                            limiter,
                            user_obj,
                            "수시",
                            login_delay_sec,
                            log,
                            stop_event,
                        )
                    except Exception as e:
                        log.exception("세션 오류 %s: %s", user_obj["id"], e)
                    finally:
                        next_at = next_relogin_at(datetime.now())
                        with state_lock:
                            active_users.discard(user_obj["id"])
                            next_login_map[user_obj["id"]] = next_at
                        log.info(
                            "[수시] %s 세션 종료, 다음 로그인 예정시각=%s",
                            user_obj["id"],
                            next_at.strftime("%H:%M:%S"),
                        )

                t_user = threading.Thread(
                    target=_run_user_session,
                    name=f"session-dynamic-{uid}",
                    daemon=False,
                )
                t_user.start()
                sessions_threads.append(t_user)
                log.info("[수시] %s 세션 시작 예약(지연 %.0f초)", uid, login_delay)

        if t > WORK_END or t < WORK_START:
            if t > WORK_END:
                next_start = datetime.combine(today + timedelta(days=1), WORK_START)
            else:
                next_start = datetime.combine(today, WORK_START)
            wait = (next_start - now).total_seconds()
            log.info("업무 외 시간. 다음 시작(%s)까지 약 %.0f초", WORK_START, wait)
            timeout = min(max(wait, 30.0), 3600.0)
            # STOP_TIME에 도달하면 대기를 중단하고 즉시 종료 흐름으로 들어가야 함.
            stop_dt = datetime.combine(today, STOP_TIME)
            if stop_dt > now:
                timeout = min(timeout, (stop_dt - now).total_seconds())
            stop_event.wait(timeout=timeout)
        else:
            stop_event.wait(timeout=20)

    # 세션들이 DO6002 전송할 시간을 주고 종료
    for th in sessions_threads:
        th.join(timeout=300.0)


def daemonize(chdir: Path | None, log_file: Path | None) -> None:
    """Unix: 백그라운드 이중 포크."""
    if os.name != "posix":
        raise RuntimeError("데몬 모드는 Unix 계열에서만 지원합니다.")

    if os.fork():
        sys.exit(0)
    os.setsid()
    if os.fork():
        sys.exit(0)

    sys.stdout.flush()
    sys.stderr.flush()
    si = os.open(os.devnull, os.O_RDONLY)
    os.dup2(si, sys.stdin.fileno())
    os.close(si)
    if log_file:
        log_file.parent.mkdir(parents=True, exist_ok=True)
        so = os.open(str(log_file), os.O_WRONLY | os.O_CREAT | os.O_APPEND, 0o644)
        os.dup2(so, sys.stdout.fileno())
        os.dup2(so, sys.stderr.fileno())
        if so > 2:
            os.close(so)
    else:
        so = os.open(os.devnull, os.O_WRONLY)
        os.dup2(so, sys.stdout.fileno())
        os.dup2(so, sys.stderr.fileno())
        os.close(so)

    if chdir:
        os.chdir(chdir)


def write_pid(path: Path) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(str(os.getpid()), encoding="utf-8")
