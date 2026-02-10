<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>물류 창고 이동 관리</title>
    <style>
        /* Global CSS Variables (재사용) */
        :root {
            --primary-color: #00bcd4;     
            --secondary-color: #673ab7;   
            --background: #f8f9fa;
            --card-bg: white;
            --main-font: #343a40;
            --border-color: #dee2e6;
            --status-success: #4caf50;
            --status-critical: #f44336;
            --status-warning: #ff9800;    /* 이동 중 */
            --status-info: #2196f3;       /* 요청 대기 */
        }

        body {
            font-family: 'Malgun Gothic', 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background);
            color: var(--main-font);
        }
        
        /* --- MAIN CONTAINER & CONTENT STYLES --- */
        .main-container {
            padding: 30px;
            max-width: 1600px; 
            margin: 0 auto;
            background-color: var(--background);
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Table and Badge Styles */
        .data-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .data-table th, .data-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border-color); }
        .data-table th { background-color: #f0f4f7; color: var(--secondary-color); font-weight: 600; }
        .data-table tr:hover { background-color: #f5f5f5; cursor: pointer; }
        
        .btn { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; }
        .btn-primary { background-color: var(--primary-color); color: white; }
        .btn-submit { background-color: var(--secondary-color); color: white; margin-top: 20px; }

        .status-badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-weight: 700; font-size: 11px; color: white; }
        .status-request { background-color: var(--status-info); }
        .status-moving { background-color: var(--status-warning); }
        .status-complete { background-color: var(--status-success); }
        .location-badge { background-color: var(--secondary-color); margin-right: 5px; }

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
            background-color: rgba(0, 0, 0, 0.4); 
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; 
            padding: 30px;
            border-radius: 8px;
            width: 600px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }
        .modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; color: var(--secondary-color); }
        .form-group input, .form-group select, .form-group textarea { 
            width: calc(100% - 20px); 
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            box-sizing: border-box; 
        }
    </style>
