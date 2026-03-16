<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 작업지시서 관리</div> 
    <div class='search-wrapper'>
        <div class='search-box'>
            <div class='search-section'>
                <div class='search-input'>
                    <input type="text" id='searchAccount' placeholder="거래처">
                    <input type="text" id='searchItem' placeholder="수주품목">
                    <input type="text" class='input datepicker' id='startOrderDate' placeholder="수주/납기 시작일">
                    <input type="text" class='input datepicker' id='endOrderDate' placeholder="수주/납기 종료일">
                    <button class='btn-large primary' id='btnSearch'>검색</button>
                    <button class='btn-large success revision' id='btnRevision'><i class='bx bx-revision'></i></button>
                </div>
            </div>
            <div class='button-box'>
                <button class='btn-large grey hands' id='btnPlanWorkOrder'>계획 생산지시 등록</button>
            </div>
        </div>
    </div>
    <div class='content-wrapper'>
        <div>
            <div class='title red'>수주 품목 목록</div>
            <table class='order-list list mt10'>
                <colgroup>
                    <col width='150' />
                    <col width='200' />
                    <col width='100' />
                    <col width='150' />
                    <col width='150' />
                    <col width='100' />
                    <col width='100' />
                    <col width='100' />
                    <col width='100' />
                    <col width='100' />                        
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
                        <th>재고수량</th>
                        <th>수주일</th>
                        <th>납기일</th>
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
                <div class='title red'>생산지시 목록</div>
                <div class='btn-box'>
                    <input type="text" id='searchWorkItem' class='input' placeholder="작업품목">
                    <select id='searchWorkStatus' class='input'>
                        <option value="ALL">상태 전체</option>
                        <option value="생산대기">생산대기</option>
                        <option value="작업중">작업중</option>
                        <option value="부분작업완료">부분작업완료</option>
                        <option value="작업완료">작업완료</option>
                    </select>
                    <input type='text' class='input datepicker' id='startWorkOrderDate' placeholder='작업지시 시작일'/>
                    <input type='text' class='input datepicker' id='endWorkOrderDate' placeholder='작업지시 종료일'/>
                    <input type='button' class='btn primary' value='검색' onclick='searchWorkOrderList()' />
                </div>
            </div>
            <table class='work-order-list list mt10'>
                <colgroup>
                    <col />                                                            
                    <col />                                                            
                    <col />                                                            
                    <col />                                                            
                    <col />                                                            
                    <col />                                                            
                    <col />                                                            
                    <col />                                                            
                    <col />                                                            
                    <col />                                                            
                    <col />                                                            
                    <col />                                                            
                    <col />                                                            
                    <col />                                                            
                </colgroup>
                <thead>
                    <tr>
                        <th>생산구분</th>
                        <th>작업지시일</th>
                        <th>거래처</th>
                        <th>작업품목</th>                        
                        <th>품번</th>
                        <th>규격</th>
                        <th>단위</th>
                        <th>지시 수량</th>
                        <th>작업한 수량</th>                        
                        <th>합격 수량</th>                        
                        <th>불량 수량</th>                        
                        <th>검사 대기 수량</th>                        
                        <th>상태</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="work-order-paging-area mt30 center"></div>
    </div>    
</div>

<input type='hidden' id='currentPage' value='1'>
<input type='hidden' id='currentWorkOrderPage' value='1'>

<?php
include "./views/modal/modalRegisterWorkOrder.php";
include "./views/modal/modalRegisterPlanWorkOrder.php";
include "./views/modal/modalModifyPlanWorkOrder.php";
include "./views/modal/modalModifyWorkOrder.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    const today = new Date().toISOString().slice(0,10);
    try {
        const s1 = document.getElementById('startOrderDate');
        const e1 = document.getElementById('endOrderDate');
        if (s1 && !s1.value) s1.value = today;
        if (e1 && !e1.value) e1.value = today;

        const s2 = document.getElementById('startWorkOrderDate');
        const e2 = document.getElementById('endWorkOrderDate');
        if (s2 && !s2.value) s2.value = today;
        if (e2 && !e2.value) e2.value = today;

        const workStatus = document.getElementById('searchWorkStatus');
        if (workStatus) workStatus.value = '생산대기';
    } catch(e) {}
    // 검색
    try {
        const search = document.getElementById('btnSearch');
        if(search) {
            search.addEventListener('click', () => {
                getOrdersList({page : 1});
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
                getOrdersList({page:document.getElementById('currentPage').value});
            }
        });
    } catch(e) {}
    
    getOrdersList({page : document.getElementById('currentPage').value});
    getWorkOrderList({page : document.getElementById('currentWorkOrderPage').value});

    try {
        const searchType = document.getElementById('searchType');
        searchType.addEventListener('change', function() {
            getOrdersList({page:document.getElementById('currentPage').value});
        });
    } catch(e) {}

    try {
        const btnPlanWorkOrder = document.getElementById('btnPlanWorkOrder');
        if(btnPlanWorkOrder) {
            btnPlanWorkOrder.addEventListener('click', function() {
                openModal('modalRegisterPlanWorkOrder', 1000, 450);
            });
        }
    } catch(e) {}
});


