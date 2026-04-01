## 프로젝트 개요
이 프로젝트는 MES 사용 파악용 “로그 API”를 통해 정부 시스템에 사용 로그를 전송(또는 시뮬레이션)하기 위한 구성입니다.

핵심은 `sendLogData.json` API를 POST로 호출하고, 응답의 `recptnRsltCd` 값이 `AP1002`일 때 성공으로 판별하며, 시뮬레이터는 실제 업무 패턴(로그인/조회/CRUD/로그아웃의 불규칙성, 점심시간 회피, 최소 간격 제약)을 흉내냅니다.

## 구성
- `logApiCaller.php`: PHP 기반 API 호출기(인증키/전송/로컬 로그 append/성공코드 판별)
- `logapi_py/`: Python 독립 실행 시뮬레이터 및 API 호출기
  - `caller.py`: API 호출 및 응답 성공 판별
  - `simulator.py`: 업무시간/점심/세션/액션 주기 시뮬레이션, 공휴일 스킵, 18:00 로그아웃 종료, 사용자별 최소 간격 제한
  - `__main__.py`: CLI 진입점(데몬/포그라운드/단발 --once 지원)
- 로컬 로그
  - `logs/logapi.log`: API 요청/응답(또는 실패) 로컬 기록
  - `logs/simulator.log`: 시뮬레이터 실행 로그(데몬 출력)
- 공휴일 파일
  - `logs/holidays/holidays_<YYYY>.txt`: KASI 특일 정보 기반으로 생성/로드
  - 휴일에는 `useSe` 전 액션을 스킵하여 API 호출을 하지 않음
- `etc/logrotate.d/logapi`: `logs/logapi.log` 및 `logs/simulator.log` 로테이트 설정(5MB 단위, 최대 5개 보관)
- `crontab`: `월~금 09:00`에 시뮬레이터를 시작하도록 설정(중복 실행은 PID 락으로 방지)

## 작업 기록

2026-03-30 (월)
- `logapi_py` 시뮬레이터에 **공휴일 파일 1회 갱신 + 로드 캐시**를 추가하고, **휴일에는 모든 `useSe`(로그인/조회/CRUD/입출고/로그아웃) API 호출을 스킵**하도록 구현
- `crontab`(월~금 09:00)에 `python3 -m logapi_py` 실행 항목 추가
- 시뮬레이터에 **중복 실행 방지(PID 락)** 로직 추가 및 **18:00 도달 시 살아있는 세션 전부 로그아웃 후 종료** 처리
- `LogApiCaller.is_success()`가 응답 구조(`result.recptnRsltCd`)를 기준으로 **AP1002 판별을 정확히** 하도록 수정

2026-03-31 (화)
- `etc/logrotate.d/logapi` 설정의 로그 경로 변경 후 `logrotate -d`로 파싱/적용 대상(`logapi.log`, `simulator.log`)을 검증
- `/etc/logrotate.conf`에 `include /var/www/html/dainlab/dainsf/kfood/logapi/etc/logrotate.d`를 추가해 프로젝트 내 logrotate 설정도 함께 로드되도록 반영
- 다중 `include` 사용 시 동작(각 디렉터리 설정 병합)과 주의사항(동일 로그 파일 중복 로테이션 가능성)을 점검
