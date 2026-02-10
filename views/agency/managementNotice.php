<div class='main-container'>
    <div class='title-wrapper'>공지사항 관리</div>
        <div class='search-wrapper'>
            <div class='search-box'>
                <div class='search-section'>
                    <input type="text" placeholder="검색">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </div>
            </div>
            <div class='button-box'>
                <input type='button' class='btn-large orange' id='btnRegister' value='공지사항 등록' />
                <input type='button' class='btn-large' id='btnDeleteSelected' value='선택 삭제' />
            </div>
        </div>
        <div class='content-wrapper'>
            <div>
                <table class='list'>
                    <colgroup>
                        <col width='50'>
                        <col>
                        <col width='200'>
                        <col width='150'>
                        <col width='150'>
                        <col width='150'>
                    </colgroup>
                    <thead>
                        <tr>
                            <th>
                                <label class="custom-checkbox">
                                    <input type="checkbox" id='chkAll'>
                                    <span class="checkmark"></span>
                                </label>
                            </th>
                            <th>제목</th>
                            <th>등록일시</th>
                            <th>등록인</th>
                            <th>조회수</th>
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

<script>


window.addEventListener('DOMContentLoaded', async () => {
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

    getNoticeList({page:1});    
});

// 상수 정의
const CONTROLLER = 'agency';
const MODE = 'getNoticeList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'asc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getNoticeList = async ({ page, per = 13, block = 4, where = 'where 1=1', orderBy = DEFAULT_ORDER_BY, order = DEFAULT_ORDER }) => {
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

            getPaging('sk_board', 'uid', where, page, per, block, 'getNoticeList');
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
                <td class='center'>
                    <label class="custom-checkbox">
                        <input type="checkbox" class='chk' value='${item.uid}'>
                        <span class="checkmark"></span>
                    </label>
                </td>  
                <td class='center' style='width:770px'>${item.title}</td>
                <td class='center' style='width:200px'>${item.registerDateTime}</td>
                <td class='center' style='width:100px'>${item.registerName}</td>
                <td class='center' style='width:100px'>${comma(item.viewCnt)}</td>
                <td class='center'>
                    <input type='button' class='btn grey' value='수정' onclick='modifyNotice(${item.uid})' />
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
    getNoticeList({page : 1});
}
</script>