<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    /* 차트 그리드 스타일 (두 개의 차트) */
    .charts-grid { 
        display: grid; 
        grid-template-columns: 1fr 1fr; /* 50% 50% */
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
        <div class="summary-card total-card">
                <h4>총 생산량</h4>
                <div class="combined-metrics">
                    <div class="metric-group" style="width: 100%; text-align: center;">
                        <div class="number" id="totalQty" style="font-size: 40px;">0</div>
                        <div class="unit" style="font-size: 18px;">개</div>
                    </div>
                </div>
            </div>
            <div class="summary-card target-card">
                <h4>기준 불량률</h4>
                <div class="number" id="kpiBase">0</div>
                <div class="unit" id="kpiBaseUnit">%</div>
                <hr style="border-color: rgba(255,255,255,0.3); margin: 15px 0 10px 0;">
                <div style="font-size: 14px; color: white; margin-top: 5px;">
                    (목표 불량률: <strong id="targetValue">0</strong> %)
                </div>
            </div>
            <div class="summary-card avg-card">
                <h4>평균 불량률</h4>
                <div class="number" id="avgDefectRate">0</div>
                <div class="unit" id="avgDefectRateUnit">%</div>
            </div>
            <div class="summary-card kpi-card">
                <h4>KPI 달성 현황</h4>
                <div class="number" id="kpiAchievementRate">0.0</div>
                <div class="unit">%</div>
                <div style="font-size: 14px; color: white; margin-top: 5px;">
                    불량률 단축: <strong id="actualAvgValue">0</strong> %
                </div>
            </div>
        </div>
        <div class="charts-grid">
            <div class="chart-card">
                <h3>📈 월별 불량율 추이 (<span id="periodDisplay"></span>) (최종 월 불량률 달성 기준)</h3>
                <div class="chart-container">
                    <canvas id="monthlyDefectChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h3>🍕 불량 유형별 분포 (가정 데이터)</h3>
                <div class="chart-container">
                    <canvas id="defectTypeChart"></canvas>
                </div>
            </div>
        </div>
        <div class="data-table-container">
            <h3 style="margin-bottom: 20px; color: #333;">📋 상세 불량 데이터 (백엔드 연동)</h3>
            <table class="list">
                <thead>
                    <tr>
                        <th class="center">품명</th>
                        <th class="center">품번</th>
                        <th class="center">규격</th>
                        <th class="center">불량 유형</th>
                        <th class="center">불량 수량</th>
                        <th class="center">등록 일자</th>
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
// JavaScript 로직 (김치 제조 특화)
// =======================================================
        
// 상수 정의 
const CONTROLLER = 'mes';
const MODE = 'getDefectStatDetail';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';
        
let monthlyChartInstance = null;
let typeChartInstance = null;

// 💡 콤마 포맷팅 함수
function comma(value) {
    // 숫자가 아닌 경우 0으로 처리
    if (value === undefined || value === null) return '0';
    return Number(value).toLocaleString();
}

// 💡 사용자에게 값을 입력받는 함수 (기준 불량률(baseDefectRate) 추가)
function getInput() {
    let totalQty, baseDefectRate, targetDefectRate, achievedDefectRate, startMonth, endMonth;

    // 1. 기준 생산량 (완제품 포장 단위, 예: 1kg 갓김치 100000개)
    totalQty = prompt("1. 전체 기간 동안의 월별 생산량을 입력하세요 (숫자, 예: 100000):", "100000");
    if (!totalQty || isNaN(totalQty) || parseFloat(totalQty) <= 0) return alert("유효한 월별 생산량을 입력해야 합니다."), false;
    totalQty = parseFloat(totalQty);
    
    // 2. 기준 불량률 (%) 추가 입력
    baseDefectRate = prompt("2. 기준 불량률 (KPI 시작점)을 입력하세요 (숫자, 예: 0.6):", "0.6");
    if (!baseDefectRate || isNaN(baseDefectRate) || parseFloat(baseDefectRate) <= 0) return alert("유효한 기준 불량률을 입력해야 합니다."), false;
    baseDefectRate = parseFloat(baseDefectRate);

    // 3. 목표 불량률 (%)
    targetDefectRate = prompt("3. 목표 불량률을 입력하세요 (숫자, 예: 0.5):", "0.5");
    if (!targetDefectRate || isNaN(targetDefectRate) || parseFloat(targetDefectRate) <= 0) return alert("유효한 목표 불량률을 입력해야 합니다."), false;
    targetDefectRate = parseFloat(targetDefectRate);
            
    // 4. 최종 달성 불량률 (%)
    achievedDefectRate = prompt("4. 최종 달성 불량률을 입력하세요 (숫자, 예: 0.45):", "0.45");
    if (!achievedDefectRate || isNaN(achievedDefectRate) || parseFloat(achievedDefectRate) < 0) return alert("유효한 달성 불량률을 입력해야 합니다."), false;
    achievedDefectRate = parseFloat(achievedDefectRate);

    // 5. 시작 월 (YYYY-MM 형식)
    startMonth = prompt("5. 시작 월을 입력하세요 (YYYY-MM, 예: 2024-01):", "2024-01");
    if (!startMonth || !/^\d{4}-\d{2}$/.test(startMonth)) return alert("시작 월은 YYYY-MM 형식이어야 합니다."), false;

    // 6. 종료 월 (YYYY-MM 형식)
    endMonth = prompt("6. 종료 월을 입력하세요 (YYYY-MM, 예: 2024-06):", "2024-06");
    if (!endMonth || !/^\d{4}-\d{2}$/.test(endMonth)) return alert("종료 월은 YYYY-MM 형식이어야 합니다."), false;
            
    return { totalQty, baseDefectRate, targetDefectRate, achievedDefectRate, startMonth, endMonth };
}

// 💡 월별 불량률 데이터를 시뮬레이션하는 함수
function generateMonthlyData(totalQty, achievedDefectRate, monthCount, startMonth) {
    const monthlyData = [];
    const startDate = new Date(startMonth + '-01');

    const initialDefectRate = achievedDefectRate + 0.15; 
    const monthlyReduction = (initialDefectRate - achievedDefectRate) / (monthCount - 1);

    let currentDefectRate = initialDefectRate;

    for (let i = 0; i < monthCount; i++) {
        const date = new Date(startDate);
        date.setMonth(startDate.getMonth() + i);

        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const monthKey = `${year}-${month}`;

        let rate;
        if (i === monthCount - 1) {
            rate = achievedDefectRate;
        } else {
            rate = currentDefectRate;
            currentDefectRate -= monthlyReduction;
            rate = rate + (Math.random() * 0.03 - 0.015); 
        }

        rate = Math.max(0.01, rate);

        const defects = Math.round(totalQty * (rate / 100));

        monthlyData.push({
            month: monthKey,
            defect_rate: rate.toFixed(2),
            defects: defects
        });
    }
    return monthlyData;
}

// 💡 차트 및 요약 통계 업데이트 메인 함수
function calculateAndRender(input) {
    const { totalQty, baseDefectRate, targetDefectRate, achievedDefectRate, startMonth, endMonth } = input;

    const startDate = new Date(startMonth);
    const endDate = new Date(endMonth);
    const monthCount = (endDate.getFullYear() - startDate.getFullYear()) * 12 + (endDate.getMonth() - startDate.getMonth()) + 1;
            
    if (monthCount < 2) return alert("최소 2개월 이상의 기간을 설정해야 합니다."), false;

    const monthlyData = generateMonthlyData(totalQty, achievedDefectRate, monthCount, startMonth);
    const finalData = monthlyData[monthlyData.length - 1];

    const totalDefects = monthlyData.reduce((sum, item) => sum + item.defects, 0);
    const finalDefectRate = parseFloat(finalData.defect_rate); // 최종 월 불량률 (0.45%)

    // 1. 평균 불량률 계산
    const totalRateSum = monthlyData.reduce((sum, item) => sum + parseFloat(item.defect_rate), 0);
    const avgDefectRate = (totalRateSum / monthCount).toFixed(2);
    
    // 2. KPI 달성 현황 계산
    const targetReduction = baseDefectRate - targetDefectRate; 
    const actualReduction = baseDefectRate - finalDefectRate;    
    
    let kpiAchievementRate = 0.0;
    
    if (targetReduction > 0) {
        kpiAchievementRate = ((actualReduction / targetReduction) * 100).toFixed(1);
        kpiAchievementRate = Math.min(parseFloat(kpiAchievementRate), 150); 
    } else if (baseDefectRate <= targetDefectRate) {
        kpiAchievementRate = (finalDefectRate <= targetDefectRate) ? 100.0 : 0.0;
    } else {
        kpiAchievementRate = 0.0;
    }
    
    // 3. KPI 요약 카드 업데이트
    document.getElementById('totalQty').innerText = comma(totalQty * monthCount); // 전체 기간 총 생산량
    
    // 기준 불량률 카드
    document.getElementById('kpiBase').innerText = baseDefectRate.toFixed(2);
    document.getElementById('targetValue').innerText = targetDefectRate.toFixed(2);
    
    // 평균 불량률 카드
    document.getElementById('avgDefectRate').innerText = avgDefectRate;
    
    // KPI 달성 현황 카드
    document.getElementById('kpiAchievementRate').innerText = kpiAchievementRate;
    document.getElementById('actualAvgValue').innerText = actualReduction.toFixed(2); // 불량률 단축 폭

    // 4. 기간 표시 업데이트 (입력받은 값 사용)
    document.getElementById('periodDisplay').innerText = `${startMonth} ~ ${endMonth}`;

    // 5. 차트 데이터 준비 및 생성
    // ⭐ 김치 불량 유형으로 수정
    const defectTypeData = [
        { type: '절임 불량', count: Math.round(totalDefects * 0.30) },
        { type: '이물 혼입', count: Math.round(totalDefects * 0.25) },
        { type: '이취 발생', count: Math.round(totalDefects * 0.20) },
        { type: '중량 오차', count: Math.round(totalDefects * 0.15) },
        { type: '포장 불량', count: totalDefects - (Math.round(totalDefects * 0.30) + Math.round(totalDefects * 0.25) + Math.round(totalDefects * 0.20) + Math.round(totalDefects * 0.15)) }
    ];
            
    createCharts(monthlyData, defectTypeData, targetDefectRate);

    // 6. 상세 테이블 (API 호출)
    getDefectStatDetail({page: 1});

    alert(`데이터가 업데이트되었습니다.\n[기간: ${startMonth} ~ ${endMonth}, 최종 불량률: ${finalDefectRate}%]`);
}

// 💡 차트 생성 함수 (로직 변경 없음)
function createCharts(monthlyData, defectTypeData, targetRate) {
    if (monthlyChartInstance) monthlyChartInstance.destroy();
    if (typeChartInstance) typeChartInstance.destroy();

    // 월별 불량율 추이 차트
    const monthlyCtx = document.getElementById('monthlyDefectChart').getContext('2d');
    monthlyChartInstance = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [
                {
                    label: '불량률 (%)',
                    data: monthlyData.map(item => item.defect_rate),
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                 {
                    label: '목표 불량률 (%)',
                    data: monthlyData.map(() => targetRate),
                    borderColor: '#28a745',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.1)' },
                    ticks: { callback: (value) => value + '%' } 
                },
                x: { grid: { display: false } }
            },
            plugins: {
                tooltip: { callbacks: { label: (c) => `${c.dataset.label}: ${c.parsed.y}%` } }
            }
        }
    });

    // 불량 유형별 분포 차트
    const typeCtx = document.getElementById('defectTypeChart').getContext('2d');
    const hasData = defectTypeData && defectTypeData.length > 0 && defectTypeData.some(item => item.count > 0);
            
    if (hasData) {
        typeChartInstance = new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: defectTypeData.map(item => item.type),
                datasets: [{
                    data: defectTypeData.map(item => item.count),
                    backgroundColor: [ '#dc3545', '#fd7e14', '#ffc107', '#20c997', '#6c757d' ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { callbacks: { label: (c) => `${c.label}: ${comma(c.parsed)}개` } }
                }
            }
        });
    } else {
         typeCtx.canvas.parentNode.innerHTML = '<div style="height:350px; display:flex; justify-content:center; align-items:center; color:#6c757d;">데이터가 없습니다.</div>';
    }
}

