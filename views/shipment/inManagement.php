<div class="main-container">
    <div class="page-title"><i class='bx bxs-food-menu'></i> 제품 입고 관리</div>    
    <div class='content-wrapper mt10'>   
        <div class="flex">
            <div class="title red">입고 대기 목록</div>
            <button class="btn btn-primary" id="btnRegisterItemIn">+ 신규 입고 등록</button>
        </div>
        <div class="btn-box mt10">
            <input type="text" class="input" id="pending_account" placeholder="거래처"/>
            <input type="text" class="input" id="pending_item_name" placeholder="품목명"/>
            <input type="text" class="input datepicker" id="pending_start_date" placeholder="시작일"/>
            <input type="text" class="input datepicker" id="pending_end_date" placeholder="종료일"/>
            <input type="button" class="btn primary" value="검색" id="btnSearchPending"/>
            <input type="button" class="btn danger" value="선택 삭제" id="btnDeleteSelectedPending"/>
        </div>
        <table class="list mt10" id="pending-list">
            <colgroup>
                <col style="width:3em"/>
                <col style="width:14%"/>
                <col style="width:6%"/>
                <col style="width:18%"/>
                <col/>
                <col style="width:6em"/>
                <col style="width:9em"/>
                <col style="width:5em"/>
                <col style="width:8em"/>
            </colgroup>
            <thead>
                <tr>
                    <th class="center">선택<br/><input type="checkbox" id="pending-check-all" title="전체 선택" style="display:inline-block;width:auto;vertical-align:middle"/></th>
                    <th>거래처</th>
                    <th>구분</th>
                    <th>품목명</th>
                    <th>규격</th>
                    <th>구매 수량</th>
                    <th class="center">구매 요청 일자 <span id="pending_sort_order" class="sort-btns" data-order="desc"><span class="sort-asc" title="오름차순">▲</span><span class="sort-desc" title="내림차순">▼</span></span></th>
                    <th>상태</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="pending-paging-area mt30 center"></div>
    </div>
    <div class='content-wrapper mt10'>   
        <div class="title red">입고 완료 이력</div>
        <div class="btn-box mt10">
            <input type="text" class="input" id="completed_account" placeholder="거래처"/>
            <input type="text" class="input" id="completed_item_name" placeholder="품목명"/>
            <input type="text" class="input datepicker" id="completed_start_date" placeholder="시작일"/>
            <input type="text" class="input datepicker" id="completed_end_date" placeholder="종료일"/>
            <input type="button" class="btn primary" value="검색" id="btnSearchCompleted"/>
            <input type="button" class="btn danger" value="선택 삭제" id="btnDeleteSelectedCompleted"/>
        </div>
        <table class="list mt10" id="completed-list">
            <colgroup>
                <col style="width:3em"/>
                <col style="width:14%"/>
                <col style="width:6%"/>
                <col style="width:18%"/>
                <col/>
                <col style="width:6em"/>
                <col style="width:9em"/>
                <col style="width:5em"/>
            </colgroup>
            <thead>
                <tr>
                    <th class="center">선택<br/><input type="checkbox" id="completed-check-all" title="전체 선택" style="display:inline-block;width:auto;vertical-align:middle"/></th>
                    <th>거래처</th>
                    <th>구분</th>
                    <th>품목명</th>
                    <th>규격</th>
                    <th>입고 수량</th>
                    <th class="center">입고 일자 <span id="completed_sort_order" class="sort-btns" data-order="desc"><span class="sort-asc" title="오름차순">▲</span><span class="sort-desc" title="내림차순">▼</span></span></th>
                    <th>상태</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="completed-paging-area mt30 center"></div>
    </div>
</div>

<?php
include_once './views/modal/modalRegisterItemIn.php';
?>

