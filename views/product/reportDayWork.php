<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 작업일보 관리</div> 
    <div class='content-wrapper'>
        <div>
            <div class='title red flex'>
                <div>생산지시서 목록</div>
                <div class='btn-box'>
                    <input type='text' class='input datepicker' name='start_order_date' id='start_order_date' placeholder='시작일'/>
                    <input type='text' class='input datepicker' name='end_order_date' id='end_order_date' placeholder='종료일'/>
                    <select class='input' id='order_status_filter'>
                        <option value='ALL'>상태 전체</option>
                        <option value='생산대기'>생산대기</option>
                        <option value='작업중'>작업중</option>
                        <option value='부분작업완료'>부분작업완료</option>
                        <option value='작업완료'>작업완료</option>
                    </select>
                    <input type='text' class='input' name='order_item_name' id='order_item_name' placeholder='작업품목, 거래처, 지시 수량'/>
                    <input type='button' class='btn primary' value='검색' onclick='searchOrderList()' />
                    <input type='button' class='btn' value='엑셀 다운로드' id='btnOrderExcelDownload' />
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
                </colgroup>
                <thead>
                    <tr>
                        <th>생산구분</th>
                        <th class='center'>작업지시일 <span id='order_date_sort' class='sort-btns' data-order='desc'><span class='sort-asc' title='오름차순'>▲</span><span class='sort-desc' title='내림차순'>▼</span></span></th>
                        <th>거래처</th>
                        <th>작업품목</th>
                        <th>품번</th>
                        <th>규격</th>
                        <th>지시 수량</th>
                        <th>작업한 수량</th>                   
                        <th>합격 수량</th>                        
                        <th>불량 수량</th>                        
                        <th>검사 대기 수량</th>                        
                        <th>상태</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="work-order-paging-area mt30 center"></div>
    </div>
    <div class='content-wrapper mt20'>
        <div>
            <div class='flex'>
                <div class='title red'>작업일보 목록</div>
                <div class='btn-box'>
                    <input type='text' class='input datepicker' name='start_work_date' id='start_work_date' placeholder='시작일'/>
                    <input type='text' class='input datepicker' name='end_work_date' id='end_work_date' placeholder='종료일'/>
                    <input type='text' class='input' name='work_item_name' id='work_item_name' placeholder='작업자, 품목'/>
                    <input type='button' class='btn primary' value='검색' onclick='searchWorkReportList()' />
                    <input type='button' class='btn' value='엑셀 다운로드' id='btnWorkReportExcelDownload' />
                </div>
            </div>
            <table class='work-report-list list mt10'>
                <colgroup>
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
                        <th class='center'>작업일 <span id='work_report_date_sort' class='sort-btns' data-order='desc'><span class='sort-asc' title='오름차순'>▲</span><span class='sort-desc' title='내림차순'>▼</span></span></th>
                        <th>작업자</th>
                        <th>품목</th>
                        <th>품번</th>
                        <th>규격</th>                        
                        <th>작업수량</th>
                        <th>품질검사상태</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>
                </tbody>
            </table>
        </div>
        <div class="work-report-paging-area mt30 center"></div>
    </div>    
</div>

<input type='hidden' id='currentPage' value='1'>
<input type='hidden' id='currentWorkReportPage' value='1'>
<input type='hidden' id='orderDateSortOrder' value='desc'>
<input type='hidden' id='workReportDateSortOrder' value='desc'>

<style>
.sort-btns { margin-left: 4px; vertical-align: middle; }
.sort-btns .sort-asc, .sort-btns .sort-desc { cursor: pointer; opacity: 0.5; padding: 0 1px; }
.sort-btns .sort-asc:hover, .sort-btns .sort-desc:hover { opacity: 1; }
.sort-btns .sort-active { opacity: 1; font-weight: bold; }
</style>

