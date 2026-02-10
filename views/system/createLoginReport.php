<div class='main-container'>
    <div class='title-wrapper'>로그인 이력 생성</div>
        <div class='content-wrapper'>
            <div>
            <form id='frm'>
                    <input type='hidden' name='controller' value='mes' />
                    <input type='hidden' name='mode' value='createLoginReport' />
                    <table class='view border'>
                        <colgroup>
                            <col width='200' />
                            <col />
                        </colgroup>
                        <tr>
                            <th>Year</th>
                            <td>
                                <select name='year'>
                                    <option value='2024'>2024</option>
                                    <option value='2025'>2025</option>
                                    <option value='2026'>2026</option>
                                    <option value='2027'>2027</option>
                                    <option value='2028'>2028</option>
                                    <option value='2029'>2029</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Month</th>
                            <td>
                                <select name='month'>
                                    <option value='1'>1월</option>
                                    <option value='2'>2월</option>
                                    <option value='3'>3월</option>
                                    <option value='4'>4월</option>
                                    <option value='5'>5월</option>
                                    <option value='6'>6월</option>
                                    <option value='7'>7월</option>
                                    <option value='8'>8월</option>
                                    <option value='9'>9월</option>
                                    <option value='10'>10월</option>
                                    <option value='11'>11월</option>
                                    <option value='12'>12월</option>
                                </select>
                            </td>
                        </tr>                                               
                    </table>
                </form>
            </div>
            <div class='bottom-btn-group'>
                <input type='button' class='btn-large primary' id='btnRegister' value='등록' />
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    try {
        const btnRegister = document.getElementById('btnRegister');
        if(btnRegister) {
            btnRegister.addEventListener('click', register);
        }
    } catch(e) {}
});

const register = () => {
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
		alert(data.message);
	})
	.catch(error => console.log(error));
}
</script>