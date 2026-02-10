<div class="modal" id="modalRegisterDevice">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>협력사 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='agency' />
                <input type='hidden' name='mode' id='mode' value='registerDevice' />
                <input type='hidden' name='uid' id='uid' value="<?php echo $_GET['uid']; ?>" />
                <input type='hidden' name='oldThumb1' id='oldThumb1' />
                <input type='hidden' name='oldThumb2' id='oldThumb2' />
                <input type='hidden' name='oldThumb3' id='oldThumb3' />
                <input type='hidden' name='oldContent1' id='oldContent1' />
                <input type='hidden' name='oldContent2' id='oldContent2' />

                <table class='register'>
                    <colgroup>
                        <col width='200'>
                        <col />
                    </colgroup>
                    <tr>
                        <th>카테고리</th>
                        <td>
                            <select name='category' id='category'>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>기기명</th>
                        <td>
                            <input type='text' class='input' name='deviceName' id='deviceName' />
                        </td>
                    </tr>
                    <tr>
                        <th>모델명</th>
                        <td>
                            <input type='text' class='input' name='model' id='model' />
                        </td>
                    </tr>
                    <tr>
                        <th>가격</th>
                        <td>
                            <input type='text' class='input comma' name='price' id='price' />
                        </td>
                    </tr>
                    <tr>
                        <th>노출</th>
                        <td>
                            <input type='radio' class='input' name='display' id='display1' value='Y' checked /> 노출
                            <input type='radio' class='input' name='display' id='display2' value='N' /> 숨김
                        </td>
                    </tr>
                    <tr>
                        <th>썸네일</th>
                        <td>
                            <div class="upload-grid">
                                <div class="upload-element">
                                    <input type="file" name="thumb1" id="file-1" accept="image/*">
                                    <label for="file-1" id="file-1-preview">
                                        <img src="./assets/images/noimg.jpg" id='img1' alt="">
                                    </label>
                                    <div class='hand' onclick="emptying('file-1')">
                                        <span>비우기</span>
                                    </div> 
                                </div>
                                <div class="upload-element">
                                    <input type="file" name="thumb2" id="file-2" accept="image/*">
                                    <label for="file-2" id="file-2-preview">
                                        <img src="./assets/images/noimg.jpg" id='img2' alt="">
                                    </label>
                                    <div class='hand' onclick="emptying('file-2')">
                                        <span>비우기</span>
                                    </div> 
                                </div>
                                <div class="upload-element">
                                    <input type="file" name="thumb3" id="file-3" accept="image/*">
                                    <label for="file-3" id="file-3-preview">
                                        <img src="./assets/images/noimg.jpg" id='img3' alt="">
                                    </label>
                                    <div class='hand' onclick="emptying('file-3')">
                                        <span>비우기</span>
                                    </div> 
                                </div>
                            </div>
                        </td>
                    </tr>            
                    <tr>
                        <th>상세정보 이미지</th>
                        <td>
                            <div class="upload-grid">
                                <div class="upload-element">
                                    <input type="file" name="content1" id="file-4" accept="image/*">
                                    <label for="file-4" id="file-4-preview">
                                        <img src="./assets/images/noimg.jpg" id='img4' alt="">
                                    </label>
                                    <div class='hand' onclick="emptying('file-4')">
                                        <span>비우기</span>
                                    </div> 
                                </div>
                                <div class="upload-element">
                                    <input type="file" name="content2" id="file-5" accept="image/*">
                                    <label for="file-5" id="file-5-preview">
                                        <img src="./assets/images/noimg.jpg" id='img5' alt="">
                                    </label>
                                    <div class='hand' onclick="emptying('file-5')">
                                        <span>비우기</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>기타</th>
                        <td>
                            <input type='text' class='input' name='etc' id='etc' />
                        </td>
                    </tr>  
                </table>
            </form>
            <hr />
            <div class='help'>
                <i class='bx bx-check'></i> 은 필수입력 사항입니다
            </div>

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
                closeModal('modalRegisterAgency');
            });
        }
    } catch(e) {}

    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterAgency');
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
                    { id: 'deviceName', message: '기기명을 입력하세요', type: 'text' },
                    { id: 'model', message: '모델명을 입력하세요', type: 'text' },
                    { id: 'price', message: '가격을 입력하세요', type: 'text' },
                    { id: 'file-1', message: '썸네일은 최소 하나이상  등록하세요', type: 'file' }
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
});    

// 협력사 등록
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
                        getAgencyList({page:1});
                        clearn();
                        closeModal('modalRegisterAgency');
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

// 협력사 가져오기
const getter = async (uid) => {  
    document.getElementById('uid').value = uid;

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
        document.getElementById('name').value = data.name;
        document.getElementById('code').value = data.code;
        document.getElementById('adminName').value = data.adminName;
        document.getElementById('adminMobile').value = data.adminMobile;
        document.getElementById('loginId').value = data.loginId;
    }
}

const displayError = (error) => {
    console.error('Error:', error);
}

const clearn = () => {
    const inputs = document.querySelectorAll('.input'); // input 클래스를 가진 모든 요소 선택
    inputs.forEach(input => input.value = ''); // 각 요소의 값을 빈 문자열로 설정
};
</script>