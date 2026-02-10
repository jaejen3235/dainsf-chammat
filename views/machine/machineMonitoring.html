<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>설비 예지보전 모니터링</title>
    <style>
        /* ======================================= */
        /* Global & Theme Styles (Dark/Monitoring Theme) */
        /* ======================================= */
        :root {
            --primary-color: #00bcd4;     /* Cyan/Aqua Blue */
            --background: #212529;        /* Dark Background */
            --card-bg: #2b3035;           /* Darker Card */
            --main-font: #f8f9fa;         /* Light Font */
            --border-color: #3d444a;      
            
            /* Health Status Colors */
            --status-normal: #4caf50;     /* Normal (Green) */
            --status-warning: #ffeb3b;    /* Warning (Yellow) */
            --status-critical: #f44336;   /* Critical (Red) */
        }

        body {
            font-family: 'Malgun Gothic', 'Roboto', 'Dosis', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background);
            color: var(--main-font);
        }

        .main-container {
            padding: 30px;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Header */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }
        .report-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
        }
        .current-time {
            font-size: 18px;
            font-weight: 300;
            color: #ccc;
        }

        /* ======================================= */
        /* 설비 카드 그리드 레이아웃 */
        /* ======================================= */
        .equipment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .monitoring-card {
            background: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        /* Health Status Border Color */
        .status-normal-border { border-left: 5px solid var(--status-normal); }
        .status-warning-border { border-left: 5px solid var(--status-warning); }
        .status-critical-border { border-left: 5px solid var(--status-critical); }


        /* Card Header (설비명 & 상태) */
        .card-header {
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }
        .equip-name {
            font-size: 20px;
            font-weight: 600;
            color: var(--main-font);
            margin: 0;
        }
        .equip-id {
            font-size: 14px;
            color: var(--primary-color);
        }

        /* RUL (Remaining Useful Life) Section */
        .rul-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 15px 10px;
        }
        .rul-label {
            font-size: 14px;
            color: #aaa;
            margin-bottom: 5px;
        }
        .rul-score {
            font-size: 48px;
            font-weight: 900;
            font-family: 'Dosis', sans-serif;
            transition: color 0.3s;
        }
        .status-text {
            font-size: 16px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 5px;
            margin-top: 5px;
        }
        .text-normal { color: var(--status-normal); }
        .text-warning { color: var(--status-warning); }
        .text-critical { color: var(--status-critical); }
        .bg-critical { background-color: var(--status-critical); color: var(--background); }


        /* Sensor Data Section */
        .sensor-data {
            padding: 15px;
            border-top: 1px solid var(--border-color);
            background: #252a2f;
        }
        .sensor-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .sensor-label {
            color: #999;
        }
        .sensor-value {
            font-weight: 700;
        }
        .anomaly-value {
            color: var(--status-critical);
        }
        .unit {
            color: #777;
            font-weight: 400;
            margin-left: 5px;
        }

        /* Anomaly/Action Log */
        .anomaly-log {
            padding: 15px;
            font-size: 12px;
            color: #999;
            border-top: 1px solid var(--border-color);
            min-height: 40px;
        }
        .log-critical {
            color: var(--status-critical);
            font-weight: 600;
        }
        .log-warning {
            color: var(--status-warning);
            font-weight: 600;
        }

    </style>
