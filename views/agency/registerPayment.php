<div id='lnbCloseBtn' class="btn-lnb-close">
    <i class='bx bx-left-arrow-alt'></i>
</div>

<div class="title-container">
    <div class="title-box">요금제 등록</div>
    <div class="btn-box"></div>                
</div>

<div class="card-container">
    <div class="card">
        <div>
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='agency' />
                <input type='hidden' name='mode' id='mode' value='registerPayment' />
                <input type='hidden' name='uid' id='uid' value="<?=$_GET['uid']?>" />

                <table class='register-table'>
                    <colgroup>
                        <col width='200'>
                        <col />
                    </colgroup>
                    <tr>
                        <th>요금제 구분</th>
                        <td>
                            <input type='radio' name='category' id='category1' value='5G' checked /> 5G
                            <input type='radio' name='category' id='category2' value='LTE' /> LTE
                        </td>
                    </tr>
                    <tr>
                        <th>세대 구분</th>
                        <td>
                            <input type='radio' name='age' id='age1' value='성인' checked /> 성인
                            <input type='radio' name='age' id='age2' value='청소년' /> 청소년
                            <input type='radio' name='age' id='age3' value='어르신' /> 어르신
                        </td>
                    </tr>
                    <tr>
                        <th>요금제명</th>
                        <td>
                            <input type='text' class='input w300' name='paymentName' id='paymentName' />
                        </td>
                    </tr>
                    <tr>
                        <th>데이터명</th>
                        <td>
                            <input type='text' class='input w300' name='dataName' id='dataName' />
                        </td>
                    </tr>
                    <tr>
                        <th>월 이용료</th>
                        <td>
                            <input type='text' class='input comma' name='payment' id='payment' />
                        </td>
                    </tr>
                    <tr>
                        <th>사용 여부</th>
                        <td>
                            <input type='radio' name='display' id='display1' value='Y' checked /> 사용
                            <input type='radio' name='display' id='display2' value='N' /> 미사용
                        </td>
                    </tr>                 
                </table>
            </form>
        </div>
        <div class='center mt30'>
            <input type='button' class='btn-large primary' id='btnRegister' value='요금제 등록' />
            <input type='button' class='btn-large grey-400' id='btnList' value='목록가기' />
        </div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	  
    const uid = document.getElementById('uid');

    try {
        if(uid.value != '') {
            getter(uid.value);
            document.getElementById('btnRegister').value = '요금제 수정';
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }

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
                location.href = `?controller=agency&action=listPayment&menu=payment`;
            });
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
                        location.href = `?controller=agency&action=listPayment&menu=payment`;
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
    formData.append('mode', 'getPayment');
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
        if(data.category == '5G') document.getElementById('category1').checked = true;
        else document.getElementById('category2').checked = true;

        if(data.age == '성인') document.getElementById('age1').checked = true;
        else if(data.age == '청소년') document.getElementById('age2').checked = true;
        else if(data.age == '어르신') document.getElementById('age3').checked = true;
        else {
            console.log('세대 에러');
        }

        document.getElementById('paymentName').value = data.paymentName;
        document.getElementById('dataName').value = data.dataName;
        document.getElementById('payment').value = comma(data.payment);

        if(data.display == 'Y') document.getElementById('display1').checked = true;
        else document.getElementById('display2').checked = true;
    }
}

const displayError = (error) => {
    console.error('Error:', error);
}
</script>