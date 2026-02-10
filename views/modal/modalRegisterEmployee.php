<div class="modal" id="modalRegisterEmployee">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>사원 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerEmployee' />
                <input type='hidden' class='input' name='uid' id='uid' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col />
                        <col width='150'>
                        <col />
                    </colgroup>
                    <tr>
                        <th><i class='bx bx-check'></i> 이름</th>
                        <td>
                            <input type='text' class='input' name='name' id='name' />
                        </td>
                        <th><i class='bx bx-check'></i> 성별</th>
                        <td>
                            <label>
                                <input type="radio" name="gender" id='gender1' value="남성" checked />
                                <span>남성</span>
                            </label>
                            <label>
                                <input type="radio" name="gender" id='gender2' value="여성" />
                                <span>여성</span>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th>부서</th>
                        <td>
                            <input type='text' class='input' name='department' id='department' />
                        </td>
                        <th>직급</th>
                        <td>
                            <input type='text' class='input' name='rank' id='rank' />
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 연락처</th>
                        <td>
                            <input type='text' class='input' name='mobile' id='mobile' />                            
                        </td>
                        <th>이메일</th>
                        <td>
                            <input type='text' class='input' name='email' id='email' />
                        </td>
                    </tr>                    
                    <tr>
                        <th>주소</th>
                        <td colspan='3'>
                            <input type='text' class='input' name='address' id='address' />
                        </td>
                    </tr>
                </table>
            </form>
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
                closeModal('modalRegisterEmployee');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterEmployee');
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
                    { id: 'name', message: '이름을 입력하세요', type: 'text' },                    
                    { id: 'mobile', message: '연락처를 입력하세요', type: 'text' }
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

// 사원 등록
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
                        getEmployeeList({page:1});
                        clearn();
                        closeModal('modalRegisterEmployee');
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

// 사원 가져오기
const getter = async (uid) => {  
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getEmployee');
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
        document.getElementById('department').value = data.department;
        document.getElementById('rank').value = data.rank;
        document.getElementById('mobile').value = data.mobile;
        document.getElementById('email').value = data.email;
        document.getElementById('address').value = data.address;
    }
}

const clearn = () => {
    const inputs = document.querySelectorAll('.input'); // input 클래스를 가진 모든 요소 선택
    inputs.forEach(input => input.value = ''); // 각 요소의 값을 빈 문자열로 설정
};
</script>