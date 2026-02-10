<div class="modal" id="modalRegisterBlueprint">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>도면 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerBlueprint' />
                <input type='hidden' class='input' name='uid' id='uid' />
                <input type='hidden' class='input' name='oldFile' id='oldFile' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col />
                    </colgroup>
                    <tr>
                        <td><i class='bx bx-check'></i> 거래처</td>
                        <td>
                            <select name='account' id='account'>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 도면 명칭</td>
                        <td>
                            <input type='text' class='input' name='name' id='name' />
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 도면</td>
                        <td>
                            <input type='file' class='input' name='blueprint' id='blueprint' />
                        </td>
                    </tr>
                </table>
            </form>
            <hr />
            <div class='help'>
                ※ <i class='bx bx-check'></i> 은 필수입력 사항입니다
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
                closeModal('modalRegisterBlueprint');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterBlueprint');
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
                    { id: 'account', message: '거래처를 선택하세요', type: 'select' },
                    { id: 'name', message: '도면명칭을 입력하세요', type: 'text' },
                    { id: 'blueprint', message: '도면을 선택하세요', type: 'text' }
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

    getSelectList('getAccountList', 'uid', 'name', '#account');
});    

// 수주 등록
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
                        getBlueprintList({page:1});
                        clearn();
                        closeModal('modalRegisterBlueprint');
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

const getter = async (uid) => {
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getBlueprint');
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
        document.getElementById('account').value = data.account;
        document.getElementById('name').value = data.name;
        document.getElementById('oldFile').value = data.blueprint;
    }
}

const clearn = () => {
    const inputs = document.querySelectorAll('.input'); // input 클래스를 가진 모든 요소 선택
    inputs.forEach(input => input.value = ''); // 각 요소의 값을 빈 문자열로 설정
};
</script>