<div class="modal" id="modalRegisterDailyWork">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>작업일지 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerDailyWork' />
                <input type='hidden' class='input' name='uid' id='uid' />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col />
                        <col width='150'>
                        <col />
                    </colgroup>
                    <tr>
                        <td><i class='bx bx-check'></i> 작업 일자</td>
                        <td>
                            <input type='text' class='input datepicker' name='workDate' id='workDate' />
                        </td>
                        <td><i class='bx bx-check'></i> 품목 구분</td>
                        <td>
                            <select class="classification" name="classification" id="classification">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 작업 품목</td>
                        <td>
                            <select name='item' id='item'>
                                <option value='0'>== 선택 ==</option>
                            </select>
                        </td>
                        <td><i class='bx bx-check'></i> 작업 품번</td>
                        <td>
                            <input type='text' class='input' name='code' id='code' readonly />
                        </td>
                    </tr>
                    <tr>
                        <td>작업 공정</td>
                        <td>
                            <select name='process' id='process'>
                            </select>
                        </td>
                        <td><i class='bx bx-check'></i> 작업자</td>
                        <td>
                            <select name='employee' id='employee'>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 적합 수량</td>
                        <td colspan='3'>
                            <input type='text' class='input' name='qty' id='qty' />                            
                        </td>
                    </tr>                    
                    <tr>
                        <td>불량 수량</td>
                        <td>
                            <input type='text' class='input' name='defectiveQty' id='defectiveQty' />
                        </td>
                        <td>불량 사유</td>
                        <td>
                            <select name='defectiveReason' id='defectiveReason'>
                                <option value='0'>== 선택 ==</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </form>
            <hr />
            <div class='help'>
                ※ 적합수량은 불량이 없는 수량을 기재하시면 됩니다.</br>
                ※ 불량이 있을 경우 불량수량은 '불량수량'에 수량을 기입하시면 됩니다.
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
                closeModal('modalRegisterDailyWork');
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
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterDailyWork');
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
                    { id: 'workDate', message: '작업일자를 입력하세요', type: 'text' },                    
                    { id: 'classification', message: '품목구분을 선택하세요', type: 'select' },
                    { id: 'item', message: '품목을 선택하세요', type: 'select' },
                    { id: 'code', message: '품번을 입력하세요', type: 'text' },
                    { id: 'qty', message: '생산수량을 입력하세요', type: 'text' },
                    { id: 'employee', message: '작업자를 선택하세요', type: 'select' },
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
    getProcessList();
    getEmployeeList();
    getDefectiveList();
});    

// 작업일지 등록
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
                        getDailyWorkList({page:1});
                        clearn();
                        closeModal('modalRegisterDailyWork');
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
            option.textContent = item.name; // option의 표시되는 텍스트는 제품 구분의 name으로 설정
            select.appendChild(option);
        });
    }
}


// 품목 가져오기
const getProcessList = async () => {    
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getProcessList');

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setProcessList(data);
    } catch (error) {
        displayError(error);
    }
}

const setProcessList = (data) => {    
    const select = document.querySelector('#process'); // 단일 select 요소 선택

    if (data && data.length > 0 && select) {
        select.innerHTML = ''; // 기존 옵션 제거

        // 기본 선택 옵션 추가
        const defaultOption = document.createElement('option');
        defaultOption.value = '0';
        defaultOption.textContent = '== 선택 ==';
        select.appendChild(defaultOption);

        // 받아온 데이터를 기반으로 option 태그 추가
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.uid; // option의 value는 제품 구분의 id로 설정
            option.textContent = item.name; // option의 표시되는 텍스트는 제품 구분의 name으로 설정
            select.appendChild(option);
        });
    }
}

// 사원 가져오기
const getEmployeeList = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getEmployeeList');

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setEmployeeList(data);
    } catch (error) {
        displayError(error);
    }
}

const setEmployeeList = (data) => {    
    const select = document.querySelector('#employee'); // 단일 select 요소 선택

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
            option.textContent = item.name; // option의 표시되는 텍스트는 제품 구분의 name으로 설정
            select.appendChild(option);
        });
    }
}

// 품목 가져오기
const getDefectiveList = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDefectiveList');

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setDefectiveList(data);
    } catch (error) {
        displayError(error);
    }
}

const setDefectiveList = (data) => {    
    const select = document.querySelector('#defectiveReason'); // 단일 select 요소 선택

    if (data && data.length > 0 && select) {
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
    }
}

// 작업지시서 가져오기
const getItem = async () => {  
    console.log('품목 가져오기');
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
        document.getElementById('code').value = data.code;
    }
}


// 작업일지 가져오기
const getter = async (uid) => {      
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDailyWork');
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
        document.getElementById('workDate').value = data.workDate;
        document.getElementById('classification').value = data.classification;
        await getItemList();
        document.getElementById('item').value = data.item;
        document.getElementById('code').value = data.code;
        document.getElementById('process').value = data.process;
        document.getElementById('employee').value = data.employee;
        document.getElementById('qty').value = data.qty;
        document.getElementById('defectiveQty').value = data.defectiveQty;
        document.getElementById('defectiveReason').value = data.defectiveReason;
    }
}


const clearn = () => {
    const inputs = document.querySelectorAll('.input');
    const selects = document.querySelectorAll('select');
    
    inputs.forEach(input => input.value = '');
    selects.forEach(select => select.selectedIndex = 0);
};
</script>