<style>
.sort-btns { margin-left: 4px; vertical-align: middle; }
.sort-btns .sort-asc, .sort-btns .sort-desc { cursor: pointer; opacity: 0.5; padding: 0 1px; }
.sort-btns .sort-asc:hover, .sort-btns .sort-desc:hover { opacity: 1; }
.sort-btns .sort-active { opacity: 1; font-weight: bold; }
</style>
<script>
window.addEventListener('DOMContentLoaded', async () => {
    const today = new Date().toISOString().slice(0, 10);
    try {
        const p1 = document.getElementById('pending_start_date');
        const p2 = document.getElementById('pending_end_date');
        const c1 = document.getElementById('completed_start_date');
        const c2 = document.getElementById('completed_end_date');
        if (p1 && !p1.value) p1.value = today;
        if (p2 && !p2.value) p2.value = today;
        if (c1 && !c1.value) c1.value = today;
        if (c2 && !c2.value) c2.value = today;
    } catch (e) {}

    try {
        const btnRegisterItemIn = document.getElementById('btnRegisterItemIn');
        if (btnRegisterItemIn) {
            btnRegisterItemIn.addEventListener('click', function () {
                openModal('modalRegisterItemIn', 700, 450);
            });
        }
        document.getElementById('btnSearchPending').addEventListener('click', () => getPurchaseList({ page: 1 }));
        document.getElementById('btnSearchCompleted').addEventListener('click', () => getCompletedList({ page: 1 }));
        document.getElementById('btnDeleteSelectedPending').addEventListener('click', deleteSelectedPending);
        document.getElementById('btnDeleteSelectedCompleted').addEventListener('click', deleteSelectedCompleted);
        document.getElementById('pending-check-all').addEventListener('change', function () {
            document.querySelectorAll('#pending-list tbody input[name="pending_uid"]').forEach(cb => { cb.checked = this.checked; });
        });
        document.getElementById('completed-check-all').addEventListener('change', function () {
            document.querySelectorAll('#completed-list tbody input[name="completed_uid"]').forEach(cb => { cb.checked = this.checked; });
        });
        document.querySelector('#pending_sort_order .sort-asc').addEventListener('click', () => { setPendingSort('asc'); getPurchaseList({ page: 1 }); });
        document.querySelector('#pending_sort_order .sort-desc').addEventListener('click', () => { setPendingSort('desc'); getPurchaseList({ page: 1 }); });
        document.querySelector('#completed_sort_order .sort-asc').addEventListener('click', () => { setCompletedSort('asc'); getCompletedList({ page: 1 }); });
        document.querySelector('#completed_sort_order .sort-desc').addEventListener('click', () => { setCompletedSort('desc'); getCompletedList({ page: 1 }); });
    } catch (e) {}

    setPendingSort('desc');
    setCompletedSort('desc');
    getPurchaseList({ page: 1 });
    getCompletedList({ page: 1 });
});

function setPendingSort(dir) {
    const wrap = document.getElementById('pending_sort_order');
    if (!wrap) return;
    wrap.setAttribute('data-order', dir);
    wrap.querySelectorAll('.sort-asc, .sort-desc').forEach(el => { el.classList.remove('sort-active'); if ((el.classList.contains('sort-asc') && dir === 'asc') || (el.classList.contains('sort-desc') && dir === 'desc')) el.classList.add('sort-active'); });
}
function setCompletedSort(dir) {
    const wrap = document.getElementById('completed_sort_order');
    if (!wrap) return;
    wrap.setAttribute('data-order', dir);
    wrap.querySelectorAll('.sort-asc, .sort-desc').forEach(el => { el.classList.remove('sort-active'); if ((el.classList.contains('sort-asc') && dir === 'asc') || (el.classList.contains('sort-desc') && dir === 'desc')) el.classList.add('sort-active'); });
}

function buildPendingWhere() {
    let where = `where 1=1 and status="입고대기"`;
    const account = document.getElementById('pending_account');
    const itemName = document.getElementById('pending_item_name');
    const startDate = document.getElementById('pending_start_date');
    const endDate = document.getElementById('pending_end_date');
    if (account && account.value.trim() !== '') where += ` and account_name like '%${account.value.trim().replace(/'/g, "''")}%'`;
    if (itemName && itemName.value.trim() !== '') where += ` and item_name like '%${itemName.value.trim().replace(/'/g, "''")}%'`;
    if (startDate && startDate.value) where += ` and purchase_date >= '${startDate.value}'`;
    if (endDate && endDate.value) where += ` and purchase_date <= '${endDate.value}'`;
    return where;
}

function buildCompletedWhere() {
    let where = `where 1=1 and status="입고완료"`;
    const account = document.getElementById('completed_account');
    const itemName = document.getElementById('completed_item_name');
    const startDate = document.getElementById('completed_start_date');
    const endDate = document.getElementById('completed_end_date');
    if (account && account.value.trim() !== '') where += ` and account_name like '%${account.value.trim().replace(/'/g, "''")}%'`;
    if (itemName && itemName.value.trim() !== '') where += ` and item_name like '%${itemName.value.trim().replace(/'/g, "''")}%'`;
    if (startDate && startDate.value) where += ` and in_date >= '${startDate.value}'`;
    if (endDate && endDate.value) where += ` and in_date <= '${endDate.value}'`;
    return where;
}