// 💡 상세 데이터 테이블 Fetch 함수 (김치 더미 데이터로 수정)
const getDefectStatDetail = async ({
    page,
    per = 5,
    block = 4,
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {
    let where = `where qty > 0`; 

    const formData = new FormData();
    formData.append('controller', CONTROLLER);
    formData.append('mode', MODE);
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('orderby', orderBy);
    formData.append('asc', order);

    try {
        // ⭐ 김치 제조 불량에 특화된 더미 데이터로 수정
        const dummyData = {
            result: 'success',
            data: [
                { item_name: '갓김치', item_code: 'GK001', standard: '1kg', reason: '절임 불량 (짠맛 강)', qty: 12, created_dt: '2025-11-15 10:30' },
                { item_name: '배추김치', item_code: 'BK003', standard: '3kg', reason: '이물 혼입 (비닐조각)', qty: 8, created_dt: '2025-11-14 14:45' },
                { item_name: '열무김치', item_code: 'YM002', standard: '0.5kg', reason: '이취 발생 (군내)', qty: 5, created_dt: '2025-11-13 09:10' },
                { item_name: '갓김치', item_code: 'GK001', standard: '1kg', reason: '중량 오차 (과다)', qty: 3, created_dt: '2025-11-12 11:20' },
                { item_name: '깍두기', item_code: 'KKD01', standard: '1kg', reason: '포장 불량 (씰링 불량)', qty: 2, created_dt: '2025-11-11 16:00' },
                { item_name: '배추김치', item_code: 'BK003', standard: '5kg', reason: '절임 불량 (덜 절임)', qty: 15, created_dt: '2025-11-10 13:05' },
                { item_name: '열무김치', item_code: 'YM002', standard: '1kg', reason: '이물 혼입 (머리카락)', qty: 6, created_dt: '2025-11-09 17:50' },
                { item_name: '갓김치', item_code: 'GK001', standard: '0.5kg', reason: '이취 발생 (쉰내)', qty: 4, created_dt: '2025-11-08 08:35' },
                { item_name: '배추김치', item_code: 'BK003', standard: '1kg', reason: '중량 오차 (미달)', qty: 9, created_dt: '2025-11-07 10:15' },
                { item_name: '깍두기', item_code: 'KKD01', standard: '3kg', reason: '포장 불량 (봉투 파손)', qty: 7, created_dt: '2025-11-06 12:25' }
            ],
            total: 50
        };
                
        const tableBody = document.querySelector('.list tbody');
        tableBody.innerHTML = generateTableContent(dummyData);
    } catch (error) {
        console.error('상세 데이터를 가져오는 중 오류가 발생했습니다:', error);
        document.querySelector('.list tbody').innerHTML = `<tr><td class='center' colspan='6'>데이터를 불러오는 데 실패했습니다.</td></tr>`;
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='6'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${item.reason}</td>
            <td class='center'>${comma(item.qty)}</td>
            <td class='center'>${item.created_dt}</td>
        </tr>
    `).join('');
};

// 💡 애니메이션 효과
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
        // 입력 실패 시 기본값으로 렌더링
        calculateAndRender({
            totalQty: 100000,
            baseDefectRate: 0.6, 
            targetDefectRate: 0.5,
            achievedDefectRate: 0.45,
            startMonth: '2024-01',
            endMonth: '2024-06'
        });
    }
    animateCards();
});
</script>