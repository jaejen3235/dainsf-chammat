<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<div class='main-container'>
    <div class='content-wrapper'>
        <div class="summary-stats">  
            <div class="summary-card">
                <div class="kpi-title">총 공수 절감액 (Man-Hour)</div>
                <div class="kpi-value" id="totalSavingMH">0.00</div>
            </div>
            <div class="summary-card target-card">
                <div class="kpi-title">평균 공수 절감률 (%)</div>
                <div class="kpi-value" id="avgSavingRate">0.00%</div>
            </div>
            <div class="summary-card">
                <div class="kpi-title">기준 공수 (개선 前 총 MH)</div>
                <div class="kpi-value" id="totalBeforeMH">0.00</div>
            </div>
            <div class="summary-card kpi-card">
                <div class="kpi-title">총 수주 건수 (개선 전)</div>
                <div class="kpi-value" id="totalOrders">0</div>
            </div>
        </div>
        <div style="position: relative; height: 400px;">
            <div class='title red'>월별 공수 절감 현황 (Man-Hour & 절감률)</div>
            <canvas id="kpiChart"></canvas>
        </div>
        <br>
        <div class="mt30">
            <div class='title red'>월별 상세 작업 공수 데이터 셋</div>
            <hr class='hr'>
            <table class="list">
                <thead>
                    <tr>
                        <th>월</th>
                        <th>월별 수주 건수</th>
                        <th>개선 前 공수 (MH)</th>
                        <th>도달 목표 공수 (MH)</th>
                        <th>공수 절감액 (MH)</th>
                        <th>공수 절감률 (%)</th>
                    </tr>
                </thead>
                <tbody id="detailTableBody">
                    </tbody>
            </table>
        </div>
    </div>
</div>
    
<script>
// ==============================================================================
// JAVASCRIPT: 계산 및 데이터 시각화 로직
// ==============================================================================
let kpiChart;

// 초기 설정값을 저장하는 객체 (기본값을 설정해 둡니다)
let currentInputs = {
    currentMH: 40,      
    targetGoalMH: 25,   
    targetReachMH: 30,  
    startMonth: '2025-01',
    endMonth: '2025-06',
    totalOrders: 100
};

const INPUT_LABELS = {
    currentMH: "1. 현재 기준 공수 (MH/건) (예: 40)",
    targetGoalMH: "2. 최종 목표 공수 (MH/건) (예: 25)",
    targetReachMH: "3. 도달할 공수 (실제 개선 후 공수, MH/건) (예: 30)",
    startMonth: "4. 시작 월 (YYYY-MM) (예: 2025-01)",
    endMonth: "5. 종료 월 (YYYY-MM) (예: 2025-06)",
    totalOrders: "6. 전체 수주 건수 (총) (예: 100)"
};

/**
 * 지정된 범위 내에서 랜덤한 정수 또는 실수 생성
 * @param {number} min - 최소값
 * @param {number} max - 최대값
 * @param {boolean} isInteger - 정수 여부
 */
function getRandomValue(min, max, isInteger = true) {
    const val = Math.random() * (max - min) + min;
    return isInteger ? Math.round(val) : parseFloat(val.toFixed(2));
}
        
/**
 * 시작 월부터 12개월 리스트를 YYYY-MM 형식으로 반환
 */
function getMonthsBetween(startDateStr, endDateStr) {
    const start = new Date(startDateStr);
    const months = [];
            
    let current = new Date(start.getFullYear(), start.getMonth(), 1);

    // 12개월 고정
    for (let i = 0; i < 12; i++) {
        const year = current.getFullYear();
        const month = String(current.getMonth() + 1).padStart(2, '0');
        months.push(`${year}-${month}`);
        
        current.setMonth(current.getMonth() + 1);
    }
    return months;
}

/**
 * 페이지 로드 시 실행되어 프롬프트로 입력값을 받아옵니다.
 */
