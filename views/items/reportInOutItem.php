<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 자재 수불부</div> 
    <div class='content-wrapper'>
        <div class='right'>
            <input type='text' class='input datepicker' id='start_date' placeholder='시작일' />
            <input type='text' class='input datepicker' id='end_date' placeholder='종료일' />
            <input type='button' class='btn-middle primary' value='검색' id='btnSearch' />
            <input type='button' class='btn-middle success' value='초기화' id='btnReset' />
        </div>
        <table class='list mt10'>
            <colgroup>
                <col width='100' />
                <col />
                <col width='200' />
                <col width='200' />
                <col width='150' />
                <col width='150' />
            </colgroup>
            <thead>
                <tr>
                    <th>구분</th>
                    <th>품목명</th>
                    <th>품목코드</th>
                    <th>품목규격</th>
                    <th>입고수량</th>
                    <th>출고수량</th>
                    <th>입/출고 날짜</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="paging-area mt20"></div>
    </div>
</div>


<script>
window.addEventListener('DOMContentLoaded', ()=>{
    // 검색
    try {
        const btnSearch = document.getElementById('btnSearch');
        if(btnSearch) {
            btnSearch.addEventListener('click', () => {
                getInOutList({page : 1});
            });
        }
    } catch(e) {}


    try {
        const btnReset = document.getElementById('btnReset');
        if(btnReset) {
            btnReset.addEventListener('click', function() {
                document.getElementById('start_date').value = '';
                document.getElementById('end_date').value = '';
                getInOutList({page : 1});
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
                getInOutList({page:1});
            }
        });
    } catch(e) {}

    getInOutList({page : 1});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getItemsInOutList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getInOutList = async ({
    page,
    per = 10,
    block = 4,
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {    
    let where = `where 1=1`;

    // 검색어가 있다면
    try {
        const start_date = document.getElementById('start_date').value;
        const end_date = document.getElementById('end_date').value;
        if(start_date && end_date) {
            where += ` and register_date between '${start_date}' and '${end_date}'`;
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

        getPaging('mes_items_inout', 'uid', where, page, per, block, 'getInOutList');
    } catch (error) {
        console.error('품목 데이터를 가져오는 중 오류가 발생했습니다:', error);
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
    document.getElementById('searchText').value = '';
    getInOutList({page:1});
}
</script>