<!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<div class='main-container'>
    <div class='title-wrapper'>상담신청 내용</div>
        <div class='content-wrapper'>
            <input type='hidden' name='uid' id='uid' value="<?=$_GET['uid']?>" />
                
            <table class='register'>
                <colgroup>
                    <col width='200'>
                    <col />
                </colgroup>
                <tr>
                    <td>신청정보</td>
                    <td colspan='3'>
                        <div class='counseling'>
                            <div class='deviceName'></div>
                            <div class='paymentName'></div>
                            <hr/>
                            <div class='payment'>
                                <div>기기 월 할부금</div>
                                <div id='deviceMip'></div>
                            </div>
                            <div class='payment'>
                                <div>기기 월 할부이자</div>
                                <div id='deviceRateMip'></div>
                            </div>
                            <div class='payment'>
                                <div>통신요금</div>
                                <div id='communicationPrice'></div>
                            </div>
                            <div class='payment'>
                                <div>선택약정 할인요금</div>
                                <div id='choiceContractSales'></div>
                            </div>
                            <hr/>
                            <div class='total-payment'>
                                <div>월 납부금액</div>
                                <div id='totalPrice'></div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>이름</td>
                    <td>
                        <input type='text' id='name' />
                    </td>                    
                </tr>
                <tr>
                    <td>휴대폰번호</td>
                    <td>
                        <input type='text' id='mobile' />
                    </td>
                </tr>
                <tr>
                    <td>이메일</td>
                    <td>
                        <input type='text' id='email' />
                    </td>
                </tr>
                <tr>
                    <td>상담 메모</td>
                    <td>
                        <textarea rows="5" id="memo"></textarea>
                    </td>
                </tr>
            </table>
            <hr />
            <table class='register'>
                <colgroup>
                    <col width='200'>
                    <col />
                </colgroup>
                <tr>
                    <td>진행상황</td>
                    <td>
                        <select id='state'>
                            <option value='신규'>신규</option>
                            <option value='진행중'>진행중</option>
                            <option value='완료'>완료</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>관리자 메모</td>
                    <td>
                        <textarea rows='5' id='adminMemo'></textarea>
                    </td>                    
                </tr>                    
            </table>
            <div class='bottom-btn-group'>
                <input type='button' class='btn-large orange' id='btnRegister' value='관지자 메모 저장' />
                <input type='button' class='btn-large' id='btnList' value='목록가기' />
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    const uid = document.getElementById('uid');
    try {
        if(uid.value != '') {
            getter(uid.value);
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }

    // 등록
    try {
        const btnRegister = document.getElementById('btnRegister');
        if (btnRegister) {
            btnRegister.addEventListener('click', register);
        } else {
            console.log('btnRegister button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }

    try {
        const btnList = document.getElementById('btnList')
        if(btnList) {
            btnList.addEventListener('click', () => {
                movePage('agency', 'managementCounseling');
            });
        }
    } catch(e) {}
});

const register = () => {
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'registerCounselingMemo');
    formData.append('uid', document.getElementById('uid').value);
    formData.append('state', document.getElementById('state').value);
    formData.append('adminMemo', document.getElementById('adminMemo').value);

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
                movePage('agency', 'managementCounseling');
            } else {
                alert(data.message);
            }
        }
    })
    .catch(error => console.log(error));
}

const getter = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getCounseling');
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
        document.getElementById('uid').value = data.uid;
        document.querySelector('.deviceName').innerText = data.deviceName + ` (${data.deviceModel})`;
        document.querySelector('.paymentName').innerText = data.payment;        
        document.getElementById('deviceMip').innerText = comma(data.deviceMip) + '원';
        document.getElementById('deviceRateMip').innerText = comma(data.deviceRateMip) + '원';
        document.getElementById('communicationPrice').innerText = comma(data.communicationPrice) + '원';
        document.getElementById('choiceContractSales').innerText = comma(data.choiceContractSales) + '원';
        document.getElementById('totalPrice').innerText = comma(data.totalPrice) + '원';
        document.getElementById('name').value = data.name;
        document.getElementById('mobile').value = data.mobile;
        document.getElementById('email').value = data.email;
        document.getElementById('memo').value = data.memo;
        document.getElementById('state').value = data.state;
        document.getElementById('adminMemo').value = data.adminMemo;
    }
}

const displayError = (error) => {
    console.error('Error:', error);
}
</script>