const getPurchaseList = async ({
    page,
    per = 15,
    block = 4
}) => {
    const orderBy = 'purchase_date';
    const order = (document.getElementById('pending_sort_order') && document.getElementById('pending_sort_order').getAttribute('data-order')) || 'desc';
    const where = buildPendingWhere();
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getPurchaseItemList');
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('orderby', orderBy);
    formData.append('asc', order);

    try {
        const response = await fetch('./handler.php', { method: 'POST', body: formData });
        const data = await response.json();
        const tableBody = document.querySelector('#pending-list tbody');
        tableBody.innerHTML = generateTableContent(data);
        getPagingTarget('mes_purchase_item', 'uid', where, page, per, block, 'getPurchaseList', 'pending-paging-area');
    } catch (error) {
        console.error('입고 대기 목록 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='9'>데이터가 없습니다</td></tr>`;
    }
    return data.data.map(item => `
        <tr>
            <td class='center'><input type='checkbox' name='pending_uid' value='${item.uid}' style='display:inline-block;width:auto;vertical-align:middle'/></td>
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

const getCompletedList = async ({
    page,
    per = 15,
    block = 4
}) => {
    const orderBy = 'in_date';
    const order = (document.getElementById('completed_sort_order') && document.getElementById('completed_sort_order').getAttribute('data-order')) || 'desc';
    const where = buildCompletedWhere();
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getPurchaseItemList');
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('orderby', orderBy);
    formData.append('asc', order);

    try {
        const response = await fetch('./handler.php', { method: 'POST', body: formData });
        const data = await response.json();
        const tableBody = document.querySelector('#completed-list tbody');
        tableBody.innerHTML = generateCompletedTableContent(data);
        getPagingTarget('mes_purchase_item', 'uid', where, page, per, block, 'getCompletedList', 'completed-paging-area');
    } catch (error) {
        console.error('입고 완료 이력 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateCompletedTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='8'>데이터가 없습니다</td></tr>`;
    }
    return data.data.map(item => {
        const inDate = (item.in_date && item.in_date !== '0000-00-00') ? item.in_date : (item.purchase_date || '');
        return `
        <tr>
            <td class='pd center'><input type='checkbox' name='completed_uid' value='${item.uid}' style='display:inline-block;width:auto;vertical-align:middle'/></td>
            <td class='pd center'>${item.account_name}</td>
            <td class='pd center'>${item.classification}</td>
            <td class='pd center'>${item.item_name}</td>
            <td class='pd center'>${item.standard}</td>
            <td class='pd center'>${item.purchase_qty}</td>
            <td class='pd center'>${inDate}</td>
            <td class='pd center'>${item.status}</td>
        </tr>
    `;
    }).join('');
};

async function deleteSelectedPending() {
    const uids = Array.from(document.querySelectorAll('#pending-list tbody input[name="pending_uid"]:checked')).map(cb => cb.value);
    if (uids.length === 0) { alert('삭제할 항목을 선택하세요.'); return; }
    if (!confirm(`선택한 ${uids.length}건을 삭제하시겠습니까?`)) return;
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'deletePurchaseItems');
    uids.forEach(uid => formData.append('uids[]', uid));
    try {
        const res = await fetch('./handler.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.result === 'success') {
            alert(data.message);
            getPurchaseList({ page: 1 });
            getCompletedList({ page: 1 });
        } else alert(data.message || '삭제 실패');
    } catch (e) {
        alert('삭제 요청 중 오류가 발생했습니다.');
    }
}

async function deleteSelectedCompleted() {
    const uids = Array.from(document.querySelectorAll('#completed-list tbody input[name="completed_uid"]:checked')).map(cb => cb.value);
    if (uids.length === 0) { alert('삭제할 항목을 선택하세요.'); return; }
    if (!confirm(`선택한 ${uids.length}건을 삭제하시겠습니까?`)) return;
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'deletePurchaseItems');
    uids.forEach(uid => formData.append('uids[]', uid));
    try {
        const res = await fetch('./handler.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.result === 'success') {
            alert(data.message);
            getPurchaseList({ page: 1 });
            getCompletedList({ page: 1 });
        } else alert(data.message || '삭제 실패');
    } catch (e) {
        alert('삭제 요청 중 오류가 발생했습니다.');
    }
}

function deletePurchaseItem(uid) {
    if (!confirm('이 항목을 삭제하시겠습니까?')) return;
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'deletePurchaseItem');
    formData.append('uid', uid);
    fetch('./handler.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.result === 'success') {
                alert(data.message);
                getPurchaseList({ page: 1 });
                getCompletedList({ page: 1 });
            } else alert(data.message);
        })
        .catch(() => alert('삭제 중 오류가 발생했습니다'));
}

const completePurchaseItem = (uid) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'completePurchaseItem');
    formData.append('uid', uid);
    fetch('./handler.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.result === 'success') {
                alert('입고 처리가 완료되었습니다');
                getPurchaseList({ page: 1 });
                getCompletedList({ page: 1 });
            } else alert(data.message);
        })
        .catch(() => alert('입고 처리 중 오류가 발생했습니다'));
};
</script>