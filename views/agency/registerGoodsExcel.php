<div id='lnbCloseBtn' class="btn-lnb-close">
    <i class='bx bx-left-arrow-alt'></i>
</div>

<div class="title-container">
    <div class="title-box">상품 엑셀 등록</div>
    <div class="btn-box"></div>                
</div>

<div class="card-container">
    <div class="card">
        <div>
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='agency' />
                <input type='hidden' name='mode' id='mode' value='registerGoodsExcel' />

                <table class='register-table'>
                    <colgroup>
                        <col width='200'>
                        <col />
                    </colgroup>
                    <tr>
                        <th>엑셀 파일</th>
                        <td>
                            <input type='file' class='input w300' name='excel' id='excel' />
                        </td>
                    </tr>                    
                </table>
            </form>
        </div>
        <div class='center mt30'>
            <input type='button' class='btn-large primary' id='btnRegister' value='상품 엑셀 등록' />
            <input type='button' class='btn-large grey-400' id='btnList' value='목록가기' />
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

    try {
        const btnList = document.getElementById('btnList');
        if (btnList) {
            btnList.addEventListener('click', () => {
                location.href = `?controller=agency&action=listGoods&menu=goods`;
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
</script>