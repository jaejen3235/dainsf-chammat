<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>월별 납품건수 대비 공수 현황</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>


        /* 요약 통계 카드 스타일 (4개 카드 구성) */
        .summary-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .summary-card { background-color: #007bff; border-radius: 8px; padding: 25px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); color: white; border-left: 5px solid #ffc107; }
        
        /* 카드별 강조 색상 */
        .summary-card.kpi-card { 
            background-color: #28a745; 
            border-left-color: #ffc107; 
            color: white; 
        }
        
        /* 카드별 강조 색상 */
        .summary-card.kpi-card { 
            background-color: #28a745; 
            border-left-color: #ffc107; 
            color: white; 
        }
        
        /* Target 카드 (납품건수, 총 공수) */
        .summary-card.target-card { 
            background-color: #6c757d; 
            border-left-color: #007bff; 
        }
        
        /* Variance 카드 */
        .summary-card.variance-card { 
             background-color: #6c757d; 
            border-left-color: #007bff; 
        }
        
        /* Variance 카드 색상 오버라이드 */
        .summary-card.variance-card.positive { 
            background-color: #28a745; 
            border-left-color: #ffc107;
        }
        .summary-card.variance-card.negative { 
            background-color: #dc3545; 
            border-left-color: #ffc107;
        }

        /* 폰트 스타일 */
        .summary-card h4 { 
            font-size: 16px; 
            margin-bottom: 15px; 
            font-weight: 600; 
            color: white; 
        }
        .summary-card .number { 
            font-size: 36px; 
            font-weight: 700; 
            display: inline-block;
            line-height: 1; 
            margin-top: auto; 
        }
        .summary-card .unit { 
            display: inline-block; 
            margin-left: 8px; 
            font-size: 18px; 
        }
        .summary-card .sub-text { font-size: 12px; opacity: 0.8; margin-top: 5px; }

        /* Chart Styles */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr; 
            gap: 30px;
            margin-top: 30px;
            margin-bottom: 40px;
        }
        .chart-card { 
            background-color: #fff; 
            padding: 25px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); 
        }
        .chart-card h3 { 
            font-size: 20px; 
            color: #333; 
            margin-bottom: 15px; 
            border-bottom: 2px solid #f0f0f0; 
            padding-bottom: 10px; 
        }
        .chart-container { height: 350px; }

        /* Separator */
        hr { border: 0; border-top: 1px solid #e0e0e0; margin: 30px 0; }
        
        /* Table Style */
        .data-table-container { margin-top: 30px; }
        .list { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
        .list thead { background-color: #007bff; color: #fff; }
        .list th, .list td { padding: 12px 15px; border: 1px solid #ddd; color: #333;}
        .list tbody tr:nth-child(even) { background-color: #f9f9f9; }
        .list tbody tr:hover { background-color: #e6f7ff; }
        .center { text-align: center !important; }
        
        /* Status/Trend Badges */
        .status-badge { 
            display: inline-block; 
            padding: 4px 8px; 
            border-radius: 4px; 
            font-weight: 600; 
            font-size: 12px;
        }
        .status-badge.efficient { background-color: #28a745; color: white; }
        .status-badge.neutral { background-color: #ffc107; color: #333; }
        .status-badge.overrun { background-color: #dc3545; color: white; }
        /* 🎨 CSS 스타일 시트 끝 */
    </style>
</head>
<body>
    <div class='main-container'>
        <h2>🗓️ 월별 납품건수 대비 인력 공수 현황 분석</h2>

        <div class='content-wrapper'>
            <div class="summary-stats">
                <div class="summary-card target-card" id="cardShipments">
                    <h4>당월 납품건수 (실제)</h4>
                    <div class="number" id="currentShipments">0</div><span class="unit">건</span>
                    <div class="sub-text" id="shipmentMonth"></div>
                </div>
                <div class="summary-card target-card" id="cardManHours">
                    <h4>당월 총 투입 공수 (실제)</h4>
                    <div class="number" id="currentManHours">0</div><span class="unit">M/H</span>
                    <div class="sub-text">실제 인력 투입 시간 합계</div>
                </div>

                <div class="summary-card kpi-card" id="cardKPI">
                    <h4>건당 공수 (핵심 효율 KPI)</h4>
                    <div class="number" id="mhPerShipment">0.00</div><span class="unit">M/H/건</span>
                    <div class="sub-text">공수가 낮을수록 효율적</div>
                </div>

                <div class="summary-card variance-card" id="cardVariance">
                    <h4>공수 차이 (계획 대비)</h4>
                    <div class="number" id="mhVariance">0</div><span class="unit">M/H</span>
                    <div class="sub-text">양수: 절감 (효율적), 음수: 초과 투입</div>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-card">
                    <h3>📊 지난 6개월간 납품 건수 및 공수 투입 추이</h3>
                    <div class="chart-container">
                        <canvas id="timeSeriesChart"></canvas>
                    </div>
                </div>
            </div>

             <hr>

             <div class="data-table-container">
                <h3 style="margin-bottom: 20px; color: #333;">📋 월별 공수 집계 상세 내역</h3>
                <table class="list">
                <thead>
                    <tr>
                        <th class="center">월</th>
                        <th class="center">납품 (계획)</th>
                        <th class="center">납품 (실제)</th>
                        <th class="center">공수 (계획 M/H)</th>
                        <th class="center">공수 (실제 M/H)</th>
                        <th class="center">공수 차이 (M/H)</th>
                        <th class="center">건당 공수 (M/H/건)</th>
                        <th class="center">공수 효율</th>
                    </tr>
                </thead>
                <tbody id="monthlyTableBody">
                    <tr><td class='center' colspan='8'>데이터 로딩 중...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <script>
        // 🚀 JavaScript 로직 시작 (데이터 및 차트 로직은 이전과 동일하게 유지)
        let monthlyData = [];
        let chartInstance = null;
        const NUM_MONTHS = 6; 

        function comma(value) {
            return Number(value).toLocaleString();
        }

        function generateMonthlyData(months) {
            const data = [];
            const today = new Date(2025, 10, 15);
            
            let basePlannedMH = 1200;
            let basePlannedShipments = 300;

            for (let i = months - 1; i >= 0; i--) {
                const date = new Date(today.getFullYear(), today.getMonth() - i, 1);
                const monthLabel = `${date.getFullYear().toString().slice(2)}.${(date.getMonth() + 1).toString().padStart(2, '0')}`;

                const mhFluctuation = (Math.random() - 0.5) * 200;
                const shipmentFluctuation = (Math.random() - 0.5) * 50;

                const plannedMH = Math.round(basePlannedMH + mhFluctuation * 0.5);
                const plannedShipments = Math.round(basePlannedShipments + shipmentFluctuation * 0.5);

                const actualMH = Math.round(plannedMH * (1 + (Math.random() * 0.1 - 0.05)));
                const actualShipments = Math.round(plannedShipments * (1 + (Math.random() * 0.1 - 0.03)));

                const varianceMH = plannedMH - actualMH;
                const mhPerShipment = actualShipments > 0 ? (actualMH / actualShipments) : 0;
                
                const plannedMhPerShipment = plannedShipments > 0 ? (plannedMH / plannedShipments) : 0;
                let efficiency = 0;
                if (plannedMhPerShipment > 0) {
                    efficiency = (1 - (mhPerShipment / plannedMhPerShipment)) * 100; 
                }

                data.push({
                    month: monthLabel,
                    plannedMH,
                    actualMH,
                    plannedShipments,
                    actualShipments,
                    varianceMH,
                    mhPerShipment,
                    efficiency: efficiency.toFixed(1)
                });
                
                basePlannedMH += 20;
                basePlannedShipments += 5;
            }
            return data;
        }

        function updateSummaryCards(data) {
            if (data.length === 0) return;
            
            const latestData = data[data.length - 1];
            
            document.getElementById('shipmentMonth').innerText = `(${latestData.month} 기준)`;
            document.getElementById('currentShipments').innerText = comma(latestData.actualShipments);
            document.getElementById('currentManHours').innerText = comma(latestData.actualMH);
            
            document.getElementById('mhPerShipment').innerText = latestData.mhPerShipment.toFixed(2);
            
            document.getElementById('mhVariance').innerText = comma(latestData.varianceMH);
            const varianceCard = document.getElementById('cardVariance');
            
            varianceCard.classList.remove('positive', 'negative');
            
            if (latestData.varianceMH >= 0) {
                varianceCard.classList.add('positive');
            } else {
                varianceCard.classList.add('negative');
            }
        }

        function createTimeSeriesChart(data) {
            if (chartInstance) chartInstance.destroy();
            
            const labels = data.map(item => item.month);
            const actualMHData = data.map(item => item.actualMH);
            const actualShipmentData = data.map(item => item.actualShipments);

            const ctx = document.getElementById('timeSeriesChart').getContext('2d');
            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: '실제 투입 공수 (M/H)',
                            data: actualMHData,
                            backgroundColor: '#007bff', 
                            yAxisID: 'y1',
                            borderRadius: 4
                        },
                        {
                            type: 'line',
                            label: '실제 납품 건수 (건)',
                            data: actualShipmentData,
                            borderColor: '#28a745', 
                            yAxisID: 'y2',
                            tension: 0.4,
                            pointBackgroundColor: '#28a745',
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: { display: true, text: '공수 (M/H)' }
                        },
                        y2: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: { display: true, text: '납품 건수 (건)' },
                            grid: { drawOnChartArea: false }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    }
                }
            });
        }

        function renderTable() {
            const tableBody = document.getElementById('monthlyTableBody');
            const displayData = [...monthlyData].reverse(); 

            if (monthlyData.length === 0) {
                tableBody.innerHTML = `<tr><td class='center' colspan='8'>데이터 로딩 오류</td></tr>`;
                return;
            }

            tableBody.innerHTML = displayData.map(item => {
                let statusClass = 'neutral';
                let statusText = '보통';
                
                if (item.varianceMH > item.plannedMH * 0.01) { 
                    statusClass = 'efficient';
                    statusText = '효율적 (절감)';
                } else if (item.varianceMH < item.plannedMH * -0.01) { 
                    statusClass = 'overrun';
                    statusText = '비효율적 (초과)';
                }

                return `
                    <tr>
                        <td class='center'>${item.month}</td>
                        <td class='center'>${comma(item.plannedShipments)}</td>
                        <td class='center'>${comma(item.actualShipments)}</td>
                        <td class='center'>${comma(item.plannedMH)}</td>
                        <td class='center'>${comma(item.actualMH)}</td>
                        <td class='center' style="color: ${item.varianceMH < 0 ? '#dc3545' : '#28a745'}; font-weight: 600;">
                            ${item.varianceMH < 0 ? '-' : ''}${comma(Math.abs(item.varianceMH))}
                        </td>
                        <td class='center'>${item.mhPerShipment.toFixed(2)}</td>
                        <td class='center'>
                            <span class="status-badge ${statusClass}">${item.efficiency}% (${statusText})</span>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        document.addEventListener('DOMContentLoaded', function() {
            monthlyData = generateMonthlyData(NUM_MONTHS);
            updateSummaryCards(monthlyData);
            renderTable();
            createTimeSeriesChart(monthlyData);
        });
        // 🚀 JavaScript 로직 끝
    </script>
</body>
</html>