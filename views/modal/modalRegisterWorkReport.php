<div class="modal" id="modalRegisterWorkReport">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>작업일보 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerWorkReport' />
                <input type='hidden' name='uid' id='uid' />
                <input type='hidden' name='work_order_uid' id='work_order_uid' />
                <input type='hidden' name='item_uid' id='item_uid' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col width='426'>
                        <col width='150'>
                        <col width='426'>
                    </colgroup>
                    <tr>
                        <th><i class='bx bx-check'></i>작업 일자</th>
                        <td>
                            <input type='text' class='input datepicker' name='work_date' id='work_date' value='<?php echo date('Y-m-d'); ?>' />
                        </td>
                        <th><i class='bx bx-check'></i>작업자</th>
                        <td>
                            <select class='input' name='worker' id='worker'>
                                <option value='0'>== 선택 ==</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>작업 품목</th>
                        <td>
                            <span class='span' id='item_name'></span>
                        </td>
                        <th>작업 품번</th>
                        <td>
                            <span class='span' id='item_code'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>규격</th>
                        <td>
                            <span class='span' id='standard'></span>
                        </td>
                        <th>단위</th>
                        <td>
                            <span class='span' id='unit'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>작업 지시 수량</th>
                        <td>
                            <span class='span' id='work_order_qty'></span>
                        </td>
                        <th>잔여 작업 수량</th>
                        <td>
                            <span class='span' id='remain_qty'></span>
                        </td>
                    </tr>
                    <tr>                        
                        <th><i class='bx bx-check'></i> 작업 수량</th>
                        <td colspan='3'>
                            <input type='text' class='input' name='work_qty' id='work_qty' />
                        </td>
                    </tr>
                    <!--
                        <th>적격 수량</th>
                        <td>
                            <input type='text' class='input' name='qualified_qty' id='qualified_qty' onkeyup='calcDefectQty()' />                            
                        </td>
                    </tr>
                    <tr>
                        <th>부적격 수량</th>
                        <td>
                            <input type='text' class='input' name='defect_qty' id='defect_qty' readonly />
                        </td>
                        <th>불량 사유</th>
                        <td>
                            <select class='input' name='defect_reason' id='defect_reason'>
                                <option value='0'>== 선택 ==</option>
                            </select>
                        </td>
                    </tr>  
                    -->                 
                </table>
            </form>            
            <div class='help mt5'>
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
    // 창닫기
    try {
        const btnClose = document.getElementById('btnClose');
        if(btnClose) {
            btnClose.addEventListener('click', function() {
                clean();
                cleanSpan();
                closeModal('modalRegisterWorkReport');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clean();
                cleanSpan();
                closeModal('modalRegisterWorkReport');
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
                    { id: 'work_date', message: '작업 일자를 선택하세요', type: 'text' },                    
                    { id: 'worker', message: '작업자를 선택하세요', type: 'select' },
                    { id: 'work_qty', message: '작업 수량을 입력하세요', type: 'text' },
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
    
    getSelectList('getAllEmployeeList', 'uid', 'name', '#worker');
});    

// 작업일지 등록
const register = () => {    
    const frm = document.getElementById('frm');

    /*
    const defectQty = Number(document.getElementById('defect_qty').value);
    const defectReason = document.getElementById('defect_reason').value;

    if(defectQty === 0) {
        // 부적격 수량이 0이면 불량사유를 == 선택 == 으로 변경
        document.getElementById('defect_reason').value = '0';
    } else if(defectQty > 0) {
        // 부적격 수량이 0보다 크면 불량사유 선택 활성화 및 선택 확인        
        if(!defectReason || defectReason === '0') {
            alert('불량 사유를 선택하지 않으셨습니다. 불량 사유를 선택해주세요.');
            document.getElementById('defect_reason').focus();
            return;
        }
    }
    */

    const workQty = Number(document.getElementById('work_qty').value);
    /*
    const qualifiedQty = Number(document.getElementById('qualified_qty').value);

    if (qualifiedQty > workQty) {
        alert('적격 수량이 총 작업 수량보다 많을 수 없습니다.');
        document.getElementById('qualified_qty').focus();
        return;
    }
    */

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
                        getWorkOrderList({page:1});    
                        getWorkReportList({page:1});
                    }

                    clean();
                    alert(data.message);
                    closeModal('modalRegisterWorkReport');
                }
            })
            .catch(error => console.log(error));
        }
    }
}

// 작업지시서 정보 가져오기
const getter = async (uid) => {        
    document.getElementById('work_order_uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getWorkOrder');
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

// 작업지시서 정보 설정
const setter = (data) => {
    if (data) {                
        document.getElementById('work_date').value = data.order_date;        
        document.getElementById('item_uid').value = data.item_uid;
        document.getElementById('item_name').innerHTML = data.item_name;
        document.getElementById('item_code').innerHTML = data.item_code;
        document.getElementById('standard').innerHTML = data.standard;
        document.getElementById('unit').innerHTML = data.unit;
        document.getElementById('work_order_qty').innerHTML = data.order_qty;
        document.getElementById('work_qty').value = data.order_qty;
        document.getElementById('remain_qty').innerHTML = data.remain_qty;

        // worker select box의 첫번째 option을 자동 선택
        const workerSelect = document.getElementById('worker');
        if (workerSelect && workerSelect.options.length > 0) {
            workerSelect.selectedIndex = 1;
        }
    }
}

// 작업일보(수정) 정보 가져오기
const getWorkOrder = async (uid) => {  
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDailyWork');
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
        setWorkOrder(data);
    } catch (error) {
        displayError(error);
    }
}

// 작업일보(수정) 정보 설정
const setWorkOrder = (data) => {
    if (data) {   
        document.querySelector('.modal-title').innerHTML = '작업일보 수정';
        document.getElementById('work_date').value = data.work_date;
        document.getElementById('work_order_uid').value = data.work_order_uid;

        // worker select에서 text가 data.worker와 같은 option을 선택
        const workerSelect = document.getElementById('worker');
        if(workerSelect) {
            for(let i=0; i<workerSelect.options.length; i++) {
                if(workerSelect.options[i].text === data.worker.trim()) {
                    workerSelect.selectedIndex = i;
                    break;
                }
            }
        }
        
        
        document.getElementById('item_name').innerHTML = data.item_name;
        document.getElementById('item_code').innerHTML = data.item_code;
        document.getElementById('standard').innerHTML = data.standard;
        document.getElementById('unit').innerHTML = data.unit;
        document.getElementById('work_order_qty').innerHTML = data.work_order_qty;        
        document.getElementById('remain_qty').innerHTML = data.remain_qty;   
        document.getElementById('work_qty').value = data.work_qty;
    }
}

const calcDefectQty = () => {
    const work_qty = document.getElementById('work_qty').value;
    const qualified_qty = document.getElementById('qualified_qty').value;
    const defect_qty = work_qty - qualified_qty;
    document.getElementById('defect_qty').value = defect_qty;
}
</script>