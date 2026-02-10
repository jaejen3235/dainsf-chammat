<div class='main-container'>
    <div class='title-wrapper'>상담신청 관리</div>
        <div class='search-wrapper'>
            <div class='search-box'>
                <div class='search-section'>
                    <input type="text" class='datepicker' id='startDate' placeholder="날짜 선택">
                    <i class="fa fa-calendar"></i>
                </div>~
                <div class='search-section'>
                    <input type="text" class='datepicker' id='endDate' placeholder="날짜 선택">
                    <i class="fa fa-calendar"></i>
                </div>
                <div>
                    <select id='state'>
                        <option value='0'>진행상황 선택</option>
                        <option value='신규'>신규</option>
                        <option value='진행중'>진행중</option>
                        <option value='완료'>완료</option>
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
                            <th>협력사</th>
                            <th>고객사</th>
                            <th>신청자명</th>
                            <th>연락처</th>
                            <th>기기명</th>
                            <th>모델명</th>
                            <th>요금제</th>
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
$(document).ready(function() {
    // datepicker에 change 이벤트가 아닌 datepicker 이벤트 사용
    $(".datepicker").datepicker({
        onSelect: function() {
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            
            // 날짜가 선택되었을 때 처리
            if (startDate.value !== '' && endDate.value !== '') {                
                getCounselingList({ page: 1 });
            }
        }
    });
});

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
            getCounselingList({ page: 1 });
        });
    } catch(e) {}

    try {
        const state = document.getElementById('state');
        state.addEventListener('change', () => {
            getCounselingList({ page: 1 });
        });
    } catch(e) {}

    getCounselingList({page : 1});
});

// 상수 정의
const CONTROLLER = 'agency';
const MODE = 'getCounselingList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'asc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getCounselingList = async ({ page, per = 13, block = 4, where = 'where 1=1', orderBy = DEFAULT_ORDER_BY, order = DEFAULT_ORDER }) => {
    return new Promise((resolve, reject) => {

        try {
            const searchText = document.getElementById('searchText');
            if(searchText.value != '') {
                where += ` and title like '%${searchText.value}%'`;
            }
        } catch(e) {}

        try {
            const state = document.getElementById('state');
            if(state.value != '0') {
                where += ` and state='${state.value}'`;
            }            
        } catch(e) {}

        // 추가: startDate와 endDate 값이 있는 경우 where 절에 날짜 조건 추가
        try {
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            if (startDate.value !== '' && endDate.value !== '') {
                where += ` and registerDate BETWEEN '${startDate.value}' AND '${endDate.value}'`;
            } else if (startDate.value !== '') {
                where += ` and registerDate >= '${startDate.value}'`;
            } else if (endDate.value !== '') {
                where += ` and registerDate <= '${endDate.value}'`;
            }
        } catch (e) {}

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

            getPaging('sk_counseling', 'uid', where, page, per, block, 'getCounselingList');
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
        return `<tr><td class='center pd10' colspan='11'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return listData.map((item, index) => {
        const no = totalCount - index;  // 총 게시물에서 현재 index를 빼서 no 계산

        return `
            <tr> 
                <td class='center'>${no}</td>  
                <td class='center hands' onclick="movePage('agency', 'viewCounseling', ${item.uid})">${item.registerDate}</td>
                <td class='center hands' onclick="movePage('agency', 'viewCounseling', ${item.uid})">${item.agency}</td>
                <td class='center hands' onclick="movePage('agency', 'viewCounseling', ${item.uid})">${item.client}</td>
                <td class='center hands' onclick="movePage('agency', 'viewCounseling', ${item.uid})">${item.name}</td>
                <td class='center hands' onclick="movePage('agency', 'viewCounseling', ${item.uid})">${item.mobile}</td>
                <td class='center hands' onclick="movePage('agency', 'viewCounseling', ${item.uid})">${item.deviceName}</td>
                <td class='center hands' onclick="movePage('agency', 'viewCounseling', ${item.uid})">${item.deviceModel}</td>
                <td class='center hands' onclick="movePage('agency', 'viewCounseling', ${item.uid})">${item.payment}</td>
                <td class='center'>${item.state}</td>
                <td class='center'>                    
                    <input type='button' class='btn' value='삭제' onclick='deleteNotice(${item.uid})' />
                </td>
            </tr>
        `;
    }).join('');
};


const modifyNotice = (uid) => {
    location.href = `?controller=agency&action=registerNotice&menu=board&uid=${uid}`;
}

const deleteNotice = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'agency');
        formData.append('mode', 'deleteNotice');
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
                    getNoticeList({page : 1});
                }
            }
        })
        .catch(error => console.log(error));
    }
}


// 선택삭제
const deleteSelected = () => {
    let uids = '';
	document.querySelectorAll('.chk').forEach((elem, index) => {
		if(elem.checked) {
			uids += elem.value + ",";
		}
	});

    if(uids == '') {
        alert('삭제하실 데이터를 선택하세요');
        return false;
    }

	if(confirm("선택하신 DATA를 삭제하시겠습니까? 삭제 후에는 복구가 불가능합니다")) {
		const formData = new FormData();
        formData.append('controller', 'functions');
        formData.append('mode', 'deleteSelected');
        formData.append('uids', uids);
        formData.append('table', 'sk_board');

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
            alert(data.message);
			if(data.result == "success") {
				chkAll.checked = false;
				getNoticeList({page : 1});
			}            
		}).catch(error => console.log(error));
	}
}

const refresh = () => {
    const categorySelect = document.getElementById('category');
    
    // 카테고리 select 요소의 첫 번째 옵션 선택
    if (categorySelect.options.length > 0) {
        categorySelect.selectedIndex = 0;
    }
    
    document.getElementById('searchText').value = '';
    getPaymentList({page : 1});
}
</script>