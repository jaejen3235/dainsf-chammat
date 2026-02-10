<div class='main-container'>
    <div class='content-wrapper'>
        <div class="summary-stats">
            <div class="summary-card">
                <h4>총 생산량</h4>
                <div class="number" id="totalQuantity">0</div>
                <div class="unit">개</div>
            </div>

            <div class="summary-card">
                <h4>작업시간</h4>
                <div class="number" id="planRunningTime">0</div>
                <div class="unit">시간</div>
            </div>
                    
            <div class="summary-card">
                <h4>시간당 평균 생산량</h4>
                <div class="number" id="avgQuantity">0</div>
                <div class="unit">개</div>
            </div>
                    
            <div class="summary-card">
                <h4>1개당 제조 리드 타임</h4>
                <div class="number" id="readTime">0</div>
                <div class="unit">초</div>
            </div>
        </div>

        <div class="charts-grid">
            <!-- 월별 생산량 차트 -->
            <div class="chart-card">
                <h3>📈 월별 생산량 추이</h3>
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <!-- 제품별 생산량 차트 -->
           <!--<div class="chart-card">
                <h3>🍕 제품별 생산량</h3>
                <div class="chart-container">
                    <canvas id="productChart"></canvas>
                </div>
            </div>-->

            <!-- 일별 생산량 차트 -->
            <div class="chart-card">
                <h3>📅 일별 생산량 (최근 30일)</h3>
                <div class="chart-container">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>            
        </div>

        <!-- 상세 데이터 테이블 -->
        <div class="data-table-container">
            <h3 style="margin-bottom: 20px; color: #333;">📋 상세 생산 데이터</h3>
            <table class="list">
                <thead>
                    <tr>
                        <th>생산일</th>
                        <th>품명</th>
                        <th>품번</th>
                        <th>규격</th>
                        <th>생산수량</th>
                        <th>개당 제조리드타임</th>                        
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="paging-area mt20"></div>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// 샘플 데이터
// sampleData를 초기화하고, monthly 데이터는 fetch로 mes.php에서 받아와서 monthly 배열에 저장한다.
const sampleData = {
    monthly: [],
    daily: []
};

// monthly 데이터 fetch 함수 (mes.php로부터 받아와서 sampleData.monthly에 넣음)
async function fetchMonthlyData() {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getMonthlyData');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const data = await response.json();
        // data.monthly가 정상적으로 넘어온다고 가정 (예: [{ month: '2024-01', total_quantity: ..., total_orders: ... }, ...])
        if (data && data.result === 'success' && Array.isArray(data.monthly)) {
            sampleData.monthly = data.monthly;
        } else {
            // 실패 케이스 처리
            sampleData.monthly = [];
        }
    } catch (error) {
        console.error('fetchMonthlyData error:', error);
        sampleData.monthly = [];
    }
}

// 일별 데이터 fetch 함수 (mes.php로부터 받아와서 sampleData.daily에 넣음)
async function fetchDailyData(year, month) {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDailyData');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const data = await response.json();
        // data.daily가 정상적으로 넘어온다고 가정 (예: [{ date: '2024-06-01', daily_quantity: ..., daily_orders: ... }, ...])
        if (data && data.result === 'success' && Array.isArray(data.daily)) {
            sampleData.daily = data.daily;
        } else {
            // 실패 케이스 처리
            sampleData.daily = [];
        }
    } catch (error) {
        console.error('fetchDailyData error:', error);
        sampleData.daily = [];
    }
}


