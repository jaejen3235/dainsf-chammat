<div class="modal" id="modalRegisterItem">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>품목 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerItem' />
                <input type='hidden' class='input' name='uid' id='uid' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col />
                        <col width='150'>
                        <col />
                    </colgroup>
                    <tr>
                        <th><i class='bx bx-check'></i> 구분</th>
                        <td>
                            <select class='classification' name='classification' id='classification'>
                            </select>
                        </td>
                        <th><i class='bx bx-check'></i> 품번</th>
                        <td>
                            <input type='text' class='input' name='item_code' id='item_code' />
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 품명</th>
                        <td>
                            <input type='text' class='input' name='item_name' id='item_name' />
                        </td>
                        <th><i class='bx bx-check'></i> 규격</th>
                        <td>
                            <input type='text' class='input' name='standard' id='standard' />
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 단위</th>
                        <td>
                            <select class='unit' name='unit' id='unit'>
                            </select>
                        </td>
                        <th><i class='bx bx-check'></i> 재고 수량</th>
                        <td>
                            <input type='text' class='input' name='stock_qty' id='stock_qty' />
                        </td>
                    </tr>
                    <tr>
                        <th>안전재고 수량</th>
                        <td>
                            <input type='text' class='input' name='safety_stock_qty' id='safety_stock_qty' />
                        </td>
                        <th>가격</th>
                        <td>
                            <input type='text' class='input' name='price' id='price' />
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
                clearn();
                closeModal('modalRegisterItem');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterItem');
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
                    { id: 'classification', message: '구분을 선택하세요', type: 'select' },
                    { id: 'item_code', message: '품번을 입력하세요', type: 'text' },
                    { id: 'item_name', message: '품명을 입력하세요', type: 'text' },
                    { id: 'unit', message: '단위를 선택하세요', type: 'select' },
                    { id: 'standard', message: '규격을 입력하세요', type: 'text' },
                    { id: 'price', message: '가격을 입력하세요', type: 'text' },
                    { id: 'stock_qty', message: '재고수량을 입력하세요', type: 'text' }
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

    getClassificationList();
    getUnitList();
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
                        getItemList({page:1});
                        clearn();
                        closeModal('modalRegisterItem');
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

// 제품구분 가져오기
const getClassificationList = async () => {  
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getClassificationList');

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setClassification(data);
    } catch (error) {
        displayError(error);
    }
}

const setClassification = (data) => {    
    if (data && data.length > 0) {
        const classification = document.querySelectorAll('.classification'); // 모든 select 요소 선택
        classification.forEach(select => {
            select.innerHTML = ''; // 기존 옵션 제거

            // 기본 선택 옵션 추가
            const defaultOption = document.createElement('option');
            defaultOption.value = '0';
            defaultOption.textContent = '== 선택 ==';
            select.appendChild(defaultOption);

            // 받아온 데이터를 기반으로 option 태그 추가
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.name; // option의 value는 제품 구분의 id로 설정
                option.textContent = item.name; // option의 표시되는 텍스트는 제품 구분의 name으로 설정
                select.appendChild(option);
            });
        });
    }
}


// 제품단위 가져오기
const getUnitList = async () => {  
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getUnitList');

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setUnit(data);
    } catch (error) {
        displayError(error);
    }
}

const setUnit = (data) => {    
    if (data && data.length > 0) {
        const unit = document.querySelectorAll('.unit'); // 모든 select 요소 선택
        unit.forEach(select => {
            select.innerHTML = ''; // 기존 옵션 제거

            // 기본 선택 옵션 추가
            const defaultOption = document.createElement('option');
            defaultOption.value = '0';
            defaultOption.textContent = '== 선택 ==';
            select.appendChild(defaultOption);

            // 받아온 데이터를 기반으로 option 태그 추가
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.name; // option의 value는 제품 구분의 id로 설정
                option.textContent = item.name; // option의 표시되는 텍스트는 제품 구분의 name으로 설정
                select.appendChild(option);
            });
        });
    }
}


// 품목 가져오기
const getter = async (uid) => {  
    document.getElementById('uid').value = uid;

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
        setter(data);
    } catch (error) {
        displayError(error);
    }
}

const setter = (data) => {
    if (data) {        
        document.getElementById('item_name').value = data.item_name;
        document.getElementById('item_code').value = data.item_code;
        document.getElementById('standard').value = data.standard;
        document.getElementById('classification').value = data.classification;
        document.getElementById('unit').value = data.unit;
        document.getElementById('stock_qty').value = data.stock_qty;
        document.getElementById('safety_stock_qty').value = data.safety_stock_qty;
        document.getElementById('price').value = data.price;
    }
}

const clearn = () => {
    const inputs = document.querySelectorAll('.input'); // input 클래스를 가진 모든 요소 선택
    inputs.forEach(input => input.value = ''); // 각 요소의 값을 빈 문자열로 설정
};
</script>