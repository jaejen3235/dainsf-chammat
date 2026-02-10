<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>납기 현황 관리 및 등록</title>
    <style>
        /* ======================================= */
        /* Custom CSS (기존 스타일 유지 및 확장) */
        /* ======================================= */
        :root {
            --primary-color: #ff9800;     /* Orange: 납기/출하 관련 색상 */
            --background: #f8f9fa;       
            --card-bg: white;
            --main-font: #343a40;
            --table-border: #dee2e6;
            --header-bg: #fff3e0;         /* 연한 주황색 헤더 */
            --status-late: #dc3545;       /* 납기 지연 (Red) */
            --status-on-time: #28a745;    /* 납기 정상 (Green) */
            --status-due: #ffc107;        /* 납기 임박 (Yellow) */
        }


        /* Title & Header */
        .report-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        /* Registration Form (Input Section) */
        .registration-box {
            background-color: #fff8ee; /* 연한 주황색 배경 */
            border: 1px solid var(--primary-color);
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 6px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            align-items: end;
        }
        
        /* 폼 요소 스타일 */
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: var(--main-font);
        }

        .input, .select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-box {
            grid-column: 4 / 5; /* 마지막 칼럼 사용 */
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            background-color: var(--primary-color);
            color: white;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #e68a00;
        }
        .btn.primary {
            background-color: var(--status-on-time);
        }
        .btn.primary:hover {
            background-color: #218838;
        }


        /* Data Table (Delivery Status History) */
        .list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .list {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 14px;
        }
        .list thead th {
            background-color: var(--header-bg);
            border: 1px solid var(--table-border);
            padding: 12px;
            font-weight: 700;
            color: var(--primary-color);
        }
        .list tbody td {
            border: 1px solid var(--table-border);
            padding: 10px 8px;
        }
        .list tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        /* Highlight Colors */
        .status-late-text { color: var(--status-late); font-weight: 700; }
        .status-on-time-text { color: var(--status-on-time); font-weight: 700; }
        .status-due-text { color: var(--status-due); font-weight: 700; }
        .total-row { font-weight: 700; background-color: #fff0d4 !important; }

    </style>
</head>
<body>

    <div class='main-container'>
        <div class='content-wrapper'>
            
            <div id="delivery-management">
                <div class="report-title">🚚 납기 현황 관리</div>

                <div class="registration-box">
                    <h4>제품 납기 정보 등록/수정</h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="delivery_order">지시/주문번호</label>
                            <input type='text' class='input' id='delivery_order' value="PO-20251101"/>
                        </div>
                        <div class="form-group">
                            <label for="delivery_item">제품 품목명</label>
                            <select id="delivery_item" class="select">
                                <option value="A-100">스마트칩 A-100</option>
                                <option value="B-200">모듈케이스 B-200</option>
                                <option value="C-300">배터리팩 C-300</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="delivery_due_date">납기 요청일</label>
                            <input type='date' class='input' id='delivery_due_date' value="2025-11-20"/>
                        </div>
                        <div class="form-group">
                            <label for="delivery_qty">납품 수량 (EA)</label>
                            <input type='number' class='input' id='delivery_qty' value="200" min="1" />
                        </div>
                        
                        <div class="form-group" style="grid-column: 1 / 3;">
                            <label for="delivery_actual_date">실제 출하/납기일</label>
                            <input type='date' class='input' id='delivery_actual_date' value=""/>
                        </div>
                        <div class="form-group">
                            <label for="delivery_status">납기 상태</label>
                            <select id="delivery_status" class="select">
                                <option value="진행 중">진행 중</option>
                                <option value="정상 완료">정상 완료</option>
                                <option value="지연">지연</option>
                            </select>
                        </div>
                        <div class="btn-box">
                            <input type='button' class='btn primary' value='납기 정보 등록/수정' onclick='registerDelivery()' />
                        </div>
                    </div>
                </div>

                <div class="list-header">
                    <div class="report-title" style="font-size: 18px; margin: 0; color: var(--main-font);">납기 현황 조회</div>
                    <div class="btn-box">
                        <input type='date' class='input' id='search_start_date' value="2025-11-01"/>
                        <span>~</span>
                        <input type='date' class='input' id='search_end_date' value="2025-11-30"/>
                        <input type='button' class='btn' value='현황 조회' onclick='searchDeliveryHistory()' />
                    </div>
                </div>

                <table class='list'>
                    <thead>
                        <tr>
                            <th>주문번호</th>
                            <th>제품명</th>
                            <th>요청 수량</th>
                            <th>**납기 요청일**</th>
                            <th>실제 납기일</th>
                            <th>**납기 상태**</th>
                            <th>납기 일자 차이</th>
                            <th>거래처</th>
                        </tr>
                    </thead>
                    <tbody id="delivery-history-body">
                        </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        // ===============================================
        // Mock Data (납기 현황 예시 데이터)
        // ===============================================
        const mockDeliveryData = [
            { id: 1, order: 'PO-20251101', item: '스마트칩 A-100', qty: 100, due_date: '2025-11-15', actual_date: '2025-11-14', status: '정상 완료', customer: '전자상사 A' },
            { id: 2, order: 'PO-20251102', item: '배터리팩 C-300', qty: 50, due_date: '2025-11-10', actual_date: '2025-11-12', status: '지연', customer: '모듈 테크' },
            { id: 3, order: 'PO-20251103', item: '모듈케이스 B-200', qty: 200, due_date: '2025-11-20', actual_date: '', status: '진행 중', customer: '케이스 유통' },
            { id: 4, order: 'PO-20251104', item: '스마트칩 A-100', qty: 150, due_date: '2025-11-17', actual_date: '2025-11-17', status: '정상 완료', customer: '전자상사 A' },
            { id: 5, order: 'PO-20251105', item: '배터리팩 C-300', qty: 30, due_date: '2025-11-30', actual_date: '', status: '진행 중', customer: '신규 거래처 D' },
        ];


        // ===============================================
        // Utility Functions
        // ===============================================

        /** 숫자 포맷팅 (콤마 추가) */
        function formatNumber(num) {
            return num.toLocaleString('ko-KR');
        }
        
        /** 납기일 차이 계산 (일 단위) */
        function calculateDateDifference(dueDate, actualDate) {
            if (!actualDate) {
                // 실제 납기일이 없으면 납기 요청일과 오늘 날짜 비교
                const today = new Date();
                const due = new Date(dueDate);
                const diffTime = due - today;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays < 0) return { text: `${Math.abs(diffDays)}일 지연`, days: diffDays };
                if (diffDays === 0) return { text: `오늘 납기`, days: 0 };
                return { text: `${diffDays}일 남음`, days: diffDays };
            }

            const due = new Date(dueDate);
            const actual = new Date(actualDate);
            const diffTime = actual - due;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays > 0) return { text: `+${diffDays}일 지연`, days: diffDays };
            if (diffDays < 0) return { text: `${diffDays}일 조기납품`, days: diffDays };
            return { text: '정시 납품', days: 0 };
        }

        // ===============================================
        // Rendering Functions
        // ===============================================
        
        /** 납기 현황 렌더링 함수 */
        function renderDeliveryHistory(data) {
            const body = document.getElementById('delivery-history-body');
            body.innerHTML = '';
            
            let totalQty = 0;
            let onTimeCount = 0;
            let lateCount = 0;

            if (data.length === 0) {
                body.innerHTML = `<tr><td colspan='8'>검색 조건에 해당하는 납기 현황이 없습니다.</td></tr>`;
                return;
            }
            
            data.forEach(item => {
                totalQty += item.qty;
                
                const { text: diffText, days: diffDays } = calculateDateDifference(item.due_date, item.actual_date);
                
                let statusClass = '';
                if (item.status === '지연' || diffDays > 0 && item.actual_date) {
                    statusClass = 'status-late-text';
                    lateCount++;
                } else if (item.status === '정상 완료' || diffDays <= 0 && item.actual_date) {
                    statusClass = 'status-on-time-text';
                    onTimeCount++;
                } else if (diffDays <= 3 && !item.actual_date) { // 납기 임박
                    statusClass = 'status-due-text';
                }
                
                const statusDisplay = item.status === '정상 완료' && diffDays < 0 ? '조기 완료' : item.status;


                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.order}</td>
                    <td>${item.item}</td>
                    <td>${formatNumber(item.qty)} EA</td>
                    <td>${item.due_date}</td>
                    <td>${item.actual_date || '-'}</td>
                    <td class="${statusClass}">**${statusDisplay}**</td>
                    <td style="color: ${statusClass}">${diffText}</td>
                    <td>${item.customer}</td>
                `;
                body.appendChild(row);
            });

            // 총 합계 행 추가 (간단한 요약 정보)
            body.innerHTML += `
                <tr class="total-row">
                    <td colspan="3">총 요청 수량 및 현황</td>
                    <td colspan="5" style="text-align: left; padding-left: 20px;">
                        총 요청 수량: **${formatNumber(totalQty)}** EA 
                        (정상 완료: <span class="status-on-time-text">${onTimeCount}건</span>, 
                        지연/미완료: <span class="status-late-text">${lateCount}건</span>)
                    </td>
                </tr>
            `;
        }


        // ===============================================
        // Event Handlers
        // ===============================================

        /** 납기 정보 등록/수정 시뮬레이션 */
        function registerDelivery() {
            const order = document.getElementById('delivery_order').value;
            const itemCode = document.getElementById('delivery_item').value;
            const itemName = document.getElementById('delivery_item').options[document.getElementById('delivery_item').selectedIndex].text;
            const dueDate = document.getElementById('delivery_due_date').value;
            const qty = parseInt(document.getElementById('delivery_qty').value);
            const actualDate = document.getElementById('delivery_actual_date').value;
            const status = document.getElementById('delivery_status').value;
            
            if (qty <= 0 || order === "" || dueDate === "") {
                alert("주문번호, 납기 요청일, 수량을 정확히 입력해주세요.");
                return;
            }
            
            // 실제 API 호출 로직: /api/delivery/register
            
            console.log(`[납기 등록/수정 요청] 주문번호: ${order}, 요청일: ${dueDate}, 실제일: ${actualDate}, 상태: ${status}`);
            alert(`[${order}] 납기 정보 등록/수정 요청이 완료되었습니다.\n(API 호출 시뮬레이션)`);

            // 등록 후 내역 재조회
            searchDeliveryHistory();
        }

        /** 납기 현황 조회 */
        function searchDeliveryHistory() {
            const startDate = document.getElementById('search_start_date').value;
            const endDate = document.getElementById('search_end_date').value;
            
            console.log(`[납기 현황 조회] 기간: ${startDate} ~ ${endDate}`);
            
            // Mock Data 필터링: 납기 요청일 기준으로 기간 필터링
            const filteredData = mockDeliveryData.filter(d => {
                return d.due_date >= startDate && d.due_date <= endDate;
            });
            
            renderDeliveryHistory(filteredData);
        }

        // ===============================================
        // Initial Load
        // ===============================================
        window.onload = () => {
            // 페이지 로드 시 납기 현황을 바로 표시
            searchDeliveryHistory(); 
        };
    </script>
</body>
</html>