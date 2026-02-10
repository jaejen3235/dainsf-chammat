<div class="modal" id="modalRegisterPurchaseItemIn">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>입고 등록</span>
			<span class="btn-close" id="btnClosePurchaseItemIn"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='purchaseInfrm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerPurchaseItemIn' />
                <input type='hidden' class='input' name='purchase_item_uid' id='purchase_item_uid' />
                <input type='hidden' class='input' name='fid' id='fid' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col />
                        <col width='150'>
                        <col />
                    </colgroup>
                    <tr>
                        <th>구분</th>
                        <td colspan='3'>
                            <span id='in_classification_name'></span>
                        </td>
                    </tr>
                        <th>품번</th>
                        <td>
                            <span id='in_item_code'></span>
                        </td>
                        <th>품명</th>
                        <td>
                            <span id='in_item_name'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>규격</th>
                        <td>
                            <span id='in_standard'></span>
                        </td>
                        <th>단위</th>
                        <td>
                            <span id='in_unit'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>구매 업체</th>
                        <td>
                            <span id='in_account'></span>
                        </td>
                        <th>구매 요청 수량</th>
                        <td>
                            <span id='in_purchase_qty'></span>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 입고 수량</th>
                        <td colspan='3'>
                            <input type='text' class='input' name='in_qty' id='in_qty' />
                        </td>
                    </tr>                     
                </table>
            </form>

            <div class='help'>
                <i class='bx bx-check'></i> 은 필수입력 사항입니다
            </div>

            <div class='btn-group'>
                <input type='button' class='modal-btn danger' id='btnRegisterPurchaseItemIn' value='저장' />&nbsp;
                <input type='button' class='modal-btn' id='btnClosePurchaseItemInModal' value='취소' />
            </div>
		</div>
	</div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    // 창닫기
    try {
        const btnClosePurchaseItemIn = document.getElementById('btnClosePurchaseItemIn');
        if(btnClosePurchaseItemIn) {
            btnClosePurchaseItemIn.addEventListener('click', function() {
                clean();
                closeModal('modalRegisterPurchaseItemIn');
            });
        }
    } catch(e) {}

    try {
        const btnClosePurchaseItemInModal = document.getElementById('btnClosePurchaseItemInModal');
        if(btnClosePurchaseItemInModal) {
            btnClosePurchaseItemInModal.addEventListener('click', function() {
                clean();
                closeModal('modalRegisterPurchaseItemIn');
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnRegisterPurchaseItemIn = document.getElementById('btnRegisterPurchaseItemIn');
        if (btnRegisterPurchaseItemIn) {            
            btnRegisterPurchaseItemIn.addEventListener('click', () => {
                // 배열을 전달하여 함수 호출
                const fieldsArray = [
                    { id: 'in_qty', message: '입고 수량을 입력하세요', type: 'text' }
                ];

                // 유효성 검사를 위한 함수 호출
                const isValid = validateFields(fieldsArray);
                if(isValid) registerPurchaseItemIn();
                else console.log('유효성 검사를 통과하지 못했습니다');
            });            
        } else {
            console.log('btnRegisterPurchaseItemIn button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }      
});    

// 품목 등록
const registerPurchaseItemIn = () => {    
    const frm = document.getElementById('purchaseInfrm');


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
                if(document.getElementById('fid').value) {
                    getPurchaseItemList({page:1}, document.getElementById('fid').value);
                } else {
                    getPurchaseItemList({page:1});
                }
                getPurchaseList({page : 1});
                clean();
                closeModal('modalRegisterPurchaseItemIn');
            } else {
                clean();
                alert(data.message);
            }
        }
    })
    .catch(error => console.log(error));
}


// 품목 가져오기
const getPurchaseItem = async (uid, fid = null) => {  
    document.getElementById('purchase_item_uid').value = uid;
    document.getElementById('fid').value = fid;

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
        setPurchaseItem(data);
    } catch (error) {
        displayError(error);
    }
}

const setPurchaseItem = (data) => {
    if (data) {        
        document.getElementById('in_classification_name').innerHTML = data.classification;
        document.getElementById('in_item_code').innerHTML = data.item_code;
        document.getElementById('in_item_name').innerHTML = data.item_name;
        document.getElementById('in_standard').innerHTML = data.standard;
        document.getElementById('in_unit').innerHTML = data.unit;
        document.getElementById('in_account').innerHTML = data.account_name;
        document.getElementById('in_purchase_qty').innerHTML = comma(data.purchase_qty);
    }
}
</script>