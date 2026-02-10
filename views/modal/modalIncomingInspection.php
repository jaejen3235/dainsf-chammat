<div class="modal" id="modalIncomingInspection">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>수입검사 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerIncomingInspection' />
                <input type='hidden' class='input' name='uid' id='uid' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col />
                        <col width='150'>
                        <col />
                    </colgroup>
                    <tr>
                        <th><i class='bx bx-check'></i> 품목명</th>
                        <td>
                            <input type='hidden' name='item_uid' id='item_uid' />
                            <input type='text' class='input' name='item_name' id='item_name' readonly />
                        </td>
                        <th><i class='bx bx-check'></i> 품번</th>
                        <td>
                            <input type='text' class='input' name='item_code' id='item_code' readonly />
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 규격</th>
                        <td>
                            <input type='text' class='input' name='standard' id='standard' readonly />
                        </td>
                        <th><i class='bx bx-check'></i> 단위</th>
                        <td>
                            <input type='text' class='input' name='unit' id='unit' readonly />
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 입고수량</th>
                        <td>
                            <input type='text' class='input' name='in_qty' id='in_qty' readonly />
                        </td>
                        <th><i class='bx bx-check'></i> 검사일자</th>
                        <td>
                            <input type='text' class='input datepicker' name='inspection_date' id='inspection_date' value='<?php echo date("Y-m-d"); ?>' />
                        </td>
                    </tr>
                    
                    <!-- 추가된 검사항목 1 -->
                    <tr>
                        <th><i class='bx bx-check'></i> 외관 검사</th>
                        <td>
                            <select class='input inspection-item' name='appearance_check' id='appearance_check'>
                                <option value='OK'>합격(OK)</option>
                                <option value='NG'>불합격(NG)</option>
                            </select>
                        </td>
                        <th><i class='bx bx-check'></i> 기능 검사</th>
                        <td>
                            <select class='input inspection-item' name='function_check' id='function_check'>
                                <option value='OK'>합격(OK)</option>
                                <option value='NG'>불합격(NG)</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th><i class='bx bx-check'></i> 검사자</th>
                        <td>
                            <select name='inspector' id='inspector'>
                                <option value=''>검사자를 선택해주세요</option>
                            </select>
                        </td>
                        <th><i class='bx bx-check'></i> 전체 검사결과</th>
                        <td>
                            <select class='input disabled-visual' name='inspection_result' id='inspection_result'>
                                <option value='OK' selected>합격(OK)</option>
                                <option value='NG'>불합격(NG)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>비고</th>
                        <td colspan='3'>
                            <input type='text' class='input' name='remark' id='remark' />
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
// 모든 검사항목이 다 OK이면 전체 검사결과도 자동 OK로 변경
document.addEventListener('DOMContentLoaded', function() {    
    function checkAllInspectionsOK() {
        const items = document.querySelectorAll('.inspection-item');
        for(let i=0; i<items.length; i++) {
            if(items[i].value !== 'OK') return false;
        }
        return true;
    }

    function updateInspectionResult() {
        const resultSelect = document.getElementById('inspection_result');
        if(checkAllInspectionsOK()) {
            resultSelect.value = 'OK';
            // 모두 OK일 때 처리할 코드. 예: 알림 표시
            // alert('모든 검사항목이 합격입니다!');
        } else {
            // 불합격 항목이 있으면 NG로 자동 변경
            resultSelect.value = 'NG';
        }
    }

    try {
        const btnClose = document.getElementById('btnClose');
        if(btnClose) {
            btnClose.addEventListener('click', function() {
                clean();
                closeModal('modalIncomingInspection');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clean();
                closeModal('modalIncomingInspection');
            });
        }
    } catch(e) {}

    // 검사항목 select 변경시 전체 검사결과 반영
    const inspectionItems = document.querySelectorAll('.inspection-item');
    inspectionItems.forEach(function(item){
        item.addEventListener('change', updateInspectionResult);
    });

    // 등록
    try {
        const btnRegister = document.getElementById('btnRegister');
        if (btnRegister) {            
            btnRegister.addEventListener('click', () => {
                // 배열을 전달하여 함수 호출
                const fieldsArray = [
                    { id: 'inspector', message: '검사자를 선택해주세요', type: 'select' },                    
                    { id: 'inspection_date', message: '검사일자를 선택해주세요', type: 'date' }
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

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clean();
                closeModal('modalIncomingInspection');
            });
        }
    } catch(e) {}

    getInspectorList();
});


// 카테고리 등록
const register = () => {        
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
                clean();
                closeModal('modalIncomingInspection');
                getItemsInOutList({page:1});
            } else {
                clean();
                alert(data.message);
            }
        }
    })
    .catch(error => console.log(error));
}

const getInspectorList = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getAllEmployeeList');

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        if(data != null || data != '') {
            if(data.result == 'success') {
                const inspector = document.getElementById('inspector');
                data.data.forEach(item => {
                    inspector.innerHTML += `<option value='${item.uid}'>${item.name}</option>`;
                });
            }
        }
    } catch (error) {
        displayError(error);
    }
}

const getter = async (uid) => {
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getPurchaseItem');
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
        document.getElementById('item_uid').value = data.uid;
        document.getElementById('item_name').value = data.item_name;
        document.getElementById('item_code').value = data.item_code;
        document.getElementById('standard').value = data.standard;
        document.getElementById('unit').value = data.unit;
        document.getElementById('in_qty').value = data.purchase_qty;
    }
}
</script>