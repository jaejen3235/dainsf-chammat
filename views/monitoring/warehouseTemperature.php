<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>창고 온도 실시간 모니터링</title>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        /* ======================================= */
        /* Custom CSS (스타일 유지) */
        /* ======================================= */
        :root {
            --primary-color: #007bff;     
            --background: #f8f9fa;      
            --card-bg: white;
            --main-font: #343a40;
            --status-normal: #28a745;     
            --status-warn: #ffc107;       
            --status-alert: #dc3545;      
            --chart-bg: #ffffff; 
            --table-border: #dee2e6;
        }

        html, body {
            height: 100%;
        }

        .main-container {
            height: 100vh;
            box-sizing: border-box;
        }

        .main-container,
        .content-wrapper {
            min-height: 100%;
        }

        .content-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 0;
        }

        #temperature-monitor {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        /* Title */
        .report-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        /* 1. Dashboard Cards */
        .temp-dashboard {
            display: grid;
            grid-template-columns: repeat(3, 1fr); 
            gap: 20px;
            margin-bottom: 30px;
        }

        .warehouse-card {
            background: #e9f7ff;
            padding: 25px;
            border-radius: 8px;
            border-left: 5px solid var(--primary-color);
            transition: all 0.3s ease;
            cursor: pointer;
            min-height: 150px; 
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* 활성화된 카드 스타일 (선택된 창고 차트 표시) */
        /* 모든 창고를 표시하더라도, 활성화된 카드 스타일은 현재 주시하는 창고를 시각적으로 강조합니다. */
        .warehouse-card.is-active {
            border: 2px solid var(--primary-color); 
            border-left: 5px solid var(--primary-color);
            background-color: #f0f8ff;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2); /* 선택 효과 강조 */
        }
        
        /* 상태별 카드 색상 */
        .warehouse-card.status-warn { border-color: var(--status-warn); background-color: #fffde7; }
        .warehouse-card.status-alert { border-color: var(--status-alert); background-color: #f8d7da; }
        
        .temp-display { font-size: 48px; font-weight: 700; }
        .temp-display small { font-size: 20px; font-weight: 400; }
        .temp-display.normal { color: var(--status-normal); }
        .temp-display.warn { color: var(--status-warn); }
        .temp-display.alert { color: var(--status-alert); }

        .warehouse-card h3,
        .warehouse-card .temp-display,
        .warehouse-card .details {
            text-align: center;
        }

        .details p { margin: 4px 0; font-size: 14px; color: #6c757d; }
        
        /* 2. Temperature Chart Area */
        .chart-container {
            margin-top: 30px;
            margin-bottom: 30px;
            padding: 20px;
            background-color: var(--chart-bg);
            border: 1px solid var(--table-border);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        
        #chartTitle {
            font-size: 18px;
            font-weight: 600;
            color: var(--main-font);
            margin-bottom: 15px;
        }

        .chart-container canvas {
            flex: 1;
            width: 100% !important;
            height: 100% !important;
        }

        .view-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
        }
        .view-tab-btn {
            border: 1px solid #cfd8dc;
            background: #fff;
            color: #333;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .view-tab-btn.active {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .view-pane {
            display: none;
            flex: 1;
            min-height: 0;
        }
        .view-pane.active {
            display: flex;
            flex-direction: column;
        }
        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            gap: 10px;
            flex-wrap: wrap;
        }
        .table-controls .left,
        .table-controls .right {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .table-controls input[type="date"] {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 6px 8px;
            font-size: 13px;
        }
        .btn-small {
            border: 1px solid #007bff;
            background: #007bff;
            color: #fff;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 13px;
            cursor: pointer;
        }
        .temp-history-table-wrap {
            border: 1px solid var(--table-border);
            border-radius: 8px;
            overflow-y: auto;
            overflow-x: hidden;
            max-height: 310px; /* 헤더 + 약 6행 표시 후 스크롤 */
            background: #fff;
        }
        .temp-history-table {
            width: 100%;
            border-collapse: collapse;
        }
        .temp-history-table th,
        .temp-history-table td {
            border-bottom: 1px solid #eee;
            padding: 10px 8px;
            text-align: center;
            font-size: 13px;
        }
        .temp-history-table thead th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .history-pagination {
            margin-top: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .history-pagination button {
            border: 1px solid #cfd8dc;
            background: #fff;
            color: #333;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 12px;
            cursor: pointer;
        }
        .history-pagination button.active {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
    </style>
</head>
<body>

    <div class='main-container'>
        <div class='content-wrapper'>
            
            <div id="temperature-monitor">
                <div class="report-title">🌡️ 창고 온도 실시간 모니터링</div>

                <div class="temp-dashboard" id="warehouse-summary">
                </div>

                <div class="view-tabs">
                    <button type="button" class="view-tab-btn active" data-tab="chart">실시간 차트</button>
                    <button type="button" class="view-tab-btn" data-tab="table">시간별 조회</button>
                </div>

                <div class="view-pane active" id="chart-pane">
                    <div class="chart-container">
                        <div id="chartTitle"></div>
                        <canvas id="tempChart"></canvas>
                    </div>
                </div>

                <div class="view-pane" id="table-pane">
                    <div class="table-controls">
                        <div class="left">
                            <label for="history_start_date">시작일</label>
                            <input type="date" id="history_start_date">
                            <label for="history_end_date">종료일</label>
                            <input type="date" id="history_end_date">
                            <label for="history_order">정렬</label>
                            <select id="history_order">
                                <option value="desc" selected>내림차순</option>
                                <option value="asc">오름차순</option>
                            </select>
                            <button type="button" class="btn-small" id="btnSearchHistory">조회</button>
                        </div>
                        <div class="right">
                            <span id="historyCountText">총 0건</span>
                        </div>
                    </div>

                    <div class="temp-history-table-wrap">
                        <table class="temp-history-table">
                            <thead>
                                <tr>
                                    <th>측정시간(10분 단위)</th>
                                    <th>냉장창고 A (제품)</th>
                                    <th>냉장창고 B (혼합)</th>
                                    <th>냉장창고 C (원자재)</th>
                                </tr>
                            </thead>
                            <tbody id="hourly-history-body"></tbody>
                        </table>
                    </div>
                    <div class="history-pagination" id="hourly-history-pagination"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ===============================================
        // Global State & Constants
        // ===============================================
        let myChart = null; 
        let currentChartMachine = ''; // 현재 차트에 표시 중인 창고 코드
        const MAX_DATA_POINTS = 120; // 10분 데이터 유지 (5초 * 120회)
        const REFRESH_INTERVAL_MS = 5000; // 5초
        
        // 실시간 데이터 누적을 위한 캐시
        const liveDataCache = { 
            'frig_goods': [],
            'frig_mix': [],
            'frig_stuff': [],
        };
        
        const TEMP_MAX_NORMAL = 5; 
        const TEMP_MAX_ALERT = 7; 

        // DB machine_id와 표시 이름을 매핑
        const FRIG_WAREHOUSES = [
            { code: 'frig_goods', name: '냉장창고 A (제품)', color: '#007bff' }, 
            { code: 'frig_mix', name: '냉장창고 B (혼합)', color: '#28a745' }, 
            { code: 'frig_stuff', name: '냉장창고 C (원자재)', color: '#ffc107' }, 
        ];

        const HISTORY_PER_PAGE = 20;
        let historyPage = 1;

        function getTodayStr() {
            const d = new Date();
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }

        function initHistoryFilters() {
            const startInput = document.getElementById('history_start_date');
            const endInput = document.getElementById('history_end_date');
            const today = getTodayStr();
            if (startInput) startInput.value = today;
            if (endInput) endInput.value = today;
        }

        function initTabs() {
            const tabButtons = document.querySelectorAll('.view-tab-btn');
            const chartPane = document.getElementById('chart-pane');
            const tablePane = document.getElementById('table-pane');

            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    tabButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    const tab = btn.dataset.tab;
                    if (tab === 'chart') {
                        chartPane?.classList.add('active');
                        tablePane?.classList.remove('active');
                    } else {
                        tablePane?.classList.add('active');
                        chartPane?.classList.remove('active');
                        loadHourlyHistory(1);
                    }
                });
            });
        }

        function bindHistoryEvents() {
            const btn = document.getElementById('btnSearchHistory');
            if (btn) {
                btn.addEventListener('click', () => {
                    loadHourlyHistory(1);
                });
            }
        }

        async function loadHourlyHistory(page = 1) {
            historyPage = page;
            const startDate = document.getElementById('history_start_date')?.value || getTodayStr();
            const endDate = document.getElementById('history_end_date')?.value || getTodayStr();
            const order = document.getElementById('history_order')?.value || 'desc';

            const response = await fetchData('getWarehouseTempHourlyHistory', {
                start_date: startDate,
                end_date: endDate,
                order,
                page: historyPage,
                per: HISTORY_PER_PAGE
            });

            renderHistoryTable(response);
            renderHistoryPagination(response.total_count || 0, historyPage, HISTORY_PER_PAGE);
        }

        function renderHistoryTable(response) {
            const tbody = document.getElementById('hourly-history-body');
            const countText = document.getElementById('historyCountText');
            if (!tbody) return;

            const rows = Array.isArray(response?.data) ? response.data : [];
            if (countText) {
                countText.textContent = `총 ${Number(response?.total_count || 0).toLocaleString()}건`;
            }

            if (rows.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4">조회된 데이터가 없습니다.</td></tr>`;
                return;
            }

            tbody.innerHTML = rows.map(row => `
                <tr>
                    <td>${escapeHtml(row.measure_slot || '-')}</td>
                    <td>${formatTemp(row.frig_goods_temp)}</td>
                    <td>${formatTemp(row.frig_mix_temp)}</td>
                    <td>${formatTemp(row.frig_stuff_temp)}</td>
                </tr>
            `).join('');
        }

        function renderHistoryPagination(totalCount, currentPage, per) {
            const wrap = document.getElementById('hourly-history-pagination');
            if (!wrap) return;

            const totalPages = Math.max(1, Math.ceil(totalCount / per));
            if (totalPages <= 1) {
                wrap.innerHTML = '';
                return;
            }

            const pages = [];
            const start = Math.max(1, currentPage - 2);
            const end = Math.min(totalPages, currentPage + 2);

            pages.push(`<button type="button" data-page="${Math.max(1, currentPage - 1)}">이전</button>`);
            for (let p = start; p <= end; p++) {
                pages.push(`<button type="button" data-page="${p}" class="${p === currentPage ? 'active' : ''}">${p}</button>`);
            }
            pages.push(`<button type="button" data-page="${Math.min(totalPages, currentPage + 1)}">다음</button>`);

            wrap.innerHTML = pages.join('');
            wrap.querySelectorAll('button[data-page]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const nextPage = parseInt(btn.getAttribute('data-page') || '1', 10);
                    loadHourlyHistory(nextPage);
                });
            });
        }

        function formatTemp(val) {
            if (val === null || val === undefined || val === '') return '-';
            const n = Number(val);
            if (Number.isNaN(n)) return '-';
            return `${n.toFixed(2)}°C`;
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = String(str);
            return div.innerHTML;
        }
        
        
        // ===============================================
        // Utility & Fetch Logic 
        // ===============================================

        /** PHP 백엔드와 통신 */
        async function fetchData(mode, params = {}) {
            const formData = new FormData();
            formData.append('controller', 'mes'); 
            formData.append('mode', mode); 
            
            for (const key in params) {
                formData.append(key, params[key]);
            }

            try {
                const response = await fetch('./handler.php', {
                    method: 'POST',
                    body: formData
                });
                if (!response.ok) {
                    throw new Error(`HTTP 오류! 상태 코드: ${response.status}`);
                }
                return await response.json(); 
            } catch (error) {
                console.error(`[${mode}] 데이터 로딩 오류:`, error);
                return { result: 'success', data: [] }; 
            }
        }


        /** 온도에 따른 상태 및 클래스 결정 */
        function getTempStatus(temp) {
            if (temp > TEMP_MAX_ALERT) {
                return { status: '위험', class: 'temp-alert-text', cardClass: 'status-alert' };
            } else if (temp > TEMP_MAX_NORMAL) {
                return { status: '경고', class: 'temp-warn-text', cardClass: 'status-warn' };
            } else {
                return { status: '정상', class: 'temp-normal-text', cardClass: '' }; 
            }
        }
        
        /** HH:MM 포맷팅 */
        function formatTime(dateTimeStr) {
            if (!dateTimeStr || dateTimeStr.length < 16) return 'N/A';
            const time = dateTimeStr.split(' ')[1];
            return time.substring(0, 5); // HH:MM
        }


        // ===============================================
        // Card Rendering & Update
        // ===============================================

        /** 0. 초기 DOM 구조 생성 및 이벤트 바인딩 */
        function initializeSummaryCards() {
            const summaryContainer = document.getElementById('warehouse-summary');
            summaryContainer.innerHTML = ''; 

            FRIG_WAREHOUSES.forEach(w => {
                const card = document.createElement('div');
                card.id = `card_${w.code}`; 
                card.className = `warehouse-card`;
                card.dataset.machineCode = w.code; 
                
                card.addEventListener('click', handleCardClick);
                
                card.innerHTML = `
                    <h3>${w.name}</h3> 
                    <p class="temp-display">--.- <small>°C</small></p>
                    <div class="details">
                        <p>현재 상태: <span>로딩 중</span></p>
                        <p>기준 온도: Max ${TEMP_MAX_ALERT}°C</p>
                        <p id="time_${w.code}">측정 시각: N/A</p>
                    </div>
                `;
                summaryContainer.appendChild(card);
            });
            
            // 초기 활성화 설정: 첫 번째 창고를 선택한 상태로 시작
            currentChartMachine = FRIG_WAREHOUSES[0].code;
            document.getElementById(`card_${currentChartMachine}`)?.classList.add('is-active');
        }

        /** 1. 실시간 요약 카드 업데이트 & 데이터 캐시 (핵심: 실시간 데이터 수집) */
        async function loadCurrentTemp() {
            const response = await fetchData('getFrigWarehouseStatus');

            if (!Array.isArray(response.data)) {
                return;
            }
            
            const currentTime = new Date();
            const timeDisplay = currentTime.toTimeString().substring(0, 8); // HH:mm:ss

            // 현재 캐시에서 가장 큰 X값(순번)을 찾거나, 없으면 -1에서 시작
            let maxIndex = -1;
            Object.values(liveDataCache).forEach(cache => {
                if (cache.length > 0) {
                    const lastIndex = cache[cache.length - 1].x; 
                    if (lastIndex > maxIndex) {
                        maxIndex = lastIndex;
                    }
                }
            });
            const nextIndex = maxIndex + 1; // 다음 데이터 포인트의 순번

            response.data.forEach(item => {
                const machineId = item.machine_id; 
                const cardElement = document.getElementById(`card_${machineId}`);
                
                if (cardElement) {
                    const currentTemp = parseFloat(item.temp);
                    const maxLimit = TEMP_MAX_ALERT;
                    const { status, class: tempClass, cardClass } = getTempStatus(currentTemp);
                    
                    // 1. 카드 DOM 업데이트
                    const isActive = machineId === currentChartMachine ? 'is-active' : '';
                    cardElement.className = `warehouse-card ${cardClass} ${isActive}`; 
                    
                    const tempDisplayEl = cardElement.querySelector('.temp-display');
                    tempDisplayEl.className = `temp-display ${tempClass.replace('-text', '')}`;
                    tempDisplayEl.innerHTML = `${currentTemp.toFixed(1)} <small>°C</small>`;
                    
                    const detailsP = cardElement.querySelectorAll('.details p');
                    if (detailsP.length >= 3) {
                        detailsP[0].innerHTML = `현재 상태: <span class="${tempClass}">${status}</span>`;
                        detailsP[1].innerHTML = `기준 온도: Max ${maxLimit}°C`;
                        const timePart = item.measure_time ? formatTime(item.measure_time) : timeDisplay.substring(0, 5);
                        detailsP[2].innerHTML = `측정 시각: ${timePart}`;
                    }

                    // 2. 데이터 캐시에 누적 (Linear Scale용)
                    if (liveDataCache[machineId]) {
                        liveDataCache[machineId].push({ 
                            x: nextIndex, // 순번 (X축)
                            y: currentTemp,
                            time: timeDisplay // 툴팁용 시간 (HH:mm:ss)
                        });
                        
                        // 최대 포인트 개수 유지 (스크롤링을 위해 오래된 데이터 삭제)
                        if (liveDataCache[machineId].length > MAX_DATA_POINTS) {
                            liveDataCache[machineId].shift();
                        }
                    }
                }
            });
            
            // 3. 모든 창고 데이터를 기반으로 차트 갱신
            updateChartWithCache();
        }
        
        // ===============================================
        // Chart Functions 
        // ===============================================
        
        /** 차트 초기화 (Linear Scale 사용) */
        function initChart() {
            const ctx = document.getElementById('tempChart').getContext('2d');
            if (myChart) myChart.destroy(); 

            myChart = new Chart(ctx, {
                type: 'line', 
                data: { datasets: [] },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'linear', // Linear Scale 사용 (순번 기반)
                            title: { display: true, text: `측정 횟수 (${REFRESH_INTERVAL_MS/1000}초 간격)` },
                            min: 0, 
                            max: MAX_DATA_POINTS, 
                            ticks: {
                                display: true, 
                                maxTicksLimit: 10,
                                autoSkip: true,
                            }
                        },
                        y: {
                            title: { display: true, text: '온도 (°C)' },
                            min: -25,
                            max: 10,
                            ticks: {
                                stepSize: 5,
                                color: (context) => (context.tick && context.tick.value === 0 ? '#dc3545' : '#6c757d'),
                                font: (context) => ({
                                    size: 12,
                                    weight: context.tick && context.tick.value === 0 ? '700' : '400'
                                })
                            },
                            grid: {
                                color: (context) => (context.tick && context.tick.value === 0 ? '#000000' : 'rgba(0,0,0,0.08)'),
                                lineWidth: (context) => (context.tick && context.tick.value === 0 ? 2 : 1)
                            }
                        }
                    },
                    plugins: {
                        legend: { display: true },
                        tooltip: {
                             mode: 'index',
                             intersect: false,
                             callbacks: {
                                label: function(context) {
                                    const dataPoint = context.dataset.data[context.dataIndex];
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (dataPoint) {
                                        label += dataPoint.y.toFixed(1) + '°C';
                                    }
                                    return label;
                                },
                                title: function(context) {
                                    const dataPoint = context[0].dataset.data[context[0].dataIndex];
                                    return `시각: ${dataPoint.time || 'N/A'}`;
                                }
                             }
                        }
                    }
                }
            });
            
            document.getElementById('chartTitle').textContent = `실시간 온도 변화 추이`;
        }

        /** 캐시된 모든 데이터를 이용해 차트 갱신 */
        function updateChartWithCache() {
            if (!myChart) return;
            
            const datasets = [];
            let maxIndex = 0; // X축 최대값을 찾기 위한 변수

            // 1. 모든 창고의 온도 데이터셋 추가
            FRIG_WAREHOUSES.forEach(warehouse => {
                const machineCode = warehouse.code;
                const dataPoints = liveDataCache[machineCode] || [];

                if (dataPoints.length > 0) {
                    // 최신 데이터 순번(x)을 maxIndex에 업데이트
                    const lastX = dataPoints[dataPoints.length - 1].x;
                    if (lastX > maxIndex) {
                        maxIndex = lastX;
                    }
                }
                
                datasets.push({
                    label: warehouse.name,
                    data: dataPoints, 
                    borderColor: warehouse.color,
                    backgroundColor: warehouse.color + '20',
                    fill: false, // 모든 라인을 구분하기 위해 fill을 false로 설정
                    tension: 0.2,
                    pointRadius: machineCode === currentChartMachine ? 3 : 1 // 선택된 창고의 포인트를 더 강조
                });
            });
            
            // 2. 위험 기준선 데이터셋 추가 (모두에게 적용되는 공통 기준)
            // (이 데이터셋은 X축 범위 설정을 위해 가장 긴 데이터셋의 길이를 따라야 합니다.)
            const refDataPoints = liveDataCache[currentChartMachine] || [];
            datasets.push({
                label: 'Max Limit',
                data: refDataPoints.map(p => ({ x: p.x, y: TEMP_MAX_ALERT })),
                borderColor: 'red',
                borderWidth: 1,
                borderDash: [5, 5],
                pointRadius: 0,
                fill: false,
                tension: 0
            });
            
            myChart.data.datasets = datasets;

            // X축 범위 동적 업데이트 (스크롤 효과 구현)
            if (maxIndex > 0) {
                 // X축의 최대값을 모든 데이터 중 가장 큰 순번으로 설정
                 myChart.options.scales.x.max = maxIndex;
                 // X축의 최소값을 (최대값 - 표시할 데이터 개수)로 설정하여 스크롤
                 myChart.options.scales.x.min = Math.max(0, maxIndex - MAX_DATA_POINTS + 1);
            } else {
                 myChart.options.scales.x.max = MAX_DATA_POINTS;
                 myChart.options.scales.x.min = 0;
            }

            myChart.update();
        }


        /** 창고 카드 클릭 이벤트 핸들러 */
        function handleCardClick(event) {
            document.querySelectorAll('.warehouse-card').forEach(card => {
                card.classList.remove('is-active');
            });

            const card = event.currentTarget;
            card.classList.add('is-active');
            const machineCode = card.dataset.machineCode;

            currentChartMachine = machineCode;
            // 모든 창고를 표시하되, 포인트를 강조하기 위해 차트 갱신
            updateChartWithCache();
        }
        
        // ===============================================
        // Initializer & Timer
        // ===============================================

        window.onload = () => {
            // 0. DOM 구조 및 차트 초기화
            initializeSummaryCards(); 
            initChart(); 
            initTabs();
            initHistoryFilters();
            bindHistoryEvents();
            
            // 1. 페이지 로드 시 최초 1회 즉시 실행 (카드 & 차트 초기 데이터 로딩)
            loadCurrentTemp(); 
            
            // 2. 5초마다 실시간 데이터 요청 및 갱신
            const timerId = setInterval(() => {
                loadCurrentTemp(); 
            }, REFRESH_INTERVAL_MS);

            // 메모리 누수 방지
            window.onbeforeunload = () => {
                clearInterval(timerId);
            };
        };
    </script>
</body>
</html>
