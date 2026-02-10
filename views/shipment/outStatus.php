<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>제품 출하 현황</title>
    <style>
        /* CSS Variables and General Layout (Consistent) */
        :root {
            --primary-color: #00bcd4;     
            --secondary-color: #673ab7;   
            --background: #f8f9fa;
            --card-bg: white;
            --main-font: #343a40;
            --border-color: #dee2e6;
            --status-success: #4caf50;    /* 배송 완료 / OTD */
            --status-shipping: #2196f3;   /* 배송 중 */
            --status-delay: #f44336;      /* 지연 */
            --status-info: #6c757d;       /* Total */
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
        .kpi-shipping { background-color: var(--status-shipping); }
        .kpi-delay { background-color: var(--status-delay); }
        .kpi-otd { background-color: var(--status-success); }
        
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
        }

        /* Status & Tracking Link */
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: 700;
            font-size: 11px;
            color: white;
        }
        .status-complete-color { background-color: var(--status-success); }
        .status-shipping-color { background-color: var(--status-shipping); }
        .status-delay-color { background-color: var(--status-delay); }

        .tracking-link {
            color: var(--primary-color);
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
        }
        
        /* --- Modal (Popup) Styles --- */
        .modal-overlay {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4); /* Dim background */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto; /* Centered position */
            padding: 30px;
            border-radius: 8px;
            width: 50%; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }
        .modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .modal-close:hover,
        .modal-close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
        .modal-detail-row {
            padding: 8px 0;
            border-bottom: 1px dashed #eee;
        }
        .modal-detail-row strong {
            display: inline-block;
            width: 120px;
            color: var(--secondary-color);
        }

    </style>
</head>
<body>

    <div class="main-container">
        <div class="page-title">🚚 제품 출하 현황 ($\text{Shipping}$ $\text{Status}$ $\text{Tracking}$)</div>

        <div class="kpi-grid">
            <div class="kpi-card kpi-total">
                <div class="kpi-title">금월 $\text{총}$ 출하 $\text{S/O}$ (건)</div>
                <div class="kpi-value">125</div>
            </div>
            <div class="kpi-card kpi-shipping">
                <div class="kpi-title">운송 중 주문 수 (건)</div>
                <div class="kpi-value">45</div>
            </div>
            <div class="kpi-card kpi-delay">
                <div class="kpi-title">배송 완료 지연 주문 (건)</div>
                <div class="kpi-value">3</div>
            </div>
            <div class="kpi-card kpi-otd">
                <div class="kpi-title">정시 납기율 ($\text{OTD}$ $\text{Rate}$)</div>
                <div class="kpi-value">95.5%</div>
            </div>
        </div>

        <div class="analysis-card">
            <div class="card-header">📜 출하 완료 주문 이력 및 운송 상태</div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>주문 $\text{ID}$</th>
                        <th>고객명</th>
                        <th>제품명 / 수량</th>
                        <th>출하 일시</th>
                        <th>운송 업체</th>
                        <th>운송 상태</th>
                        <th>운송장</th>
                    </tr>
                </thead>
                <tbody id="shipping-history-list">
                    <tr>
                        <td>SO-20251111-A01</td>
                        <td>ABC 전자</td>
                        <td>PRD-A102 / 400 $\text{EA}$</td>
                        <td>2025.11.11 16:00</td>
                        <td>CJ 대한통운</td>
                        <td><span class="status-badge status-shipping-color">배송 중</span></td>
                        <td><span class="tracking-link" onclick="openTrackingPopup('CJ', '123456789012', 'SO-20251111-A01')">1234-5678-9012 보기</span></td>
                    </tr>
                    <tr>
                        <td>SO-20251110-A02</td>
                        <td>미래테크</td>
                        <td>PRD-B300 / 200 $\text{EA}$</td>
                        <td>2025.11.10 10:00</td>
                        <td>경동택배</td>
                        <td><span class="status-badge status-complete-color">배송 완료</span></td>
                        <td><span class="tracking-link" onclick="openTrackingPopup('KD', '987654321098', 'SO-20251110-A02')">9876-5432-1098 보기</span></td>
                    </tr>
                    <tr>
                        <td>SO-20251109-A03</td>
                        <td>글로벌 T</td>
                        <td>PRD-A102 / 100 $\text{EA}$</td>
                        <td>2025.11.08 14:00</td>
                        <td>CJ 대한통운</td>
                        <td><span class="status-badge status-delay-color">지연 (납기일 초과)</span></td>
                        <td><span class="tracking-link" onclick="openTrackingPopup('CJ', '112233445566', 'SO-20251109-A03')">1122-3344-5566 보기</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="tracking-modal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" onclick="closeTrackingPopup()">&times;</span>
            <h2>운송장 상세 정보 및 배송 조회</h2>
            <div class="modal-detail-row">
                <strong>주문 ID:</strong> <span id="modal-order-id"></span>
            </div>
            <div class="modal-detail-row">
                <strong>운송 업체:</strong> <span id="modal-courier"></span>
            </div>
            <div class="modal-detail-row">
                <strong>운송장 번호:</strong> <span id="modal-tracking-no" style="font-weight: 700; color: var(--status-shipping);"></span>
            </div>
            
            <p style="margin-top: 20px; font-weight: 600;">외부 운송장 조회 링크</p>
            <a id="modal-tracking-link" href="#" target="_blank" style="color: var(--primary-color); text-decoration: none; font-size: 16px;">
                <button class="btn" style="background-color: var(--primary-color); color: white; padding: 10px;">🚚 외부 사이트에서 배송 조회하기</button>
            </a>
            
            <p style="margin-top: 20px; font-size: 13px; color: #666;">* 실제 배송 상태는 운송 업체 사이트에서 실시간으로 확인됩니다.</p>
        </div>
    </div>

    <script>
        const COURIER_URLS = {
            'CJ': 'https://www.cjlogistics.com/lgs/service/trace/D_view.jsp?item_id=',
            'KD': 'https://www.kdexp.com/basic/service_search_view.jsp?barcode='
            // 실제 API 또는 링크가 여기에 추가됩니다.
        };

        /** 운송장 팝업을 열고 정보를 채웁니다. */
        function openTrackingPopup(courierCode, trackingNo, orderId) {
            const modal = document.getElementById('tracking-modal');
            
            // 데이터 채우기
            document.getElementById('modal-order-id').textContent = orderId;
            document.getElementById('modal-courier').textContent = (courierCode === 'CJ' ? 'CJ 대한통운' : '경동택배');
            document.getElementById('modal-tracking-no').textContent = trackingNo;
            
            // 외부 조회 링크 설정 (시뮬레이션)
            const trackingLink = document.getElementById('modal-tracking-link');
            const baseUrl = COURIER_URLS[courierCode];
            
            if (baseUrl) {
                trackingLink.href = baseUrl + trackingNo.replace(/-/g, ''); // 하이픈 제거 후 링크 연결
                trackingLink.style.display = 'block';
            } else {
                trackingLink.href = '#';
                trackingLink.style.display = 'none';
                alert('해당 운송 업체에 대한 외부 조회 링크 정보가 없습니다.');
            }
            
            modal.style.display = 'block';
        }

        /** 운송장 팝업을 닫습니다. */
        function closeTrackingPopup() {
            document.getElementById('tracking-modal').style.display = 'none';
        }

        // 모달 영역 밖을 클릭하면 팝업 닫기
        window.onclick = function(event) {
            const modal = document.getElementById('tracking-modal');
            if (event.target == modal) {
                closeTrackingPopup();
            }
        }
    </script>
</body>
</html>