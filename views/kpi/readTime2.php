<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* 합쳐진 카드를 위한 세부 스타일 */
.combined-metrics { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 10px; }
.metric-group { display: flex; flex-direction: column; }
.metric-group .label { font-size: 14px; color: #b8daff; margin-bottom: 4px; }
        
.summary-card .number { font-size: 36px; font-weight: 700; color: white; display: inline-block; }
.summary-card.target-card .number { color: #fff; }

.summary-card .unit { display: inline-block; margin-left: 8px; font-size: 18px; color: white; }
        
/* 차트 그리드 스타일 */
.charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 30px; margin-bottom: 40px; }
.chart-card { background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); }
.chart-card h3 { font-size: 20px; color: #333; margin-bottom: 15px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
.chart-container { height: 350px; }
</style>

<div class='main-container'>
    <div class='content-wrapper'>
        <div class="summary-stats">                
            <div class="summary-card combined-card">
                <h4 id="itemSpecTitle">총 작업 현황 (0.48ton)</h4>
                <div class="combined-metrics">
                    <div class="metric-group">
                        <span class="label">총 생산량</span>
                        <div class="number" id="totalQuantity">0</div>
                        <div class="unit">개</div>
                    </div>
                    <div class="metric-group" style="text-align: right;">
                        <span class="label">총 작업시간</span>
                        <div class="number" id="totalRunningTime" style="font-size: 28px;">0.0</div>
                        <div class="unit" style="font-size: 14px;">시간</div>
                    </div>
                </div>
            </div>

            <div class="summary-card target-card">
                <h4>목표 제조 리드 타임</h4>
                <div class="number" id="kpiTarget">0.00</div>
                <div class="unit">시간</div>
                <hr style="border-color: rgba(255,255,255,0.3); margin: 15px 0 10px 0;">
                <div style="font-size: 14px; color: white; margin-top: 5px;">
                    (기준 리드 타임: <strong id="kpiBase">0.00</strong> 시간)
                </div>
            </div>
                            
            <div class="summary-card avg-card">
                <h4>평균 제조 리드 타임 (도달값)</h4>
                <div class="number" id="avgLeadTime">0.00</div>
                <div class="unit">시간</div>
            </div>
                            
            <div class="summary-card kpi-card">
                <h4 id="kpiTitle">KPI 달성 현황 (목표: 0.00시간)</h4>
                <div class="number" id="kpiAchievementRate">0.0</div>
                <div class="unit">%</div>
                <div style="font-size: 14px; color: white; margin-top: 5px;">
                    평균 리드 타임: <strong id="actualAvgValue">0.00</strong> 시간
                </div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div class='title red'>월별 생산량 추이</div>
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class='title red'>일별 생산량 (최근 30일)</div>
                <div class="chart-container">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
        </div>

        <div class="data-table-container">
            <div class='title red'>상세 생산 데이터</div>
            <hr class='hr'>
            <table class="list">
                <thead>
                    <tr>
                        <th>생산일</th>
                        <th>품명</th>
                        <th>품번</th>
                        <th>규격</th>
                        <th style="text-align: right;">생산수량</th>
                        <th style="text-align: right;">개당 제조리드타임 (시간)</th>
                        <th>관리</th>
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
        
const ITEM_NAME = 'Sling Wire Rope';
const ITEM_SPEC = '0.48ton';
const ITEM_CODES = ['SWR-1A', 'SWR-1B', 'SWR-1C'];
const DATA_COUNT = 30; // 최근 30일 데이터 생성
const READ_TIME_UNIT = '시간';

// 💡 조건부 포맷팅 함수: 소수점 이하가 0이면 정수만, 아니면 2자리 표시
function formatTimeValue(value) {
    const num = parseFloat(value);
    if (Number.isInteger(num)) {
        return num.toFixed(0);
    } else {
        return num.toFixed(2);
    }
}

// 💡 사용자에게 기준값, 목표값, 도달값을 입력받는 함수
function getInput() {
    let kpiBase, kpiTarget, avgLeadTime;

    // 1. 기준 리드 타임 (현재값) 입력 (예: 32)
    kpiBase = prompt(`1. 기준 제조 리드 타임(현재값)을 입력하세요 (${READ_TIME_UNIT}, 예: 32.0):`, "32.0");
    if (!kpiBase || isNaN(kpiBase) || parseFloat(kpiBase) <= 0) return alert("유효한 기준 시간을 입력해야 합니다."), false;
    kpiBase = parseFloat(kpiBase);

    // 2. 목표 리드 타임 입력 (예: 28)
    kpiTarget = prompt(`2. 목표 제조 리드 타임를 입력하세요 (${READ_TIME_UNIT}, 예: 28.0):`, "28.0");
    if (!kpiTarget || isNaN(kpiTarget) || parseFloat(kpiTarget) <= 0 || parseFloat(kpiTarget) >= kpiBase) return alert("유효한 목표 시간을 입력해야 하며, 기준 시간보다 짧아야 합니다."), false;
    kpiTarget = parseFloat(kpiTarget);

    // 3. 실제 도달값 입력 (평균 리드 타임, 예: 27.88)
    avgLeadTime = prompt(`3. 실제 도달값(평균 리드 타임)을 입력하세요 (${READ_TIME_UNIT}, 예: 27.88):`, "27.88");
    if (!avgLeadTime || isNaN(avgLeadTime) || parseFloat(avgLeadTime) <= 0) return alert("유효한 평균 리드 타임 시간을 입력해야 합니다."), false;
    avgLeadTime = parseFloat(avgLeadTime);
            
    return { kpiBase, kpiTarget, avgLeadTime };
}

/**
 * KPI 계산 및 화면 렌더링을 수행하는 메인 함수
 */
function calculateAndRender(input) {
    const { kpiBase, kpiTarget, avgLeadTime } = input;
            
    // 1. KPI 계산
    const targetImprovement = kpiBase - kpiTarget; // 목표 개선 폭
    const actualImprovement = kpiBase - avgLeadTime; // 실제 개선 폭
    let kpiAchievementRate = 0;
            
    if (targetImprovement > 0) {
        // 달성률: (실제 개선 폭 / 목표 개선 폭) * 100
        kpiAchievementRate = ((actualImprovement / targetImprovement) * 100).toFixed(1);
    }
            
    // 2. 더미 데이터 생성 (차트 및 테이블용)
    const productionData = [];
    const monthlySummary = {};
    let totalQuantity = 0;
    let totalRunningTimeSeconds = 0;
    const today = new Date();

    for (let i = 0; i < DATA_COUNT; i++) {
        const date = new Date(today);
        date.setDate(today.getDate() - i);
        const dateString = date.toISOString().split('T')[0];
        const monthKey = dateString.substring(0, 7);
                
        const dailyItemCount = Math.floor(Math.random() * 3) + 1; // 하루 1~3건
                
        for (let j = 0; j < dailyItemCount; j++) {
            const code = ITEM_CODES[Math.floor(Math.random() * ITEM_CODES.length)];
            const quantity = Math.floor(Math.random() * 3) + 1; // 1~3개 생산
                    
            // 도달값(avgLeadTime) 주변 0.5시간 내에서 랜덤하게 데이터 생성
            const minRange = avgLeadTime - 0.5;
            const maxRange = avgLeadTime + 0.5;
            const leadTimeHoursRaw = (Math.random() * (maxRange - minRange) + minRange);
            const leadTimeHours = parseFloat(leadTimeHoursRaw.toFixed(2));
                    
            // 작업 시간 (초): 생산 수량 * 개당 리드 타임(초)
            const runningTimeSec = Math.round(quantity * leadTimeHours * 3600);

            productionData.push({
                date: dateString,
                name: ITEM_NAME,
                code: code,
                spec: ITEM_SPEC,
                quantity: quantity,
                lead_time_hours: leadTimeHours,
                running_time_sec: runningTimeSec,
            });

            // 총계 및 월별 요약 누적
            totalQuantity += quantity;
            totalRunningTimeSeconds += runningTimeSec;

            if (!monthlySummary[monthKey]) {
                monthlySummary[monthKey] = 0;
            }
            monthlySummary[monthKey] += quantity;
        }
    }

    const totalRunningTimeHours = (totalRunningTimeSeconds / 3600).toFixed(1);

    // 3. KPI 요약 카드 업데이트
    document.getElementById('itemSpecTitle').innerText = `총 작업 현황 (${ITEM_SPEC})`;
    document.getElementById('totalQuantity').innerText = totalQuantity.toLocaleString();
    document.getElementById('totalRunningTime').innerText = totalRunningTimeHours;

    document.getElementById('kpiTarget').innerText = formatTimeValue(kpiTarget);
    document.getElementById('kpiBase').innerText = formatTimeValue(kpiBase);
    document.getElementById('kpiTitle').innerText = `KPI 달성 현황 (목표: ${formatTimeValue(kpiTarget)}${READ_TIME_UNIT})`;
            
    document.getElementById('avgLeadTime').innerText = formatTimeValue(avgLeadTime);
    document.getElementById('actualAvgValue').innerText = formatTimeValue(avgLeadTime);
    document.getElementById('kpiAchievementRate').innerText = kpiAchievementRate;
            

    // 4. 테이블 업데이트
    renderTable(productionData);

    // 5. 차트 업데이트
    renderMonthlyChart(monthlySummary);
    renderDailyChart(productionData);

    alert(`데이터가 업데이트되었습니다.\n\n[기준: ${formatTimeValue(kpiBase)}${READ_TIME_UNIT}, 목표: ${formatTimeValue(kpiTarget)}${READ_TIME_UNIT}, 도달값: ${formatTimeValue(avgLeadTime)}${READ_TIME_UNIT}]`);
}

// =======================================================
// 테이블 렌더링 함수
// =======================================================
function renderTable(data) {
    const tbody = document.getElementById('dataTableBody');
    tbody.innerHTML = ''; // 기존 내용 삭제
            
    // 최신 데이터가 위에 오도록 역순으로 정렬
    const reversedData = [...data].reverse();

    reversedData.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${row.date}</td>
            <td>${row.name}</td>
            <td>${row.code}</td>
            <td>${row.spec}</td>
            <td style="text-align: right;">${row.quantity.toLocaleString()}</td>
            <td style="text-align: right;">${formatTimeValue(row.lead_time_hours)}</td>
            <td><button class='btn-small success'>상세보기</button></td>
        `;
        tbody.appendChild(tr);
    });
}
        
// =======================================================
// 차트 렌더링 함수
// =======================================================

let monthlyChartInstance = null;
let dailyChartInstance = null;

/**
 * Chart.js를 사용하여 월별 생산량 차트 렌더링
 */
function renderMonthlyChart(monthlySummary) {
    const ctx = document.getElementById('monthlyChart').getContext('2d');
            
    if (monthlyChartInstance) {
        monthlyChartInstance.destroy();
    }

    // 월별 요약 데이터를 키를 기준으로 정렬
    const sortedLabels = Object.keys(monthlySummary).sort();
    const data = sortedLabels.map(label => monthlySummary[label]);

    monthlyChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: sortedLabels,
            datasets: [{
                label: '월별 총 생산량 (개)',
                data: data,
                backgroundColor: 'rgba(0, 123, 255, 0.7)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, 
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: '생산량 (개)' }
                }
            },
            plugins: { legend: { display: true } }
        }
    });
}

/**
 * Chart.js를 사용하여 일별 생산량 차트 렌더링
 */
function renderDailyChart(productionData) {
    const ctx = document.getElementById('dailyChart').getContext('2d');
            
    if (dailyChartInstance) {
        dailyChartInstance.destroy();
    }

    // 상세 데이터(tableData)에서 일별 총 생산량을 계산
    const dailyDataMap = productionData.reduce((acc, row) => {
        acc[row.date] = (acc[row.date] || 0) + row.quantity;
        return acc;
    }, {});

    // 일자 순으로 정렬 및 레이블/데이터 배열 생성
    const sortedDates = Object.keys(dailyDataMap).sort();
    const dailyLabels = sortedDates;
    const dailyQuantities = sortedDates.map(date => dailyDataMap[date]);

    dailyChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                label: '일별 총 생산량 (개)',
                data: dailyQuantities,
                backgroundColor: 'rgba(40, 167, 69, 0.4)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                pointRadius: 3,
                tension: 0.3, 
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: '생산량 (개)' }
                },
                x: {
                    ticks: { autoSkip: true, maxTicksLimit: 10 }
                }
            },
            plugins: { legend: { display: true } }
        }
    });
}

// 페이지 로드 시 입력 프롬프트 실행
document.addEventListener('DOMContentLoaded', () => {
    const inputValues = getInput();
    if (inputValues) {
        calculateAndRender(inputValues);
    } else {
        // 입력이 취소되거나 유효하지 않은 경우, 기본값으로 렌더링
        calculateAndRender({ kpiBase: 32.0, kpiTarget: 28.0, avgLeadTime: 27.88 });
    }
});
</script>