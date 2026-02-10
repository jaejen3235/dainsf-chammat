<div class='main-container'>
    <div class='title-wrapper'>KPI 지표</div>
        <div class='content-wrapper'>
            <div>
                <form id='frm'>
                    <input type='hidden' name='controller' value='mes' />
                    <input type='hidden' name='mode' value='registerKpiValue' />
                    <input type='hidden' name='uid' id='uid' />

                    <select name='classification' id='classification'>
                        <option value='0'>== 선택 ==</option>
                        <option value='E'>E</option>
                        <option value='P'>P</option>
                    </select>
                    <input type='text' name='indicator' id='indicator' placeholder='핵심성과지표' />
                    <input type='text' name='unit' id='unit' placeholder='단위' />
                    <input type='text' name='pastValue' id='pastValue' placeholder='기준' />
                    <input type='text' name='targetValue' id='targetValue' placeholder='목표' />
                    <input type='button' class='btn-small primary' id='btnRegister' value='등록' />
                </form>
            </div>
            <div class='mt20'>
                <table class='list'>
                    <thead>
                        <tr>
                            <th>분야</th>
                            <th>핵심성과지표</th>
                            <th>단위</th>
                            <th>기존</th>
                            <th>목표</th>
                            <th>현재</th>
                            <th>초과 달성률</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody id='data-body'></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', async () => {	
    getKpiList();

    try {
        const btnRegister = document.getElementById('btnRegister');
        if(btnRegister) {
            btnRegister.addEventListener('click', register);
        }
    } catch(e) {}
});


const register = () => {    
    const frm = document.getElementById('frm');

    if(frm) {
        if(check('frm')) {
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
                    if(data.result == 'success') {
                        alert(data.message);
                        getKpi();
                        clearn();
                    } else {
                        clearn();
                        alert(data.message);
                    }

                }
            })
            .catch(error => console.log(error));
        }
    }
}

// Fetch와 테이블 생성 함수
const getKpiList = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getKpiList');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.list tbody');
        tableBody.innerHTML = generateWorkProgressTable(data);
    } catch (error) {
        console.error('작업지시 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

// 테이블 내용 생성 함수
const generateWorkProgressTable = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='7'>데이터가 없습니다</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.classification}</td>
            <td class='center'>${item.indicator}</td>
            <td class='center'>${item.unit}</td>
            <td class='center'>${item.pastValue}</td>
            <td class='center'>${item.targetValue}</td>
            <td class='center'>${item.currentValue}</td>
            <td class='center'>${item.attainmentRate}%</td>
            <td class='center'>
                <input type='button' class='btn success' value='수정' onclick='getter(${item.uid})' />
                <input type='button' class='btn danger' value='삭제' onclick='deleteKpi(${item.uid})' />
            </td>
        </tr>
    `).join('');
};


// 거래처 가져오기
const getter = async (uid) => {  
    document.getElementById('uid').value = uid;

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getKpi');
    formData.append('uid', uid);

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setter(data);
    } catch (error) {
        displayError(error);
    }
}

const setter = (data) => {
    if (data) {        
        document.getElementById('classification').value = data.classification;
        document.getElementById('indicator').value = data.indicator;
        document.getElementById('unit').value = data.unit;
        document.getElementById('pastValue').value = data.pastValue;
        document.getElementById('targetValue').value = data.targetValue;
    }
}


const deleteKpi = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'mes');
        formData.append('mode', 'deleteKpi');
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
                    getKpiList();
                }
            }
        })
        .catch(error => console.log(error));
    }
}

const clearn = () => {
    document.getElementById('classification').value = 0;
    document.getElementById('indicator').value = '';
    document.getElementById('unit').value = '';
    document.getElementById('pastValue').value = '';
    document.getElementById('targetValue').value = '';
}
</script>