<?php
    include "./views/modal/modalRegisterWorkReport.php";
    ?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    const today = new Date().toISOString().slice(0,10);

    try {
        const startOrder = document.getElementById('start_order_date');
        const endOrder = document.getElementById('end_order_date');
        if (startOrder && !startOrder.value) startOrder.value = today;
        if (endOrder && !endOrder.value) endOrder.value = today;
        const statusFilter = document.getElementById('order_status_filter');
        if (statusFilter) statusFilter.value = '작업완료'; // 초기 진입 시 생산완료(작업완료)만 조회
    } catch(e) {}

    try {
        const startWork = document.getElementById('start_work_date');
        const endWork = document.getElementById('end_work_date');
        if (startWork && !startWork.value) startWork.value = today;
        if (endWork && !endWork.value) endWork.value = today;
    } catch(e) {}

    getWorkOrderList({page : document.getElementById('currentPage').value});
    getWorkReportList({page : document.getElementById('currentWorkReportPage').value});

    try {
        const btnOrderExcelDownload = document.getElementById('btnOrderExcelDownload');
        if (btnOrderExcelDownload) {
            btnOrderExcelDownload.addEventListener('click', downloadWorkOrderExcel);
        }
    } catch(e) {}

    try {
        const btnWorkReportExcelDownload = document.getElementById('btnWorkReportExcelDownload');
        if (btnWorkReportExcelDownload) {
            btnWorkReportExcelDownload.addEventListener('click', downloadWorkReportExcel);
        }
    } catch(e) {}


    try {
        document.getElementById('order_item_name').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {  // Enter 키를 감지
                searchOrderList();
            }
        });
    } catch(e) {}
    try {
        document.getElementById('work_item_name').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {  // Enter 키를 감지
                searchWorkReportList();
            }
        });
    } catch(e) {}

    // 생산지시서 목록: 작업지시일 정렬
    try {
        const wrap = document.getElementById('order_date_sort');
        if (wrap) {
            wrap.querySelector('.sort-asc').addEventListener('click', () => {
                setOrderDateSort('asc');
                getWorkOrderList({page:1});
            });
            wrap.querySelector('.sort-desc').addEventListener('click', () => {
                setOrderDateSort('desc');
                getWorkOrderList({page:1});
            });
            setOrderDateSort('desc');
        }
    } catch(e) {}

    // 작업일보 목록: 작업일 정렬
    try {
        const wrap = document.getElementById('work_report_date_sort');
        if (wrap) {
            wrap.querySelector('.sort-asc').addEventListener('click', () => {
                setWorkReportDateSort('asc');
                getWorkReportList({page:1});
            });
            wrap.querySelector('.sort-desc').addEventListener('click', () => {
                setWorkReportDateSort('desc');
                getWorkReportList({page:1});
            });
            setWorkReportDateSort('desc');
        }
    } catch(e) {}
});

function setOrderDateSort(dir) {
    const wrap = document.getElementById('order_date_sort');
    const hidden = document.getElementById('orderDateSortOrder');
    if (!wrap) return;
    wrap.setAttribute('data-order', dir);
    if (hidden) hidden.value = dir;
    wrap.querySelectorAll('.sort-asc, .sort-desc').forEach(el => {
        el.classList.remove('sort-active');
        if ((el.classList.contains('sort-asc') && dir === 'asc') || (el.classList.contains('sort-desc') && dir === 'desc')) {
            el.classList.add('sort-active');
        }
    });
}

function setWorkReportDateSort(dir) {
    const wrap = document.getElementById('work_report_date_sort');
    const hidden = document.getElementById('workReportDateSortOrder');
    if (!wrap) return;
    wrap.setAttribute('data-order', dir);
    if (hidden) hidden.value = dir;
    wrap.querySelectorAll('.sort-asc, .sort-desc').forEach(el => {
        el.classList.remove('sort-active');
        if ((el.classList.contains('sort-asc') && dir === 'asc') || (el.classList.contains('sort-desc') && dir === 'desc')) {
            el.classList.add('sort-active');
        }
    });
}

const searchOrderList = () => {
    const start_order_date = document.getElementById('start_order_date').value;
    const end_order_date = document.getElementById('end_order_date').value;
    const status = document.getElementById('order_status_filter').value;
    const itemName = document.getElementById('order_item_name').value;

    if(start_order_date && end_order_date) {
        getWorkOrderList({page : document.getElementById('currentPage').value, status, itemName});
    }
}

