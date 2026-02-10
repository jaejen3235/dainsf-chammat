<div class='main-container'>
    <div class='title-wrapper'>설비 등록</div>
        <form id='frm'>
            <input type='hidden' name='controller' id='controller' value='mes' />
            <input type='hidden' name='mode' id='mode' value='registerMachine' />
            <input type='hidden' name='uid' id='uid' value="<?php echo $_GET['uid']; ?>" />
            <input type='hidden' name='oldImg' id='oldImg' />
            <div class='content-wrapper'>
                <div class='card-title'>기본 정보</div>
                <div>                
                    <table class='register'>
                        <colgroup>
                            <col width='200'>
                            <col />
                            <col width='200'>
                            <col />
                        </colgroup>
                        <tr>
                            <td>설비명</td>
                            <td>
                                <input type='text' class='p' data-width='100' name='name' id='name' />
                            </td>
                            <td>관리 번호</td>
                            <td>
                                <input type='text' class='p' data-width='100' name='code' id='code' />
                            </td>
                        </tr>
                        <tr>
                            <td>제조 업체</td>
                            <td>
                                <input type='text' class='p' data-width='100' name='maker' id='maker' />
                            </td>
                            <td>업체 연락처</td>
                            <td>
                                <input type='text' class='p' data-width='100' name='makerContact' id='makerContact' />
                            </td>
                        </tr>
                        <tr>
                            <td>구입년도</td>
                            <td>
                                <input type='text' class='p' data-width='100' name='purchaseYear' id='purchaseYear' />
                            </td>
                            <td>사진</td>
                            <td>
                                <div id='img'></div>
                                <input type='file' class='p' data-width='100' name='attach' id='attach' />
                            </td>
                        </tr>
                        <tr>
                            <td>관리담당(정)</td>
                            <td>
                                <select class='employee' name='mainOfficer' id='mainOfficer'>
                                </select>
                            </td>
                            <td>관리담당(부)</td>
                            <td>
                                <select class='employee' name='subOfficer' id='subOfficer'>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>정격전압</td>
                            <td>
                                <input type='text' name='ratedVoltage' id='ratedVoltage' />
                            </td>
                        </tr>                            
                    </table>                
                </div>
            </div>

            <div class='content-wrapper mt20'>
                <div class='card-title flex'>
                    <div>재원 정보</div>
                    <div>
                        <input type='button' class='btn' value='재원 추가' onclick='addSpec()' />
                    </div>
                </div>
                <div>
                    <table class='list register' id='spec'>
                        <thead>
                            <tr>
                                <th>재원명</th>
                                <th>재원값</th>
                                <th>관리</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class='content-wrapper mt20'>
                <div class='card-title flex'>
                    <div>부품 정보</div>
                    <div>
                        <input type='button' class='btn' value='부품 추가' onclick='addComponent()' />
                    </div>
                </div>
                <div>
                    <table class='list register' id='component'>
                        <thead>
                            <tr>
                                <th>부품명</th>
                                <th>규격</th>
                                <th>구입 업체</th>
                                <th>업체 연락처</th>
                                <th>재고수량</th>
                                <th>관리</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class='content-wrapper mt20'>
                <div class='card-title flex'>
                    <div>점검 항목</div>
                    <div>
                        <input type='button' class='btn' value='점검 추가' onclick='addInspect()' />
                    </div>
                </div>
                <div>
                    <table class='list register' id='inspect'>
                        <thead>
                            <tr>
                                <th>점검 부위</th>
                                <th>점검 항목</th>
                                <th>점검 방법</th>
                                <th>점검 주기</th>
                                <th>점검 기준</th>
                                <th>관리</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class='bottom-btn-group'>
                    <input type='button' class='btn-large orange' id='btnRegister' value='정보 등록' />
                </div>
            </div>
        </form>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', async () => {	  
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

    await getEmployeeList();

    if(uid.value != '') {
        try {
            await getMachine();
            await getSpec();
            await getComponent();
            await getInspect();
        } catch (error) {
            console.error('An error occurred while fetching data:', error);
        }
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
                        location.href = `?controller=machine&action=listMachine`;
                    } else {
                        console.log(data.message);
                    }

                }
            })
            .catch(error => console.log(error));
        }
    }
}

// 직원 가져오기
const getEmployeeList = async () => {  
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getEmployeeList');

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setEmployeeList(data);
    } catch (error) {
        displayError(error);
    }
}

const setEmployeeList = (data) => {    
    if (data && data.data.length > 0) {
        const employee = document.querySelectorAll('.employee'); // 모든 select 요소 선택
        employee.forEach(select => {
            select.innerHTML = ''; // 기존 옵션 제거

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
        });
    }
}


// 재원추가
const addSpec = () => {
    let tr = document.createElement('tr'); // 새로운 tr 요소 생성

    tr.innerHTML = `
        <td><input type='text' name='specName[]' /></td>
        <td><input type='text' name='specValue[]' /></td>
        <td class='center'><input type='button' class='btn' onclick='deleteRow(this)' value='삭제' /></td>
    `;

    document.querySelector('#spec tbody').appendChild(tr); // tbody에 새로운 tr 추가
}

