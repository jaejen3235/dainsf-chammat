<div class="main-container">
    <div class="page-title"><i class='bx bxs-food-menu'></i> 제품 출하 처리</div>
    <div class="content-wrapper">
        <div class="title red">출하 대기 주문 목록</div>
        <div class="btn-box mt10">
            <input type="text" class="input" id="search_keyword" placeholder="거래처, 품목명"/>
            <input type="text" class="input datepicker" id="search_start_date" placeholder="시작일"/>
            <input type="text" class="input datepicker" id="search_end_date" placeholder="종료일"/>
            <input type="button" class="btn primary" value="검색" id="btnSearch"/>
            <input type="button" class="btn" value="엑셀 다운로드" id="btnExcelDownload"/>
        </div>
        <table class="list mt10">
            <thead>
                <tr>                    
                    <th class='center' style='width: 9em; white-space: nowrap;'>출하 요청일 <span id='delivery_date_sort' class='sort-btns' data-order='desc'><span class='sort-asc' title='오름차순'>▲</span><span class='sort-desc' title='내림차순'>▼</span></span></th>
                    <th>거래처</th>
                    <th>품목명</th>
                    <th>규격</th>                    
                    <th>출하 수량</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>        

<?php
include "./views/modal/modalRegisterDelivery.php";
include "./views/modal/modalNewInvoice.php";
?>

<style>
.sort-btns { margin-left: 4px; vertical-align: middle; }
.sort-btns .sort-asc, .sort-btns .sort-desc { cursor: pointer; opacity: 0.5; padding: 0 1px; }
.sort-btns .sort-asc:hover, .sort-btns .sort-desc:hover { opacity: 1; }
.sort-btns .sort-active { opacity: 1; font-weight: bold; }
</style>

<script>
let currentOrderBy = 'delivery_date';
let currentOrder = 'desc';

window.addEventListener('DOMContentLoaded', async () => {	  
    const today = new Date().toISOString().slice(0, 10);
    try {
        const startDate = document.getElementById('search_start_date');
        const endDate = document.getElementById('search_end_date');
        if (startDate && !startDate.value) startDate.value = today;
        if (endDate && !endDate.value) endDate.value = today;
    } catch (e) {}

    try {
        const btnSearch = document.getElementById('btnSearch');
        if (btnSearch) {
            btnSearch.addEventListener('click', () => getDeliveryList({ page: 1, orderBy: currentOrderBy, order: currentOrder }));
        }
        const btnExcelDownload = document.getElementById('btnExcelDownload');
        if (btnExcelDownload) {
            btnExcelDownload.addEventListener('click', downloadDeliveryExcel);
        }
        const keyword = document.getElementById('search_keyword');
        if (keyword) {
            keyword.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    getDeliveryList({ page: 1, orderBy: currentOrderBy, order: currentOrder });
                }
            });
        }

        const deliveryDateSort = document.getElementById('delivery_date_sort');
        if (deliveryDateSort) {
            deliveryDateSort.querySelector('.sort-asc').addEventListener('click', () => {
                setDeliveryDateSort('asc');
                getDeliveryList({ page: 1, orderBy: currentOrderBy, order: currentOrder });
            });
            deliveryDateSort.querySelector('.sort-desc').addEventListener('click', () => {
                setDeliveryDateSort('desc');
                getDeliveryList({ page: 1, orderBy: currentOrderBy, order: currentOrder });
            });
        }
    } catch (e) {}

    setDeliveryDateSort('desc');
    getDeliveryList({page:1, orderBy: currentOrderBy, order: currentOrder});
});      

function setDeliveryDateSort(dir) {
    const wrap = document.getElementById('delivery_date_sort');
    if (!wrap) return;
    currentOrderBy = 'delivery_date';
    currentOrder = dir;
    wrap.setAttribute('data-order', dir);
    wrap.querySelectorAll('.sort-asc, .sort-desc').forEach((el) => {
        el.classList.remove('sort-active');
        if ((el.classList.contains('sort-asc') && dir === 'asc') || (el.classList.contains('sort-desc') && dir === 'desc')) {
            el.classList.add('sort-active');
        }
    });
}

const getDeliveryList = async ({
    page = 1,
    per = 10,
    block = 4,
    orderBy = currentOrderBy,
    order = currentOrder
}) => {    
    currentOrderBy = orderBy;
    currentOrder = order;
    // 출하 대기만 표시 (출하완료 제외)
    let where = `where 1=1 and status != '출하완료'`;

    // 검색 조건
    try {
        const keyword = document.getElementById('search_keyword');
        const startDate = document.getElementById('search_start_date');
        const endDate = document.getElementById('search_end_date');

        if (keyword && keyword.value.trim() !== '') {
            const q = keyword.value.trim().replace(/'/g, "''");
            where += ` and (account_name like '%${q}%' or item_name like '%${q}%')`;
        }
        if (startDate && startDate.value) where += ` and delivery_date >= '${startDate.value}'`;
        if (endDate && endDate.value) where += ` and delivery_date <= '${endDate.value}'`;
    } catch(e) {}
    

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDeliveryList');
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
        console.error('데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>데이터가 없습니다</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.delivery_date}</td>
            <td class='center'>${item.account_name}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${comma(item.delivery_qty)}</td>
            <td class='center'>
                <input type='button' class='btn-small primary' value='출하 처리' onclick='registerDelivery(${item.uid})' />
                <input type='button' class='btn-small success' value='거래명세서 인쇄' onclick='openInvoiceModal(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const registerDelivery = (uid) => {
    getter(uid);
    openModal('modalRegisterDelivery', 1200, 500);
}

const openInvoiceModal = (uid) => {
    getInvoiceData(uid);
    //openModal('modalInvoice', 900, 700);
    openModal('modalNewInvoice', 900, 700);
}

const downloadDeliveryExcel = () => {
    let where = `where 1=1 and status != '출하완료'`;

    try {
        const keyword = document.getElementById('search_keyword');
        const startDate = document.getElementById('search_start_date');
        const endDate = document.getElementById('search_end_date');

        if (keyword && keyword.value.trim() !== '') {
            const q = keyword.value.trim().replace(/'/g, "''");
            where += ` and (account_name like '%${q}%' or item_name like '%${q}%')`;
        }
        if (startDate && startDate.value) where += ` and delivery_date >= '${startDate.value}'`;
        if (endDate && endDate.value) where += ` and delivery_date <= '${endDate.value}'`;
    } catch (e) {}

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = './handler.php';
    form.target = '_blank';
    form.style.display = 'none';

    const fields = {
        controller: 'mes',
        mode: 'getShipmentOrderListExcel',
        where,
        orderby: currentOrderBy,
        asc: currentOrder
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