<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 재고 현황</div>
    <!--
    <div class="title red">재고 효율 및 정확성</div>
    <div class="kpi-grid"> 
        <div class="kpi-card" style="--main-color: #27ae60;">
            <h4>재고 회전율 (월간)</h4>
            <p class="kpi-value efficiency" id="kpi-turnover">8.5 회</p>
            <small>목표: 10회 | $\text{DSI}$: 43일</small>
        </div>
        <div class="kpi-card" style="--main-color: #2980b9;">
            <h4>재고 정확도 ($\text{Cycle Count}$)</h4>
            <p class="kpi-value" id="kpi-accuracy">98.2%</p>
            <small>전월 대비 <span style="color:#27ae60;">+0.5%</span> 상승</small>
        </div>
        <div class="kpi-card" style="--main-color: #e74c3c;">
            <h4>품절률 (최근 7일)</h4>
            <p class="kpi-value danger" id="kpi-shortage">1.5%</p>
            <small>품절 건수: 7건 | 매출 손실 추정</small>
        </div>
        <div class="kpi-card" style="--main-color: #f39c12;">
           <h4>재고 유지 비용 ($\text{Holding Cost}$)</h4>
           <p class="kpi-value" id="kpi-holding">$ 15,000</p>
           <small>총 재고 가치 대비 4.2%</small>
        </div>
    </div>
    

    <div class="main-content-grid">
        <div class="panel">
            <div class="title red">월별 재고 가치 추이 (원자재 vs. 완제품)</div>
            <canvas id="inventoryChart"></canvas> 
        </div>

        <div class="panel risk-item-panel">
            <div class="title red">재고 위험 품목 목록 (Top 5)</div>
            <ul class="risk-list" id="risk-list">
                <li>
                    <span>1. 원자재 A-202</span>
                    <span class="risk-tag tag-shortage">품절 임박 (D-2)</span>
                </li>
                <li>
                    <span>2. 완제품 Z-15</span>
                    <span class="risk-tag tag-overstock">과재고 (150%↑)</span>
                </li>
                <li>
                    <span>3. 부품 C-10</span>
                    <span class="risk-tag tag-shortage">안전재고 미달</span>
                </li>
                <li>
                    <span>4. 완제품 T-50</span>
                    <span class="risk-tag tag-slow">저회전 (90일)</span>
                </li>
                <li>
                    <span>5. 부품 F-55</span>
                    <span class="risk-tag tag-overstock">장기 보관</span>
                </li>
            </ul>
        </div>    
    </div>
    -->
    <div class='content-wrapper'>
        <div class='small-search-wrapper'>
            <div class='search-box'>
                <div class='search-section'>
                    <div class='search-input'>
                        <select class='input' id='searchType'>
                            <option value='0'>== 구분 ==</option>
                        </select>
                        <input type="text" id='searchText' placeholder="검색">
                        <button class='btn-small primary hands' id='btnSearch'>검색</button>
                        <button class='btn-small success revision' id='btnRevision'><i class='bx bx-revision'></i></button>
                    </div>
                </div>
                <div class='button-box'>
                    총 재고금액 : <span id='totalStockAmount'>0</span>
                    <input type='button' class='btn-small primary' value='재고마감' onclick='closeStock()' />
                    <!--<input type='button' class='btn-small danger' value='관리자 재고마감' onclick='closeStockAdmin()' />-->
                </div>
            </div>
        </div>
        <div>
            <table class='list'>
                <colgroup>
                    <col width='100' />
                    <col />
                    <col width='200' />
                    <col width='200' />
                    <col width='150' />
                    <col width='150' />
                    <col width='150' />
                    <col width='150' />
                    <col width='150' />
                </colgroup>
                <thead>
                    <tr>
                        <th>구분</th>
                        <th>품목명</th>
                        <th>품목코드</th>
                        <th>품목규격</th>
                        <th>안전재고수량</th>
                        <th>재고수량</th>
                        <th>단가</th>
                        <th>재고금액</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="paging-area mt20"></div>
    </div>
</div>

<input type='hidden' id='page' value='1' />

<?php
include "./views/modal/modalRegisterPurchase.php";
include "./views/modal/modalAdjustItemStock.php";
include "./views/modal/modalRegisterStockClose.php";
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// 현재 시간 업데이트 (모든 대시보드 공통 기능)
function updateTime() {
    const now = new Date();
    const formattedTime = now.toLocaleString('ko-KR', { 
        year: 'numeric', month: '2-digit', day: '2-digit', 
        hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false 
    }).replace(/\. /g, '.').replace(/\./, '.').replace(/, /, ' ').replace(/\./g, ' ');
    document.getElementById('current-time').textContent = formattedTime;
}

