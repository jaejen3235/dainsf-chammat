## 작업 내역 (2026-03-16)

- 출하지시/납품 처리
  - 출하지시 등록(`registerDeliveryOrder`)에서 잘못된 `uid`와 PHP Notice로 인한 JSON 파싱 오류 수정
  - MES 로그 후킹(`SmartFactoryLogger`) 시 JSON 응답이 깨지지 않도록 `handler.php`에 출력 버퍼 및 예외 처리 추가
  - 출하/납품 처리 시 수주 연동이 없는 개별 출하지시도 정상 처리되도록 로직 분리
  - 출하 등록(`registerDelivery`) 시 재고 부족이어도 출하 가능하도록 프론트 검증 제거, 출하 수량만큼 재고(`mes_items.stock_qty`) 차감 처리 추가

- 로그/디버깅
  - `Mes` 컨트롤러에 디버그 로그 메서드(`debugLog`) 추가 및 1MB × 5개 롤링 파일(`log/mes_debug.log`) 적용
  - 스마트공장 로그 전송 유틸(`SmartFactoryLogger`) 추가 및 MES 액션 후킹

- 작업지시/작업일보
  - 작업지시서 관리 화면
    - 수주 품목 목록: 거래처/수주품목 검색, 수주일·납기일 기간(둘 중 하나라도 범위 내) 필터 추가, 접속 시 오늘 날짜 기본 조회
    - 생산지시 목록: 작업품목/상태(생산대기/작업중/부분작업완료/작업완료) 필터 및 작업지시일 기간 검색 추가, 기본값은 오늘 날짜 + 생산대기 중심 조회
  - 계획 생산지시 수정 모달
    - 불필요한 항목(성별/사이즈/색상/품목 그룹) 제거, 품목·품번·규격·단위·재고·지시 일자/수량만 표시
    - `getPlanWorkOrder`, `modifyPlanWorkOrder` API 추가 및 검증/수정 로직 구현
  - 작업일보 관리 화면
    - 생산지시서 목록/작업일보 목록 모두 접속 시 오늘 날짜로 기본 조회
    - 상태/품목 조건 검색 추가
  - 장비 연동 작업일보
    - `apis/machine.php`에서 자동 생성되는 작업일보의 품질 상태를 `Quality inspection completed` → `품질검사완료`로 변경

- 출하 대기/출하 처리
  - 제품 출하 처리(`outManagement`): 출하 대기 목록에서 `status != '출하완료'`만 보이도록 수정
  - 거래명세서(구/신 모달)
    - `comma` 중복 정의 제거 및 전역 `common.js` 함수 재사용
    - 새 거래명세서 모달(`modalNewInvoice`): 거래 내역이 15줄이 되도록 부족한 줄은 빈 `<tr>`로 채우는 로직 추가
    - 인쇄 미리보기 시 상단 여백 확보를 위해 `@media print`에서 `#invoiceContent`의 상단 마진 조정
    - 공급자 정보 영역에서 대표 셀 폭 축소 및 상호 셀 폭 확대(가독성 개선)

## 작업 내역 (2026-03-17)
- 제품 입출하 관리
  - 제품 입고 관리(`views/shipment/inManagement.php`)
    - 입고 대기 목록: 거래처/품목명 검색, 구매 요청 일자 기간 필터, 접속 시 금일 자동 선택 및 목록 조회 추가
    - 입고 완료 이력: 거래처/품목명 검색, 입고 일자 기간 필터, 접속 시 금일 자동 선택 및 목록 조회 추가
    - 두 목록 모두 행 선택 체크박스 + "선택 삭제" 기능 추가 (`mes::deletePurchaseItems` 사용), 단건 삭제 버튼 유지
    - 입고 완료 이력에서 입고 일자 기준 기간 검색 시 `in_date` 사용(없으면 `purchase_date` 표시), 페이징 개별 영역으로 분리
    - 테이블 `colgroup` 설정으로 거래처/품목명 폭을 넓히고, 구매 요청 일자/입고 일자 컬럼 폭을 날짜+정렬 아이콘 정도로 축소

