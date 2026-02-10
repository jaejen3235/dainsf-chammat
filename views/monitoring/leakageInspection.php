<div class='main-container'>
    <div class='content-wrapper'>
        <div>
            <div class="kpi-summary">
                <div class="kpi-card">
                    <h3>현재 검사 상태</h3>
                    <p id="currentInspecting" class="kpi-value">검사중</p>
                </div>
                <div class="kpi-card">
                    <h3>전체 누적 검사 수량</h3>
                    <p id="totalInspected" class="kpi-value">로딩 중...</p>
                </div>
                <div class="kpi-card">
                    <h3>누전검사 누적 불합격</h3>
                    <p id="failedCount" class="kpi-value">로딩 중...</p>
                </div>                
                <div class="kpi-card">
                    <h3>합격률</h3>
                    <p id="passRate" class="kpi-value">로딩 중...</p>
                </div>
            </div>
        </div>

        <div>
            <div class="table-section">
                <div class="flex">
                    <div class="title red">누전검사 실시간 현황</div>
                </div>
                <table class="list">
                    <thead>
                        <tr>
                            <th>설비명</th>
                            <th>데이터 타입</th>
                            <th>값</th>
                            <th>수집일시</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    getLeakageInspection({page:1});
    // 5초마다 자동 갱신
    setInterval(getLeakageInspection({page:1}), 5000);
});



const getLeakageInspection = async ({
    page,
    per = 15,
    block = 4,
    orderBy = 'uid',
    order = 'desc'
}) => {    
    let where = `where machine='goods_counter'`;    

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getLeakageInspection');
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

        //getPaging('mes_account', 'uid', where, page, per, block, 'getAccountList');
    } catch (error) {
        console.error('거래처 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.data.map(item => {

        // 이게 맞아?
        // item.datatype이 실제 데이터 구조에 존재하는지 확인 필요
        // 만약 item.data_type 등이 실제 값이라면 아래처럼 써야 할 수 있음
        // (아래 코드는 기존 코멘트에 따라 일단 구조를 검증하지 않고 그대로 둠)
        // data_type이 ok_tot_count, ng_tot_count일 때만 KPI 갱신
        let totalOK = 0;
        let totalNG = 0;

        if (item.data_type === 'ok_tot_count') {
            totalOK = Number(item.value) || 0;
            document.getElementById('totalInspected').innerText = (totalOK + (Number(document.getElementById('failedCount').innerText) || 0)).toLocaleString();
        } else if (item.data_type === 'ng_tot_count') {
            totalNG = Number(item.value) || 0;
            document.getElementById('failedCount').innerText = totalNG.toLocaleString();
            document.getElementById('totalInspected').innerText = ((Number(document.getElementById('totalInspected').innerText.replace(/,/g, '')) || 0) + totalNG).toLocaleString();
        }

        // passRate는 ok, ng가 모두 셋팅됐을 때만 계산
        const inspected = 
            (item.data_type === 'ok_tot_count'
                ? totalOK
                : Number(document.getElementById('totalInspected').innerText.replace(/,/g, '')) - (Number(document.getElementById('failedCount').innerText.replace(/,/g, '')) || 0)
            ) +
            (item.data_type === 'ng_tot_count'
                ? totalNG
                : Number(document.getElementById('failedCount').innerText.replace(/,/g, '')) || 0
            );

        const okCnt = (item.data_type === 'ok_tot_count')
            ? totalOK
            : (Number(document.getElementById('totalInspected').innerText.replace(/,/g, '')) - (Number(document.getElementById('failedCount').innerText.replace(/,/g, '')) || 0));
        if (inspected > 0) {
            document.getElementById('passRate').innerText = (okCnt / inspected * 100).toFixed(1) + '%';
        } else {
            document.getElementById('passRate').innerText = '0%';
        }

        return `
            <tr>
                <td class='center'>${item.machine}</td>
                <td class='center'>${item.data_type}</td>
                <td class='center'>${item.value}</td>
                <td class='center'>${item.timestamp}</td>
            </tr>
        `;
    }).join('');
};
</script>

