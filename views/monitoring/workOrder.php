<div class='main-container'>
    <div class='content-wrapper'>
        <div>
            <div class="kpi-summary">
                <div class="kpi-card">
                    <h3>총 생산 수량</h3>
                    <p id="totalQty" class="kpi-value">로딩 중...</p>
                </div>
                <div class="kpi-card">
                    <h3>평균 품질 합격률</h3>
                    <p id="avgQuality" class="kpi-value">로딩 중...</p>
                </div>
                <div class="kpi-card">
                    <h3>최다 생산 품목</h3>
                    <p id="topItem" class="kpi-value">로딩 중...</p>
                </div>
            </div>
        </div>

        <div>
            <div class="chart-section">
                <div class="chart-box">
                    <h2>📈 일별 생산 추이</h2>
                    <canvas id="dailyWorkChart"></canvas>
                </div>
                <div class="chart-box">
                    <h2>📊 품목별 생산 비중</h2>
                    <canvas id="itemRatioChart"></canvas>
                </div>
            </div>
        </div>

        <div>
            <div class="table-section">
                <div class="flex">
                    <div class="title red">실적 상세 내역</div>
                    <div class="filter-controls">
                        <label for="filterWorker">작업자 필터:</label>
                        <select id="filterWorker">
                            <option value="">전체</option>
                        </select>
                    </div>
                </div>
                <table class="list">
                    <thead>
                        <tr>
                            <th>작업일자</th>
                            <th>품목명</th>
                            <th>작업자</th>
                            <th>총생산수량</th>
                            <th>품질상태</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. PHP에서 JSON 데이터를 받아 처리하는 핵심 함수
    async function fetchAndRenderData() {
        try {
            // 실제 PHP 엔드포인트로 변경하세요.
            const formData = new FormData();
            formData.append('controller', 'mes');
            formData.append('mode', 'getReportProduct');

            const response = await fetch('./handler.php', {
                method: 'POST',
                body: formData
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            
            // 데이터 처리 및 렌더링 시작
            renderKpi(data.kpi);
            renderCharts(data.daily_data, data.item_ratio_data);
            renderDetailTable(data.detail_data);
            populateWorkerFilter(data.detail_data);

        } catch (error) {
            console.error("데이터를 불러오는 중 오류 발생:", error);
            // 사용자에게 오류 메시지 표시
            document.getElementById('totalQty').textContent = "오류 발생";
        }
    }

    // 2. KPI 값 렌더링
    function renderKpi(kpiData) {
        document.getElementById('totalQty').textContent = kpiData.totalQty.toLocaleString() + ' 개';
        document.getElementById('avgQuality').textContent = kpiData.avgQualityRate.toFixed(1) + ' %';
        document.getElementById('topItem').textContent = kpiData.topItem;
    }

    // 3. 차트 렌더링
    function renderCharts(dailyData, itemRatioData) {
        // 일별 생산 추이 (라인 차트)
        const dailyLabels = dailyData.map(d => d.work_date);
        const dailyValues = dailyData.map(d => d.total_qty);
        new Chart(document.getElementById('dailyWorkChart'), {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: '총 생산 수량',
                    data: dailyValues,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            }
        });

        // 품목별 생산 비중 (도넛 차트)
        const itemLabels = itemRatioData.map(d => d.item_name);
        const itemValues = itemRatioData.map(d => d.qty);
        new Chart(document.getElementById('itemRatioChart'), {
            type: 'doughnut',
            data: {
                labels: itemLabels,
                datasets: [{
                    label: '생산 수량',
                    data: itemValues,
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1' 
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // 4. 상세 테이블 렌더링
    let allDetailData = []; // 필터링을 위해 원본 데이터를 저장
    function renderDetailTable(detailData, filterWorker = "") {
        allDetailData = detailData; // 최초 로드 시 원본 저장
        const tbody = document.querySelector('.list tbody');
        tbody.innerHTML = ''; // 테이블 초기화

        const filteredData = detailData.filter(d => 
            filterWorker === "" || d.worker === filterWorker
        );

        filteredData.forEach(item => {
            const row = tbody.insertRow();
            row.insertCell().textContent = item.work_date;
            row.insertCell().textContent = item.item_name;
            row.insertCell().textContent = item.worker;
            row.insertCell().textContent = item.work_qty.toLocaleString();
            row.insertCell().textContent = item.quality_status;
            // 품질 상태에 따른 색상 표시 (예시)
            if (item.quality_status !== '합격') {
                row.cells[4].style.color = 'red';
                row.cells[4].style.fontWeight = 'bold';
            }
        });
    }

    // 5. 작업자 필터 드롭다운 채우기
    function populateWorkerFilter(detailData) {
        const select = document.getElementById('filterWorker');
        const workers = [...new Set(detailData.map(d => d.worker))];
        
        workers.forEach(worker => {
            const option = document.createElement('option');
            option.value = worker;
            option.textContent = worker;
            select.appendChild(option);
        });

        // 필터 변경 시 테이블 업데이트 이벤트 리스너 추가
        select.addEventListener('change', (e) => {
            renderDetailTable(allDetailData, e.target.value);
        });
    }

    // 페이지 로드 시 데이터 가져오기 및 렌더링 시작
    fetchAndRenderData();
});
</script>