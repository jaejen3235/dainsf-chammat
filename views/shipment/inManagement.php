<div class="main-container">
    <div class="page-title"><i class='bx bxs-food-menu'></i> 제품 입고 관리</div>    
    <div class='content-wrapper mt10'>   
        <div class="flex">
            <div class="title red">입고 대기 목록</div>
            <button class="btn btn-primary" id="btnRegisterItemIn">+ 신규 입고 등록</button>
        </div>
        <table class="list mt10" id="pending-list">
            <thead>
                <tr>
                    <th>거래처</th>
                    <th>구분</th>
                    <th>품목명</th>
                    <th>규격</th>
                    <th>구매 수량</th>
                    <th>구매 요청 일자</th>
                    <th>상태</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody id="pending-list"></tbody>
        </table>
    </div>
    <div class='content-wrapper mt10'>   
        <div class="title red">입고 완료 이력</div>
        <table class="list mt10" id="completed-list">
            <thead>
                <tr>
                    <th>거래처</th>
                    <th>구분</th>
                    <th>품목명</th>
                    <th>규격</th>
                    <th>입고 수량</th>
                    <th>입고 일자</th>
                    <th>상태</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php
include_once './views/modal/modalRegisterItemIn.php';
?>

<script>
window.addEventListener('DOMContentLoaded', async () => {	  
    try {
        const btnRegisterItemIn = document.getElementById('btnRegisterItemIn');
        if(btnRegisterItemIn) {
            btnRegisterItemIn.addEventListener('click', function() {
                openModal('modalRegisterItemIn', 700, 450);
            });
        }
    } catch(e) {}

    getPurchaseList({page:1});
    getCompletedList({page:1});
});

// 입고 대기 목록 불러오기
const getPurchaseList = async ({
    page,
    per = 15,
    block = 4,
    orderBy = 'uid',
    order = 'desc'
}) => {    
    let where = `where 1=1 and status="입고대기"`;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getPurchaseItemList');
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

        const tableBody = document.querySelector('#pending-list tbody');
        tableBody.innerHTML = generateTableContent(data);

        getPaging('mes_purchase_item', 'uid', where, page, per, block, 'getPurchaseList');
    } catch (error) {
        console.error('입고 대기 목록 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>데이터가 없습니다</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.account_name}</td>
            <td class='center'>${item.classification}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${item.purchase_qty}</td>
            <td class='center'>${item.purchase_date}</td>
            <td class='center'>${item.status}</td>
            <td class='center'>
                <input type='button' class='btn-small primary' value='입고 처리' onclick='completePurchaseItem(${item.uid})' />
                <input type='button' class='btn-small danger' value='삭제' onclick='deletePurchaseItem(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

// 입고 완료 목록 불러오기
const getCompletedList = async ({
    page,
    per = 15,
    block = 4,
    orderBy = 'uid',
    order = 'desc'
}) => {    
    let where = `where 1=1 and status="입고완료"`;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getPurchaseItemList');
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

        const tableBody = document.querySelector('#completed-list tbody');
        tableBody.innerHTML = generateCompletedTableContent(data);

        getPaging('mes_purchase_item', 'uid', where, page, per, block, 'getCompletedList');
    } catch (error) {
        console.error('입고 대기 목록 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateCompletedTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>데이터가 없습니다</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='pd center'>${item.account_name}</td>
            <td class='pd center'>${item.classification}</td>
            <td class='pd center'>${item.item_name}</td>
            <td class='pd center'>${item.standard}</td>
            <td class='pd center'>${item.purchase_qty}</td>
            <td class='pd center'>${item.purchase_date}</td>
            <td class='pd center'>${item.status}</td>
        </tr>
    `).join('');
};

//입고 처리
const completePurchaseItem = (uid) => {
    console.log(uid);
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'completePurchaseItem');
    formData.append('uid', uid);
    fetch('./handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        if(data.result === 'success') {
            alert('입고 처리가 완료되었습니다');
            getPurchaseList({page:1});
            getCompletedList({page:1});
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('입고 처리 중 오류가 발생했습니다');
    });
}
</script>