</head>
<body>

    <div class='main-container'>
        
        <div class="report-header">
            <div class="report-title">⚙️ 설비 예지보전 진단 대시보드</div>
            <div class="current-time" id="current-time">실시간 업데이트: 2025-11-11 00:00:00</div>
        </div>

        <div class="equipment-grid" id="monitoring-grid">
            </div>

    </div>

    <script>
        // ===============================================
        // Mock Data: 설비별 잔여 수명 및 센서 데이터
        // RUL: Remaining Useful Life (%)
        // ===============================================
        const MOCK_MONITORING_DATA = [
            { 
                id: 'E101', name: 'CNC 가공기 A', sensorData: { vibration: 1.2, temp: 45.1, pressure: 5.2 }, 
                rul: 95, anomaly: 'Normal', lastUpdate: '15:01:20'
            },
            { 
                id: 'E102', name: '용접 로봇 3호', sensorData: { vibration: 4.8, temp: 78.5, pressure: 7.9 }, 
                rul: 25, anomaly: 'Critical', lastUpdate: '15:01:10', log: '진동 임계치 초과. 베어링 마모 예상.'
            },
            { 
                id: 'E201', name: '레이저 커팅기', sensorData: { vibration: 2.1, temp: 55.6, pressure: 6.1 }, 
                rul: 75, anomaly: 'Warning', lastUpdate: '15:01:05', log: '온도 상승 추이 감지. 냉각팬 점검 요망.'
            },
            { 
                id: 'E202', name: '포장 자동화 라인', sensorData: { vibration: 0.9, temp: 38.0, pressure: 4.5 }, 
                rul: 88, anomaly: 'Normal', lastUpdate: '15:00:50'
            },
        ];

        const gridContainer = document.getElementById('monitoring-grid');

        // ===============================================
        // Utility Functions
        // ===============================================

        /** RUL 값에 따라 상태와 CSS 클래스를 결정합니다. */
        function getHealthStatus(rul) {
            if (rul <= 30) return { name: '심각 (Critical)', scoreClass: 'text-critical', borderClass: 'status-critical-border', logClass: 'log-critical', statusBg: 'bg-critical' };
            if (rul <= 60) return { name: '경고 (Warning)', scoreClass: 'text-warning', borderClass: 'status-warning-border', logClass: 'log-warning', statusBg: '' };
            return { name: '정상 (Normal)', scoreClass: 'text-normal', borderClass: 'status-normal-border', logClass: '', statusBg: '' };
        }

        // ===============================================
        // Rendering Functions
        // ===============================================

        /**
         * 설비 모니터링 카드 하나를 렌더링합니다.
         */
        function createMonitoringCard(data) {
            const health = getHealthStatus(data.rul);
            
            const card = document.createElement('div');
            card.className = `monitoring-card ${health.borderClass}`;

            // RUL 스코어와 배경색 클래스 결정
            const statusTextClass = data.anomaly === 'Critical' ? health.statusBg : health.scoreClass;
            const statusText = data.anomaly === 'Critical' ? '고장 위험 (IMMEDIATE ACTION)' : health.name;

            // 로그 내용 설정
            const logContent = data.log || (data.anomaly === 'Normal' ? '특이 사항 없음' : '자동 진단 시스템 경고');
            const logClass = data.anomaly === 'Critical' ? 'log-critical' : (data.anomaly === 'Warning' ? 'log-warning' : '');

            card.innerHTML = `
                <div class="card-header">
                    <div>
                        <div class="equip-id">${data.id}</div>
                        <h4 class="equip-name">${data.name}</h4>
                    </div>
                </div>
                <div class="rul-section">
                    <div class="rul-label">잔여 수명 (RUL)</div>
                    <div class="rul-score ${health.scoreClass}">${data.rul.toFixed(0)}</div>
                    <div class="status-text ${statusTextClass}">${statusText}</div>
                </div>
                <div class="sensor-data">
                    <div class="sensor-row">
                        <span class="sensor-label">진동 레벨 (Vibration)</span>
                        <span class="sensor-value ${data.sensorData.vibration > 4.0 ? 'anomaly-value' : ''}">${data.sensorData.vibration.toFixed(1)} <span class="unit">mm/s</span></span>
                    </div>
                    <div class="sensor-row">
                        <span class="sensor-label">온도 (Temperature)</span>
                        <span class="sensor-value ${data.sensorData.temp > 70.0 ? 'anomaly-value' : ''}">${data.sensorData.temp.toFixed(1)} <span class="unit">°C</span></span>
                    </div>
                    <div class="sensor-row">
                        <span class="sensor-label">압력 (Pressure)</span>
                        <span class="sensor-value">${data.sensorData.pressure.toFixed(1)} <span class="unit">Bar</span></span>
                    </div>
                </div>
                <div class="anomaly-log">
                    <span class="${logClass}">[이상 감지 로그] ${logContent}</span>
                </div>
            `;
            return card;
        }

        /**
         * 설비 현황 그리드를 렌더링합니다.
         */
        function renderMonitoringGrid(data) {
            gridContainer.innerHTML = '';
            
            if (data.length === 0) {
                gridContainer.innerHTML = '<p style="text-align: center; grid-column: 1 / -1; color: #999;">모니터링 대상 설비가 없습니다.</p>';
                return;
            }

            data.forEach(item => {
                const card = createMonitoringCard(item);
                gridContainer.appendChild(card);
            });
        }
        
        /** 실시간 시계 업데이트 */
        function updateTime() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('current-time').textContent = 
                `실시간 업데이트: ${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        }


        // ===============================================
        // Initial Load
        // ===============================================
        window.onload = () => {
            // 페이지 로드 시 모니터링 데이터 렌더링
            renderMonitoringGrid(MOCK_MONITORING_DATA);
            
            // 실시간 시계 업데이트 시작
            updateTime();
            setInterval(updateTime, 1000);
        };
    </script>
</body>
</html>