<div class='main-container'>
    <div class='title-wrapper'>메인 슬리아드 관리</div>
        <div class='search-wrapper'>
            <div class='search-box'>
            </div>
            <div class='button-box'>
                <input type='button' class='btn-large orange' id='btnOpenModal' value='메인슬라이드 등록' />
                <input type='button' class='btn-large' id='btnDeleteSelected' value='선택 삭제' />
            </div>
        </div>
        <div class='content-wrapper'>
            <div>
                <table class='list'>
                    <colgroup>
                        <col width='50'>
                        <col width='200'>
                        <col>
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
                            <th>출력순서</th>
                            <th>이미지</th>
                            <th>사용 유무</th>
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
include "./views/modal/modalRegisterMainSlide.php";
?>


<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    const uid = document.getElementById('uid');
    
    // 모달열기
    try {
        const btnOpenModal = document.getElementById('btnOpenModal');
        if(btnOpenModal) {
            btnOpenModal.addEventListener('click', function() {
                openModal('modalRegisterMainSlide', 800, 300);
            });
        }
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

    getBannerList();
});    

// 상수 정의
const CONTROLLER = 'agency';
const MODE = 'getBannerList';
const DEFAULT_ORDER_BY = 'seq';
const DEFAULT_ORDER = 'asc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getBannerList = ({ orderBy = DEFAULT_ORDER_BY, order = DEFAULT_ORDER } = {}) => {
    const formData = new FormData();
    formData.append('controller', CONTROLLER);
    formData.append('mode', MODE);
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
    })
    .catch(error => {
        console.error('데이터를 가져오는 중 오류가 발생했습니다:', error);
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
            <td class='center'>
                <div class='arrow-box' onclick="changeSeq(${item.uid}, ${item.seq}, 'down')"><i class='bx bx-chevron-up' ></i></div>
                <div class='arrow-box' onclick="changeSeq(${item.uid}, ${item.seq}, 'up')"><i class='bx bx-chevron-down' ></i></div>
            </td>
            <td class='center'><img src='../attach/banner/${item.banner}' style='width:200px; height:80px' /></td>
            <td class='center'>${item.used}</td>
            <td class='center'>
                <input type='button' class='btn grey' value='수정' onclick='modifyBanner(${item.uid})' />
                <input type='button' class='btn' value='삭제' onclick='deleteBanner(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const modifyBanner = (uid) => {
    document.getElementById('uid').value = uid;
    getter(uid);
    openModal('modalRegisterMainSlide', 800, 410);
}

const deleteBanner = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'agency');
        formData.append('mode', 'deleteBanner');
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
                    getBannerList();
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
        formData.append('table', 'sk_banner');

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
				getBannerList();
			}            
		}).catch(error => console.log(error));
	}
}

const changeSeq = (uid, seq, updown) => {    
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'changeBannerSeq');
    formData.append('uid', uid);
    formData.append('seq', seq);
    formData.append('updown', updown);

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
		if(data != '') {
			getBannerList();
		}
	})
	.catch(error => console.log(error));
}

const displayError = (error) => {
    console.error('Error:', error);
}
</script>