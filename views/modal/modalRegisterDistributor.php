<div class="modal" id="modalRegisterDistributor">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>총판 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='distributor' />
                <input type='hidden' name='mode' id='mode' value='registerDistributor' />
                <input type='hidden' class='input' name='uid' id='uid' >

                <table class='register'>
                    <colgroup>
                        <col width='200'>
                        <col />
                    </colgroup>
                    <tr>
                        <td><i class='bx bx-check'></i> 총판명</td>
                        <td>
                            <input type='text' class='input' name='name' id='name' />
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 총판 코드</td>
                        <td>
                            <input type='text' class='input' name='code' id='code' />
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 총판 관리자명</td>
                        <td>
                            <input type='text' class='input' name='adminName' id='adminName' />
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 총판 관리자 연락처</td>
                        <td>
                            <input type='text' class='input' name='adminMobile' id='adminMobile' maxlength='13' />
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 로그인 아이디</td>
                        <td>
                            <input type='text' class='input' name='loginId' id='loginId' />
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 로그인 비밀번호</td>
                        <td>
                            <input type='password' class='input' name='loginPwd' id='loginPwd' />
                        </td>
                    </tr>
                </table>
            </form>
            <hr />
            <div class='help'>
                <i class='bx bx-check'></i> 은 필수입력 사항입니다
            </div>

            <div class='btn-group'>
                <input type='button' class='modal-btn danger' id='btnRegister' value='저장' />&nbsp;
                <input type='button' class='modal-btn' id='btnCloseModal' value='취소' />
            </div>
		</div>
	</div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    try {
        const uid = document.getElementById('uid');
    } catch(e) {}

    // 창닫기
    try {
        const btnClose = document.getElementById('btnClose');
        if(btnClose) {
            btnClose.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterDistributor');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterDistributor');
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnRegister = document.getElementById('btnRegister');
        if (btnRegister) {            
            btnRegister.addEventListener('click', () => {
                // 배열을 전달하여 함수 호출
                const fieldsArray = [
                    { id: 'name', message: '협력사명을 입력하세요', type: 'text' },
                    { id: 'code', message: '협력사코드를 입력하세요', type: 'text' },
                    { id: 'adminName', message: '담당자를 입력하세요', type: 'text' },
                    { id: 'adminMobile', message: '연락처를 입력하세요', type: 'text' },
                    { id: 'loginId', message: '로그인아이디를 입력하세요', type: 'text' },
                    { id: 'loginPwd', message: '로그인비밀번호를 입력하세요', type: 'text' }
                ];

                // 유효성 검사를 위한 함수 호출
                const isValid = validateFields(fieldsArray);
                if(isValid) register();
                else console.log('유효성 검사를 통과하지 못했습니다');
            });            
        } else {
            console.log('btnRegister button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }
});    

// 협력사 등록
const register = () => {    
    const frm = document.getElementById('frm');

    if(frm) {
        if(check('frm')) {
            const formData = new FormData(frm);

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
                    if(data.result == 'success') {
                        alert(data.message);
                        getDistributorList({page:1});
                        clearn();
                        closeModal('modalRegisterDistributor');
                    } else {
                        clearn();
                        alert(data.message);
                    }

                }
            })
            .catch(error => console.log(error));
        }
    }
}

// 협력사 가져오기
const getter = async (uid) => {  
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'distributor');
    formData.append('mode', 'getDistributor');
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
        setter(data);
    } catch (error) {
        displayError(error);
    }
}

const setter = (data) => {
    if (data) {
        document.getElementById('name').value = data.name;
        document.getElementById('code').value = data.code;
        document.getElementById('adminName').value = data.adminName;
        document.getElementById('adminMobile').value = data.adminMobile;
        document.getElementById('loginId').value = data.loginId;
    }
}

const displayError = (error) => {
    console.error('Error:', error);
}

const clearn = () => {
    const inputs = document.querySelectorAll('.input'); // input 클래스를 가진 모든 요소 선택
    inputs.forEach(input => input.value = ''); // 각 요소의 값을 빈 문자열로 설정
};
</script>