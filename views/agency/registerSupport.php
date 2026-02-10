<div class='main-container'>
    <div class='title-wrapper'>지원금 설정</div>
    <div class='content-wrapper'>
        <div class='search-section'>
            <div class='bar-box'></div>
            <div class='title-box'>협력사 검색</div>
            <div class='summary-box'>협력사 선택 시 협력사 정보 및 고객사 리스트가 활성화됩니다.</div>
        </div>
        
        <div class='input-section'>
            <div class='search-box'>
                <input type="text" class='input' id='searchAgencyText' placeholder="협력사명을 입력해주세요">
                <i class="fa fa-search hands" aria-hidden="true"></i>
            </div>
        </div>

        <div class='agency-section'>
            <div class='table-box'>
                <table class='list' id='agencyList'>
                    <colgroup>
                        <col width='50'>
                        <col>
                        <col width='150'>
                    </colgroup>
                    <thead>
                        <tr>
                            <th>번호</th>
                            <th>협력사명</th>
                            <th>선택</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="agency-paging-area paging-area"></div>
            </div>
            <div class='pannel-box'>
                <div class='flex-box'>
                    <div>협력사명</div>
                    <div id='agencyName'></div>
                </div>
                <div class='flex-box'>
                    <div>담당자명</div>
                    <div id='adminName'></div>
                </div>
                <div class='flex-box'>
                    <div>연락처</div>
                    <div id='adminMobile'></div>
                </div>
                <div class='flex-box'>
                    <div>기업코드</div>
                    <div id='code'></div>
                </div>
            </div>
        </div>
    </div>

    <div class='content-wrapper' style='margin-top:23px'>
        <div class='content-flex'>
            <div class='left-content'>
                <div class='search-section'>
                    <div class='bar-box'></div>
                    <div class='title-box'>고객사</div>            
                </div>
                <div class='client-section'>
                    <table class='list' id='clientList'>
                        <colgroup>
                            <col width='50'>
                            <col width='200'>
                            <col>
                        </colgroup>
                        <thead>
                            <tr>
                                <th>
                                    <label class="custom-checkbox">
                                        <input type="checkbox" id='chkClientAll'>
                                        <span class="checkmark"></span>
                                    </label>
                                </th>
                                <th>고객사명</th>
                                <th>로고</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div class="client-paging-area paging-area"></div>
                </div>
            </div>

            <div class='right-content'>
                <div class='search-section'>
                    <div class='bar-box'></div>
                    <div class='title-box'>지원금 설정</div>            
                </div>
                <form id='frm'>
                    <input type='hidden' name='controller' value='agency' />
                    <input type='hidden' name='mode' value='registerDiscount' />
                    <input type='hidden' name='agencyUid' id='agencyUid' />
                    <table class='list' id='supportList'>
                        <colgroup>
                            <col/>
                            <col width='200'>
                            <col>
                        </colgroup>
                        <thead>
                            <tr>
                                <th>고객사명</th>
                                <th>지원금</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </form>
                <div class='bottom-btn-group'>
                    <input type='button' class='btn-large orange' id='btnRegister' value='지원금 저장' />
                </div>
            </div>
        </div>
    </div>
</div>

<input type='hidden' id='distributorCode' value='<?=$_SESSION['distributorCode']?>' />
<input type='hidden' id='agencyUid' />

<script>
window.addEventListener('DOMContentLoaded', async () => {	
    // 등록
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




    getAgencyList({page:1});
    await getClientList({page:1});

    try{
		const chkClientAll = document.getElementById('chkClientAll');
		chkClientAll.addEventListener('click', ()=>{
			if(chkClientAll.checked) {
                checkAll('chkClient');
                updateSupportList();
            } else {
                checkAllDisolve('chkClient');
                updateSupportList();
            }
		});
	} catch(e) {}
});    

// 협력사 등록
const register = () => {    
    if(document.getElementById('agencyUid').value == '') {
        alert('협력사를 선택하세요');
        return false;
    }

    // chkClient 체크박스가 선택되어 있는지 확인
    const checkedBoxes = document.querySelectorAll('#clientList .chkClient:checked');
    if (checkedBoxes.length === 0) {
        alert('적어도 하나의 고객사를 선택하세요');
        return false;
    }

     // 지원금 입력 필드 확인
    const supportInputs = document.querySelectorAll('input[name="supportAmount[]"]'); // 모든 지원금 입력 필드 선택
    let supportAmountFilled = true; // 지원금 입력 여부 플래그
    supportInputs.forEach(input => {
        if (input.value.trim() === '') {
            supportAmountFilled = false; // 지원금이 비어있거나 숫자가 아닌 경우
        }
    });

    if (!supportAmountFilled) {
        alert('모든 고객사에 대해 지원금을 입력하세요');
        return false;
    }

    const frm = document.getElementById('frm');

    if(frm) {
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
                alert(data.message);
            }
        })
        .catch(error => console.log(error));
    }
}


