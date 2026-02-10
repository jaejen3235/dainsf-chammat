<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>설비 가동률 현황 최종 버전 (강제 표시)</title>
    <style>
        /* ======================================= */
        /* Custom CSS - 설비/보전 관련 색상 (Deep Purple) */
        /* ======================================= */
        :root {
            --primary-color: #673ab7;    
            --background: #f8f9fa;       
            --card-bg: white;
            --main-font: #343a40;
            --table-border: #dee2e6;
            --header-bg: #e9ecef;
            --status-good: #4caf50;      /* Green */
            --status-warn: #ffc107;      /* Yellow */
            --status-bad: #dc3545;       /* Red */
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

        /* Search & Title - (생략) */
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
        .btn:hover { background-color: #5e35b1; }

        /* KPI Summary Cards - (생략) */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: #ede7f6; 
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
        .card p.good { color: var(--status-good); }
        .card p.bad { color: var(--status-bad); }

        /* Data Table - (생략) */
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
        
        /* Progress Bar (핵심 시각화) */
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
            /* width는 JS에서 인라인으로 ${barWidth}% 지정 */
            /* background-color는 JS에서 인라인으로 ${color} 지정 */
        }
        
        .good-text { color: var(--status-good); }
        .warn-text { color: var(--status-warn); }
        .bad-text { color: var(--status-bad); }

    </style>
</head>
<body>

    <div class='main-container'>
        <div class='content-wrapper'>
            
            <div class="report-header">
                <div class="report-title">⚙️ 설비 가동률 현황 (최종)</div>
                <div class="btn-box">
                    <input type='date' class='input' id='start_date' value="2025-11-10"/>
                    <input type='date' class='input' id='end_date' value="2025-11-11"/>
                    <select class="select" id="line_select">
                        <option value="">전체 라인</option>
                        <option value="L-A">생산 라인 A</option>
                        <option value="L-B">생산 라인 B</option>
                    </select>
                    <input type='button' class='btn' value='조회' onclick='searchEquipmentStatus()' />
                </div>
            </div>
            
            <div class="summary-cards" id="equipment-summary-cards">
                </div>

            <table class='list'>
                <thead>
                    <tr>
                        <th style="width: 10%;">라인/구역</th>
                        <th style="width: 15%;">설비명</th>
                        <th style="width: 10%;">총 측정 시간 (분)</th>
                        <th style="width: 10%;">가동 시간 (분)</th>
                        <th style="width: 10%;">비가동 시간 (분)</th>
                        <th style="width: 10%;">가동률</th>
                        <th style="width: 30%;">가동률 시각화</th>
                    </tr>
                </thead>
                <tbody id="equipment-status-body">
                    </tbody>
            </table>

        </div>
    </div>

    <script>
        // ===============================================
        // Mock Data: 설비별 가동 현황 (기준: 1440분 = 24시간)
        // ===============================================
        const MOCK_TOTAL_TIME = 1440; // 24시간 * 60분
        
        // 색상 코드 (JS에서 인라인으로 style에 적용)
        const COLOR_GOOD = '#4caf50'; 
        const COLOR_WARN = '#ffc107'; 
        const COLOR_BAD = '#dc3545'; 

        const mockEquipmentData = [
            { id: 101, line: 'L-A', name: '가공기 #1', op_time: 1300, down_time: 140 }, 
            { id: 102, line: 'L-A', name: '조립 로봇', op_time: 1250, down_time: 190 }, 
            { id: 201, line: 'L-B', name: '검사 설비 #3', op_time: 950, down_time: 490 }, 
            { id: 202, line: 'L-B', name: '포장 라인', op_time: 1420, down_time: 20 },  
            { id: 103, line: 'L-A', name: '프레스기 #2', op_time: 1100, down_time: 340 }, 
        ];

        const tableBody = document.getElementById('equipment-status-body');
        const summaryCards = document.getElementById('equipment-summary-cards');

        // ===============================================
        // Utility Functions
        // ===============================================

        /**
         * 가동률을 계산하고 색상 코드를 반환합니다.
         */
        function calculateOperationRate(opTime, totalTime) {
            if (totalTime === 0) return { rate: 0, color: COLOR_BAD, rateClass: 'bad' };
            const rate = (opTime / totalTime) * 100;
            
            let color = COLOR_GOOD;
            let rateClass = 'good';

            if (rate < 80) {
                color = COLOR_BAD;
                rateClass = 'bad';
            } else if (rate < 90) {
                color = COLOR_WARN;
                rateClass = 'warn';
            }
            
            return { rate: rate, color: color, rateClass: rateClass };
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
            let totalOpTime = 0;
            let totalDownTime = 0;
            let totalTotalTime = data.length * MOCK_TOTAL_TIME; 

            data.forEach(item => {
                totalOpTime += item.op_time;
                totalDownTime += item.down_time;
            });

            const { rate: totalRate, rateClass } = calculateOperationRate(totalOpTime, totalTotalTime);
            
            const rateTextColor = rateClass === 'good' ? 'good' : 'bad';
            
            summaryCards.innerHTML = `
                <div class="card"><h4>총 가동률 (전체 평균)</h4><p class="${rateTextColor}">${totalRate.toFixed(1)}%</p></div>
                <div class="card"><h4>총 가동 시간</h4><p>${formatNumber(totalOpTime)} 분</p></div>
                <div class="card"><h4>총 비가동 시간</h4><p class="bad-text">${formatNumber(totalDownTime)} 분</p></div>
                <div class="card"><h4>총 측정 시간</h4><p>${formatNumber(totalTotalTime)} 분</p></div>
            `;
        }

        /** 설비별 상세 테이블 렌더링 (Progress Bar 포함) */
        function renderEquipmentStatusList(data) {
            tableBody.innerHTML = '';
            
            if (data.length === 0) {
                tableBody.innerHTML = `<tr><td class='center' colspan='7'>검색된 설비 현황 자료가 없습니다</td></tr>`;
                return;
            }

            // 1. 합계 계산 및 카드 업데이트
            renderSummaryCards(data);

            // 2. 상세 리스트 렌더링
            data.forEach(item => {
                const { rate, color, rateClass } = calculateOperationRate(item.op_time, MOCK_TOTAL_TIME);
                // 가동률이 100%를 초과해도 bar는 100%까지만 표시
                const barWidth = Math.min(Math.max(rate, 0), 100); 
                const progressText = `${rate.toFixed(1)}%`;
                
                const rateTextColorClass = rateClass + '-text'; // good-text, warn-text, bad-text

                const row = document.createElement('tr');
                // Progress Fill의 배경색과 너비를 JS에서 직접 인라인 스타일로 지정
                row.innerHTML = `
                    <td>${item.line}</td>
                    <td>${item.name}</td>
                    <td>${formatNumber(MOCK_TOTAL_TIME)}</td>
                    <td class="good-text">${formatNumber(item.op_time)}</td>
                    <td class="bad-text">${formatNumber(item.down_time)}</td>
                    <td style="font-weight: 700;" class="${rateTextColorClass}">${rate.toFixed(1)}%</td>
                    <td class="progress-cell">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${barWidth}%; background-color: ${color};">
                                ${progressText}
                            </div>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        // ===============================================
        // Event Handlers
        // ===============================================

        /** 설비 가동 현황 검색 */
        function searchEquipmentStatus() {
            const lineCode = document.getElementById('line_select').value;
            
            // Mockup 데이터 필터링 시뮬레이션
            let filteredData = mockEquipmentData;
            
            if (lineCode) {
                filteredData = mockEquipmentData.filter(item => item.line === lineCode);
            }

            renderEquipmentStatusList(filteredData); 
        }

        // ===============================================
        // Initial Load
        // ===============================================
        window.onload = () => {
            // 페이지 로드 시 자동으로 검색 실행
            searchEquipmentStatus();
        };
    </script>
</body>
</html>