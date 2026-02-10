<div class="modal" id="modalModifyPurchase">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>구매 요청 수정</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerPurchase' />
                <input type='hidden' class='input' name='uid' id='uid' />
                <input type='hidden' class='input' name='item_uid' id='item_uid' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col />
                        <col width='150'>
                        <col />
                    </colgroup>
                    <tr>
                        <th>구분</th>
                        <td>
                            <span id='classification_name'></span>
                        </td>
                        <th>그룹</th>
                        <td>
                            <span id='group_name'></span>
                        </td>
                    </tr>
                        <th>품번</th>
                        <td>
                            <span id='item_code'></span>
                        </td>
                        <th>품명</th>
                        <td>
                            <span id='item_name'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>규격</th>
                        <td>
                            <span id='standard'></span>
                        </td>
                        <th>단위</th>
                        <td>
                            <span id='unit'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>재고 수량</th>
                        <td>
                            <span id='stock_qty'></span>
                        </td>
                        <th>안전재고 수량</th>
                        <td>
                            <span id='safety_stock_qty'></span>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 구매 업체</th>
                        <td>
                            <select name='account' id='account'>
                                <option value='0'>== 선택 ==</option>
                            </select>
                        </td>
                        <th><i class='bx bx-check'></i> 구매 요청 수량</th>
                        <td>
                            <input type='text' class='input' name='purchase_qty' id='purchase_qty' />
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
    // 창닫기
    try {
        const btnClose = document.getElementById('btnClose');
        if(btnClose) {
            btnClose.addEventListener('click', function() {
                clean();
                closeModal('modalModifyPurchase');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clean();
                closeModal('modalModifyPurchase');
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
                    { id: 'purchase_qty', message: '구매 요청 수량을 입력하세요', type: 'text' }
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

    getSelectList('getAccountList', 'uid', 'name', '#account', "where classification='매입'");
});    

// 품목 등록
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
                        getPurchaseList({page : 1});
                        getPurchaseItemList({page : 1});
                        clean();
                        closeModal('modalModifyPurchase');
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


// 품목 가져오기
const getter = async (uid) => {  
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getPurchase');
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
        document.getElementById('classification_name').innerHTML = data.data.classification_name;
        document.getElementById('group_name').innerHTML = data.data.group_name;
        document.getElementById('item_uid').value = data.data.item_uid;
        document.getElementById('item_code').innerHTML = data.data.item_code;
        document.getElementById('item_name').innerHTML = data.data.item_name;
        document.getElementById('standard').innerHTML = data.data.standard;
        document.getElementById('unit').innerHTML = data.data.unit;
        document.getElementById('stock_qty').innerHTML = data.data.stock_qty;
        document.getElementById('safety_stock_qty').innerHTML = data.data.safety_stock_qty;
        document.getElementById('purchase_qty').value = data.data.purchase_qty; 
        document.getElementById('account').value = data.data.account_uid;
    }
}
</script>