<?php
include "head.php";
?>
    
<main>
    <div class="title-container">
        <div class="title-wrapper">
            <div class="title">공지사항</div>            
            <div class="summary">여러분들을 환영합니다.</div>
        </div>
    </div>
    <div class="board-container">
        <div class="board-wrapper">
            <div class="search-section">
                <div></div>
                <div class="search-box">
                    <input type="text" id="searchText" placeholder="제목" />
                    <i class="fa fa-search" aria-hidden="true"></i>
                </div>
            </div>
            <div class="table-section">
                <table id="list" class='list'>
                    <colgroup>
                        <col width="150" />
                        <col />
                        <col width="150" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>제목</th>
                            <th>등록일</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class='paging-area center'></div>
        </div>
    </div>
</main>

<input type='hidden' id='page' value="<?=$_GET['page']?>" />

<?php
include "foot.php";
?>

<script>
const currentPage = document.getElementById('page');

window.addEventListener('DOMContentLoaded', () => {
    try {
        const btnSearch = document.querySelector('.fa-search');
        btnSearch.addEventListener('click', function() {
            getNoticeFixedList();
        });
    } catch(e) {}

    try {
        document.getElementById('searchText').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {  // Enter 키를 감지
                getNoticeFixedList();
            }
        });
    } catch(e) {}

    let page = (currentPage.value == '') ? 1 : currentPage.value;
    getNoticeFixedList();
});



const getNoticeFixedList = async () => {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('controller', 'agency');
        formData.append('mode', 'getFixedNotice');

        fetch('./webadm/handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector('.list tbody');
            if (!tableBody) {
                console.error('tableBody를 찾을 수 없습니다.');
                reject('tableBody를 찾을 수 없습니다.');
                return;
            }

            tableBody.innerHTML = generateFixedTableContent(data);
            console.log('고정된 공지사항이 성공적으로 로드되었습니다.');
            getData({page:1});
            resolve();
        })
        .catch(error => {
            console.error('데이터를 가져오는 중 오류가 발생했습니다:', error);
            reject(error);  // 에러 발생 시 reject 호출
        });
    });
};

const generateFixedTableContent = (data) => {
    const listData = data;

    if (!listData || listData.length === 0) {
        return `<tr><td class='center pd10' colspan='8'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return listData.map((item, index) => {
        let dateTime = item.registerDateTime;
        let dateOnly = dateTime.split(' ')[0];

        return `
            <tr style='background-color:#ffe3dc'> 
                <td class='center'><i class="fa fa-flag-checkered"></i></td>  
                <td class='left' style='width:770px'><a href='boardView.php?uid=${item.uid}&page=${document.getElementById('page').value}'>${item.title}</a></td>
                <td class='center' style='width:200px'>${dateOnly}</td>
            </tr>
        `;
    }).join('');
};

// 상수 정의
const CONTROLLER = 'agency';
const MODE = 'getNoticeList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'asc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getData = async ({ page, per = 5, block = 2, where = `where fixed!='Y'`, orderBy = DEFAULT_ORDER_BY, order = DEFAULT_ORDER }) => {
    
    currentPage.value = page;
    return new Promise((resolve, reject) => {
        const searchText = document.getElementById('searchText');

        if(searchText.value != '') {
            where += ` and title like '%${searchText.value}%'`;
        }

        const formData = new FormData();
        formData.append('controller', CONTROLLER);
        formData.append('mode', MODE);
        formData.append('where', where);
        formData.append('page', page);
        formData.append('per', per);
        formData.append('orderby', orderBy);
        formData.append('asc', order);

        fetch('./webadm/handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector('#list tbody');
            tableBody.innerHTML += generateTableContent(data, per, page);

            getPaging('sk_board', 'uid', where, page, per, block, 'getData');
            resolve();  // 디바이스 리스트 설정이 완료되면 resolve 호출
        })
        .catch(error => {
            console.error('데이터를 가져오는 중 오류가 발생했습니다:', error);
            reject(error);  // 에러 발생 시 reject 호출
        });
    });
};

const generateTableContent = (data, pageSize, currentPage) => {
    const totalCount = data.totalCount;
    const listData = data.data;

    if (!listData || listData.length === 0) {
        return `<tr><td class='center' colspan='8'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    // 페이지의 첫 번째 항목 번호 계산
    const startNo = totalCount - (currentPage - 1) * pageSize;

    return listData.map((item, index) => {
        let no = startNo - index;  // 페이지 첫 번호에서 index를 빼서 no 계산
        let dateTime = item.registerDateTime;
        let dateOnly = dateTime.split(' ')[0];

        return `
            <tr>     
                <td class='center'>${no}</td>
                <td class='left'><a href='boardView.php?uid=${item.uid}&page=${document.getElementById('page').value}'>${item.title}</a></td>
                <td class='center'>${dateOnly}</td>
            </tr>
        `;
    }).join('');
};
</script>