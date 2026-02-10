<div class="modal" id="modalRegisterAccount">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>거래처 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='mes' />
                <input type='hidden' name='mode' id='mode' value='registerAccount' />
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
                        <th><i class='bx bx-check'></i> 거래처명</th>
                        <td>
                            <input type='text' class='input' name='name' id='name' />
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 사업자등록번호</th>
                        <td>
                            <input type='text' class='input' name='tax_number' id='tax_number' />
                        </td>
                        <th>법인등록번호</th>
                        <td>
                            <input type='text' class='input' name='biz_number' id='biz_number' />
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check'></i> 대표자명</th>
                        <td>
                            <input type='text' class='input' name='owner' id='owner' />                            
                        </td>
                        <th><i class='bx bx-check'></i> 대표자 연락처</th>
                        <td>
                            <input type='text' class='input' name='mobile' id='mobile' />
                        </td>
                    </tr>
                    <tr>
                        <th>일반전화</th>
                        <td>
                            <input type='text' class='input' name='telephone' id='telephone' />
                        </td>
                        <th>FAX</th>
                        <td>
                            <input type='text' class='input' name='fax' id='fax' />
                        </td>
                    </tr>
                    <tr>
                        <th>이메일</th>
                        <td colspan='3'>
                            <input type='text' class='input' name='email' id='email' />
                        </td>
                    </tr>
                    <tr>
                        <th>주소</th>
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
                closeModal('modalRegisterAccount');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterAccount');
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
                    { id: 'name', message: '거래처명을 입력하세요', type: 'text' },
                    { id: 'tax_number', message: '사업자등록번호를 입력하세요', type: 'text' },
                    { id: 'owner', message: '대표자명을 입력하세요', type: 'text' },
                    { id: 'mobile', message: '대표자 연락처를 입력하세요', type: 'text' }
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
});    

// 거래처 등록
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
                        getAccountList({page:1});
                        clearn();
                        closeModal('modalRegisterAccount');
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
    formData.append('mode', 'getAccountClassificationList');

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

// 거래처 가져오기
const getter = async (uid) => {  
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getAccount');
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
        document.getElementById('classification').value = data.classification;
        document.getElementById('name').value = data.name;
        document.getElementById('tax_number').value = data.tax_number;
        document.getElementById('biz_number').value = data.biz_number;
        document.getElementById('owner').value = data.owner;
        document.getElementById('mobile').value = data.mobile;
        document.getElementById('telephone').value = data.telephone;
        document.getElementById('fax').value = data.fax;
        document.getElementById('email').value = data.email;
        document.getElementById('address').value = data.address;
    }
}

const clearn = () => {
    const inputs = document.querySelectorAll('.input'); // input 클래스를 가진 모든 요소 선택
    inputs.forEach(input => input.value = ''); // 각 요소의 값을 빈 문자열로 설정
};
</script>