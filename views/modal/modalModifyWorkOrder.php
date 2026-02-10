<div class="modal" id="modalModifyWorkOrder">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>생산지시서 수정</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='modify_frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerWorkOrder' />                
                <input type='text' class='input' name='modify_work_order_uid' id='modify_work_order_uid' />
                <input type='text' class='input' name='modify_remain' id='modify_remain' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col width='426'>
                        <col width='150'>
                        <col width='426'>
                    </colgroup>
                    <tr>
                        <th>거래처</th>
                        <td>
                            <span class='span' id='modify_account_name'></span>
                        </td>
                        <th>수주 품목</th>
                        <td colspan='3'>
                            <span class='span' id='modify_item_name'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>품번</th>
                        <td>
                            <span class='span' id='modify_item_code'></span>
                        </td>
                        <th>규격</th>
                        <td>
                            <span class='span' id='modify_standard'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>단위</th>
                        <td>
                            <span class='span' id='modify_unit'></span>
                        </td>
                        <th>수주 수량</th>
                        <td>
                            <span class='span' id='modify_qty'></span>
                        </td>
                        
                    </tr>
                    
                    <tr>
                        <th>수주일</th>
                        <td>
                            <span class='span' id='modify_obtain_date'></span>
                        </td>
                        <th>납기일</th>
                        <td>
                            <span class='span' id='modify_shipment_date'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>잔여 생산 수량</th>
                        <td colspan='3'>
                            <span class='span' id='modify_remain_work_order_qty'></span>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 생산지시 일자</th>
                        <td>
                            <input type='text' class='input datepicker' name='modify_order_date' id='modify_order_date' />
                        </td>
                        <th><i class='bx bx-check'></i> 생산지시 수량</th>
                        <td>
                            <input type='text' class='input' name='modify_order_qty' id='modify_order_qty' />
                        </td>
                    </tr>                    
                </table>
            </form>
            <div class='help'>
                ※ <i class='bx bx-check'></i> 은 필수입력 사항입니다
            </div>

            <div class='btn-group'>
                <input type='button' class='modal-btn danger' id='btnModifyWorkOrder' value='저장' />&nbsp;
                <input type='button' class='modal-btn' id='btnCloseModal' value='취소' />
            </div>
		</div>
	</div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    try {
        const delivery_uid = document.getElementById('delivery_uid');
    } catch(e) {}

    try {
        const order_qty = document.getElementById('order_qty');
        if(order_qty) {
            // 엔터키로도 이벤트 발생
            order_qty.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    register();
                }
            });
        }
    } catch(e) {}

    // 창닫기
    try {
        const btnClose = document.getElementById('btnClose');
        if(btnClose) {
            btnClose.addEventListener('click', function() {
                clean();
                cleanSpan()
                closeModal('modalModifyWorkOrder');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clean();
                cleanSpan();
                closeModal('modalModifyWorkOrder');
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnModifyWorkOrder = document.getElementById('btnModifyWorkOrder');
        if (btnModifyWorkOrder) {            
            btnModifyWorkOrder.addEventListener('click', () => {
                // 배열을 전달하여 함수 호출
                const fieldsArray = [
                    { id: 'modify_order_date', message: '생산지시 일자를 선택하세요', type: 'text' },
                    { id: 'modify_order_qty', message: '생산지시 수량을 입력하세요', type: 'text' },
                ];

                // 유효성 검사를 위한 함수 호출
                const isValid = validateFields(fieldsArray);
                if(isValid) modifyWorkOrder();
                else console.log('유효성 검사를 통과하지 못했습니다');                
            });            
        } else {
            console.log('btnRegister button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }
});    

// 수주 등록
const modifyWorkOrder = () => {    
    const frm = document.getElementById('modify_frm');

    if(frm) {
        if(check('modify_frm')) {
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
                        getOrdersList({page:1});
                        getWorkOrderList({page:1});
                        clean();
                        closeModal('modalRegisterWorkOrder');
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

const getModifyWorkOrderItem = async (uid) => {    
    document.getElementById('modify_work_order_uid').value = uid;

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
        setModifyWorkOrderItem(data);
    } catch (error) {
        displayError(error);
    }
}

const setModifyWorkOrderItem = (data) => {
    if (data) {           
        document.getElementById('modify_account_name').innerHTML = data.account_name;        
        document.getElementById('modify_item_name').innerHTML = data.item_name;
        document.getElementById('modify_item_code').innerHTML = data.item_code;
        document.getElementById('modify_standard').innerHTML = data.standard;
        document.getElementById('modify_unit').innerHTML = data.unit;        
        document.getElementById('modify_qty').innerHTML = comma(data.qty);
        document.getElementById('modify_obtain_date').innerHTML = data.order_date;
        document.getElementById('modify_shipment_date').innerHTML = data.shipment_date;        
        document.getElementById('modify_remain_work_order_qty').innerHTML = data.remain_work_order_qty;
        document.getElementById('modify_order_qty').value = data.order_qty;
        document.getElementById('modify_order_date').value = data.order_date;
        document.getElementById('modify_remain').value = data.order_qty;
    }
}
</script>