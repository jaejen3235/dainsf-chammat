<div class='main-container'>
    <div class='content-wrapper'>
        <div class="summary-stats">
            <div class="summary-card">
                <h4>현재 재고비용</h4>
                <div class="number" id="currentCost">0</div>
                <div class="unit">원</div>
            </div>

            <div class="summary-card">
                <h4>전월 대비 증감율</h4>
                <div class="number" id="costChangeRate">0</div>
                <div class="unit">%</div>
            </div>
        </div>

        <div class="charts-grid">
            <!-- 월별 생산량 차트 -->
            <div class="chart-card">
                <h3>📈 월별 재고금액 변동 추이</h3>
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>       
        </div>

        <!-- 상세 데이터 테이블 -->
        <div class="data-table-container">
            <h3 style="margin-bottom: 20px; color: #333;">📋 월별 제고 마감 데이터</h3>
            <table class="list">
                <thead>
                    <tr>
                        <th>년월</th>
                        <th>재고금액</th>
                        <th>변동</th>
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
    formData.append('mode', 'getMonthlyCostData');

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

// 통계 계산 및 표시
function updateSummaryStats() {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getInventoryCostStat');

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
            
            document.getElementById('currentCost').innerHTML = comma(data.current_cost);
            if (data.cost_change_type === 'increase') {
                document.getElementById('costChangeRate').innerHTML = `▲ ${data.cost_change_rate.toFixed(2)}%`;
            } else if (data.cost_change_type === 'decrease') {
                document.getElementById('costChangeRate').innerHTML = `▼ ${data.cost_change_rate.toFixed(2)}%`;
            } else {
                document.getElementById('costChangeRate').innerHTML = `—`;
            }
                
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
                label: '재고금액',
                data: sampleData.monthly.map(item => item.stock_amount),
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
const MODE = 'getInventoryCostList';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getInventoryCostList = async ({
    page,
    per = 5,
    block = 4,
}) => {    
    let where = `where 1=1`;

    const formData = new FormData();
    formData.append('controller', CONTROLLER);
    formData.append('mode', MODE);
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.list tbody');
        tableBody.innerHTML = generateTableContent(data);

        getPaging('mes_stock_close', 'uid', where, page, per, block, 'getInventoryCostList');
    } catch (error) {
        console.error('월별 제고 마감 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map((item, idx, arr) => {
        const prev = idx > 0 ? arr[idx - 1] : null;
        let diff = '';
        let diffDisplay = '';
        if (prev) {
            const delta = (item.close_amount ?? 0) - (prev.close_amount ?? 0);
            if (delta > 0) {
                diff = `▲ ${delta.toLocaleString()}`;
                diffDisplay = `<span style="color: #14833B; font-weight:bold;">${diff}</span>`;
            } else if (delta < 0) {
                diff = `▼ ${Math.abs(delta).toLocaleString()}`;
                diffDisplay = `<span style="color: #D23B3B; font-weight:bold;">${diff}</span>`;
            } else {
                diff = '—';
                diffDisplay = `<span style="color:#999;">${diff}</span>`;
            }
        } else {
            diffDisplay = `<span style="color:#999;">-</span>`;
        }
        return `
            <tr>
                <td class='center'>${item.year}-${item.month}</td>
                <td class='center'>${Number(item.close_amount).toLocaleString()}</td>
                <td class='center'>${diffDisplay}</td>
            </tr>
        `;
    }).join('');
};

// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', async function() {
    await fetchMonthlyData();    
    await getInventoryCostList({page:1});
    updateSummaryStats();
    createCharts();
    animateCards();

    console.log('loginLevel:', localStorage.getItem('loginLevel'));
});
</script>