<div class="main-container">
    <div class="title-wrapper">일별 전력 사용량 정보</div>
    <div class="content-wrapper">
        <!-- 첫 번째 차트: 해당 월의 일별 사용량 -->
        <div class="title-bar">해당월 일별 사용량</div>
        <div class="flex-start-top">
            <canvas id="monthlyDailyChart1" style="width:100%; height: 300px;"></canvas>
        </div>

        <!-- 두 번째 차트: 2024년과 2023년의 일별 사용량 비교 -->
        <div class="title-bar">작년 대비 일별 사용량 비교</div>
        <div class="flex-start-top">
            <canvas id="monthlyDailyChart2" style="width:100%; height: 300px;"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
window.addEventListener('DOMContentLoaded', () => {
    const year = new Date().getFullYear();
    const month = new Date().getMonth() + 1; // 월은 0부터 시작하므로 +1 필요

    fetchMonthlyDailyData(year, month);
    fetchMonthlyComparisonData(year, month);
});

// 차트 초기화
const monthlyCtx1 = document.getElementById('monthlyDailyChart1').getContext('2d');
const monthlyDailyChart1 = new Chart(monthlyCtx1, {
    type: 'line',
    data: {
        labels: [], // 날짜 라벨
        datasets: [
            {
                label: `${new Date().getFullYear()}년 ${new Date().getMonth() + 1}월 일별 전력 사용량`,
                data: [],
                borderColor: '#4CAF50',
                fill: false,
                tension: 0.1
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            x: { title: { display: true, text: '일' } },
            y: { title: { display: true, text: '전력 사용량 (kWh)' }, beginAtZero: true }
        }
    }
});

const monthlyCtx2 = document.getElementById('monthlyDailyChart2').getContext('2d');
const monthlyDailyChart2 = new Chart(monthlyCtx2, {
    type: 'line',
    data: {
        labels: [], // 날짜 라벨
        datasets: [
            { label: `${new Date().getFullYear()}년`, data: [], borderColor: '#4CAF50', fill: false },
            { label: `${new Date().getFullYear() - 1}년`, data: [], borderColor: '#FF6347', fill: false }
        ]
    },
    options: {
        responsive: true,
        scales: {
            x: { title: { display: true, text: '일' } },
            y: { title: { display: true, text: '전력 사용량 (kWh)' }, beginAtZero: true }
        }
    }
});

// 데이터 가져오기 함수
function fetchMonthlyDailyData(year, month) {
    const formData = new FormData();
    formData.append('controller', 'electricity');
    formData.append('mode', 'getMonthlyDailyData');
    formData.append('year', year);
    formData.append('month', month);

    fetch('./handler.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            const labels = data.data.map(item => `${item.day}일`);
            const powerData = data.data.map(item => item.totalPower);

            monthlyDailyChart1.data.labels = labels;
            monthlyDailyChart1.data.datasets[0].data = powerData;
            monthlyDailyChart1.update();
        });
}

function fetchMonthlyComparisonData(year, month) {
    const formData = new FormData();
    formData.append('controller', 'electricity');
    formData.append('mode', 'getMonthlyComparisonData');
    formData.append('year', year);
    formData.append('month', month);

    fetch('./handler.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            const labels = data.data.map(item => `${item.day}일`);
            const currentYearData = data.data.map(item => item.currentYear.totalPower);
            const previousYearData = data.data.map(item => item.previousYear.totalPower);

            monthlyDailyChart2.data.labels = labels;
            monthlyDailyChart2.data.datasets[0].data = currentYearData;
            monthlyDailyChart2.data.datasets[1].data = previousYearData;
            monthlyDailyChart2.update();
        });
}


</script>