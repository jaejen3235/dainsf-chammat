<div class="modal" id="modalRegisterPayment">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>요금제 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
        <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='agency' />
                <input type='hidden' name='mode' id='mode' value='registerPayment' />
                <input type='hidden' name='uid' id='uid' value="<?php echo $_GET['uid']; ?>" />

                <table class='register'>
                    <colgroup>
                        <col width='200'>
                        <col />
                    </colgroup>
                    <tr>
                        <td>요금제 구분</td>
                        <td class='p15'>
                            <input type='radio' name='category' id='category1' value='5G' checked /> 5G
                            <input type='radio' name='category' id='category2' value='LTE' /> LTE
                        </td>
                    </tr>
                    <tr>
                        <td>세대 구분</td>
                        <td class='p15'>
                            <input type='radio' name='age' id='age1' value='성인' checked /> 성인
                            <input type='radio' name='age' id='age2' value='청소년' /> 청소년
                            <input type='radio' name='age' id='age3' value='어르신' /> 어르신
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 요금제명</td>
                        <td>
                            <input type='text' class='input w300' name='paymentName' id='paymentName' />
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 데이터명</td>
                        <td>
                            <input type='text' class='input w300' name='dataName' id='dataName' />
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 월 이용료</td>
                        <td>
                            <input type='text' class='input comma' name='payment' id='payment' />
                        </td>
                    </tr>
                    <tr>
                        <td>사용 여부</td>
                        <td>
                            <input type='radio' name='display' id='display1' value='Y' checked /> 사용
                            <input type='radio' name='display' id='display2' value='N' /> 미사용
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
                closeModal('modalRegisterPayment');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterPayment');
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
                    { id: 'paymentName', message: '요금제명을 입력하세요', type: 'text' },
                    { id: 'dataName', message: '데이터명을 입력하세요', type: 'text' },
                    { id: 'payment', message: '월 이용료를 입력하세요', type: 'text' }
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

// 협력사 등록
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
                        getPaymentList({page:1});
                        clearn();
                        closeModal('modalRegisterPayment');
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

const getter = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getPayment');
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
        if(data.category == '5G') document.getElementById('category1').checked = true;
        else document.getElementById('category2').checked = true;

        if(data.age == '성인') document.getElementById('age1').checked = true;
        else if(data.age == '청소년') document.getElementById('age2').checked = true;
        else if(data.age == '어르신') document.getElementById('age3').checked = true;
        else {
            console.log('세대 에러');
        }

        document.getElementById('paymentName').value = data.paymentName;
        document.getElementById('dataName').value = data.dataName;
        document.getElementById('payment').value = comma(data.payment);

        if(data.display == 'Y') document.getElementById('display1').checked = true;
        else document.getElementById('display2').checked = true;
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