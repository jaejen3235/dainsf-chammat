<div class="modal" id="modalViewWorkOrder">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>작업지시 내용</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <table class='register'>
                <colgroup>
                    <col width='150' />
                    <col width='234' />
                    <col width='150' />
                    <col width='234' />
                </colgroup>
                <tr>
                    <th>작업 지시 일자</th>
                    <td><span id="order_date"></span></td>
                    <th>작업 품목</th>
                    <td><span id="item_name"></span></td>
                </tr>
                <tr>
                    <th>품번</th>
                    <td><span id="item_code"></span></td>
                    <th>규격</th>
                    <td><span id="standard"></span></td>
                </tr>
                <tr>
                    <th>단위</th>
                    <td><span id="unit"></span></td>
                    <th>작업 지시 수량</th>
                    <td><span id="order_qty"></span></td>
                </tr>
            </table>    
            
            <div class='btn-group'>                
                <input type='button' class='modal-btn danger' id='btnCloseModal' value='닫기' />
            </div>
		</div>
	</div>
</div>

<script>
window.addEventListener('DOMContentLoaded', async ()=>{	
    // 창닫기
    try {
        const btnClose = document.getElementById('btnClose');
        if(btnClose) {
            btnClose.addEventListener('click', function() {                
                closeModal('modalViewWorkOrder');
            });
        }
        
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {                
                closeModal('modalViewWorkOrder');
            });
        }
    } catch(e) {}
});


const getter = async (uid) => {    
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getWorkOrder');
    formData.append('uid', uid);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        setter(data);        
    } catch (error) {
        console.error('납품 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const setter = (data) => {
    document.getElementById('order_date').innerHTML = data.order_date;
    document.getElementById('item_name').innerHTML = data.item_name;
    document.getElementById('item_code').innerHTML = data.item_code;
    document.getElementById('standard').innerHTML = data.standard;
    document.getElementById('unit').innerHTML = data.unit;
    document.getElementById('order_qty').innerHTML = data.order_qty;
};
</script>