<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style> 
/* 차트 그리드 스타일 */
.charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 30px; margin-bottom: 40px; }
.chart-card { background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); }
.chart-card h3 { font-size: 20px; color: #333; margin-bottom: 15px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
.chart-container { height: 350px; }    
</style>
</head>

<div class='main-container'>
    <div class='content-wrapper'>
        <div class="summary-stats">
            <div class="summary-card total-card">
                <h4>총 납기 실적 건수</h4>
                <div class="combined-metrics">
                    <div class="metric-group" style="width: 100%; text-align: center;">
                        <div class="number" id="totalShipments" style="font-size: 40px;">0</div>
                        <div class="unit" style="font-size: 18px;">건</div>
                    </div>
                </div>
            </div>
            <div class="summary-card target-card">
                <h4>기준 납기 기간</h4>
                <div class="number" id="kpiBase">0</div>
                <div class="unit" id="kpiBaseUnit">시간</div>
                <hr style="border-color: rgba(255,255,255,0.3); margin: 15px 0 10px 0;">
                <div style="font-size: 14px; color: white; margin-top: 5px;">
                    (목표 납기 기간: <strong id="targetValue">0</strong> 시간)
                </div>
            </div>
            <div class="summary-card avg-card">
                <h4>평균 납기 소요시간</h4>
                <div class="number" id="avgLeadTime">0</div>
                <div class="unit" id="avgLeadTimeUnit">시간</div>
            </div>
            <div class="summary-card kpi-card">
                <h4>KPI 달성 현황</h4>
                <div class="number" id="kpiAchievementRate">0.0</div>
                <div class="unit">%</div>
                <div style="font-size: 14px; color: white; margin-top: 5px;">
                    단축 소요 기간: <strong id="actualAvgValue">0</strong> 시간
                </div>
            </div>
        </div>
        <div class="charts-grid">
            <div class="chart-card">
                <h3>📈 월별 평균 납기 기간 추이 (단위: 시간)</h3>
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h3>📅 일별 납기 기간 실적 (단위: 시간)</h3>
                <div class="chart-container">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
        </div>
        <div class="data-table-container">
            <h3 style="margin-bottom: 20px; color: #333;">📋 상세 납기 실적</h3>
            <table class="list">
                <thead>
                    <tr>
                        <th>번호</th>
                        <th>거래처</th>
                        <th>제품명</th>
                        <th>주문일</th>
                        <th style="text-align: right;">수량</th>
                        <th style="text-align: right;">실제 납기일</th>
                        <th style="text-align: right;">납기 소요기간 (시간)</th>
                        <th>상태</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody"></tbody>
            </table>
            <div class="paging-area mt20"></div>
        </div>
    </div>
</div>

<script>
// =======================================================
// Javascript 입력 및 계산 로직 
// =======================================================
        
// 💡 조건부 포맷팅 함수: 소수점 이하가 0이면 정수만, 아니면 2자리 표시
function formatTimeValue(value) {
    const num = parseFloat(value);
    if (Number.isInteger(num)) {
        return num.toFixed(0);
    } else {
        return num.toFixed(2);
    }
}

// 💡 사용자에게 현재값, 목표값, 도달값을 입력받는 함수
function getInput() {
    let kpiBase, kpiTarget, avgLeadTime;

    // 1. 기준 납기 (현재값) 입력 (예: 56)
    kpiBase = prompt("1. 기준 납기 기간(현재값)을 입력하세요 (시간, 예: 56):", "56");
    if (!kpiBase || isNaN(kpiBase) || parseFloat(kpiBase) <= 0) return alert("유효한 기준 납기 시간을 입력해야 합니다."), false;
    kpiBase = parseFloat(kpiBase);

    // 2. 목표 납기 입력 (예: 52)
    kpiTarget = prompt("2. 목표 납기 기간을 입력하세요 (시간, 예: 52):", "52");
    if (!kpiTarget || isNaN(kpiTarget) || parseFloat(kpiTarget) <= 0 || parseFloat(kpiTarget) >= kpiBase) return alert("유효한 목표 납기 시간을 입력해야 하며, 기준 시간보다 짧아야 합니다."), false;
    kpiTarget = parseFloat(kpiTarget);

    // 3. 실제 도달값 입력 (평균 납기 소요시간, 예: 51.0)
    avgLeadTime = prompt("3. 실제 도달값(평균 납기 소요시간)을 입력하세요 (시간, 예: 51.0):", "51.0");
    if (!avgLeadTime || isNaN(avgLeadTime) || parseFloat(avgLeadTime) <= 0) return alert("유효한 평균 납기 소요시간을 입력해야 합니다."), false;
    avgLeadTime = parseFloat(avgLeadTime);
            
    return { kpiBase, kpiTarget, avgLeadTime };
}

/**
 * KPI 계산 및 화면 렌더링을 수행하는 메인 함수
 */
function calculateAndRender(input) {
    const { kpiBase, kpiTarget, avgLeadTime } = input;
    const unit = '시간';
    const dataCount = 50; // 차트 및 테이블에 사용할 데이터 건
    // 1. KPI 계산
    const targetImprovement = kpiBase - kpiTarget; // 목표 개선 폭 (예: 56-52=4)
    const actualImprovement = kpiBase - avgLeadTime; // 실제 개선 폭 (예: 56-51=5)
    let kpiAchievementRate = 0;
            
    if (targetImprovement > 0) {
        kpiAchievementRate = ((actualImprovement / targetImprovement) * 100).toFixed(1);
    }
            
    // 2. 더미 데이터 생성 (차트 및 테이블용)
    const shipmentData = [];
    const monthlySummary = {};
    const customers = ['(주)대한테크', '세종물산', '미래금속', '신화ENG'];
    const products = ['A-3000', 'B-4050', 'C-1002'];
    const today = new Date();

    for (let i = 0; i < dataCount; i++) {
        const date = new Date(today);
        date.setDate(today.getDate() - i);
        const shipmentDate = date.toISOString().split('T')[0];
        const orderDate = new Date(date);
        orderDate.setDate(date.getDate() - Math.floor(Math.random() * 3 + 3)); // 3~5일 전 주문

        const monthKey = shipmentDate.substring(0, 7);

        // 도달값(avgLeadTime) 주변 1.0시간 내에서 랜덤하게 데이터 생성
        const minRange = avgLeadTime - 0.5;
        const maxRange = avgLeadTime + 0.5;
        const leadTimeHours = (Math.random() * (maxRange - minRange) + minRange).toFixed(2);
                
        const status = (leadTimeHours <= kpiTarget) ? '단축 성공' : '목표 초과';
        const statusClass = (leadTimeHours <= kpiTarget) ? 'status-ontime' : 'status-delayed';

        shipmentData.push({
            shipment_no: 1000 + i,
            customer: customers[Math.floor(Math.random() * customers.length)],
            product: products[Math.floor(Math.random() * products.length)],
            order_date: orderDate.toISOString().split('T')[0],
            shipment_date: shipmentDate,
            quantity: Math.floor(Math.random() * 91) + 10,
            lead_time_hours: parseFloat(leadTimeHours),
            status: status,
            status_class: statusClass,
        });

        // 월별 요약
        if (!monthlySummary[monthKey]) {
            monthlySummary[monthKey] = { total_hours: 0, count: 0 };
        }
        monthlySummary[monthKey].total_hours += parseFloat(leadTimeHours);
        monthlySummary[monthKey].count++;
    }

    // 월별 평균 계산
    const monthlyAvgLeadTimes = {};
    Object.keys(monthlySummary).sort().forEach(month => {
        const data = monthlySummary[month];
        monthlyAvgLeadTimes[month] = (data.total_hours / data.count).toFixed(2);
    });


    // 3. KPI 요약 카드 업데이트
    document.getElementById('totalShipments').innerText = dataCount;
    document.getElementById('kpiBase').innerText = formatTimeValue(kpiBase);
    document.getElementById('targetValue').innerText = formatTimeValue(kpiTarget);
    document.getElementById('avgLeadTime').innerText = formatTimeValue(avgLeadTime);
    document.getElementById('actualAvgValue').innerText = formatTimeValue(avgLeadTime);
    document.getElementById('kpiAchievementRate').innerText = kpiAchievementRate;
            

    // 4. 테이블 업데이트
    renderTable(shipmentData, unit);

    // 5. 차트 업데이트
    renderMonthlyChart(monthlyAvgLeadTimes, kpiTarget, unit);
    renderDailyChart(shipmentData, kpiTarget, unit);

    alert(`데이터가 업데이트되었습니다.\n\n[기준 납기: ${kpiBase}${unit}, 목표 납기: ${kpiTarget}${unit}, 도달값: ${avgLeadTime}${unit}]`);
}

// =======================================================
// 테이블 렌더링 함수
// =======================================================
function renderTable(data, unit) {
    const tbody = document.getElementById('dataTableBody');
    tbody.innerHTML = ''; // 기존 내용 삭제
            
    // 최신 데이터가 위에 오도록 역순으로 정렬
    const reversedData = [...data].reverse();

    reversedData.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${row.shipment_no}</td>
            <td>${row.customer}</td>
            <td>${row.product}</td>
            <td>${row.order_date}</td>
            <td style="text-align: right;">${row.quantity.toLocaleString()}</td>
            <td style="text-align: right;">${row.shipment_date}</td>
            <td style="text-align: right;">${formatTimeValue(row.lead_time_hours)}</td>
            <td><span class="${row.status_class}">${row.status}</span></td>
        `;
        tbody.appendChild(tr);
    });
}
        
// =======================================================
// 차트 렌더링 함수
// =======================================================

let monthlyChartInstance = null;
let dailyChartInstance = null;

function renderMonthlyChart(monthlyAvgLeadTimes, kpiTarget, unit) {
    const ctx = document.getElementById('monthlyChart').getContext('2d');
            
    // 기존 차트 인스턴스가 있다면 파괴
    if (monthlyChartInstance) {
        monthlyChartInstance.destroy();
    }

    const labels = Object.keys(monthlyAvgLeadTimes);
    const data = Object.values(monthlyAvgLeadTimes).map(v => parseFloat(v));
    const targetData = labels.map(() => kpiTarget);

    monthlyChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: `월별 평균 납기 기간 (${unit})`,
                    data: data,
                    backgroundColor: 'rgba(0, 123, 255, 0.7)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                },
                {
                    label: '목표 납기 기간',
                    data: targetData,
                    type: 'line',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 2,
                    fill: false,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, 
            scales: {
                y: {
                    beginAtZero: false,
                    min: Math.min(...data) > kpiTarget ? kpiTarget - 1 : Math.min(...data) - 1, // 최소값 설정으로 변화 폭 강조
                    title: { display: true, text: `평균 납기 소요기간 (${unit})` }
                }
            },
            plugins: {
                legend: { display: true },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            const value = context.parsed.y;
                            return label + formatTimeValue(value) + ` ${unit}`;
                        }
                    }
                }
            }
        }
    });
}

function renderDailyChart(shipmentData, kpiTarget, unit) {
    const ctx = document.getElementById('dailyChart').getContext('2d');
            
    // 기존 차트 인스턴스가 있다면 파괴
    if (dailyChartInstance) {
        dailyChartInstance.destroy();
    }

    const dailyData = shipmentData.map(row => ({
        date: row.shipment_date,
        lead_time: row.lead_time_hours
    })).sort((a, b) => new Date(a.date) - new Date(b.date));

    const dailyLabels = dailyData.map(row => row.date);
    const dailyLeadTimes = dailyData.map(row => row.lead_time);
    const targetData = dailyLabels.map(() => kpiTarget);
            
    dailyChartInstance = new Chart(ctx, {
        type: 'line', 
        data: {
            labels: dailyLabels,
            datasets: [
                {
                    label: `납기 소요기간 실적 (${unit})`,
                    data: dailyLeadTimes,
                    backgroundColor: 'rgba(40, 167, 69, 0.4)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    tension: 0.3, 
                    fill: false
                },
                {
                    label: '목표 납기 기간',
                    data: targetData,
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2,
                    fill: false,
                    pointRadius: 0 
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    min: Math.min(...dailyLeadTimes) > kpiTarget ? kpiTarget - 1 : Math.min(...dailyLeadTimes) - 1,
                    title: { display: true, text: `납기 소요기간 (${unit})` }
                },
                x: { ticks: { autoSkip: true, maxTicksLimit: 15 } }
            },
            plugins: {
                legend: { display: true },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            const value = context.parsed.y;
                            return label + formatTimeValue(value) + ` ${unit}`;
                        }
                    }
                }
            }
        }
    });
}

// 페이지 로드 시 입력 프롬프트 실행
document.addEventListener('DOMContentLoaded', () => {
    const inputValues = getInput();
    if (inputValues) {
        calculateAndRender(inputValues);
    } else {
        // 입력이 취소되거나 유효하지 않은 경우 기본값으로 렌더링
        calculateAndRender({ kpiBase: 56.0, kpiTarget: 52.0, avgLeadTime: 51.0 });
    }
});
</script>