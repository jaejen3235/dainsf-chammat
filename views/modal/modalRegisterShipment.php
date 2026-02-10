<div class="modal" id="modalRegisterShipment">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>출하지시 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerDeliveryOrder' />
                <input type='hidden' class='input' name='uid' id='uid' value="<?php echo $_GET['uid']; ?>" />
                <input type='hidden' class='input' name='remain' id='remain' value='0' />
                <input type='hidden' class='input' name='stock' id='stock' value='0' />

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
                            <span id='account_name'></span>
                        </td>
                        <th>수주 품목</th>
                        <td>
                            <span id='item_name'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>품번</th>
                        <td>
                            <span id='item_code'></span>
                        </td>
                        <th>규격</th>
                        <td>
                            <span id='standard'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>단위</th>
                        <td>
                            <span id='unit'></span>
                        </td>
                        <th>수주 수량</th>
                        <td>
                            <span id='qty'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>수주일</th>
                        <td>
                            <span id='order_date'></span>
                        </td>
                        <th>납기일</th>
                        <td>
                            <span id='shipment_date'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>현재 재고 수량</th>
                        <td>
                            <span id='stock_qty'></span>
                        </td>
                        <th>잔여 납품 수량</th>
                        <td>
                            <span id='delivery_remain_qty'></span>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 출하지시일</th>
                        <td>
                            <input type='text' class='input datepicker' name='delivery_date' id='delivery_date' value='<?php echo date('Y-m-d'); ?>' />
                        </td>
                        <th><i class='bx bx-check'></i> 출하지시 수량</th>
                        <td>
                            <input type='text' class='input' name='delivery_qty' id='delivery_qty' />
                        </td>
                    </tr>                    
                </table>
            </form>
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
                clean();
                closeModal('modalRegisterShipment');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clean();
                closeModal('modalRegisterShipment');
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
                    { id: 'delivery_date', message: '출하지시일을 선택하세요', type: 'text' },
                    { id: 'delivery_qty', message: '출하지시수량을 입력하세요', type: 'text' },
                ];

                // 유효성 검사를 위한 함수 호출
                const isValid = validateFields(fieldsArray);
                if(isValid) registerShipment();
                else console.log('유효성 검사를 통과하지 못했습니다');
            });            
        } else {
            console.log('btnRegister button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }
});    

// 납품 등록
const registerShipment = () => {    
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
                        getOrdersItemList({page:1});
                        clean();
                        closeModal('modalRegisterShipment');
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

const getShipmentItem = async (uid) => {
    document.getElementById('uid').value = uid;

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
        setShipmentItem(data);
    } catch (error) {
        displayError(error);
    }
}

const setShipmentItem = (data) => {
    if (data) {        
        document.getElementById('account_name').innerHTML = data.account_name;
        document.getElementById('item_code').innerHTML = data.item_code;
        document.getElementById('standard').innerHTML = data.standard;
        document.getElementById('unit').innerHTML = data.unit;
        document.getElementById('item_name').innerHTML = data.item_name;
        document.getElementById('qty').innerHTML = comma(data.qty);
        document.getElementById('delivery_remain_qty').innerHTML = comma(data.delivery_remain_qty);
        document.getElementById('order_date').innerHTML = data.order_date;
        document.getElementById('shipment_date').innerHTML = data.shipment_date;  
        const sd = 3;
        // data.shipment_date 에 sd 만큼 날짜를 추가해서 value 에 삽입
        const shipmentDate = new Date(data.order_date);
        shipmentDate.setDate(shipmentDate.getDate() + sd);
        const yyyy = shipmentDate.getFullYear();
        const mm = String(shipmentDate.getMonth() + 1).padStart(2, '0');
        const dd = String(shipmentDate.getDate()).padStart(2, '0');
        document.getElementById('delivery_date').value = `${yyyy}-${mm}-${dd}`;

        document.getElementById('delivery_qty').value = data.delivery_remain_qty;
        document.getElementById('stock_qty').innerHTML = comma(data.stock_qty);
        document.getElementById('stock').value = data.stock_qty;
    }
}
</script>
