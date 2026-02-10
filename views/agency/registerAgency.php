<div id='lnbCloseBtn' class="btn-lnb-close">
    <i class='bx bx-left-arrow-alt'></i>
</div>

<div class="title-container">
    <div class="title-box">협력사 등록</div>
    <div class="btn-box"></div>                
</div>

<div class="card-container">
    <div class="card">
        <div>
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='agency' />
                <input type='hidden' name='mode' id='mode' value='registerAgency' />
                <input type='hidden' name='uid' id='uid' value="<?=$_GET['uid']?>" />

                <table class='register-table'>
                    <colgroup>
                        <col width='200'>
                        <col />
                    </colgroup>
                    <tr>
                        <th>협력사명</th>
                        <td>
                            <input type='text' class='input w300' name='name' id='name' />
                        </td>
                    </tr>
                    <tr>
                        <th>협력사 코드</th>
                        <td>
                            <input type='text' class='input w300' name='code' id='code' />
                        </td>
                    </tr>
                    <tr>
                        <th>담당자</th>
                        <td>
                            <input type='text' class='input' name='adminName' id='adminName' />
                        </td>
                    </tr>
                    <tr>
                        <th>연락처</th>
                        <td>
                            <input type='text' class='input' name='adminMobile' id='adminMobile' maxlength='13' />
                        </td>
                    </tr>
                    <tr>
                        <th>로그인 아이디</th>
                        <td>
                            <input type='text' class='input' name='loginId' id='loginId' />
                        </td>
                    </tr>
                    <tr>
                        <th>로그인 비밀번호</th>
                        <td>
                            <input type='password' class='input' name='loginPwd' id='loginPwd' />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class='center mt30'>
            <input type='button' class='btn-large primary' id='btnRegister' value='협력사 등록' />
            <input type='button' class='btn-large grey-400' id='btnList' value='목록가기' />
        </div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	  
    const uid = document.getElementById('uid');

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
        const btnList = document.getElementById('btnList');
        if (btnList) {
            btnList.addEventListener('click', () => {
                location.href = `?controller=agency&action=listAgency&menu=agency`;
            });
        } else {
            console.log('btnRegister button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }

    try {
        if(uid.value != '') {
            btnRegister.value = '협력사 수정';
            getter(uid.value);
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
                        location.href = `?controller=agency&action=listAgency&menu=agency`;
                    } else {
                        console.log(data.message);
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
    formData.append('mode', 'getAgency');
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
        document.getElementById('name').value = data.name;
        document.getElementById('code').value = data.code;
        document.getElementById('adminName').value = data.adminName;
        document.getElementById('adminMobile').value = data.adminMobile;
        document.getElementById('loginId').value = data.loginId;
    }
}

const displayError = (error) => {
    console.error('Error:', error);
}
</script>