<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 수주 정보 관리</div> 
    <div class='search-wrapper'>
        <div class='search-box'>
            <div class='search-section'>
                <div class='search-input'>
                    <input type='text' class='datepicker' id='start_date' placeholder='검색일' />
                    <input type='text' class='datepicker' id='end_date' placeholder='검색일' />
                    <input type="text" id='searchText' placeholder="거래처명">
                    <button class='btn-large primary' id='btnSearch'>검색</button>
                    <button class='btn-large success revision' id='btnRevision'>새로고침</button>
                    <button class='btn-large' id='btnExcelDownload'>엑셀 다운로드</button>
                </div>
            </div>
            <div class='button-box'>
                <input type='button' class='btn-large orange' id='btnMovePage' value='주문 등록' />
                <input type='button' class='btn-large' id='btnDeleteSelected' value='선택 삭제' />
            </div>
        </div>
    </div>
    <div class='content-wrapper'>
        <div>
            <table class='list'>
                <colgroup>                      
                    <col width='50' />
                    <col />
                    <col />
                    <col />
                    <col />
                    <col />
                    <col />
                </colgroup>
                <thead>
                    <tr>
                        <th>
                            <label class="custom-checkbox">
                                <input type="checkbox" id='chkAll'>
                                <span class="checkmark"></span>
                            </label>
                        </th>
                        <th>거래처</th>
                        <th>주문품목</th>
                        <th>수주일</th>
                        <th>납기일</th>
                        <th>상태</th>                            
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="paging-area mt20"></div>
    </div>
</div>


<script>
window.addEventListener('DOMContentLoaded', ()=>{
    // 검색
    try {
        const btnSearch = document.getElementById('btnSearch');
        if(btnSearch) {
            btnSearch.addEventListener('click', () => {
                getOrdersList({page : 1});
            });
        }
    } catch(e) {}

    // 체크박스
    try{
		const chkAll = document.getElementById('chkAll');
		chkAll.addEventListener('click', ()=>{
			if(chkAll.checked) checkAll('chk');
			else checkAllDisolve('chk');
		});
	} catch(e) {}

    // 모달열기
    try {
        const btnMovePage = document.getElementById('btnMovePage');
        if(btnMovePage) {
            btnMovePage.addEventListener('click', function() {
                movePage('sales', 'registerOrder');
            });
        }
    } catch(e) {}

    try {
        const btnRevision = document.getElementById('btnRevision');
        if(btnRevision) {
            btnRevision.addEventListener('click', function() {
                revision();
            });
        }
    } catch(e) {}

    try {
        const btnExcelDownload = document.getElementById('btnExcelDownload');
        if (btnExcelDownload) {
            btnExcelDownload.addEventListener('click', downloadOrdersExcel);
        }
    } catch(e) {}

    // 선택삭제
    try {
        const btnDeleteSelected = document.getElementById('btnDeleteSelected');
        if(btnDeleteSelected) {
            btnDeleteSelected.addEventListener('click', deleteSelected);
        }
    } catch(e) {}

    try {
        document.getElementById('searchText').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {  // Enter 키를 감지
                getOrdersList({page:1});
            }
        });
    } catch(e) {}
    
    getOrdersList({page : 1});

    try {
        const searchType = document.getElementById('searchType');
        searchType.addEventListener('change', function() {
            getOrdersList({page:1});
        });
    } catch(e) {}

    //alert(localStorage.getItem('id'));
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getOrdersList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getOrdersList = async ({
    page,
    per = 15,
    block = 4,
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {    
    let where = `where 1=1`;
    // 검색어가 있다면
    try {
        const searchText = document.getElementById('searchText');
        if(searchText) {
            if(searchText.value != '') {
                where += ` and account_name like '%${searchText.value}%'`;
            }
        }
    } catch(e) {
        console.log(e);
    }

    const formData = new FormData();
    formData.append('controller', CONTROLLER);
    formData.append('mode', MODE);
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('orderby', orderBy);
    formData.append('asc', order);
    console.log(formData);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.list tbody');
        tableBody.innerHTML = generateTableContent(data);

        getPaging('mes_orders', 'uid', where, page, per, block, 'getOrdersList');
    } catch (error) {
        console.error('수주 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>
                <label class="custom-checkbox">
                    <input type="checkbox" class='chk' value='${item.uid}'>
                    <span class="checkmark"></span>
                </label>
            </td>
            <td class='center'>${item.account_name}</td>
            
            <td class='center'>${item.items}</td>
            <td class='center'>${item.order_date}</td>
            <td class='center'>${item.shipment_date}</td>
            <td class='center'>${item.status}</td>
            <td class='center'>
                <input type='button' class='btn-small grey' value='내역&수정' onclick='modifyOrders(${item.uid})' />
                <input type='button' class='btn-small danger' value='삭제' onclick='deleteOrders(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const modifyOrders = (uid) => {
    movePage('sales', 'registerOrder', uid);
}

const deleteOrders = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'mes');
        formData.append('mode', 'deleteOrders');
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
                alert(data.message);

                if(data.result == 'success') {
                    getOrdersList({page:1});
                }
            }
        })
        .catch(error => console.log(error));
    }
}


// 선택삭제
const deleteSelected = () => {
    let uids = '';
	document.querySelectorAll('.chk').forEach((elem, index) => {
		if(elem.checked) {
			uids += elem.value + ",";
		}
	});

    if(uids == '') {
        alert('삭제하실 데이터를 선택하세요');
        return false;
    }

	if(confirm("선택하신 DATA를 삭제하시겠습니까? 삭제 후에는 복구가 불가능합니다")) {
		const formData = new FormData();
        formData.append('controller', 'functions');
        formData.append('mode', 'deleteSelected');
        formData.append('uids', uids);
        formData.append('table', 'mes_orders');

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
			if(data.result == "success") {
				chkAll.checked = false;
				getOrdersList({page:1});
			}            
		}).catch(error => console.log(error));
	}
}

// 새로고침
const revision = () => {
    document.getElementById('searchText').value = '';
    document.getElementById('searchType').value = '0';
    getOrdersList({page:1});
}

const downloadOrdersExcel = () => {
    let where = `where 1=1`;

    // 검색어가 있다면
    try {
        const searchText = document.getElementById('searchText');
        if(searchText && searchText.value != '') {
            where += ` and account_name like '%${searchText.value}%'`;
        }
    } catch(e) {
        console.log(e);
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = './handler.php';
    form.target = '_blank';
    form.style.display = 'none';

    const fields = {
        controller: 'mes',
        mode: 'getOrdersListExcel',
        where
    };

    Object.entries(fields).forEach(([key, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
    form.remove();
};
</script>
