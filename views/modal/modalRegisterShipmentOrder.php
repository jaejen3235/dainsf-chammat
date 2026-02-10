<div class="modal" id="modalRegisterShipmentOrder">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>출하지시 등록</span>
			<span class="btn-close" id="btnCloseShipmentOrder"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerShipmentOrder' />
                <input type='hidden' class='input' name='uid' id='uid' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col />
                        <col width='150'>
                        <col />
                    </colgroup>
                    <tr>
                        <th><i class='bx bx-check'></i> 거래처 선택</th>
                        <td>
                            <select name='account' id='account'>
                            </select>
                        </td>
                        <th><i class='bx bx-check'></i> 품목 구분</th>
                        <td>
                            <select class='classification' name='classification' id='classification'>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 품목</th>
                        <td>
                            <select name='item' id='item'>
                                <option>== 선택 ==</option>
                            </select>
                        </td>
                        <th><i class='bx bx-check'></i> 품번</th>
                        <td>
                            <input type='text' class='input' name='code' id='code' />
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 출하 일자</th>
                        <td>
                            <input type='text' class='input datepicker' name='shipmentDate' id='shipmentDate' />
                        </td>
                        <th><i class='bx bx-check'></i> 출하 수량</th>
                        <td>
                            <input type='text' class='input' name='qty' id='shipment_qty' />                            
                        </td>
                        
                    </tr>    
                    <tr>
                        <th>배송지</th>
                        <td colspan='3'>
                            <input type='text' class='input' name='address' id='address' />                            
                        </td>
                    </tr>
                </table>
            </form>
            <div class='help'>
                <i class='bx bx-check'></i> 은 필수입력 사항입니다
            </div>

            <div class='btn-group'>
                <input type='button' class='modal-btn danger' id='btnRegisterShipmentOrder' value='저장' />&nbsp;
                <input type='button' class='modal-btn' id='btnCloseModalShipmentOrder' value='취소' />
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
        const btnClose = document.getElementById('btnCloseShipmentOrder');
        if(btnClose) {
            btnClose.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterShipmentOrder');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModalShipmentOrder');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterShipmentOrder');
            });
        }
    } catch(e) {}

    try {
        const classification = document.getElementById('classification');
        classification.addEventListener('change', getItemList) ;
    } catch(e) {}

    try {
        const item = document.getElementById('item');
        item.addEventListener('change', getItem) ;
    } catch(e) {}

    try {
        const account = document.getElementById('account');
        account.addEventListener('change', getAccount) ;
    } catch(e) {}

    // 등록
    try {
        const btnRegister = document.getElementById('btnRegisterShipmentOrder');
        if (btnRegister) {            
            btnRegister.addEventListener('click', () => {
                // 배열을 전달하여 함수 호출
                const fieldsArray = [
                    { id: 'classification', message: '품목구분을 선택하세요', type: 'select' },                    
                    { id: 'item', message: '품목을 선택하세요', type: 'select' },
                    { id: 'code', message: '품번을 입력하세요', type: 'text' },
                    { id: 'shipmentDate', message: '출하일자를 입력하세요', type: 'text' },
                    { id: 'account', message: '거래처를 선택하세요', type: 'select' },
                    { id: 'shipment_qty', message: '출하수량을 입력하세요', type: 'text' },
                ];

                // 유효성 검사를 위한 함수 호출
                const isValid = validateFields(fieldsArray);
                if(isValid) registerShipmentOrder();
                else console.log('유효성 검사를 통과하지 못했습니다');
            });            
        } else {
            console.log('btnRegister button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }

    getClassificationList();
    getAccountList();
});    


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

// 제품구분 가져오기
const getAccountList = async () => {  
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getAccountList');

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setAccountList(data);
    } catch (error) {
        displayError(error);
    }
}

const setAccountList = (data) => {    
    if (data && data.data.length > 0) {
        const account = document.querySelectorAll('#account'); // 모든 select 요소 선택
        account.forEach(select => {
            select.innerHTML = ''; // 기존 옵션 제거

            // 기본 선택 옵션 추가
            const defaultOption = document.createElement('option');
            defaultOption.value = '0';
            defaultOption.textContent = '== 선택 ==';
            select.appendChild(defaultOption);

            // 받아온 데이터를 기반으로 option 태그 추가
            data.data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.uid; // option의 value는 제품 구분의 id로 설정
                option.textContent = item.name; // option의 표시되는 텍스트는 제품 구분의 name으로 설정
                select.appendChild(option);
            });
        });
    }
}


// 품목 가져오기
const getItemList = async () => {
    const where = `where classification='${document.getElementById('classification').value}'`;
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getItemList');
    formData.append('where', where);

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setItemList(data);
    } catch (error) {
        displayError(error);
    }
}

const setItemList = (data) => {    
    const select = document.querySelector('#item'); // 단일 select 요소 선택

    if (data && data.data.length > 0 && select) {
        select.innerHTML = ''; // 기존 옵션 제거

        // 기본 선택 옵션 추가
        const defaultOption = document.createElement('option');
        defaultOption.value = '0';
        defaultOption.textContent = '== 선택 ==';
        select.appendChild(defaultOption);

        // 받아온 데이터를 기반으로 option 태그 추가
        data.data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.uid; // option의 value는 제품 구분의 id로 설정
            option.textContent = item.item_name + ' (' + item.standard + ')'; // option의 표시되는 텍스트는 제품 구분의 name으로 설정
            select.appendChild(option);
        });
    }
}



// 품목 가져오기
const getItem = async () => {  
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getItem');
    formData.append('uid', document.getElementById('item').value);

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setItem(data);
    } catch (error) {
        displayError(error);
    }
}

const setItem = (data) => {
    if (data) {        
        document.getElementById('code').value = data.item_code;
    }
}




// 거래처 하나 가져오기
const getAccount = async () => {  
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getAccount');
    formData.append('uid', document.getElementById('account').value);

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setAccount(data);
    } catch (error) {
        displayError(error);
    }
}

const setAccount = (data) => {
    if (data) {        
        document.getElementById('address').value = data.address;
    }
}

// 사원 등록
const registerShipmentOrder = () => {    
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
                        getShipmentOrderList({page:1});
                        clearn();
                        closeModal('modalRegisterShipmentOrder');
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

// 출하지시 가져오기
const getShipmentOrder = async (uid) => {  
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getShipmentOrder');
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
        setShipmentOrder(data);
    } catch (error) {
        displayError(error);
    }
}

const setShipmentOrder = async (data) => {
    if (data) {        
        document.getElementById('uid').value = data.uid;
        document.getElementById('classification').value = data.classification;

        await getItemList();

        document.getElementById('item').value = data.item;
        document.getElementById('code').value = data.code;
        document.getElementById('shipmentDate').value = data.shipmentDate;
        document.getElementById('account').value = data.account;
        document.getElementById('address').value = data.address;
        document.getElementById('shipment_qty').value = data.qty;
    }
}

// 출하지시 가져오기
const getterOrder = async (uid) => {  
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getShipmentOrder');
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
        setterOrder(data);
    } catch (error) {
        displayError(error);
    }
}

const setterOrder = async (data) => {
    if (data) {        
        document.getElementById('classification').value = data.classification;

        await getItemList();

        document.getElementById('item').value = data.item;
        document.getElementById('code').value = data.code;
        document.getElementById('account').value = data.account;
        document.getElementById('address').value = data.address;
    }
}

const clearn = () => {
    const inputs = document.querySelectorAll('.input'); // input 클래스를 가진 모든 요소 선택
    inputs.forEach(input => input.value = ''); // 각 요소의 값을 빈 문자열로 설정
};
</script>
