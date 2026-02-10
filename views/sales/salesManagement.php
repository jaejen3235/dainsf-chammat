<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>판매 정보 관리 및 등록</title>
    <style>
        /* ======================================= */
        /* Custom CSS (기존 스타일 유지 및 확장) */
        /* ======================================= */
        :root {
            --primary-color: #20c997;     /* Teal Green: 판매/수익 관련 색상 */
            --background: #f8f9fa;       
            --card-bg: white;
            --main-font: #343a40;
            --table-border: #dee2e6;
            --header-bg: #e6fff7;         /* 연한 녹색 헤더 */
            --status-sales: #20c997;      /* 판매 (Green) */
            --status-paid: #007bff;       /* 결제 완료 (Blue) */
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
            background-color: #f1fcf9; /* 연한 녹색 배경 */
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
            background-color: #17a2b8; /* 약간 더 진한 색 */
        }
        .btn.primary {
            background-color: var(--status-sales);
        }
        .btn.primary:hover {
            background-color: #1baf88;
        }


        /* Data Table (Sales History) */
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
            color: var(--status-sales);
        }
        .list tbody td {
            border: 1px solid var(--table-border);
            padding: 10px 8px;
        }
        .list tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        /* Highlight Colors */
        .sales-text { color: var(--status-sales); font-weight: 700; }
        .total-row { font-weight: 700; background-color: #d8f5eb !important; }

    </style>
</head>
<body>

    <div class='main-container'>
        <div class='content-wrapper'>
            
            <div id="sales-management">
                <div class="report-title">💰 판매 정보 관리</div>

                <div class="registration-box">
                    <h4>신규 판매 정보 등록</h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="sales_date">판매 일자</label>
                            <input type='date' class='input' id='sales_date' value="2025-11-17"/>
                        </div>
                        <div class="form-group">
                            <label for="sales_item">제품 품목명</label>
                            <select id="sales_item" class="select">
                                <option value="A-100">스마트칩 A-100</option>
                                <option value="B-200">모듈케이스 B-200</option>
                                <option value="C-300">배터리팩 C-300</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sales_qty">판매 수량</label>
                            <input type='number' class='input' id='sales_qty' value="50" min="1" />
                        </div>
                        <div class="form-group">
                            <label for="sales_price">단가 (원)</label>
                            <input type='number' class='input' id='sales_price' value="10000" min="100"/>
                        </div>
                        <div class="form-group" style="grid-column: 1 / 3;">
                            <label for="sales_customer">판매처/고객명</label>
                            <input type='text' class='input' id='sales_customer' placeholder="판매된 거래처명을 입력하세요." />
                        </div>
                        <div class="form-group">
                            <label for="sales_status">결제 상태</label>
                            <select id="sales_status" class="select">
                                <option value="완료">결제 완료</option>
                                <option value="미결">결제 미결</option>
                            </select>
                        </div>
                        <div class="btn-box">
                            <input type='button' class='btn primary' value='판매 등록' onclick='registerSales()' />
                        </div>
                    </div>
                </div>

                <div class="list-header">
                    <div class="report-title" style="font-size: 18px; margin: 0; color: var(--main-font);">판매 이력 조회</div>
                    <div class="btn-box">
                        <input type='date' class='input' id='search_start_date' value="2025-11-01"/>
                        <span>~</span>
                        <input type='date' class='input' id='search_end_date' value="2025-11-17"/>
                        <input type='button' class='btn' value='이력 조회' onclick='searchSalesHistory()' />
                    </div>
                </div>

                <table class='list'>
                    <thead>
                        <tr>
                            <th>판매일</th>
                            <th>제품 코드</th>
                            <th>제품명</th>
                            <th>**판매 수량**</th>
                            <th>단가 (원)</th>
                            <th>총 매출액 (원)</th>
                            <th>판매처</th>
                            <th>결제 상태</th>
                        </tr>
                    </thead>
                    <tbody id="sales-history-body">
                        </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        // ===============================================
        // Mock Data (판매 내역 예시 데이터)
        // ===============================================
        const mockSalesData = [
            { id: 1, date: '2025-11-05', code: 'A-100', item: '스마트칩 A-100', qty: 100, price: 10000, customer: '전자상사 A', status: '완료' },
            { id: 2, date: '2025-11-08', code: 'C-300', item: '배터리팩 C-300', qty: 50, price: 25000, customer: '모듈 테크', status: '완료' },
            { id: 3, date: '2025-11-10', code: 'B-200', item: '모듈케이스 B-200', qty: 200, price: 3000, customer: '케이스 유통', status: '미결' },
            { id: 4, date: '2025-11-15', code: 'A-100', item: '스마트칩 A-100', qty: 150, price: 10500, customer: '전자상사 A', status: '완료' },
            { id: 5, date: '2025-11-17', code: 'B-200', item: '모듈케이스 B-200', qty: 100, price: 3200, customer: '새로운 거래처 B', status: '완료' },
        ];


        // ===============================================
        // Utility Functions
        // ===============================================

        /** 숫자 포맷팅 (콤마 추가) */
        function formatNumber(num) {
            return num.toLocaleString('ko-KR');
        }

        /** 총 매출액 계산 */
        function calculateTotal(qty, price) {
            return qty * price;
        }

        // ===============================================
        // Rendering Functions
        // ===============================================
        
        /** 판매 이력 렌더링 함수 */
        function renderSalesHistory(data) {
            const body = document.getElementById('sales-history-body');
            body.innerHTML = '';
            
            let totalSalesAmount = 0;

            if (data.length === 0) {
                body.innerHTML = `<tr><td colspan='8'>검색 조건에 해당하는 판매 이력이 없습니다.</td></tr>`;
                return;
            }
            
            data.forEach(item => {
                const totalAmount = calculateTotal(item.qty, item.price);
                totalSalesAmount += totalAmount;
                
                const statusColor = item.status === '완료' ? 'var(--status-paid)' : 'var(--primary-color)';

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.date}</td>
                    <td>${item.code}</td>
                    <td>${item.item}</td>
                    <td class="sales-text">${formatNumber(item.qty)} EA</td>
                    <td>${formatNumber(item.price)}</td>
                    <td class="sales-text">**${formatNumber(totalAmount)}**</td>
                    <td>${item.customer}</td>
                    <td style="color: ${statusColor}; font-weight: 700;">${item.status}</td>
                `;
                body.appendChild(row);
            });

            // 총 합계 행 추가
            body.innerHTML += `
                <tr class="total-row">
                    <td colspan="5">총 매출액 합계</td>
                    <td colspan="3" class="sales-text" style="text-align: left; padding-left: 20px; font-size: 16px;">
                        **${formatNumber(totalSalesAmount)} 원**
                    </td>
                </tr>
            `;
        }


        // ===============================================
        // Event Handlers
        // ===============================================

        /** 판매 등록 처리 시뮬레이션 */
        function registerSales() {
            const date = document.getElementById('sales_date').value;
            const itemCode = document.getElementById('sales_item').value;
            const itemName = document.getElementById('sales_item').options[document.getElementById('sales_item').selectedIndex].text;
            const qty = parseInt(document.getElementById('sales_qty').value);
            const price = parseInt(document.getElementById('sales_price').value);
            const customer = document.getElementById('sales_customer').value;
            const status = document.getElementById('sales_status').value;
            
            if (qty <= 0 || price <= 0 || customer === "") {
                alert("수량, 단가, 판매처를 정확히 입력해주세요.");
                return;
            }
            
            // 실제 API 호출 로직: /api/sales/register
            
            console.log(`[판매 등록 요청] 일자: ${date}, 품목: ${itemName}, 수량: ${qty}, 총액: ${formatNumber(qty * price)}`);
            alert(`[${itemName}] ${qty} EA 판매 등록이 완료되었습니다.\n총액: ${formatNumber(qty * price)} 원 (API 호출 시뮬레이션)`);

            // 등록 후 내역 재조회
            searchSalesHistory();
        }

        /** 판매 이력 조회 */
        function searchSalesHistory() {
            const startDate = document.getElementById('search_start_date').value;
            const endDate = document.getElementById('search_end_date').value;
            
            console.log(`[판매 이력 조회] 기간: ${startDate} ~ ${endDate}`);
            
            // Mock Data 필터링: 기간 필터링
            const filteredData = mockSalesData.filter(d => {
                return d.date >= startDate && d.date <= endDate;
            });
            
            renderSalesHistory(filteredData);
        }

        // ===============================================
        // Initial Load
        // ===============================================
        window.onload = () => {
            // 페이지 로드 시 판매 이력을 바로 표시
            searchSalesHistory(); 
        };
    </script>
</body>
</html>