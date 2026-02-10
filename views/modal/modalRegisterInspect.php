<div class="modal" id="modalRegisterInspect">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>도면 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' value='mes' />
                <input type='hidden' name='mode' value='registerMachineInspect' />
                <input type='hidden' name='uid' id='uid' />

                <div>
                    <table class='view'>
                        <thead>
                            <colgroup>
                                <col width='12%' />
                                <col width='12%' />
                                <col width='12%' />
                                <col width='12%' />
                                <col width='12%' />
                                <col width='12%' />
                                <col width='12%' />
                                <col width='12%' />
                            </colgroup>
                            <tr>
                                <th class='th-grey'>설비명</th>
                                <td><span id='name'></span></td>
                                <th class='th-grey'>관리번호</th>
                                <td><span id='code'></span></td>
                                <th class='th-grey'>점검일</th>
                                <td>
                                    <input type='text' class='input datepicker' name='inspectDate' id='inspectDate' value='<?php echo date('Y-m-d'); ?>' />
                                </td>
                                <th class='th-grey'>점검자</th>
                                <td>
                                    <select name='employee' id='employee'>
                                    </select>
                                </td>
                            </tr>
                        </thead>
                    </table>
                </div>
                <hr />
                <div>
                    <table class='inspect-list list'>
                        <thead>
                            <tr>
                                <th>점검부위</th>
                                <th>점검항목</th>
                                <th>점검방법</th>
                                <th>점검기준</th>
                                <th>점검결과</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <hr />
                <div class='help'>
                    ※ <i class='bx bx-check'></i> 은 필수입력 사항입니다
                </div>
            </form>

            <div class='btn-group'>
                <input type='button' class='modal-btn danger' id='btnRegister' value='저장' />&nbsp;
                <input type='button' class='modal-btn' id='btnCloseModal' value='취소' />
            </div>
		</div>
	</div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    try {
        const uid = document.getElementById('uid');
    } catch(e) {}

    // 창닫기
    try {
        const btnClose = document.getElementById('btnClose');
        if(btnClose) {
            btnClose.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterInspect');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterInspect');
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnRegister = document.getElementById('btnRegister');
        if (btnRegister) {            
            btnRegister.addEventListener('click', () => {
                // 배열을 전달하여 함수 호출
                const fieldsArray = [                 
                    { id: 'inspectDate', message: '점검일자를 선택하세요', type: 'select' },
                    { id: 'employee', message: '점검자를 선택하세요', type: 'select' }
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

    getSelectList('getEmployeeList', 'uid', 'name', '#employee');
});    

// 점검 등록
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
                        getMachineList({page:1});
                        clearn();
                        closeModal('modalRegisterInspect');
                    } else {
                        clearn();
                        alert(data.message);
                    }

                }
            })
            .catch(error => console.log(error));
        }
    }
}

const getter = async (uid) => {
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getMachine');
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
        document.getElementById('name').innerText = data.name;
        document.getElementById('code').innerText = data.code;

        getInspect({page:1});
    }
}

const getInspect = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getInspect');    
    formData.append('uid', document.getElementById('uid').value);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.inspect-list tbody');
        tableBody.innerHTML = generateTableContent2(data);
    } catch (error) {
        console.error('점검 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent2 = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.map(item => `
        <tr>
            <td class='center'><input type='text' class='nonBorder center' name='inspectPart[]' value='${item.part}' readonly /></td>
            <td class='center'><input type='text' class='nonBorder center' name='inspectName[]' value='${item.name}' readonly /></td>
            <td class='center'><input type='text' class='nonBorder center' name='inspectMethod[]' value='${item.method}' readonly /></td>
            <td class='center'><input type='text' class='nonBorder center' name='inspectComment[]' value='${item.comment}' readonly /></td>
            <td class='center'>
            	<select name='inspectResult[]'>
                    <option value='0'>== 선택 ==</option>
				    <option value='정상'>정상</option>
				    <option value='비정상'>비정상</option>
				</select>
            </td>
        </tr>
    `).join('');
};

const clearn = () => {
    const inputs = document.querySelectorAll('.input'); // input 클래스를 가진 모든 요소 선택
    inputs.forEach(input => input.value = ''); // 각 요소의 값을 빈 문자열로 설정
};
</script>