<div class='main-container'>
    <div class='title-wrapper'>설비점검 관리</div>
        <div class='search-wrapper'>
            <div class='search-box'>
                <div class='search-section'>
                    <input type="text" id='searchText' placeholder="검색">
                    <i class="fa fa-search hands" aria-hidden="true"></i>
                </div>
                <button class='btn-large success revision' id='btnRevision'><i class='bx bx-revision'></i></button>
            </div>
            <div class='button-box'>
            </div>
        </div>
        <div class='content-wrapper'>
            <div>
                <table class='list'>
                    <colgroup>
                        <col />
                        <col width='200' />
                        <col width='100' />
                        <col width='150' />
                        <col width='150' />
                        <col width='200' />
                        <col width='250' />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>설비명</th>
                            <th>관리코드</th>
                            <th>구입년도</th>
                            <th>구입처</th>
                            <th>구입처 연락처</th>
                            <th>관리자</th>
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
include "./views/modal/modalRegisterInspect.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    // 검색
    try {
        const search = document.querySelector('.fa-search');
        if(search) {
            search.addEventListener('click', () => {
                getMachineList({page : 1});
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
                getMachineList({page:1});
            }
        });
    } catch(e) {}

    getMachineList({page : 1});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getMachineList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getMachineList = async ({
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
                where += ` and (name like '%${searchText.value}%' or code like '%${searchText.value}%')`;
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

        getPaging('mes_machine', 'uid', where, page, per, block, 'getMachineList');
    } catch (error) {
        console.error('대리점 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'><a href='?controller=machine&action=viewMachine&uid=${item.uid}'>${item.name}</a></td>
            <td class='center'><a href='?controller=machine&action=viewMachine&uid=${item.uid}'>${item.code}</a></td>
            <td class='center'>${item.purchaseYear}</td>
            <td class='center'>${item.maker}</td>
            <td class='center'>${item.makerContact}</td>
            <td class='center'>${item.mainOfficerName}/${item.subOfficerName}</td>
            <td class='center'>
                <input type='button' class='btn grey' value='점검등록' onclick='inspectMachine(${item.uid})' />
                <input type='button' class='btn danger' value='점검내역 보기' onclick='viewInspectList(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const inspectMachine = (uid) => {
    getter(uid);
    openModal('modalRegisterInspect', 1200, 600);
}

const viewInspectList = (uid) => {
    location.href = `?controller=machine&action=listMachineInspect&uid=${uid}`;
}

// 새로고침
const revision = () => {
    document.getElementById('searchText').value = '';
    getMachineList({page:1});
}
</script>