- 정렬 기능 추가
  - 제품 입고 관리
    - 입고 대기 목록: "구매 요청 일자" 헤더에 ▲/▼ 정렬 버튼 추가, `purchase_date` 기준 오름차순/내림차순 정렬
    - 입고 완료 이력: "입고 일자" 헤더에 ▲/▼ 정렬 버튼 추가, `in_date` 기준 오름차순/내림차순 정렬
  - 작업지시서 관리(`views/product/workOrderManagement.php`)
    - 수주 품목 목록: "수주일", "납기일" 헤더에 ▲/▼ 정렬 버튼 추가, 각각 `order_date`, `shipment_date` 기준 정렬
    - 생산지시 목록: "작업지시일" 헤더에 ▲/▼ 정렬 버튼 추가, `mes_work_order.order_date` 기준 정렬
  - 생산 실적 관리(`views/product/reportPeriodProduct.php`)
    - 생산실적 목록: "작업일자" 헤더에 ▲/▼ 정렬 버튼 추가, `work_date` 기준 오름차순/내림차순 정렬 및 합계 행 유지

- 공통/백엔드
  - `mes::getPurchaseItemList` 응답에 `in_date` 필드 추가하여 입고 완료 이력에서 입고 일자 표시 및 정렬에 활용
  - `mes::deletePurchaseItems` 추가: `mes_purchase_item`의 `uid` 배열을 받아 일괄 삭제

- 공통 UI 개선
  - datepicker 전역 설정(`views/client/foot.php`)
    - `.datepicker` 초기화를 커스터마이징하여 연도/월 셀렉터 변경 시에도 입력 포커스가 유지되는 동안 달력이 닫히지 않도록 수정
    - 사용자가 실제 날짜(일)를 클릭하거나 입력 포커스를 잃을 때에만 달력이 닫히도록 UX 개선

## 작업 내역 (2026-03-18)
- HACCP 양식 입력 UI 개선
  - CCP-1BP 모니터링일지(`views/haccp/HC01_CCP-1BP_모니터링일지.html`)
    - 작성자(`writer_name`) 입력 영역을 다중 라인 입력이 가능하도록 `textarea`로 변경
    - 승인자(`approver_name`) 입력 영역에 대해서도 동일하게 `textarea` 적용(표시 라인 수 통일)
    - 점검표 O/X 선택 영역들을 라디오 UI에서 사용자 직접 입력(`input[type="text"]`) 방식으로 변경 (`rows[n][ox]`)
    - 중복 배치로 인해 겹침이 발생하던 `.hce.s135_02` 블록(행 인덱스 `rows[6]` 사용)을 제거하여 row/col 충돌 해소
  - CCP-2BP 모니터링일지(`views/haccp/HC02_CCP-2BP_모니터링일지.html`)
    - 점검자/승인자 입력을 `textarea`(2줄)로 변경하고 중앙 정렬/스크롤 제거 적용
    - 서명/확인 영역(`HC02_actor`, `HC02_verifier`) 입력을 `textarea`로 변경(부모 영역 100% 채움) 및 줄간격 축소(`line-height: 1.1`)
  - 스타일 조정(`assets/css/CCP-1BP_모니터링일지_style.css`)
    - 작성자/승인자 입력 영역 높이 확장(2줄 입력이 보이도록 영역 높이 조정)
    - 입력 영역을 flex 기반 중앙 정렬로 배치(가로/세로 중앙 정렬)
    - 스크롤이 보이지 않도록 `overflow: hidden` 적용
    - 가독성 개선을 위해 폰트 크기를 단계적으로 축소(`0.8em`)하고 줄간격(`line-height`)을 소폭 확대

## 작업 내역 (2026-03-18, 추가)
- 공통/기초 화면 조정
  - 품목관리(`views/basic/listItem.php`) 검색어 입력창 폭을 해당 페이지에서만 확장되도록 인라인 스타일로 조정
  - `assets/css/CCP-1BP_모니터링일지_style.css`의 브라우저 호환 경고 대응(`appearance: none` 추가)

- HACCP 화면 구조/렌더링 정리
  - `views/haccp/CCP-2BP_모니터링일지.php`를 `CCP-1BP`와 동일한 좌/우 분할 구조로 사용
  - 우측 문서 영역은 `HC02_CCP-2BP_모니터링일지.html`의 `<form>...</form>`만 동적 출력하도록 정리

