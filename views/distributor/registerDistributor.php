<div id='lnbCloseBtn' class="btn-lnb-close">
    <i class='bx bx-left-arrow-alt'></i>
</div>

<div class="title-container">
    <div class="title-box">총판 등록</div>
    <div class="btn-box"></div>                
</div>

<div class="card-container">
    <div class="card">
        <div>
            <form id='frm'>
                <input type='hidden' name='controller' value='distributor' />
                <input type='hidden' name='mode' value='registerDistributor' />
                <input type='hidden' name='uid' value="<?=$_GET['uid']?>" />

                <table class='register-table'>
                    <tr>
                        <th>총판명</th>
                        <td>
                            <input type='text' class='input w300' name='name' id='name' />
                        </td>
                    </tr>
                    <tr>
                        <th>총판 코드</th>
                        <td>
                            <input type='text' class='input w300' name='code' id='code' />
                        </td>
                    </tr>
                    <tr>
                        <th>총판 관리자</th>
                        <td>
                            <input type='text' class='input' name='adminName' id='adminName' />
                        </td>
                    </tr>
                    <tr>
                        <th>총판 관리자 연락처</th>
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
            <input type='button' class='btn-large primary' id='btnRegister' value='총판 등록' />
        </div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	    
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
                        location.href = `?controller=distributor&action=listDistributor`;
                    } else {
                        console.log(data.message);
                    }

                }
            })
            .catch(error => console.log(error));
        }
    }
}
</script>