<?php
include "head.php";
?>
    
<main>
    <div class="title-container">
        <div class="title-wrapper">
            <div class="title"></div>            
            <div class="summary">여러분들을 환영합니다.</div>
        </div>
    </div>
    <div class="terms-container">
        <div class="terms-wrapper">
            <div class="terms-section">
            </div>
        </div>
    </div>
</main>
<input type="hidden" id="div" value="<?=$_GET['div']?>" />

<?php
include "foot.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    getData();
});

const getData = async () => {
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getPolicy');
    formData.append('div', document.getElementById('div').value);

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
        const div = document.getElementById('div');
        const title = document.querySelector('.title');

        switch(div.value) {
            case "1" : title.innerText = '개인정보처리방침'; break;
            case "2" : title.innerText = '이용약관'; break;
            case "3" : title.innerText = '이메일무단수집거부'; break;
        }
        document.querySelector('.terms-section').innerText = data.policy;
    }
}
</script>