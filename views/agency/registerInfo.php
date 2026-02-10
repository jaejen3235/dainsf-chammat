<div class='main-container'>
    <div class='title-wrapper'>사이트정보 관리</div>
        <div class='content-wrapper'>
            <div>
                <form id='frm'>
                    <input type='hidden' name='controller' id='controller' value='agency' />
                    <input type='hidden' name='mode' id='mode' value='registerInfo' />
                    <input type='hidden' name='uid' id='uid' />
                    <input type='hidden' name='oldFile' id='oldFile' />

                    <table class='register'>
                        <colgroup>
                            <col width='200'>
                            <col />
                            <col width='200'>
                            <col />
                        </colgroup>
                        <tr>
                            <td>상호</td>
                            <td>
                                <input type='text' class='p' data-width='100' name='name' id='name' />
                            </td>
                            <td>대표자명</td>
                            <td>
                                <input type='text' class='p' data-width='100' name='owner' id='owner' />
                            </td>
                        </tr>
                        <tr>
                            <td>주소</td>
                            <td>
                                <input type='text' class='p' data-width='100' name='address' id='address' />
                            </td>
                            <td>전화번호</td>
                            <td>
                                <input type='text' class='p' data-width='100' name='telephone' id='telephone' />
                            </td>
                        </tr>
                        <tr>
                            <td>팩스번호</td>
                            <td>
                                <input type='text' class='p' data-width='100' name='fax' id='fax' />
                            </td>
                            <td>이메일</td>
                            <td>
                                <input type='text' class='p' data-width='100' name='email' id='email' />
                            </td>
                        </tr>
                        <tr>
                            <td>브라우저 타이틀</td>
                            <td>
                                <input type='text' class='p' data-width='100' name='title' id='title' />
                            </td>
                            <td>브라우저 로고</td>
                            <td>
                                <input type='file' class='p' data-width='30' name='favicon' />
                                <span id='favicon'></span>
                            </td>
                        </tr>
                        <tr>
                            <td>문자알림 받을 전화번호</td>
                            <td colspan='3'>
                                <input type='text' class='p' data-width='100' name='receiver' id='receiver' />
                                <br>
                                <span class='help red'>※ 문자발송은 보유하신 문자포인트내에서 발송이 가능합니다.</span>
                            </td>
                        </tr>
                        <tr>
                            <td>개인정보처리방침</td>
                            <td colspan='3'>
                                <textarea rows='10' name='policy1' id='policy1'></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>이용약관</td>
                            <td colspan='3'>
                                <textarea rows='10' name='policy2' id='policy2'></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>이메일무단수집거부</td>
                            <td colspan='3'>
                                <textarea rows='10' name='policy3' id='policy3'></textarea>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>            
            <div class='bottom-btn-group'>
                <input type='button' class='btn-large orange' id='btnRegister' value='정보 등록' />
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', () => {	  
    const uid = document.getElementById('uid');

    getter();

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
});  

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
                    } else {
                        console.log(data.message);
                    }

                }
            })
            .catch(error => console.log(error));
        }
    }
}

const getter = async () => {
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getInfo');

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
        document.getElementById('title').value = data.title;
        document.getElementById('oldFile').value = data.favicon;
        document.getElementById('name').value = data.name;
        document.getElementById('owner').value = data.owner;
        document.getElementById('address').value = data.address;
        document.getElementById('telephone').value = data.telephone;
        document.getElementById('fax').value = data.fax;
        document.getElementById('email').value = data.email;
        document.getElementById('receiver').value = data.receiver;
        document.getElementById('policy1').value = data.policy1;
        document.getElementById('policy2').value = data.policy2;
        document.getElementById('policy3').value = data.policy3;
        document.getElementById('favicon').innerText = data.favicon;
    }
}

const displayError = (error) => {
    //console.error('Error:', error);
}
</script>