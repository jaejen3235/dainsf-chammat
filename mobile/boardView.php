<?php
include "head.php";
?>
    
<main>
    <div class="title-container">
        <div class="title-wrapper">
            <div class="title">공지사항</div>            
            <div class="summary">여러분들을 환영합니다.</div>
        </div>
    </div>
    <div class="board-container">
        <div class="board-wrapper">
            <div class="title-section">
                <div class="view-title"></div>
                <div class="view-date"></div>
            </div>
            <div class="view-section"></div>
            <div class="btn-section center">
                <input type="button" class="btn" id='btnList' value="목록가기" />
            </div>
        </div>
    </div>
</main>

<input type='hidden' id='uid' value="<?=$_GET['uid']?>" />
<input type='hidden' id='page' value="<?=$_GET['page']?>" />

<?php
include "foot.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    const uid = document.getElementById('uid');
    getData(uid.value);

    try {
        const btnList = document.getElementById('btnList');
        const page = document.getElementById('page');

        btnList.addEventListener('click', function() {
            location.href = `boardList.php?page=${page.value}`;
        });
    } catch(e) {}
});



const getData = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getNotice');
    formData.append('uid', uid);

    try {
        const response = await fetch('./webadm/handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setData(data);
    } catch (error) {
        displayError(error);
    }
}

const setData = (data) => {
    if (data) {        
        document.querySelector('.view-title').innerText = data.title;
        document.querySelector('.view-date').innerText = data.date;
        document.querySelector('.view-section').innerHTML = data.content;
    }
}
</script>