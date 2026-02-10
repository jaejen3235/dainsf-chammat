<div class='main-container'>
    <div class='title-wrapper'>전화상담 관리</div>
        <div class='search-wrapper'>
            <div class='search-box'>
                <div class='search-section'>
                    <input type="text" id='searchText' placeholder="검색">
                    <i class="fa fa-calendar"></i>
                </div>~
                <div class='search-section'>
                    <input type="text" id='searchText' placeholder="검색">
                    <i class="fa fa-calendar"></i>
                </div>
                <div>
                    <select id='category'>
                        <option value='0'>진행상황 선택</option>
                        <option value='5G'>5G</option>
                        <option value='LTE'>LTE</option>
                    </select>
                </div>
                <div class='search-section'>
                    <input type="text" id='searchText' placeholder="검색">
                    <i class="fa fa-search hands" aria-hidden="true"></i>
                </div>
            </div>
            <div class='button-box'>
                <!--
                <input type='button' class='btn-large orange' id='btnRegister' value='공지사항 등록' />
                <input type='button' class='btn-large' id='btnDeleteSelected' value='선택 삭제' />
                -->
            </div>
        </div>
        <div class='content-wrapper'>
            <div>
                <table class='list'>
                    <colgroup>
                        <col>
                        <col>
                        <col>
                        <col>
                        <col>
                        <col>
                        <col>
                    </colgroup>
                    <thead>
                        <tr>
                            <th>번호</th>
                            <th>신청일자</th>
                            <th>이름</th>
                            <th>연락처</th>
                            <th>내용</th>
                            <th>진행상황</th>
                            <th>삭제</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="paging-area mt20"></div>
        </div>
    </div>
</div>       

<script>
window.addEventListener('DOMContentLoaded', () => {
    // 새로고침
    try {
        const btnRefresh = document.getElementById('btnRefresh');
        btnRefresh.addEventListener('click', refresh);
    } catch(e) {}

    // 검색 버튼
    try {
        const btnSearch = document.getElementById('btnSearch');
        btnSearch.addEventListener('click', function() {
            getNoticeList({ page: 1 });
        });
    } catch(e) {}

    // 검색 버튼
    try {
        const btnRegister = document.getElementById('btnRegister');
        btnRegister.addEventListener('click', function() {
            location.href = `?controller=agency&action=registerNotice`;
        });
    } catch(e) {}

    // 체크박스 관련 코드
    try {
        const chkAll = document.getElementById('chkAll');
        chkAll.addEventListener('click', () => {
            if(chkAll.checked) checkAll('chk');
            else checkAllDisolve('chk');
        });
    } catch(e) {}

    // 선택삭제
    try {
        const btnDeleteSelected = document.getElementById('btnDeleteSelected');
        if(btnDeleteSelected) {
            btnDeleteSelected.addEventListener('click', deleteSelected);
        }
    } catch(e) {}

    getTelephoneCounselingList({page : 1});
});

// 상수 정의
const CONTROLLER = 'agency';
const MODE = 'getTelephoneCounselingList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'asc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getTelephoneCounselingList = async ({ page, per = 13, block = 4, where = 'where 1=1', orderBy = DEFAULT_ORDER_BY, order = DEFAULT_ORDER }) => {
    return new Promise((resolve, reject) => {

        try {
            const searchText = document.getElementById('searchText');
            if(searchText.value != '') {
                where += ` and title like '%${searchText.value}%'`;
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

        fetch('./handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector('.list tbody');
            tableBody.innerHTML = generateTableContent(data);

            getPaging('sk_telephone_counseling', 'uid', where, page, per, block, 'getTelephoneCounselingList');
            resolve();  // 디바이스 리스트 설정이 완료되면 resolve 호출
        })
        .catch(error => {
            console.error('데이터를 가져오는 중 오류가 발생했습니다:', error);
            reject(error);  // 에러 발생 시 reject 호출
        });
    });
};

const generateTableContent = (data) => {
    const totalCount = data.totalCount;
    const listData = data.data;

    if (!listData || listData.length === 0) {
        return `<tr><td class='center pd10' colspan='8'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return listData.map((item, index) => {
        const no = totalCount - index;  // 총 게시물에서 현재 index를 빼서 no 계산

        return `
            <tr> 
                <td class='center'>${no}</td>  
                <td class='center'>${item.registerDate}</td>
                <td class='center'>${item.name}</td>
                <td class='center'>${item.mobile}</td>
                <td class='center'>${item.memo}</td>
                <td class='center'>
                    <select name='state' onchange='register(this.value, ${item.uid})'>
                        <option value='신규' ${item.state === '신규' ? 'selected' : ''}>신규</option>
                        <option value='진행중' ${item.state === '진행중' ? 'selected' : ''}>진행중</option>
                        <option value='완료' ${item.state === '완료' ? 'selected' : ''}>완료</option>
                    </select>
                </td>
                <td class='center'>                    
                    <input type='button' class='btn' value='삭제' onclick='deleteTelephoneCounseling(${item.uid})' />
                </td>
            </tr>
        `;
    }).join('');
};

// 예시 register 함수
function register(stateValue, uid) {
    // 서버로 변경된 상태와 uid를 전송하는 로직 구현
    console.log(`Selected state: ${stateValue}, UID: ${uid}`);
    
    // 예시로 fetch로 서버에 상태 전송
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'changeTelephoneCounselingState');
    formData.append('state', stateValue);
    formData.append('uid', uid);

    fetch('./handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.result === 'success') {
            alert('상태가 업데이트되었습니다.');
        } else {
            alert('상태 업데이트 중 오류가 발생했습니다.');
        }
    })
    .catch(error => {
        console.error('Error updating state:', error);
    });
}


const deleteTelephoneCounseling = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'agency');
        formData.append('mode', 'deleteTelephoneCounseling');
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
                alert(data.message);

                if(data.result == 'success') {
                    getTelephoneCounselingList({page : 1});
                }
            }
        })
        .catch(error => console.log(error));
    }
}
</script>