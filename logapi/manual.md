# [Log API 사용법]

## 경로
- cd /var/www/html/dainlab/dainsf/hyogwang/logapi

## 설정·페이로드만 확인 (API 호출 없음)
- python3 -m logapi_py --dry-run

## 실제 전송 + logs/logapi.log 기록 (기본)
- python3 -m logapi_py

## 로컬 파일 로그 생략
- python3 -m logapi_py --no-log

## 경로 지정
- python3 -m logapi_py --config /path/to/config.md --log-dir /path/to/logs


# 기본: 데몬(백그라운드) — 로그 logs/simulator.log, PID logs/logapi_sim.pid
python3 -m logapi_py
# 터미널에 로그 보이게(포그라운드)
python3 -m logapi_py --foreground