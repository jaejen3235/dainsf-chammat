<div id='lnbCloseBtn' class="btn-lnb-close">
    <i class='bx bx-left-arrow-alt'></i>
</div>

<div class="title-container">
    <div class="title-box">고객사 등록</div>
    <div class="btn-box"></div>                
</div>

<div class="card-container">
    <div class="card">
        <div>
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='agency' />
                <input type='hidden' name='mode' id='mode' value='registerClient' />
                <input type='hidden' name='uid' id='uid' value="<?=$_GET['uid']?>" />
                <input type='hidden' name='oldFile' id='oldFile' />

                <table class='register-table'>
                    <colgroup>
                        <col width='200'>
                        <col />
                    </colgroup>
                    <tr>
                        <th>고객사명</th>
                        <td>
                            <input type='text' class='input w300' name='name' id='name' />
                        </td>
                    </tr>
                    <tr>
                        <th>로고</th>
                        <td>
                            <div id='hiddenDiv' class='hidden'>
                                <img src='' id='logoImg' style='width:400px' />
                            </div>
                            <div>
                                <input type='file' class='input' name='logo' id='logo' />
                            </div>
                        </td>
                    </tr>                    
                </table>
            </form>
        </div>
        <div class='center mt30'>
            <input type='button' class='btn-large primary' id='btnRegister' value='고객사 등록' />
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
                location.href = `?controller=agency&action=listClient&menu=client`;
            });
        } else {
            console.log('btnRegister button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }

    try {
        if(uid.value != '') {
            btnRegister.value = '고객사 수정';
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
                        location.href = `?controller=agency&action=listClient&menu=client`;
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
    formData.append('mode', 'getClient');
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
        document.getElementById('oldFile').value = data.logo;
        document.getElementById('logoImg').src = `./attach/client/${data.logo}`;
        document.getElementById('hiddenDiv').classList.toggle('hidden');
        document.getElementById('hiddenDiv').style.marginBottom = '20px';
    }
}

const displayError = (error) => {
    console.error('Error:', error);
}
</script>