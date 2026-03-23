<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 자재 수불부</div> 
    <div class='content-wrapper'>
        <div class='right'>
            <input type='text' class='input' id='search_classification' placeholder='구분' />
            <input type='text' class='input' id='search_item_name' placeholder='품목명' />
            <input type='text' class='input datepicker' id='start_date' placeholder='시작일' />
            <input type='text' class='input datepicker' id='end_date' placeholder='종료일' />
            <input type='button' class='btn-middle primary' value='검색' id='btnSearch' />
            <input type='button' class='btn-middle success' value='검색일 초기화' id='btnReset' />
        </div>
        <table class='list mt10' id='inout-list'>
            <colgroup>
                <col width='90' />
                <col width='160' />
                <col width='100' />
                <col width='140' />
                <col width='85' />
                <col width='85' />
                <col width='120' />
            </colgroup>
            <thead>
                <tr>
                    <th>구분</th>
                    <th>품목명</th>
                    <th>품목코드</th>
                    <th>품목규격</th>
                    <th>입고수량</th>
                    <th>출고수량</th>
                    
                    <th class='center'>입/출고 날짜 <span id='inout_sort_order' class='sort-btns' data-order='desc'><span class='sort-asc' title='오름차순'>▲</span><span class='sort-desc' title='내림차순'>▼</span></span></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="paging-area mt20"></div>
    </div>
</div>

<style>
#inout-list { table-layout: fixed; width: 100%; }
#inout-list th:last-child,
#inout-list td:last-child { min-width: 140px; }
.sort-btns { margin-left: 4px; vertical-align: middle; }
.sort-btns .sort-asc, .sort-btns .sort-desc { cursor: pointer; opacity: 0.5; padding: 0 1px; }
.sort-btns .sort-asc:hover, .sort-btns .sort-desc:hover { opacity: 1; }
.sort-btns .sort-active { opacity: 1; font-weight: bold; }
</style>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    const today = new Date().toISOString().slice(0, 10);
    try {
        const start = document.getElementById('start_date');
        const end = document.getElementById('end_date');
        if (start && !start.value) start.value = today;
        if (end && !end.value) end.value = today;
    } catch(e) {}

    try {
        const btnSearch = document.getElementById('btnSearch');
        if(btnSearch) {
            btnSearch.addEventListener('click', () => getInOutList({ page: 1 }));
        }
    } catch(e) {}

    try {
        const btnReset = document.getElementById('btnReset');
        if(btnReset) {
            btnReset.addEventListener('click', function() {
                document.getElementById('search_classification').value = '';
                document.getElementById('search_item_name').value = '';
                document.getElementById('start_date').value = '';
                document.getElementById('end_date').value = '';
                getInOutList({ page: 1 });
            });
        }
    } catch(e) {}

    try {
        const btnDeleteSelected = document.getElementById('btnDeleteSelected');
        if(btnDeleteSelected) {
            btnDeleteSelected.addEventListener('click', deleteSelected);
        }
    } catch(e) {}

    try {
        const sortWrap = document.getElementById('inout_sort_order');
        if (sortWrap) {
            sortWrap.querySelector('.sort-asc').addEventListener('click', () => { setInoutSort('asc'); getInOutList({ page: 1 }); });
            sortWrap.querySelector('.sort-desc').addEventListener('click', () => { setInoutSort('desc'); getInOutList({ page: 1 }); });
            setInoutSort('desc');
        }
    } catch(e) {}

    getInOutList({ page: 1 });
});

function setInoutSort(dir) {
    const wrap = document.getElementById('inout_sort_order');
    if (!wrap) return;
    wrap.setAttribute('data-order', dir);
    wrap.querySelectorAll('.sort-asc, .sort-desc').forEach(el => {
        el.classList.remove('sort-active');
        if ((el.classList.contains('sort-asc') && dir === 'asc') || (el.classList.contains('sort-desc') && dir === 'desc')) el.classList.add('sort-active');
    });
}

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getItemsInOutList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getInOutList = async ({
    page,
    per = 10,
    block = 4
}) => {
    let where = `where 1=1`;

    try {
        const classification = document.getElementById('search_classification');
        const itemName = document.getElementById('search_item_name');
        if (classification && classification.value.trim() !== '') {
            where += ` and classification like '%${classification.value.trim().replace(/'/g, "''")}%'`;
        }
        if (itemName && itemName.value.trim() !== '') {
            where += ` and item_name like '%${itemName.value.trim().replace(/'/g, "''")}%'`;
        }
    } catch(e) {}

    try {
        const start_date = document.getElementById('start_date').value;
        const end_date = document.getElementById('end_date').value;
        if (start_date && end_date) {
            where += ` and register_date between '${start_date}' and '${end_date}'`;
        }
    } catch(e) {}

    const orderBy = 'register_date';
    const order = (document.getElementById('inout_sort_order') && document.getElementById('inout_sort_order').getAttribute('data-order')) || 'desc';

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

        getPaging('mes_stock_log', 'uid', where, page, per, block, 'getInOutList');
    } catch (error) {
        console.error('데이터를 가져오는 중 오류가 발생했습니다:', error);
        console.error("error",error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='pd center'>${item.classification}</td>
            <td class='pd center'>${item.item_name}</td>
            <td class='pd center'>${item.item_code}</td>
            <td class='pd center'>${item.standard}</td>
            <td class='pd center'>${comma(item.in_qty)}</td>
            <td class='pd center'>${comma(item.out_qty)}</td>
            <td class='pd center'>${item.register_date}</td>
        </tr>
    `).join('');
};

const modifyItem = (uid) => {
    getter(uid);
    openModal('modalRegisterItem', 900, 500);
}

// 새로고침
const revision = () => {
    try {
        const el = document.getElementById('search_classification'); if (el) el.value = '';
        const el2 = document.getElementById('search_item_name'); if (el2) el2.value = '';
    } catch(e) {}
    getInOutList({ page: 1 });
}
</script>