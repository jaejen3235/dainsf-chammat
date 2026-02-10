<div class='main-container'>
    <div class='title-wrapper'>시간대별 전력 사용량 정보</div>
        <div class='content-wrapper'>
            <div>
                <div class='title-bar'>월별 사용량 추이</div>
                <div class='flex-start-top'>
                    <canvas id="hourlyChart1" style='width:100%; height: 300px'></canvas>
                </div>
            </div>

            <div class='mt30'>
                <div class='title-bar'>년도별 사용량 추이</div>
                <div class='flex-start-top'>
                    <canvas id="hourlyChart2" style='width:100%; height: 300px'></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
window.addEventListener('DOMContentLoaded', ()=>{
    fetchHourlyData();
    fetchHourlyComparisonData();
});

// 차트 초기화
const hourlyCtx1 = document.getElementById('hourlyChart1').getContext('2d');
const hourlyChart1 = new Chart(hourlyCtx1, {
    type: 'line', // 선 그래프
    data: {
        labels: [], // 시간대 라벨 (예: 00:00, 01:00, ...)
        datasets: [
            {
                label: `${new Date().getFullYear()}년 전력 사용량`, // 2024년도
                data: [],
                borderColor: '#4CAF50', // 2024년 전력 사용량 선 색상
                fill: false, // 선 그래프는 채우지 않음
                tension: 0.1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `${context.dataset.label}: ${context.raw.toLocaleString()} kWh`;
                    }
                }
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: '시간대'
                }
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: '전력 사용량 (kWh)'
                },
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            }
        }
    }
});

// 서버에서 시간대별 전력 사용량 데이터를 가져오는 함수
const fetchHourlyData = () => {
    const formData = new FormData();
    formData.append('controller', 'electricity');
    formData.append('mode', 'getHourlyData'); // PHP에서 작성한 함수 호출
    
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
        if (data && data.data) {
            updateHourlyChart(data.data); // 시간대별 차트 업데이트
        }
    })
    .catch(error => console.error('Error fetching data:', error));
};

// 차트를 업데이트하는 함수
function updateHourlyChart(hourlyData) {
    // 시간대 라벨과 전력 사용량 데이터 준비
    const labels = hourlyData.map(item => item.hour); // 예: 00:00, 01:00, ...
    const currentYearData = hourlyData.map(item => item.currentYear.totalPower); // 각 시간대별 전력 사용량

    // 차트 데이터 업데이트
    hourlyChart1.data.labels = labels; // 시간대 라벨 설정
    hourlyChart1.data.datasets[0].data = currentYearData; // 2024년 데이터 업데이트

    hourlyChart1.update(); // 차트 업데이트
}







// 차트 초기화
// 차트 초기화
const hourlyCtx2 = document.getElementById('hourlyChart2').getContext('2d');
const hourlyChart2 = new Chart(hourlyCtx2, {
    type: 'line', // 시간대별은 보통 선 그래프(Line chart)로 시각화
    data: {
        labels: [], // 시간대 라벨 (예: 00:00, 01:00, ...)
        datasets: [
            {
                label: `${new Date().getFullYear()}년 전력 사용량`,
                data: [],
                borderColor: '#4CAF50', // 2024년 전력 사용량 선 색상
                fill: false, // 선 그래프는 채우지 않음
                tension: 0.1
            },
            {
                label: `${new Date().getFullYear() - 1}년 전력 사용량`, // 2023년 데이터셋 추가
                data: [],
                borderColor: '#FF6347', // 2023년 전력 사용량 선 색상
                fill: false, // 선 그래프는 채우지 않음
                tension: 0.1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `${context.dataset.label}: ${context.raw.toLocaleString()} kWh`;
                    }
                }
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: '시간대'
                }
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: '전력 사용량 (kWh)'
                },
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            }
        }
    }
});

// 서버에서 시간대별 전력 사용량 데이터를 가져오는 함수
const fetchHourlyComparisonData = () => {
    const formData = new FormData();
    formData.append('controller', 'electricity');
    formData.append('mode', 'getHourlyComparisonData'); // 새로운 API 모드 추가
    
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
        if (data && data.data) {
            updateHourlyComparisonChart(data.data); // 시간대별 차트 업데이트
        }
    })
    .catch(error => console.error('Error fetching data:', error));
};

// 차트를 업데이트하는 함수
function updateHourlyComparisonChart(hourlyData) {
    // 시간대 라벨과 전력 사용량 데이터 준비
    const labels = hourlyData.map(item => item.hour); // 예: 00:00, 01:00, ...
    const currentYearData = hourlyData.map(item => item.currentYear.totalPower); // 각 시간대별 전력 사용량 (2024)
    const previousYearData = hourlyData.map(item => item.previousYear.totalPower); // 각 시간대별 전력 사용량 (2023)

    // 차트 데이터 업데이트
    hourlyChart2.data.labels = labels;

    // 2024년 데이터셋 업데이트
    hourlyChart2.data.datasets[0].label = `${new Date().getFullYear()}년 전력 사용량`; // 2024년
    hourlyChart2.data.datasets[0].data = currentYearData;

    // 2023년 데이터셋 업데이트
    hourlyChart2.data.datasets[1].label = `${new Date().getFullYear() - 1}년 전력 사용량`; // 2023년
    hourlyChart2.data.datasets[1].data = previousYearData;

    hourlyChart2.update();
}
</script>