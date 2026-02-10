<div class="modal" id="modalRegisterMainSlide">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>메인슬라이드 등록</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='agency' />
                <input type='hidden' name='mode' id='mode' value='registerBanner' />
                <input type='hidden' class='input' name='uid' id='uid' value="<?php echo $_GET['uid']; ?>" />
                <input type='hidden' class='input' name='oldFile' id='oldFile' />

                <table class='register'>
                    <colgroup>
                        <col width='100'>
                        <col />
                        <col width='100'>
                        <col />
                    </colgroup>
                    <tr>
                        <td><i class='bx bx-check'></i> 이미지</td>
                        <td>
                            <div id='hiddenDiv' class='hidden'>
                                <img src='' id='bannerImg' style='width:200px; height:80px;' />
                            </div>
                            <div>
                                <input type='file' class='input' name='banner' id='banner' />
                            </div>
                        </td>
                        <td>사용 유무</td>
                        <td>
                            <input type='radio' name='used' id='used1' value='Y' checked>
                            <label for='used1'>사용</label>
                            <input type='radio' name='used' id='used2' value='N'>
                            <label for='used2'>미사용</label>
                        </td>
                    </tr>                    
                </table>
            </form>
            <hr />
            <div class='mt5 help left'>
                <i class='bx bx-check'></i> 은 필수입력 사항입니다
            </div>

            <div class='btn-group'>
                <input type='button' class='modal-btn danger' id='btnRegister' value='저장하기' />&nbsp;
                <input type='button' class='modal-btn' id='btnCloseModal' value='창닫기' />
            </div>
		</div>
	</div>
</div>

<script>
window.addEventListener('DOMContentLoaded', async ()=>{	    
    // 창닫기
    try {
        const btnClose = document.getElementById('btnClose');
        if(btnClose) {
            btnClose.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterMainSlide');
            });
        }
    } catch(e) {}

    // 창닫기
    try {
        const btnCloseModal = document.getElementById('btnCloseModal');
        if(btnCloseModal) {
            btnCloseModal.addEventListener('click', function() {
                clearn();
                closeModal('modalRegisterMainSlide');
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnRegister = document.getElementById('btnRegister');
        if (btnRegister) {
            btnRegister.addEventListener('click', ()=>{
                // 배열을 전달하여 함수 호출
                const fieldsArray = [
                    { id: 'banner', message: '이미지를 등록하세요', type: 'file' }
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

// 등록
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
                        document.getElementById('hiddenDiv').classList.add('hidden');
                        document.getElementById('used1').checked = true;
                        getBannerList();
                        clearn();
                        closeModal('modalRegisterMainSlide');
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
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getBanner');
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
        document.getElementById('uid').value = data.uid;
        document.getElementById('oldFile').value = data.banner;

        if(data.used == 'Y') document.getElementById('used1').checked = true;
        else document.getElementById('used2').checked = true;
        document.getElementById('bannerImg').src = `../attach/banner/${data.banner}`;
        document.getElementById('hiddenDiv').classList.remove('hidden');
        document.getElementById('hiddenDiv').style.marginBottom = '20px';
    }
}

const clearn = () => {
    document.getElementById('hiddenDiv').classList.add('hidden');
    const inputs = document.querySelectorAll('.input'); // input 클래스를 가진 모든 요소 선택
    inputs.forEach(input => input.value = ''); // 각 요소의 값을 빈 문자열로 설정
};
</script>