<div class="modal" id="modalAdjustItemStock">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>재고 조정</span>
			<span class="btn-close" id="btnAdjustClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='adjustfrm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerAdjustItemStock' />
                <input type='hidden' class='input' name='adjust_item_uid' id='adjust_item_uid' />

                <table class='register'>
                    <colgroup>
                        <col width='15%'>
                        <col width='35%'/>
                        <col width='15%'>
                        <col width='35%'/>
                    </colgroup>
                    <tr>
                        <th>구분</th>
                        <td colspan='3'>
                            <span id='adjust_classification_name'></span>
                        </td>
                    </tr>
                        <th>품번</th>
                        <td>
                            <span id='adjust_item_code'></span>
                        </td>
                        <th>품명</th>
                        <td>
                            <span id='adjust_item_name'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>규격</th>
                        <td>
                            <span id='adjust_standard'></span>
                        </td>
                        <th>단위</th>
                        <td>
                            <span id='adjust_unit'></span>
                        </td>
                    </tr>
                    <tr>
                        <th>현 재고 수량</th>
                        <td>
                            <span id='adjust_current_stock_qty'></span>
                        </td>
                        <th>조정 재고 수량</th>
                        <td>
                            <input type='text' class='input' name='adjust_stock_qty' id='adjust_stock_qty' />
                        </td>
                    </tr>
                </table>
            </form>

            <div class='help'>
                <i class='bx bx-check'></i> 은 필수입력 사항입니다
            </div>

            <div class='btn-group'>
                <input type='button' class='modal-btn danger' id='btnAdjustRegister' value='저장' />&nbsp;
                <input type='button' class='modal-btn' id='btnAdjustCloseModal' value='취소' />
            </div>
		</div>
	</div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    // adjust_stock_qty 입력란에서 Enter를 누르면 adjust() 함수 호출
    try {
        const adjustStockQtyInput = document.getElementById('adjust_stock_qty');
        if (adjustStockQtyInput) {
            adjustStockQtyInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Enter의 기본 동작(폼 제출 등) 방지
                    // 유효성 검사와 동일하게 동작
                    const fieldsArray = [
                        { id: 'adjust_stock_qty', message: '조정 재고 수량을 입력하세요', type: 'text' }
                    ];
                    const isValid = validateFields(fieldsArray);
                    if(isValid) adjust();
                    else console.log('유효성 검사를 통과하지 못했습니다');
                }
            });
        }
    } catch(e) {}
    // 창닫기
    try {
        const btnAdjustClose = document.getElementById('btnAdjustClose');
        if(btnAdjustClose) {
            btnAdjustClose.addEventListener('click', function() {
                clean();
                closeModal('modalAdjustItemStock');
            });
        }
    } catch(e) {}

    try {
        const btnAdjustCloseModal = document.getElementById('btnAdjustCloseModal');
        if(btnAdjustCloseModal) {
            btnAdjustCloseModal.addEventListener('click', function() {
                clean();
                closeModal('modalAdjustItemStock');
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnAdjustRegister = document.getElementById('btnAdjustRegister');
        if (btnAdjustRegister) {            
            btnAdjustRegister.addEventListener('click', () => {
                // 배열을 전달하여 함수 호출
                const fieldsArray = [
                    { id: 'adjust_stock_qty', message: '조정 재고 수량을 입력하세요', type: 'text' }
                ];

                // 유효성 검사를 위한 함수 호출
                const isValid = validateFields(fieldsArray);
                if(isValid) adjust();
                else console.log('유효성 검사를 통과하지 못했습니다');
            });            
        } else {
            console.log('btnRegister button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }  
});    

// 품목 등록
const adjust = () => {    
    const frm = document.getElementById('adjustfrm');

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
                    //alert(data.message);
                    if(data.result == 'success') {
                        getItemList({page:document.getElementById('page').value});
                        clean();
                        closeModal('modalAdjustItemStock');
                    }                    
                }
            })
            .catch(error => console.log(error));
        }
    }
}


// 품목 가져오기
const adjustGetter = async (uid) => {  
    document.getElementById('adjust_item_uid').value = uid;

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
        setterAdjustItemStock(data);
    } catch (error) {
        displayError(error);
    }
}

const setterAdjustItemStock = (data) => {
    if (data) {        
        document.getElementById('adjust_classification_name').innerHTML = data.classification;        
        document.getElementById('adjust_item_code').innerHTML = data.item_code;
        document.getElementById('adjust_item_name').innerHTML = data.item_name;
        document.getElementById('adjust_standard').innerHTML = data.standard;
        document.getElementById('adjust_unit').innerHTML = data.unit;
        document.getElementById('adjust_current_stock_qty').innerHTML = data.stock_qty;
    }
}
</script>