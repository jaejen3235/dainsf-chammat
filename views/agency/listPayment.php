<div class='main-container'>
    <div class='title-wrapper'>요금제 관리</div>
        <div class='search-wrapper'>
            <div class='search-box'>
                <div>
                    <select id='category'>
                        <option value='0'>선택</option>
                        <option value='5G'>5G</option>
                        <option value='LTE'>LTE</option>
                    </select>
                </div>
                <div class='search-section'>
                    <input type="text" id='searchText' placeholder="검색">
                    <i class="fa fa-search hands" aria-hidden="true"></i>
                </div>
                <button class='btn-large success revision' id='btnRevision'><i class='bx bx-revision'></i></button>
            </div>
            <div class='button-box'>
                <input type='button' class='btn-large orange' id='btnOpenModal' value='요금제 등록' />
                <input type='button' class='btn-large' id='btnDeleteSelected' value='선택 삭제' />
            </div>
        </div>
        <div class='content-wrapper'>
            <div>
                <table class='list'>
                    <colgroup>
                        <col width='50'>
                        <col width='100'>
                        <col>
                        <col width='150'>
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
                            <th>요금제 구분</th>
                            <th>요금제명</th>
                            <th>데이터</th>
                            <th>월 이용료</th>
                            <th>세대 구분</th>
                            <th>사용</th>
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
include "./views/modal/modalRegisterPayment.php";
?>

<script>
window.addEventListener('DOMContentLoaded', () => {
    // 카테고리 변경 이벤트 리스너 설정
    try {
        document.getElementById('category').addEventListener('change', () => {
            getPaymentList({ page: 1 });
        });
    } catch(e) {}

    // 검색
    try {
        const search = document.querySelector('.fa-search');
        if(search) {
            search.addEventListener('click', () => {
                getClientList({page : 1});
            });
        }
    } catch(e) {}

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
                openModal('modalRegisterPayment', 600, 560);
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
                getPaymentList({page:1});
            }
        });
    } catch(e) {}


    getPaymentList({page : 1});
});

// 상수 정의
const CONTROLLER = 'agency';
const MODE = 'getPaymentList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'asc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getPaymentList = async ({ page, per = 7, block = 4, where = 'where 1=1', orderBy = DEFAULT_ORDER_BY, order = DEFAULT_ORDER }) => {
    return new Promise((resolve, reject) => {
        const category = document.getElementById('category');
        const searchText = document.getElementById('searchText');
        
        try {
            if(category.value != '0') {   
                where += ` and category='${category.value}'`;
            }
        } catch(e) {}

        try {
            if(searchText.value != '') {
                where += ` and paymentName like '%${searchText.value}%'`;
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

            getPaging('sk_payment', 'uid', where, page, per, block, 'getPaymentList');
            resolve();  // 디바이스 리스트 설정이 완료되면 resolve 호출
        })
        .catch(error => {
            console.error('데이터를 가져오는 중 오류가 발생했습니다:', error);
            reject(error);  // 에러 발생 시 reject 호출
        });
    });
};

const generateTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center pd10' colspan='8'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.map(item => `
        <tr>
            <td class='center'>
                <label class="custom-checkbox">
                    <input type="checkbox" class='chk' value='${item.uid}'>
                    <span class="checkmark"></span>
                </label>
            </td>
            <td class='center'>${item.category}</td>
            <td class='center'>${item.paymentName}</td>
            <td class='center'>${item.dataName}</td>
            <td class='center'>${comma(item.payment)}</td>
            <td class='center'>${item.age}</td>
            <td class='center'>${item.display}</td>
            <td class='center'>
                <input type='button' class='btn grey' value='수정' onclick='modifyPayment(${item.uid})' />
                <input type='button' class='btn' value='삭제' onclick='deletePayment(${item.uid})' />
            </td>
        </tr>
    `).join('');
};


const modifyPayment = (uid) => {
    document.getElementById('uid').value = uid;
    getter(uid);
    openModal('modalRegisterPayment', 600, 560);
}

const deletePayment = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'agency');
        formData.append('mode', 'deletePayment');
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
                    getPaymentList({page : 1});
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
        formData.append('table', 'sk_payment');

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
				getPaymentList({page : 1});
			}            
		}).catch(error => console.log(error));
	}
}

const revision = () => {
    const categorySelect = document.getElementById('category');
    
    // 카테고리 select 요소의 첫 번째 옵션 선택
    if (categorySelect.options.length > 0) {
        categorySelect.selectedIndex = 0;
    }
    
    document.getElementById('searchText').value = '';
    getPaymentList({page : 1});
}
</script>