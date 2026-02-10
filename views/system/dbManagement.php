<div class='main-container'>
    <div class='title-wrapper'>DB 설정</div>
        <div class='search-wrapper'>
            <div class='search-box'>
            </div>
            <div class='button-box'>
                <input type='button' class='btn-large orange' id='btnInitTest' value='테스트용 초기화' />
            </div>
        </div>
        <div class='content-wrapper'>
            <div>
                <table class='list' id='listTable'>
                    <thead>
                        <tr>
                            <th>테이블명</th>
                            <th>데이터수</th>
                            <th>관리</th>
                            <th>설명</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const btnInitTest = document.getElementById('btnInitTest');

window.addEventListener('DOMContentLoaded', ()=>{
	btnInitTest.addEventListener('click', initTest);
	getTableList();
});

function initTest() {
	if(confirm("테스트 데이터를 초기화 시키시겠습니까?")) {
        const formData = new FormData();
        formData.append('controller', 'mes');
        formData.append('mode', 'initTest');

		fetch('./handler.php', {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(function(data) {
			if(data != '') {
				if(data.status == "success") {
					getTableList();
				}
			} 			
		})
		.catch(error => console.log(error));
	}
}

// 테이블 리스트 가져오기
function getTableList() {
	let tag = "";
	let color = "";

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getTableList');

	fetch('./handler.php', {
		method: 'POST',
		body: formData
	})
	.then(response => response.json())
	.then(function(data) {
		if(data != '') {
			data.forEach(item => {
                if(item.table != 'adminst' && item.table != 'system_admin') {
                    tag += `<tr>`;
                    tag += `<td class='center'>${item.table}</td>`;

                    if(item.cnt > 0) color = 'red';
                    else color = '';

                    tag += `<td class='center'><span class='${color}'>${comma(item.cnt)}</span></td>`;
                    tag += `<td class='center'>`;
                    tag += `<input type='button' class='btn-small soft-danger' onclick="truncate('${item.table}')" value='비우기' />`;
                    tag += `</td>`;
                    tag += `<td class='center'>${item.comment}</td>`;
                    tag += `</tr>`;
                }
			});
		} else {
			tag = `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td>`;
		}

		document.querySelector('#listTable tbody').innerHTML = tag;
	})
	.catch(error => console.log(error));
}

function truncate(tb) {
	if(confirm(`${tb} 테이블의 데이터를 초기화 시키시겠습니까?`)) {
        const formData = new FormData();
        formData.append('controller', 'mes');
        formData.append('mode', 'truncateTable');
        formData.append('table', tb);

		fetch('./handler.php', {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(function(data) {
			if(data != null) {
				if(data.status == 'success') getTableList();
			}
		})
		.catch(error => console.log(error));
	}
}    
</script>