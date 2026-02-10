<div class='main-container'>
    <div class='title-wrapper'>관리자 계정 설정</div>
        <div class='content-wrapper'>
            <div>
                <table class='view' id='listTable'>
                    <tbody>
                        <tr>
                            <th>아이디</th>
                            <td><input type='text' name='loginId' id='loginId' /></td>
                            <th>비밀번호</th>
                            <td><input type='password' name='loginPwd' id='loginPwd' /></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class='bottom-btn-group'>
                <input type='button' class='btn-large primary' id='btnRegister' value='등록' />
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    try {
        const btnRegister = document.getElementById('btnRegister');
        if(btnRegister) {
            btnRegister.addEventListener('click', () => {
                if(document.getElementById('loginId').value != '' && document.getElementById('loginPwd').value != '') {
                    addAdmin();
                } else {
                    alert('아이디 또는 비밀번호를 입력하세요');
                }
            });
        }
    } catch(e) {}

    getter();
});

const addAdmin = () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'addAdmin');
    formData.append('loginId', document.getElementById('loginId').value);
    formData.append('loginPwd', document.getElementById('loginPwd').value);

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
		alert(data.message);
	})
	.catch(error => console.log(error));
}

const getter = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getAdmin');

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
        document.getElementById('loginId').value = data.id;   
        document.getElementById('loginPwd').value = data.pwd;
    }
}
</script>