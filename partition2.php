<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>모니터링 대시보드</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Malgun Gothic', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 20px;
            height: calc(100vh - 40px);
            max-width: 1920px;
            margin: 0 auto;
        }

        .section {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .section-header {
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .section-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .washer-header-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: auto;
        }

        .washer-header-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: bold;
        }

        .washer-header-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .washer-header-power {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }

        .washer-header-unit {
            font-size: 12px;
            color: #7f8c8d;
            margin-left: 3px;
        }

        .section-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: stretch;
            overflow-y: auto;
        }

        /* 온도 카드 스타일 */
        .temperature-cards {
            display: flex;
            flex-direction: row;
            gap: 15px;
            width: 100%;
            height: 100%;
        }

        .temperature-card {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start; /* top-align */
            align-items: stretch;
            flex: 1;
            min-width: 0;
            box-sizing: border-box;
        }

        .card-header {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:8px;
            gap:8px;
        }

        .card-title { text-align:center; }
        .card-title .warehouse-name {
            font-size:14px;
            font-weight:700;
            color:#333;
        }

        .card-title .warehouse-time {
            font-size:11px;
            color:#7f8c8d;
        }

        .card-stats { text-align:right; }

        .temperature-main { margin: 6px 0; }
        .temp-number.large { font-size:34px; font-weight:700; color:#2c3e50; }
        .temp-status { display:block; text-align:center; margin-top:6px; }

        .chart-canvas {
            width:100%;
            height:110px;
            max-height:110px; /* prevent growing beyond card */
            flex: 0 0 110px; /* fixed space in flex layout */
            border-radius:6px;
            background:linear-gradient(180deg, rgba(255,255,255,0.6), rgba(248,249,250,0.6));
            display:block;
            overflow:hidden;
        }

        .warehouse-info {
            text-align: center;
            margin-bottom: 10px;
        }

        .warehouse-name {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .warehouse-time {
            font-size: 9px;
            color: #7f8c8d;
        }

        .temperature-value {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .temp-number {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }

        .temp-unit {
            font-size: 12px;
            color: #7f8c8d;
        }

        .temp-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            margin-top: 5px;
        }

        .temp-normal {
            background-color: #d4edda;
            color: #155724;
        }

        .temp-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .temp-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .loading {
            text-align: center;
            color: #95a5a6;
            padding: 20px;
        }

        .error {
            text-align: center;
            color: #e74c3c;
            padding: 20px;
        }

        /* 온도 섹션 */
        .temperature-section {
            border-top: 4px solid #ff6b6b;
        }

        /* 금속검출 섹션 */
        .metal-detection-section {
            border-top: 4px solid #4ecdc4;
        }

        /* 세척기 전력량 섹션 */
        .washer-power-section {
            border-top: 4px solid #45b7d1;
        }

        /* 작업지시 현황 섹션 */
        .work-order-section {
            border-top: 4px solid #f9ca24;
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-on {
            background-color: #2ecc71;
        }

        .status-off {
            background-color: #e74c3c;
        }

        .value-display {
            font-size: 48px;
            font-weight: bold;
            color: #2c3e50;
            margin: 20px 0;
        }

        .unit {
            font-size: 24px;
            color: #7f8c8d;
            margin-left: 10px;
        }

        .placeholder-text {
            color: #95a5a6;
            font-size: 18px;
            text-align: center;
        }

        /* 금속검출 카드 스타일 */
        .metal-detection-cards {
            display: flex;
            flex-direction: row;
            gap: 15px;
            width: 100%;
            height: 100%;
        }

        .summary-card {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
            min-width: 0;
            justify-content: center;
            align-items: center;
        }

        .card-title {
            font-size: 13px;
            color: #7f8c8d;
            font-weight: 500;
            text-align: center;
        }

        .card-value {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
        }

        .card-value.ok {
            color: #2ecc71;
        }

        .card-value.ng {
            color: #e74c3c;
        }

        /* 작업지시 현황 테이블 스타일 */
        .work-order-table-container {
            width: 100%;
            height: 100%;
            overflow-y: auto;
        }

        .work-order-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .work-order-table thead {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .work-order-table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #dee2e6;
            font-size: 12px;
        }

        .work-order-table td {
            padding: 8px;
            border-bottom: 1px solid #e9ecef;
            color: #495057;
        }

        .work-order-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .work-order-table tbody tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        /* 세척기 전력량 스타일 */
        .washer-power-container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }


        .washer-status-running {
            background-color: #2ecc71;
        }

        .washer-status-stopped {
            background-color: #e74c3c;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .washer-power-value {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }

        .washer-power-unit {
            font-size: 12px;
            color: #7f8c8d;
            margin-left: 3px;
        }

        .washer-chart-container {
            flex: 1;
            min-height: 250px;
            position: relative;
            padding: 5px;
            overflow: visible;
        }

        .status-작업대기 {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-작업중 {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-작업완료 {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- 온도 섹션 -->
        <div class="section temperature-section">
            <div class="section-header">
                <h2 class="section-title">온도</h2>
            </div>
            <div class="section-content">
                <div id="temperature-container" class="temperature-cards">
                    <div class="loading">데이터를 불러오는 중...</div>
                </div>
            </div>
        </div>

        <!-- 금속검출 섹션 -->
        <div class="section metal-detection-section">
            <div class="section-header">
                <h2 class="section-title">금속검출</h2>
            </div>
            <div class="section-content">
                <div id="metal-detection-container" class="metal-detection-cards">
                    <div class="loading">데이터를 불러오는 중...</div>
                </div>
            </div>
        </div>

        <!-- 세척기 전력량 섹션 -->
        <div class="section washer-power-section">
            <div class="section-header">
                <h2 class="section-title">세척기 전력량</h2>
            </div>
            <div class="section-content">
                <div id="washer-power-container">
                    <div class="loading">데이터를 불러오는 중...</div>
                </div>
            </div>
        </div>

        <!-- 작업지시 현황 섹션 -->
        <div class="section work-order-section">
            <div class="section-header">
                <h2 class="section-title">작업지시 현황</h2>
            </div>
            <div class="section-content">
                <div id="work-order-container" class="work-order-table-container">
                    <div class="loading">데이터를 불러오는 중...</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        // 창고 이름 매핑
        const warehouseNames = {
            'frig_goods': '냉장창고',
            'frig_mix': '부재료&완제품',
            'frig_stuff': '냉동창고'
        };

        // 세척기 가동 기준 전력량 (kW) - 이 값 이상이면 가동으로 표시
        const WASHER_POWER_THRESHOLD = 1.0;
        
        // 차트 인스턴스
        let washerPowerChart = null;
        
        // 마지막으로 추가한 데이터의 timestamp (중복 방지)
        let lastAddedTimestamp = null;

        // 온도 데이터 가져오기
        async function loadTemperatureData() {
            const container = document.getElementById('temperature-container');
            
            // FormData 생성
            const formData = new FormData();
            formData.append('controller', 'mes');
            formData.append('mode', 'getFrigWarehouseStatus');
            
            try {
                const response = await fetch('./handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.result === 'success' && data.data) {
                    displayTemperatureData(data.data);
                } else {
                    container.innerHTML = '<div class="error">데이터를 불러올 수 없습니다.</div>';
                }
            } catch (error) {
                console.error('데이터 로딩 오류:', error);
                container.innerHTML = '<div class="error">서버와의 통신 중 오류가 발생했습니다.</div>';
            }
        }

        // 온도 데이터 표시
        // Chart instances per machine
        const tempCharts = {};

        function displayTemperatureData(data) {
            const container = document.getElementById('temperature-container');
            
            if (!data || data.length === 0) {
                container.innerHTML = '<div class="error">온도 데이터가 없습니다.</div>';
                return;
            }

            let html = '';

            data.forEach(function(item) {
                const id = item.machine_id;
                const warehouseName = warehouseNames[id] || item.machine_name || id;
                const temperature = parseFloat(item.temp) || 0;
                const maxLimit = parseFloat(item.max_limit) || 30;
                const measureTime = item.measure_time || '';

                // 온도 상태 판단
                let statusClass = 'temp-normal';
                let statusText = '정상';

                if (temperature >= maxLimit) {
                    statusClass = 'temp-danger';
                    statusText = '위험';
                } else if (temperature >= maxLimit - 5) {
                    statusClass = 'temp-warning';
                    statusText = '주의';
                }

                // 시간 포맷팅
                let timeDisplay = '';
                if (measureTime) {
                    const date = new Date(measureTime);
                    timeDisplay = date.toLocaleString('ko-KR', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                }

                html += `
                    <div class="temperature-card" id="card-${id}">
                        <div class="card-title" style="text-align:center; margin-bottom:6px;">
                            <div class="warehouse-name">${warehouseName}</div>
                            <div class="warehouse-time">${timeDisplay}</div>
                        </div>

                        <div class="temperature-main" style="display:flex; justify-content:center; align-items:center;">
                            <div class="temp-number large" style="font-size:34px; font-weight:700;">${temperature.toFixed(1)} °C</div>
                        </div>

                        <div style="text-align:center; margin-top:6px;"><span class="temp-status ${statusClass}">${statusText}</span></div>

                        <canvas id="chart-${id}" class="chart-canvas"></canvas>
                    </div>
                `;
            });

            container.innerHTML = html;

            // after DOM updated, fetch recent history for each machine and render charts
            data.forEach(function(item) {
                loadTempHistory(item.machine_id);
            });
        }

        async function loadTempHistory(machineId) {
            try {
                const fd = new FormData();
                fd.append('controller', 'mes');
                fd.append('mode', 'getRecentTemperature');
                fd.append('minutes', 10);
                fd.append('machine_code', machineId);

                const res = await fetch('./handler.php', { method: 'POST', body: fd });
                const json = await res.json();
                if (!json || json.result !== 'success') {
                    renderTempChart(machineId, []);
                    return;
                }
                renderTempChart(machineId, json.data || []);
            } catch (e) {
                console.error('온도 히스토리 로드 실패', e);
                renderTempChart(machineId, []);
            }
        }

        function renderTempChart(machineId, rows) {
            const canvas = document.getElementById('chart-' + machineId);
            if (!canvas) return;

            // empty placeholder
            if (!rows || rows.length === 0) {
                if (tempCharts[machineId]) { tempCharts[machineId].destroy(); delete tempCharts[machineId]; }
                const ctx = canvas.getContext('2d');
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * (window.devicePixelRatio || 1);
                canvas.height = rect.height * (window.devicePixelRatio || 1);
                ctx.clearRect(0,0,canvas.width,canvas.height);
                ctx.strokeStyle = '#ddd'; ctx.setLineDash([4,4]); ctx.beginPath(); ctx.moveTo(0, canvas.height/2); ctx.lineTo(canvas.width, canvas.height/2); ctx.stroke(); ctx.setLineDash([]);
                return;
            }

            const labels = rows.map(r => {
                const d = new Date(r.measure_time);
                return d.toLocaleTimeString();
            });
            const temps = rows.map(r => parseFloat(r.temp));

            // remove existing chart if any
            if (tempCharts[machineId]) { tempCharts[machineId].destroy(); }

            try {
                const ctx = document.getElementById('chart-' + machineId).getContext('2d');

                // determine y axis range: 30% padding above max and below min
                let yMin = Math.min(...temps);
                let yMax = Math.max(...temps);
                const padTop = Math.abs(yMax) * 0.3;
                const padBottom = Math.abs(yMin) * 0.3;
                yMin = yMin - padBottom;
                yMax = yMax + padTop;

                // safety: ensure a reasonable range
                if (!isFinite(yMin) || !isFinite(yMax)) {
                    yMin = undefined; yMax = undefined;
                } else if (yMax - yMin < 0.5) {
                    const mid = (yMax + yMin) / 2;
                    yMin = mid - 0.5; yMax = mid + 0.5;
                }

                tempCharts[machineId] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '온도 (°C)',
                            data: temps,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0,123,255,0.08)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                             x: { display: false, title: { display: false, text: '시간' }, ticks: { display: false } },
                             y: { display: true, title: { display: false, text: '온도 (°C)' }, min: yMin, max: yMax }
                        }
                    }
                });
            } catch (e) {
                console.error('온도 챠트 렌더링 실패', e);
            }
        }

        // 숫자 포맷팅 (콤마)
        function formatNumber(num) {
            return num.toLocaleString();
        }

        // 금속검출 데이터 가져오기
        async function loadMetalDetectionData() {
            const container = document.getElementById('metal-detection-container');
            
            // FormData 생성
            const formData = new FormData();
            formData.append('controller', 'mes');
            formData.append('mode', 'updateMetalStats');
            
            try {
                const response = await fetch('./handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error('검출 현황 조회 중 문제가 발생했습니다.');
                }
                
                const data = await response.json();
                
                if (data.result === 'success') {
                    displayMetalDetectionData(data);
                } else {
                    container.innerHTML = '<div class="error">데이터를 불러올 수 없습니다.</div>';
                }
            } catch (error) {
                console.error('금속검출 데이터 로딩 오류:', error);
                container.innerHTML = '<div class="error">서버와의 통신 중 오류가 발생했습니다.</div>';
            }
        }

        // 금속검출 데이터 표시
        function displayMetalDetectionData(data) {
            const container = document.getElementById('metal-detection-container');
            
            const currentItem = data.current_item ?? '-';
            const totalChecked = formatNumber(data.total_checked_sum ?? 0);
            const totalDetected = formatNumber(data.total_detected_sum ?? 0);
            
            const html = `
                <div class="summary-card">
                    <div class="card-title">현재 검사 품목</div>
                    <div class="card-value" id="current_item_md01">${currentItem}</div>
                </div>
                <div class="summary-card">
                    <div class="card-title">오늘 검사 총 수량 (OK + NG)</div>
                    <div class="card-value ok" id="total_check_count">${totalChecked}</div>
                </div>
                <div class="summary-card">
                    <div class="card-title">오늘 검출된 불량 수량 (NG)</div>
                    <div class="card-value ng" id="total_ng_count">${totalDetected}</div>
                </div>
            `;
            
            container.innerHTML = html;
        }

        // 작업지시 현황 데이터 가져오기
        async function loadWorkOrderData() {
            const container = document.getElementById('work-order-container');
            
            // 오늘 날짜 가져오기 (로컬 시간대 기준)
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const today = `${year}-${month}-${day}`;
            
            // FormData 생성
            const formData = new FormData();
            formData.append('controller', 'mes');
            formData.append('mode', 'getTodayWorkOrderList');
            formData.append('today', today);
            
            try {
                const response = await fetch('./handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.data) {
                    displayWorkOrderData(data.data);
                } else {
                    container.innerHTML = '<div class="error">데이터를 불러올 수 없습니다.</div>';
                }
            } catch (error) {
                console.error('작업지시 현황 데이터 로딩 오류:', error);
                container.innerHTML = '<div class="error">서버와의 통신 중 오류가 발생했습니다.</div>';
            }
        }

        // 작업지시 현황 데이터 표시
        function displayWorkOrderData(data) {
            const container = document.getElementById('work-order-container');
            
            if (!data || data.length === 0) {
                container.innerHTML = '<div class="error">작업지시 데이터가 없습니다.</div>';
                return;
            }

            let html = `
                <table class="work-order-table">
                    <thead>
                        <tr>
                            <th style="text-align: center;">품명(규격)</th>
                            <th style="text-align: center;">작업지시수량</th>
                            <th style="text-align: center;">잔여작업수량</th>
                            <th style="text-align: center;">상태</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            data.forEach(function(item) {
                const itemName = item.item_name || '-';
                const standard = item.standard || '';
                const itemDisplay = standard ? `${itemName} (${standard})` : itemName;
                const orderQty = formatNumber(item.order_qty || 0);
                const remainQty = formatNumber(item.remain_qty || 0);
                const status = item.status || '-';
                
                html += `
                    <tr>
                        <td style="text-align: center;">${itemDisplay}</td>
                        <td style="text-align: center;">${orderQty}</td>
                        <td style="text-align: center;">${remainQty}</td>
                        <td style="text-align: center;"></td>
                    </tr>
                `;
            });
            
            html += `
                    </tbody>
                </table>
            `;
            
            container.innerHTML = html;
        }

        // 세척기 전력량 데이터 가져오기
        async function loadWasherPowerData() {
            const container = document.getElementById('washer-power-container');
            
            // Chart.js 로드 확인
            if (typeof Chart === 'undefined') {
                console.error('Chart.js가 로드되지 않았습니다.');
                container.innerHTML = '<div class="error">차트 라이브러리를 불러올 수 없습니다.</div>';
                return;
            }
            
            // FormData 생성
            const formData = new FormData();
            formData.append('controller', 'mes');
            formData.append('mode', 'getCleaner');
            
            try {
                const response = await fetch('./handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                console.log('세척기 전력량 데이터:', data);
                
                if (data.result === 'success' && data.data) {
                    displayWasherPowerData(data.data);
                } else {
                    container.innerHTML = '<div class="error">데이터를 불러올 수 없습니다.</div>';
                }
            } catch (error) {
                console.error('세척기 전력량 데이터 로딩 오류:', error);
                container.innerHTML = '<div class="error">서버와의 통신 중 오류가 발생했습니다.</div>';
            }
        }

        // 세척기 전력량 데이터 표시
        function displayWasherPowerData(data) {
            const container = document.getElementById('washer-power-container');
            
            if (!data || data.length === 0) {
                container.innerHTML = '<div class="error">전력량 데이터가 없습니다.</div>';
                return;
            }

            console.log('원본 데이터:', data);

            // 전력량 데이터 필터링 (data_type이 'power' 또는 '전력'인 것 우선, 없으면 모든 데이터 사용)
            let powerData = data.filter(item => {
                const dataType = (item.data_type || '').toLowerCase();
                return dataType.includes('power') || dataType.includes('전력');
            });

            // power 데이터가 없으면 모든 데이터 사용
            if (powerData.length === 0) {
                powerData = data;
            }

            console.log('필터링된 데이터:', powerData);

            // 최신 전력량 값
            const latestPower = powerData[0];
            const currentPower = parseFloat(latestPower.value) || 0;
            const isRunning = currentPower >= WASHER_POWER_THRESHOLD;

            // 차트가 이미 있으면 최신 데이터만 추가, 없으면 초기 데이터 준비
            let chartData = [];
            if (washerPowerChart && lastAddedTimestamp) {
                // 기존 차트가 있으면 마지막 timestamp 이후의 새 데이터만 필터링
                const newData = powerData.filter(item => {
                    if (!item.timestamp) return false;
                    try {
                        const itemTime = new Date(item.timestamp).getTime();
                        const lastTime = new Date(lastAddedTimestamp).getTime();
                        return itemTime > lastTime;
                    } catch (e) {
                        return false;
                    }
                });
                
                // 새 데이터가 있으면 그것만 사용, 없으면 최신 1개만 사용 (값 업데이트용)
                if (newData.length > 0) {
                    chartData = newData;
                } else {
                    // 새 데이터가 없어도 최신 값은 업데이트 (같은 시간대 데이터)
                    chartData = [latestPower];
                }
            } else {
                // 차트가 없으면 초기 데이터로 최근 20개 사용
                chartData = powerData.slice(0, 20).reverse();
            }

            // 최신 timestamp 업데이트
            if (latestPower.timestamp) {
                lastAddedTimestamp = latestPower.timestamp;
            }

            console.log('차트 데이터:', chartData);

            renderWasherPowerDisplay(container, currentPower, isRunning, chartData);
        }

        // 세척기 전력량 표시 렌더링
        function renderWasherPowerDisplay(container, currentPower, isRunning, chartData) {
            const statusText = isRunning ? '가동' : '비가동';
            const statusClass = isRunning ? 'washer-status-running' : 'washer-status-stopped';
            
            // 차트가 이미 존재하는지 확인
            const existingChart = washerPowerChart;
            const chartExists = existingChart !== null && existingChart !== undefined;
            
            // 섹션 헤더에 상태와 전력량 표시
            const sectionHeader = container.closest('.section').querySelector('.section-header');
            if (sectionHeader) {
                let headerInfo = sectionHeader.querySelector('.washer-header-info');
                if (!headerInfo) {
                    headerInfo = document.createElement('div');
                    headerInfo.className = 'washer-header-info';
                    sectionHeader.appendChild(headerInfo);
                }
                
                headerInfo.innerHTML = `
                    <div class="washer-header-status">
                        <span class="washer-header-dot ${statusClass}"></span>
                        <span>${statusText}</span>
                    </div>
                    <div>
                        <span class="washer-header-power">${currentPower.toFixed(2)}</span>
                        <span class="washer-header-unit">kW</span>
                    </div>
                `;
            }
            
            // 차트 컨테이너 확인 및 생성
            let chartContainer = container.querySelector('.washer-chart-container');
            if (!chartContainer) {
                // 처음 로드 시 차트 컨테이너만 생성
                let html = `
                    <div class="washer-chart-container">
                        <canvas id="washer-power-chart"></canvas>
                    </div>
                `;
                container.innerHTML = html;
            }

            // 차트 업데이트 또는 생성
            setTimeout(() => {
                if (chartExists && chartData.length > 0) {
                    // 기존 차트에 데이터 추가
                    updateWasherPowerChart(chartData);
                } else if (!chartExists) {
                    // 차트가 없으면 새로 생성
                    if (chartData.length > 0) {
                        createWasherPowerChart(chartData);
                    } else {
                        createWasherPowerChart([]);
                    }
                }
            }, 100);
        }

        // 세척기 전력량 차트에 데이터 추가 (이어서 그리기)
        function updateWasherPowerChart(newData) {
            if (!washerPowerChart) {
                // 차트가 없으면 생성
                createWasherPowerChart(newData);
                return;
            }

            if (!newData || newData.length === 0) {
                return;
            }

            const chart = washerPowerChart;
            const existingLabels = chart.data.labels || [];
            const existingData = chart.data.datasets[0].data || [];
            
            // 새 데이터를 시간순으로 정렬 (오래된 것부터)
            const sortedData = [...newData].sort((a, b) => {
                const timeA = new Date(a.timestamp || 0).getTime();
                const timeB = new Date(b.timestamp || 0).getTime();
                return timeA - timeB;
            });
            
            // 새 데이터 추가
            sortedData.forEach(item => {
                if (item.timestamp) {
                    try {
                        const date = new Date(item.timestamp);
                        if (!isNaN(date.getTime())) {
                            const timeLabel = date.toLocaleTimeString('ko-KR', {
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });
                            const powerValue = parseFloat(item.value) || 0;
                            
                            // 중복 체크 (마지막 레이블과 비교)
                            const lastLabel = existingLabels[existingLabels.length - 1];
                            if (lastLabel !== timeLabel) {
                                // 새로운 데이터 추가
                                existingLabels.push(timeLabel);
                                existingData.push(powerValue);
                                
                                // 최대 50개까지만 유지 (오래된 데이터 제거)
                                const maxDataPoints = 50;
                                if (existingLabels.length > maxDataPoints) {
                                    existingLabels.shift();
                                    existingData.shift();
                                }
                            } else {
                                // 같은 시간이면 값만 업데이트
                                existingData[existingData.length - 1] = powerValue;
                            }
                        }
                    } catch (e) {
                        console.error('날짜 파싱 오류:', e);
                    }
                }
            });

            // 차트 업데이트 (부드러운 전환)
            chart.update({
                duration: 0, // 애니메이션 없이 즉시 업데이트
                easing: 'linear'
            });
        }

        // 세척기 전력량 차트 생성
        function createWasherPowerChart(data) {
            console.log('차트 생성 시작, 데이터 개수:', data ? data.length : 0);
            
            if (typeof Chart === 'undefined') {
                console.error('Chart.js가 로드되지 않았습니다.');
                return;
            }

            const ctx = document.getElementById('washer-power-chart');
            
            if (!ctx) {
                console.error('차트 canvas 요소를 찾을 수 없습니다.');
                return;
            }

            console.log('Canvas 요소 찾음:', ctx);

            // 기존 차트가 있으면 제거하지 않고 업데이트만
            if (washerPowerChart) {
                console.log('기존 차트 업데이트');
                updateWasherPowerChart(data);
                return;
            }

            // 데이터가 없으면 빈 차트 생성
            if (!data || data.length === 0) {
                washerPowerChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: '전력량 (kW)',
                            data: [],
                            borderColor: '#45b7d1',
                            backgroundColor: 'rgba(69, 183, 209, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                top: 10,
                                right: 10,
                                bottom: 10,
                                left: 10
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: '전력량 (kW)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: '시간'
                                }
                            }
                        }
                    }
                });
                return;
            }

            // 시간 레이블과 전력량 값 추출
            const labels = data.map(item => {
                if (item.timestamp) {
                    try {
                        const date = new Date(item.timestamp);
                        if (!isNaN(date.getTime())) {
                            return date.toLocaleTimeString('ko-KR', {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    } catch (e) {
                        console.error('날짜 파싱 오류:', e);
                    }
                }
                return '';
            }).filter(label => label !== '');

            const powerValues = data.map(item => {
                const value = parseFloat(item.value);
                return isNaN(value) ? 0 : value;
            });

            try {
                console.log('차트 생성 시도, 레이블 개수:', labels.length, '값 개수:', powerValues.length);
                washerPowerChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '전력량 (kW)',
                            data: powerValues,
                            borderColor: '#45b7d1',
                            backgroundColor: 'rgba(69, 183, 209, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                top: 10,
                                right: 10,
                                bottom: 10,
                                left: 10
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: '전력량 (kW)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: '시간'
                                }
                            }
                        }
                    }
                });
                console.log('차트 생성 성공');
            } catch (error) {
                console.error('차트 생성 오류:', error);
                console.error('오류 상세:', error.stack);
            }
        }

        // 페이지 로드 시 데이터 가져오기
        document.addEventListener('DOMContentLoaded', function() {
            loadTemperatureData();
            loadMetalDetectionData();
            loadWorkOrderData();
            loadWasherPowerData();
            
            // 5초마다 데이터 갱신
            setInterval(loadTemperatureData, 5000);
            setInterval(loadMetalDetectionData, 5000);
            setInterval(loadWorkOrderData, 5000);
            setInterval(loadWasherPowerData, 5000);
        });
    </script>
</body>
</html>