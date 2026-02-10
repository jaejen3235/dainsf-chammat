<div class='main-container'>
    <div class='content-wrapper'>
        <div>
            <div class="kpi-summary">
                <div class="kpi-card">
                    <h3>총 검사 건수</h3>
                    <p id="totalInspections" class="kpi-value">로딩 중...</p>
                </div>
                <div class="kpi-card">
                    <h3>종합 합격률 (OK Rate)</h3>
                    <p id="overallOkRate" class="kpi-value">로딩 중...</p>
                </div>
                <div class="kpi-card">
                    <h3>최다 불합격 품목</h3>
                    <p id="mostNgItem" class="kpi-value">로딩 중...</p>
                </div>
            </div>
        </div>

        <div>
            <div class="chart-section">
                <div class="chart-box">
                    <h2>📊 종합 검사 결과 비중</h2>
                    <canvas id="overallResultChart"></canvas>
                </div>
                <div class="chart-box">
                    <h2>📉 기간별 불합격 추이</h2>
                    <canvas id="monthlyNgTrendChart"></canvas>
                </div>
            </div>
        </div>

        <div>
            <div class="table-section">
                <div class="flex">
                    <div class="title red">📋 상세 검사 내역</div>                
                    <div class="filter-controls">
                        <label for="filterResult">검사결과 필터:</label>
                        <select id="filterResult">
                            <option value="">전체</option>
                            <option value="OK">합격 (OK)</option>
                            <option value="NG">불합격 (NG)</option>
                        </select>
                    </div>
                </div>
                <table class='list'id="inspectionDetailTable">
                    <thead>
                        <tr>
                            <th>검사일자</th>
                            <th>품목명</th>
                            <th>품번</th>
                            <th>입고수량</th>
                            <th>외관</th>
                            <th>기능</th>
                            <th>최종결과</th>
                            <th>검사자</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. PHP에서 JSON 데이터를 받아 처리하는 함수
    async function fetchAndRenderData() {
        try {
            // 실제 PHP 엔드포인트로 변경하세요.
            const formData = new FormData();
            formData.append('controller', 'mes');
            formData.append('mode', 'getImportInspection');

            const response = await fetch('./handler.php', {
                method: 'POST',
                body: formData
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            
            // 데이터 처리 및 렌더링
            renderKpi(data.kpi);
            renderCharts(data.overall_results, data.monthly_ng_trend);
            renderDetailTable(data.detail_data);
            
            // 필터링 이벤트 설정
            document.getElementById('filterResult').addEventListener('change', (e) => {
                renderDetailTable(data.detail_data, e.target.value);
            });

        } catch (error) {
            console.error("수입검사 데이터를 불러오는 중 오류 발생:", error);
            // 오류 발생 시 사용자에게 표시
            document.getElementById('totalInspections').textContent = "ERR";
        }
    }

    // 2. KPI 값 렌더링
    function renderKpi(kpiData) {
        document.getElementById('totalInspections').textContent = kpiData.totalInspections.toLocaleString() + ' 건';
        document.getElementById('overallOkRate').textContent = kpiData.overallOkRate.toFixed(1) + ' %';
        document.getElementById('mostNgItem').textContent = kpiData.mostNgItem || '데이터 없음';
    }

    // 3. 차트 렌더링
    function renderCharts(overallData, trendData) {
        // 3-1. 종합 검사 결과 비중 (도넛 차트)
        const overallLabels = overallData.map(d => d.inspection_result + (d.inspection_result === 'OK' ? ' (합격)' : ' (불합격)'));
        const overallCounts = overallData.map(d => d.count);
        new Chart(document.getElementById('overallResultChart'), {
            type: 'doughnut',
            data: {
                labels: overallLabels,
                datasets: [{
                    data: overallCounts,
                    backgroundColor: ['#2ecc71', '#e74c3c'], // OK: 녹색, NG: 빨간색
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: false }
                }
            }
        });

        // 3-2. 기간별 불합격 추이 (바 차트)
        const trendLabels = trendData.map(d => d.month);
        const trendNgCounts = trendData.map(d => d.ng_count);
        new Chart(document.getElementById('monthlyNgTrendChart'), {
            type: 'bar',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: '월별 불합격 (NG) 건수',
                    data: trendNgCounts,
                    backgroundColor: '#e74c3c', // 빨간색
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true, title: { display: true, text: '불합격 건수' } } },
                plugins: { legend: { display: false } }
            }
        });
    }

    // 페이지 로드 시 시작
    fetchAndRenderData();
    getIncomingInspectionList({page:1});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getIncomingInspectionList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getIncomingInspectionList = async ({
    page,
    per = 10,
    block = 4,
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {    
    let where = `where 1=1`;

    // 검색어가 있다면
    try {
        const searchText = document.getElementById('searchText');
        if(searchText) {
            if(searchText.value != '') {
                where += ` and (name like '%${searchText.value}%' or code like '%${searchText.value}%')`;
            }
        }
    } catch(e) {}
    

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

        getPaging('mes_incoming_inspection', 'uid', where, page, per, block, 'getIncomingInspectionList');
    } catch (error) {
        console.error('품목 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.inspection_date}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${comma(item.in_qty)}</td>
            <td class='center'>${item.appearance_check}</td>
            <td class='center'>${item.function_check}</td>
            <td class='center'>${item.inspection_result}</td>
            <td class='center'>${item.inspector_name}</td>
        </tr>
    `).join('');
};
</script>    