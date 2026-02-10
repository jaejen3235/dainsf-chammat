<?php
include "./head.php";
?>
    
<main>
    <div class="content-container">
        <div class='form-wrapper'>
            <div class='title'>출고 등록</div>
            <input type='hidden' name='uid' id='uid' />
            <table>
                <colgroup>
                    <col width='130' />
                    <col />
                </colgroup>
                <tr>
                    <th>출고 품목</th>
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
                    <th>출고 일자</th>
                    <td>
                        <input type='text' class='datepicker' name='outDate' id='outDate' inputmode='none'/>
                    </td>
                </tr>
                <tr>
                    <th>출고 수량</th>
                    <td>
                        <input type='text' name='qty' id='qty' />
                    </td>
                </tr>
            </table>
        </div>
        <div class='mt30 center'>
            <input type='button' class='btn btn-large primary' id='btnRegister' value='출고 등록' />
        </div>
    </div>

    <div class='mt30'>
        <table class='responsive-table list'>
            <thead>
                <tr>
                    <th>일자</th>
                    <th>품목</th>
                    <th>수량</th>
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
                    { id: 'item', message: '출고 품목을 선택하세요', type: 'select' },
                    { id: 'qty', message: '출고 수량을 입력하세요', type: 'text' },
                    { id: 'outDate', message: '출고일을 선택하세요', type: 'text' }
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
            item.addEventListener('change', getItem);
        }
    } catch(e) {}

    getItemsInOutList({page : 1});
    getItemList({page : 1});
});


// 출하 등록
const register = () => {    
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'registerItemOut');
    formData.append('uid', document.getElementById('uid').value);
    formData.append('item', document.getElementById('item').value); 
    formData.append('qty', document.getElementById('qty').value);
    formData.append('outDate', document.getElementById('outDate').value);

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

const getItemsInOutList = async ({
    page,
    per = 10,
    block = 4,
    orderBy = 'uid',
    order = 'desc'
}) => {    
    let where = `where classification='출고'`;

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
    formData.append('mode', 'getItemsInOutList');
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

        getPaging('mes_items_inout', 'uid', where, page, per, block, 'getItemsInOutList');
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
        const [year, month, day] = item.registerDate.split('-');
        const formattedDate = `${month}-${day}`;

        return `
            <tr>
                <td data-label='일자'>${formattedDate}</td>
                <td data-label='품명'>${item.itemName}</td>
                <td data-label='수량'>${comma(item.qty)}</td>
                <td data-label='관리'>
                    <input type='button' class='btn grey' value='수정' onclick='getter(${item.uid})' />
                </td>
            </tr>
        `;
    }).join('');
};




// 품목 하나 가져오기
const getItem = async () => { 

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getItem');
    formData.append('uid', document.getElementById('item').value);

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
        document.getElementById('code').innerText = data.code;       
    }
}

// 출고 하나 가져오기
const getter = async (uid) => {  
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getItemsInOut');
    formData.append('uid', uid);

    try {
        const response = await fetch('../handler.php', {
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
        document.getElementById('item').value = data.itemUid;        
        document.getElementById('code').innerText = data.itemCode;
        document.getElementById('qty').value = comma(data.qty);   
        document.getElementById('outDate').value = data.registerDate;

        // 화면을 맨 위로 스크롤
        window.scrollTo({
            top: 0,
            behavior: 'smooth'  // 부드럽게 스크롤 되도록 설정
        });        
    }
}

const clean = () => {
    document.getElementById('outDate').value = '';
    document.getElementById('qty').value = '';
    document.getElementById('item').value = '0';
    document.getElementById('code').innerText = '';
}
</script>