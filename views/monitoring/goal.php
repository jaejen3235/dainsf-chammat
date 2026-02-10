<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>목표 대비 달성률 대시보드</title>
    <style>
        /* ======================================= */
        /* Custom CSS */
        /* ======================================= */
        :root {
            --primary-color: #ff9800;    /* Goal Orange: 목표 대비 달성률 색상 */
            --background: #f8f9fa;       
            --card-bg: white;
            --main-font: #343a40;
            --table-border: #dee2e6;
            --header-bg: #e9ecef;
            --status-success: #28a745;   /* 달성 성공 (Green) */
            --status-fail: #dc3545;      /* 달성 미달 (Red) */
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
            margin-bottom: 20px;
        }

        /* Search & Title */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
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
        .input, .select {
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
        .btn:hover { background-color: #e68900; }

        /* KPI Summary Cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: #fff3e0; /* 연한 오렌지 배경 */
            padding: 20px;
            border-radius: 6px;
            border-left: 5px solid var(--primary-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .card h4 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #666;
        }
        .card p {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
        }
        .card p.success { color: var(--status-success); }
        .card p.fail { color: var(--status-fail); }

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
            vertical-align: middle;
        }
        .list tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }
        .total-row { font-weight: 700; background-color: #ffe0b2 !important; }
        
        /* Progress Bar (시각화) */
        .progress-cell {
            padding: 8px !important;
            text-align: left !important;
            vertical-align: middle !important;
        }
        .progress-bar {
            width: 100%;
            height: 24px;
            min-height: 24px;
            background-color: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
            display: block;
            box-sizing: border-box;
        }
        .progress-fill {
            height: 24px;
            min-height: 24px;
            width: 0%;
            background-color: var(--primary-color);
            transition: width 0.5s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            position: relative;
            box-sizing: border-box;
            font-size: 11px;
            font-weight: 700;
            color: white;
            padding-right: 6px;
            white-space: nowrap;
        }
        .progress-over {
            background-color: var(--status-success); /* 100% 초과 시 녹색 */
        }
    </style>
</head>
<body>

    <div class='main-container'>
        <div class='content-wrapper'>
            
            <div class="report-header">
                <div class="report-title">🎯 목표 대비 생산 달성률 대시보드</div>
                <div class="btn-box">
                    <input type='month' class='input' id='target_month' value="2025-11"/>
                    <input type='button' class='btn' value='조회' onclick='searchGoalPerformance()' />
                </div>
            </div>
            
            <div class="summary-cards" id="goal-summary-cards">
                </div>

            <table class='list'>
                <thead>
                    <tr>
                        <th>품번</th>
                        <th>품목명</th>
                        <th>목표 수량</th>
                        <th>실적 수량</th>
                        <th>미달/초과 수량</th>
                        <th>달성률</th>
                        <th style="width: 25%;">시각화</th>
                    </tr>
                </thead>
                <tbody id="goal-performance-body">
                    </tbody>
            </table>

        </div>
    </div>

    <script>
        // ===============================================
        // Mock Data: 목표 및 실적 데이터
        // 목표 데이터는 별도 관리 시스템에서 가져온다고 가정
        // 실적 데이터는 생산 실적 페이지에서 사용된 데이터를 재사용한다고 가정
        // ===============================================
        const mockGoalData = [
            { item_code: 'SM-C001', item_name: '스마트칩', target_qty: 6000, actual_qty: 4800 },
            { item_code: 'MO-K101', item_name: '모듈케이스', target_qty: 1500, actual_qty: 1550 }, // 초과 달성
            { item_code: 'SE-P005', item_name: '센서부품', target_qty: 3000, actual_qty: 1000 },
            { item_code: 'NEW-P100', item_name: '신규부품', target_qty: 1000, actual_qty: 980 },
        ];

        const tableBody = document.getElementById('goal-performance-body');
        const summaryCards = document.getElementById('goal-summary-cards');

        // ===============================================
        // Utility Functions
        // ===============================================

        /** 목표 달성률 계산 */
        function calculateAttainment(actual, target) {
            if (target === 0) return { rate: 0, status: 'N/A' };
            const rate = (actual / target) * 100;
            const diff = actual - target;
            const status = rate >= 100 ? 'SUCCESS' : 'FAIL';
            return { rate: rate, diff: diff, status: status };
        }

        /** 숫자 포맷팅 */
        function formatNumber(num) {
            return num.toLocaleString();
        }

        // ===============================================
        // Rendering Functions
        // ===============================================

        /** 전체 요약 카드 렌더링 */
        function renderSummaryCards(data) {
            let totalTarget = 0;
            let totalActual = 0;
            let achievedCount = 0;

            data.forEach(item => {
                const result = calculateAttainment(item.actual_qty, item.target_qty);
                totalTarget += item.target_qty;
                totalActual += item.actual_qty;
                if (result.status === 'SUCCESS') achievedCount++;
            });

            const totalResult = calculateAttainment(totalActual, totalTarget);
            const rateClass = totalResult.status === 'SUCCESS' ? 'success' : 'fail';
            const diffSign = totalResult.diff >= 0 ? '+' : '-';

            summaryCards.innerHTML = `
                <div class="card"><h4>총 목표 수량</h4><p>${formatNumber(totalTarget)} EA</p></div>
                <div class="card"><h4>총 생산 실적</h4><p>${formatNumber(totalActual)} EA</p></div>
                <div class="card"><h4>총 달성률</h4><p class="${rateClass}">${totalResult.rate.toFixed(1)}%</p></div>
                <div class="card"><h4>달성/미달 수량</h4><p class="${rateClass}">${diffSign}${formatNumber(Math.abs(totalResult.diff))} EA</p></div>
            `;
        }

        /** 품목별 상세 테이블 렌더링 */
        function renderGoalPerformanceList(data) {
            tableBody.innerHTML = '';
            
            if (data.length === 0) {
                tableBody.innerHTML = `<tr><td class='center' colspan='7'>검색된 목표 실적 자료가 없습니다</td></tr>`;
                return;
            }

            // 합계 계산을 위해 summaryCards 함수를 먼저 실행
            renderSummaryCards(data);

            data.forEach(item => {
                const result = calculateAttainment(item.actual_qty, item.target_qty);
                const rate = result.rate;
                const diff = result.diff;
                const diffSign = diff >= 0 ? '+' : '';
                const diffColor = diff >= 0 ? 'var(--status-success)' : 'var(--status-fail)';

                // Progress Bar 시각화 계산
                let barWidth = Math.min(Math.max(rate, 0), 100); // 달성률이 100%를 넘어도 시각적 바는 100%까지만 채움
                const barClass = rate >= 100 ? 'progress-fill progress-over' : 'progress-fill';
                const progressText = `${rate.toFixed(1)}%`;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.item_code}</td>
                    <td>${item.item_name}</td>
                    <td>${formatNumber(item.target_qty)}</td>
                    <td>${formatNumber(item.actual_qty)}</td>
                    <td style="color: ${diffColor}; font-weight: 700;">${diffSign}${formatNumber(diff)}</td>
                    <td style="color: ${diffColor}; font-weight: 700;">${rate.toFixed(1)}%</td>
                    <td class="progress-cell">
                        <div class="progress-bar">
                            <div class="${barClass}" style="width: ${barWidth}%;">
                                ${progressText}
                            </div>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
            
            // 총 합계는 Summary Cards에서 보여주므로 테이블 합계 행은 생략
        }

        // ===============================================
        // Event Handlers
        // ===============================================

        /** 목표 대비 실적 검색 */
        function searchGoalPerformance() {
            const month = document.getElementById('target_month').value;
            console.log(`[목표 대비 실적] 검색 월: ${month}`);
            
            // TODO: 실제 API 호출 (예: /api/goal/performance?month=${month})

            // Mockup 데이터 렌더링
            renderGoalPerformanceList(mockGoalData); 
        }

        // ===============================================
        // Initial Load
        // ===============================================
        window.onload = () => {
            searchGoalPerformance();
        };
    </script>
</body>
</html>