const getWorkOrderList = async ({page, order_uid = null, status = null, itemName = null}) => {    
    document.getElementById('currentPage').value = page;
    let where = `where 1=1`;
    const start_order_date = document.getElementById('start_order_date').value;
    const end_order_date = document.getElementById('end_order_date').value;

    if(order_uid) {
        where += ` and order_uid=${order_uid}`;
    }

    if(start_order_date && end_order_date) {
        where += ` and order_date between '${start_order_date}' and '${end_order_date}'`;
    }

    // 상태 조건 (ALL 선택 시 조건 없음)
    const statusFilter = status || document.getElementById('order_status_filter').value;
    if (statusFilter && statusFilter !== 'ALL') {
        where += ` and status='${statusFilter}'`;
    }

    // 작업품목/거래처/지시수량 통합 LIKE 조건
    const itemFilter = (itemName !== null ? itemName : document.getElementById('order_item_name').value) || '';
    if (itemFilter) {
        where += ` and (item_name like '%${itemFilter}%' or account_name like '%${itemFilter}%' or CAST(order_qty AS CHAR) like '%${itemFilter}%')`;
    }
    
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getWorkOrderList');
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', 5);
    formData.append('orderby', 'order_date');
    formData.append('asc', (document.getElementById('orderDateSortOrder')?.value || 'desc'));

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

const downloadWorkOrderExcel = () => {
    let where = `where 1=1`;
    const start_order_date = document.getElementById('start_order_date').value;
    const end_order_date = document.getElementById('end_order_date').value;
    const status = document.getElementById('order_status_filter').value;
    const itemFilter = document.getElementById('order_item_name').value || '';

    if(start_order_date && end_order_date) {
        where += ` and order_date between '${start_order_date}' and '${end_order_date}'`;
    }

    if (status && status !== 'ALL') {
        where += ` and status='${status}'`;
    }

    if (itemFilter) {
        where += ` and (item_name like '%${itemFilter}%' or account_name like '%${itemFilter}%' or CAST(order_qty AS CHAR) like '%${itemFilter}%')`;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = './handler.php';
    form.target = '_blank';
    form.style.display = 'none';

    const fields = {
        controller: 'mes',
        mode: 'getWorkOrderListExcel',
        where,
        orderby: 'order_date',
        asc: (document.getElementById('orderDateSortOrder')?.value || 'desc')
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

const generateWorkOrderTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.classification}</td>
            <td class='center'>${item.order_date}</td>
            <td class='center'>${item.account_name}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${comma(item.order_qty)}</td>
            <td class='center'>${comma(item.work_qty)}</td>
            <td class='center'>${comma(item.pass_qty)}</td>
            <td class='center'>${comma(item.fail_qty)}</td>
            <td class='center'>${comma(item.quality_qty)}</td>
            <td class='center'>${item.status}</td>
        </tr>
    `).join('');
};   


const startWork = (uid, button) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'startWork');
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
            if(data.result == 'success') {
                // 작업시작 버튼 숨기기
                button.style.display = 'none';
                // 같은 행의 작업종료 버튼 보이기
                const row = button.closest('tr');
                const endWorkButton = row.querySelector('input[value="작업종료"]');
                if(endWorkButton) {
                    endWorkButton.style.display = '';
                }
            } else {
                alert(data.message);
            }
        }
    })
    .catch(error => console.log(error));
}


const endWork = (uid, button) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'endWork');
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
            if(data.result == 'success') {
                // 작업종료 버튼 숨기기
                button.style.display = 'none';
                // 같은 행의 작업시작 버튼 보이기
                const row = button.closest('tr');
                const startWorkButton = row.querySelector('input[value="작업시작"]');
                if(startWorkButton) {
                    startWorkButton.style.display = '';
                }
            } else {
                alert(data.message);
            }
        }
    })
    .catch(error => console.log(error));
}


const searchWorkReportList = () => {
    const start_work_date = document.getElementById('start_work_date').value;
    const end_work_date = document.getElementById('end_work_date').value;
    const itemName = document.getElementById('work_item_name').value;

    if(start_work_date && end_work_date) {
        getWorkReportList({page : document.getElementById('currentWorkReportPage').value, itemName});
    }
}

const getWorkReportList = async ({page, work_order_uid = null, itemName = null, orderBy = 'work_date', order = (document.getElementById('workReportDateSortOrder')?.value || 'desc')}) => {
    document.getElementById('currentWorkReportPage').value = page;
    let where = `where 1=1`;
    const start_work_date = document.getElementById('start_work_date').value;
    const end_work_date = document.getElementById('end_work_date').value;

    if(start_work_date && end_work_date) {
        where += ` and work_date between '${start_work_date}' and '${end_work_date}'`;
    }

    const itemFilter = (itemName !== null ? itemName : document.getElementById('work_item_name').value) || '';
    if (itemFilter) {
        where += ` and (item_name like '%${itemFilter}%' or worker like '%${itemFilter}%')`;
    }

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getWorkReportList');
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', 5);
    formData.append('orderby', orderBy);
    formData.append('asc', order);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.work-report-list tbody');
        tableBody.innerHTML = generateWorkReportTableContent(data); 
        
        getPagingTarget('mes_daily_work', 'uid', where, page, 5, 4, 'getWorkReportList', 'work-report-paging-area');
    } catch (error) {
        console.error('작업일보 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};


const generateWorkReportTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.map(item => `
        <tr>
            <td class='center'>${item.work_date}</td>
            <td class='center'>${item.worker}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>         
            <td class='center'>${comma(item.work_qty)}</td>
            <td class='center'>${item.quality_status}</td>
            <td class='center'>
                <input type='button' class='btn-small danger' value='삭제' onclick='deleteWorkReport(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const deleteWorkReport = async (uid) => {
    if(!confirm('정말 삭제하시겠습니까?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'deleteWorkReport');
    formData.append('uid', uid);

    const response = await fetch('./handler.php', {
        method: 'POST',
        body: formData
    });
    const data = await response.json();
    if(data.result == 'success') {        
        getWorkReportList({page : document.getElementById('currentWorkReportPage').value});
    }
        
    alert(data.message);
};

const downloadWorkReportExcel = () => {
    let where = `where 1=1`;
    const start_work_date = document.getElementById('start_work_date').value;
    const end_work_date = document.getElementById('end_work_date').value;
    const keyword = document.getElementById('work_item_name').value || '';

    if(start_work_date && end_work_date) {
        where += ` and work_date between '${start_work_date}' and '${end_work_date}'`;
    }

    if (keyword) {
        where += ` and (item_name like '%${keyword}%' or worker like '%${keyword}%')`;
    }

    const order = document.getElementById('workReportDateSortOrder')?.value || 'desc';

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = './handler.php';
    form.target = '_blank';
    form.style.display = 'none';

    const fields = {
        controller: 'mes',
        mode: 'getWorkReportListExcel',
        where,
        orderby: 'work_date',
        asc: order
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