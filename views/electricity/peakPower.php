<div class="main-container">
    <div class="title-wrapper">전력 사용량 정보</div>
    <div id="machines-wrapper">
        <canvas id="myChart" aria-label="chart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 최초 데이터 로드
    getLatestData();
});

// 차트 데이터 초기화
const MAX_DATA_POINTS = 20; // 최대 표시할 데이터 수
var chartData = {
    labels: [], // X축 라벨
    datasets: [
        {
            label: '설비1호기 (전력)',
            data: [],
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            tension: 0.4,
            spanGaps: true
        },
        {
            label: '설비2호기 (전력)',
            data: [],
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 2,
            tension: 0.4,
            spanGaps: true
        },
        {
            label: '설비3호기 (전력)',
            data: [],
            borderColor: 'rgba(255, 159, 64, 1)',
            borderWidth: 2,
            tension: 0.4,
            spanGaps: true
        },
        {
            label: '설비4호기 (전력)',
            data: [],
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2,
            tension: 0.4,
            spanGaps: true
        },
        {
            label: '설비5호기 (전력)',
            data: [],
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            tension: 0.4,
            spanGaps: true
        },
        {
            label: '설비6호기 (전력)',
            data: [],
            borderColor: 'rgba(255, 206, 86, 1)',
            borderWidth: 2,
            tension: 0.4,
            spanGaps: true
        },
        {
            label: '설비7호기 (전력)',
            data: [],
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            tension: 0.4,
            spanGaps: true
        }
    ]
};

// 차트 옵션 설정
var chartOptions = {
    scales: {
        x: {
            display: true,
            ticks: {
                maxTicksLimit: 10 // x축에 최대 10개의 라벨 표시
            }
        },
        y: {
            beginAtZero: true,
            suggestedMin: 0,
            suggestedMax: 100
        }
    },
    plugins: {
        legend: {
            display: true
        },
        annotation: {
            annotations: {
                line1: {
                    type: 'line',
                    yMin: 70, // 기준선의 Y축 시작값
                    yMax: 70, // 기준선의 Y축 끝값 (같은 값으로 선 생성)
                    borderColor: 'black', // 기준선 색상 (검정색)
                    borderWidth: 2, // 기준선 두께
                    borderDash: [5, 5], // 점선 설정 ([길이, 간격])
                    label: {
                        content: '기준선: 70', // 선 옆에 표시할 텍스트
                        enabled: true,
                        position: 'end'
                    }
                }
            }
        }
    }
};

// 차트 초기화
var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'line',
    data: chartData,
    options: chartOptions
});

// 데이터셋 업데이트 함수
function updateChartData(dataArray) {
    // 현재 시간을 라벨로 추가
    const timestamp = new Date().toLocaleTimeString(); // 현재 시간을 라벨로 사용
    chartData.labels.push(timestamp);

    // 데이터셋 업데이트
    dataArray.forEach((dataValue, index) => {
        const numericValue = parseFloat(dataValue);
        if (!isNaN(numericValue)) {
            chartData.datasets[index].data.push(numericValue);

            // 데이터셋이 최대 길이를 초과하면 오래된 데이터 제거
            if (chartData.datasets[index].data.length > MAX_DATA_POINTS) {
                chartData.datasets[index].data.shift();
            }
        } else {
            console.warn(`Invalid numeric value for dataset index ${index}:`, dataValue);
        }
    });

    // x축 라벨이 데이터 포인트 수와 동기화되도록 설정
    if (chartData.labels.length > MAX_DATA_POINTS) {
        chartData.labels.shift();
    }

    // 차트 업데이트
    myChart.update();
}

// 서버로부터 최신 데이터 가져오기
function getLatestData() {
    const formData = new FormData();
    formData.append('controller', 'electricity');
    formData.append('mode', 'getPeakPowerData');
    formData.append('code', 'M001,M002,M003,M004,M005,M006,M007');

    fetch('./handler.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        console.log(data); // 서버 응답 확인

        // 데이터가 7개의 설비에 해당하는 배열인지 확인
        if (data && data.length === chartData.datasets.length) {
            updateChartData(data); // 데이터 동기화
        } else {
            console.warn('Invalid data format received:', data);
        }
    })
    .catch(error => {
        console.error("Error fetching data:", error);
    });
}

// 5초마다 데이터 갱신
setInterval(() => {
    getLatestData();
}, 5000);
</script>

<style>
/* Canvas 크기 조정 */
#myChart {
    width: 100%;
}
</style>
