"""CLI: 기본은 시뮬레이터 백그라운드. 단발 전송은 --once."""

from __future__ import annotations

import argparse
import fcntl
import json
import logging
import sys
from pathlib import Path

from .caller import USE_TEST, LogApiCaller, format_log_dt, load_config_from_markdown
from .simulator import daemonize, run_simulator_loop, write_pid


def _project_root() -> Path:
    return Path(__file__).resolve().parent.parent


def _setup_logging(log_path: Path | None, verbose: bool) -> None:
    level = logging.DEBUG if verbose else logging.INFO
    fmt = "%(asctime)s [%(levelname)s] %(message)s"
    handlers: list[logging.Handler] = []
    if log_path:
        log_path.parent.mkdir(parents=True, exist_ok=True)
        handlers.append(logging.FileHandler(log_path, encoding="utf-8"))
    else:
        handlers.append(logging.StreamHandler(sys.stderr))
    logging.basicConfig(level=level, format=fmt, handlers=handlers, force=True)


def main(argv: list[str] | None = None) -> int:
    root = _project_root()
    p = argparse.ArgumentParser(description="MES 로그 수집 API (Python)")
    p.add_argument(
        "--config",
        type=Path,
        default=root / "config.md",
        help="config.md 경로",
    )
    p.add_argument(
        "--log-dir",
        type=Path,
        default=root / "logs",
        help="logapi.log 및 시뮬레이터 로그 디렉터리",
    )
    p.add_argument(
        "--no-log",
        action="store_true",
        help="로컬 logapi.log 에 API 기록 안 함",
    )
    p.add_argument(
        "--once",
        action="store_true",
        help="단발 TEST(DO6999) 한 번만 전송 후 종료",
    )
    p.add_argument(
        "--dry-run",
        action="store_true",
        help="--once 와 함께: 페이로드만 출력",
    )
    p.add_argument(
        "--foreground",
        action="store_true",
        help="시뮬레이터를 포그라운드에서 실행 (로그는 stderr)",
    )
    p.add_argument(
        "--no-daemon",
        action="store_true",
        help="데몬으로 포크하지 않음 (--foreground 과 동일)",
    )
    p.add_argument(
        "--sim-log",
        type=Path,
        help="시뮬레이터 텍스트 로그 파일 (기본: log-dir/simulator.log)",
    )
    p.add_argument(
        "--pid-file",
        type=Path,
        help="PID 파일 (기본: log-dir/logapi_sim.pid, 데몬일 때만 기록)",
    )
    p.add_argument("-v", "--verbose", action="store_true", help="디버그 로그")
    args = p.parse_args(argv)

    cfg = load_config_from_markdown(args.config)
    if not cfg.get("users"):
        print("config.md 에 사용자가 없습니다.", file=sys.stderr)
        return 1

    log_dir_api = None if args.no_log else args.log_dir
    sim_log = args.sim_log if args.sim_log is not None else (args.log_dir / "simulator.log")
    pid_file = args.pid_file if args.pid_file is not None else (args.log_dir / "logapi_sim.pid")

    if args.once:
        user = cfg["users"][0]
        payload = {
            "logDt": format_log_dt(),
            "useSe": USE_TEST,
            "sysUser": user["id"],
            "conectIp": user["ip"],
            "dataUsgqty": "0",
        }
        if args.dry_run:
            print(json.dumps({"payload": payload, "crtfcKey_set": bool(cfg.get("crtfcKey"))}, ensure_ascii=False, indent=2))
            return 0
        caller = LogApiCaller(cfg["crtfcKey"], local_log_dir=log_dir_api)
        try:
            res = caller.send(payload)
        except Exception as e:
            print(str(e), file=sys.stderr)
            return 1
        print(json.dumps(res, ensure_ascii=False, indent=2))
        ok = caller.is_success(res)
        print("OK=" + ("yes" if ok else "no"), flush=True)
        return 0 if ok else 2

    foreground = args.foreground or args.no_daemon

    # 단일 인스턴스 실행 방지: pid-file 인접 lock을 잡지 못하면 종료
    # (cron으로 중복 실행되는 경우 동시 실행을 막기 위함)
    lock_path = pid_file.with_name(pid_file.name + ".lock")
    lock_fp = open(lock_path, "a+")
    try:
        fcntl.flock(lock_fp.fileno(), fcntl.LOCK_EX | fcntl.LOCK_NB)
    except BlockingIOError:
        print(
            f"이미 실행 중입니다. (lock={lock_path})",
            file=sys.stderr,
            flush=True,
        )
        return 0

    if not foreground:
        print(
            f"백그라운드 시뮬레이터 시작 → 로그: {sim_log}, PID 파일: {pid_file}",
            file=sys.stderr,
            flush=True,
        )
        try:
            daemonize(chdir=root, log_file=sim_log)
        except RuntimeError as e:
            print(str(e), file=sys.stderr)
            return 1
        write_pid(pid_file)
    # 데몬은 stderr가 simulator.log 로 연결됨
    _setup_logging(None, args.verbose)

    log = logging.getLogger("logapi_py.simulator")
    try:
        run_simulator_loop(cfg, args.config, log_dir_api, log)
    except KeyboardInterrupt:
        log.info("종료 요청")
        return 130
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
