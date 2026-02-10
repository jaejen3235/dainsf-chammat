<!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<div class='main-container'>
    <div class='title-wrapper'>공지사항 등록</div>
        <div class='content-wrapper'>
            <form id='frm'>
                <input type='hidden' name='controller' id='controller' value='agency' />
                <input type='hidden' name='mode' id='mode' value='registerNotice' />
                <input type='hidden' name='uid' id='uid' value="<?=$_GET['uid']?>" />
                <input type='hidden' name='oldFile' id='oldFile' />
                
                <table class='register'>
                    <colgroup>
                        <col width='200'>
                        <col />
                        <col width='200'>
                        <col />
                    </colgroup>
                    <tr>
                        <td>제목</td>
                        <td colspan='3'>
                            <input type='text' class='p' data-width='100' name='title' id='title' />
                        </td>
                    </tr>
                    <tr>
                        <td>고정</td>
                        <td>
                            <div class='flex-start'>
                                <label class="custom-checkbox">
                                    <input type="checkbox" name='fixed' id='fixed' value='고정'>
                                    <span class="checkmark"></span>
                                </label>
                                <div>고정</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>첨부파일</td>
                        <td>
                            <input type='file' class='input' name='attach' id='attach' />
                            <span class='help'>&nbsp;(허용되는 파일 : jpg, jpeg, gif, png, zip, xlsx, xls)</span>
                        </td>
                    </tr>
                    </tr>
                    <tr>
                        <td>내용</td>
                        <td>
                            <textarea id="summernote" name="content"></textarea>
                        </td>
                    </tr>
                </table>
            </form>
            <div class='center'>
                <input type='button' class='btn-large orange' id='btnRegister' value='공지사항 등록' />
                <input type='button' class='btn-large' id='btnList' value='목록가기' />
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#summernote').summernote({
        height: 400,
        minHeight: null,
        maxHeight: null,
        focus: true,
        lang: 'ko-KR', // 한국어 설정
        placeholder: '내용을 입력해주세요',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['table', ['table']],
            ['insert', ['link', 'picture',]],
        ],
    });
    // 상품 등록 버튼 클릭 이벤트
    $('#btnRegister').click(function(event) {
        event.preventDefault();
        register();        
    });
    // 목록가기 버튼 클릭 이벤트
    $('#btnList').click(function() {
        location.href = `?controller=agency&action=managementNotice&menu=board`;
    });
});

window.addEventListener('DOMContentLoaded', ()=>{	
    const uid = document.getElementById('uid');
    try {
        if(uid.value != '') {
            btnRegister.value = '공지사항 수정';
            getter(uid.value);
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }
});
const register = () => {
    var content = $('#summernote').summernote('code');

    const frm = document.getElementById('frm');
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
		if(data != '') {
			alert(data.message);
            location.href = `?controller=agency&action=managementNotice&menu=board`;
		}
	})
	.catch(error => console.log(error));
}



const getter = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getNotice');
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
        document.getElementById('title').value = data.title;
        document.getElementById('oldFile').value = data.attach;
        // Summernote에 데이터 삽입
        $('#summernote').summernote('code', data.content);
    }
}

const displayError = (error) => {
    console.error('Error:', error);
}
</script>