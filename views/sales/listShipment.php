<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 출하 지시 관리</div> 
    <div class='search-wrapper'>
        <div class='search-box'>
            <div class='search-section'>
                <div class='search-input'>
                    <input type="text" id='searchText' placeholder="검색">
                    <button class='btn-large primary' id='btnSearch'>검색</button>
                    <button class='btn-large success revision' id='btnRevision'>새로고침</button>
                    <button class='btn-large' id='btnExcelDownload'>엑셀 다운로드</button>
                </div>
            </div>
            <div class='button-box'>
                <button class="btn btn-primary" id="btnRegisterShipment">+ 개별 출하 지시 등록</button>
            </div>
        </div>
    </div>
    <div class='content-wrapper'>
        <div>
            <table class='list'>
                <colgroup>
                    <col width='150' />
                    <col width='200' />
                    <col width='150' />
                    <col width='150' />
                    <col width='100' />
                    <col width='100' />
                    <col width='100' />
                    <col width='100' />
                    <col width='130' />
                    <col width='130' />                        
                    <col width='150' />                        
                    <col />
                </colgroup>
                <thead>
                    <tr>
                        <th>거래처</th>
                        <th>수주품목</th>
                        <th>품번</th>
                        <th>규격</th>
                        <th>단위</th>
                        <th>수주수량</th>
                        <th>잔여납품수량</th>
                        <th>재고수량</th>
                        <th>수주일</th>
                        <th>납기일</th>
                        <th>출하상태</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="paging-area mt20"></div>
    </div>
</div>

<?php
include "./views/modal/modalRegisterShipmentOrder.php";
include "./views/modal/modalRegisterShipment.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    // 검색
    try {
        const btnSearch = document.getElementById('btnSearch');
        if(btnSearch) {
            btnSearch.addEventListener('click', () => {
                getOrdersItemList({page : 1});
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
        const btnExcelDownload = document.getElementById('btnExcelDownload');
        if (btnExcelDownload) {
            btnExcelDownload.addEventListener('click', downloadShipmentExcel);
        }
    } catch(e) {}

    try {
        document.getElementById('searchText').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {  // Enter 키를 감지
                getOrdersItemList({page:1});
            }
        });
    } catch(e) {}

    try {
        const btnRegisterShipment = document.getElementById('btnRegisterShipment');
        if(btnRegisterShipment) {
            btnRegisterShipment.addEventListener('click', function() {
                openModal('modalRegisterShipmentOrder', 1200, 500);
            });
        }
    } catch(e) {}
    
    getOrdersItemList({page : 1});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getOrdersItemList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getOrdersItemList = async ({
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
                where += ` and (item_name like '%${searchText.value}%' or account_name like '%${searchText.value}%')`;
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

        getPaging('mes_order_items', 'uid', where, page, per, block, 'getOrdersItemList');
    } catch (error) {
        console.error('사원 데이터를 가져오는 중 오류가 발생했습니다:', error);
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
            <td class='center'>${item.unit}</td>
            <td class='center'>${comma(item.qty)}</td>
            <td class='center'>${comma(item.delivery_remain_qty)}</td>
            <td class='center'>${comma(item.stock_qty)}</td>
            <td class='center'>${item.order_date}</td>
            <td class='center' style="${
                (item.shipment_date < new Date().toISOString().slice(0,10) && item.shipment_status !== '출하완료')
                    ? 'color:red;font-weight:bold;'
                    : ''
            }">${item.shipment_date}</td>
            <td class='center'>${item.shipment_status}</td>
            <td class='center'>
                ${(item.shipment_status === '출하지시대기' || item.shipment_status === '부분출하') ? `<input type='button' class='btn-small grey' value='출하지시 등록' onclick='registerDelivery(${item.uid})' />` : ''}
            </td>
        </tr>
    `).join('');
};

const registerDelivery = (uid) => {
    getShipmentItem(uid);
    openModal('modalRegisterShipment', 1200, 530);
}

const listDelivery = (uid) => {
    getDeliveryList(uid);
    openModal('modalRegisterShipment', 1200, 530);
}

// 새로고침
const revision = () => {
    document.getElementById('searchText').value = '';
    document.getElementById('searchType').value = '0';
    getOrdersItemList({page:1});
}

const downloadShipmentExcel = () => {
    let where = `where 1=1`;

    try {
        const searchText = document.getElementById('searchText');
        if(searchText && searchText.value != '') {
            where += ` and (item_name like '%${searchText.value}%' or account_name like '%${searchText.value}%')`;
        }
    } catch(e) {}

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = './handler.php';
    form.target = '_blank';
    form.style.display = 'none';

    const fields = {
        controller: 'mes',
        mode: 'getOrdersItemListExcel',
        where
    };

    Object.entries(fields).forEach(([key, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
    form.remove();
};
</script>