const getOrdersList = async({page}) => {  
    document.getElementById('currentPage').value = page;
    let where = `where product_status='주문' or product_status='생산중'`;

    // 거래처 / 수주품목 검색
    try {
        const account = document.getElementById('searchAccount').value;
        const item = document.getElementById('searchItem').value;
        if (account) {
            where += ` and account_name like '%${account}%'`;
        }
        if (item) {
            where += ` and item_name like '%${item}%'`;
        }
    } catch(e) {}

    // 수주일/납기일 기간 (둘 중 하나라도 기간 내에 있으면 포함)
    try {
        const start = document.getElementById('startOrderDate').value;
        const end = document.getElementById('endOrderDate').value;
        if (start && end) {
            where += ` and ((order_date between '${start}' and '${end}') or (shipment_date between '${start}' and '${end}'))`;
        }
    } catch(e) {}
    

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getOrdersItemList');
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

        getPaging('mes_order_items', 'uid', where, page, 3, 4, 'getOrdersList');
    } catch (error) {
        console.error('데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.account_name}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${item.unit}</td>
            <td class='center'>${comma(item.qty)}</td>
            <td class='center'>${comma(item.stock_qty)}</td>
            <td class='center'>${item.order_date}</td>
            <td class='center' style="${
                (item.shipment_date < new Date().toISOString().slice(0,10) && item.shipment_status !== '납품완료')
                    ? 'color:red;font-weight:bold;'
                    : ''
            }">${item.shipment_date}</td>
            <td class='center'>${item.product_status}</td>
            <td class='center'>
                <input type='button' class='btn-small grey' value='생산지시' onclick='openRegisterWorkOrder(${item.uid})' />
                <!--<input type='button' class='btn-small success' value='생산지시 목록' onclick='setWorkOrder(${item.uid})' />-->
            </td>
        </tr>
    `).join('');
};

const openRegisterWorkOrder = (uid) => {
    getterWorkOrderItem(uid);
    openModal('modalRegisterWorkOrder', 1000, 500);
}

const setWorkOrder = (uid) => {
    getWorkOrderList({page : document.getElementById('currentWorkOrderPage').value, order_uid : uid});
}

const searchWorkOrderList = () => {
    getWorkOrderList({page:1});
}

const getWorkOrderList = async ({page, order_uid = null}) => {   
    document.getElementById('currentWorkOrderPage').value = page;
    let where = `where 1=1`;

    // 작업지시일 기간
    try {
        const start = document.getElementById('startWorkOrderDate').value;
        const end = document.getElementById('endWorkOrderDate').value;
        if (start && end) {
            where += ` and order_date between '${start}' and '${end}'`;
        }
    } catch(e) {}

    // 작업품목
    try {
        const item = document.getElementById('searchWorkItem').value;
        if (item) {
            where += ` and item_name like '%${item}%'`;
        }
    } catch(e) {}

    // 상태 (기본: 생산대기,부분작업완료)
    try {
        const status = document.getElementById('searchWorkStatus').value;
        if (status && status !== 'ALL') {
            where += ` and status='${status}'`;
        } else if (!status || status === 'ALL') {
            where += ` and (status='생산대기' or status='부분작업완료')`;
        }
    } catch(e) {
        where += ` and (status='생산대기' or status='부분작업완료')`;
    }

    if(order_uid) {
        where += ` and order_uid=${order_uid}`;
    }
    
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getWorkOrderList');
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

        const tableBody = document.querySelector('.work-order-list tbody');
        tableBody.innerHTML = generateWorkOrderTableContent(data);

        getPagingTarget('mes_work_order', 'uid', where, page, 5, 4, 'getWorkOrderList', 'work-order-paging-area');
    } catch (error) {
        console.error('데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateWorkOrderTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'${item.classification === '계획생산' ? " style='color: orange;'" : ""}>${item.classification}</td>
            <td class='center'>${item.order_date}</td>
            <td class='center'>${item.account_name}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${item.unit}</td>
            <td class='center'>${comma(item.order_qty)}</td>
            <td class='center'>${comma(item.work_qty)}</td>
            <td class='center'>${comma(item.pass_qty)}</td>
            <td class='center'>${comma(item.fail_qty)}</td>
            <td class='center'>${comma(item.quality_qty)}</td>            
            <td class='center'>${item.status}</td>
            <td class='center'>
                ${
                    (item.classification == '수주생산' && item.status == '생산대기') ? `
                        <input type='button' class='btn-small grey' value='수정' onclick='openModifyWorkOrder(${item.uid})' />                    
                    ` : (item.classification == '계획생산' && item.status == '생산대기') ? `
                        <input type='button' class='btn-small grey' value='수정' onclick='modifyPlanWorkOrder(${item.uid})' />
                    ` : ``
                }
                <input type='button' class='btn-small danger' value='삭제' onclick='deleteWorkOrder(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

// 수주 생산지시 수정
const openModifyWorkOrder = (uid) => {    
    getModifyWorkOrderItem(uid);
    openModal('modalModifyWorkOrder', 1000, 500);
}

// 계획 생산지시 수정
const modifyPlanWorkOrder = (uid) => {
    getPlanWorkOrder(uid);
    openModal('modalModifyPlanWorkOrder', 1000, 500);
}

const deleteWorkOrder = async (uid) => {
    if(confirm('정말 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'mes');
        formData.append('mode', 'deleteWorkOrder');
        formData.append('uid', uid);

        try {
            const response = await fetch('./handler.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if(data.result == 'success') {
                getWorkOrderList({page : document.getElementById('currentWorkOrderPage').value});
            }
        } catch (error) {
            console.error('데이터를 삭제하는 중 오류가 발생했습니다:', error);
        }
    }
}

// 새로고침
const revision = () => {
    document.getElementById('searchText').value = '';
    document.getElementById('searchType').value = '';
    getOrdersList({page:document.getElementById('currentPage').value});
    getWorkOrderList({page:document.getElementById('currentWorkOrderPage').value});
}
</script>