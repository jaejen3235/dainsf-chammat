<?php
include "./head.php";
?>
    
<main>
    <div class="content-container">
        <div class='form-wrapper'>
            <div class='title'>작업지시 관리</div>
            <input type='hidden' name='uid' id='uid' />
            <input type='hidden' name='workOrderUid' id='workOrderUid' />
            <table>
                <colgroup>
                    <col width='130' />
                    <col />
                </colgroup>
                <tr>
                    <th>생산 품목</th>
                    <td>
                        <select name='item' id='item'>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>품번</th>
                    <td>
                        <span id='code'></span>
                    </td>
                </tr>
                <tr>
                    <th>작업 공정</th>
                    <td>
                        <select name='process' id='process'>                            
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>총생산 수량</th>
                    <td>
                        <input type='text' name='productQty' id='productQty' />
                    </td>
                </tr>
                <tr>
                    <th>적합 수량</th>
                    <td>
                        <input type='text' name='qty' id='qty' /> <input type='button' class='btn success' id='btnAll' value='ALL' />
                    </td>
                </tr>
                <tr>
                    <th>부적합 수량</th>
                    <td>
                        <input type='text' name='defectQty' id='defectQty' readonly /> <input type='button' class='btn warning' id='btnCal' value='계산' />
                    </td>
                </tr>
                <tr>
                    <th>부적합 사유</th>
                    <td>
                        <select name='defectReason' id='defectReason'>                            
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>작업자</th>
                    <td>
                        <select name='employee' id='employee'>                            
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>작업 일자</th>
                    <td>
                        <input type='text' class='datepicker' name='workDate' id='workDate' inputmode='none'/>
                    </td>
                </tr>
            </table>
        </div>
        <div class='mt30 center'>
            <input type='button' class='btn btn-large primary' id='btnRegister' value='실적 등록' />
        </div>
    </div>

    <div class='mt30'>
        <table class='responsive-table list'>
            <thead>
                <tr>
                    <th>작업 일자</th>
                    <th>작업 품목</th>
                    <th>지시 수량</th>
                    <th>잔여 수량</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="paging-area mt20"></div>
</main>

<?php
include "./foot.php";
?>

<script>
window.addEventListener('DOMContentLoaded', async ()=>{	
    // 등록
    try {
        const btnRegister = document.getElementById('btnRegister');
        if (btnRegister) {            
            btnRegister.addEventListener('click', () => {
                // 배열을 전달하여 함수 호출
                const fieldsArray = [                 
                    { id: 'item', message: '입고 품목을 선택하세요', type: 'select' },
                    { id: 'process', message: '작업 공정을 선택하세요', type: 'select' },
                    { id: 'productQty', message: '총생산수량을 입력하세요', type: 'text' },
                    { id: 'qty', message: '적합수량을 입력하세요', type: 'text' },
                    { id: 'employee', message: '작업자를 선택하세요', type: 'select' },
                    { id: 'workDate', message: '작업일자를 입력하세요', type: 'text' }
                ];

                // 유효성 검사를 위한 함수 호출
                const isValid = validateFields(fieldsArray);
                if(isValid) register();
                else console.log('유효성 검사를 통과하지 못했습니다');
            });            
        } else {
            console.log('btnRegister button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }

    try {
        const item = document.getElementById('item');

        if(item) {
            item.addEventListener('change', () => {
                getItem('', document.getElementById('item').value);
            });
        }
    } catch(e) {}

    try {
        const btnAll = document.getElementById('btnAll');
        const productQty = document.getElementById('productQty');
        const qty = document.getElementById('qty');

        if(btnAll) {
            btnAll.addEventListener('click', () => {
                if(productQty.value != '') {
                    qty.value = productQty.value;
                } else {
                    alert('총생산 수량을 입력하세요');
                }

                cal();
            });
        }
    } catch(e) {}

    try {
        const btnCal = document.getElementById('btnCal');

        if(btnCal) {
            btnCal.addEventListener('click', cal);
        }
    } catch(e) {}

    getWorkOrderList({page : 1});
    getItemList({page : 1});
    getEmployeeList({page : 1});
    getDefectReason({page : 1});
    getSelectList('getProcess', 'uid', 'name', '#process');
});

// 부적합 수량 계산
const cal = () => {
    const productQty = document.getElementById('productQty');
    const qty = document.getElementById('qty');
    const defectQty = document.getElementById('defectQty');

    if(productQty.value == '') {
        alert('총생산수량을 입력하세요');
        return;
    }

    if(qty.value == '') {
        alert('적합수량을 입력하세요');
        return;
    }

    defectQty.value = Number(removeComma(productQty.value)) - Number(removeComma(qty.value));
}

// 출하 등록
const register = () => {    
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'registerProductWork');
    formData.append('uid', document.getElementById('uid').value);
    formData.append('workOrderUid', document.getElementById('workOrderUid').value);
    formData.append('item', document.getElementById('item').value); 
    formData.append('process', document.getElementById('process').value); 
    formData.append('productQty', document.getElementById('productQty').value); 
    formData.append('qty', document.getElementById('qty').value);
    formData.append('defectQty', document.getElementById('defectQty').value);
    formData.append('defectReason', document.getElementById('defectReason').value);
    formData.append('employee', document.getElementById('employee').value);
    formData.append('workDate', document.getElementById('workDate').value);

    fetch('../handler.php', {
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
                clean();
                getItemsInOutList({page:1});                
            } else {                
                alert(data.message);
            
            }
        }
    });
}

