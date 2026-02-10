<div class='main-container'>
    <div class='search-wrapper'>
        <div class='search-box'>
            <div class='search-section'>
                <div class='search-input'>
                    <input type="text" id='searchText' placeholder="검색">
                    <button class='btn-large primary' id='btnSearch'>검색</button>
                    <button class='btn-large success revision' id='btnRevision'><i class='bx bx-revision'></i></button>
                </div>
            </div>
            <div class='button-box'>
            </div>
        </div>
    </div>
    <div class='content-wrapper'>
        <div>
            <div class='title red'>구매 요청 목록</div>
            <table class='order-list list mt10'>
                <colgroup>
                    <col />
                    <col width='200' />
                    <col width='200' />                       
                    <col width='300' />
                </colgroup>
                <thead>
                    <tr>
                        <th>거래처</th>
                        <th>구매 요청일</th>                        
                        <th>상태</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="paging-area mt20"></div>
    </div>
    <div class='content-wrapper mt20'>
        <div>
            <div class='flex'>
                <div class='title red'>구매 요청 품목</div>
                <div class='btn-box'>
                    <input type='button' class='btn primary' value='전체보기' onclick='getPurchaseItemList({page : 1})' />
                </div>
            </div>
            <table class='purchase-item-list list mt10'>
                <colgroup>                    
                    <col width='100' />
                    <col width='200' />
                    <col width='100' />
                    <col width='150' />
                    <col width='150' />
                    <col width='150' />
                    <col width='150' />
                    <col width='150' />
                    <col />                                                            
                </colgroup>
                <thead>
                    <tr>                        
                        <th>품목구분</th>
                        <th>품목명</th>
                        <th>규격</th>
                        <th>단위</th>
                        <th>구매 요청 수량</th>
                        <th>잔여 구매 수량</th>                        
                        <th>상태</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="purchase-item-paging-area mt30 center"></div>
    </div>    
</div>

<?php
include "./views/modal/modalModifyPurchase.php";
include "./views/modal/modalRegisterPurchaseItemIn.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    // 검색
    try {
        const search = document.getElementById('btnSearch');
        if(search) {
            search.addEventListener('click', () => {
                getPurchaseList({page : 1});
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
                getPurchaseList({page:1});
            }
        });
    } catch(e) {}
    
    getPurchaseList({page : 1});
    getPurchaseItemList({page : 1});

    try {
        const searchType = document.getElementById('searchType');
        searchType.addEventListener('change', function() {
            getPurchaseList({page:1});
        });
    } catch(e) {}
});


const getPurchaseList = async({page}) => {    
    let where = `where status='구매요청'`;

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
    formData.append('mode', 'getPurchaseList');
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', 3);
    formData.append('orderby', 'uid');
    formData.append('asc', 'desc');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.order-list tbody');
        tableBody.innerHTML = generateTableContent(data);

        getPaging('mes_purchase', 'uid', where, page, 3, 4, 'getPurchaseList');
    } catch (error) {
        console.error('구매 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.account_name}</td>
            <td class='center'>${item.purchase_date}</td>
            <td class='center'>${item.status}</td>
            <td class='center'>                
                <input type='button' class='btn-small primary' value='수정' onclick='modifyPurchase(${item.uid})' />
                <input type='button' class='btn-small success' value='구매 요청 품목 목록' onclick='setPurchaseItemList(${item.uid})' />
                <input type='button' class='btn-small danger' value='삭제' onclick='deletePurchase(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const modifyPurchase = (uid) => {
    getter(uid);
    openModal('modalModifyPurchase', 1200, 480);
}

const setPurchaseItemList = (uid) => {    
    getPurchaseItemList({page : 1}, uid);
}

const getPurchaseItemList = async ({page}, fid = null) => {    
    
    let where;
    if(fid) {
        where = `where fid=${fid} and status='입고대기'`;
    } else {
        where = `where status='입고대기'`;
    }
    
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getPurchaseItemList');
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', 5);
    formData.append('orderby', 'uid');
    formData.append('asc', 'desc');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.purchase-item-list tbody');
        if(fid) {
            tableBody.innerHTML = generatePurchaseItemTableContent(data, fid);
        } else {
            tableBody.innerHTML = generatePurchaseItemTableContent(data);
        }

        getPagingTarget('mes_purchase_item', 'uid', where, page, 5, 4, 'getPurchaseItemList', 'purchase-item-paging-area');
    } catch (error) {
        console.error('구매 요청 품목 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generatePurchaseItemTableContent = (data, fid = null) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.data.map(item => `
        <tr>            
            <td class='center'>${item.classification}</td>
            <td class='center'>${item.item_name}</td>            
            <td class='center'>${item.standard}</td>
            <td class='center'>${item.unit}</td>
            <td class='center'>${comma(item.purchase_qty)}</td>
            <td class='center'>${comma(item.remain_qty)}</td>
            <td class='center'>${item.status}</td>
            <td class='center'>
                ${(item.status && item.status.trim() === '입고대기') 
                    ? (fid 
                        ? `<input type='button' class='btn-small grey' value='입고 등록' onclick='openPurchaseItemIn(${item.uid}, ${fid})' />`
                        : `<input type='button' class='btn-small grey' value='입고 등록' onclick='openPurchaseItemIn(${item.uid})' />`)
                    : ''
                }
                <input type='button' class='btn-small danger' value='삭제' onclick='deletePurchaseItem(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

// 구매요청 품목 삭제
const deletePurchaseItem = (uid) => {
    if(!confirm('해당 데이터를 삭제하시겠습니까?')) {
        return;
    }

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'deletePurchaseItem');
    formData.append('uid', uid);

    fetch('./handler.php', {
        method: 'post',
        body : formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
    })
    .then(data => {
        if(data.result == 'success') {
            getPurchaseItemList({page : 1});
        }
    })
    .catch(error => console.log(error));
}

const openPurchaseItemIn = (uid, fid = null) => {
    if(fid) {
        getPurchaseItem(uid, fid);
    } else {
        getPurchaseItem(uid);
    }
    openModal('modalRegisterPurchaseItemIn', 800, 450);
}

// 구매요청 삭제
const deletePurchase = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'mes');
        formData.append('mode', 'deletePurchase');
        formData.append('uid', uid);

        fetch('./handler.php', {
            method: 'post',
            body : formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(function(data) {
            if(data != null || data != '') {
                alert(data.message);

                if(data.result == 'success') {
                    getPurchaseList({page:1});
                    getPurchaseItemList({page : 1});
                }
            }
        })
        .catch(error => console.log(error));
    }
}

// 새로고침
const revision = () => {
    document.getElementById('searchText').value = '';
    document.getElementById('searchType').value = '';
    getOrdersList({page:1});
}
</script>