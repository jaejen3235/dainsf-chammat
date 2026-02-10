<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>일일/주간/월간 생산 현황 대시보드</title>
    <style>
        /* ======================================= */
        /* Custom CSS */
        /* ======================================= */
        :root {
            --primary-color: #17a2b8;    /* Teal: 현황/대시보드 색상 */
            --background: #f8f9fa;       
            --card-bg: white;
            --main-font: #343a40;
            --table-border: #dee2e6;
            --header-bg: #e9ecef;
            --status-good: #28aa45;      /* 합격 (Green) */
            --status-bad: #dc3545;       /* 불량 (Red) */
        }

        body {
            font-family: 'Malgun Gothic', 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background);
            color: var(--main-font);
        }

        .main-container {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .content-wrapper {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        /* Tab Navigation */
        .tab-menu {
            display: flex;
            border-bottom: 2px solid var(--primary-color);
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 12px 25px;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            background-color: #f1f1f1;
            border: 1px solid #ccc;
            border-bottom: none;
            border-radius: 6px 6px 0 0;
            transition: background-color 0.3s, color 0.3s;
            margin-right: 5px;
        }

        .tab-btn.active {
            background-color: var(--card-bg);
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            border-bottom: 2px solid var(--card-bg); /* 밑줄 겹침 방지 */
            z-index: 1;
        }
        
        /* Tab Content */
        .tab-content {
            display: none;
            padding: 10px 0;
        }
        .tab-content.active {
            display: block;
        }

        /* Search & Title */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .report-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
        }
        .btn-box {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .input {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
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
        }

        /* Data Table */
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
        }
        .list tbody td {
            border: 1px solid var(--table-border);
            padding: 10px 8px;
        }
        .list tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }
        
        /* Summary Cards (KPIs) */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: #e0f7fa; /* 연한 청록색 */
            padding: 20px;
            border-radius: 6px;
            border-left: 5px solid var(--primary-color);
        }
        .card h4 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #555;
        }
        .card p {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
        }

        /* Highlight Colors */
        .pass-text { color: var(--status-good); font-weight: 700; }
        .fail-text { color: var(--status-bad); font-weight: 700; }
        .total-row { font-weight: 700; background-color: #d4f5f5 !important; }

    </style>
</head>
<body>

    <div class='main-container'>
        <div class='content-wrapper'>
            
            <div class="tab-menu">
                <button class="tab-btn active" onclick="openReport('daily')">🌞 일일 생산 현황</button>
                <button class="tab-btn" onclick="openReport('weekly')">🗓️ 주간 생산 현황</button>
                <button class="tab-btn" onclick="openReport('monthly')">📅 월간 생산 현황</button>
            </div>

            <div id="daily" class="tab-content active">
                <div class="report-header">
                    <div class="report-title">일일 생산 실적 (금일: 2025-11-11)</div>
                    <div class="btn-box">
                        <input type='date' class='input' id='daily_date' value="2025-11-11"/>
                        <input type='button' class='btn' value='조회' onclick='searchDailyReport()' />
                    </div>
                </div>
                
                <div class="summary-cards" id="daily-summary">
                    </div>

                <table class='list'>
                    <thead>
                        <tr>
                            <th>시간대</th>
                            <th>작업자 수</th>
                            <th>생산 품목</th>
                            <th>지시 수량</th>
                            <th>생산 수량</th>
                            <th>합격 수량</th>
                            <th>불량 수량</th>
                            <th>합격률</th>
                        </tr>
                    </thead>
                    <tbody id="daily-report-body">
                        </tbody>
                </table>
            </div>

            <div id="weekly" class="tab-content">
                <div class="report-header">
                    <div class="report-title">주간 생산 실적 (W46: 2025-11-10 ~ 2025-11-16)</div>
                    <div class="btn-box">
                        <input type='week' class='input' id='weekly_week' value="2025-W46"/>
                        <input type='button' class='btn' value='조회' onclick='searchWeeklyReport()' />
                    </div>
                </div>
                
                <table class='list'>
                    <thead>
                        <tr>
                            <th>날짜</th>
                            <th>총 생산 수량</th>
                            <th>총 합격 수량</th>
                            <th>총 불량 수량</th>
                            <th>평균 합격률</th>
                            <th>주요 이슈</th>
                        </tr>
                    </thead>
                    <tbody id="weekly-report-body">
                        </tbody>
                </table>
            </div>

            <div id="monthly" class="tab-content">
                <div class="report-header">
                    <div class="report-title">월간 생산 실적 (2025년 11월)</div>
                    <div class="btn-box">
                        <input type='month' class='input' id='monthly_month' value="2025-11"/>
                        <input type='button' class='btn' value='조회' onclick='searchMonthlyReport()' />
                    </div>
                </div>
                
                <table class='list'>
                    <thead>
                        <tr>
                            <th>주차</th>
                            <th>총 생산 수량</th>
                            <th>총 합격 수량</th>
                            <th>총 불량 수량</th>
                            <th>평균 합격률</th>
                            <th>지시 달성률</th>
                        </tr>
                    </thead>
                    <tbody id="monthly-report-body">
                        </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        // ===============================================
        // Mock Data
        // ===============================================
        const mockDailyData = [
            { time: '09:00~12:00', workers: 5, item: '스마트칩', ordered: 1000, produced: 980, pass: 970, fail: 10 },
            { time: '13:00~18:00', workers: 6, item: '스마트칩', ordered: 1500, produced: 1520, pass: 1500, fail: 20 },
            { time: '19:00~22:00', workers: 3, item: '모듈케이스', ordered: 800, produced: 790, pass: 785, fail: 5 },
        ];

        const mockWeeklyData = [
            { date: '2025-11-10 (일)', total_prod: 0, total_pass: 0, total_fail: 0, issue: '휴일', rate: 0 },
            { date: '2025-11-11 (월)', total_prod: 3290, total_pass: 3255, total_fail: 35, issue: '정상 가동', rate: 98.9 },
            { date: '2025-11-12 (화)', total_prod: 3500, total_pass: 3400, total_fail: 100, issue: '설비 A 문제 발생', rate: 97.1 },
            { date: '2025-11-13 (수)', total_prod: 3350, total_pass: 3330, total_fail: 20, issue: '자재 B 투입', rate: 99.4 },
        ];
        
        const mockMonthlyData = [
            { week: '1주차 (11/01~11/03)', total_prod: 8500, total_pass: 8300, total_fail: 200, avg_rate: 97.6, order_rate: 95 },
            { week: '2주차 (11/04~11/10)', total_prod: 15000, total_pass: 14750, total_fail: 250, avg_rate: 98.3, order_rate: 100 },
            { week: '3주차 (11/11~11/17)', total_prod: 10140, total_pass: 10000, total_fail: 140, avg_rate: 98.6, order_rate: 90 },
        ];

        // ===============================================
        // Utility Functions
        // ===============================================

        /** 합격률 계산 및 렌더링 */
        function getYieldRate(pass, produced) {
            if (produced === 0) return { rate: '0.0%', color: 'gray' };
            const rate = (pass / produced) * 100;
            const color = rate >= 98 ? 'var(--status-good)' : rate >= 95 ? 'orange' : 'var(--status-bad)';
            return { rate: `${rate.toFixed(1)}%`, color: color };
        }

        /** 숫자 포맷팅 */
        function formatNumber(num) {
            return num.toLocaleString();
        }

        // ===============================================
        // Rendering Functions
        // ===============================================
        
        /** 탭 전환 핸들러 */
        function openReport(reportName) {
            const contents = document.querySelectorAll('.tab-content');
            const buttons = document.querySelectorAll('.tab-btn');

            contents.forEach(content => content.classList.remove('active'));
            buttons.forEach(button => button.classList.remove('active'));

            document.getElementById(reportName).classList.add('active');
            document.querySelector(`.tab-btn[onclick*="${reportName}"]`).classList.add('active');

            // 탭 전환 시 해당 데이터 재로딩
            if (reportName === 'daily') searchDailyReport();
            if (reportName === 'weekly') searchWeeklyReport();
            if (reportName === 'monthly') searchMonthlyReport();
        }

        /** 일일 생산현황 렌더링 */
        function renderDailyReport(data) {
            const body = document.getElementById('daily-report-body');
            const summaryCard = document.getElementById('daily-summary');
            body.innerHTML = '';
            
            let totalOrdered = 0;
            let totalProduced = 0;
            let totalPass = 0;
            let totalFail = 0;

            if (data.length === 0) {
                body.innerHTML = `<tr><td colspan='8'>해당 일자에 생산 실적이 없습니다.</td></tr>`;
                return;
            }

            data.forEach(item => {
                const { rate, color } = getYieldRate(item.pass, item.produced);
                totalOrdered += item.ordered;
                totalProduced += item.produced;
                totalPass += item.pass;
                totalFail += item.fail;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.time}</td>
                    <td>${item.workers}</td>
                    <td>${item.item}</td>
                    <td>${formatNumber(item.ordered)}</td>
                    <td>${formatNumber(item.produced)}</td>
                    <td class="pass-text">${formatNumber(item.pass)}</td>
                    <td class="fail-text">${formatNumber(item.fail)}</td>
                    <td style="color: ${color}; font-weight: 700;">${rate}</td>
                `;
                body.appendChild(row);
            });
            
            // 총 합계 행 추가
            const { rate: totalRate, color: totalColor } = getYieldRate(totalPass, totalProduced);
            body.innerHTML += `
                <tr class="total-row">
                    <td colspan="4">총 합계</td>
                    <td>${formatNumber(totalProduced)}</td>
                    <td>${formatNumber(totalPass)}</td>
                    <td>${formatNumber(totalFail)}</td>
                    <td style="color: ${totalColor}; font-weight: 900;">${totalRate}</td>
                </tr>
            `;

            // KPI 카드 업데이트
            summaryCard.innerHTML = `
                <div class="card"><h4>총 생산 수량</h4><p>${formatNumber(totalProduced)} EA</p></div>
                <div class="card"><h4>총 합격 수량</h4><p class="pass-text">${formatNumber(totalPass)} EA</p></div>
                <div class="card"><h4>총 불량 수량</h4><p class="fail-text">${formatNumber(totalFail)} EA</p></div>
                <div class="card"><h4>총 합격률</h4><p style="color: ${totalColor}">${totalRate}</p></div>
            `;
        }

        /** 주간 생산현황 렌더링 */
        function renderWeeklyReport(data) {
            const body = document.getElementById('weekly-report-body');
            body.innerHTML = '';

            if (data.length === 0) {
                body.innerHTML = `<tr><td colspan='6'>해당 주간에 생산 실적이 없습니다.</td></tr>`;
                return;
            }

            let grandTotalProd = 0;
            let grandTotalPass = 0;
            let grandTotalFail = 0;

            data.forEach(item => {
                grandTotalProd += item.total_prod;
                grandTotalPass += item.total_pass;
                grandTotalFail += item.total_fail;
                
                const { rate, color } = getYieldRate(item.total_pass, item.total_prod);

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.date}</td>
                    <td>${formatNumber(item.total_prod)}</td>
                    <td>${formatNumber(item.total_pass)}</td>
                    <td>${formatNumber(item.total_fail)}</td>
                    <td style="color: ${color}; font-weight: 700;">${rate}</td>
                    <td>${item.issue}</td>
                `;
                body.appendChild(row);
            });
            
            // 주간 합계 행 추가
            const { rate: totalRate, color: totalColor } = getYieldRate(grandTotalPass, grandTotalProd);
            body.innerHTML += `
                <tr class="total-row">
                    <td>주간 합계</td>
                    <td>${formatNumber(grandTotalProd)}</td>
                    <td>${formatNumber(grandTotalPass)}</td>
                    <td>${formatNumber(grandTotalFail)}</td>
                    <td style="color: ${totalColor}; font-weight: 900;">${totalRate}</td>
                    <td>-</td>
                </tr>
            `;
        }

        /** 월간 생산현황 렌더링 */
        function renderMonthlyReport(data) {
            const body = document.getElementById('monthly-report-body');
            body.innerHTML = '';

            if (data.length === 0) {
                body.innerHTML = `<tr><td colspan='6'>해당 월에 생산 실적이 없습니다.</td></tr>`;
                return;
            }

            let grandTotalProd = 0;
            let grandTotalPass = 0;
            let grandTotalFail = 0;

            data.forEach(item => {
                grandTotalProd += item.total_prod;
                grandTotalPass += item.total_pass;
                grandTotalFail += item.total_fail;
                
                const { rate, color } = getYieldRate(item.total_pass, item.total_prod);

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.week}</td>
                    <td>${formatNumber(item.total_prod)}</td>
                    <td>${formatNumber(item.total_pass)}</td>
                    <td>${formatNumber(item.total_fail)}</td>
                    <td style="color: ${color}; font-weight: 700;">${rate}</td>
                    <td>${item.order_rate}%</td>
                `;
                body.appendChild(row);
            });
            
            // 월간 합계 행 추가
            const { rate: totalRate, color: totalColor } = getYieldRate(grandTotalPass, grandTotalProd);
            body.innerHTML += `
                <tr class="total-row">
                    <td>월간 합계</td>
                    <td>${formatNumber(grandTotalProd)}</td>
                    <td>${formatNumber(grandTotalPass)}</td>
                    <td>${formatNumber(grandTotalFail)}</td>
                    <td style="color: ${totalColor}; font-weight: 900;">${totalRate}</td>
                    <td>-</td>
                </tr>
            `;
        }


        // ===============================================
        // Search Event Handlers (API Simulation)
        // ===============================================

        function searchDailyReport() {
            const date = document.getElementById('daily_date').value;
            console.log(`[일일 현황] 검색일: ${date}`);
            // TODO: API 호출: /api/production/daily?date=${date}
            renderDailyReport(mockDailyData); 
        }

        function searchWeeklyReport() {
            const week = document.getElementById('weekly_week').value;
            console.log(`[주간 현황] 검색 주차: ${week}`);
            // TODO: API 호출: /api/production/weekly?week=${week}
            renderWeeklyReport(mockWeeklyData); 
        }

        function searchMonthlyReport() {
            const month = document.getElementById('monthly_month').value;
            console.log(`[월간 현황] 검색 월: ${month}`);
            // TODO: API 호출: /api/production/monthly?month=${month}
            renderMonthlyReport(mockMonthlyData);
        }

        // ===============================================
        // Initial Load
        // ===============================================
        window.onload = () => {
            // 페이지 로드 시 일일 생산 현황을 기본으로 표시
            searchDailyReport();
        };
    </script>
</body>
</html>