- HC03 입력영역 편집(`views/haccp/HC03_CCP-3P_모니터링일지.html`)
  - 작성자 입력(`writer_name`)을 `input`에서 `textarea`로 변경
  - `productOnlyPass` 항목:
    - `row1`~`row13`에서 체크박스 기반 표시를 텍스트 입력(`textarea rows="2"`) 방식으로 단계적 전환
    - `row2` 기준으로 `row3`~`row13`의 폭/높이/정렬/위치 보정(`transform`)을 동일화
  - `foreignDetected` 항목:
    - `row2` 기준으로 `row3`~`row13`을 `textarea rows="2"` + 동일 정렬/폰트/위치 보정으로 통일
  - `time` 항목:
    - `CCP-3P_row2_time`~`CCP-3P_row13_time`을 `CCP-3P_row1_time` 기준으로 통일(placeholder, padding-top, 스타일)
    - 일부 `font-weight`(bold) 적용으로 인해 일반 입력과 다르게 보이던 문제를 normal 기준으로 정리

## 작업 내역 (2026-03-24)
- HACCP 문서 입력영역 통일/보정
  - `views/haccp/HC03_CCP-3P_모니터링일지.html`
    - `HC03_row2_productName`~`HC03_row13_productName`을 `textarea` 전환 후 정렬/폰트 보정 과정을 거쳐 최종 `row1`과 동일하게 `input` 구조로 통일
    - `HC03_actionPerson`, `HC03_check`를 `textarea`로 전환하고 부모 영역 맞춤(높이 100%), 스크롤/리사이즈 비노출 처리
  - `views/haccp/HC04_검교정_성적서.html`
    - 상단 텍스트 영역 일부를 `input`/`textarea`로 전환하고 작성자/승인자 입력영역(`HC04_writer_name`, `HC04_approver_name`) 추가/정렬
    - 내부 자체 검교정 표에서 row 2개 추가(`row6`, `row7`) 및 하단 섹션(`한계기준 이탈내용/개선조치 및 결과/조치자/확인`) 좌표 재배치
    - 표 라인(SVG path) 중복/오정렬 정리 및 하단 렌더링 라인 위치 보정
  - `views/haccp/HC05_방충_및_방서점검표.html`
    - 상단 주기 텍스트(`매주 월요일`)를 중앙 정렬 `input`으로 전환(경계/배경 제거, 부모 폭 맞춤)
    - 작성자/승인자 영역을 `textarea`로 전환하고 스크롤/리사이즈 비노출 적용
    - `HC05_writeDate`를 클릭 입력 방식에서 페이지 로드시 자동 오늘 날짜 입력 방식으로 변경
    - `HC05_action_person`, `HC05_check`를 `textarea`로 전환하고 부모 영역(`top/width/height`) 기준으로 확장/중앙 정렬
  - `views/haccp/HC06_온도점검표.html`
    - `ROW1/2/3/5/7/9` 서명/승인 입력영역을 `textarea`로 전환, 부모 영역 100% 채움 및 중앙 정렬 적용
    - 필드명 요청에 따라 `writerName` → `approverName`으로 변경(ROW 번호는 유지)
    - 변경 후 작아 보이던 텍스트 크기를 동일하게 보이도록 `font-size` 보정

