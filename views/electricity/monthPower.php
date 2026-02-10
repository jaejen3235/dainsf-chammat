<div class="main-container">
    <div class="title-wrapper">월별 전력 사용량 정보</div>
    <div class="content-wrapper">
        <!-- 첫 번째 차트: 해당 월의 일별 사용량 -->
        <div class="title-bar">당해년도 월별 사용량</div>
        <div class="flex-start-top">
            <div class='p33'>
                <table class='no-border'>
                    <tr>
                        <td>당해년도 사용량</td>
                        <td><span id='currentYearTotal'></span>Kwh</td>
                    </tr>
                    <tr>
                        <td>월평균 사용량</td>
                        <td><span id='monthlyAverage'></span>Kwh</td>
                    </tr>
                </table>
            </div>
            <div class="p65">
                <canvas id="yearlyMonthlyChart1" style="width:100%; height: 300px;"></canvas>
            </div>
        </div>

        <!-- 두 번째 차트: 2024년과 2023년의 일별 사용량 비교 -->
        <div class="title-bar">작년 대비 월별 사용량 비교</div>
        <div class="flex-start-top">
            <canvas id="yearlyMonthlyChart2" style="width:100%; height: 300px;"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
window.addEventListener('DOMContentLoaded', () => {
    const year = new Date().getFullYear();

    fetchYearlyMonthlyData(year);
    fetchYearlyComparisonData(year);
    getFiveYearsSumData();
});

// 차트 초기화
const yearlyCtx1 = document.getElementById('yearlyMonthlyChart1').getContext('2d');
const yearlyMonthlyChart1 = new Chart(yearlyCtx1, {
    type: 'bar',
    data: {
        labels: [], // 월별 라벨
        datasets: [
            {
                label: `${new Date().getFullYear()}년 월별 전력 사용량`,
                data: [],
                backgroundColor: '#4CAF50',
                borderColor: '#388E3C',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            x: { title: { display: true, text: '월' } },
            y: { title: { display: true, text: '전력 사용량 (kWh)' }, beginAtZero: true }
        }
    }
});

const yearlyCtx2 = document.getElementById('yearlyMonthlyChart2').getContext('2d');
const yearlyMonthlyChart2 = new Chart(yearlyCtx2, {
    type: 'line',
    data: {
        labels: [], // 월별 라벨
        datasets: [
            { label: `${new Date().getFullYear()}년`, data: [], borderColor: '#4CAF50', fill: false },
            { label: `${new Date().getFullYear() - 1}년`, data: [], borderColor: '#FF6347', fill: false }
        ]
    },
    options: {
        responsive: true,
        scales: {
            x: { title: { display: true, text: '월' } },
            y: { title: { display: true, text: '전력 사용량 (kWh)' }, beginAtZero: true }
        }
    }
});

// 데이터 가져오기 함수
function fetchYearlyMonthlyData(year) {
    const formData = new FormData();
    formData.append('controller', 'electricity');
    formData.append('mode', 'getMonthlyPowerData');

    fetch('./handler.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            // 올해의 데이터 필터링
            const currentYearData = data.find(item => item.year == year);

            if (!currentYearData) {
                console.error('올해의 데이터가 없습니다.');
                return;
            }

            // 월별 데이터 추출
            const labels = Array.from({ length: 12 }, (_, i) => `${i + 1}월`);
            const powerData = [
                currentYearData.power1,
                currentYearData.power2,
                currentYearData.power3,
                currentYearData.power4,
                currentYearData.power5,
                currentYearData.power6,
                currentYearData.power7,
                currentYearData.power8,
                currentYearData.power9,
                currentYearData.power10,
                currentYearData.power11,
                currentYearData.power12
            ];

            // 차트 데이터 업데이트
            yearlyMonthlyChart1.data.labels = labels;
            yearlyMonthlyChart1.data.datasets[0].data = powerData;
            yearlyMonthlyChart1.update();
        })
        .catch(error => console.error('데이터 로드 에러:', error));
}


function fetchYearlyComparisonData(year) {
    const formData = new FormData();
    formData.append('controller', 'electricity');
    formData.append('mode', 'getYearlyMonthlyComparisonData');
    formData.append('year', year);

    fetch('./handler.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            // 월별 라벨 생성
            const labels = data.data.map(item => `${item.month}월`);

            // 올해와 작년 데이터 분리
            const currentYearData = data.data.map(item => item.currentYear.totalPower);
            const previousYearData = data.data.map(item => item.previousYear.totalPower);

            // 차트 데이터 업데이트
            yearlyMonthlyChart2.data.labels = labels;
            yearlyMonthlyChart2.data.datasets[0].data = currentYearData;
            yearlyMonthlyChart2.data.datasets[1].data = previousYearData;
            yearlyMonthlyChart2.update();
        })
        .catch(error => console.error('데이터 로드 에러:', error));
}


const getYearsSumData = () => {
    const formData = new FormData();
    formData.append('controller', 'electricity');
    formData.append('mode', 'getFiveYearsSumData');

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
		if(data != null || data != '') {
            document.getElementById('currentYearTotal').innerText = comma(data.currentYearTotal);
            document.getElementById('monthlyAverage').innerText = comma(data.monthlyAverage);
		}
	})
	.catch(error => console.log(error));
}
</script>