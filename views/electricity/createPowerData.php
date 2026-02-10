<div class='main-container'>
    <div class='title-wrapper'>전력데이터 생성</div>
        <div class='content-wrapper'>
            <div>
                <form id='frm'>
                    <input type='hidden' name='controller' value='electricity' />
                    <input type='hidden' name='mode' value='registerPowerData' />
                    <table class='view border'>
                        <colgroup>
                            <col width='200' />
                            <col />
                        </colgroup>
                        <tr>
                            <th>Year</th>
                            <td>
                                <input type='text' name='year' value='2024' />
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
                        <tr>
                            <th>Day</th>
                            <td>
                                <select name='day'>
                                    <option value='1'>1일</option>
                                    <option value='2'>2일</option>
                                    <option value='3'>3일</option>
                                    <option value='4'>4일</option>
                                    <option value='5'>5일</option>
                                    <option value='6'>6일</option>
                                    <option value='7'>7일</option>
                                    <option value='8'>8일</option>
                                    <option value='9'>9일</option>
                                    <option value='10'>10일</option>
                                    <option value='11'>11일</option>
                                    <option value='12'>12일</option>
                                    <option value='13'>13일</option>
                                    <option value='14'>14일</option>
                                    <option value='15'>15일</option>
                                    <option value='16'>16일</option>
                                    <option value='17'>17일</option>
                                    <option value='18'>18일</option>
                                    <option value='19'>19일</option>
                                    <option value='20'>20일</option>
                                    <option value='21'>21일</option>
                                    <option value='22'>22일</option>
                                    <option value='23'>23일</option>
                                    <option value='24'>24일</option>
                                    <option value='25'>25일</option>
                                    <option value='26'>26일</option>
                                    <option value='27'>27일</option>
                                    <option value='28'>28일</option>
                                    <option value='29'>29일</option>
                                    <option value='30'>30일</option>
                                    <option value='31'>31일</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Machine</th>
                            <td>
                                <select name='machine'>
                                    <option value='M001'>M001</option>
                                    <option value='M002'>M002</option>
                                    <option value='M003'>M003</option>
                                    <option value='M004'>M004</option>
                                    <option value='M005'>M005</option>
                                    <option value='M006'>M006</option>
                                    <option value='M007'>M007</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Hour</th>
                            <td>
                                <select name='hour'>
                                    <option value='9'>9시</option>
                                    <option value='10'>10시</option>
                                    <option value='11'>11시</option>                                    
                                    <option value='13'>13시</option>
                                    <option value='14'>14시</option>
                                    <option value='15'>15시</option>
                                    <option value='16'>16시</option>
                                    <option value='17'>17시</option>                                    
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Power</th>
                            <td>
                                <input text='text' name='power' id='power' />
                            </td>
                        </tr>
                        <tr>
                            <th>Price</th>
                            <td>
                                <input text='text' name='Price' id='price' />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class='mt30 center'>
                <input type='button' class='btn-large danger' id='btnRegister' value='등록' />
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
		if(data != null || data != '') {
			data.forEach(item => {
                tag += `<option value='${item.uid}'>${item.name}</option>`;
            });
		}
	})
	.catch(error => console.log(error));
}
</script>