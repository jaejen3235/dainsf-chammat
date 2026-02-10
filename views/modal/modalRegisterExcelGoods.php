<div class="modal" id="modalRegisterExcelGoods">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>대량 상품 등록 (EXCEL)</span>
			<span class="btn-close" id="btnExcelClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='excelFrm'>
                <input type='hidden' name='controller' id='controller' value='agency' />
                <input type='hidden' name='mode' id='mode' value='registerGoodsExcel' />

                <table class='register-table'>
                    <colgroup>
                        <col width='200'>
                        <col />
                    </colgroup>
                    <tr>
                        <th>엑셀 파일</th>
                        <td>
                            <input type='file' class='input w300' name='excel' id='excel' />
                        </td>
                    </tr>                    
                </table>
            </form>
            <hr />
            <div class='help'>
                <i class='bx bx-check'></i> 은 필수입력 사항입니다
            </div>

            <div class='btn-group'>
                <input type='button' class='modal-btn danger' id='btnRegisterExcel' value='저장' />&nbsp;
                <input type='button' class='modal-btn' id='btnCloseExcelModal' value='취소' />
            </div>
		</div>
	</div>
</div>

<script>
window.addEventListener('DOMContentLoaded', async ()=>{	    
    // 창닫기
    try {
        const btnExcelClose = document.getElementById('btnExcelClose');
        if(btnExcelClose) {
            btnExcelClose.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterExcelGoods');
            });
        }
    } catch(e) {}

    try {
        const btnCloseExcelModal = document.getElementById('btnCloseExcelModal');
        if(btnCloseExcelModal) {
            btnCloseExcelModal.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterExcelGoods');
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnRegisterExcel = document.getElementById('btnRegisterExcel');
        if (btnRegisterExcel) {
            btnRegisterExcel.addEventListener('click', registerExcel);
        } else {
            console.log('btnRegisterExcel button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }
});    

// 등록
const registerExcel = () => {    
    const frm = document.getElementById('excelFrm');

    if(frm) {
        if(check('excelFrm')) {
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
                        getGoodsList({page:1});
                        clearn();
                        closeModal('modalRegisterExcelGoods');
                    } else {
                        clearn();
                        alert(data.message);
                    }

                }
            })
            .catch(error => console.log(error));
        }
    }
}
</script>