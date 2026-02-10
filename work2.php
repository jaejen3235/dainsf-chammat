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
            .action-btn {
                min-width: 120px;
                height: 36px;
                padding: 6px 12px;
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
                <select id="item-select" style="height:40px; padding:0 12px; border-radius:4px; border:1px solid #ccc; margin-left:8px;">
                    <option value="">전체 품목</option>
                </select>
                <button class="btn" onclick="loadWorkOrders()">작업지시 불러오기</button>
                <label style="margin-left:8px;">페이지당
                    <select id="page-size" onchange="changePageSize()" style="height:40px; margin-left:6px;">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </label>
                <div id="status"></div>
            </div>
        </div>
        
        <div id="totals" style="margin-top:12px; text-align:right; color:#333; font-weight:600; display:none;"></div>
        
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

            <div id="pagination" style="margin-top:12px; display:none; text-align:right;">
                <button class="btn" id="prev-page" onclick="prevPage()">Prev</button>
                <span id="page-numbers" style="margin:0 8px;"></span>
                <button class="btn" id="next-page" onclick="nextPage()">Next</button>
            </div>
        </div>
    </div>
    
    <script>
        // 페이지 로드 시 작업지시 목록 불러오기
        window.addEventListener('DOMContentLoaded', async function() {
            await loadItems();
            loadWorkOrders();
        });

        // 품목 목록을 서버에서 불러와 select에 채우기
        async function loadItems() {
            try {
                const fd = new FormData();
                fd.append('controller', 'mes');
                fd.append('mode', 'getAllItemList');

                const response = await fetch('./handler.php', { method: 'POST', body: fd });
                const data = await response.json();
                const select = document.getElementById('item-select');
                if (select && Array.isArray(data)) {
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.uid;
                        opt.textContent = item.item_name + (item.standard ? ' (' + item.standard + ')' : '');
                        select.appendChild(opt);
                    });

                    // 선택이 변경되면 바로 작업지시 목록을 불러오도록 처리
                    select.addEventListener('change', function() {
                        currentPage = 1;
                        loadWorkOrders();
                    });
                }
            } catch (e) {
                console.error('아이템 로드 실패', e);
            }
        }

        // 클라이언트 사이드 페이징/필터 상태
        let allOrders = [];
        let filteredOrders = [];
        let currentPage = 1;
        let pageSize = 10;
        let totalPages = 1;
        
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
            const itemSelect = document.getElementById('item-select');
            if (itemSelect && itemSelect.value) {
                formData.append('item_uid', itemSelect.value);
            }
            
            try {
                // API 호출
                const response = await fetch('./handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                loading.style.display = 'none';
                
                allOrders = data.data || [];
                filteredOrders = allOrders.slice();
                // 페이지 사이즈를 selector에서 읽어 초기 로드에도 페이징을 적용하도록 함
                const pageSizeSelect = document.getElementById('page-size');
                pageSize = pageSizeSelect ? (parseInt(pageSizeSelect.value, 10) || 10) : 10;
                currentPage = 1;
                renderPage();
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
                        <button class="btn btn-success action-btn" onclick="completeWork(${order.uid})">전체수량</button>
                        <button class="btn btn-danger action-btn" onclick="partialCompleteWork(${order.uid})">일부수량</button>
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

        // 필터 적용 (서버에 선택된 item_uid로 요청하여 결과를 갱신)
        function applyFilter() {
            currentPage = 1;
            loadWorkOrders();
        }

        function changePageSize() {
            const val = parseInt(document.getElementById('page-size').value, 10);
            pageSize = isNaN(val) ? 10 : val;
            currentPage = 1;
            renderPage();
        }

        function renderPage() {
                pageSize = pageSize || 10;
            totalPages = Math.max(1, Math.ceil(filteredOrders.length / pageSize));

            // totals: 전체(필터된) 합계
            const totalOrderQty = filteredOrders.reduce((s, o) => s + (parseInt(o.order_qty || 0) || 0), 0);
            const totalWorkQty = filteredOrders.reduce((s, o) => s + (parseInt(o.work_qty || 0) || 0), 0);
            const totalRemainQty = filteredOrders.reduce((s, o) => s + (((parseInt(o.order_qty || 0) || 0) - (parseInt(o.work_qty || 0) || 0))), 0);
            const totalAchieve = totalOrderQty ? ((totalWorkQty / totalOrderQty) * 100) : 0;

            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * pageSize;
            const end = start + pageSize;
            const pageOrders = filteredOrders.slice(start, end);
            displayWorkOrders(pageOrders);

            // 페이지 합계
            const pageOrderQty = pageOrders.reduce((s, o) => s + (parseInt(o.order_qty || 0) || 0), 0);
            const pageWorkQty = pageOrders.reduce((s, o) => s + (parseInt(o.work_qty || 0) || 0), 0);
            const pageRemainQty = pageOrders.reduce((s, o) => s + (((parseInt(o.order_qty || 0) || 0) - (parseInt(o.work_qty || 0) || 0))), 0);
            const pageAchieve = pageOrderQty ? ((pageWorkQty / pageOrderQty) * 100) : 0;

            const totalsEl = document.getElementById('totals');
            if (totalsEl) {
                totalsEl.style.display = 'block';
                totalsEl.innerHTML = `전체 합계 — 작업지시: ${formatNumber(totalOrderQty)} / 작업완료: ${formatNumber(totalWorkQty)} / 잔여: ${formatNumber(totalRemainQty)} &nbsp; 달성: ${totalAchieve.toFixed(1)} % &nbsp;&nbsp; (현재 페이지: 작업지시 ${formatNumber(pageOrderQty)} / 작업완료 ${formatNumber(pageWorkQty)} / 잔여 ${formatNumber(pageRemainQty)} &nbsp; 달성: ${pageAchieve.toFixed(1)} %)`;
            }

            // 빈 데이터인 경우 테이블/페이지네이션 표시 제어
            if (filteredOrders.length === 0) {
                document.getElementById('work-table').style.display = 'none';
                document.getElementById('no-data').style.display = 'block';
                document.getElementById('pagination').style.display = 'none';
                return;
            } else {
                document.getElementById('work-table').style.display = 'table';
                document.getElementById('no-data').style.display = 'none';
            }

            // pagination controls
            document.getElementById('pagination').style.display = totalPages > 1 ? 'block' : 'none';
            document.getElementById('prev-page').disabled = currentPage <= 1;
            document.getElementById('next-page').disabled = currentPage >= totalPages;

            const pageNumbers = document.getElementById('page-numbers');
            pageNumbers.innerHTML = '';
            const maxButtons = 7;
            let startPage = Math.max(1, currentPage - Math.floor(maxButtons/2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);
            if (endPage - startPage + 1 < maxButtons) {
                startPage = Math.max(1, endPage - maxButtons + 1);
            }
            for (let i = startPage; i <= endPage; i++) {
                const btn = document.createElement('button');
                btn.className = 'btn' + (i === currentPage ? ' btn-success' : '');
                btn.style.margin = '0 4px';
                btn.textContent = i;
                btn.onclick = (function(p){ return function(){ goToPage(p); }; })(i);
                pageNumbers.appendChild(btn);
            }
        }

        function goToPage(p) { currentPage = p; renderPage(); }
        function prevPage() { if (currentPage > 1) { currentPage--; renderPage(); } }
        function nextPage() { if (currentPage < totalPages) { currentPage++; renderPage(); } }

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