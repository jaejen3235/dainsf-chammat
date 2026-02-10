<div class="modal" id="modalAddItem">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>품목 추가</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <table class='register'>
                <colgroup>
                    <col width='150'>
                    <col width='426'>
                    <col width='150'>
                    <col width='426'>
                </colgroup>
                <tr>
                    <th><i class='bx bx-check'></i> 수주 품목</th>
                    <td>
                        <select class='select' name='item' id='item'>
                        </select>
                    </td>
                    <th><i class='bx bx-check'></i> 품번</th>
                    <td>
                        <input type='text' class='input' name='item_code' id='item_code' readonly />
                    </td>
                </tr>
                <tr>
                    <th><i class='bx bx-check'></i> 규격</th>
                    <td>
                        <input type='text' class='input' name='standard' id='standard' readonly />                        
                    </td>
                    <th><i class='bx bx-check'></i> 단위</th>
                    <td>
                        <input type='text' class='input' name='unit' id='unit' readonly />
                    </td>
                </tr>
                <tr>
                    <th><i class='bx bx-check'></i> 수주 수량</th>
                    <td colspan='3'>
                        <input type='text' class='input' name='qty' id='qty' />
                    </td>
                </tr>                
            </table>

            <div class='btn-group'>
                <input type='button' class='modal-btn danger' id='btnAdd' value='추가' />
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
                closeModal('modalAddItem');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                cleanMoal();
                closeModal('modalAddItem');
            });
        }
    } catch(e) {}

    try {
        const item = document.getElementById('item');
        if(item) {
            item.addEventListener('change', function() {
                getItem(item.value);
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnAdd = document.getElementById('btnAdd');
        if (btnAdd) {            
            btnAdd.addEventListener('click', () => {
                // 배열을 전달하여 함수 호출
                const fieldsArray = [
                    { id: 'item', message: '주문 품목을 선택하세요', type: 'select' },
                    { id: 'qty', message: '주문 수량을 입력하세요', type: 'text' },
                ];

                // 유효성 검사를 위한 함수 호출
                const isValid = validateFields(fieldsArray);
                if(isValid) add();
                else console.log('유효성 검사를 통과하지 못했습니다');
            });            
        } else {
            console.log('btnAdd button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }
       
    getSelectList('getAllItemList', 'uid', 'item_name', '#item', `where classification='완제품'`, 'code');        
    
});  

const getItem = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getItem');
    formData.append('uid', uid);
    
    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        setItem(data);
        //console.log(data);
    } catch(e) {}
}

const setItem = (data) => {
    document.getElementById('item_code').value = data.item_code;
    document.getElementById('standard').value = data.standard;
    document.getElementById('unit').value = data.unit;
}

const add = () => {
    // 필요한 DOM 요소들 가져오기    
    const item = document.getElementById('item');    
    const item_code = document.getElementById('item_code');
    const standard = document.getElementById('standard');
    const unit = document.getElementById('unit');
    const qty = document.getElementById('qty');
    
    const list = document.querySelector('.list tbody');
    if(list) {
        // DOM 요소 생성
        const row = document.createElement('tr');        
        
        const td1 = document.createElement('td');
        td1.innerHTML = `
            ${item.options[item.selectedIndex].text}
            <input type='hidden' name='item[]' value='${item.value}' />
        `;

        const td2 = document.createElement('td');
        td2.innerHTML = `
            ${item_code.value}
        `;

        const td3 = document.createElement('td');
        td3.innerHTML = `
            ${standard.value}
        `;

        const td4 = document.createElement('td');
        td4.innerHTML = `
            ${unit.value}
        `;
        
        const td5 = document.createElement('td');
        td5.innerHTML = `
            ${qty.value}
            <input type='hidden' name='qty[]' value='${qty.value}' />
        `;
        
        const td6 = document.createElement('td');
        td6.innerHTML = '<button type="button" class="btn-small danger hands" onclick="removeRow(this)">삭제</button>';
        
        // 셀들을 행에 추가
        row.appendChild(td1);
        row.appendChild(td2);        
        row.appendChild(td3);
        row.appendChild(td4);
        row.appendChild(td5);
        row.appendChild(td6);        
        
        // 행을 테이블에 추가
        list.appendChild(row);
        cleanModal();
        
        // 모달 닫기
        closeModal('modalAddItem');
    }
}

// 행 삭제 함수
window.removeRow = function(button) {
    const row = button.closest('tr');
    if (row) {
        row.remove();
    }
}
</script>