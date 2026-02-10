<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>설비별 가동 현황 (간소화)</title>
    <style>
        /* ======================================= */
        /* Global & Theme Styles (Deep Purple) */
        /* ======================================= */
        :root {
            --primary-color: #673ab7;    /* Deep Purple */
            --background: #f8f9fa;       
            --card-bg: white;
            --main-font: #343a40;
            --border-color: #dee2e6;
            
            /* Status Colors */
            --status-run: #4caf50;       /* RUN (Green) */
            --status-stop: #ff9800;      /* STOP/IDLE (Orange) */
            --status-fault: #dc3545;     /* FAULT/DOWN (Red) */
            
            /* OEE/KPI Colors */
            --oee-good: #e8f5e9;         /* OEE 85%+ (Light Green) */
            --oee-warn: #fffde7;         /* OEE 70-85% (Light Yellow) */
            --oee-bad: #ffebee;          /* OEE < 70% (Light Red) */
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

        /* Header & Search */
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
        .btn-box {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .input, .select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            background-color: var(--primary-color);
            color: white;
        }

        /* ======================================= */
        /* 설비 카드 그리드 레이아웃 */
        /* ======================================= */
        .equipment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .equipment-card {
            background: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .equipment-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
        
        /* OEE에 따른 카드 배경색 (시각적 강조) */
        .oee-good-bg { background-color: var(--oee-good); }
        .oee-warn-bg { background-color: var(--oee-warn); }
        .oee-bad-bg { background-color: var(--oee-bad); }

        /* Card Header (설비명 & 상태) */
        .card-header {
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            background: rgba(255, 255, 255, 0.7); /* 반투명 배경 */
        }
        .equip-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }
        .equip-id {
            font-size: 12px;
            color: #777;
        }

        /* Status Badge */
        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 700;
            color: white;
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.2);
        }
        .status-RUN { background-color: var(--status-run); }
        .status-STOP { background-color: var(--status-stop); }
        .status-FAULT { background-color: var(--status-fault); }

        /* Card Body (KPIs) */
        .card-body {
            padding: 15px;
        }
        .kpi-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }
        .kpi-row:last-child {
            border-bottom: none; /* 마지막 항목 하단 선 제거 */
        }
        .kpi-label {
            font-size: 14px;
            color: #555;
            font-weight: 500;
        }
        .kpi-value {
            font-size: 16px;
            font-weight: 700;
            color: var(--main-font);
        }
        /* OEE Value Styling */
        .oee-value {
            font-size: 24px !important;
            font-weight: 900 !important;
            color: var(--primary-color) !important;
        }

        /* Last Update Time */
        .card-footer {
            text-align: right;
            font-size: 11px;
            color: #999;
            padding: 0 15px 10px 15px;
        }
    </style>
</head>
<body>

    <div class='main-container'>
        <div class='content-wrapper'>
            
            <div class="report-header">
                <div class="report-title">📊 설비별 가동 현황 대시보드 (OEE & 가동률)</div>
                <div class="btn-box">
                    <select class="select" id="line_select" onchange="searchEquipmentStatus()">
                        <option value="">전체 라인</option>
                        <option value="L-A">생산 라인 A</option>
                        <option value="L-B">생산 라인 B</option>
                    </select>
                    <input type='button' class='btn' value='새로고침' onclick='searchEquipmentStatus()' />
                </div>
            </div>

            <div class="equipment-grid" id="equipment-grid">
                <p style="text-align: center; grid-column: 1 / -1; color: #999;">데이터를 로드하는 중...</p>
            </div>

        </div>
    </div>

    <script>
        // ===============================================
        // Mock Data: 설비별 실시간 가동 현황
        // 성능(P)과 품질(Q) 데이터는 내부적으로만 유지하고 화면에 표시하지 않음
        // ===============================================
        const MOCK_DATA = [
            { 
                id: 'E101', name: 'CNC 가공기 A', line: 'L-A', status: 'RUN', 
                oee: 87.5, availability: 95.0, performance: 92.1, quality: 99.8, lastUpdate: '15:00:20'
            },
            { 
                id: 'E102', name: '용접 로봇 3호', line: 'L-A', status: 'FAULT', 
                oee: 55.2, availability: 60.5, performance: 91.2, quality: 99.0, lastUpdate: '14:58:10'
            },
            { 
                id: 'E201', name: '레이저 커팅기', line: 'L-B', status: 'RUN', 
                oee: 78.9, availability: 90.0, performance: 88.0, quality: 99.5, lastUpdate: '15:01:05'
            },
            { 
                id: 'E202', name: '포장 자동화 라인', line: 'L-B', status: 'STOP', 
                oee: 65.0, availability: 70.0, performance: 92.8, quality: 99.1, lastUpdate: '15:00:50'
            },
            { 
                id: 'E103', name: '프레스기 #1', line: 'L-A', status: 'RUN', 
                oee: 90.1, availability: 98.0, performance: 92.5, quality: 99.9, lastUpdate: '14:59:30'
            },
        ];

        const gridContainer = document.getElementById('equipment-grid');

        // ===============================================
        // Utility Functions
        // ===============================================

        /** OEE 값에 따라 배경색 클래스를 결정합니다. */
        function getOeeClass(oee) {
            if (oee >= 85) return 'oee-good-bg';
            if (oee >= 70) return 'oee-warn-bg';
            return 'oee-bad-bg';
        }

        // ===============================================
        // Rendering Functions
        // ===============================================

        /**
         * 설비 카드 하나를 렌더링합니다.
         */
        function createEquipmentCard(data) {
            const oeeClass = getOeeClass(data.oee);
            
            const card = document.createElement('div');
            card.className = `equipment-card ${oeeClass}`;

            card.innerHTML = `
                <div class="card-header">
                    <div>
                        <div class="equip-id">${data.line} | ${data.id}</div>
                        <h4 class="equip-name">${data.name}</h4>
                    </div>
                    <span class="status-badge status-${data.status}">${data.status}</span>
                </div>
                <div class="card-body">
                    <div class="kpi-row">
                        <span class="kpi-label">OEE (종합 설비 효율)</span>
                        <span class="kpi-value oee-value">${data.oee.toFixed(1)}%</span>
                    </div>
                    <div class="kpi-row">
                        <span class="kpi-label">가동률 (Availability)</span>
                        <span class="kpi-value">${data.availability.toFixed(1)}%</span>
                    </div>
                    </div>
                <div class="card-footer">
                    업데이트: ${data.lastUpdate}
                </div>
            `;
            return card;
        }

        /**
         * 설비 현황 그리드를 렌더링합니다.
         */
        function renderEquipmentGrid(data) {
            gridContainer.innerHTML = '';
            
            if (data.length === 0) {
                gridContainer.innerHTML = '<p style="text-align: center; grid-column: 1 / -1; color: #999;">조회 조건에 맞는 설비가 없습니다.</p>';
                return;
            }

            data.forEach(item => {
                const card = createEquipmentCard(item);
                gridContainer.appendChild(card);
            });
        }

        // ===============================================
        // Event Handlers
        // ===============================================

        /** 설비 가동 현황 검색 및 필터링 */
        function searchEquipmentStatus() {
            const lineCode = document.getElementById('line_select').value;
            
            let filteredData = MOCK_DATA;
            
            if (lineCode) {
                filteredData = MOCK_DATA.filter(item => item.line === lineCode);
            }

            renderEquipmentGrid(filteredData); 
        }

        // ===============================================
        // Initial Load
        // ===============================================
        window.onload = () => {
            // 페이지 로드 시 자동으로 검색 실행
            searchEquipmentStatus();
        };
    </script>
</body>
</html>