const distributorCode = document.getElementById('distributorCode').value;
const getAgencyList = async ({ page, per = 5, block = 4, where = `where distributor='${distributorCode}'`, orderBy = 'uid', order = 'asc'}) => {
    
    let tag = '';

    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getAgencyListWithNo');
    formData.append('where', where);
    formData.append('orderby', 'uid');
    formData.append('per', per);
    formData.append('asc', 'asc');
    formData.append('page', page);

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
        const totalCount = data.totalCount;  // totalCount 추출
        const listData = data.data;  // 실제 리스트 데이터 추출

        if (!listData || listData.length === 0) {
            tag = `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
        } else {
            tag = listData.map((item, index) => {
                const no = totalCount - index;  // 총 개수에서 index를 빼서 no 계산

                return `
                    <tr> 
                        <td class='center'>${no}</td>                  
                        <td class='center'>${item.name}</td>
                        <td class='center'>                    
                            <input type='button' class='btn grey' value='선택' onclick='getter(${item.uid})' />
                        </td>
                    </tr>
                `;
            }).join('');  // map의 결과를 문자열로 합침
        }

        document.querySelector('#agencyList tbody').innerHTML = tag;
        getPagingTarget('sk_agency', 'uid', where, page, per, block, 'getAgencyList', 'agency-paging-area');
    })
    .catch(error => console.log(error));
}

// 협력사 가져오기
const getter = async (uid) => {  
    document.getElementById('agencyUid').value = uid;

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
        document.getElementById('agencyName').innerText = data.name;
        document.getElementById('code').innerText = data.code;
        document.getElementById('adminName').innerText = data.adminName;
        document.getElementById('adminMobile').innerText = data.adminMobile;

        updateSupportList();
    }
}


const getClientList = async ({ page, per = 5, block = 4, where = `where 1=1`, orderBy = 'uid', order = 'asc' }) => {
    let tag = '';    

    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getClientList');
    formData.append('where', where);
    formData.append('orderby', 'uid');
    formData.append('asc', 'asc');
    formData.append('page', page);
    formData.append('per', 5);

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
        const listData = data;  // data 자체가 리스트일 경우

        if (!listData || listData.length === 0) {
            tag = `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
        } else {
            listData.forEach(item => {
                tag += `
                    <tr>
                        <td class='center'>
                            <label class="custom-checkbox">
                                <input type="checkbox" class='chkClient' value='${item.uid}' />
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td class='center'>${item.name}</td>
                        <td class='center'><img src='../attach/client/${item.logo}' /></td>
                    </tr>
                `;
            });
        }

        document.querySelector('#clientList tbody').innerHTML = tag;
        getPagingTarget('sk_client', 'uid', where, page, per, block, 'getClientList', 'client-paging-area');

        // 체크박스에 클릭 이벤트 리스너 추가
        const checkboxes = document.querySelectorAll('.chkClient');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('click', () => {                
                updateSupportList(); // 해당 함수가 정의되어 있어야 함
            });
        });
    })
    .catch(error => console.log(error));
}

async function updateSupportList() {
    const supportListTbody = document.querySelector('#supportList tbody');
    supportListTbody.innerHTML = ''; // 기존 내용 지우기

    // 고객 리스트에서 체크된 항목 수집
    const checkedBoxes = document.querySelectorAll('#clientList .chkClient:checked');
    
    for (const box of checkedBoxes) {
        const row = box.closest('tr'); // 체크된 체크박스의 부모 tr을 찾음
        const name = row.querySelector('td:nth-child(2)').textContent; // 고객사명 추출
        const uid = box.value; // 체크박스의 uid 값 추출

        // 기존에 등록된 지원금이 있다면 가지고 온다
        const formData = new FormData();
        formData.append('controller', 'agency');
        formData.append('mode', 'getSupport');
        formData.append('clientUid', uid);
        formData.append('agencyUid', document.getElementById('agencyUid').value);

        try {
            const response = await fetch('./handler.php', {
                method: 'post',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }

            const data = await response.json();

            // 지원 리스트에 추가할 행 생성
            const newRow = `
                <tr>
                    <td class='center'>${name}</td>
                    <td class='center'>
                        <input type='hidden' name='clientUid[]' value='${uid}' />
                        <input type='text' class='input right' placeholder='지원금 입력' name='supportAmount[]' value='${comma(data.discount)}' />
                    </td>
                </tr>
            `;

            // tbody에 새로운 행 추가
            supportListTbody.insertAdjacentHTML('beforeend', newRow);

        } catch (error) {
            displayError(error);
        }
    }

    // 입력 필드에 콤마 추가 이벤트 리스너 (이벤트 위임)
    supportListTbody.addEventListener('input', function(event) {
        if (event.target.name === 'supportAmount[]') {
            // 숫자만 필터링하고 콤마 추가
            const value = event.target.value.replace(/,/g, ''); // 기존의 콤마 제거
            const formattedValue = formatNumber(value); // 포맷팅
            event.target.value = formattedValue; // 포맷팅된 값을 입력 필드에 반영
        }
    });
}


// 숫자를 포맷팅하는 함수
function formatNumber(num) {
    // 숫자로 변환하고, 3자리마다 콤마 추가
    return num.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}


const displayError = (error) => {
    console.error('Error:', error);
}

const clearn = () => {
    const inputs = document.querySelectorAll('.input'); // input 클래스를 가진 모든 요소 선택
    inputs.forEach(input => input.value = ''); // 각 요소의 값을 빈 문자열로 설정
};
</script>