const getItemList = async ({
    page,
    per = 10,
    block = 4,
    orderBy = 'uid',
    order = 'desc'
}) => {    
    let where = `where 1=1`;

    // 검색어가 있다면
    try {
        const searchText = document.getElementById('searchText');
        if(searchText) {
            if(searchText.value != '') {
                where += ` and (name like '%${searchText.value}%' or code like '%${searchText.value}%')`;
            }
        }
    } catch(e) {}
    

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getItemList');
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('orderby', orderBy);
    formData.append('asc', order);

    try {
        const response = await fetch('../handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        setItemList(data);
    } catch (error) {
        console.error('품목 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const setItemList = (data) => {    
    const select = document.querySelector('#item');

    // 기본 선택 옵션 추가
    const defaultOption = document.createElement('option');
    defaultOption.value = '0';
    defaultOption.textContent = '== 선택 ==';
    select.appendChild(defaultOption);

    // 받아온 데이터를 기반으로 option 태그 추가
    data.data.forEach(item => {
        const option = document.createElement('option');
        option.value = item.uid; // option의 value는 제품 구분의 id로 설정
        option.textContent = item.name; // option의 표시되는 텍스트는 제품 구분의 name으로 설정
        select.appendChild(option);
    });
};


const getEmployeeList = async ({
    page,
    per = 10,
    block = 4,
    orderBy = 'uid',
    order = 'desc'
}) => {    
    let where = `where 1=1`;

    // 검색어가 있다면
    try {
        const searchText = document.getElementById('searchText');
        if(searchText) {
            if(searchText.value != '') {
                where += ` and (name like '%${searchText.value}%' or code like '%${searchText.value}%')`;
            }
        }
    } catch(e) {}
    

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getEmployeeList');
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('orderby', orderBy);
    formData.append('asc', order);

    try {
        const response = await fetch('../handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        setEmployeeList(data);
    } catch (error) {
        console.error('품목 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const setEmployeeList = (data) => {    
    const select = document.querySelector('#employee');

    // 기본 선택 옵션 추가
    const defaultOption = document.createElement('option');
    defaultOption.value = '0';
    defaultOption.textContent = '== 선택 ==';
    select.appendChild(defaultOption);

    // 받아온 데이터를 기반으로 option 태그 추가
    data.data.forEach(item => {
        const option = document.createElement('option');
        option.value = item.uid; // option의 value는 제품 구분의 id로 설정
        option.textContent = item.name; // option의 표시되는 텍스트는 제품 구분의 name으로 설정
        select.appendChild(option);
    });
};

const getDefectReason = async ({
    page,
    per = 10,
    block = 4,
    orderBy = 'uid',
    order = 'desc'
}) => {    
    let where = `where 1=1`;

    // 검색어가 있다면
    try {
        const searchText = document.getElementById('searchText');
        if(searchText) {
            if(searchText.value != '') {
                where += ` and (name like '%${searchText.value}%' or code like '%${searchText.value}%')`;
            }
        }
    } catch(e) {}
    

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDefectReasonList');
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('orderby', orderBy);
    formData.append('asc', order);

    try {
        const response = await fetch('../handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        setDefectReason(data);
    } catch (error) {
        console.error('품목 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const setDefectReason = (data) => {    
    const select = document.querySelector('#defectReason');

    // 기본 선택 옵션 추가
    const defaultOption = document.createElement('option');
    defaultOption.value = '0';
    defaultOption.textContent = '== 선택 ==';
    select.appendChild(defaultOption);

    // 받아온 데이터를 기반으로 option 태그 추가
    data.data.forEach(item => {
        const option = document.createElement('option');
        option.value = item.uid; // option의 value는 제품 구분의 id로 설정
        option.textContent = item.name; // option의 표시되는 텍스트는 제품 구분의 name으로 설정
        select.appendChild(option);
    });
};

const getWorkOrderList = async ({
    page,
    per = 10,
    block = 4,
    orderBy = 'uid',
    order = 'desc'
}) => {    
    let where = `where 1=1`;

    // 검색어가 있다면
    try {
        const searchText = document.getElementById('searchText');
        if(searchText) {
            if(searchText.value != '') {
                where += ` and itemName like '%${searchText.value}%'`;
            }
        }
    } catch(e) {}
    

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getWorkOrderList');
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('orderby', orderBy);
    formData.append('asc', order);

    try {
        const response = await fetch('../handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.list tbody');
        tableBody.innerHTML = generateTableContent(data);

        getPaging('mes_work_order', 'uid', where, page, per, block, 'getWorkOrderList');
    } catch (error) {
        console.error('입고 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>데이터가 없습니다</td></tr>`;
    }

    return data.data.map(item => {
        // registerDate 값을 'YYYY-MM-DD' 형식에서 'MM-DD' 형식으로 변환
        const [syear, smonth, sday] = item.startDate.split('-');
        const std = `${smonth}-${sday}`;

        const [eyear, emonth, eday] = item.endDate.split('-');
        const etd = `${emonth}-${eday}`;

        return `
            <tr>
                <td data-label='작업 일자'>${std} ~ ${etd}</td>
                <td data-label='작업 품목'>${item.name}</td>
                <td data-label='지시 수량'>${comma(item.orderQty)}</td>
                <td data-label='잔여 수량'>${comma(item.remainQty)}</td>
                <td data-label='관리'>
                    <input type='button' class='btn grey' value='생산실적 등록' onclick='getItem(${item.uid}, ${item.itemUid})' />
                </td>
            </tr>
        `;
    }).join('');
};




// 품목 하나 가져오기
const getItem = async (workOrderUid = null, itemUid) => {
    if(workOrderUid)  {
        document.getElementById('workOrderUid').value = workOrderUid;
    }

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getItem');
    formData.append('uid', itemUid);

    try {
        const response = await fetch('../handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setItem(data);
    } catch (error) {
        displayError(error);
    }
}

const setItem = (data) => {
    if (data) {        
        document.getElementById('item').value = data.uid;       
        document.getElementById('code').innerText = data.code;       
    }
}


const clean = () => {
    document.getElementById('workDate').value = '';
    document.getElementById('productQty').value = '';
    document.getElementById('qty').value = '';
    document.getElementById('defectQty').value = '';
    document.getElementById('item').value = '0';
    document.getElementById('process').value = '0';
    document.getElementById('defectReason').value = '0';
    document.getElementById('code').innerText = '';
}
</script>