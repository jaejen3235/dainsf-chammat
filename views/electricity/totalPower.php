<div class='main-container'>
    <div class='title-wrapper'>종합 전력 사용량 정보</div>
        <div class='content-wrapper'>
            <div>
                <div class='title-bar'>월별 사용량 추이</div>
                <div class='flex-start-top'>
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
                    <canvas id="fiveYearsMonthlyChart" style="width:100%; height: 300px;"></canvas>
                </div>                            
            </div>

            <div class='mt30'>
                <div class='title-bar'>설비별 사용량</div>
                <div class='flex'>
                    <div class='chart-box p33'>
                        <div class='center'>설비1호</div>
                        <div class='flex gap30'>
                            <div>전력사용량 : 1,232kWh</div>
                            <div><canvas id="myChart0" width="200" height="200"></canvas></div>
                        </div>
                    </div>
                    <div class='chart-box p33'>
                        <div class='center title'>설비2호</div>
                        <div class='flex gap30'>
                            <div>전력사용량 : 1,540kWh</div>
                            <div><canvas id="myChart1" width="200" height="200"></canvas></div>
                        </div>
                    </div>
                    <div class='chart-box p33'>
                        <div class='center'>설비3호</div>
                        <div class='flex gap30'>
                            <div>전력사용량 : 1,870kWh</div>
                            <div><canvas id="myChart2" width="200" height="200"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class='flex mt10'>
                    <div class='chart-box p33'>
                        <div class='center'>설비1호</div>
                        <div class='flex gap30'>
                            <div>전력사용량 : 1,232kWh</div>
                            <div><canvas id="myChart3" width="200" height="200"></canvas></div>
                        </div>
                    </div>
                    <div class='chart-box p33'>
                        <div class='center title'>설비2호</div>
                        <div class='flex gap30'>
                            <div>전력사용량 : 1,540kWh</div>
                            <div><canvas id="myChart4" width="200" height="200"></canvas></div>
                        </div>
                    </div>
                    <div class='chart-box p33'>
                        <div class='center'>설비3호</div>
                        <div class='flex gap30'>
                            <div>전력사용량 : 1,870kWh</div>
                            <div><canvas id="myChart5" width="200" height="200"></canvas></div>
                        </div>
                    </div>
                </div> 
                <div class='flex mt10'>
                    <div class='chart-box p33'>
                        <div class='center'>설비7호</div>
                        <div class='flex gap30'>
                            <div>전력사용량 : 1,232kWh</div>
                            <div><canvas id="myChart6" width="200" height="200"></canvas></div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    const year = new Date().getFullYear();
    getCurrentYearMonthlyData(year);
    getFiveYearsSumData();

    setInterval(fetchRealPowerData, 3000);
});

// 차트 초기화
const ctx = document.getElementById('fiveYearsMonthlyChart').getContext('2d');
const fiveYearsMonthlyChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: Array.from({ length: 12 }, (_, i) => `${i + 1}월`), // 1월~12월 라벨
        datasets: [] // 데이터셋 추가 예정
    },
    options: {
        responsive: true,
        scales: {
            x: { title: { display: true, text: '월' } },
            y: { 
                title: { display: true, text: '전력 사용량 (kWh)' },
                beginAtZero: true
            }
        },
        plugins: {
            tooltip: { callbacks: {
                label: (context) => `${context.raw} kWh`
            }},
            legend: { position: 'top' }
        }
    }
});

// 데이터 가져오기 함수
function getCurrentYearMonthlyData(year) {
    const formData = new FormData();
    formData.append('controller', 'electricity');
    formData.append('mode', 'getCurrentYearMonthlyData');
    formData.append('year', year);

    fetch('./handler.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (!data || !data.monthlyData) {
                console.error('데이터가 없습니다.');
                return;
            }

            const monthlyData = Array.from({ length: 12 }, (_, month) => data.monthlyData[month + 1] || 0);

            // 차트 데이터셋 업데이트
            fiveYearsMonthlyChart.data.datasets = [
                {
                    label: `${data.year}년`,
                    data: monthlyData,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true
                }
            ];

            fiveYearsMonthlyChart.update();
        })
        .catch(error => console.error('데이터 로드 에러:', error));
}


const getFiveYearsSumData = () => {
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





// 최대 값 설정
// 최대 값 설정
const maxPower = 100; // 최대 전력량 설정 (예: 1억)

// 각 차트의 데이터를 저장
const chartData = {
    myChart0: 0,
    myChart1: 0,
    myChart2: 0,
    myChart3: 0,
    myChart4: 0,
    myChart5: 0,
    myChart6: 0
};

// 각 차트 인스턴스를 생성
const charts = {};
for (let i = 0; i <= 6; i++) {
    charts[`myChart${i}`] = createChart(`myChart${i}`);
}

// Chart.js 인스턴스를 생성하는 함수
function createChart(chartId) {
    const ctx = document.getElementById(chartId).getContext('2d');
    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                label: 'Real-Time Data',
                data: [0, 100], // 초기 데이터
                backgroundColor: ['#36A2EB', '#E0E0E0'], // 기본 색상: 파란색
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            rotation: -90,
            circumference: 180,
            animation: { animateRotate: true, animateScale: true },
            plugins: { tooltip: { enabled: false } }
        }
    });
}

// 차트를 업데이트하는 함수
function updateChart(chart, realPower) {
    if (!chart) return; // 차트가 생성되지 않은 경우 무시
    const percentage = Math.min((realPower / maxPower) * 100, 100); // 전력량을 100% 기준으로 변환
    const remaining = 100 - percentage;

    // 색상 변경 조건 (70% 이상은 빨간색, 이하 파란색)
    const newColor = percentage > 70 ? '#FF6384' : '#36A2EB';

    // 데이터 및 색상 업데이트
    chart.data.datasets[0].data = [percentage, remaining];
    chart.data.datasets[0].backgroundColor = [newColor, '#E0E0E0'];
    chart.update(); // 차트 업데이트
}

// 실시간 데이터를 받아와 차트를 업데이트
function fetchRealPowerData() {
    const formData = new FormData();
    formData.append('controller', 'electricity');
    formData.append('mode', 'getRealPowerData');

    fetch('./handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        console.log(data); // 응답 데이터를 확인하기 위해 콘솔에 출력
        Object.keys(chartData).forEach(key => {
            if (data[key] !== undefined) {
                chartData[key] = data[key]; // 데이터 저장
                updateChart(charts[key], data[key]); // 차트 업데이트
            }
        });
    })
    .catch(error => {
        console.error('Error fetching real-time data:', error);
    });
}



</script>