## 작업 내역 (2026-03-25)
- HACCP 양식 입력영역 변환/정렬 보정
  - `views/haccp/HC07_원부재료_입고_점검표.html`
    - `HC07_packStatus_1~11`, `HC07_appearance/smell/freshness/certificate_1~11` 입력값을 중앙 정렬로 통일
    - `HC07_writer_1~11`을 `input`에서 `textarea`로 변경하고 부모 영역(상단/폭/높이 100%) 기준으로 맞춤
    - 승인 칸 1~11행에 `HC07_approver_1~11` `textarea` 추가(중앙 정렬, 부모 영역 맞춤)
  - `views/haccp/HC08_일반위생관리_및_공정점검표1.html`
    - 상단 안내 문구(`작업 시 (생산이 있는 날)`)를 `textarea`로 전환하고 크기/정렬/폰트 보정
    - `HC08_writer`, `HC08_actor`를 `textarea`로 전환(부모 영역 100% 채움, 중앙 정렬)
    - 하단 확인 영역에 `HC08_confirmer` `textarea` 추가(조치자 영역과 동일 높이/정렬)
    - 체크박스 크기 불일치 이슈를 정리하여 `HC08_ROW02_RESULT_Y`를 다른 체크박스와 동일 규칙으로 통일
    - `HC08_writeDate`는 페이지 로드시 오늘 날짜 자동 입력되도록 스크립트 적용 및 중복 리스너 정리
  - `views/haccp/HC09_일반위생관리_및_공정점검표2.html`
    - 상단 주기 안내(주/월/년)를 단일 `textarea`(`HC09_scheduleLegend`)로 통합하고 줄바꿈 기본값 적용
    - `매주/월요일` 문구를 `textarea`(`HC09_weeklyCycleLabel`)로 전환, 부모 기준 100% 맞춤 및 중앙 정렬
    - `HC09_writeDate` 페이지 로드시 오늘 날짜 자동 입력되도록 `</form>` 직전 스크립트 추가
  - `views/haccp/HC10_중요관리점(CCP)_검증점검표.html`
    - 상단 `매월 1일` 문구를 `textarea`(`HC10_cycleLabel`)로 전환하고 폰트 유지/중앙 정렬/부모 영역 맞춤 적용
    - 공정 문구를 `textara`로 전환하고 폰트 유지/중앙정렬/부모 영역 맞춤 적용 (3개)
  - HACCP 전체 문서 대상 작성일자를 onclick로 변경 (서버의 목록을 수정할 경우 항상 현재 날짜가 적용되는 문제 예방)

## 작업 내역 (2026-03-25, 추가)
- HACCP REST CRUD 전환(저장/조회/수정/삭제) + 공통 모듈 연동
  - DB: `haccp_records` 공통 테이블 생성(`payload_json` LONGTEXT 저장) 및 인덱스 추가
  - Backend: `controllers/haccpRecords.php`에서 `create/list/getOne/update` 구현 및 `delete` 모드 충돌 회피를 위해 `deleteRecord()`로 분리
  - 라우팅: `handler.php`에서 `mode=delete` 요청을 `deleteRecord()`로 매핑
  - Frontend: `views/haccp/haccpFormClient.js` 추가
    - DOM→payload 변환/조회 시 payload→DOM 적용
    - “새로 작성” 시 HTML default 값 복구(기본값 스냅샷/리셋)
    - 목록 페이징 버튼이 `total/per`에 맞게 숨김/비활성화되도록 UI 갱신
- UI: `views/haccp/*.php`(10개) 좌측 목록을 더미(tr) 제거 후 `handler.php` fetch 기반으로 연동
- 인쇄/미리보기 레이아웃 수정
  - CCP-1BP 제외 문서들에서 `@media print` 기본 여백/overflow 이슈를 수정하여 A4 영역 잘림/우측 컷 문제 완화

## 작업 내역 (2026-03-26)
- 장비 수신/로그/보관 정책
  - `apis/machine.php` 수신 로그 파일(`apis/log.txt`) 권한 문제를 정리하여 로그 기록이 정상 동작하도록 조치
  - 서버 `logrotate` 설정 추가: `apis/log.txt`를 5MB 단위로 롤링하고 최대 5개 보관
  - 로그 로테이션 산출물(`apis/log.txt.*`)이 Git 변경사항에 포함되지 않도록 `.gitignore` 보강
  - `mes_machine_data` 확률 삭제 로직의 보관 기간을 24시간에서 14일(2주)로 변경

- 세척기(cleaner) 가동/정지 이력 + 전력 집계
  - `apis/machine.php`에서 `machine=cleaner`, `data_type=current` 수신 시 상태 전환 이력 처리 추가
    - 기준: `value > 0.4` 가동 / 이하 정지
    - 노이즈 완화: 연속 3회 수신 시에만 상태 전환
  - DB 테이블 추가
    - `cleaner_run_state`: 현재 상태/카운트 관리
    - `cleaner_run_history`: 가동 시작/종료 이력 관리
  - `controllers/mes.php`
    - `getCleanerRunHistory` API 추가(가동시간 합계/목록 조회)
    - `getEmsPowerKpi` API 추가(주간/일간 소비전력 KPI)
  - `apis/machine.php`에서 cleaner 전류값으로 샘플별 소비전력(kWh) 계산 후 `mes_day_power` 일자 컬럼(`day1~day31`) 누적 반영
    - 3상 380V, 수신 전류 1상값 기준 계산식 적용

