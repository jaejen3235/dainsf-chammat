<div class='main-container'>        
    <div class='search-wrapper'>
        <div class='search-box'>
            <div class='search-section'>
                <div class='search-input'>
                    <input type="text" id='start_date' placeholder="시작일">
                    <input type="text" id='end_date' placeholder="종료일">
                    <button class='btn-large primary' id='btnSearch'>검색</button>
                    <button class='btn-large success revision' id='btnRevision'><i class='bx bx-revision'></i></button>
                </div>
            </div>
            <div>
            </div>
        </div>
    </div>
    
    <div class='content-wrapper'>
        <div>
            <table class='list'>
                <colgroup>
                    <col width='200' />
                    <col width='150' />
                    <col width='150' />
                    <col width='150' />
                    <col width='150' />
                    <col />
                </colgroup>
                <thead>
                    <tr>
                        <th>품목명</th>
                        <th>품번</th>
                        <th>규격</th>
                        <th>재작업 지시수량</th>
                        <th>등록일시</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
        <div class="paging-area mt20"></div>
    </div>    
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    // 검색
    try {
        const search = document.querySelector('.fa-search');
        if(search) {
            search.addEventListener('click', () => {
                getReworkList({page : 1});
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
                openModal('modalRegisterDefectReason', 600, 330);
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
                getReworkList({page:1});
            }
        });
    } catch(e) {}

    getReworkList({page : 1});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getReworkList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getReworkList = async ({
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

        getPaging('mes_rework_report', 'uid', where, page, per, block, 'getReworkList');
    } catch (error) {
        console.error('리워크 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>${item.unsuitable_qty}</td>
            <td class='center'>${item.created_dt}</td>
            <td class='center'>
                <input type='button' class='btn-small danger' value='삭제' onclick='deleteInspection(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const deleteInspection = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'mes');
        formData.append('mode', 'deleteInspection');
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
                    getInspectionList({page:1});
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
        formData.append('table', 'mes_incoming_inspection');

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
				getInspectionList({page:1});
			}            
		}).catch(error => console.log(error));
	}
}

// 새로고침
const revision = () => {
    document.getElementById('searchText').value = '';
    getReworkList({page:1});
}
</script>