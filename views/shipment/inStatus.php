<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>제품 입고 현황</title>
    <style>
        /* CSS Variables and General Layout (Consistent with previous pages) */
        :root {
            --primary-color: #00bcd4;     
            --secondary-color: #673ab7;   
            --background: #f8f9fa;
            --card-bg: white;
            --main-font: #343a40;
            --border-color: #dee2e6;
            --status-success: #4caf50;    /* PASS / Available */
            --status-danger: #f44336;     /* FAIL / Critical */
            --status-info: #2196f3;       /* Total / Info */
            --status-warning: #ff9800;    /* Pending / Warning */
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

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--primary-color);
        }

        .analysis-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            margin-bottom: 30px;
        }

        .card-header {
            font-size: 20px;
            font-weight: 600;
            color: var(--main-font);
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #eee;
        }
        
        /* KPI Grid and Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .kpi-card {
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .kpi-title {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 5px;
            opacity: 0.8;
        }
        
        .kpi-value {
            font-size: 32px;
            font-weight: 700;
        }

        /* KPI Colors */
        .kpi-total { background-color: var(--status-info); }
        .kpi-daily { background-color: var(--primary-color); }
        .kpi-hold { background-color: var(--status-danger); }
        .kpi-ratio { background-color: var(--status-success); }
        
        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .data-table th {
            background-color: #f0f4f7;
            color: var(--secondary-color);
            font-weight: 600;
        }

        .data-table tr:hover {
            background-color: #f5f5f5;
            cursor: default; /* Not a transaction list */
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: 700;
            font-size: 11px;
            color: white;
        }
        .quality-pass { background-color: var(--status-success); }
        .quality-fail { background-color: var(--status-danger); }
        .quality-hold { background-color: var(--status-warning); }
        .location-code { color: var(--secondary-color); font-weight: 600; }
        
    </style>
</head>
<body>

    <div class="main-container">
        <div class="page-title">📈 제품 입고 현황 및 재고 $\text{Status}$</div>

        <div class="kpi-grid">
            <div class="kpi-card kpi-total">
                <div class="kpi-title">현재 $\text{총}$ 입고 재고 (EA)</div>
                <div class="kpi-value">5,500</div>
            </div>
            <div class="kpi-card kpi-daily">
                <div class="kpi-title">금일 입고 완료 수량 (EA)</div>
                <div class="kpi-value">495</div>
            </div>
            <div class="kpi-card kpi-hold">
                <div class="kpi-title">품질 불합격/대기 재고 (EA)</div>
                <div class="kpi-value">150</div>
            </div>
            <div class="kpi-card kpi-ratio">
                <div class="kpi-title">가용 재고율 (%)</div>
                <div class="kpi-value">97.2</div>
            </div>
        </div>

        <div class="analysis-card">
            <div class="card-header">📍 창고 위치별 재고 분포 현황</div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>창고 위치 $\text{Code}$</th>
                        <th>담당 제품 (최다)</th>
                        <th>보관 수량 ($\text{EA}$)</th>
                        <th>$\text{Q/C}$ $\text{Pass}$ 비율</th>
                        <th>최대 $\text{Lot}$ $\text{No.}$</th>
                    </tr>
                </thead>
                <tbody id="location-inventory">
                    <tr>
                        <td><span class="location-code">W1-A-05</span></td>
                        <td>IoT Sensor Hub</td>
                        <td>2,000</td>
                        <td>99%</td>
                        <td>L-251109</td>
                    </tr>
                    <tr>
                        <td><span class="location-code">W2-B-12</span></td>
                        <td>Actuator Kit</td>
                        <td>1,500</td>
                        <td>95%</td>
                        <td>L-251101</td>
                    </tr>
                    <tr>
                        <td><span class="location-code">W3-C-01</span></td>
                        <td>C200 Component</td>
                        <td>1,000</td>
                        <td>90%</td>
                        <td>L-251015</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="analysis-card">
            <div class="card-header">📜 상세 $\text{Lot}$ 단위 입고 재고 목록</div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>$\text{Lot}$ $\text{No.}$</th>
                        <th>제품 $\text{Code}$</th>
                        <th>제품명</th>
                        <th>입고 수량</th>
                        <th>현재 재고</th>
                        <th>창고 위치</th>
                        <th>입고 일시</th>
                        <th>품질 결과</th>
                    </tr>
                </thead>
                <tbody id="detailed-inventory">
                    <tr>
                        <td>L-251109</td>
                        <td>PRD-A102</td>
                        <td>IoT Sensor Hub</td>
                        <td>495</td>
                        <td>450</td>
                        <td>W1-A-05</td>
                        <td>2025.11.11</td>
                        <td><span class="status-badge quality-pass">PASS</span></td>
                    </tr>
                    <tr>
                        <td>L-251101</td>
                        <td>PRD-B300</td>
                        <td>Actuator Kit</td>
                        <td>100</td>
                        <td>100</td>
                        <td>W2-B-12</td>
                        <td>2025.11.01</td>
                        <td><span class="status-badge quality-fail">FAIL</span></td>
                    </tr>
                    <tr>
                        <td>L-251025</td>
                        <td>PRD-A102</td>
                        <td>IoT Sensor Hub</td>
                        <td>500</td>
                        <td>500</td>
                        <td>W1-A-05</td>
                        <td>2025.10.25</td>
                        <td><span class="status-badge quality-pass">PASS</span></td>
                    </tr>
                    <tr>
                        <td>L-251015</td>
                        <td>PRD-C200</td>
                        <td>C200 Component</td>
                        <td>150</td>
                        <td>100</td>
                        <td>W3-C-01</td>
                        <td>2025.10.15</td>
                        <td><span class="status-badge quality-hold">HOLD</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>