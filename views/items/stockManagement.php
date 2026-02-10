<div class='main-container'>
    <div class='search-wrapper'>
        <div class='search-box'>
            <div class='search-section'>
                <div class='search-input'>
                    <select class='input' id='searchType'>
                        <option value='0'>== 구분 ==</option>
                    </select>
                    <input type="text" id='searchText' placeholder="검색">
                    <button class='btn-large primary hands' id='btnSearch'>검색</button>
                    <button class='btn-large success revision' id='btnRevision'><i class='bx bx-revision'></i></button>
                </div>
            </div>
            <div class='button-box'>
                총 재고금액 : <span id='totalStockAmount'>0</span>
                <input type='button' class='btn-large primary' value='재고마감' onclick='closeStock()' />
                <input type='button' class='btn-large danger' value='관리자 재고마감' onclick='closeStockAdmin()' />
            </div>
        </div>
    </div>
    <div class='content-wrapper'>
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

<script>
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
    per = 10,
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