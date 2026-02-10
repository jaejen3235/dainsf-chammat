<div class="modal" id="modalRegisterStockClose">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>재고 마감 등록</span>
			<span class="btn-close" id="btnCloseStockCloseModal"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='closefrm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='adminRegisterStockClose' />

                <table class='register'>
                    <colgroup>
                        <col width='200'>
                        <col />
                    </colgroup>
                    <tr>
                        <th><i class='bx bx-check'></i> 연도</th>
                        <td>
                            <select class='select' name='year' id='year'>
                                <option value=''>== 연도 ==</option>
                                <?php for($i=2020; $i<=date('Y'); $i++) { ?>
                                    <option value='<?php echo $i; ?>'><?php echo $i; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 월</th>
                        <td>
                            <select class='select' name='month' id='month'>
                                <option value=''>== 월 ==</option>
                                <?php for($i=1; $i<=12; $i++) { ?>
                                    <option value='<?php echo $i; ?>'><?php echo $i; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 마감 금액</th>
                        <td>
                            <input type='text' class='input' name='close_amount' id='close_amount' />                            
                        </td>
                    </tr>
                </table>
            </form>

            <div class='btn-group'>
                <input type='button' class='modal-btn danger' id='btnCloseRegister' value='저장' />&nbsp;
                <input type='button' class='modal-btn' id='btnCloseStockClose' value='취소' />
            </div>
		</div>
	</div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    // 창닫기
    try {
        const btnCloseStockClose = document.getElementById('btnCloseStockClose');
        if(btnCloseStockClose) {
            btnCloseStockClose.addEventListener('click', function() {
                clean();
                closeModal('modalRegisterStockClose');
            });
        }
    } catch(e) {}

    try {
        const btnCloseStockCloseModal = document.getElementById('btnCloseStockCloseModal');
        if(btnCloseStockCloseModal) {
            btnCloseStockCloseModal.addEventListener('click', function() {
                clean();
                closeModal('modalRegisterStockClose');
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnCloseRegister = document.getElementById('btnCloseRegister');
        if (btnCloseRegister) {            
            btnCloseRegister.addEventListener('click', () => {

                // 배열을 전달하여 함수 호출
                const fieldsArray = [
                    { id: 'year', message: '연도를 선택하세요', type: 'select' },
                    { id: 'month', message: '월을 선택하세요', type: 'select' },
                    { id: 'close_amount', message: '마감 금액을 입력하세요', type: 'text' }
                ];

                // 유효성 검사를 위한 함수 호출
                const isValid = validateFields(fieldsArray);
                if(isValid) closeStockRegister();
                else console.log('유효성 검사를 통과하지 못했습니다');
            });            
        } else {
            console.log('btnRegister button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }

    // close_amount 입력 시 자동 콤마 추가
    try {
        const closeAmountInput = document.getElementById('close_amount');
        if (closeAmountInput) {
            closeAmountInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if(value === "") {
                    e.target.value = "";
                    return;
                }
                // 숫자로 변환 후 콤마 붙임
                e.target.value = Number(value).toLocaleString();
            });
        }
    } catch(e) {}
});    

// 재고 마감 등록
const closeStockRegister = () => {    
    const closefrm = document.getElementById('closefrm');

    if(frm) {
        if(check('closefrm')) {
            const formData = new FormData(closefrm);

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
                if (data && data.message) { // data가 존재하고, message가 있을 때만 처리
                    alert(data.message);
                    clean();
                    if (data.result === 'success') {
                        closeModal('modalRegisterStockClose');
                    }
                }
            })
            .catch(error => console.log(error));
        }
    }
}
</script>