// 부품추가
const addComponent = () => {
    let tr = document.createElement('tr'); // 새로운 tr 요소 생성

    tr.innerHTML = `
        <td><input type='text' name='componentName[]' /></td>
        <td><input type='text' name='componentStandard[]' /></td>
        <td><input type='text' name='componentPurchaseCompany[]' /></td>
        <td><input type='text' name='componentCompanyContact[]' /></td>
        <td><input type='text' name='componentQty[]' /></td>
        <td class='center'><input type='button' class='btn' onclick='deleteRow(this)' value='삭제' /></td>
    `;

    document.querySelector('#component tbody').appendChild(tr); // tbody에 새로운 tr 추가
}

// 점검항목 추가
const addInspect = () => {
    let tr = document.createElement('tr'); // 새로운 tr 요소 생성

    tr.innerHTML = `
        <td><input type='text' name='inspectPart[]' /></td>
        <td><input type='text' name='inspectName[]' /></td>
        <td><input type='text' name='inspectMethod[]' /></td>
        <td><input type='text' name='inspectDate[]' /></td>
        <td><input type='text' name='inspectComment[]' /></td>
        <td class='center'><input type='button' class='btn' onclick='deleteRow(this)' value='삭제' /></td>
    `;

    document.querySelector('#inspect tbody').appendChild(tr); // tbody에 새로운 tr 추가
}

// 설비 가져오기
const getMachine = async () => {  
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getMachine');
    formData.append('uid', uid.value);

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setMachine(data);
    } catch (error) {
        displayError(error);
    }
}

const setMachine = (data) => {    
    // data가 객체인지 확인
    if (data && typeof data === 'object') {
        document.getElementById('name').value = data.name;
        document.getElementById('code').value = data.code;
        document.getElementById('maker').value = data.maker;
        document.getElementById('makerContact').value = data.makerContact;
        document.getElementById('purchaseYear').value = data.purchaseYear;
        document.getElementById('mainOfficer').value = data.mainOfficer;
        document.getElementById('subOfficer').value = data.subOfficer;
        document.getElementById('ratedVoltage').value = data.ratedVoltage;
        if(data.attach != '') document.getElementById('img').innerHTML = `<img src='./attach/${data.attach}' style='width:80px; height:80px' />`;
    } else {
        displayError('No machine data found.');
    }
}


// 재원 가져오기
const getSpec = async () => {  
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getSpec');
    formData.append('uid', uid.value);

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setSpec(data);
    } catch (error) {
        displayError(error);
    }
}

const setSpec = (data) => {    
    if (data && data.length > 0) {
        // 받아온 데이터를 기반으로 option 태그 추가
        data.forEach(item => {
            let tr = document.createElement('tr'); // 새로운 tr 요소 생성

            tr.innerHTML = `
                <td><input type='text' name='specName[]' value='${item.name}' /></td>
                <td><input type='text' name='specValue[]' value='${item.value}' /></td>
                <td class='center'><input type='button' class='btn' onclick='deleteRow(this)' value='삭제' /></td>
            `;

            document.querySelector('#spec tbody').appendChild(tr); // tbody에 새로운 tr 추가
        });        
    }
}

// 부품 가져오기
const getComponent = async () => {  
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getComponent');
    formData.append('uid', uid.value);

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setComponent(data);
    } catch (error) {
        displayError(error);
    }
}

const setComponent = (data) => {    
    if (data && data.length > 0) {
        // 받아온 데이터를 기반으로 option 태그 추가
        data.forEach(item => {
            let tr = document.createElement('tr'); // 새로운 tr 요소 생성

            tr.innerHTML = `
                <td><input type='text' name='componentName[]' value='${item.name}' /></td>
                <td><input type='text' name='componentStandard[]' value='${item.standard}' /></td>
                <td><input type='text' name='componentPurchaseCompany[]' value='${item.purchaseCompany}' /></td>
                <td><input type='text' name='componentCompanyContact[]' value='${item.companyContact}' /></td>
                <td><input type='text' name='componentQty[]' value='${item.qty}' /></td>
                <td class='center'><input type='button' class='btn' onclick='deleteRow(this)' value='삭제' /></td>
            `;

            document.querySelector('#component tbody').appendChild(tr); // tbody에 새로운 tr 추가
        });        
    }
}

// 점검항목 가져오기
const getInspect = async () => {  
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getInspect');
    formData.append('uid', uid.value);

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setInspect(data);
    } catch (error) {
        displayError(error);
    }
}

const setInspect = (data) => {    
    if (data && data.length > 0) {
        // 받아온 데이터를 기반으로 option 태그 추가
        data.forEach(item => {
            let tr = document.createElement('tr'); // 새로운 tr 요소 생성

            tr.innerHTML = `
                <td><input type='text' name='inspectPart[]' value='${item.part}' /></td>
                <td><input type='text' name='inspectName[]' value='${item.name}' /></td>
                <td><input type='text' name='inspectMethod[]' value='${item.method}' /></td>
                <td><input type='text' name='inspectDate[]' value='${item.inspectDate}' /></td>
                <td><input type='text' name='inspectComment[]' value='${item.comment}' /></td>
                <td class='center'><input type='button' class='btn' onclick='deleteRow(this)' value='삭제' /></td>
            `;

            document.querySelector('#inspect tbody').appendChild(tr); // tbody에 새로운 tr 추가
        });        
    }
}
</script>