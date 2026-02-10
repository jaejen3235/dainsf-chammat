<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* 차트 그리드 스타일 */
.charts-grid { 
    display: grid; 
    grid-template-columns: 1fr; 
    gap: 30px; 
    margin-bottom: 40px; 
}
.chart-card { background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); }
.chart-card h3 { font-size: 20px; color: #333; margin-bottom: 15px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
.chart-container { height: 350px; } 
        
        
/* 테이블 변동 표시 */
.diff-increase { color: #dc3545; font-weight: bold; }
.diff-decrease { color: #28a745; font-weight: bold; }
</style>

<div class='main-container'>
    <div class='content-wrapper'>
        <div class="summary-stats">
            <div class="summary-card">
                <h4>현재(최종 월) 재고비용</h4>
                <div class="number" id="currentCost">0</div>
                <div class="unit">원</div>
            </div>
            <div class="summary-card target-card">
                <h4>목표 재고금액</h4>
                <div class="number" id="targetCost">0</div>
                <div class="unit">원</div>
            </div>
            <div class="summary-card kpi-card">
                <h4>KPI 달성률 (재고 감축 목표)</h4>
                <div class="number" id="kpiAchievementRate">0.0</div>
                <div class="unit">%</div>
            </div>
            <div class="summary-card">
                <h4>전월 대비 증감액</h4>
                <div class="number" id="costChangeRate">0</div>
                <div class="unit" id="costChangeUnit">원 (0.0%)</div>
            </div>
        </div>
        <div class="charts-grid">
            <div class="chart-card">
                <h3>📈 월별 재고금액 변동 추이 (<span id="periodDisplay">기간:</span>)</h3>
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div> 	 	 
        </div>
        <div class="data-table-container">
            <h3 style="margin-bottom: 20px; color: #333;">📋 월별 재고 마감 데이터</h3>
            <table class="list">
                <thead>
                    <tr>
                        <th class="center">년월</th>
                        <th class="right">재고금액 (원)</th>
                        <th class="right">전월 대비 변동 (원)</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody">
                    </tbody>
            </table>
            <div class="paging-area mt20"></div>
        </div>
    </div>
</div>

<script>
// =======================================================
// Javascript 입력 및 계산 로직 
// =======================================================
function getInput() {
    let currentCost, targetCost, achievedCost, startMonth, endMonth;

    currentCost = prompt("1. 현재/기준 재고금액을 입력하세요 (숫자, 예: 500000000):", "500000000");
    if (!currentCost || isNaN(currentCost) || parseFloat(currentCost) <= 0) return alert("유효한 현재 재고금액을 입력해야 합니다."), false;
    currentCost = parseFloat(currentCost);

    targetCost = prompt("2. 목표 재고금액을 입력하세요 (숫자, 예: 450000000):", "450000000");
    if (!targetCost || isNaN(targetCost) || parseFloat(targetCost) <= 0) return alert("유효한 목표 재고금액을 입력해야 합니다."), false;
    targetCost = parseFloat(targetCost);
            
    achievedCost = prompt("3. 달성 재고금액을 입력하세요 (숫자, 예: 440000000):", "440000000");
    if (!achievedCost || isNaN(achievedCost) || parseFloat(achievedCost) <= 0) return alert("유효한 달성 재고금액을 입력해야 합니다."), false;
    achievedCost = parseFloat(achievedCost);

    startMonth = prompt("4. 시작 월을 입력하세요 (YYYY-MM, 예: 2024-01):", "2024-01");
    if (!startMonth || !/^\d{4}-\d{2}$/.test(startMonth)) return alert("시작 월은 YYYY-MM 형식이어야 합니다."), false;

    endMonth = prompt("5. 종료 월을 입력하세요 (YYYY-MM, 예: 2024-06):", "2024-06");
    if (!endMonth || !/^\d{4}-\d{2}$/.test(endMonth)) return alert("종료 월은 YYYY-MM 형식이어야 합니다."), false;
            
    return { currentCost, targetCost, achievedCost, startMonth, endMonth };
}

function generateMonthlyData(startMonth, endMonth, achievedCost, monthCount) {
    const monthlyData = [];
    const currentDate = new Date(startMonth + '-01');
            
    let initialAmount = achievedCost + 60000000; 
    const monthlyReduction = (initialAmount - achievedCost) / (monthCount - 1);

    let currentAmount = initialAmount;
            
    for (let i = 0; i < monthCount; i++) {
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const monthKey = `${year}-${month}`;
                
        let stockAmount = currentAmount;
        if (i < monthCount - 1) {
            currentAmount -= monthlyReduction;
            const randomFactor = (Math.random() * 0.02 - 0.01); 
            stockAmount = stockAmount + (stockAmount * randomFactor);
        } else {
            stockAmount = achievedCost;
        }
                
        monthlyData.push({
            month: monthKey,
            stock_amount: Math.round(stockAmount)
        });
                
        currentDate.setMonth(currentDate.getMonth() + 1);
    }
            
    return monthlyData;
}

function calculateAndRender(input) {
    const { currentCost, targetCost, achievedCost, startMonth, endMonth } = input;

    const startDate = new Date(startMonth);
    const endDate = new Date(endMonth);
    const monthCount = (endDate.getFullYear() - startDate.getFullYear()) * 12 + (endDate.getMonth() - startDate.getMonth()) + 1;
            
    if (monthCount < 2) return alert("최소 2개월 이상의 기간을 설정해야 합니다."), false;

    const monthlyData = generateMonthlyData(startMonth, endMonth, achievedCost, monthCount);
    const finalData = monthlyData[monthlyData.length - 1];

    const targetReduction = currentCost - targetCost;
    const actualReduction = currentCost - finalData.stock_amount;
    let kpiAchievementRate = 0;
            
    if (targetReduction > 0) {
        kpiAchievementRate = ((actualReduction / targetReduction) * 100).toFixed(1);
    } else if (currentCost <= targetCost) {
        kpiAchievementRate = 100.0;
    } else {
        kpiAchievementRate = 0.0;
    }

    let costChangeAmount = 0;
    let costChangeRate = 0;
    let costChangeSign = '—';
            
    if (monthlyData.length >= 2) {
        const currentMonthCost = finalData.stock_amount;
        const prevMonthCost = monthlyData[monthlyData.length - 2].stock_amount;
        costChangeAmount = currentMonthCost - prevMonthCost;
                
        if (prevMonthCost !== 0) {
            costChangeRate = ((costChangeAmount / prevMonthCost) * 100).toFixed(1);
        }
                
        costChangeSign = (costChangeAmount > 0) ? '▲' : (costChangeAmount < 0 ? '▼' : '—');
    }

    // 5. KPI 요약 카드 업데이트
    document.getElementById('currentCost').innerText = comma(finalData.stock_amount);
    document.getElementById('targetCost').innerText = comma(targetCost);
            
    document.getElementById('kpiAchievementRate').innerText = kpiAchievementRate;
            
    let changeDisplay = `${costChangeSign} ${comma(Math.abs(costChangeAmount))}`;
    let changeUnitDisplay = `원 (${costChangeRate}%)`;
            
    // 증감액 텍스트 색상을 빨간색으로 고정
    const fixedRedColor = '#dc3545'; 

    document.getElementById('costChangeRate').innerHTML = `<span style="color: ${fixedRedColor};">${changeDisplay}</span>`;
    document.getElementById('costChangeUnit').innerHTML = `<span style="color: ${fixedRedColor}; font-size: 14px;">${changeUnitDisplay}</span>`;
            
    document.getElementById('periodDisplay').innerText = `기간: ${startMonth} ~ ${endMonth}`;

    // 6. 테이블 및 차트 업데이트
    renderTable(monthlyData.reverse());
    createCharts(monthlyData.reverse(), targetCost);

    alert(`데이터가 업데이트되었습니다.\n\n[기간: ${startMonth} ~ ${endMonth}]`);
}
        
// 💡 테이블 렌더링 함수
function renderTable(data) {
    const tbody = document.getElementById('dataTableBody');
    tbody.innerHTML = '';
            
    data.forEach((item, index) => {
        let diffDisplay = '';
        let changeClass = '';
                
        const prev = index < data.length - 1 ? data[index + 1] : null; 
                
        if (prev) {
            const delta = item.stock_amount - prev.stock_amount;
            if (delta > 0) {
                changeClass = 'diff-increase';
                diffDisplay = `▲ ${comma(delta)}`;
            } else if (delta < 0) {
                changeClass = 'diff-decrease';
                diffDisplay = `▼ ${comma(Math.abs(delta))}`;
            } else {
                diffDisplay = `—`;
                changeClass = 'center';
            }
        } else {
            diffDisplay = `<span style="color:#999;">-</span>`;
            changeClass = 'center';
        }

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class='center'>${item.month}</td>
            <td class='right'>${comma(item.stock_amount)}</td>
            <td class='right ${changeClass}'>${diffDisplay}</td>
        `;
        tbody.appendChild(tr);
    });
}
        
let monthlyChartInstance = null;

// 💡 차트 생성 함수
function createCharts(data, targetCost) {
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            
    const chartData = [...data].reverse();
            
    if (monthlyChartInstance) {
        monthlyChartInstance.destroy();
    }
            
    monthlyChartInstance = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.month),
            datasets: [
                {
                    label: '재고금액',
                    data: chartData.map(item => item.stock_amount),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: '목표금액',
                    data: chartData.map(() => targetCost),
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: false,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            return label + comma(context.parsed.y) + '원';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    grid: { color: 'rgba(0, 0, 0, 0.1)' },
                    ticks: { callback: (value) => comma(value) + '원' }
                },
                x: { grid: { display: false } }
            }
        }
    });
}

// 💡 카드 애니메이션
function animateCards() {
    const cards = document.querySelectorAll('.chart-card, .summary-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// 페이지 로드 시 입력 프롬프트 실행
document.addEventListener('DOMContentLoaded', () => {
    const inputValues = getInput();
    if (inputValues) {
        calculateAndRender(inputValues);
    } else {
        calculateAndRender({ 
            currentCost: 500000000, 
            targetCost: 450000000, 
            achievedCost: 440000000, 
            startMonth: '2024-01', 
            endMonth: '2024-06' 
        });
    }
    animateCards();
});
</script>