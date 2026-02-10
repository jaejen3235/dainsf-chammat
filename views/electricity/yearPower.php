<div class="main-container">
    <div class="title-wrapper">연도별 전력 사용량 정보</div>
    <div class="content-wrapper">
        <!-- 첫 번째 차트: 해당 월의 일별 사용량 -->
        <div class="title-bar">연도별 사용량</div>
        <div class="flex-start-top">
            <div class='p33'>
                <table class='no-border'>
                    <tr>
                        <td>당해년도 사용량</td>
                        <td><span id='currentYearTotal'></span>Kwh</td>
                    </tr>
                    <tr>
                        <td>전년도 사용량</td>
                        <td><span id='lastYearTotal'></span>Kwh</td>
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
            <table class='list'>
                <thead>
                    <tr>
                        <th>년도</th>
                        <th>1월</th>
                        <th>2월</th>
                        <th>3월</th>
                        <th>4월</th>
                        <th>5월</th>
                        <th>6월</th>
                        <th>7월</th>
                        <th>8월</th>
                        <th>9월</th>
                        <th>10월</th>
                        <th>11월</th>
                        <th>12월</th>
                        <th>합계</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
window.addEventListener('DOMContentLoaded', async () => {
    const year = new Date().getFullYear();
    fetchFiveYearsMonthlyData(year);
    getFiveYearsSumData();
    getMonthlyPowerData();
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
function fetchFiveYearsMonthlyData(year) {
    const formData = new FormData();
    formData.append('controller', 'electricity');
    formData.append('mode', 'getFiveYearsMonthlyData');
    formData.append('year', year);

    fetch('./handler.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            const datasets = [];
            const colors = ['rgba(54, 162, 235, 0.5)', 'rgba(255, 99, 132, 0.5)', 'rgba(75, 192, 192, 0.5)', 'rgba(153, 102, 255, 0.5)', 'rgba(255, 159, 64, 0.5)'];
            const borderColors = ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'];

            Object.keys(data).forEach((year, index) => {
                const monthlyData = Array.from({ length: 12 }, (_, month) => data[year][month + 1] || 0);

                datasets.push({
                    label: `${year}년`,
                    data: monthlyData,
                    backgroundColor: colors[index % colors.length],
                    borderColor: borderColors[index % borderColors.length],
                    borderWidth: 2,
                    fill: true
                });
            });

            fiveYearsMonthlyChart.data.labels = Array.from({ length: 12 }, (_, i) => `${i + 1}월`);
            fiveYearsMonthlyChart.data.datasets = datasets;
            fiveYearsMonthlyChart.update();
        });
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
            document.getElementById('lastYearTotal').innerText = comma(data.lastYearTotal);
            document.getElementById('monthlyAverage').innerText = comma(data.monthlyAverage);
		}
	})
	.catch(error => console.log(error));
}


const getMonthlyPowerData = async () => {    
    

    const formData = new FormData();
    formData.append('controller', 'electricity');
    formData.append('mode', 'getMonthlyPowerData');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.list tbody');
        tableBody.innerHTML = generateTableContent(data);
    } catch (error) {
        console.error('거래처 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.map(item => `
        <tr>
            <td class='center'>${item.year}</td>
            <td class='center'>${comma(item.power1)}<br>(${comma(item.price1)}원)</td>
            <td class='center'>${comma(item.power2)}<br>(${comma(item.price2)}원)</td>
            <td class='center'>${comma(item.power3)}<br>(${comma(item.price3)}원)</td>
            <td class='center'>${comma(item.power4)}<br>(${comma(item.price4)}원)</td>
            <td class='center'>${comma(item.power5)}<br>(${comma(item.price5)}원)</td>
            <td class='center'>${comma(item.power6)}<br>(${comma(item.price6)}원)</td>
            <td class='center'>${comma(item.power7)}<br>(${comma(item.price7)}원)</td>
            <td class='center'>${comma(item.power8)}<br>(${comma(item.price8)}원)</td>
            <td class='center'>${comma(item.power9)}<br>(${comma(item.price9)}원)</td>
            <td class='center'>${comma(item.power10)}<br>(${comma(item.price10)}원)</td>
            <td class='center'>${comma(item.power11)}<br>(${comma(item.price11)}원)</td>
            <td class='center'>${comma(item.power12)}<br>(${comma(item.price12)}원)</td>
            <td class='center'>${comma(item.totalPower)}<br>(${comma(item.totalPrice)}원)</td>
        </tr>
    `).join('');
};
</script>