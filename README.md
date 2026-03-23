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

