<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 생산 실적 관리</div> 
    <div class='content-wrapper'>
        <div>
            <div class='title red flex'>
                <div>📅 생산실적 목록 (기간별 실적 요약)</div>
                <div class='btn-box'>
                    <input type='text' class='input datepicker' id='start_date' placeholder='시작일' />
                    <input type='text' class='input datepicker' id='end_date' placeholder='종료일' />
                    <input type='text' class='input' id='item_name' placeholder='품목' />
                    <input type='button' class='btn-middle primary' value='검색' id='btnSearch' />
                </div>
            </div>
            <table class='list mt10'>
                <colgroup>
                    <col />
                    <col />
                    <col />
                    <col />
                    <col />
                    <col />
                </colgroup>
                <thead>
                    <tr>
                        <th class='center'>작업일자 <span id='work_date_sort' class='sort-btns' data-order='desc'><span class='sort-asc' title='오름차순'>▲</span><span class='sort-desc' title='내림차순'>▼</span></span></th>
                        <th>작업자</th>
                        <th>품목</th>
                        <th>품번</th>
                        <th>규격</th>
                        <th>생산수량</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="paging-area mt30 center"></div>
    </div> 
</div>

<input type='hidden' id='currentPage' value='1'>

<style>
.sort-btns { margin-left: 4px; vertical-align: middle; }
.sort-btns .sort-asc, .sort-btns .sort-desc { cursor: pointer; opacity: 0.5; padding: 0 1px; }
.sort-btns .sort-asc:hover, .sort-btns .sort-desc:hover { opacity: 1; }
.sort-btns .sort-active { opacity: 1; font-weight: bold; }
</style>

<script>
window.addEventListener('DOMContentLoaded', async() => {
    const today = new Date().toISOString().slice(0,10);
    try {
        const start = document.getElementById('start_date');
        const end = document.getElementById('end_date');
        if (start && !start.value) start.value = today;
        if (end && !end.value) end.value = today;
    } catch(e) {}

    try {
        const btn = document.getElementById('btnSearch');
        if (btn) {
            btn.addEventListener('click', () => {
                getPeriodProductList({page : 1});
            });
        }
    } catch(e) {}

    try {
        const sortWrap = document.getElementById('work_date_sort');
        if (sortWrap) {
            sortWrap.querySelector('.sort-asc').addEventListener('click', () => { setWorkDateSort('asc'); getPeriodProductList({page : 1}); });
            sortWrap.querySelector('.sort-desc').addEventListener('click', () => { setWorkDateSort('desc'); getPeriodProductList({page : 1}); });
            setWorkDateSort('desc');
        }
    } catch(e) {}

    await getPeriodProductList({page : document.getElementById('currentPage').value});
});

function setWorkDateSort(dir) {
    const wrap = document.getElementById('work_date_sort');
    if (!wrap) return;
    wrap.setAttribute('data-order', dir);
    wrap.querySelectorAll('.sort-asc, .sort-desc').forEach(el => {
        el.classList.remove('sort-active');
        if ((el.classList.contains('sort-asc') && dir === 'asc') || (el.classList.contains('sort-desc') && dir === 'desc')) {
            el.classList.add('sort-active');
        }
    });
}

const getPeriodProductList = async({page}) => {  
    document.getElementById('currentPage').value = page;
    let where = `where 1=1`;

    const start_date = document.getElementById('start_date').value;
    const end_date = document.getElementById('end_date').value;
    const itemName = document.getElementById('item_name').value;

    if(start_date && end_date) {
        where += ` and work_date between '${start_date}' and '${end_date}'`;
    }
    if (itemName) {
        where += ` and item_name like '%${itemName}%'`;
    }
    
    const sortWrap = document.getElementById('work_date_sort');
    const orderBy = 'work_date';
    const order = sortWrap ? (sortWrap.getAttribute('data-order') || 'desc') : 'desc';

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDailyWorkList');
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', 15);
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

        getPaging('mes_daily_work', 'uid', where, page, 15, 4, 'getPeriodProductList');
    } catch (error) {
        console.error('사원 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    let totalQty = 0;
    const rows = data.data.map(item => {
        totalQty += Number(item.work_qty) || 0;
        return `
            <tr>
                <td class='pd center'>${item.work_date}</td>
                <td class='pd center'>${item.worker}</td>
                <td class='pd center'>${item.item_name}</td>
                <td class='pd center'>${item.item_code}</td>
                <td class='pd center'>${item.standard}</td>
                <td class='pd center'>${item.work_qty}</td>
            </tr>
        `;
    }).join('');

    // 합계 행 추가 (마지막에)
    const totalRow = `
        <tr style="font-weight:bold;background:#f5f5f5;">
            <td class='pd center' colspan='5'>합계</td>
            <td class='pd cente'><span class='red'>${comma(totalQty)}</span></td>
        </tr>
    `;

    return rows + totalRow;
};
</script>