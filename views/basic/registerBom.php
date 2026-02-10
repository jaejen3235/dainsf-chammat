<div class='main-container'>
    <div class='title-wrapper'>BOM 등록</div>
        <input type='hidden' name='uid' id='uid' value="<?php echo $_GET['uid']; ?>" /> 
            
        <div class='content-wrapper'>
            <div>
                <table class='list register'>
                    <thead>
                        <tr>
                            <th>구분</th>
                            <th>품명</th>
                            <th>품번</th>
                            <th>규격</th>
                            <th>단위</th>
                            <th>소요량</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div>
                <span class='help'>※ 하위 부품은 한번에 하나씩 등록하셔야 합니다.</span>
            </div>
            <div class='mt30 center'>
                <input type='button' class='btn-large primary' value='목록가기' id='btnList' />
            </div>
        </div>
    </div>
</div>

<?php
include "./views/modal/modalListItem.php";
?>

<script>
let gid = 0;
let fid = 0; // 어떤 부품의 하위인지 알기 위한 변수
let depth = 0; // 깊이를 알기 위한 변수
let addMode = false;

window.addEventListener('DOMContentLoaded', async () => {	  
    const uid = document.getElementById('uid');
    const btnList = document.getElementById('btnList');

    if(btnList) {
        btnList.addEventListener('click', () => {
            location.href = `?controller=basic&action=listBom`;
        });
    }


    checkBom();
    getItemList({page : 1}); // 품목선택 모달창에 뿌려줄거임
});  


// 완제품 BOM이 등록이 되어있는지 확인
const checkBom = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'checkBom');
    formData.append('uid', uid.value);

    try {
        // 서버로 데이터 전송
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error('등록 중 오류가 발생했습니다.');
        }

        const result = await response.json();

        if (result['result'] == 'success') { // 새롭게 등록해야 한다면            
            registerBasicBom();
        } else {
            getBomList(uid.value);
        }
    } catch (error) {
        console.error('오류:', error);
    }
}

const registerBasicBom = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'registerBasicBom');
    formData.append('uid', uid.value);

    try {
        // 서버로 데이터 전송
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error('등록 중 오류가 발생했습니다.');
        }

        const result = await response.json();

        if (result['result'] == 'success') { // 새롭게 등록해야 한다면
            getBomList(uid.value);
        } else {
            return false;
        }
    } catch (error) {
        console.error('오류:', error);
    }
}

// 품목 가져오기
const getBomList = async (uid) => {  
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getBomTree');
    formData.append('uid', uid);

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        const tableBody = document.querySelector('.list tbody');
        tableBody.innerHTML = setItem(data);
    } catch (error) {
        displayError(error);
    }
}
const setItem = (data) => {
    console.log('Received data:', data);

    // 객체를 배열로 변환하여 처리
    const items = Array.isArray(data) ? data : [data];

    if (!items.length) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    let rows = '';  // 결과 문자열 초기화

    items.forEach(item => {        
        let space = '&nbsp;'.repeat(item.depth * 5); // 계층 구조 시각화
        if(item.depth > 1) space += `<i class='bx bx-subdirectory-right'></i>`;

        rows += `
            <tr>
                <td class='center'>${item.classification}</td>
                <td class='left'>${space}${item.item_name}</td>
                <td class='center'>${item.item_code}</td>
                <td class='center'>${item.standard}</td>
                <td class='center'>${item.unit}</td>
                <td class='center'><input type='text' class='w100' value='${item.qty || ''}' /></td>
                <td class='center'>
                    <input type='button' class='btn-small grey' value='하위부품 선택' onclick='selectItem(${item.uid}, ${item.depth})' />
                    <input type='button' class='btn-small danger' value='삭제' onclick='deleteBom(${item.uid}, this)' />
                </td>
            </tr>
        `;

        // 하위 children 항목이 있는 경우 재귀적으로 추가
        if (item.children && item.children.length > 0) {
            rows += setItem(item.children);
        }
    });

    return rows;
};



const selectItem = (uid, dp) => {
    gid = uid;
    fid = uid;
    depth = dp;
    if(dp == 3) {
        alert('BOM단계는 최대 2단계입니다.');
        return;
    }
    if(addMode) {
        alert('먼저 등록할 하위 제품이 있습니다. 등록 후 하위 부품을 등록하세요');
        return;
    }
    openModal('modalListItem', 900, 540);
}


const addBom = async (itemUid, button) => {
    // 해당 라인에서 소요량 입력값 가져오기
    const row = button.closest('tr'); // 현재 버튼의 부모 행
    const quantityInput = row.querySelector('input[type="text"]'); // 소요량 필드
    const quantity = quantityInput.value.trim(); // 입력값 가져오기

    if (!quantity) {
        alert('소요량을 입력해주세요.');
        return;
    }

    // 서버로 보낼 데이터 준비
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'addBom');
    formData.append('itemUid', itemUid); // BOM의 하위 부품으로 등록할 품목의 uid
    formData.append('quantity', quantity); // 소요량
    formData.append('uid', uid.value); // 완제품 품목의 UID
    formData.append('gid', gid); // 완제품 품목의 UID
    formData.append('fid', fid); // 추가할 품목의 상위 품목의 UID
    formData.append('depth', depth); // 완제품 품목의 Depth...0 이지

    try {
        // 서버로 데이터 전송
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error('등록 중 오류가 발생했습니다.');
        }

        const result = await response.json();

        if (result['result'] == 'success') {
            // 등록 성공 시 버튼 숨기기 또는 비활성화
            button.style.display = 'none'; // 버튼 숨기기
            alert('등록되었습니다.');
        } else {
            alert('등록에 실패했습니다: ' + result.message);
        }
    } catch (error) {
        console.error('등록 중 오류:', error);
        alert('서버와 통신 중 오류가 발생했습니다.');
    }
};

const deleteRow = async (uid, button) => {
    // 확인 알림
    const confirmDelete = confirm('해당 품목을 삭제하시겠습니까?');
    if (!confirmDelete) {
        return;
    }

    const row = button.closest('tr');
    row.remove();
};

const deleteBom = async (uid, button) => {
    // 확인 알림
    const confirmDelete = confirm('해당 품목을 삭제하시겠습니까?');
    if (!confirmDelete) {
        return;
    }

    // 서버로 보낼 데이터 준비
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'deleteBom');
    formData.append('uid', uid);

    try {
        // 서버에 데이터 전송
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error('삭제 요청 중 문제가 발생했습니다.');
        }

        const result = await response.json();

        if (result['result'] == 'success') {
            // 삭제 성공 시 행 제거
            const row = button.closest('tr');
            row.remove();
            alert('삭제되었습니다.');
        } else {
            alert('삭제 실패: ' + result.message);
        }
    } catch (error) {
        console.error('삭제 중 오류:', error);
        alert('서버와 통신 중 오류가 발생했습니다.');
    }
};
</script>