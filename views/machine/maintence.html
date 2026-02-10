<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>유지보수 관리 (Work Order)</title>
    <style>
        /* CSS variables and basic styling remain the same for consistency */
        :root {
            --primary-color: #00bcd4;
            --secondary-color: #673ab7;
            --background: #f8f9fa;
            --card-bg: white;
            --main-font: #343a40;
            --border-color: #dee2e6;
            --status-high: #f44336;       /* Critical/High Priority */
            --status-medium: #ff9800;     /* Warning/Medium Priority */
            --status-low: #4caf50;        /* Normal/Low Priority */
            --status-pending: #adb5bd;    /* 미처리 */
            --status-in-progress: #673ab7;/* 진행 중 */
            --status-completed: #4caf50;  /* 완료 */
            --status-on-hold: #ffc107;    /* 부품 대기 */
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
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
            cursor: pointer;
        }
        
        /* Priority and Status Badges */
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 12px;
            color: white;
            cursor: pointer;
        }
        .priority-high { background-color: var(--status-high); }
        .priority-medium { background-color: var(--status-medium); }
        .priority-low { background-color: var(--status-low); }

        .status-badge { 
            cursor: default; /* W/O status usually changed in detail view */
            background-color: var(--status-in-progress); 
        }
        .status-on-hold { background-color: var(--status-on-hold); color: #343a40; }
        .status-pending { background-color: var(--status-pending); }
        .status-completed { background-color: var(--status-completed); }

        /* Button */
        .btn-create {
            background-color: var(--primary-color);
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-create:hover {
            background-color: #00a0b3;
        }

    </style>
</head>
<body>

    <div class="main-container">
        <div class="page-title">🛠️ 설비 유지보수 관리 ($\text{Work Order}$ $\text{System}$)</div>

        <div class="analysis-card">
            <div class="card-header">
                <span>보전 작업 지시서 ($\text{W/O}$) 목록</span>
                <button class="btn-create" onclick="openWorkOrderForm()">+ 새 작업 지시서 등록</button>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>$\text{W/O}$ $\text{ID}$</th>
                        <th>설비/부품</th>
                        <th>$\text{AI}$ 알람 $\text{ID}$</th>
                        <th>우선순위</th>
                        <th>작업 상태</th>
                        <th>계획 완료일</th>
                        <th>배정 인력</th>
                    </tr>
                </thead>
                <tbody id="work-order-list">
                    <tr onclick="showWorkOrderDetails('WO-20251110-001')">
                        <td><span style="color: var(--secondary-color);">WO-20251110-001</span></td>
                        <td>E102 / 모터 Bearing</td>
                        <td>ALM-1025</td>
                        <td><span class="badge priority-high">HIGH</span></td>
                        <td><span class="badge status-on-hold">부품 대기</span></td>
                        <td>2025.11.20</td>
                        <td>이보전</td>
                    </tr>
                    <tr onclick="showWorkOrderDetails('WO-20251105-002')">
                        <td><span style="color: var(--secondary-color);">WO-20251105-002</span></td>
                        <td>E201 / Spindle Coil</td>
                        <td>ALM-1020</td>
                        <td><span class="badge priority-medium">MEDIUM</span></td>
                        <td><span class="badge status-in-progress">진행 중</span></td>
                        <td>2025.11.15</td>
                        <td>김보전</td>
                    </tr>
                    <tr onclick="showWorkOrderDetails('WO-20251020-003')">
                        <td><span style="color: var(--secondary-color);">WO-20251020-003</span></td>
                        <td>E201 / Motor Bearing</td>
                        <td>ALM-1015</td>
                        <td><span class="badge priority-low">LOW</span></td>
                        <td><span class="badge status-completed">완료</span></td>
                        <td>2025.10.28</td>
                        <td>김보전</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="work-order-detail-view" class="analysis-card" style="display:none;">
            <div class="card-header">
                <span>작업 지시서 상세 정보 (<span id="detail-wo-id"></span>)</span>
            </div>
            <p><strong>작업명:</strong> <span id="detail-task-name"></span></p>
            <p><strong>작업 유형:</strong> <span style="color: var(--primary-color);">예지보전 ($\text{Predictive}$ $\text{Maintenance}$)</span></p>
            <p><strong>배정 인력:</strong> <input type="text" id="detail-assignee" value="이보전"></p>
            <p><strong>작업 상태 변경:</strong>
                <select id="detail-status-select" onchange="updateWOStatus()">
                    <option value="pending">미처리</option>
                    <option value="in-progress">진행 중</option>
                    <option value="on-hold">부품 대기</option>
                    <option value="completed">완료</option>
                </select>
            </p>
            <p><strong>총 비용:</strong> <span id="detail-total-cost"></span></p>
            
            <p style="margin-top: 15px;"><button onclick="saveWorkOrderDetails()">저장 및 상태 업데이트</button></p>
        </div>
    </div>

    <script>
        // Mock data for W/O details (in a real system, this would come from a server API)
        const WORK_ORDERS = {
            'WO-20251110-001': {
                id: 'WO-20251110-001', taskName: 'E102 모터 Bearing 교체', cost: '270,000원', status: 'on-hold'
            },
            'WO-20251105-002': {
                id: 'WO-20251105-002', taskName: 'E201 Spindle Coil 재배선', cost: '150,000원', status: 'in-progress'
            },
            'WO-20251020-003': {
                id: 'WO-20251020-003', taskName: 'E201 Bearing 교체', cost: '220,000원', status: 'completed'
            }
        };

        /**
         * 새 작업 지시서 등록 폼을 엽니다. (간단한 alert로 대체)
         */
        function openWorkOrderForm() {
            alert("새 작업 지시서 등록 폼이 열립니다.\n(AI 알람을 통해 자동 등록이 권장됩니다.)");
        }

        /**
         * W/O 목록 클릭 시 상세 정보를 표시합니다.
         * @param {string} woId - 클릭된 Work Order ID
         */
        function showWorkOrderDetails(woId) {
            const detailCard = document.getElementById('work-order-detail-view');
            const data = WORK_ORDERS[woId];

            if (data) {
                document.getElementById('detail-wo-id').textContent = data.id;
                document.getElementById('detail-task-name').textContent = data.taskName;
                document.getElementById('detail-total-cost').textContent = data.cost;
                document.getElementById('detail-status-select').value = data.status;
                
                detailCard.style.display = 'block';
            }
        }
        
        /**
         * 상세 보기에서 상태 변경을 저장합니다.
         */
        function updateWOStatus() {
             const selectedStatus = document.getElementById('detail-status-select').value;
             const woId = document.getElementById('detail-wo-id').textContent;
             
             // 목록 테이블 업데이트 (간단한 JS DOM 조작으로 시뮬레이션)
             const row = document.querySelector(`#work-order-list tr[onclick*="${woId}"]`);
             if (row) {
                 const statusCell = row.querySelector('.badge:not(.priority-high):not(.priority-medium):not(.priority-low)');
                 let statusText = '';
                 let statusClass = 'status-in-progress';
                 
                 switch(selectedStatus) {
                     case 'pending': statusText = '미처리'; statusClass = 'status-pending'; break;
                     case 'in-progress': statusText = '진행 중'; statusClass = 'status-in-progress'; break;
                     case 'on-hold': statusText = '부품 대기'; statusClass = 'status-on-hold'; break;
                     case 'completed': statusText = '완료'; statusClass = 'status-completed'; break;
                 }
                 
                 statusCell.textContent = statusText;
                 statusCell.className = `badge ${statusClass}`;
             }
        }

        function saveWorkOrderDetails() {
            const woId = document.getElementById('detail-wo-id').textContent;
            updateWOStatus(); // 상태 변경을 먼저 반영
            alert(`W/O ID ${woId}의 상세 정보 및 상태가 저장되었습니다.`);
            document.getElementById('work-order-detail-view').style.display = 'none'; // 폼 닫기
        }

        // 초기 로드 시 상세 페이지는 숨김
        window.onload = function() {
            document.getElementById('work-order-detail-view').style.display = 'none';
        };

    </script>
</body>
</html>