<div class="modal" id="modalRegisterPlanWorkOrder">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>계획 생산지시 등록</span>
			<span class="btn-close" id="btnPlanIconClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='plan_frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerPlanWorkOrder' />
                <input type='hidden' name='plan_uid' id='plan_uid' />
                <input type='hidden' class='input' name='plan_remain' id='plan_remain' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col width='426'>
                        <col width='150'>
                        <col width='426'>
                    </colgroup>
                    <tr>
                        <th>작업 품목</th>
                        <td>
                            <select class='input' name='plan_item' id='plan_item'>
                                <option value='0'>== 선택 ==</option>
                            </select>
                        </td>
                        <th>품번</th>
                        <td>
                            <span class='span'id='plan_item_code'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>규격</th>
                        <td>
                            <span class='span' id='plan_standard'></span>
                        </td>
                        <th>단위</th>
                        <td>
                            <span class='span' id='plan_unit'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>재고 수량</th>
                        <td colspan='3'>
                            <span class='span stock_qty'></span>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 생산지시 일자</th>
                        <td>
                            <input type='text' class='input datepicker' name='plan_order_date' id='plan_order_date' />
                        </td>
                        <th><i class='bx bx-check'></i> 생산지시 수량</th>
                        <td>
                            <input type='text' class='input' name='plan_order_qty' id='plan_order_qty' />
                        </td>
                    </tr>                    
                </table>
            </form>
            <div class='help'>
                ※ <i class='bx bx-check'></i> 은 필수입력 사항입니다
            </div>

            <div class='btn-group'>
                <input type='button' class='modal-btn danger' id='btnPlanRegister' value='등록' />&nbsp;
                <input type='button' class='modal-btn' id='btnPlanClose' value='취소' />
            </div>
		</div>
	</div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    // 창닫기
    try {
        const btnPlanIconClose = document.getElementById('btnPlanIconClose');
        if(btnPlanIconClose) {
            btnPlanIconClose.addEventListener('click', function() {
                clean();
                cleanSpan();
                document.getElementById('stock_qty').innerHTML = '';
                closeModal('modalRegisterPlanWorkOrder');
            });
        }
    } catch(e) {}

    try {
        const btnPlanClose = document.getElementById('btnPlanClose');
        if(btnPlanClose) {
            btnPlanClose.addEventListener('click', function() {
                clean();
                cleanSpan();
                document.getElementById('stock_qty').innerHTML = '';
                closeModal('modalRegisterPlanWorkOrder');
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnPlanRegister = document.getElementById('btnPlanRegister');
        if (btnPlanRegister) {            
            btnPlanRegister.addEventListener('click', () => {
                // 배열을 전달하여 함수 호출
                const fieldsArray = [
                    { id: 'plan_order_date', message: '생산지시 일자를 선택하세요', type: 'text' },
                    { id: 'plan_order_qty', message: '생산지시 수량을 입력하세요', type: 'text' },
                ];

                // 유효성 검사를 위한 함수 호출
                const isValid = validateFields(fieldsArray);
                if(isValid) planRegister();
                else console.log('유효성 검사를 통과하지 못했습니다');                
            });            
        } else {
            console.log('btnRegister button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }

    getSelectList('getItemList', 'uid', 'item_name', '#plan_item', `where classification='완제품'`, 'code');

    try {
        const plan_item = document.getElementById('plan_item');
        if(plan_item) {
            plan_item.addEventListener('change', () => {
                getItemData(plan_item.value);
            });
        }
    } catch(e) {}
});    

const getItemData = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getItem');
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
        setItemData(data);
    } catch (error) {
        displayError(error);
    }
}

const setItemData = (data) => {
    if (data) {        
        document.getElementById('plan_item_code').innerHTML = data.item_code;
        document.getElementById('plan_standard').innerHTML = data.standard;
        document.getElementById('plan_unit').innerHTML = data.unit;
        document.querySelector('.stock_qty').innerHTML = comma(data.stock_qty);
    }
}

// 계획 생산지시 등록
const planRegister = () => {    
    const frm = document.getElementById('plan_frm');

    if(frm) {
        if(check('plan_frm')) {
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
                        cleanSpan();
                        closeModal('modalRegisterPlanWorkOrder');
                    } else {
                        clean();
                        cleanSpan();
                        alert(data.message);
                    }
                }
            })
            .catch(error => console.log(error));
        }
    }
}
</script>