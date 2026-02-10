<div class="modal" id="modalRegisterUser">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>사용자 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerUser' />
                <input type='hidden' class='input' name='uid' id='uid' value="<?php echo $_GET['uid']; ?>" />

                <table class='register'>
                    <colgroup>
                        <col width='200'>
                        <col />
                    </colgroup>
                    <tr>
                        <th><i class='bx bx-check'></i> 사용자</th>
                        <td>
                            <select class='select' name='employee' id='employee'>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 로그인 아이디</th>
                        <td>
                            <input type='text' class='input' name='loginId' id='loginId' />
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 로그인 비밀번호</th>
                        <td>
                            <input type='password' class='input' name='loginPwd' id='loginPwd' />                            
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 접근 권한</th>
                        <td>
                            <select class='select' name='auth' id='auth'>
                                <option value='0'>== 선택 ==</option>
                                <option value='99'>일반</option>
                                <option value='100'>관리자</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </form>
            <div class='help'>
                ※ <i class='bx bx-check'></i> 은 필수입력 사항입니다
            </div>
            <div class='help'>※ 수정 시 로그인 비밀번호를 입력하지 않으면 기존의 비밀번호가 유지됩니다.</div>

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
                closeModal('modalRegisterUser');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterUser');
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnRegister = document.getElementById('btnRegister');
        if (btnRegister) {            
            btnRegister.addEventListener('click', () => {

                if(document.getElementById('uid').value == '') {
                    // 배열을 전달하여 함수 호출
                    const fieldsArray = [
                        { id: 'employee', message: '사용자를 선택하세요', type: 'select' },
                        { id: 'loginId', message: '로그인 아이디를 입력하세요', type: 'text' },
                        { id: 'loginPwd', message: '로그인 비밀번호를 입력하세요', type: 'text'},
                        { id: 'auth', message: '권한을 선택하세요', type: 'select'}
                    ];
                } else {
                        // 배열을 전달하여 함수 호출
                    const fieldsArray = [
                        { id: 'employee', message: '사용자를 선택하세요', type: 'select' },
                        { id: 'loginId', message: '로그인 아이디를 입력하세요', type: 'text' },
                        { id: 'auth', message: '권한을 선택하세요', type: 'select'}
                    ];
                }

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

    getSelectList('getEmployeeList', 'uid', 'name', '#employee');
});    

// 사용자 등록
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
                        getUserList({page:1});
                        clearn();
                        closeModal('modalRegisterUser');
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

// 사용자 가져오기
const getter = async (uid) => {  
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getUser');
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
        document.getElementById('employee').value = data.employee;
        document.getElementById('loginId').value = data.loginId;
        document.getElementById('auth').value = data.auth;
    }
}

const clearn = () => {
    // input 클래스를 가진 모든 input 요소 선택
    const inputs = document.querySelectorAll('.input');
    inputs.forEach(input => input.value = ''); // 각 input 요소의 값을 빈 문자열로 설정

    // 모든 select 요소 선택
    const selects = document.querySelectorAll('.select');
    selects.forEach(select => select.value = '0'); // 각 select 요소의 값을 0으로 설정
};
</script>