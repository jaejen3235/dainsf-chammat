<div id='lnbCloseBtn' class="btn-lnb-close">
    <i class='bx bx-left-arrow-alt'></i>
</div>

<div class="title-container">
    <div class="title-box">상품 등록</div>
    <div class="btn-box"></div>                
</div>

<div class="card-container">
    <div class="card">
        <div>
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='agency' />
                <input type='hidden' name='mode' id='mode' value='registerGoods' />
                <input type='hidden' name='uid' id='uid' value="<?=$_GET['uid']?>" />

                <table class='register-table'>
                    <colgroup>
                        <col width='200'>
                        <col />
                    </colgroup>
                    <tr>
                        <th>기기 카테고리</th>
                        <td>
                            <select name='category' id='category'>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>기기 모델명</th>
                        <td>
                            <input type='text' class='input w300' name='model' id='model' />
                        </td>
                    </tr>
                    <tr>
                        <th>요금제</th>
                        <td>
                            <select name='paymentName' id='paymentName'>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>기기변경</th>
                        <td>
                            <input type='text' class='input comma' name='changeDevice' id='changeDevice' />
                        </td>
                    </tr>
                    <tr>
                        <th>통신사이동</th>
                        <td>
                            <input type='text' class='input comma' name='moveTelecom' id='moveTelecom' />
                        </td>
                    </tr>
                    <tr>
                        <th>신규가입</th>
                        <td>
                            <input type='text' class='input comma' name='joinTelecom' id='joinTelecom' />
                        </td>
                    </tr>
                    <tr>
                        <th>공시지원금</th>
                        <td>
                            <input type='text' class='input comma' name='supportFund' id='supportFund' />
                        </td>
                    </tr>
                    <tr>
                        <th>공시지원 기기변경</th>
                        <td>
                            <input type='text' class='input comma' name='supportChangeDevice' id='supportChangeDevice' />
                        </td>
                    </tr>
                    <tr>
                        <th>공시지원 통신사이동</th>
                        <td>
                            <input type='text' class='input comma' name='supportMoveTelecom' id='supportMoveTelecom' />
                        </td>
                    </tr>
                    <tr>
                        <th>공시지원 신규가입</th>
                        <td>
                            <input type='text' class='input comma' name='supportJoinTelecom' id='supportJoinTelecom' />
                        </td>
                    </tr>                 
                </table>
            </form>
        </div>
        <div class='center mt30'>
            <input type='button' class='btn-large primary' id='btnRegister' value='상품 등록' />
            <input type='button' class='btn-large grey-400' id='btnList' value='목록가기' />
        </div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', async ()=>{	  
    const uid = document.getElementById('uid');

    try {
        await getCategoryList();
        await getPaymentList();

        const btnRegister = document.getElementById('btnRegister');
        if (btnRegister) {
            btnRegister.addEventListener('click', register);
        } else {
            console.log('btnRegister button not found');
        }

        const btnList = document.getElementById('btnList');
        if (btnList) {
            btnList.addEventListener('click', () => {
                location.href = `?controller=agency&action=listGoods&menu=goods`;
            });
        } else {
            console.log('btnList button not found');
        }

        if(uid.value != '') {
            btnRegister.value = '상품 수정';
            await getter(uid.value);  // getCategoryList와 getPaymentList가 끝난 후 실행
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
                        location.href = `?controller=agency&action=listGoods&menu=goods`;
                    } else {
                        console.log(data.message);
                    }

                }
            })
            .catch(error => console.log(error));
        }
    }
}


const getCategoryList = () => {
    return new Promise((resolve, reject) => {
        let tag = `<option value='0'>== 전체 ==</option>`;

        const formData = new FormData();
        formData.append('controller', 'agency');
        formData.append('mode', 'getCategoryList');

        fetch('./handler.php', {
            method: 'post',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(function(data) {
            if(data != null || data != '') {
                data.forEach(item => {
                    tag += `<option value='${item.categoryName}'>${item.categoryName}</option>`;
                });
            }

            document.querySelector('#category').innerHTML = tag;
            resolve();  // 카테고리 리스트 설정이 완료되면 resolve 호출
        })
        .catch(error => {
            console.log(error);
            reject(error);  // 에러 발생 시 reject 호출
        });
    });
}

const getPaymentList = () => {
    return new Promise((resolve, reject) => {
        let tag = `<option value='0'>== 전체 ==</option>`;

        const formData = new FormData();
        formData.append('controller', 'agency');
        formData.append('mode', 'getAllPaymentList');

        fetch('./handler.php', {
            method: 'post',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(function(data) {
            if(data != null || data != '') {
                data.forEach(item => {
                    tag += `<option value='${item.paymentName}'>${item.paymentName}</option>`;
                });
            }

            document.querySelector('#paymentName').innerHTML = tag;
            resolve();  // 카테고리 리스트 설정이 완료되면 resolve 호출
        })
        .catch(error => {
            console.log(error);
            reject(error);  // 에러 발생 시 reject 호출
        });
    });
}


const getter = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getGoods');
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
        document.getElementById('category').value = data.category;
        document.getElementById('paymentName').value = data.paymentName;
        document.getElementById('model').value = data.model;
        document.getElementById('changeDevice').value = comma(data.changeDevice);
        document.getElementById('moveTelecom').value = comma(data.moveTelecom);
        document.getElementById('joinTelecom').value = comma(data.joinTelecom);
        document.getElementById('supportFund').value = comma(data.supportFund);
        document.getElementById('supportChangeDevice').value = comma(data.supportChangeDevice);
        document.getElementById('supportMoveTelecom').value = comma(data.supportMoveTelecom);
        document.getElementById('supportJoinTelecom').value = comma(data.supportJoinTelecom);
    }
}

const displayError = (error) => {
    console.error('Error:', error);
}
</script>