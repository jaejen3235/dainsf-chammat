<div class='main-container'>
    <div class='content-wrapper'>
        <style>
            .order-line {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
            }

            .order-left {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .order-right {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .order-label {
                font-size: 18px;
                font-weight: 600;
            }
        </style>
        <form id='frm'>
            <input type='hidden' name='controller' id='controller' value='mes' />
            <input type='hidden' name='mode' id='mode' value='registerOrders' />
            <input type='hidden' class='input' name='uid' id='uid' value="<?php echo $_GET['uid']; ?>" />

            <div>기본정보</div>
            <table class='register'>
                <colgroup>
                    <col width='150'>
                    <col width='426'>
                    <col width='150'>
                    <col width='426'>
                </colgroup>
                <tr>
                    <th><i class='bx bx-check'></i> 거래처</th>
                    <td colspan='3'>
                        <select name='account' id='account'>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><i class='bx bx-check'></i> 수주일</th>
                    <td>
                        <input type='text' class='input datepicker' name='order_date' id='order_date' />
                    </td>
                    <th><i class='bx bx-check'></i> 납기일</th>
                    <td>
                        <input type='text' class='input datepicker' name='shipment_date' id='shipment_date' />
                    </td>
                </tr>
                <tr>
                    <th>납품 장소</th>
                    <td colspan='3'>
                        <input type='text' class='input' name='shipment_place' id='shipment_place' />
                    </td>
                </tr>
                <tr>
                    <th>메모</th>
                    <td colspan='3'>
                        <input type='text' class='input' name='memo' id='memo' />
                    </td>
                </tr>                    
            </table>


            <div class='mt20 order-line'>
                <div class='order-left'>
                    <span class='order-label'>주문 내역</span>
                    <input type='button' class='btn-large orange' id='btnOpenModal' value='품목추가' />
                </div>
                <div class='order-right'>
                    <input type='button' class='btn-large primary' id='btnRegister' value='주문 등록' />
                    <input type='button' class='btn-large' id='btnExit' value='나가기' />
                </div>
            </div>

            <table class='list mt20'>
                <colgroup>
                    <col />
                    <col />
                    <col />
                    <col />
                    <col />
                    <col />
                    <col />
                </colgroup>
                <thead>
                    <tr>
                        <th>품명</th>
                        <th>품번</th>
                        <th>규격</th>
                        <th>단위</th>
                        <th>수량</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            
        </form>
    </div>
</div>

<?php
include "./views/modal/modalAddItem.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    const uid = document.getElementById('uid');
    // 모달열기
    try {
        const btnOpenModal = document.getElementById('btnOpenModal');
        if(btnOpenModal) {
            btnOpenModal.addEventListener('click', function() {
                openModal('modalAddItem', 1200, 380);
            });
        }
    } catch(e) {}

    // 등록
    try {
        const btnRegister = document.getElementById('btnRegister');
        if(btnRegister) {
            btnRegister.addEventListener('click', function() {
                register();
            });
        }
    } catch(e) {}

    // 나가기
    try {
        const btnExit = document.getElementById('btnExit');
        if (btnExit) {
            btnExit.addEventListener('click', function() {
                location.href = `?controller=sales&action=listOrder`;
            });
        }
    } catch(e) {}

    getSelectList('getAllAccountList', 'uid', 'name', '#account');    

    //alert(localStorage.getItem('id'));

    if(uid.value != '') {
        getOrders(uid.value);
    }
});


const register = () => {    
    const frm = document.getElementById('frm');

    if(frm) {
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
                alert(data.message);
                clean();
                if(data.result == 'success') {
                    location.href = `?controller=sales&action=listOrder`;
                }
            }
        })
        .catch(error => console.log(error));
    }
}

const getOrders = (uid) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getOrders');
    formData.append('uid', uid);

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
            document.getElementById('account').value = data.account_uid;
            document.getElementById('order_date').value = data.order_date;
            document.getElementById('shipment_date').value = data.shipment_date;
            document.getElementById('shipment_place').value = data.shipment_place;
            document.getElementById('memo').value = data.memo;

            getOrderItemsList(uid);
        }
    })
}

const getOrderItemsList = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getAllOrdersItem');
    formData.append('uid', uid);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.list tbody');
        tableBody.innerHTML = generateTableContent(data);
    } catch (error) {
        console.error('주문품목 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
}

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>
                ${item.item_name}
            </td>
            <td class='center'>
                ${item.item_code}
            </td>
            <td class='center'>
                ${item.standard}
            </td>
            <td class='center'>
                ${item.unit}
            </td>
            <td class='center'>
                ${comma(item.qty)}
            </td>
            <td class='center'>
                <input type='button' class='btn-small danger' value='삭제' onclick='deleteOrderItem(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const deleteOrderItem = (uid) => {
    if(!confirm('정말 삭제하시겠습니까?')) {
        return;
    }

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'deleteOrderItem');
    formData.append('uid', uid);

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
                getOrderItemsList(document.getElementById('uid').value);
            }

            alert(data.message);
        }
    })
    .catch(error => console.log(error));    
}
</script>
