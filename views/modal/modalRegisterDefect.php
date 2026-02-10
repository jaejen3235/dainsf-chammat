<div class="modal" id="modalRegisterDefect">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>불량 유형 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerDefect' />
                <input type='hidden' class='input' name='uid' id='uid' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col />
                    </colgroup>
                    <tr>
                        <th><i class='bx bx-check'></i> 불량 유형명</th>
                        <td>
                            <input type='text' class='input' name='defect_name' id='defect_name' />
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 불량 증상</th>
                        <td>
                            <textarea class='input' name='defect_symptom' id='defect_symptom'></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 불량 처리 방법</th>
                        <td>
                            <textarea class='input' name='defect_process' id='defect_process'></textarea>                            
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
                clean();
                closeModal('modalRegisterDefect');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clean();
                closeModal('modalRegisterDefect');
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
                    { id: 'defect_name', message: '불량유형명을 입력하세요', type: 'text' },
                    { id: 'defect_symptom', message: '불량 증상을 입력하세요', type: 'text' },
                    { id: 'defect_process', message: '불량 처리 방법을 입력하세요', type: 'text' }
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

// 불량유형 등록
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
                        getDefectList({page:1});
                        clean();
                        closeModal('modalRegisterDefect');
                    } else {
                        clean();
                        alert(data.message);
                    }

                }
            })
            .catch(error => console.log(error));
        }
    }
}

// 불량유형 가져오기
const getter = async (uid) => {  
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDefect');
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
        document.getElementById('defect_name').value = data.defect_name;
        document.getElementById('defect_symptom').value = data.defect_symptom;
        document.getElementById('defect_process').value = data.defect_process;
    }
}
</script>