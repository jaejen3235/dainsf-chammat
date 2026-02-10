<div class='main-container'>
        <div class='search-wrapper'>
            <div class='search-box'>
                <div class='search-section'>
                    <div class='search-input'>
                        <input type="text" id='searchText' placeholder="검색">
                        <button class='btn-large primary' id='btnSearch'>검색</button>
                        <button class='btn-large success revision' id='btnRevision'>새로고침</button>
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
                        <col width='200' />
                        <col width='150' />
                        <col width='150' />
                        <col width='100' />
                        <col width='150' />
                        <col width='150' />                   
                        <col />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>거래처</th>
                            <th>품목</th>
                            <th>품번</th>
                            <th>규격</th>
                            <th>출하수량</th>
                            <th>출하일</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="paging-area mt20"></div>
        </div>
    </div>
</div>

<?php
include "./views/modal/modalModifyDelivery.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    // 검색
    try {
        const btnSearch = document.getElementById('btnSearch');
        if(btnSearch) {
            btnSearch.addEventListener('click', () => {
                getDeliveryReportList({page : 1});
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
                getDeliveryReportList({page:1});
            }
        });
    } catch(e) {}
    
    getDeliveryReportList({page : 1});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getDeliveryReportList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getDeliveryReportList = async ({
    page,
    per = 13,
    block = 4,
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {    
    let where = `where 1=1`;

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

        getPaging('mes_delivery_report', 'uid', where, page, per, block, 'getDeliveryReportList');
    } catch (error) {
        console.error('사원 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.account_name}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${comma(item.delivery_qty)}</td>
            <td class='center'>${item.delivery_date}</td>
            <td class='center'>
                <input type='button' class='btn-small primary' value='수정' onclick='updateDeliveryReport(${item.uid})' />
                <input type='button' class='btn-small danger' value='삭제' onclick='deleteDeliveryReport(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const updateDeliveryReport = async (uid) => {
    getter(uid);
    openModal('modalModifyDelivery', 1200, 450);
}

const deleteDeliveryReport = async (uid) => {
    if(!confirm('출하 내역 데이터를 삭제하시겠습니까?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'deleteDeliveryReport');
    formData.append('uid', uid);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if(data.result === 'success') {
            getDeliveryReportList({page:1});
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('출하 내역 데이터를 삭제하는 중 오류가 발생했습니다:', error);
    }
}


// 새로고침
const revision = () => {
    document.getElementById('searchText').value = '';
    document.getElementById('searchType').value = '0';
    getDeliveryReportList({page:1});
}
</script>