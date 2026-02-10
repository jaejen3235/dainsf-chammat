<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>설비 예지보전 분석 (Full Width & Flex)</title>
    <style>
        /* ======================================= */
        /* Global & Theme Styles */
        /* ======================================= */
        :root {
            --primary-color: #00bcd4;     /* Cyan/Aqua Blue */
            --secondary-color: #673ab7;   /* Deep Purple */
            --background: #f8f9fa;        /* Light Background */
            --card-bg: white;
            --main-font: #343a40;         /* Dark Font */
            --border-color: #dee2e6;
            --status-normal: #4caf50;     /* Normal (Green) */
            --status-warning: #ff9800;    /* Warning (Orange) */
            --status-critical: #f44336;   /* Critical (Red) */
            --vibration-color: #ff5722;   /* Deep Orange */
            --energy-color: #3f51b5;      /* Indigo */
        }

        body {
            font-family: 'Malgun Gothic', 'Roboto', sans-serif;
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

        /* --- Header & Card Structure --- */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--primary-color);
        }
        .report-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--secondary-color);
        }
        .select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
        }
        
        /* New Layout: Main Chart (Full Width) + Cards (Flex) */
        .card-row-flex {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        
        /* Card Styles */
        .analysis-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            margin-bottom: 20px;
        }

        /* Card Width for Flex Row (1/3 each) */
        .analysis-card.flex-item {
            flex: 1; /* 세 카드가 동일한 너비를 갖도록 설정 */
            min-width: 280px; /* 너무 줄어들지 않도록 최소 너비 설정 */
        }
        
        .chart-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        .kpi-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }

        /* --- Chart Simulation (RUL 추이) --- */
        /* RUL Chart Card is now full-width */
        .chart-simulation {
            height: 250px; 
            border-bottom: 1px solid #ccc;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end; 
            margin-bottom: 10px;
        }
        .time-axis {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 12px;
            color: #777;
        }
        .current-time-line {
            position: absolute;
            top: 0;
            left: 50%; 
            height: 100%;
            width: 2px;
            background-color: var(--secondary-color);
            z-index: 10;
        }
        .current-time-line::before {
            content: '현재 시점';
            position: absolute;
            top: -20px;
            left: -30px;
            color: var(--secondary-color);
            font-weight: 600;
            font-size: 12px;
            white-space: nowrap;
        }
        .rul-prediction-bar {
            width: 100%;
            height: 40px; 
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 40px; 
        }
        .risk-segment {
            height: 100%;
            transition: all 0.5s ease-out;
            border-right: 1px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: white;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.4);
        }
        .segment-label {
            position: absolute;
            top: 45px;
            font-size: 12px;
            color: #555;
            white-space: nowrap;
        }
        .chart-note {
            text-align: center;
            font-size: 12px;
            color: #777;
            padding-top: 10px;
        }
        
        /* --- KPI Area --- */
        .kpi-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .kpi-item:last-child {
            border-bottom: none;
        }
        .kpi-label {
            font-size: 15px;
            font-weight: 500;
            color: #555;
        }
        .kpi-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
        }
        .kpi-value.small {
            font-size: 18px;
            font-weight: 600;
            color: var(--main-font);
        }
        
        /* --- Trend Chart Area --- */
        .trend-chart-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .trend-chart {
            height: 70px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 10px;
            position: relative;
            overflow: hidden;
            background: #f0f4f7;
        }
        .trend-title {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .trend-line {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.7;
        }
        .vibration-trend { background: var(--vibration-color); }
        .energy-trend { background: var(--energy-color); }
        .trend-value {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 16px;
            font-weight: 700;
        }

        /* --- Pie Chart Area --- */
        .pie-simulation {
            margin-top: 10px;
            text-align: center; 
        }
        .pie-chart-legend {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 15px;
            font-size: 14px;
            align-items: flex-start;
        }
        .legend-item {
            display: flex;
            align-items: center;
        }
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            margin-right: 8px;
        }
        .pie-chart-placeholder {
            width: 120px; 
            height: 120px;
            border-radius: 50%;
            margin: 10px auto 0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <div class='main-container'>
        
        <div class="report-header">
            <div class="report-title">📊 설비 예지보전 분석 리포트 (진동/에너지 집중)</div>
            <div class="btn-box">
                <label for="equipment_select" style="font-size: 14px; font-weight: 600; margin-right: 5px;">대상 설비:</label>
                <select class="select" id="equipment_select" onchange="loadAnalysisData(this.value)">
                    <option value="E102">E102 - 용접 로봇 3호 (기계/전기 위험)</option>
                    <option value="E201">E201 - 레이저 커팅기 (경고)</option>
                    <option value="E101">E101 - CNC 가공기 A (정상)</option>
                </select>
            </div>
        </div>

        <p id="analysis-note" style="text-align: center; color: var(--secondary-color); font-weight: 700;">
            현재 E102 (용접 로봇 3호)의 진동 및 전류 데이터를 기반으로 분석한 결과입니다. <span style="color: var(--status-critical);">고장 임박! 2주 내 긴급 조치 필요.</span>
        </p>

        <div class="analysis-card">
            <div class="chart-title">예상 고장까지의 잔여 기간 및 위험도 추이</div>
            <div class="chart-simulation">
                <div class="current-time-line"></div>
                <div class="rul-prediction-bar" id="rul-bar-container">
                    </div>
            </div>
            <div class="time-axis">
                <span>현재 시점</span>
                <span id="warning-duration">경고 시작</span>
                <span id="critical-duration">보전 권고 시점</span>
                <span id="failure-point">예상 고장 시점</span>
            </div>
            <div class="chart-note">잔여 기간은 진동/에너지 데이터의 복합적인 열화 추세를 바탕으로 예측됩니다.</div>
        </div>

        <div class="card-row-flex">
            
            <div class="analysis-card flex-item">
                <div class="kpi-title">핵심 진단 지표</div>
                
                <div class="kpi-item">
                    <span class="kpi-label">현재 잔여 수명 ($\text{RUL}$)</span>
                    <span class="kpi-value" style="color: var(--status-critical);" id="current_rul">25%</span>
                </div>
                
                <div class="kpi-item">
                    <span class="kpi-label">예상 고장 일자</span>
                    <span class="kpi-value small" style="color: var(--status-critical);" id="failure_date">2026-02-28</span>
                </div>

                <div class="kpi-item">
                    <span class="kpi-label">권고 보전 시점</span>
                    <span class="kpi-value small" style="color: var(--status-warning);" id="maintenance_date">2026-02-14</span>
                </div>
            </div>
            
            <div class="analysis-card flex-item">
                <div class="kpi-title">핵심 센서 데이터 추이 (과거 1개월)</div>
                <div class="trend-chart-container">
                    <div class="trend-chart">
                        <div class="trend-title" style="color: var(--vibration-color);">진동 $\text{RMS}$ (기계적 건전성)</div>
                        <div class="trend-line vibration-trend" id="vibration-trend-line"></div>
                        <div class="trend-value" id="vibration-value" style="color: var(--vibration-color);">4.8 $\text{mm/s}$ (↑ 150%)</div>
                    </div>
                    <div class="trend-chart">
                        <div class="trend-title" style="color: var(--energy-color);">전류 $\text{RMS}$ (전기적/부하 건전성)</div>
                        <div class="trend-line energy-trend" id="energy-trend-line"></div>
                        <div class="trend-value" id="energy-value" style="color: var(--energy-color);">12.5 $\text{A}$ (↑ 40%)</div>
                    </div>
                </div>
            </div>

            <div class="analysis-card flex-item">
                <div class="kpi-title">고장 원인 기여도 분석</div>
                <div class="pie-simulation">
                    <div class="pie-chart-placeholder" id="pie-chart-placeholder"></div>
                    <div class="pie-chart-legend" id="pie-chart-legend">
                        </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Data and functions from the previous response (loadAnalysisData, renderRulBarChart, renderPieChart) 
        // are included here for completeness of the HTML file.
        // --- START MOCK DATA & JS FUNCTIONS ---

        const DATA_MAP = {
            'E102': {
                name: '용접 로봇 3호', rul: 25, failDate: '2026-02-28', maintDate: '2026-02-14', statusColor: 'var(--status-critical)', 
                note: '고장 임박! 2주 내 긴급 조치 필요.', 
                vibRMS: '4.8 mm/s (↑ 150%)', energyRMS: '12.5 A (↑ 40%)',
                segmentDurations: [
                    { label: '정상', duration: '1개월 전', width: '10%', color: 'var(--status-normal)' },
                    { label: '경고', duration: '1개월 전', width: '30%', color: 'var(--status-warning)' },
                    { label: '임박', duration: '2주 전', width: '60%', color: 'var(--status-critical)' }
                ],
                pieData: [
                    { name: '기계적 결함 (마모)', percent: 55, color: 'var(--vibration-color)' },
                    { name: '전기적 부하/불균형', percent: 30, color: 'var(--energy-color)' },
                    { name: '기타/미분류', percent: 15, color: 'var(--primary-color)' }
                ],
                vibClip: 'polygon(0% 100%, 10% 80%, 30% 60%, 50% 40%, 70% 20%, 90% 10%, 100% 5%, 100% 100%, 0% 100%)',
                energyClip: 'polygon(0% 100%, 10% 70%, 30% 60%, 50% 55%, 70% 50%, 90% 45%, 100% 40%, 100% 100%, 0% 100%)'
            },
            'E201': {
                name: '레이저 커팅기', rul: 75, failDate: '2026-07-15', maintDate: '2026-05-01', statusColor: 'var(--status-warning)', 
                note: 'RUL 하락 추이 감지. 3개월 내 점검 권고.', 
                vibRMS: '2.5 mm/s (↑ 40%)', energyRMS: '9.0 A (± 5%)',
                segmentDurations: [
                    { label: '정상', duration: '3개월', width: '60%', color: 'var(--status-normal)' },
                    { label: '경고', duration: '2개월', width: '30%', color: 'var(--status-warning)' },
                    { label: '임박', duration: '1개월', width: '10%', color: 'var(--status-critical)' }
                ],
                pieData: [
                    { name: '기계적 결함 (마모)', percent: 70, color: 'var(--vibration-color)' },
                    { name: '전기적 부하/불균형', percent: 10, color: 'var(--energy-color)' },
                    { name: '기타/미분류', percent: 20, color: 'var(--primary-color)' }
                ],
                vibClip: 'polygon(0% 100%, 10% 90%, 30% 80%, 50% 70%, 70% 60%, 90% 50%, 100% 40%, 100% 100%, 0% 100%)',
                energyClip: 'polygon(0% 100%, 10% 95%, 30% 90%, 50% 95%, 70% 90%, 90% 85%, 100% 80%, 100% 100%, 0% 100%)'
            },
            'E101': {
                name: 'CNC 가공기 A', rul: 95, failDate: '양호 (장기간)', maintDate: '정기 보전 예정', statusColor: 'var(--status-normal)', 
                note: '정상 상태 유지. 예방 보전 일정 준수 요망.', 
                vibRMS: '1.2 mm/s (↓ 10%)', energyRMS: '7.5 A (± 2%)',
                segmentDurations: [
                    { label: '정상', duration: '9개월', width: '90%', color: 'var(--status-normal)' },
                    { label: '경고', duration: '1개월', width: '8%', color: 'var(--status-warning)' },
                    { label: '임박', duration: '2주', width: '2%', color: 'var(--status-critical)' }
                ],
                pieData: [
                    { name: '기계적 결함', percent: 10, color: 'var(--vibration-color)' },
                    { name: '전기적 부하', percent: 10, color: 'var(--energy-color)' },
                    { name: '기타/미분류', percent: 80, color: 'var(--primary-color)' }
                ],
                vibClip: 'polygon(0% 100%, 10% 95%, 30% 90%, 50% 92%, 70% 90%, 90% 95%, 100% 90%, 100% 100%, 0% 100%)',
                energyClip: 'polygon(0% 100%, 10% 98%, 30% 99%, 50% 97%, 70% 98%, 90% 99%, 100% 98%, 100% 100%, 0% 100%)'
            }
        };

        const PIE_PLACEHOLDER = document.getElementById('pie-chart-placeholder');
        const PIE_LEGEND = document.getElementById('pie-chart-legend');
        const RUL_BAR_CONTAINER = document.getElementById('rul-bar-container');
        const VIB_TREND_LINE = document.getElementById('vibration-trend-line');
        const ENERGY_TREND_LINE = document.getElementById('energy-trend-line');

        function renderRulBarChart(segments) {
            RUL_BAR_CONTAINER.innerHTML = '';
            RUL_BAR_CONTAINER.className = 'rul-prediction-bar';

            let currentLeft = 0;
            
            segments.forEach(segment => {
                const widthPercent = parseFloat(segment.width.replace('%', ''));
                
                const segmentDiv = document.createElement('div');
                segmentDiv.className = 'risk-segment';
                segmentDiv.style.width = segment.width;
                segmentDiv.style.backgroundColor = segment.color;
                
                const labelDiv = document.createElement('div');
                labelDiv.className = 'segment-label';
                
                if (segment.label === '경고') {
                    labelDiv.style.left = `calc(${currentLeft}% - 30px)`;
                    labelDiv.textContent = '경고 시작';
                    RUL_BAR_CONTAINER.appendChild(labelDiv);
                } else if (segment.label === '임박') {
                    labelDiv.style.left = `calc(${currentLeft}% - 30px)`;
                    labelDiv.textContent = '보전 권고 시점';
                    RUL_BAR_CONTAINER.appendChild(labelDiv);
                }

                currentLeft += widthPercent;
                RUL_BAR_CONTAINER.appendChild(segmentDiv);
            });
            
            const failLabelDiv = document.createElement('div');
            failLabelDiv.className = 'segment-label';
            failLabelDiv.style.left = `calc(100% - 30px)`;
            failLabelDiv.textContent = '예상 고장';
            RUL_BAR_CONTAINER.appendChild(failLabelDiv);
        }

        function renderPieChart(pieData) {
            let conicGradient = 'conic-gradient(';
            PIE_LEGEND.innerHTML = '';
            let currentAngle = 0;

            pieData.forEach((item, index) => {
                const startAngle = currentAngle;
                const endAngle = startAngle + item.percent;
                
                conicGradient += `${item.color} ${startAngle}% ${endAngle}%`;
                if (index < pieData.length - 1) {
                    conicGradient += ', ';
                }
                currentAngle = endAngle;

                const legendItem = document.createElement('div');
                legendItem.className = 'legend-item';
                legendItem.innerHTML = `<span class="legend-color" style="background-color: ${item.color};"></span> ${item.name} (${item.percent}%)`;
                PIE_LEGEND.appendChild(legendItem);
            });
            conicGradient += ')';
            
            PIE_PLACEHOLDER.style.background = conicGradient;
        }

        function loadAnalysisData(equipmentId) {
            const data = DATA_MAP[equipmentId];
            if (!data) return;
            
            document.getElementById('analysis-note').innerHTML = `현재 ${equipmentId} (${data.name})의 진동 및 전류 데이터를 기반으로 분석한 결과입니다. <span style="color: ${data.statusColor};">${data.note}</span>`;
            document.getElementById('current_rul').textContent = data.rul + '%';
            document.getElementById('current_rul').style.color = data.statusColor;
            document.getElementById('failure_date').textContent = data.failDate;
            document.getElementById('failure_date').style.color = data.statusColor;
            document.getElementById('maintenance_date').textContent = data.maintDate;
            document.getElementById('maintenance_date').style.color = (data.statusColor === 'var(--status-critical)' ? 'var(--status-warning)' : data.statusColor);
            
            document.getElementById('vibration-value').textContent = data.vibRMS;
            VIB_TREND_LINE.style.clipPath = data.vibClip;

            document.getElementById('energy-value').textContent = data.energyRMS;
            ENERGY_TREND_LINE.style.clipPath = data.energyClip;

            renderRulBarChart(data.segmentDurations);
            renderPieChart(data.pieData);
            
            document.getElementById('warning-duration').textContent = '경고 시작';
            document.getElementById('critical-duration').textContent = '보전 권고 시점';
        }

        // --- END MOCK DATA & JS FUNCTIONS ---

        // 초기 로드
        window.onload = () => {
            loadAnalysisData('E102');
        };
    </script>
</body>
</html>