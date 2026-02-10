<?php
include "head.php";
?>
    
<main>
    <div class="login-container">
        <div class="login-wrapper">
            <div class="client-logo"></div>
            <div class="login-section">
                <div>
                    <input type="text" id="agencyCode" placeholder="접속코드" />
                </div>
                <div>
                <input type="text" id='mobile' name='mobile' placeholder="전화번호" maxlength='13' pattern="\d{3}-\d{4}-\d{4}" title="형식: 010-1234-5678" />
                </div>
                <div>
                    <input type="button" class="btn-login" id="btnLogin" value="로그인"/>
                </div>
            </div>
        </div>
    </div>
</main>
<input type='hidden' id='uid' value='<?=$_GET['uid']?>' />

<?php
include "foot.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    getData();

    document.getElementById('mobile').addEventListener('input', function(e) {
        let input = e.target.value;
            
        // 입력된 값이 숫자가 아닌 경우
        if (/\D/.test(input.replace(/-/g, ''))) {
            alert("숫자만 입력 가능합니다."); // 경고 메시지
            e.target.value = input.replace(/\D/g, ''); // 숫자가 아닌 문자는 제거
            return; // 이후 코드 실행 중단
        }

        // 숫자만 있는 경우 000-0000-0000 형식으로 변환
        input = input.replace(/\D/g, ''); // 일단 숫자만 남김
        if (input.length > 3 && input.length <= 7) {
            input = input.slice(0, 3) + '-' + input.slice(3);
        } else if (input.length > 7) {
            input = input.slice(0, 3) + '-' + input.slice(3, 7) + '-' + input.slice(7);
        }

        e.target.value = input; // 변환된 값을 다시 입력 필드에 반영
    });

    try {
        const btnLogin = document.getElementById('btnLogin');

        if(btnLogin) {
            btnLogin.addEventListener('click', function() {
                if (validatePhoneNumber()) {
                    login(); // 휴대폰 번호 유효성 검사 통과 시 로그인 함수 호출
                }
            });
        }
    } catch(e) {}
});


const getData = async () => {
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getClient');
    formData.append('uid', document.getElementById('uid').value);

    try {
        const response = await fetch('./webadm/handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setData(data);
    } catch (error) {
        displayError(error);
    }
}

const setData = (data) => {
    if (data) {
        const tag = `<img src='./attach/client/${data.logo}' />`;
        document.querySelector('.client-logo').innerHTML = tag;
    }
}

// 휴대폰 번호가 000-0000-0000 형식인지 확인하는 함수
const validatePhoneNumber = () => {
    const phoneNumber = document.getElementById('mobile').value;
    const phoneRegex = /^\d{3}-\d{4}-\d{4}$/; // 000-0000-0000 형식의 정규식

    if (!phoneRegex.test(phoneNumber)) {
        alert('휴대폰 번호는 000-0000-0000 형식으로 입력해주세요.');
        return false; // 형식이 맞지 않으면 false 반환
    }
    return true; // 형식이 맞으면 true 반환
}

const login = () => {
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'agencyLogin');
    formData.append('clientCode', document.getElementById('uid').value);
    formData.append('agencyCode', document.getElementById('agencyCode').value);

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
                localStorage.setItem('client', document.getElementById('uid').value);
                localStorage.setItem('clientName', data.clientName);
                localStorage.setItem('agency', document.getElementById('agencyCode').value);
                localStorage.setItem('mobile', document.getElementById('mobile').value);
                
                location.href = `list.php`;
            } else {
                alert(data.message);
            }
		}
	})
	.catch(error => console.log(error));
}
</script>