"""MES 로그 수집 API 호출기 (sendLogData.json) — PHP logApiCaller.php 와 동등."""

from __future__ import annotations

import fcntl
import json
import re
import ssl
import urllib.error
import urllib.request
from dataclasses import dataclass
from datetime import datetime
from pathlib import Path
from typing import Any, Mapping

URL = "https://log.smart-factory.kr/apisvc/sendLogData.json"

USE_LOGIN = "DO6001"
USE_LOGOUT = "DO6002"
USE_READ = "DO6003"
USE_CREATE = "DO6004"
USE_UPDATE = "DO6005"
USE_DELETE = "DO6006"
USE_INOUT_TIME = "DO6007"
USE_TEST = "DO6999"

SUCCESS_CODE = "AP1002"
LOCAL_LOG_FILENAME = "logapi.log"


def format_log_dt(at: datetime | None = None) -> str:
    """로그일시: YYYY-MM-DD HH:MM:SS.mmm (밀리초 3자리)."""
    if at is None:
        at = datetime.now()
    ms = at.microsecond // 1000
    return f"{at.strftime('%Y-%m-%d %H:%M:%S')}.{ms:03d}"


def load_config_from_markdown(path: str | Path) -> dict[str, Any]:
    """config.md에서 인증키·사용자(ID, IP) 목록을 읽습니다."""
    p = Path(path)
    if not p.is_file():
        raise FileNotFoundError(f"config 파일을 읽을 수 없습니다: {path}")
    raw = p.read_text(encoding="utf-8")

    users: list[dict[str, str]] = []
    for m in re.finditer(r"-\s*ID:\s*([^,]+),\s*IP:\s*([\d.]+)", raw):
        users.append({"id": m.group(1).strip(), "ip": m.group(2).strip()})

    mkey = re.search(r"##\s*AIP\s*Key\s*\r?\n-\s*(.+)", raw)
    crtfc_key = mkey.group(1).strip() if mkey else ""
    if not crtfc_key:
        raise RuntimeError("config.md에서 AIP Key를 찾지 못했습니다.")

    return {"crtfcKey": crtfc_key, "users": users}


def append_local_log_file(
    log_directory: str | Path,
    executed_at: str,
    request_payload: Mapping[str, Any],
    response_json: dict[str, Any] | None,
    error_detail: str | None = None,
) -> None:
    """지정 디렉터리의 logapi.log에 텍스트 블록을 append 합니다."""
    log_dir = Path(log_directory)
    log_dir.mkdir(parents=True, exist_ok=True)
    path = log_dir / LOCAL_LOG_FILENAME

    parts = [f"[{executed_at}]", "REQUEST: " + json.dumps(dict(request_payload), ensure_ascii=False)]
    if response_json is not None:
        parts.append("RESPONSE: " + json.dumps(response_json, ensure_ascii=False))
    if error_detail:
        parts.append("ERROR: " + error_detail)
    block = "\n".join(parts) + "\n---\n"

    with open(path, "a", encoding="utf-8") as f:
        fcntl.flock(f.fileno(), fcntl.LOCK_EX)
        try:
            f.write(block)
        finally:
            fcntl.flock(f.fileno(), fcntl.LOCK_UN)


@dataclass
class LogApiCaller:
    crtfc_key: str
    local_log_dir: str | Path | None = None

    def send(self, payload: Mapping[str, Any]) -> dict[str, Any]:
        body: dict[str, Any] = {
            "crtfcKey": payload.get("crtfcKey", self.crtfc_key),
            "logDt": payload["logDt"],
            "useSe": payload["useSe"],
            "sysUser": payload["sysUser"],
            "conectIp": payload["conectIp"],
            "dataUsgqty": payload.get("dataUsgqty", "0"),
        }

        data = json.dumps(body, ensure_ascii=False).encode("utf-8")
        executed_at = format_log_dt()

        def log_err(err: str) -> None:
            if self.local_log_dir is not None:
                append_local_log_file(self.local_log_dir, executed_at, body, None, err)

        req = urllib.request.Request(
            URL,
            data=data,
            method="POST",
            headers={
                "Content-Type": "application/json; charset=UTF-8",
                "Accept": "application/json",
            },
        )
        ctx = ssl.create_default_context()

        try:
            with urllib.request.urlopen(req, timeout=30, context=ctx) as resp:
                response_body = resp.read().decode("utf-8")
                status = resp.status
        except urllib.error.HTTPError as e:
            raw = e.read().decode("utf-8", errors="replace")
            log_err(f"HTTP {e.code}: {raw}")
            raise RuntimeError(f"HTTP {e.code}: {raw}") from e
        except urllib.error.URLError as e:
            log_err(f"요청 오류: {e.reason}")
            raise RuntimeError(f"요청 오류: {e.reason}") from e
        except TimeoutError as e:
            log_err(f"타임아웃: {e}")
            raise RuntimeError(f"타임아웃: {e}") from e

        if status < 200 or status >= 300:
            log_err(f"HTTP {status}: {response_body}")
            raise RuntimeError(f"HTTP {status}: {response_body}")

        try:
            decoded = json.loads(response_body)
        except json.JSONDecodeError as e:
            log_err(f"JSON 파싱 오류: {e} | raw: {response_body}")
            raise RuntimeError(f"응답 JSON 파싱 실패: {e}") from e

        if not isinstance(decoded, dict):
            log_err("응답 JSON이 객체가 아닙니다.")
            raise RuntimeError("응답 JSON이 객체가 아닙니다.")

        if self.local_log_dir is not None:
            append_local_log_file(self.local_log_dir, executed_at, body, decoded, None)

        return decoded

    def is_success(self, response: Mapping[str, Any]) -> bool:
        # API 응답 구조가 {"result": {"recptnRsltCd": "AP1002"}} 형태인 경우가 많음.
        code = response.get("recptnRsltCd")
        if isinstance(code, str):
            return code == SUCCESS_CODE

        result = response.get("result")
        if isinstance(result, Mapping):
            nested = result.get("recptnRsltCd")
            if isinstance(nested, str):
                return nested == SUCCESS_CODE

        return False
