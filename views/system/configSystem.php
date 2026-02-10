<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 사용자 설정</div>
    <div class='search-wrapper'>
        <div class='search-box'>
            <div class='search-section'>
                <div class='search-input'>
                    <input type="text" id='searchText' placeholder="검색">
                    <button class='btn-large primary' id='btnSearch'>검색</button>
                    <button class='btn-large success revision' id='btnRevision'><i class='bx bx-revision'></i></button>
                </div>
            </div>                            
            <div class='button-box'>
                <input type='button' class='btn-large orange' id='btnOpenModal' value='사용자 등록' />
                <input type='button' class='btn-large' id='btnDeleteSelected' value='선택 삭제' />
            </div>
        </div>
    </div>
    <div class='content-wrapper'>
        <div>
            <table class='list'>
                <colgroup>
                    <col width='50' />
                    <col />
                    <col />
                    <col />
                    <col width='150' />
                </colgroup>
                <thead>
                    <tr>
                        <th>
                            <label class="custom-checkbox">
                                <input type="checkbox" id='chkAll'>
                                <span class="checkmark"></span>
                            </label>
                        </th>
                        <th>사용자명</th>
                        <th>로그인 아이디</th>
                        <th>권한</th>
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
include "./views/modal/modalRegisterUser.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    // 검색
    try {
        const search = document.querySelector('.fa-search');
        if(search) {
            search.addEventListener('click', () => {
                getUserList({page : 1});
            });
        }
    } catch(e) {}

    // 체크박스
    try{
		const chkAll = document.getElementById('chkAll');
		chkAll.addEventListener('click', ()=>{
			if(chkAll.checked) checkAll('chk');
			else checkAllDisolve('chk');
		});
	} catch(e) {}

    // 모달열기
    try {
        const btnOpenModal = document.getElementById('btnOpenModal');
        if(btnOpenModal) {
            btnOpenModal.addEventListener('click', function() {
                openModal('modalRegisterUser', 600, 530);
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
                getUserList({page:1});
            }
        });
    } catch(e) {}

    getUserList({page : 1});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getUserList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getUserList = async ({
    page,
    per = 10,
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
                where += ` and name like '%${searchText.value}%'`;
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

        getPaging('mes_user', 'uid', where, page, per, block, 'getUserList');
    } catch (error) {
        console.error('사용자 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>
                <label class="custom-checkbox">
                    <input type="checkbox" class='chk' value='${item.uid}'>
                    <span class="checkmark"></span>
                </label>
            </td>
            <td class='center'>${item.employeeName}</td>
            <td class='center'>${item.loginId}</td>
            <td class='center'>${item.auth}</td>
            <td class='center'>
                <input type='button' class='btn grey' value='수정' onclick='modifyUser(${item.uid})' />
                <input type='button' class='btn' value='삭제' onclick='deleteUser(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const modifyUser = (uid) => {
    getter(uid);
    openModal('modalRegisterUser', 600, 530);
}

const deleteUser = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'mes');
        formData.append('mode', 'deleteUser');
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
                    getUserList({page:1});
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
        formData.append('table', 'mes_user');

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
				getUserList({page:1});
			}            
		}).catch(error => console.log(error));
	}
}

// 새로고침
const revision = () => {
    document.getElementById('searchText').value = '';
    getUserList({page:1});
}
</script>