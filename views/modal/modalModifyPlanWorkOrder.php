<div class="modal" id="modalModifyPlanWorkOrder">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>계획 생산지시 수정</span>
			<span class="btn-close" id="modifyPlanClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='modify_plan_frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='modifyPlanWorkOrder' />
                <input type='hidden' name='modify_plan_uid' id='modify_plan_uid' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col width='426'>
                        <col width='150'>
                        <col width='426'>
                    </colgroup>
                    <tr>
                        <th>품목 그룹</th>
                        <td>
                            <span id='span_plan_group'></span>
                        </td>
                        <th>작업 품목</th>
                        <td colspan='3'>
                            <span id='span_plan_item'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>성별</th>
                        <td>
                            <span id='span_plan_gender'></span>
                        </td>
                        <th>사이즈</th>
                        <td>
                            <span id='span_plan_size'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>색상</th>
                        <td>
                            <span id='span_plan_color'></span>
                        </td>
                        <th>재고 수량</th>
                        <td>
                            <span id='span_stock_qty'></span>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 생산지시 일자</th>
                        <td>
                            <input type='text' class='input datepicker' name='modify_plan_work_date' id='modify_plan_work_date' />
                        </td>
                        <th><i class='bx bx-check'></i> 생산지시 수량</th>
                        <td>
                            <input type='text' class='input' name='modify_plan_work_qty' id='modify_plan_work_qty' />
                        </td>
                    </tr>                    
                </table>
            </form>
            <hr />
            <div class='help'>
                ※ <i class='bx bx-check'></i> 은 필수입력 사항입니다
            </div>

            <div class='btn-group'>
                <input type='button' class='modal-btn danger' id='btnModifyPlanRegister' value='등록' />&nbsp;
                <input type='button' class='modal-btn' id='btnModifyPlanClose' value='취소' />
            </div>
		</div>
	</div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    // 창닫기
    try {
        const modifyPlanClose = document.getElementById('modifyPlanClose');
        if(modifyPlanClose) {
            modifyPlanClose.addEventListener('click', function() {
                clean();
                document.getElementById('span_stock_qty').innerHTML = '';
                closeModal('modalModifyPlanWorkOrder');
            });
        }
    } catch(e) {}

    try {
        const btnModifyPlanClose = document.getElementById('btnModifyPlanClose');
        if(btnModifyPlanClose) {
            btnModifyPlanClose.addEventListener('click', function() {
                clean();
                document.getElementById('span_stock_qty').innerHTML = '';
                closeModal('modalModifyPlanWorkOrder');
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnModifyPlanRegister = document.getElementById('btnModifyPlanRegister');
        if (btnModifyPlanRegister) {            
            btnModifyPlanRegister.addEventListener('click', () => {
                // 배열을 전달하여 함수 호출
                const fieldsArray = [
                    { id: 'modify_plan_work_date', message: '생산지시 일자를 선택하세요', type: 'text' },
                    { id: 'modify_plan_work_qty', message: '생산지시 수량을 입력하세요', type: 'text' },
                ];

                // 유효성 검사를 위한 함수 호출
                const isValid = validateFields(fieldsArray);
                if(isValid) modifyPlanRegister();
                else console.log('유효성 검사를 통과하지 못했습니다');                
            });            
        } else {
            console.log('btnRegister button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }
});    

// 계획 생산지시 등록
const modifyPlanRegister = () => {    
    const frm = document.getElementById('modify_plan_frm');

    if(frm) {
        if(check('modify_plan_frm')) {
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
                        getWorkOrderList({page:1});
                        clean();
                        closeModal('modalModifyPlanWorkOrder');
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

// 수정시 데이터 가져오기
const getPlanWorkOrder = async (uid) => {
    document.getElementById('modify_plan_uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getPlanWorkOrder');
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
        setPlanWorkOrderData(data);
    } catch (error) {
        displayError(error);
    }
}

// 수정시 데이터 설정
const setPlanWorkOrderData = (data) => {
    if (data) {                
        document.getElementById('span_plan_group').innerHTML = data.group_uid;
        document.getElementById('span_plan_item').innerHTML = data.item_uid;
        document.getElementById('span_plan_gender').innerHTML = data.gender;
        document.getElementById('span_plan_size').innerHTML = data.size_uid;
        document.getElementById('span_plan_color').innerHTML = data.color_uid;
        document.getElementById('modify_plan_work_date').value = data.work_date;
        document.getElementById('modify_plan_work_qty').value = data.work_qty;
        document.getElementById('span_stock_qty').innerHTML = comma(data.stock_qty);
    }
}
</script>