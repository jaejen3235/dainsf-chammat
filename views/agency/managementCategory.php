<div class='main-container'>
    <div class='title-wrapper'>카테고리 관리</div>
        <div class='search-wrapper'>
            <div class='search-box'>
            </div>
            <div class='button-box'>
                <input type='button' class='btn-large orange' id='btnOpenModal' value='카테고리 등록' />
                <input type='button' class='btn-large' id='btnDeleteSelected' value='선택 삭제' />
            </div>
        </div>
        <div class='content-wrapper'>
            <div>
                <table class='list'>
                    <colgroup>
                        <col width='50' />
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
                            <th>카테고리명</th>
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
include "./views/modal/modalRegisterCategory.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
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
                openModal('modalRegisterCategory', 600, 330);
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

    getCategoryList({page:1});
});

const CONTROLLER = 'agency';
const MODE = 'getCategoryList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'asc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getCategoryList = async ({
    page,
    per = 13,
    block = 4,
    where = 'where 1=1',
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {
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

        getPaging('sk_categorys', 'uid', where, page, per, block, 'getCategoryList');
    } catch (error) {
        console.error('카테고리 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center pd10' colspan='3'>${UI_CONSTANTS.NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.map(item => `
        <tr>
            <td class='center'>
                <label class="custom-checkbox">
                    <input type="checkbox" class='chk' value='${item.uid}'>
                    <span class="checkmark"></span>
                </label>
            </td>
            <td>${item.categoryName}</td>
            <td>
                <input type='button' class='btn grey' value='수정' onclick='modifyCategory(${item.uid})' />
                <input type='button' class='btn' value='삭제' onclick='deleteCategory(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const modifyCategory = async (uid) => {
    document.getElementById('uid').value = uid;
    getter(uid);
    openModal('modalRegisterCategory', 600, 330);
};


const deleteCategory = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'agency');
        formData.append('mode', 'deleteCategory');
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
                    getCategoryList({page:1});
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
        formData.append('table', 'sk_categorys');

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
				getCategoryList({page:1});
			}            
		}).catch(error => console.log(error));
	}
}

const refresh = () => {
    document.getElementById('uid').value = '';
    document.getElementById('categoryName').value = '';
    document.getElementById('btnRegister').value = '카테고리 등록';
};

const handleApiResponse = (data, successCallback) => {
    alert(data.message);
    if (data.result === 'success') {
        successCallback();
    }
};
</script>