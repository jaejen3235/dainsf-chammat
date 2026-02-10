<div class="modal" id="modalRegisterGoods">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>상품 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='agency' />
                <input type='hidden' name='mode' id='mode' value='registerGoods' />
                <input type='hidden' class='input' name='uid' id='uid' value="<?php echo $_GET['uid']; ?>" />

                <table class='register'>
                    <colgroup>
                        <col width='150'>
                        <col />
                        <col width='150'>
                        <col />
                        <col width='150'>
                        <col />
                    </colgroup>
                    <tr>
                        <td><i class='bx bx-check'></i> 기기 카테고리</td>
                        <td colspan='5'>
                            <select class='category' name='modalCategory' id='modalCategory'>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 기기 모델명</td>
                        <td colspan='5'>
                            <input type='text' class='input' name='model' id='model' />
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 요금제</td>
                        <td colspan='5'>
                            <select name='paymentName' id='paymentName'>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 기기변경</td>
                        <td>
                            <input type='text' class='input comma' name='changeDevice' id='changeDevice' />
                        </td>
                        <td><i class='bx bx-check'></i> 통신사이동</td>
                        <td>
                            <input type='text' class='input comma' name='moveTelecom' id='moveTelecom' />
                        </td>
                        <td><i class='bx bx-check'></i> 신규가입</td>
                        <td>
                            <input type='text' class='input comma' name='joinTelecom' id='joinTelecom' />
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 공시지원금</td>
                        <td colspan='5'>
                            <input type='text' class='input comma' name='supportFund' id='supportFund' />
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 공시지원<br />기기변경</td>
                        <td>
                            <input type='text' class='input comma' name='supportChangeDevice' id='supportChangeDevice' />
                        </td>
                        <td><i class='bx bx-check'></i> 공시지원<br />통신사이동</td>
                        <td>
                            <input type='text' class='input comma' name='supportMoveTelecom' id='supportMoveTelecom' />
                        </td>
                        <td><i class='bx bx-check'></i> 공시지원<br />신규가입</td>
                        <td>
                            <input type='text' class='input comma' name='supportJoinTelecom' id='supportJoinTelecom' />
                        </td>
                    </tr>                 
                </table>
            </form>
            <hr />
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
window.addEventListener('DOMContentLoaded', async ()=>{	
    await getPaymentList();

    try {
        const uid = document.getElementById('uid');
    } catch(e) {}

    // 창닫기
    try {
        const btnClose = document.getElementById('btnClose');
        if(btnClose) {
            btnClose.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterGoods');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterGoods');
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
                    { id: 'modalCategory', message: '기기 카테고리를 선택하세요', type: 'select' },
                    { id: 'model', message: '기기 모델명을 입력하세요', type: 'text' },
                    { id: 'paymentName', message: '요금제를 선택하세요', type: 'select' },
                    { id: 'changeDevice', message: '기기변경을 입력하세요', type: 'text' },
                    { id: 'moveTelecom', message: '통신사이동을 입력하세요', type: 'text' },
                    { id: 'joinTelecom', message: '신규가입을 입력하세요', type: 'text' },
                    { id: 'supportFund', message: '공시지원금을 입력하세요', type: 'text' },
                    { id: 'supportChangeDevice', message: '공시지원 기기변경을 입력하세요', type: 'text' },
                    { id: 'supportMoveTelecom', message: '공시지원 통신사이동을 입력하세요', type: 'text' },
                    { id: 'supportJoinTelecom', message: '공시지원 신규가입을 입력하세요', type: 'text' }
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
});    

// 등록
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
                        getGoodsList({page:1});
                        clearn();
                        closeModal('modalRegisterGoods');
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

const getPaymentList = () => {
    return new Promise((resolve, reject) => {
        let tag = `<option value='0'>== 전체 ==</option>`;

        const formData = new FormData();
        formData.append('controller', 'agency');
        formData.append('mode', 'getAllPaymentList');

        fetch('./handler.php', {
            method: 'post',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(function(data) {
            if(data != null || data != '') {
                data.forEach(item => {
                    tag += `<option value='${item.paymentName}'>${item.paymentName}</option>`;
                });
            }

            document.querySelector('#paymentName').innerHTML = tag;
            resolve();  // 카테고리 리스트 설정이 완료되면 resolve 호출
        })
        .catch(error => {
            console.log(error);
            reject(error);  // 에러 발생 시 reject 호출
        });
    });
}


const getter = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getGoods');
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
        document.getElementById('modalCategory').value = data.category;
        document.getElementById('paymentName').value = data.paymentName;
        document.getElementById('model').value = data.model;
        document.getElementById('changeDevice').value = comma(data.changeDevice);
        document.getElementById('moveTelecom').value = comma(data.moveTelecom);
        document.getElementById('joinTelecom').value = comma(data.joinTelecom);
        document.getElementById('supportFund').value = comma(data.supportFund);
        document.getElementById('supportChangeDevice').value = comma(data.supportChangeDevice);
        document.getElementById('supportMoveTelecom').value = comma(data.supportMoveTelecom);
        document.getElementById('supportJoinTelecom').value = comma(data.supportJoinTelecom);
    }
}


const displayError = (error) => {
    console.error('Error:', error);
}

const clearn = () => {
    const inputs = document.querySelectorAll('.input'); // input 클래스를 가진 모든 요소 선택
    inputs.forEach(input => input.value = ''); // 각 요소의 값을 빈 문자열로 설정
};
</script>