<div class='main-container'>
    <div class='title-wrapper'>환경 설정</div>
        <div class='content-wrapper'>
            <div>
                <form id='frm'>
                    <input type='hidden' name='controller' id='controller' value='distributor' />
                    <input type='hidden' name='mode' id='mode' value='registerSetting' />
                    <input type='hidden' name='uid' id='uid' />

                    <table class='register'>
                        <colgroup>
                            <col width='200'>
                            <col />
                        </colgroup>
                        <tr>
                            <td>상단 로고 텍스트</td>
                            <td>
                                <input type='text' class='p' data-width='100' name='logo' id='logo' />
                            </td>
                        </tr>
                        <tr>
                            <td>비밀번호 변경</td>
                            <td>
                                <input type='text' class='p' data-width='100' name='pwd' id='pwd' />
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