function initializeInputsFromPrompt() {
    alert("대표님, MES KPI 계산을 위해 6가지 설정값을 순서대로 입력해주세요.\n(이번 버전은 '현재/목표/도달 공수'를 입력받습니다)");

    const newInputs = { ...currentInputs }; 
    let inputSucceeded = true; // 입력 성공 여부 플래그

    try {
        // 프롬프트 입력 및 유효성 검사 (취소 시 null 반환)
        const promptCurrentMH = prompt(INPUT_LABELS.currentMH, currentInputs.currentMH);
        if (promptCurrentMH === null) { inputSucceeded = false; } else { newInputs.currentMH = parseFloat(promptCurrentMH) || currentInputs.currentMH; }
                
        const promptTargetGoalMH = prompt(INPUT_LABELS.targetGoalMH, currentInputs.targetGoalMH);
        if (promptTargetGoalMH === null) { inputSucceeded = false; } else { newInputs.targetGoalMH = parseFloat(promptTargetGoalMH) || currentInputs.targetGoalMH; }

        const promptTargetReachMH = prompt(INPUT_LABELS.targetReachMH, currentInputs.targetReachMH);
        if (promptTargetReachMH === null) { inputSucceeded = false; } else { newInputs.targetReachMH = parseFloat(promptTargetReachMH) || currentInputs.targetReachMH; }

        const promptStartMonth = prompt(INPUT_LABELS.startMonth, currentInputs.startMonth);
        if (promptStartMonth === null) { inputSucceeded = false; } else { newInputs.startMonth = promptStartMonth || currentInputs.startMonth; }

        const promptEndMonth = prompt(INPUT_LABELS.endMonth, currentInputs.endMonth);
        if (promptEndMonth === null) { inputSucceeded = false; } else { newInputs.endMonth = promptEndMonth || currentInputs.endMonth; }

        const promptTotalOrders = prompt(INPUT_LABELS.totalOrders, currentInputs.totalOrders);
        if (promptTotalOrders === null) { inputSucceeded = false; } else { newInputs.totalOrders = parseInt(promptTotalOrders) || currentInputs.totalOrders; }

        if (inputSucceeded) {
            // 모든 입력이 성공했을 때만 새로운 값을 반영
            if (newInputs.targetReachMH > newInputs.currentMH) {
                alert("경고: '도달할 공수'가 '현재 공수'보다 높습니다. 값이 올바른지 확인해주세요.");
            }
            currentInputs = newInputs; 
        }                 
    } catch (e) {
        alert("입력 처리 중 오류가 발생하여 기본값으로 계산합니다.");
    }
            
    // 입력 성공/실패와 관계없이 최종적으로 계산을 실행하여 화면에 표시 (데이터가 비는 것을 방지)
    calculateAndRenderKPI();
}
        
/**
 * 메인 계산 함수: 저장된 입력값을 기반으로 월별 KPI 데이터를 계산하고 화면을 업데이트합니다.
 */
function calculateAndRenderKPI() {
    const inputData = currentInputs;
            
    if (!inputData.startMonth || !inputData.endMonth || inputData.totalOrders <= 0) {
        // 유효성 검사 실패 시
        document.getElementById('totalSavingMH').textContent = '0.00';
        document.getElementById('avgSavingRate').textContent = '0.00%';
        document.getElementById('totalBeforeMH').textContent = '0.00';
        document.getElementById('totalOrders').textContent = '0';
        document.getElementById('detailTableBody').innerHTML = '';
        if (kpiChart) kpiChart.destroy();
        return;
    }

    // 1. 입력 공수 값
    const baseMHPerOrder = inputData.currentMH;      // 기준 공수 (MH/건)
    const targetMHPerOrder = inputData.targetReachMH; // 도달 공수 (MH/건)
            
    // 2. 월별 데이터 분배 및 계산 (12개월 고정)
    const months = getMonthsBetween(inputData.startMonth, inputData.endMonth);
    const numMonths = 12; // 12개월 고정
    const avgOrdersPerMonth = Math.round(inputData.totalOrders / numMonths);

    let totalSavingMH = 0;
    let totalBeforeMH = 0;
    let totalActualOrders = 0;

    const monthlyData = months.map((month) => {
        // --- 월별 데이터에 랜덤 변동성 적용 (±5% ~ ±10%) ---
                
        // 수주 건수 변동 (평균 ± 10%)
        const minOrders = Math.max(1, avgOrdersPerMonth * 0.9);
        const maxOrders = avgOrdersPerMonth * 1.1;
        const currentOrders = getRandomValue(minOrders, maxOrders, true);
                
        // 기준 공수(MH/건) 변동 (입력값 ± 5%)
        const minBaseMH = baseMHPerOrder * 0.95;
        const maxBaseMH = baseMHPerOrder * 1.05;
        const actualBaseMHPerOrder = getRandomValue(minBaseMH, maxBaseMH, false);

        // 도달 공수(MH/건) 변동 (입력값 ± 5%)
        const minTargetMH = targetMHPerOrder * 0.95;
        const maxTargetMH = targetMHPerOrder * 1.05;
        const actualTargetMHPerOrder = getRandomValue(minTargetMH, maxTargetMH, false);
                
        // --- 월별 총 공수 계산 ---

        const mhBefore = currentOrders * actualBaseMHPerOrder;
        const mhAfter = currentOrders * actualTargetMHPerOrder;
                
        const savingMH = mhBefore - mhAfter; 
        const savingRate = mhBefore > 0 ? ((savingMH / mhBefore) * 100).toFixed(2) : 0;
                
        totalSavingMH += savingMH;
        totalBeforeMH += mhBefore;
        totalActualOrders += currentOrders;

        return {
            month: month,
            orders: currentOrders,
            mhPerOrderBase: actualBaseMHPerOrder, 
            mhPerOrderTarget: actualTargetMHPerOrder, 
            mhBefore: parseFloat(mhBefore.toFixed(2)),
            mhAfter: parseFloat(mhAfter.toFixed(2)),
            savingMH: parseFloat(savingMH.toFixed(2)),
            savingRate: parseFloat(savingRate)
        };
    });

    // 4. KPI 카드 업데이트
    document.getElementById('totalSavingMH').textContent = totalSavingMH.toFixed(2);
    document.getElementById('totalBeforeMH').textContent = totalBeforeMH.toFixed(2);
    document.getElementById('totalOrders').textContent = totalActualOrders.toLocaleString(); 
            
    let avgSavingRate = 0;
    if (totalBeforeMH > 0) {
        avgSavingRate = ((totalSavingMH / totalBeforeMH) * 100).toFixed(2);
    }
    document.getElementById('avgSavingRate').textContent = avgSavingRate + '%';

    // 5. 차트 업데이트
    updateChart(months, monthlyData.map(d => d.savingMH), monthlyData.map(d => d.savingRate), inputData.targetGoalMH);

    // 6. 상세 테이블 업데이트
    updateDetailTable(monthlyData);
}

