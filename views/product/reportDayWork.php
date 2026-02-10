<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 작업일보 관리</div> 
    <div class='content-wrapper'>
        <div>
            <div class='title red flex'>
                <div>생산지시서 목록</div>
                <div class='btn-box'>
                    <input type='text' class='input datepicker' name='start_order_date' id='start_order_date' placeholder='시작일'/>
                    <input type='text' class='input datepicker' name='end_order_date' id='end_order_date' placeholder='종료일'/>
                    <input type='button' class='btn primary' value='검색' onclick='searchOrderList()' />
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
                        <th>작업지시일</th>
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
                    <input type='button' class='btn primary' value='검색' onclick='searchWorkReportList()' />
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
                        <th>작업일</th>
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

<?php
    include "./views/modal/modalRegisterWorkReport.php";
    ?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    getWorkOrderList({page : document.getElementById('currentPage').value});
    getWorkReportList({page : document.getElementById('currentWorkReportPage').value});
});

const searchOrderList = () => {
    const start_order_date = document.getElementById('start_order_date').value;
    const end_order_date = document.getElementById('end_order_date').value;

    if(start_order_date && end_order_date) {
        getWorkOrderList({page : document.getElementById('currentPage').value});
    }
}

const getWorkOrderList = async ({page, order_uid = null}) => {    
    document.getElementById('currentPage').value = page;
    //let where = `where status='생산대기'`;
    let where = `where 1=1`;
    const start_order_date = document.getElementById('start_order_date').value;
    const end_order_date = document.getElementById('end_order_date').value;

    if(order_uid) {
        where += ` and order_uid=${order_uid}`;
    }

    if(start_order_date && end_order_date) {
        where += ` and order_date between '${start_order_date}' and '${end_order_date}'`;
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
        console.error('사원 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
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

    if(start_work_date && end_work_date) {
        getWorkReportList({page : document.getElementById('currentWorkReportPage').value});
    }
}

const getWorkReportList = async ({page, work_order_uid = null}) => {
    document.getElementById('currentWorkReportPage').value = page;
    let where = `where 1=1`;
    const start_work_date = document.getElementById('start_work_date').value;
    const end_work_date = document.getElementById('end_work_date').value;

    if(start_work_date && end_work_date) {
        where += ` and work_date between '${start_work_date}' and '${end_work_date}'`;
    }

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getWorkReportList');
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
</script>