// 월별 재고 가치 추이 차트 생성
function createInventoryChart() {
    const ctx = document.getElementById('inventoryChart').getContext('2d');
    const data = {
        labels: ['8월', '9월', '10월', '11월(現)', '12월(예측)'],
        datasets: [{
            label: '원자재 재고 가치',
            data: [350, 320, 380, 410, 390],
            borderColor: '#3498db',
            backgroundColor: 'rgba(52, 152, 219, 0.4)',
            type: 'bar',
        }, {
            label: '완제품 재고 가치',
            data: [250, 280, 260, 270, 255],
            borderColor: '#2ecc71',
            backgroundColor: 'rgba(46, 204, 113, 0.4)',
            type: 'bar',
        }]
    };

    new Chart(ctx, {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false, 
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: '월별 재고 가치 추이 (단위: 백만 원)' }
            },
            scales: {
                x: { stacked: true },
                y: {
                    stacked: true,
                    title: { display: true, text: '금액 (백만원)' },
                    beginAtZero: true
                }
            }
        }
    });
}

window.onload = () => {
    //setInterval(updateTime, 1000);
    //createInventoryChart();
    // PHP API를 통해 모든 KPI 값과 목록 데이터를 받아와 업데이트하는 AJAX 로직이 필요합니다.
};

window.addEventListener('DOMContentLoaded', ()=>{
    getSelectList('getClassificationList', 'name', 'name', '#searchType');

    // 검색
    try {
        const search = document.getElementById('btnSearch');
        search.addEventListener('click', () => {
            getItemList({page : document.getElementById('page').value});
        });
    } catch(e) {}

    // 체크박스
    try{
		const chkAll = document.getElementById('chkAll');
		chkAll.addEventListener('click', ()=>{
			if(chkAll.checked) checkAll('chk');
			else checkAllDisolve('chk');
		});
	} catch(e) {}

    try {
        const btnRevision = document.getElementById('btnRevision');
        if(btnRevision) {
            btnRevision.addEventListener('click', function() {
                revision();
            });
        }
    } catch(e) {}

    // 선택삭제
    try {
        const btnDeleteSelected = document.getElementById('btnDeleteSelected');
        if(btnDeleteSelected) {
            btnDeleteSelected.addEventListener('click', deleteSelected);
        }
    } catch(e) {}

    try {
        document.getElementById('searchText').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {  // Enter 키를 감지
                getItemList({page:document.getElementById('page').value});
            }
        });
    } catch(e) {}

    try {
        const searchType = document.getElementById('searchType');
        searchType.addEventListener('change', function() {
            getItemList({page:document.getElementById('page').value});
        });
    } catch(e) {}

    getItemList({page : document.getElementById('page').value});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getItemList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getItemList = async ({
    page,
    per = 13,
    block = 4,
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {    
    document.getElementById('page').value = page;
    let where = `where 1=1`;

    // 구분이 선택되었을 경우
    try {
        const searchType = document.getElementById('searchType');
        if(searchType && searchType.value != '0') {
            where += ` and classification='${searchType.value}'`;
        }
    } catch(e) {}

    // 검색어가 있다면
    try {
        const searchText = document.getElementById('searchText');
        if(searchText) {
            if(searchText.value != '') {
                where += ` and (item_name like '%${searchText.value}%' or item_code like '%${searchText.value}%')`;
            }
        }
    } catch(e) {}
    

    const formData = new FormData();
    formData.append('controller', CONTROLLER);
    formData.append('mode', MODE);
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('orderby', orderBy);
    formData.append('asc', order);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.list tbody');
        tableBody.innerHTML = generateTableContent(data);

        getPaging('mes_items', 'uid', where, page, per, block, 'getItemList');
    } catch (error) {
        console.error('품목 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    document.getElementById('totalStockAmount').innerHTML = comma(data.totalAmount);
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.classification}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${comma(item.safety_stock_qty)}</td>
            <td class='center'>${comma(item.stock_qty)}</td>
            <td class='center'>${comma(item.price)}</td>
            <td class='center'>${comma(item.price * item.stock_qty)}</td>
            <td class='center'>
                <input type='button' class='btn-small grey' value='구매요청' onclick='requestPurchase(${item.uid})' />
                <input type='button' class='btn-small primary' value='재고조정' onclick='adjustItemStock(${item.uid})' />                
            </td>
        </tr>
    `).join('');
};

const requestPurchase = (uid) => {
    getter(uid);
    openModal('modalRegisterPurchase', 900, 450);
}

const adjustItemStock = (uid) => {
    adjustGetter(uid);
    openModal('modalAdjustItemStock', 900, 450);
}

// 새로고침
const revision = () => {
    document.getElementById('searchText').value = '';
    getItemList({page:document.getElementById('page').value});
}

const closeStock = async () => {
    if(!confirm('재고마감하시겠습니까?')) {
        return;
    }
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'closeStock');
    formData.send(formData);
    const data = await response.json();
    if(data.result === 'success') {
        alert('재고마감되었습니다');
        getItemList({page:document.getElementById('page').value});
    } else {
        alert(data.message);
    }
}

const closeStockAdmin = () => {
    openModal('modalRegisterStockClose', 900, 450);
}
</script>