/**
 * Chart.js를 사용하여 차트 데이터를 업데이트하거나 새로 생성합니다.
 */
function updateChart(labels, savingMHData, savingRateData, targetGoalMHPerOrder) {
    const ctx = document.getElementById('kpiChart').getContext('2d');
            
    const mhMax = Math.max(...savingMHData, 0) * 1.2;
    const rateMax = Math.max(...savingRateData, 0) * 1.2;
    const rateMin = Math.min(...savingRateData, 0) * 1.2;

    const config = {
        type: 'bar',
        data: {
            labels: labels.map(l => l.slice(5)), 
            datasets: [
                {
                    label: '월별 공수 절감액 (MH)',
                    data: savingMHData,
                    backgroundColor: '#007bff',
                    borderColor: '#007bff',
                    borderWidth: 1,
                    yAxisID: 'yMH',
                },
                {
                    type: 'line',
                    label: '공수 절감률 (%)',
                    data: savingRateData,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'yRate',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                yMH: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: '공수 절감액 (MH)'
                    },
                    beginAtZero: true,
                    max: mhMax,
                },
                yRate: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: '절감률 (%)'
                    },
                    grid: {
                        drawOnChartArea: false, 
                    },
                    suggestedMax: rateMax,
                    suggestedMin: rateMin,
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: `월별 공수 절감 추이 (개선 목표 MH/건: ${targetGoalMHPerOrder})`
                }
            }
        }
    };
            
    if (kpiChart) {
        kpiChart.destroy();
    }
    kpiChart = new Chart(ctx, config);
}

/**
 * 상세 데이터 테이블의 내용을 업데이트합니다.
 */
function updateDetailTable(data) {
    const tbody = document.getElementById('detailTableBody');
    tbody.innerHTML = ''; 

    data.forEach(item => {
        const tr = document.createElement('tr');
        const savingClass = item.savingMH >= 0 ? 'saving-positive' : 'saving-negative';

        tr.innerHTML = `
            <td>${item.month}</td>
            <td>${item.orders.toLocaleString()} 건</td>
            <td>${item.mhBefore.toFixed(2)}</td>
            <td>${item.mhAfter.toFixed(2)}</td>
            <td class="${savingClass}">${item.savingMH.toFixed(2)}</td>
            <td class="${savingClass}">${item.savingRate.toFixed(2)} %</td>
        `;
        tbody.appendChild(tr);
    });
}

// ==============================================================================
// 초기화 및 이벤트 리스너 설정
// ==============================================================================

document.addEventListener('DOMContentLoaded', () => {
    // 페이지 로드 후 즉시 프롬프트 입력 시작
    initializeInputsFromPrompt();
});
</script>