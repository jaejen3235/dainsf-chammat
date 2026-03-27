<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 로그인 이력</div>
    <div class='content-wrapper'>
        <div class='right' id='login-report-search'>
            <input type='text' class='input datepicker' id='start_date' placeholder='시작일' />
            <input type='text' class='input datepicker' id='end_date' placeholder='종료일' />
            <input type='text' class='input' id='searchText' placeholder='로그인 아이디' />
            <input type='button' class='btn-middle primary' value='검색' id='btnSearch' />
            <input type='button' class='btn-middle success' value='엑셀 다운로드' id='btnExcelDownload' />
        </div>
        <div>
            <table class='list'>
                <colgroup>
                    <col />
                    <col />
                </colgroup>
                <thead>
                    <tr>
                        <th class='center'>로그인 일시 <span id='login_date_sort' class='sort-btns' data-order='desc'><span class='sort-asc' title='오름차순'>▲</span><span class='sort-desc' title='내림차순'>▼</span></span></th>
                        <th>로그인 아이디</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="paging-area mt20"></div>
    </div>
</div>

<style>
#login-report-search { margin-bottom: 12px; }
.sort-btns { margin-left: 4px; vertical-align: middle; }
.sort-btns .sort-asc, .sort-btns .sort-desc { cursor: pointer; opacity: 0.5; padding: 0 1px; }
.sort-btns .sort-asc:hover, .sort-btns .sort-desc:hover { opacity: 1; }
.sort-btns .sort-active { opacity: 1; font-weight: bold; }
</style>

<script>
let currentOrderBy = 'registerDate';
let currentOrder = 'desc';

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
        if (btnSearch) {
            btnSearch.addEventListener('click', () => getLoginReport({page : 1, orderBy: currentOrderBy, order: currentOrder}));
        }
    } catch(e) {}

    try {
        const btnExcelDownload = document.getElementById('btnExcelDownload');
        if (btnExcelDownload) {
            btnExcelDownload.addEventListener('click', downloadLoginReportExcel);
        }
    } catch(e) {}

    try {
        const searchText = document.getElementById('searchText');
        if (searchText) {
            searchText.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    getLoginReport({page : 1, orderBy: currentOrderBy, order: currentOrder});
                }
            });
        }
    } catch(e) {}

    try {
        const sortWrap = document.getElementById('login_date_sort');
        if (sortWrap) {
            sortWrap.querySelector('.sort-asc').addEventListener('click', () => {
                setLoginDateSort('asc');
                getLoginReport({ page: 1, orderBy: currentOrderBy, order: currentOrder });
            });
            sortWrap.querySelector('.sort-desc').addEventListener('click', () => {
                setLoginDateSort('desc');
                getLoginReport({ page: 1, orderBy: currentOrderBy, order: currentOrder });
            });
            setLoginDateSort('desc');
        }
    } catch(e) {}

    getLoginReport({page : 1, orderBy: currentOrderBy, order: currentOrder});
});

function setLoginDateSort(dir) {
    const wrap = document.getElementById('login_date_sort');
    if (!wrap) return;
    currentOrderBy = 'registerDate';
    currentOrder = dir;
    wrap.setAttribute('data-order', dir);
    wrap.querySelectorAll('.sort-asc, .sort-desc').forEach(el => {
        el.classList.remove('sort-active');
        if ((el.classList.contains('sort-asc') && dir === 'asc') || (el.classList.contains('sort-desc') && dir === 'desc')) {
            el.classList.add('sort-active');
        }
    });
}

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getLoginReport';
const DEFAULT_ORDER_BY = 'registerDate';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getLoginReport = async ({
    page,
    per = 15,
    block = 4,
    orderBy = currentOrderBy,
    order = currentOrder
}) => {
    currentOrderBy = orderBy;
    currentOrder = order;
    let where = `where 1=1`;

    try {
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const searchText = document.getElementById('searchText');
        if (startDate && startDate.value) where += ` and registerDate >= '${startDate.value}'`;
        if (endDate && endDate.value) where += ` and registerDate <= '${endDate.value}'`;
        if (searchText && searchText.value.trim() !== '') {
            const q = searchText.value.trim().replace(/'/g, "''");
            where += ` and loginId like '%${q}%'`;
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

        getPaging('mes_user_login', 'uid', where, page, per, block, 'getLoginReport');
    } catch (error) {
        console.error('대리점 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='2'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='pd center'>${item.registerDate}</td>
            <td class='pd center'>${item.loginId}</td>
        </tr>
    `).join('');
};

const downloadLoginReportExcel = () => {
    let where = `where 1=1`;

    try {
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const searchText = document.getElementById('searchText');
        if (startDate && startDate.value) where += ` and registerDate >= '${startDate.value}'`;
        if (endDate && endDate.value) where += ` and registerDate <= '${endDate.value}'`;
        if (searchText && searchText.value.trim() !== '') {
            const q = searchText.value.trim().replace(/'/g, "''");
            where += ` and loginId like '%${q}%'`;
        }
    } catch(e) {}

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = './handler.php';
    form.target = '_blank';
    form.style.display = 'none';

    const fields = {
        controller: 'mes',
        mode: 'getLoginReportExcel',
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