<div class='main-container'>
    <div class='title-wrapper'>설비점검 내역</div>
        <div class='search-wrapper'>
            <div class='search-box'>
            </div>
            <div class='button-box'>
                <input type='button' class='btn-large orange' id='btnDeleteSelected' value='선택 삭제' />
                <input type='button' class='btn-large success' id='btnList' value='목록 가기' />
            </div>
        </div>
        <div class='content-wrapper'>
            <div>
                <table class='list'>
                    <colgroup>
                        <col width='50' />
                        <col width='150' />
                        <col width='200' />
                        <col width='100' />
                        <col width='150' />
                        <col width='150' />
                        <col width='200' />
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
                            <th>점검일자</th>
                            <th>점검부위</th>
                            <th>점검항목</th>
                            <th>점검방법</th>
                            <th>점검기준</th>
                            <th>결과</th>
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

<input type='hidden' name='uid' id='uid' value='<?php echo $_GET['uid']; ?>' />

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    // 검색
    try {
        const search = document.querySelector('.fa-search');
        if(search) {
            search.addEventListener('click', () => {
                getMachineInspectList({page : 1});
            });
        }
    } catch(e) {}

    try {
        const btnList = document.querySelector('#btnList');
        if(btnList) {
            btnList.addEventListener('click', () => {
                location.href = `?controller=machine&action=checkMachineManagement`;
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
                location.href = `?controller=machine&action=registerMachine`;
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
                getMachineInspectList({page:1});
            }
        });
    } catch(e) {}

    getMachineInspectList({page : 1});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getMachineInspectList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getMachineInspectList = async ({
    page,
    per = 10,
    block = 4,
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {
    let where = `where fid=${document.getElementById('uid').value}`;
    
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

        getPaging('mes_inspect_report', 'uid', where, page, per, block, 'getMachineInspectList');
    } catch (error) {
        console.error('설비점검 내역 데이터를 가져오는 중 오류가 발생했습니다:', error);
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
            <td class='center'>${item.inspectDate}</td>
            <td class='center'>${item.inspectPart}</td>
            <td class='center'>${item.inspectName}</td>
            <td class='center'>${item.inspectMethod}</td>
            <td class='center'>${item.inspectComment}</td>
            <td class='center'>${item.inspectResult}</td>
            <td class='center'>
                <input type='button' class='btn grey' value='수정' onclick='modifyInspectResult(${item.uid})' />
                <input type='button' class='btn' value='삭제' onclick='deleteInspectResult(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const modifyInspectResult = (uid) => {
    location.href = `?controller=machine&action=registerMachine&uid=${uid}`;
}

const deleteInspectResult = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'mes');
        formData.append('mode', 'deleteMachineInspectReport');
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
                    getMachineInspectList({page:1});
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
        formData.append('table', 'mes_inspect_report');

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
				getMachineInspectList({page:1});
			}            
		}).catch(error => console.log(error));
	}
}

// 새로고침
const revision = () => {
    document.getElementById('searchText').value = '';
    getMachineInspectList({page:1});
}
</script>