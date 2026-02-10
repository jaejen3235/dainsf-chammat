<div class="main-container">
    <div class="page-title"><i class='bx bxs-food-menu'></i> 제품 출하 처리</div>
    <div class="content-wrapper">
        <div class="title red">출하 대기 주문 목록</div>    
        <table class="list mt10">
            <thead>
                <tr>                    
                    <th>출하 요청일</th>
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
include "./views/modal/modalInvoice.php";
?>

<script>
window.addEventListener('DOMContentLoaded', async () => {	  
    getDeliveryList({page:1});
});      

const getDeliveryList = async ({
    page = 1,
    per = 10,
    block = 4,
    orderBy = 'uid',
    order = 'desc'
}) => {    
    let where = `where 1=1`;

    // 검색어가 있다면
    try {
        const searchText = document.getElementById('searchText');
        if(searchText) {
            if(searchText.value != '') {
                where += ` and item_name like '%${searchText.value}%'`;
            }
        }
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
        console.error('사원 데이터를 가져오는 중 오류가 발생했습니다:', error);
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
    openModal('modalInvoice', 900, 700);
}
</script>