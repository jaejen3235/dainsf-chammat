<div class='main-container'>
    <div class='search-wrapper'>
        <div class='search-box'>
            <div class='search-section'>
                <div class='search-input'>
                    <input type="text" id='searchText' placeholder="검색">
                    <button class='btn-large primary' id='btnSearch'>검색</button>
                    <button class='btn-large success revision' id='btnRevision'>새로고침</button>
                </div>
            </div>
            <div class='button-box'>
            </div>
        </div>
    </div>
    <div class='content-wrapper'>
        <div>
            <div class='card-title flex'>
                <div>출하 지시서</div>
            </div>
            <table class='list'>
                <colgroup>
                    <col width='150' />
                    <col width='200' />
                    <col width='150' />
                    <col />                    
                    <col width='150' />
                    <col width='150' />
                    <col width='100' />
                    <col width='100' />                        
                    <col width='200' />
                </colgroup>
                <thead>
                    <tr>
                        <th>거래처</th>
                        <th>품목</th>
                        <th>품번</th>
                        <th>규격</th>
                        <th>출하지시일</th>
                        <th>출하지시수량</th>
                        <th>잔여출하수량</th>
                        <th>출하상태</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="paging-area mt20"></div>
        <div class='mt20'>
            <div class='card-title flex'>
                <div>출하 품목 내역</div>
            </div>
            <table class='delivery-list list'>
                <colgroup>
                    <col width='150' />
                    <col width='200' />
                    <col width='150' />
                    <col />
                    <col width='100' />
                    <col width='150' />     
                </colgroup>
                <thead>
                    <tr>
                        <th>거래처</th>
                        <th>품목</th>
                        <th>품번</th>
                        <th>규격</th>
                        <th>출하일</th>
                        <th>출하수량</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<?php
include "./views/modal/modalRegisterDelivery.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    // 검색
    try {
        const btnSearch = document.getElementById('btnSearch');
        if(btnSearch) {
            btnSearch.addEventListener('click', () => {
                getDeliveryList({page : 1});
            });
        }
    } catch(e) {}

    try {
        const btnRevision = document.getElementById('btnRevision');
        if(btnRevision) {
            btnRevision.addEventListener('click', function() {
                revision();
            });
        }
    } catch(e) {}

    try {
        document.getElementById('searchText').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {  // Enter 키를 감지
                getDeliveryList({page:1});
            }
        });
    } catch(e) {}
    
    getDeliveryList({page : 1});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getDeliveryList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getDeliveryList = async ({
    page,
    per = 10,
    block = 4,
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {    
    let where = `where 1=1`;

    // 검색어가 있다면
    try {
        const searchText = document.getElementById('searchText');
        if(searchText) {
            if(searchText.value != '') {
                where += ` and (item_name like '%${searchText.value}%' or item_code like '%${searchText.value}%') or account_name like '%${searchText.value}%'`;
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

        getPaging('mes_delivery', 'uid', where, page, per, block, 'getDeliveryList');
    } catch (error) {
        console.error('출하 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.account_name}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${comma(item.delivery_date)}</td>
            <td class='center'>${comma(item.delivery_qty)}</td>
            <td class='center'>${comma(item.remain_qty)}</td>
            <td class='center'>${item.status}</td>
            <td class='center'>
                ${
                    Number(item.delivery_remain_qty) > Number(item.stock_qty)
                        ? `<input type='button' class='btn-small danger' value='재고부족' disabled />`
                        : (item.status === '출하완료'
                            ? ''
                            : `<input type='button' class='btn-small grey' value='출하등록' onclick='registerDelivery(${item.uid})' />`)
                }
                <input type='button' class='btn-small orange' value='출하내역 보기' onclick='listDelivery(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const listDelivery = async (uid) => {
    const formData = new FormData();
    formData.append('controller', CONTROLLER);
    formData.append('mode', 'getDeliveryItemList');
    formData.append('uid', uid);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.delivery-list tbody');
        tableBody.innerHTML = generateTableContent2(data);
    } catch (error) {
        console.error('출하 내역 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
}

const generateTableContent2 = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.account_name}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${item.delivery_date}</td>
            <td class='center'>${comma(item.delivery_qty)}</td>
        </tr>
    `).join('');
}

const registerDelivery = (uid) => {
    getter(uid);
    openModal('modalRegisterDelivery', 1200, 450);
}

// 새로고침
const revision = () => {
    document.getElementById('searchText').value = '';
    document.getElementById('searchType').value = '0';
    getDeliveryList({page:1});
}
</script>