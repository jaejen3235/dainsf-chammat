<div class='main-container'>
    <div class='title-wrapper center'>모니터링</div>
    <div class='content-wrapper'>
        <div class='flex mt10'>
            <div class="chart-box p33">
                <div class="center">생산량 추이 (일주일)</div>
                <canvas id="barChart" width="400" height="400"></canvas>
            </div>
            <div class="chart-box p33">
                <div class="center">불량률 추이</div>
                <canvas id="pieChart" width="400" height="420"></canvas>
            </div>
            <div class='p33 vertical-top' style='height:420px'>
                <div class='title center'>안전재고</div>
                <table class='list mt20'>
                    <thead>
                        <tr>
                            <th>품명</th>
                            <th>현재고수량</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class='title center mt20'>출하현황</div>
                <table class='list mt20'>
                    <thead>
                        <tr>
                            <th>품명</th>
                            <th>출하수량</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class='flex mt10'>
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
                <div class='center'>설비1호</div>
                <div class='flex gap30'>
                    <div>전력사용량 : 1,232kWh</div>
                    <div><canvas id="myChart6" width="200" height="200"></canvas></div>
                </div>
            </div>
        </div>           
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    fetchWeeklyProduction();
    fetchPieChartData();
});


// 최대 값 설정 (예시로 최대 전력량을 10억으로 설정)
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

// 주기적으로 데이터를 받아와서 차트를 업데이트 (예: 2초마다)
setInterval(fetchRealPowerData, 2000);  // 2초마다 실시간 데이터 갱신




// 생산량 추이 막대 그래프
// 막대 그래프를 생성
const barCtx = document.getElementById('barChart').getContext('2d');
const barChart = new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: ['월', '화', '수', '목', '금', '토', '일'],
        datasets: [{
            label: '생산량 (단위: 개)',
            data: [], // 초기 데이터는 빈 배열로 설정
            backgroundColor: '#4CAF50'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            datalabels: {
                color: '#000',
                anchor: 'end',
                align: 'top',
                formatter: (value) => value + '개'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: '생산량' }
            }
        }
    }
});

const fetchWeeklyProduction = () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getWeeklyProductQty');

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
			updateBarChart(data);
		}
	})
	.catch(error => console.log(error));
}

// 차트를 업데이트하는 함수
function updateBarChart(weeklyData) {
    barChart.data.datasets[0].data = weeklyData; // 서버에서 받은 데이터를 차트 데이터로 설정
    barChart.update(); // 차트를 새로고침하여 변경된 데이터가 반영되도록 업데이트
}







// 불량률 추이 원형 그래프
function fetchPieChartData() {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getPieChartData');

    fetch('./handler.php', {
        method: 'post',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        console.log('Fetched Data:', data); // 데이터 확인
        const newLabels = data.labels; // 불량 사유 라벨
        const newData = data.data;     // 불량 사유 데이터

        const newColors = ['#FF6384', '#36A2EB', '#FFCE56', '#AA65FF'];
        while (newColors.length < newData.length) {
            newColors.push('#E0E0E0'); // 추가 색상
        }

        // 차트 라벨 및 데이터 업데이트
        pieChart.data.labels = newLabels;
        pieChart.data.datasets[0].data = newData;
        pieChart.data.datasets[0].backgroundColor = newColors;

        // 차트 업데이트
        pieChart.update();
    })
    .catch(error => {
        console.error('Error fetching pie chart data:', error);
    });
}

const pieCtx = document.getElementById('pieChart').getContext('2d');
const pieChart = new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels: [], // 초기 라벨
        datasets: [{
            data: [], // 초기 데이터
            backgroundColor: [] // 초기 색상
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            datalabels: {
                color: '#000',
                font: { size: 14, weight: 'bold' },
                anchor: 'center',
                align: 'center',
                formatter: (value, context) => {
                    const dataset = context.chart.data.datasets[0].data;
                    const total = dataset.reduce((acc, val) => acc + val, 0);
                    const percentage = Math.round((value / total) * 100);
                    return percentage + '%';
                }
            }
        }
    },
    plugins: [ChartDataLabels]
});

</script>