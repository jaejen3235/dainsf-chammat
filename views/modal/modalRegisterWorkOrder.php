<div class="modal" id="modalRegisterWorkOrder">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>수주 생산지시 등록</span>
			<span class="btn-close" id="btnWorkOrderClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerWorkOrder' />    
                <input type='hidden' name='order_items_uid' id='order_items_uid' />
                <input type='hidden' class='input' name='work_order_uid' id='work_order_uid' />

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
                            <span class='span'id='account_name'></span>
                        </td>
                        <th>수주 품목</th>
                        <td colspan='3'>
                            <span class='span' id='item_name'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>품번</th>
                        <td>
                            <span class='span'id='item_code'></span>
                        </td>
                        <th>규격</th>
                        <td>
                            <span class='span'id='standard'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>단위</th>
                        <td>
                            <span class='span' id='unit'></span>
                        </td>
                        <th>수주 수량</th>
                        <td>
                            <span class='span'id='qty'></span>
                        </td>
                        
                    </tr>
                    
                    <tr>
                        <th>수주일</th>
                        <td>
                            <span class='span' id='obtain_date'></span>
                        </td>
                        <th>납기일</th>
                        <td>
                            <span class='span'id='shipment_date'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>현재 재고수량</th>
                        <td colspan='3'>
                            <span class='span'id='stock_qty'></span>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 생산지시 일자</th>
                        <td>
                            <input type='text' class='input datepicker' name='order_date' id='order_date' />
                        </td>
                        <th><i class='bx bx-check'></i> 생산지시 수량</th>
                        <td>
                            <input type='text' class='input' name='order_qty' id='order_qty' />
                        </td>
                    </tr>                    
                </table>
            </form>
            <div class='help'>
                ※ <i class='bx bx-check'></i> 은 필수입력 사항입니다
            </div>

            <div class='btn-group'>
                <input type='button' class='modal-btn danger' id='btnRegisterWorkOrder' value='저장' />&nbsp;
                <input type='button' class='modal-btn' id='btnCloseWorkOrderModal' value='취소' />
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
                    btnRegisterWorkOrder();
                }
            });
        }
    } catch(e) {}

    // 창닫기
    try {
        const btnWorkOrderClose = document.getElementById('btnWorkOrderClose');
        if(btnWorkOrderClose) {
            btnWorkOrderClose.addEventListener('click', function() {
                clean();
                cleanSpan()
                closeModal('modalRegisterWorkOrder');
            });
        }
    } catch(e) {}

    try {
        const btnCloseWorkOrderModal = document.getElementById('btnCloseWorkOrderModal');
        if(btnCloseWorkOrderModal) {
            btnCloseWorkOrderModal.addEventListener('click', function() {
                clean();
                cleanSpan();
                closeModal('modalRegisterWorkOrder');
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnRegisterWorkOrder = document.getElementById('btnRegisterWorkOrder');
        if (btnRegisterWorkOrder) {            
            btnRegisterWorkOrder.addEventListener('click', () => {
                // 배열을 전달하여 함수 호출
                const fieldsArray = [
                    { id: 'order_date', message: '생산지시 일자를 선택하세요', type: 'text' },
                    { id: 'order_qty', message: '생산지시 수량을 입력하세요', type: 'text' },
                ];

                // 유효성 검사를 위한 함수 호출
                const isValid = validateFields(fieldsArray);
                if(isValid) registerWorkOrder();
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
const registerWorkOrder = () => {    
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
                        getOrdersList({page:document.getElementById('currentPage').value});
                        getWorkOrderList({page:document.getElementById('currentWorkOrderPage').value});
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

const getterWorkOrderItem = async (uid) => {
    document.getElementById('order_items_uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getOrderItem');
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
        setterWorkOrderItem(data);
    } catch (error) {
        displayError(error);
    }
}

const setterWorkOrderItem = (data) => {
    if (data) {        
        document.getElementById('account_name').innerHTML = data.account_name;        
        document.getElementById('item_name').innerHTML = data.item_name;
        document.getElementById('item_code').innerHTML = data.item_code;
        document.getElementById('standard').innerHTML = data.standard;
        document.getElementById('unit').innerHTML = data.unit;        
        document.getElementById('qty').innerHTML = comma(data.qty);
        document.getElementById('order_qty').value = data.qty;
        document.getElementById('obtain_date').innerHTML = data.order_date;
        document.getElementById('order_date').value = data.order_date;
        document.getElementById('shipment_date').innerHTML = data.shipment_date;        
        document.getElementById('stock_qty').innerHTML = comma(data.stock_qty);        
    }
}
</script>