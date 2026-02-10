<div class='main-container'>   
    <div class="page-title"><i class='bx bxs-food-menu'></i> 수입 검사 관리</div>  
    <div class='search-wrapper'>
        <div class='search-box'>
            <div class='search-section'>
                <div class='search-input'>
                    <input type="text" class='datepicker'id='start_date' placeholder="시작일">
                    <input type="text" class='datepicker'id='end_date' placeholder="종료일">
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
            <table class='list'>
                <colgroup>                    
                    <col width='150' />
                    <col width='150' />
                    <col width='150' />
                    <col width='150' />
                    <col width='150' />
                    <col width='150' />
                    <col width='150' />
                    <col width='150' />
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
                <tbody></tbody>
            </table>
        </div>
        <div class="paging-area mt20"></div>
    </div>
</div>

<?php
include "./views/modal/modalRegisterShortCircuitTest.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    // 검색
    try {
        const btnSearch = document.getElementById('btnSearch');
        if(btnSearch) {
            btnSearch.addEventListener('click', () => {
                searchDateList();
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
                getWorkReportList({page:1});
            }
        });
    } catch(e) {}

    getWorkReportList({page : 1});
});

const searchDateList = () => {
    const start_date = document.getElementById('start_date').value;
    const end_date = document.getElementById('end_date').value;

    if(start_date && end_date) {
        getWorkReportList({page : 1});
    }
}

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getWorkReportList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getWorkReportList = async ({
    page,
    per = 10,
    block = 4,
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {
    let where = `where quality_status = '품질검사대기'`;
    const start_date = document.getElementById('start_date').value;
    const end_date = document.getElementById('end_date').value;

    // 검색어가 있다면
    if(start_date && end_date) {
        where += ` and work_date between '${start_date}' and '${end_date}'`;
    }
    

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

        getPaging('mes_daily_work', 'uid', where, page, per, block, 'getWorkReportList');
    } catch (error) {
        console.error('불량사유 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.map(item => `
        <tr>
            <td class='center'>${item.work_date}</td>
            <td class='center'>${item.worker}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${item.work_qty}</td>
            <td class='center'>${item.quality_status}</td>
            <td class='center'>
                <input type='button' class='btn-small grey' value='품질검사 등록' onclick='registerQualityTest(${item.uid})' />                
            </td>
        </tr>
    `).join('');
};

const registerQualityTest = (uid) => {
    getter(uid);
    openModal('modalRegisterShortCircuitTest', 900, 560);
    
    // 모달이 열린 후 datepicker 초기화
    setTimeout(() => {
        if (typeof jQuery !== 'undefined' && jQuery.fn.datepicker) {
            jQuery('#test_date').datepicker({
                dateFormat: 'yy-mm-dd',
                showButtonPanel: true,
                changeMonth: true,
                changeYear: true
            });
        }
    }, 100);
}

// 새로고침
const revision = () => {
    document.getElementById('searchText').value = '';
    getWorkReportList({page:1});
}
</script>