- EMS 화면 개선 (`views/monitoring/ems.php`)
  - KPI 카드를 좌/우 2분할로 구성하고 항목을 `주간 소비전력량`, `일간 소비전력량`으로 변경
  - KPI 값은 `mes_day_power` 기반 API(`getEmsPowerKpi`)를 통해 갱신
  - 하단 테이블은 전류 실시간 목록 대신 `cleaner_run_history` 가동/정지 이력 표시로 전환
    - 20개 row 기준 페이징 처리
  - 카드 타이틀 가시성/간격 스타일 조정

- 창고 온도 모니터링 개선 (`views/monitoring/warehouseTemperature.php`)
  - 기존 상단 카드 + 실시간 차트는 유지
  - 하단 영역에 `실시간 차트 / 시간별 조회` 탭 구조 추가
  - 시간별 조회 탭에 냉장창고 3개(`frig_goods`, `frig_mix`, `frig_stuff`) 10분 단위 온도 집계 테이블 추가
    - 20개 row 페이징
    - 오름차순/내림차순 정렬 옵션
    - 테이블 row 6개 초과 시 세로 스크롤 적용

## 작업 내역 (2026-03-27)
- 작업일보/품질/입출하 화면 검색·정렬·엑셀 기능 확장
  - `views/product/reportDayWork.php`
    - 작업일보 목록: `작업자+품목` 통합 LIKE 검색, 작업일 ASC/DESC, 엑셀 다운로드 추가
  - `views/quality/shortCircuitTest.php`
    - 검색 조건에 `작업자+품목` 통합 LIKE, Enter 검색, 작업일 ASC/DESC, 엑셀 다운로드 추가
  - `views/quality/metalDetection.php`
    - `시작시간/종료시간` 정렬(ASC/DESC) 추가, 품목 검색 Enter 이벤트 추가
  - `views/shipment/inManagement.php`
    - 입고 대기/완료 검색창을 각각 통합 키워드(`거래처, 품목명`) LIKE 방식으로 변경
    - 입고 대기/완료 목록 엑셀 다운로드 버튼 및 로직 추가
  - `views/shipment/outManagement.php`
    - 검색 UI(키워드+기간) 추가, Enter/검색 버튼 조회 연결
    - 출하 요청일 ASC/DESC 정렬 추가, 엑셀 다운로드 추가

- 재고/수불/시스템 화면 검색·엑셀 기능 보강
  - `views/items/stock.php`
    - 검색 영역에 엑셀 다운로드 버튼 추가, 조건 기반 `getItemListExcel` 다운로드 구현
  - `views/items/reportInOutItem.php`
    - 검색 조건을 단일 키워드(`구분, 품목명, 품목코드`)로 통합 LIKE 처리
    - Enter 검색 이벤트 추가
    - `검색일 초기화` 버튼을 `엑셀 다운로드`로 교체하고 `getItemsInOutListExcel` 구현
    - 검색 결과 없음 행 colspan을 실제 컬럼 수에 맞게 조정(헤더/바디 폭 불일치 해소)
  - `views/system/configSystem.php`
    - 사용자명/로그인아이디 동시 LIKE 검색 구현
    - 검색 버튼/Enter 검색 동작 점검 및 수정
    - 엑셀 다운로드 버튼과 `getUserListExcel` 추가
  - `views/system/loginReport.php`
    - 상단 조회 조건(시작일/종료일/로그인아이디), 검색 버튼, 엑셀 다운로드 버튼 추가
    - 로그인 일시 ASC/DESC 정렬 추가
    - `getLoginReportExcel` API 추가

- EMS 화면 고도화 (`views/monitoring/ems.php`, `controllers/mes.php`)
  - 세척기 이력 영역에 시작일/종료일 조회, 조회 버튼, 엑셀 다운로드 추가
  - 세척기 이력 `가동 시작` 컬럼 ASC/DESC 정렬 추가
  - 조회 영역과 테이블 사이 간격(갭) 및 KPI 숫자 색상 가독성 개선
  - 접속 시 이력 조회 날짜를 오늘~오늘로 자동 설정하고 즉시 조회
  - KPI 0 표시 이슈 대응:
    - `mes_day_power` 값이 0일 때 `mes_machine_data(cleaner/current)` 기반 fallback 계산으로 주간/일간 소비전력 산출 보완