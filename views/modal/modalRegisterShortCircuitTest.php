<div class="modal" id="modalRegisterShortCircuitTest">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>누전검사 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerShortCircuitTest' />
                <input type='hidden' class='input' name='uid' id='uid' />                
                <input type='hidden' class='input' name='work_qty' id='work_qty' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col />
                    </colgroup>
                    <tr>
                        <th><i class='bx bx-check'></i> 검사 날짜</th>
                        <td>
                            <input type='text' class='input datepicker' name='test_date' id='test_date' />
                        </td>
                        <th><i class='bx bx-check'></i> 검사 품목</th>
                        <td>
                            <span id='item_name'></span>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 품번</th>
                        <td>
                            <span id='item_code'></span>
                        </td>
                        <th><i class='bx bx-check'></i> 규격</th>
                        <td>
                            <span id='standard'></span>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 검사 수량</th>
                        <td colspan='3'>
                            <span id='test_qty'></span>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 적합수량</th>
                        <td>
                            <input type='text' class='input' name='suitable_qty' id='suitable_qty' onkeyup='calculateTestQty()'/>                          
                        </td>
                        <th><i class='bx bx-check'></i> 불량수량</th>
                        <td>
                            <input type='text' class='input' name='unsuitable_qty' id='unsuitable_qty' readonly>                            
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
    try {
        const uid = document.getElementById('uid');
    } catch(e) {}

    // 창닫기
    try {
        const btnClose = document.getElementById('btnClose');
        if(btnClose) {
            btnClose.addEventListener('click', function() {
                clean();
                closeModal('modalRegisterShortCircuitTest');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clean();
                closeModal('modalRegisterShortCircuitTest');
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
                    { id: 'test_date', message: '검사날짜를 선택하세요', type: 'text' },                    
                    { id: 'uid', message: '검사품목이 누락되었습니다', type: 'text' },
                    { id: 'suitable_qty', message: '적합수량을 입력하세요', type: 'text' },
                    { id: 'unsuitable_qty', message: '불량수량을 입력하세요', type: 'text' },
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

    getDefectList();
});    

// 누전검사 등록
const register = () => {
    // .defect_qty 를 가진 input 의 합계가 unsuitable_qty 의 합계랑 같은지 검사..같지 않으면 경고창과 함께 return
    const defectQtyInputs = document.querySelectorAll('.defect_qty');
    let defectSum = 0;
    defectQtyInputs.forEach(input => {
        const val = parseInt(input.value, 10);
        defectSum += isNaN(val) ? 0 : val;
    });
    const unsuitableQty = parseInt(document.getElementById('unsuitable_qty').value, 10) || 0;
    if (defectSum !== unsuitableQty) {
        alert('불량수량의 합계가 각 불량 항목 입력값의 합과 일치하지 않습니다.');
        return;
    }


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
            alert(data.message);
            if(data.result == 'success') {                
                getWorkReportList({page:1});
                clean();    
                closeModal('modalRegisterShortCircuitTest');
            }            
        }
    })
    .catch(error => console.log(error));
}

const calculateTestQty = () => {
    const work_qty = parseInt(document.getElementById('work_qty').value, 10);
    const suitable_qty = parseInt(document.getElementById('suitable_qty').value, 10);
    if(suitable_qty > work_qty) {
        alert('적합수량이 작업수량을 초과합니다');
        document.getElementById('suitable_qty').value = '';
        return;
    }
    const unsuitable_qty = document.getElementById('unsuitable_qty').value;
    const qty = work_qty - suitable_qty;
    document.getElementById('unsuitable_qty').value = qty;

    // .defect_qty 에 모두 0으로 입력
    document.querySelectorAll('.defect_qty').forEach(input => {
        input.value = 0;
    });
}

// 출하지시 가져오기
const getter = async (uid) => {  
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getWorkReport');
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

const setter = async (data) => {
    if (data) {        
        document.getElementById('item_name').innerHTML = data.item_name;
        document.getElementById('item_code').innerHTML = data.item_code;
        document.getElementById('standard').innerHTML = data.standard;
        document.getElementById('test_qty').innerHTML = data.work_qty;
        document.getElementById('work_qty').value = data.work_qty;
        document.getElementById('test_date').value = data.work_date;
    }
}

const getDefectList = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDefectList');
    
    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();

        const tableBody = document.querySelector('.register');
        tableBody.insertAdjacentHTML('beforeend', generateDefectListContent(data));
    } catch (error) {
        displayError(error);
    }
}

const generateDefectListContent = (data) => {
    return data.data.map(item => `
        <tr>
            <th class='center'><input type='hidden' name='defect_name[]' value='${item.defect_name}' />${item.defect_name}</th>
            <td colspan='3'><input type='text' class='input defect_qty' name='defect_qty[]' value='0' /></td>
        </tr>
    `).join('');
};
</script>