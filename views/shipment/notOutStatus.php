<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>제품 미출하 현황</title>
    <style>
        /* CSS Variables and General Layout (Consistent) */
        :root {
            --primary-color: #00bcd4;     
            --secondary-color: #673ab7;   
            --background: #f8f9fa;
            --card-bg: white;
            --main-font: #343a40;
            --border-color: #dee2e6;
            --status-critical: #f44336;      /* 납기 초과 */
            --status-warning: #ff9800;       /* 재고 부족 / D-Day */
            --status-info: #2196f3;          /* Total / Default */
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
            color: var(--status-critical); /* 미출하 리스크 강조 */
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
        .kpi-ddays { background-color: var(--status-warning); }
        .kpi-overdue { background-color: var(--status-critical); }
        .kpi-shortage { background-color: var(--primary-color); }
        
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

        /* Overdue Row Highlighting */
        .row-overdue {
            background-color: #fcebeb; /* Very light red */
            font-weight: 600;
        }
        .row-overdue td {
            color: var(--status-critical);
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: 700;
            font-size: 11px;
            color: white;
        }
        .reason-shortage { background-color: var(--status-critical); }
        .reason-qc { background-color: var(--status-warning); }
        .reason-delay { background-color: var(--status-info); }
        .avail-short { color: var(--status-critical); font-weight: 700; }
        .avail-available { color: var(--primary-color); font-weight: 700; }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="page-title">🚨 제품 미출하 현황 (납기 리스크 관리)</div>

        <div class="kpi-grid">
            <div class="kpi-card kpi-overdue">
                <div class="kpi-title">납기 초과 S/O (건)</div>
                <div class="kpi-value">2</div>
            </div>
            <div class="kpi-card kpi-ddays">
                <div class="kpi-title">금일 납기 D-DAY (건)</div>
                <div class="kpi-value">1</div>
            </div>
            <div class="kpi-card kpi-shortage">
                <div class="kpi-title">재고 부족 미출하 (건)</div>
                <div class="kpi-value">10</div>
            </div>
            <div class="kpi-card kpi-total">
                <div class="kpi-title">미출하 총 수량 (EA)</div>
                <div class="kpi-value">2,500</div>
            </div>
        </div>

        <div class="analysis-card">
            <div class="card-header">📊 미출하 주 원인별 현황 (조치 우선순위)</div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>미출하 주 원인</th>
                        <th>주문 건수</th>
                        <th>총 수량 ($\text{EA}$)</th>
                        <th>긴급 조치 필요도</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>재고 부족 ($\text{Shortage}$)</td>
                        <td>10 건</td>
                        <td>1,500 $\text{EA}$</td>
                        <td style="color: var(--status-critical); font-weight: 700;">HIGH</td>
                    </tr>
                    <tr>
                        <td>$\text{Q/C}$ 대기/불합격</td>
                        <td>5 건</td>
                        <td>500 $\text{EA}$</td>
                        <td style="color: var(--status-warning); font-weight: 700;">MEDIUM</td>
                    </tr>
                    <tr>
                        <td>운송업체 배정 지연</td>
                        <td>3 건</td>
                        <td>300 $\text{EA}$</td>
                        <td style="color: var(--status-info);">LOW</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="analysis-card">
            <div class="card-header">📜 상세 미출하 주문 목록 (납기일 기준 정렬)</div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>주문 $\text{ID}$</th>
                        <th>고객명</th>
                        <th>제품명 / 수량</th>
                        <th>배송 요청일</th>
                        <th>미출하 원인</th>
                        <th>재고 가용성</th>
                        <th>경과/잔여일</th>
                        <th>조치</th>
                    </tr>
                </thead>
                <tbody id="unshipped-list">
                    <tr class="row-overdue">
                        <td>SO-B01</td>
                        <td>대성 M</td>
                        <td>PRD-A102 / 500 $\text{EA}$</td>
                        <td>2025.11.08</td>
                        <td><span class="badge reason-shortage">재고 부족</span></td>
                        <td><span class="avail-short">Short (200 $\text{EA}$ 필요)</span></td>
                        <td>D+3 (지연)</td>
                        <td><button class="btn" style="background-color: var(--status-critical); color: white;">긴급 조치</button></td>
                    </tr>
                    <tr>
                        <td>SO-B02</td>
                        <td>신기술 K</td>
                        <td>PRD-B300 / 100 $\text{EA}$</td>
                        <td>2025.11.11</td>
                        <td><span class="badge reason-qc">Q/C 대기</span></td>
                        <td><span class="avail-available">Available</span></td>
                        <td>D-Day</td>
                        <td><button class="btn" style="background-color: var(--status-warning); color: white;">Q/C 독촉</button></td>
                    </tr>
                    <tr>
                        <td>SO-B03</td>
                        <td>국제 T</td>
                        <td>PRD-C200 / 50 $\text{EA}$</td>
                        <td>2025.11.15</td>
                        <td><span class="badge reason-delay">운송 지연</span></td>
                        <td><span class="avail-available">Available</span></td>
                        <td>D-4</td>
                        <td><button class="btn" style="background-color: var(--primary-color); color: white;">재할당</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>