<?php
include "head.php";
?>
    
<main>
    <div class="title-container">
        <div class="title-wrapper">
            <div class="title">상담신청</div>            
            <div class="summary">여러분들을 환영합니다.</div>
        </div>
    </div>
    <div class="form-container">
        <div class="form-wrapper">
            <div class="input-section">
                <div class="input-box">
                    <div class="input-title">이름</div>
                    <div class="input-content">
                        <input type="text" id='name' placeholder="신청자명을 입력해주세요" />
                    </div>
                </div>
                <div class="input-box">
                    <div class="input-title">휴대폰번호</div>
                    <div class="input-content">
                        <input type="text" id='mobile' placeholder="휴대폰번호를  입력해주세요" readonly />
                    </div>
                </div>
                <div class="input-box">
                    <div class="input-title">이메일</div>
                    <div class="input-content">
                        <input type="text" id='email' placeholder="이메일을 입력해주세요" />
                    </div>
                </div>
                <div class="textarea-box">
                    <div class="input-title">상담메모</div>
                    <div class="input-content">
                        <textarea id='memo' placeholder="상담 메모 및 문의사항을 입력해주세요"></textarea>
                    </div>
                </div>
            </div>
            <div class="info-section">
                <div class='device-info'>
                    <div class="device-name"></div>
                    <div class="device-model"></div>
                </div>
                <div class="payment"></div>
                <hr />
                <div class="payment-box">
                    <div>요금할인유형</div>
                    <div id='paymentType'></div>
                </div>
                <div class="payment-box">
                    <div>가입유형</div>
                    <div id='joinType'></div>
                </div>
                <hr />
                <div class="payment-box">
                    <div>월 단말기 할부금 (24개월)</div>
                    <div id='deviceMip'></div>
                </div>
                <div class="payment-box">
                    <div>월 할부이자</div>
                    <div id='deviceRateMip'></div>
                </div>
                
                <div class="payment-box">
                    <div>월 통신요금</div>
                    <div id='calCommunicationPrice'></div>
                </div>
                <div class="payment-box">
                    <div>선택약정 요금할인</div>
                    <div id='calChoiceContractSales'></div>
                </div>
                <hr />
                <div class="total-payment-box">
                    <div>요금 합계</div>
                    <div id='totalPrice'></div>
                </div>
                <div class="info-box">온라인 문의를 신청하시면, 담당자가 전화로 고객님께 상품 설명을 드릴 수 있습니다. 기존 위약금 및 할부금 문의는 상담 시 고객님께서 별도 요청하셔야 조회 및 안내가 가능하며, 미요청시 접수 주신 내용으로 개통이 진행됩니다.</div>
                <div class="check-box">
                    <input type="checkbox" id="chk" />
                    <label for="chk">전화 상담 동의 (필수)</label>
                </div>
            </div>
        </div>
        <div class="button-wrapper">
            <input type="button" class="btn" id='btnRegister' value="상담 신청" />
        </div>
    </div>
</main>

<?php
include "foot.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    // 로그인 체크할 것
    setLocalStorageData();

    const btnRegister = document.getElementById('btnRegister');
    if(btnRegister) {
        btnRegister.addEventListener('click', () => {
            // 배열을 전달하여 함수 호출
            const fieldsArray = [
                { id: 'name', message: '이름을 입력하세요', type: 'text'},
                { id: 'mobile', message: '휴대폰번호를 입력하세요', type: 'text' },
                { id: 'memo', message: '상담내용을 입력하세요', type: 'text' },
                { id: 'chk', message: '전화 상담 동의를 하셔야 합니다', type: 'checkbox' }
            ];

            // 유효성 검사를 위한 함수 호출
            const isValid = validateFields(fieldsArray);
            if(isValid) register();
            else console.log('유효성 검사를 통과하지 못했습니다');
        });
    }
});

const setLocalStorageData = () => {
    /*
    console.log("deviceName : ", localStorage.getItem('deviceName')); // 기기명
    console.log("deviceModel : ", localStorage.getItem('deviceModel')); // 모델명
    console.log("device : ", localStorage.getItem('device')); // 기기 UID
    console.log("tab1 : ", localStorage.getItem('tab1')); // 요금할인 유형
    console.log("tab2 : ", localStorage.getItem('tab2')); // 가입유형
    console.log("payment : ", localStorage.getItem('payment')); // 요금제
    console.log("deviceMip : ", localStorage.getItem('deviceMip')); // 단말기 할부금
    console.log("deviceRateMip : ", localStorage.getItem('deviceRateMip')); // 월 할부 이자
    console.log("calCommunicationPrice : ", localStorage.getItem('calCommunicationPrice')); // 월 통신요금
    console.log("calChoiceContractSales : ", localStorage.getItem('calChoiceContractSales')); // 선택약정 요금할인 
    console.log("totalPrice : ", localStorage.getItem('totalPrice')); // 합계금액
    */

    document.getElementById('mobile').value = localStorage.getItem('mobile');

    document.querySelector('.device-name').innerText = localStorage.getItem('deviceName');
    document.querySelector('.device-model').innerText = `(${localStorage.getItem('deviceModel')})`;
    document.querySelector('#paymentType').innerText = localStorage.getItem('paymentType');
    document.querySelector('#joinType').innerText = localStorage.getItem('joinType');
    document.querySelector('.payment').innerText = localStorage.getItem('payment');
    document.querySelector('#deviceMip').innerText = `${comma(localStorage.getItem('deviceMip'))}원`;
    document.querySelector('#deviceRateMip').innerText = `${comma(localStorage.getItem('deviceRateMip'))}원`;
    document.querySelector('#calCommunicationPrice').innerText = `${comma(localStorage.getItem('calCommunicationPrice'))}원`;
    document.querySelector('#calChoiceContractSales').innerText = `-${comma(localStorage.getItem('calChoiceContractSales'))}원`;
    document.querySelector('#totalPrice').innerText = `${comma(localStorage.getItem('totalPrice'))}원`;
}

const register = () => {
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'registerCounseling');
    formData.append('name', document.getElementById('name').value);
    formData.append('mobile', document.getElementById('mobile').value);
    formData.append('email', document.getElementById('email').value);
    formData.append('memo', document.getElementById('memo').value);
    formData.append('deviceName', localStorage.getItem('deviceName'));
    formData.append('deviceModel', localStorage.getItem('deviceModel'));
    formData.append('paymentType', localStorage.getItem('paymentType'));
    formData.append('joinType', localStorage.getItem('joinType'));
    formData.append('payment', localStorage.getItem('payment'));
    formData.append('deviceMip', localStorage.getItem('deviceMip'));
    formData.append('deviceRateMip', localStorage.getItem('deviceRateMip'));
    formData.append('communicationPrice', localStorage.getItem('calCommunicationPrice'));
    formData.append('choiceContractSales', localStorage.getItem('calChoiceContractSales'));
    formData.append('totalPrice', localStorage.getItem('totalPrice'));
    formData.append('client', localStorage.getItem('client'));
    formData.append('agency', localStorage.getItem('agency'));

	fetch('./webadm/handler.php', {
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
                alert('상담을 신청하였습니다');
                history.go(-1);
            }
		}
	})
	.catch(error => console.log(error));
}
</script>