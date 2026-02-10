<div class="modal" id="registMachine">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>설비 등록</span>
			<span class="btn-close"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerMachine' />    
                <input type='hidden' class='input' name='uid' id='uid' />
                <input type='hidden' class='input' name='oldImg' id='oldImg' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col width='426'>
                        <col width='150'>
                        <col width='426'>
                    </colgroup>
                    <tr>
                        <th>설비ID</th>
                        <td>
                            <input type='text' class='input' name='machine_id' id='machine_id' />
                        </td>
                        <th>설비명</th>
                        <td colspan='3'>
                            <input type='text' class='input' name='machine_name' id='machine_name' />
                        </td>
                    </tr>
                    <tr>
                        <th>Model No.</th>
                        <td>
                            <input type='text' class='input' name='model_no' id='model_no' />
                        </td>
                        <th>소속 생산 라인</th>
                        <td>
                            <input type='text' class='input' name='production_line' id='production_line' />
                        </td>
                    </tr>
                    <tr>
                        <th>도입일</th>
                        <td>
                            <input type='text' class='input' name='introduction_date' id='introduction_date' />
                        </td>
                        <th>IoT Sensor ID</th>
                        <td>
                            <input type='text' class='input' name='iot_sensor_id' id='iot_sensor_id' />
                        </td>                        
                    </tr>
                    <tr>
                        <th>구매업체</th>
                        <td>
                            <input type='text' class='input' name='purchase_company' id='purchase_company' />
                        </td>
                        <th>연락처</th>
                        <td>
                            <input type='text' class='input' name='contact_person' id='contact_person' />
                        </td>
                    </tr>     
                    <tr>                                  
                        <th>첨부파일</th>
                        <td colspan='3'>
                            <input type='file' class='input' name='attach' id='attach' />
                        </td>
                    </tr>
                </table>
            </form>
            <div class='help'>
                ※ <i class='bx bx-check'></i> 은 필수입력 사항입니다
            </div>

            <div class='btn-group'>
                <input type='button' class='btn-large secondary' id='btnRegisterMachine' value='저장' />&nbsp;
                <input type='button' class='btn-large gray btn-close' value='취소' />
            </div>
		</div>
	</div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    // 창닫기
    try {
        const btnCloseList = document.querySelectorAll('.btn-close');
        if (btnCloseList && btnCloseList.length > 0) {
            btnCloseList.forEach(btn => {
                btn.addEventListener('click', function() {
                    clean();
                    closeModal('registMachine');
                });
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnRegisterMachine = document.getElementById('btnRegisterMachine');
        if (btnRegisterMachine) {            
            btnRegisterMachine.addEventListener('click', () => {
                // 배열을 전달하여 함수 호출
                const fieldsArray = [
                    { id: 'machine_id', message: '설비ID를 입력하세요', type: 'text' },
                    { id: 'machine_name', message: '설비명을 입력하세요', type: 'text' },
                    { id: 'model_no', message: 'Model No.를 입력하세요', type: 'text' },
                    { id: 'production_line', message: '소속 생산 라인을 입력하세요', type: 'text' },
                    { id: 'introduction_date', message: '도입일을 입력하세요', type: 'text' },
                    { id: 'iot_sensor_id', message: 'IoT Sensor ID를 입력하세요', type: 'text' },
                    { id: 'purchase_company', message: '구매업체를 입력하세요', type: 'text' },
                    { id: 'contact_person', message: '연락처를 입력하세요', type: 'text' },
                ];

                // 유효성 검사를 위한 함수 호출
                const isValid = validateFields(fieldsArray);
                if(isValid) registerMachine();
                else console.log('유효성 검사를 통과하지 못했습니다');                
            });            
        } else {
            console.log('btnRegisterMachine button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }
});    


// 설비 등록
const registerMachine = () => {    
    const frm = document.getElementById('frm');
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
                getMachineList({page:1});
                clearn();
                closeModal('registMachine');
            } else {
                displayError(data.message);
            }
        }
    })
    .catch(error => console.log(error));
}

// 설비정보 가져오기
const getterMachine = async (uid) => {  
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getMachine');
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
        setterMachine(data);
    } catch (error) {
        displayError(error);
    }
}

// 설비정보 설정
const setterMachine = async (data) => {
    if (data) {        
        document.getElementById('machine_id').value = data.machine_id;
        document.getElementById('machine_name').value = data.machine_name;
        document.getElementById('model_no').value = data.model_no;
        document.getElementById('production_line').value = data.production_line;
        document.getElementById('introduction_date').value = data.introduction_date;
        document.getElementById('iot_sensor_id').value = data.iot_sensor_id;
        document.getElementById('purchase_company').value = data.purchase_company;
        document.getElementById('contact_person').value = data.contact_person;
    }
}
</script>