<?php
date_default_timezone_set('Asia/Seoul');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>작업지시</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Malgun Gothic', '맑은 고딕', sans-serif;
            background-color: #f5f5f5;
            padding: 0;
            margin: 0;
        }
        
        .container {
            width: 100%;
            margin: 0;
            background: white;
            padding: 20px;
            min-height: 100vh;
            box-sizing: border-box;
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-left: 20px;
        }
        
        
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
        
        .btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
        .btn-success {
            background-color: #28a745;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .btn-danger {
            background-color: #dc3545;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .error {
            padding: 15px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .work-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .work-table th,
        .work-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        
        .work-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            position: sticky;
            top: 0;
        }
        
        .work-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .work-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .work-table td {
            vertical-align: middle;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
        
        .status-completed {
            background-color: #28a745;
            color: white;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #888;
        }

        .toolbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                /* 패딩, 배경 제거 */
                padding: 0;
                background: none;
                border-radius: 4px;
            }
            .toolbar input[type="date"] {
                height: 40px;
                font-size: 16px;
                padding: 0 16px;
                border: 1px solid #ccc;
                border-radius: 4px;
                margin-right: 10px;
                box-sizing: border-box;
            }
            .toolbar .btn {
                height: 40px;
                font-size: 16px;
                padding: 0 20px;
                box-sizing: border-box;
            }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="toolbar">
            <h1>작업지시</h1>
            <div>
                <input type="date" id="date" value="<?php echo date('Y-m-d'); ?>">
                <button class="btn" onclick="loadWorkOrders()">작업지시 불러오기</button>
                <div id="status"></div>
            </div>
        </div>
        
        <div id="error-message" style="display: none;"></div>
        
        <div id="loading" class="loading" style="display: none;">
            데이터를 불러오는 중...
        </div>
        
        <div id="work-table-container">
            <table class="work-table" id="work-table" style="display: none;">
                <colgroup>
                    <col width="50px">
                    <col width="100px">
                    <col width="100px">
                    <col width="120px">
                    <col width="120px">
                    <col width="100px">
                    <col width="100px">
                    <col >
                </colgroup>
                <thead>
                    <tr>
                        <th style="text-align: center;">작업지시일</th>
                        <th style="text-align: center;">품목</th>
                        <th style="text-align: center;">규격</th>
                        <th style="text-align: center;">작업지시수량</th>
                        <th style="text-align: center;">작업완료수량</th>
                        <th style="text-align: center;">잔여수량</th>
                        <th style="text-align: center;">작업완료</th>
                    </tr>
                </thead>
                <tbody id="work-table-body">
                </tbody>
            </table>
            
            <div id="no-data" class="no-data" style="display: none;">
                작업지시가 없습니다.
            </div>
        </div>
    </div>
    
    <script>
        // 페이지 로드 시 작업지시 목록 불러오기
        window.addEventListener('DOMContentLoaded', function() {
            loadWorkOrders();
        });
        
        // 작업지시 목록 불러오기
        async function loadWorkOrders() {
            const loading = document.getElementById('loading');
            const errorMessage = document.getElementById('error-message');
            const workTable = document.getElementById('work-table');
            const workTableBody = document.getElementById('work-table-body');
            const noData = document.getElementById('no-data');
            
            // UI 초기화
            loading.style.display = 'block';
            errorMessage.style.display = 'none';
            workTable.style.display = 'none';
            noData.style.display = 'none';
            workTableBody.innerHTML = '';
            
            // FormData 생성
            const formData = new FormData();
            formData.append('controller', 'mes');
            formData.append('mode', 'getTodayWorkOrderList');
            formData.append('today', document.getElementById('date').value);
            
            try {
                // API 호출
                const response = await fetch('./handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                loading.style.display = 'none';
                
                
                if (data.data.length > 0) {
                    displayWorkOrders(data.data);
                    workTable.style.display = 'table';
                } else {
                    noData.style.display = 'block';
                }
            } catch (error) {
                loading.style.display = 'none';
                showError('서버와의 통신 중 오류가 발생했습니다: ' + error.message);
            }
        }
        
        // 작업지시 목록 표시
        function displayWorkOrders(orders) {
            const workTableBody = document.getElementById('work-table-body');
            workTableBody.innerHTML = '';
            
            orders.forEach((order, index) => {
                const row = document.createElement('tr');
                
                // 진행률 계산
                const progress = order.work_qty > 0 && order.order_qty > 0 
                    ? Math.round((order.work_qty / order.order_qty) * 100) 
                    : 0;
                
                // 상태 결정
                const isCompleted = order.work_qty >= order.order_qty;
                const statusClass = isCompleted ? 'status-completed' : 'status-pending';
                const statusText = isCompleted ? '완료' : '진행중';
                
                row.innerHTML = `
                    <td style="text-align: center;">${escapeHtml(order.order_date || '')}</td>
                    <td style="text-align: center;">${escapeHtml(order.item_name || '')}</td>
                    <td style="text-align: center;">${escapeHtml(order.standard || '')}</td>
                    <td style="text-align: right;">${formatNumber(order.order_qty || 0)}</td>
                    <td style="text-align: right;">${formatNumber(order.work_qty || 0)}</td>
                    <td style="text-align: right;">${formatNumber(order.order_qty - order.work_qty || 0)}</td>
                    <td style="text-align: center;">
                        <button class="btn btn-success" 
                                onclick="completeWork(${order.uid})" style="padding: 6px 12px; font-size: 12px;">작업완료 하기
                        </button>
                        <button class="btn btn-danger" 
                                onclick="partialCompleteWork(${order.uid})" 
                                style="padding: 6px 12px; font-size: 12px;">
                            부분작업완료 하기
                        </button>
                    </td>
                `;
                
                workTableBody.appendChild(row);
            });
        }
        
        // 작업 완료 처리
        async function completeWork(uid) {
            if (!confirm('작업을 완료 처리하시겠습니까?')) {
                return;
            }
            
            // FormData 생성
            const formData = new FormData();
            formData.append('controller', 'mes');
            formData.append('mode', 'completeWorkOrder');
            formData.append('uid', uid);
            
            try {
                const response = await fetch('./handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.result === 'success') {
                    alert('작업이 완료 처리되었습니다.');
                    loadWorkOrders(); // 목록 새로고침
                } else {
                    alert('작업 완료 처리에 실패했습니다: ' + (data.message || '알 수 없는 오류'));
                }
            } catch (error) {
                alert('서버와의 통신 중 오류가 발생했습니다: ' + error.message);
            }
        }


        // 마우스 클릭만으로 수량을 입력받는 커스텀 모달(숫자패드) 구현
        function showNumpadModal(onSubmit) {
            // 만약 이미 모달이 열려 있다면 제거
            let oldModal = document.getElementById('numpad-modal');
            if (oldModal) oldModal.remove();

            const modal = document.createElement('div');
            modal.id = 'numpad-modal';
            Object.assign(modal.style, {
                position: 'fixed',
                left: 0,
                top: 0,
                width: '100vw',
                height: '100vh',
                background: 'rgba(0,0,0,0.4)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                zIndex: 9999
            });

            // 모달 내용
            const modalContent = document.createElement('div');
            Object.assign(modalContent.style, {
                background: 'white',
                borderRadius: '12px',
                padding: '28px 22px',
                minWidth: '320px',
                textAlign: 'center',
                boxShadow: '0 4px 32px rgba(0,0,0,.2)',
            });

            // 수 입력 필드 및 지시
            const input = document.createElement('div');
            input.style.fontSize = '32px';
            input.style.letterSpacing = '3px';
            input.style.marginBottom = '18px';
            input.style.height = '44px';
            input.style.fontWeight = 'bold';
            input.style.userSelect = 'none';
            input.textContent = '';

            const instr = document.createElement('div');
            instr.textContent = '부분작업 완료 수량 입력';
            instr.style.marginBottom = '12px';
            instr.style.fontSize = '16px';

            // 숫자패드 버튼 생성
            const numpad = document.createElement('div');
            numpad.style.display = 'grid';
            numpad.style.gridTemplateColumns = 'repeat(3, 64px)';
            numpad.style.gap = '12px';
            numpad.style.justifyContent = 'center';
            numpad.style.marginBottom = '16px';

            const buttons = [
                '1','2','3','4','5','6','7','8','9','0','←','지우기'
            ];
            buttons.forEach(label => {
                const btn = document.createElement('button');
                btn.textContent = label;
                btn.style.fontSize = '22px';
                btn.style.height = '52px';
                btn.style.borderRadius = '8px';
                btn.style.border = '1px solid #ddd';
                btn.style.background =
                    (label === '←' || label === '지우기') ? '#eee' : '#f8f9fa';
                btn.style.cursor = 'pointer';
                btn.style.transition = 'background 0.2s';
                btn.addEventListener('mousedown', () => btn.style.background = '#e2e6ea');
                btn.addEventListener('mouseup', () => btn.style.background = (label === '←' || label === '지우기') ? '#eee' : '#f8f9fa');
                btn.addEventListener('mouseleave', () => btn.style.background = (label === '←' || label === '지우기') ? '#eee' : '#f8f9fa');
                btn.addEventListener('click', () => {
                    if (label === '지우기') {
                        input.textContent = '';
                    } else if (label === '←') {
                        input.textContent = input.textContent.slice(0, -1);
                    } else {
                        if (input.textContent.length < 7) { // 9999999 제한
                            if (!(label === '0' && input.textContent === '')) // 첫숫자 '0' 막기
                                input.textContent += label;
                        }
                    }
                });
                numpad.appendChild(btn);
            });

            // 하단 실행/취소 버튼
            const actionDiv = document.createElement('div');
            actionDiv.style.marginTop = '10px';

            const confirm = document.createElement('button');
            confirm.textContent = '확인';
            confirm.className = 'btn btn-success';
            confirm.style.marginRight = '16px';
            confirm.style.padding = '10px 22px';

            confirm.onclick = () => {
                let val = parseInt(input.textContent, 10);
                if (isNaN(val) || val <= 0) {
                    alert('수량을 입력하세요.');
                } else {
                    document.body.removeChild(modal);
                    if (onSubmit) onSubmit(val);
                }
            };

            const cancel = document.createElement('button');
            cancel.textContent = '취소';
            cancel.className = 'btn btn-danger';
            cancel.onclick = () => {
                document.body.removeChild(modal);
            };

            actionDiv.appendChild(confirm);
            actionDiv.appendChild(cancel);

            modalContent.appendChild(instr);
            modalContent.appendChild(input);
            modalContent.appendChild(numpad);
            modalContent.appendChild(actionDiv);
            modal.appendChild(modalContent);

            document.body.appendChild(modal);
        }

        async function partialCompleteWork(uid) {
            // 부분작업완료 마우스입력용 모달 호출
            showNumpadModal(async function(qty) {
                // 서버로 전송
                const formData = new FormData();
                formData.append('controller', 'mes');
                formData.append('mode', 'partialCompleteWorkOrder');
                formData.append('uid', uid);
                formData.append('qty', qty);

                try {
                    const response = await fetch('./handler.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.result === 'success') {
                        alert('부분작업이 완료 처리되었습니다.');
                        loadWorkOrders(); // 목록 새로고침
                    } else {
                        alert('부분작업 완료 처리에 실패했습니다: ' + (data.message || '알 수 없는 오류'));
                    }
                } catch (error) {
                    alert('서버와의 통신 중 오류가 발생했습니다: ' + error.message);
                }
            });
        }

        // 오류 메시지 표시
        function showError(message) {
            const errorMessage = document.getElementById('error-message');
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
        }
        
        // HTML 이스케이프
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // 숫자 포맷팅
        function formatNumber(num) {
            return new Intl.NumberFormat('ko-KR').format(num);
        }
    </script>
</body>
</html>
