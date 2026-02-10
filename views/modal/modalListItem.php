<div class="modal" id="modalListItem">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>품목 선택</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <div class='search-box flex-start'>
                <div class='search-section'>
                    <input type="text" id='searchText' placeholder="검색">
                    <i class="fa fa-search hands" aria-hidden="true"></i>
                </div>
                <button class='btn-small success revision' id='btnRevision'><i class='bx bx-revision'></i></button>
            </div>
            <div>
                <table class='list modal-list'>
                    <colgroup>
                        <col width='100' />
                        <col />
                        <col width='200' />
                        <col width='200' />
                        <col width='150' />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>구분</th>
                            <th>품목명</th>
                            <th>품목코드</th>
                            <th>품목규격</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="paging-area mt20"></div>
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
                closeModal('modalListItem');
            });
        }
    } catch(e) {}
});

const getItemList = async ({
    page,
    per = 5,
    block = 4,
    orderBy = 'uid',
    order = 'desc'
}) => {    
    let where = `where classification!='완제품'`;

    // 검색어가 있다면
    /*
    try {
        const searchText = document.getElementById('searchText');
        if(searchText) {
            if(searchText.value != '') {
                where += ` and (name like '%${searchText.value}%' or code like '%${searchText.value}%')`;
            }
        }
    } catch(e) {}
     */
    

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getItemList');
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('orderby', orderBy);
    formData.append('asc', order);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.modal-list tbody');
        tableBody.innerHTML = generateTableContent(data);

        getPaging('mes_items', 'uid', where, page, per, block, 'getItemList');
    } catch (error) {
        console.error('품목 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.classification}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${item.item_code}</td>
            <td class='center'>${item.standard}</td>
            <td class='center'>
                <input type='button' class='btn-small grey' value='선택' onclick="choiceItem(${item.uid}, '${item.classification}', '${item.item_name}', '${item.item_code}', '${item.standard}', '${item.unit}')" />
            </td>
        </tr>
    `).join('');
};

const choiceItem = (uid, classification, name, code, standard, unit) => {
    // name 앞에 depth 만큼 공백으로 채워서...
    
    let space = '';
    for(let i = 0 ; i <= (depth + 1) * 5 ; i++) { // depth 에 1을 더하는 까닭은 상위 품목의 depth 보다 더 깊게 들어가야 하기 때문이다.
        space += '&nbsp;'
    }
    space += `<i class='bx bx-subdirectory-right'></i> `;
    
    const tag = `
        <tr>
            <td class='center'>${classification}</td>
            <td class='left'>${space} ${name}</td>
            <td class='center'>${code}</td>
            <td class='center'>${standard}</td>
            <td class='center'>${unit}</td>
            <td class='center'><input type='text' class='w100' /></td>
            <td class='center'>
                <input type='button' class='btn primary' value='등록' onclick="addBom(${uid}, this)" />                
                <input type='button' class='btn danger' value='삭제' onclick="deleteRow(${uid}, this)" />                
            </td>
        </tr>
    `;

    document.querySelector('.register tbody').insertAdjacentHTML('beforeend', tag);
    addMode = true;
    closeModal('modalListItem');
};
</script>