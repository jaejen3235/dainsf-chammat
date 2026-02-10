<div class="main-container">
    <div class="page-title"><i class='bx bxs-food-menu'></i> 설비정보 관리</div> 
    <div class="kpi-grid">
        <div class="kpi-card kpi-total">
            <div class="kpi-title">총 등록 설비 수 (EA)</div>
            <div class="kpi-value">120</div>
        </div>
        <div class="kpi-card kpi-operating">
            <div class="kpi-title">운영 중 설비 수 (EA)</div>
            <div class="kpi-value">110</div>
        </div>
        <div class="kpi-card kpi-critical">
            <div class="kpi-title">긴급 정지/점검 설비 (EA)</div>
            <div class="kpi-value">3</div>
        </div>
        <div class="kpi-card kpi-pm">
            <div class="kpi-title">금월 $\text{PM}$ 예정 건수</div>
            <div class="kpi-value">15</div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="flex">
            <div class="title red">설비 목록 (전체 자산)</div>
            <input type="button" class="btn-large success" id="btnOpenRegistModal" value="+ 신규 설비 등록" />
        </div>
            
        <table class="list mt10">
            <thead>
                <tr>
                    <th>설비 ID</th>
                    <th>설비명</th>
                    <th>생산 라인</th>
                    <th>Model No.</th>
                    <th>운영 상태</th>
                    <th>최종 점검일</th>
                    <th>상세</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>EQ-A101</td>
                    <td>CNC Milling</td>
                    <td>LINE A</td>
                    <td>NX-5000</td>
                    <td><span class="status-badge status-operating-color">운영 중</span></td>
                    <td>2025.11.01</td>
                    <td><button class="btn-small primary">상세 보기</button></td>
                </tr>
                <tr>
                    <td>EQ-B205</td>
                    <td>Laser Welder</td>
                    <td>LINE B</td>
                    <td>LW-200</td>
                    <td><span class="status-badge status-critical-color">긴급 정지</span></td>
                    <td>2025.11.11</td>
                    <td><button class="btn-small danger">조치 필요</button></td>
                </tr>
                <tr>
                    <td>EQ-A102</td>
                    <td>Robot Arm</td>
                    <td>LINE A</td>
                    <td>RA-300</td>
                    <td><span class="status-badge status-inspection-color">정기 점검</span></td>
                    <td>2025.10.15</td>
                    <td><button class="btn-small warning">상세 보기</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
include "./views/modal/registMachine.php";
?>

<script>
window.addEventListener('DOMContentLoaded', ()=>{
    try {
        const btnOpenRegistModal = document.getElementById('btnOpenRegistModal');
        if(btnOpenRegistModal) {
            btnOpenRegistModal.addEventListener('click', function() {
                openModal('registMachine', 900, 500);
            });
        }
    } catch(e) {}

    getMachineList({page:1});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getMachineList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getMachineList = async ({
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
                where += ` and (machine_id like '%${searchText.value}%' or machine_name like '%${searchText.value}%')`;
            }
        }
    } catch(e) {}
    

    const formData = new FormData();
    formData.append('controller', CONTROLLER);
    formData.append('mode', MODE);
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

        const tableBody = document.querySelector('.list tbody');
        tableBody.innerHTML = generateTableContent(data);

        getPaging('mes_machine', 'uid', where, page, per, block, 'getMachineList');
    } catch (error) {
        console.error('거래처 데이터를 가져오는 중 오류가 발생했습니다:', error);
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
            <td class='center'>${item.machine_id}</td>
            <td class='center'>${item.machine_name}</td>
            <td class='center'>${item.production_line}</td>
            <td class='center'>${item.model_no}</td>
            <td class='center'>${item.introduction_date}</td>
            <td class='center'>${item.iot_sensor_id}</td>
            <td class='center'>${item.purchase_company}</td>
            <td class='center'>${item.contact_person}</td>
            <td class='center'>
                <input type='button' class='btn-small grey' value='수정' onclick='modifyMachine(${item.uid})' />
                <input type='button' class='btn-small danger' value='삭제' onclick='deleteMachine(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const modifyMachine = (uid) => {
    getterMachine(uid);
    openModal('registMachine', 900, 580);
}

const deleteMachine = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'mes');
        formData.append('mode', 'deleteMachine');
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
                    getMachineList({page:1});
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
        formData.append('table', 'mes_account');

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
				getMachineList({page:1});
			}            
		}).catch(error => console.log(error));
	}
}

// 새로고침
const revision = () => {
    document.getElementById('searchText').value = '';
    getMachineList({page:1});
}
</script>