// 통계 계산 및 표시
function updateSummaryStats() {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getProductStat');

    fetch('./handler.php', {
        method: 'post',
        body : formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(function(data) {
        if (data && data.result === 'success') {
            
            // 💡 [가정] 백엔드에서 필요한 값들이 넘어온다고 가정
            const totalQuantity = Number(data.total_quantity) || 0;
            const dailyPlannedHours = Number(data.daily_planned_hours) || 3; // 하루 3시간 가정
            const workingDays = Number(data.working_days) || 22;           // 22일 근무 가정
            
            let avgQuantity = 0;
            
            // 1. 월간 총 계획 가동 시간(Hr) 계산 (3시간/일 * 22일 = 66시간)
            const totalPlannedHours = dailyPlannedHours * workingDays;
            
            if (totalPlannedHours > 0) {
                // 2. 시간당 평균 생산량 재계산: 총 생산량 / 월간 총 계획 가동 시간
                avgQuantity = totalQuantity / dailyPlannedHours;
            }
            // ----------------------------------------------------------------------
            
            document.getElementById('totalQuantity').innerHTML = totalQuantity;
            // planRunningTime은 "계획 가동시간" 카드에 사용 (단위: 시간 * 일)
            // data.plan_running_time은 하루 계획 가동시간, 22는 근무일수(고정)
            document.getElementById('planRunningTime').innerHTML = `${data.plan_running_time ?? 0}`;
            
            // 💡 [수정됨]: 재계산된 값을 사용하고 소수점 2자리까지 표시
            document.getElementById('avgQuantity').innerHTML = Math.round(data.avg_quantity);
            document.getElementById('readTime').innerHTML = Math.round(data.lead_time);
            
                
        } else if (data && data.message) {
            console.log(data.message);
        }
    })
    .catch(error => console.log(error));    
}

// 차트 생성
function createCharts() {
    // 월별 생산량 차트
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: sampleData.monthly.map(item => item.month),
            datasets: [{
                label: '생산량',
                data: sampleData.monthly.map(item => item.total_quantity),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // 일별 생산량 차트
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'bar',
        data: {
            labels: sampleData.daily.map(item => item.date),
            datasets: [{
                label: '일별 생산량',
                data: sampleData.daily.map(item => item.daily_quantity),
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: '#667eea',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// 애니메이션 효과
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

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getWorkReportList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getWorkReportList = async ({
    page,
    per = 5,
    block = 4,
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {    
    let where = `where 1=1`;

    const formData = new FormData();
    formData.append('controller', CONTROLLER);
    formData.append('mode', MODE);
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('orderby', orderBy);
    formData.append('asc', order);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.list tbody');
        tableBody.innerHTML = generateTableContent(data);

        getPaging('mes_daily_work', 'uid', where, page, per, block, 'getWorkReportList');
    } catch (error) {
        console.error('생산실적 상세 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.map(item => `
        <tr>
            <td class='center'>${item.work_date}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${item.work_qty}</td>
            <td class='center'>${item.work_qty}</td>
            <td class='center'>     
                ${localStorage.getItem('loginLevel') === '100' ? `<button class='btn-small danger hands' onclick='deleteWorkReport(${item.uid})'>삭제</button>` : ''}
            </td>
        </tr>
    `).join('');
};

const formatSecondsToHMS = (seconds) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const remainingSeconds = seconds % 60;
    return `${hours}시간 ${minutes}분 ${remainingSeconds}초`;
};

async function deleteProductStatDetail(uid) {

    if(!confirm('정말 삭제하시겠습니까?')) {
        return;
    }

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'deleteProductStatDetail');
    formData.append('uid', uid);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const data = await response.json();
        // data.monthly가 정상적으로 넘어온다고 가정 (예: [{ month: '2024-01', total_quantity: ..., total_orders: ... }, ...])
        if (data.result === 'success') {
            await fetchMonthlyData();
            await fetchDailyData();
            await getProductStatDetail({page:1});
            await updateSummaryStats();
            await createCharts();
            animateCards();
        }        
    } catch (error) {
        console.error('deleteProductStatDetail error:', error);
    }
}

// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', async function() {
    await fetchMonthlyData();
    await fetchDailyData();
    await getWorkReportList({page:1});
    updateSummaryStats();
    createCharts();
    animateCards();

    console.log('loginLevel:', localStorage.getItem('loginLevel'));
});
</script>