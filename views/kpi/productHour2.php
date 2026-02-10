<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* 나머지 차트 및 테이블 스타일은 기존과 동일 */
.charts-grid { 
    display: grid; 
    grid-template-columns: 1fr 1fr; 
    gap: 30px; 
    margin-bottom: 40px; 
}
.chart-card { background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); }
.chart-card h3 { font-size: 20px; color: #333; margin-bottom: 15px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
.chart-container { height: 350px; } 
</style>

<div class='main-container'>
    <div class='content-wrapper'>
        <div class="summary-stats">
            <div class="summary-card kpi-card">
                <h4>KPI 달성률</h4>
                <div> 
                    <span class="number" id="kpiAchievementRate">0.0</span>
                    <span class="unit">%</span>
                </div>
            </div>
            <div class="summary-card target-card">
                <h4>목표 UPH</h4>
                <div> 
                    <span class="number" id="targetUph">0</span>
                    <span class="unit">개/시간</span>
                </div>
            </div>
            <div class="summary-card avg-card">
                <h4>도달한 UPH</h4>
                <div> 
                    <span class="number" id="achievedUph">0</span>
                    <span class="unit">개/시간</span>
                </div>
            </div>
            <div class="summary-card combined-card">
                <h4>총 월별 생산 현황</h4>
                <div class="main-metric">
                    <div>
                        <span class="number" id="totalQty">0</span>
                        <span class="unit">개</span>
                    </div>
                </div>
                <div class="sub-metric">
                    <span class="label sub-label">현재 UPH</span>
                    <span class="number sub-number" id="currentUphQtyCard">0</span>
                    <span class="unit sub-unit">개/시간</span>
                </div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <h3>📈 월별 시간당 생산량 (UPH) 추이 (<span id="periodDisplay"></span>)</h3>
                <div class="chart-container">
                    <canvas id="monthlyUphChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h3>📊 라인별 생산 비중 (가정 데이터)</h3>
                <div class="chart-container">
                    <canvas id="lineShareChart"></canvas>
                </div>
            </div>
        </div>

        <div class="data-table-container">
            <h3 style="margin-bottom: 20px; color: #333;">📋 상세 생산 목표 달성 데이터 (백엔드 연동)</h3>
            <table class="list">
                <thead>
                    <tr>
                        <th class="center">품명</th>
                        <th class="center">품번</th>
                        <th class="center">목표 수량</th>
                        <th class="center">실제 수량</th>
                        <th class="center">달성률 (%)</th>
                        <th class="center">작업 일자</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class='center' colspan='6'>데이터 로딩 중...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="paging-area mt20"></div>
    </div>
</div>
<script>
// =======================================================
// JavaScript 로직 (UPH 집중)
// =======================================================
        
// 상수 정의
const AVERAGE_WORKING_DAYS_PER_MONTH = 20; 
const AVERAGE_WORKING_HOURS_PER_DAY = 8;
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';
        
let monthlyChartInstance = null;
let lineChartInstance = null;

// 💡 사용자에게 값을 입력받는 함수 (UPH 관련 항목만)
function getInput() {
    let totalQty, targetUph, startMonth, endMonth;

    totalQty = prompt("1. 총 월별 생산 수량을 입력하세요 (숫자, 예: 100000):", "100000");
    if (!totalQty || isNaN(totalQty) || parseFloat(totalQty) <= 0) return alert("유효한 월별 생산 수량을 입력해야 합니다."), false;
    totalQty = parseFloat(totalQty);
            
    targetUph = prompt("2. 목표 시간당 생산량 (UPH)을 입력하세요 (숫자, 예: 650):", "650");
    if (!targetUph || isNaN(targetUph) || parseFloat(targetUph) <= 0) return alert("유효한 목표 UPH를 입력해야 합니다."), false;
    targetUph = parseFloat(targetUph);

    startMonth = prompt("3. 시작 월을 입력하세요 (YYYY-MM, 예: 2024-01):", "2024-01");
    if (!startMonth || !/^\d{4}-\d{2}$/.test(startMonth)) return alert("시작 월은 YYYY-MM 형식이어야 합니다."), false;

    endMonth = prompt("4. 종료 월을 입력하세요 (YYYY-MM, 예: 2024-06):", "2024-06");
    if (!endMonth || !/^\d{4}-\d{2}$/.test(endMonth)) return alert("종료 월은 YYYY-MM 형식이어야 합니다."), false;
            
    return { totalQty, targetUph, startMonth, endMonth };
}

// 💡 월별 UPH 데이터를 시뮬레이션하는 함수
function generateMonthlyUphData(currentUph, monthCount, startMonth) {
    const monthlyData = [];
    const startDate = new Date(startMonth + '-01');
    const initialUph = Math.max(100, currentUph * 0.90); 
    const monthlyIncrease = (currentUph - initialUph) / (monthCount - 1);
    let simulatedUph = initialUph;

    for (let i = 0; i < monthCount; i++) {
        const date = new Date(startDate);
        date.setMonth(startDate.getMonth() + i);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const monthKey = `${year}-${month}`;

        let uph = (i === monthCount - 1) 
            ? currentUph 
            : simulatedUph + (Math.random() - 0.5) * monthlyIncrease * 0.8; 
        simulatedUph += monthlyIncrease;
        uph = Math.round(uph);

        monthlyData.push({ month: monthKey, uph: uph });
    }
    return monthlyData;
}


// 💡 차트 및 요약 통계 업데이트 메인 함수
function calculateAndRender(input) {
    const { totalQty, targetUph, startMonth, endMonth } = input;

    const startDate = new Date(startMonth);
    const endDate = new Date(endMonth);
    const monthCount = (endDate.getFullYear() - startDate.getFullYear()) * 12 + (endDate.getMonth() - startDate.getMonth()) + 1;
            
    if (monthCount < 1) return alert("유효한 기간을 설정해야 합니다."), false;

    const monthlyWorkingHours = AVERAGE_WORKING_HOURS_PER_DAY * AVERAGE_WORKING_DAYS_PER_MONTH;
    const currentUph = Math.round(totalQty / monthlyWorkingHours); 
    const kpiAchievementRate = ((currentUph / targetUph) * 100).toFixed(1);

    const monthlyUphData = generateMonthlyUphData(currentUph, monthCount, startMonth);

    // 1. KPI 요약 카드 업데이트 
    document.getElementById('kpiAchievementRate').innerText = kpiAchievementRate;
            
    // 2. 목표 UPH 카드 업데이트
    document.getElementById('targetUph').innerText = comma(targetUph);
            
    // 3. 도달한 UPH 카드 업데이트
    document.getElementById('achievedUph').innerText = comma(currentUph);
            
    // 4. 총 월별 생산 수량 카드 업데이트 (통합됨)
    document.getElementById('totalQty').innerText = comma(totalQty);
    // 💡 현재 UPH 값 설정 부분
    document.getElementById('currentUphQtyCard').innerText = comma(currentUph); 
            
    // 5. 기간 표시 업데이트 
    document.getElementById('periodDisplay').innerText = `${startMonth} ~ ${endMonth}`;

    // 6. 차트 데이터 준비 및 생성
    const lineShareData = [
        { line: 'A라인', qty: Math.round(totalQty * 0.40) },
        { line: 'B라인', qty: Math.round(totalQty * 0.30) },
        { line: 'C라인', qty: Math.round(totalQty * 0.20) },
        { line: 'D라인', qty: Math.round(totalQty * 0.10) }
    ];
            
    createCharts(monthlyUphData, targetUph, lineShareData);

    // 7. 상세 테이블
    getProductionDetail({ totalQty, startMonth, endMonth, monthCount });
}

// 💡 차트 생성 함수
function createCharts(monthlyUphData, targetUph, lineShareData) {
    if (monthlyChartInstance) monthlyChartInstance.destroy();
    if (lineChartInstance) lineChartInstance.destroy();

    const monthlyCtx = document.getElementById('monthlyUphChart').getContext('2d');
    monthlyChartInstance = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyUphData.map(item => item.month),
            datasets: [
                {
                    label: '월별 UPH (개/시간)',
                    data: monthlyUphData.map(item => item.uph),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4, fill: true
                },
                 {
                    label: '목표 UPH (개/시간)',
                    data: monthlyUphData.map(() => targetUph),
                    borderColor: '#28a745',
                    borderWidth: 2, borderDash: [5, 5], pointRadius: 0, fill: false
                }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.1)' } }, x: { grid: { display: false } } } }
    });

    const lineCtx = document.getElementById('lineShareChart').getContext('2d');
    const hasData = lineShareData && lineShareData.length > 0 && lineShareData.some(item => item.qty > 0);
            
    if (hasData) {
        lineChartInstance = new Chart(lineCtx, {
            type: 'doughnut',
            data: {
                labels: lineShareData.map(item => item.line),
                datasets: [{
                    data: lineShareData.map(item => item.qty),
                    backgroundColor: [ '#007bff', '#ffc107', '#28a745', '#dc3545' ]
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    } else {
         lineCtx.canvas.parentNode.innerHTML = '<div style="height:350px; display:flex; justify-content:center; align-items:center; color:#6c757d;">데이터가 없습니다.</div>';
    }
}

// 💡 상세 데이터 테이블 Fetch 함수 (기간 확장 로직 포함)
const getProductionDetail = async ({ totalQty, startMonth, endMonth, monthCount }) => {    
    const monthlyTargetQty = totalQty / monthCount;
    const detailedData = [];
            
    const itemTemplates = [
        { name: '제품-A', code: 'A001', share: 0.35, ach_rate: 0.98 },
        { name: '제품-B', code: 'B005', share: 0.25, ach_rate: 1.05 },
        { name: '제품-C', code: 'C011', share: 0.20, ach_rate: 0.90 },
        { name: '제품-D', code: 'D002', share: 0.10, ach_rate: 1.10 },
        { name: '제품-E', code: 'E012', share: 0.10, ach_rate: 0.95 }
    ];

    const startDate = new Date(startMonth + '-01');

    for (let m = 0; m < monthCount; m++) {
        const date = new Date(startDate);
        date.setMonth(startDate.getMonth() + m);
        const monthKey = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
                
        // 해당 월의 데이터를 시뮬레이션
        for (let i = 0; i < itemTemplates.length; i++) {
            const template = itemTemplates[i];
                    
            const target_qty = Math.round(monthlyTargetQty * template.share);
            const actual_qty = Math.round(target_qty * template.ach_rate * (1 + (Math.random() - 0.5) * 0.05));

            detailedData.push({
                item_name: template.name,
                item_code: template.code,
                target_qty: target_qty,
                actual_qty: actual_qty,
                created_dt: `${monthKey}-15 12:00` // 월별 대표 날짜로 설정
            });
        }
    }

    const dummyData = { result: 'success', data: detailedData, total: detailedData.length };
    const tableBody = document.querySelector('.list tbody');
    tableBody.innerHTML = generateTableContent(dummyData);
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='6'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => {
        const achievementRate = ((item.actual_qty / item.target_qty) * 100).toFixed(1);
        return `
            <tr>
                <td class='center'>${item.item_name}</td>
                <td class='center'>${item.item_code}</td>
                <td class='center'>${comma(item.target_qty)}</td>
                <td class='center'>${comma(item.actual_qty)}</td>
                <td class='center'>${achievementRate}</td>
                <td class='center'>${item.created_dt}</td>
            </tr>
        `;
    }).join('');
};

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

// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', async function() {
    const inputValues = getInput();
    if (inputValues) {
        calculateAndRender(inputValues);
    } else {
        calculateAndRender({ totalQty: 100000, targetUph: 650, startMonth: '2024-01', endMonth: '2024-06' });
    }
    animateCards();
});
</script>