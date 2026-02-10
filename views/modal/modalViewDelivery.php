<div class="modal" id="modalViewDelivery">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>납품 목록</span>
			<span class="btn-close" id="btnClose2"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <table class='list modal-list'>
                <colgroup>
                    <col width='150' />
                    <col width='150'/>
                    <col width='150' />
                    <col width='200' />
                    <col width='100' />
                    <col width='100' />
                    <col width='100' />
                    <col width='100' />
                </colgroup>
                <thead>
                    <tr>
                        <th>납품일</th>
                        <th>거래처</th>
                        <th>그룹</th>
                        <th>품명</th>
                        <th>성별</th>
                        <th>사이즈</th>
                        <th>색상</th>
                        <th>납품수량</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>        
		</div>
	</div>
</div>

<script>
window.addEventListener('DOMContentLoaded', async ()=>{	
    // 창닫기
    try {
        const btnClose2 = document.getElementById('btnClose2');
        if(btnClose2) {
            btnClose2.addEventListener('click', function() {                
                closeModal('modalViewDelivery');
            });
        }
    } catch(e) {}
});


const getDeliveryList = async (uid) => {    
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDeliveryList');
    formData.append('delivery_uid', uid);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.modal-list tbody');
        tableBody.innerHTML = setDeliveryList(data);        
    } catch (error) {
        console.error('납품 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const setDeliveryList = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    // account_name을 한 번만 갱신하도록 수정
    if (data.data.length > 0 && document.getElementById('account_name')) {
        document.getElementById('account_name').innerHTML = data.data[0].account_name;
    }

    let totalQty = 0;
    const rows = data.data.map(item => {
        totalQty += Number(item.delivery_qty) || 0;
        return `
            <tr>
                <td class='center'>${item.delivery_date}</td>
                <td class='center'>${item.account_name}</td>            
                <td class='center'>${item.group_name}</td>
                <td class='center'>${item.item_name}</td>
                <td class='center'>${item.gender}</td>
                <td class='center'>${comma(item.size_name)}</td>
                <td class='center'>${comma(item.color_name)}</td>
                <td class='center'>${comma(item.delivery_qty)}</td>
            </tr>
        `;
    }).join('');

    // 합계 행 추가
    const totalRow = `
        <tr style="font-weight:bold; background:#f9f9f9;">
            <td class='center' colspan='7'>합계</td>
            <td class='center'>${comma(totalQty)}</td>
        </tr>
    `;

    return rows + totalRow;
};
</script>