</head>
<body>

    <div class="main-container">

        <div id="transfer-page" style="display: block;">
            <div class="page-title">🚚 창고 이동 요청 관리 (Inventory Transfer Management)</div>
            
            <div class="analysis-card">
                <div class="card-header">
                    <span>이동 요청 목록</span>
                    <button class="btn btn-primary" onclick="openTransferModal('New')">+ 신규 이동 요청</button>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>요청 $\text{ID}$</th>
                            <th>품목 $\text{Code}$</th>
                            <th>출발 창고</th>
                            <th>도착 창고</th>
                            <th>요청 수량</th>
                            <th>요청일</th>
                            <th>상태</th>
                            <th>처리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr onclick="openTransferModal('TRF-2025001')">
                            <td>**TRF-2025001**</td>
                            <td>$\text{PROD-A01}$</td>
                            <td><span class="location-badge">본사-창고1</span></td>
                            <td><span class="location-badge">협력사-HUB</span></td>
                            <td>$1,000$ $\text{EA}$</td>
                            <td>$2025.11.05$</td>
                            <td><span class="status-badge status-moving">이동 중</span></td>
                            <td><button class="btn btn-primary btn-sm">진행</button></td>
                        </tr>
                        <tr onclick="openTransferModal('TRF-2025002')">
                            <td>**TRF-2025002**</td>
                            <td>$\text{MAT-B03}$</td>
                            <td><span class="location-badge">협력사-HUB</span></td>
                            <td><span class="location-badge">본사-창고1</span></td>
                            <td>$20$ $\text{BOX}$</td>
                            <td>$2025.11.10$</td>
                            <td><span class="status-badge status-request">요청 대기</span></td>
                            <td><button class="btn btn-primary btn-sm">승인</button></td>
                        </tr>
                        <tr onclick="openTransferModal('TRF-2025003')">
                            <td>**TRF-2025003**</td>
                            <td>$\text{PROD-C99}$</td>
                            <td><span class="location-badge">본사-창고2</span></td>
                            <td><span class="location-badge">본사-창고1</span></td>
                            <td>$50$ $\text{EA}$</td>
                            <td>$2025.11.01$</td>
                            <td><span class="status-badge status-complete">완료</span></td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div> <div id="transfer-modal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" onclick="closeTransferModal()">&times;</span>
            <h2 id="transfer-modal-title">신규 창고 이동 요청 등록</h2>
            <form id="transfer-form">
                
                <div class="form-group">
                    <label for="source-warehouse">📍 출발 창고</label>
                    <select id="source-warehouse" required>
                        <option value="">선택</option>
                        <option value="WH1">본사-창고1</option>
                        <option value="WH2">본사-창고2</option>
                        <option value="HUB">협력사-HUB</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="target-warehouse">➡️ 도착 창고</label>
                    <select id="target-warehouse" required>
                        <option value="">선택</option>
                        <option value="WH1">본사-창고1</option>
                        <option value="WH2">본사-창고2</option>
                        <option value="HUB">협력사-HUB</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="item-code">📦 품목 코드</label>
                    <input type="text" id="item-code" placeholder="예: PROD-A01" required>
                </div>

                <div class="form-group">
                    <label for="transfer-quantity">🔢 이동 수량</label>
                    <input type="number" id="transfer-quantity" placeholder="100" required min="1">
                </div>
                
                <div class="form-group">
                    <label for="request-date">요청일</label>
                    <input type="date" id="request-date" value="2025-11-11" required>
                </div>

                <button type="submit" class="btn btn-submit" id="transfer-submit-button">이동 요청 등록/수정</button>
            </form>
        </div>
    </div>


    <script>
        // MOCK 데이터 (조회 시 사용)
        const MOCK_TRANSFER_REQUESTS = {
            'TRF-2025001': { 
                id: 'TRF-2025001', 
                source: 'WH1', 
                target: 'HUB', 
                item: 'PROD-A01', 
                qty: 1000, 
                date: '2025-11-05', 
                status: '이동 중' 
            },
            'TRF-2025002': { 
                id: 'TRF-2025002', 
                source: 'HUB', 
                target: 'WH1', 
                item: 'MAT-B03', 
                qty: 20, 
                date: '2025-11-10', 
                status: '요청 대기' 
            }
        };

        // --- Transfer Modal Functions ---
        
        /** 창고 이동 요청/상세 팝업을 열고 정보를 채웁니다. */
        function openTransferModal(requestId) {
            const modal = document.getElementById('transfer-modal');
            const form = document.getElementById('transfer-form');
            form.reset();
            
            if (requestId === 'New') {
                document.getElementById('transfer-modal-title').textContent = '신규 창고 이동 요청 등록';
                document.getElementById('transfer-submit-button').textContent = '이동 요청 등록';
            } else {
                const data = MOCK_TRANSFER_REQUESTS[requestId];
                document.getElementById('transfer-modal-title').textContent = `이동 요청 상세/수정 (${requestId})`;
                document.getElementById('source-warehouse').value = data.source;
                document.getElementById('target-warehouse').value = data.target;
                document.getElementById('item-code').value = data.item;
                document.getElementById('transfer-quantity').value = data.qty;
                document.getElementById('request-date').value = data.date;
                document.getElementById('transfer-submit-button').textContent = '이동 요청 수정';
            }

            modal.style.display = 'block';
        }

        /** 창고 이동 팝업을 닫습니다. */
        function closeTransferModal() {
            document.getElementById('transfer-modal').style.display = 'none';
        }

        // --- Form Submission Simulation ---
        document.getElementById('transfer-form').addEventListener('submit', (e) => {
            e.preventDefault(); 
            
            const source = document.getElementById('source-warehouse').value;
            const target = document.getElementById('target-warehouse').value;
            const item = document.getElementById('item-code').value;

            if (source === target) {
                alert('🚨 출발 창고와 도착 창고는 같을 수 없습니다.');
                return;
            }

            alert(`🎉 [${item}] 품목 ${document.getElementById('transfer-quantity').value}개를 ${source}에서 ${target}로 이동 요청했습니다.`); 
            closeTransferModal();
        });
        
        // --- Common Modal Close Logic (외부 클릭 시 닫기) ---
        window.onclick = function(event) {
            const modal = document.getElementById('transfer-modal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
            // (만약 다른 모달이 있다면 여기에 추가)
        }
        
    </script>
</body>
</html>