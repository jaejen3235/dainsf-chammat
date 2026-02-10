<div class='main-container'>
    <div class='title-wrapper'>기기 관리</div>
        <div class='content-wrapper'>
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='agency' />
                <input type='hidden' name='mode' id='mode' value='registerDevice' />
                <input type='hidden' name='uid' id='uid' value="<?=$_GET['uid']?>" />
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
                        <td><i class='bx bx-check'></i> 기기명</td>
                        <td>
                            <input type='text' class='input' name='deviceName' id='deviceName' />
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 모델명</td>
                        <td>
                            <input type='text' class='input' name='model' id='model' />
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 가격</td>
                        <td>
                            <input type='text' class='input comma' name='price' id='price' />
                        </td>
                    </tr>
                    <tr>
                        <td>노출</td>
                        <td>
                            <input type='radio' class='input' name='display' id='display1' value='Y' checked /> 노출
                            <input type='radio' class='input' name='display' id='display2' value='N' /> 숨김
                        </td>
                    </tr>
                    <tr>
                        <td><i class='bx bx-check'></i> 썸네일</td>
                        <td>
                            <div class="upload-grid">
                                <div class="upload-element">
                                    <input type="file" name="thumb1" id="file-1" accept="image/*">
                                    <label for="file-1" id="file-1-preview">
                                        <img src="./assets/images/noimg.jpg" id='img1' alt="">
                                    </label>
                                    <div class='hand' onclick="emptying('file-1')">
                                        <a href='#' onclick="emptying('file-1')" style='color:#fff'>비우기</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='#' onclick="deleteImg('thumb1')" style='color:#fff'>삭제<a>
                                    </div> 
                                </div>
                                <div class="upload-element">
                                    <input type="file" name="thumb2" id="file-2" accept="image/*">
                                    <label for="file-2" id="file-2-preview">
                                        <img src="./assets/images/noimg.jpg" id='img2' alt="">
                                    </label>
                                    <div class='hand' onclick="emptying('file-2')">
                                        <a href='#' onclick="emptying('file-2')" style='color:#fff'>비우기</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='#' onclick="deleteImg('thumb2')" style='color:#fff'>삭제</a>
                                    </div> 
                                </div>
                                <div class="upload-element">
                                    <input type="file" name="thumb3" id="file-3" accept="image/*">
                                    <label for="file-3" id="file-3-preview">
                                        <img src="./assets/images/noimg.jpg" id='img3' alt="">
                                    </label>
                                    <div class='hand' onclick="emptying('file-3')">
                                        <a href='#' onclick="emptying('file-3')" style='color:#fff'>비우기</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='#' onclick="deleteImg('thumb3')" style='color:#fff'>삭제</a>
                                    </div> 
                                </div>
                            </div>
                        </td>
                    </tr>            
                    <tr>
                        <td>상세정보 이미지</td>
                        <td>
                            <div class="upload-grid">
                                <div class="upload-element">
                                    <input type="file" name="content1" id="file-4" accept="image/*">
                                    <label for="file-4" id="file-4-preview">
                                        <img src="./assets/images/noimg.jpg" id='img4' alt="">
                                    </label>
                                    <div class='hand' onclick="emptying('file-4')">
                                        <a href='#' onclick="emptying('file-4')" style='color:#fff'>비우기</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='#' onclick="deleteImg('content1')" style='color:#fff'>삭제</a>
                                    </div> 
                                </div>
                                <div class="upload-element">
                                    <input type="file" name="content2" id="file-5" accept="image/*">
                                    <label for="file-5" id="file-5-preview">
                                        <img src="./assets/images/noimg.jpg" id='img5' alt="">
                                    </label>
                                    <div class='hand'>
                                        <a href='#' onclick="emptying('file-5')" style='color:#fff'>비우기</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='#' onclick="deleteImg('content2')" style='color:#fff'>삭제</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>기타</td>
                        <td>
                            <input type='text' class='input' name='etc' id='etc' />
                        </td>
                    </tr>  
                </table>
            </form>
            <div class='bottom-btn-group'>
                <input type='button' class='btn-large orange' id='btnRegister' value='저장' />&nbsp;
                <input type='button' class='btn-large' id='btnList' value='목록가기' />
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	  
    try {
        const uid = document.getElementById('uid');
        if(uid) {
            if(uid.value != '') getter(uid.value);
        }
    } catch(e) {}

    try {
        const btnRegister = document.getElementById('btnRegister');
        if (btnRegister) {
            btnRegister.addEventListener('click', () => {
                // 배열을 전달하여 함수 호출
                const fieldsArray = [
                    { id: 'deviceName', message: '기기명을 입력하세요', type: 'text' },
                    { id: 'model', message: '모델명을 입력하세요', type: 'text' },
                    { id: 'price', message: '가격을 입력하세요', type: 'text' }
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
        const btnList = document.getElementById('btnList');
        if (btnList) {
            btnList.addEventListener('click', () => {
                location.href = `?controller=agency&action=listDevice`;
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
                        location.href = `?controller=agency&action=listDevice`;
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
    formData.append('mode', 'getDevice');
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
        document.getElementById('deviceName').value = data.deviceName;
        document.getElementById('model').value = data.model;
        document.getElementById('price').value = comma(data.price);
        
        if(data.display === 'Y') document.getElementById('display1').checked = true;
        else document.getElementById('display2').checked = true;

        if(data.thumb1 !== '') {
		document.getElementById('oldThumb1').value = data.thumb1;
            document.getElementById('img1').src = `../attach/device/${data.thumb1}`;
        }

        if(data.thumb2 !== '') {
				document.getElementById('oldThumb2').value = data.thumb2;
            document.getElementById('img2').src = `../attach/device/${data.thumb2}`;
        }

        if(data.thumb3 !== '') {
				document.getElementById('oldThumb3').value = data.thumb3;
            document.getElementById('img3').src = `../attach/device/${data.thumb3}`;
        }

        if(data.content1 !== '') {
				document.getElementById('oldContent1').value = data.content1;
            document.getElementById('img4').src = `../attach/device/${data.content1}`;
        }

        if(data.content2 !== '' && data.content2 !== null ) {            
			document.getElementById('oldContent2').value = data.content2;
            document.getElementById('img5').src = `../attach/device/${data.content2}`;
        } else {            
            document.getElementById('img5').src = `./assets/images/noimg.jpg`;
        }

        document.getElementById('etc').value = data.etc;

    }
}

const displayError = (error) => {
    console.error('Error:', error);
}

// 이미지 업로드 전 미리보기
const previewBeforeUpload = (id) => {
    document.querySelector("#" + id).addEventListener('change', function(e) {
        if(e.target.files.length == 0) {
            return;
        }
        let file = e.target.files[0];
        let url = URL.createObjectURL(file);
        //document.querySelector("#" + id + "-preview div").innerText = file.name;
        document.querySelector("#" + id + "-preview img").src = url;
    })
}

previewBeforeUpload("file-1");
previewBeforeUpload("file-2");
previewBeforeUpload("file-3");
previewBeforeUpload("file-4");
previewBeforeUpload("file-5");

const emptying = (inputId) => {    
    // 파일 업로드 input 요소 초기화
    const inputElement = document.getElementById(inputId);
    inputElement.value = ''; // 파일 업로드 input 값 비우기

    // 이미지 주소 변경
    const previewLabel = document.getElementById(inputId + '-preview');
    const imgElement = previewLabel.querySelector('img');
    imgElement.src = 'https://bit.ly/3ubuq5o'; // 이미지 주소를 원하는 주소로 변경
}

const deleteImg = (fieldName) => {

    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'deleteDeviceImg');
    formData.append('uid', document.getElementById('uid').value);
    formData.append('fieldName', fieldName);

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
                getter(document.getElementById('uid').value);
            } else {
                alert(data.message);
            }
		}
	})
	.catch(error => console.log(error));
}
</script>