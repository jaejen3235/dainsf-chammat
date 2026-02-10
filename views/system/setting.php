<style>
.switch {
    position: relative;
    display: inline-block;
    width: 80px;
    height: 30px;
    margin: 0 10px;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 34px;
}

.switch input {
    display: none;
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 5px;
    bottom: 5px;
    background-color: #fff;
    transition: 0.4s;
    border-radius: 20px;
}

input:checked + .slider {
    background-color: #ff278c;
}

input:checked + .slider:before {
    transform: translateX(50px);
}
</style>

<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 환경설정</div>
    <div class='content-wrapper'>
        <div>
            <form id='frm'>
                <input type='hidden' name='controller' value='mes' />
                <input type='hidden' name='mode' value='registerSetting' />
                <table class='list'>
                    <colgroup>                      
                        <col width='150' />
                        <col />
                        <col width='150' />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>설정명</th>
                            <th>내용</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>BOM 사용</td>
                            <td>
                                체크시 생산입고시 완제품의 하위부품들의 소요량을 계산하여 생산불출을 시키며, 재고수량도 변경을 시킴
                            </td>
                            <td>
                                <label class='switch'>
                                    <input type='checkbox' name='enableBom' id='enableBom' value='Y'>
                                        <span class='slider'></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td>재고수량 '-' 허용</td>
                            <td>
                                체크시 자재 불출 시 재고수량이 부족할 때 마이너스 처리
                            </td>
                            <td>
                                <label class='switch'>
                                    <input type='checkbox' name='minusStockCount' id='minusStockCount' value='Y'>
                                        <span class='slider'></span>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class='mt20 center'>
            <input type='button' class='btn-large primary' id='btnRegister' value='저장하기' />
        </div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', async ()=>{
    const btnRegister = document.getElementById('btnRegister');
    if(btnRegister) {
        btnRegister.addEventListener('click', () => {
            register();
        });
    }

    getter();
});


// 환경설정 등록
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
                    alert(data['message']);
                }
            })
            .catch(error => console.log(error));
        }
    }
}


const getter = async () => {  
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getSetting');

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
        console.log(error);
    }
}

const setter = (data) => {
    if (data) {        
        if (data && data.hasOwnProperty('enableBom')) {
            document.getElementById('enableBom').checked = data.enableBom === 'Y';
        }   
        
        if (data && data.hasOwnProperty('minusStockCount')) {
            document.getElementById('minusStockCount').checked = data.minusStockCount === 'Y';
        }   
    }
}
</script>