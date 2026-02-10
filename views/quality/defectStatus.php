<div class='main-container'>
    <div class='title-wrapper'>불량 현황</div>
        <div class='search-wrapper'>
            <div class='search-box'>
                <input type="text" class='datepicker' id='startDate'> ~ <input type="text" class='datepicker' id='endDate'>
                <button class='btn-large success revision' id='btnRevision'><i class='bx bx-revision'></i></button>
            </div>
            <div class='button-box'>
            </div>
        </div>
        <div class='content-wrapper'>
            <div>
                <table class='list'>
                    <colgroup>
                        <col width='200' />
                        <col />
                        <col width='200' />
                        <col width='200' />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>품목</th>
                            <th>불량사유</th>
                            <th>불량수량</th>
                            <th>불량발생일</th>
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
    // 검색
    try {
        const search = document.querySelector('.fa-search');
        if(search) {
            search.addEventListener('click', () => {
                getDefectStatusList({page : 1});
            });
        }
    } catch(e) {}

    getDefectStatusList({page : 1});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getDefectStatusList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getDefectStatusList = async ({
    page,
    per = 10,
    block = 4,
    orderBy = DEFAULT_ORDER_BY,
    order = DEFAULT_ORDER
}) => {
    let where = `where 1=1`;

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

        getPaging('mes_defective_report', 'uid', where, page, per, block, 'getDefectStatusList');
    } catch (error) {
        console.error('불량 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.itemName}</td>
            <td class='center'>${item.reason}</td>
            <td class='center'>${comma(item.qty)}</td>
            <td class='center'>${item.registerDate}</td>
        </tr>
    `).join('');
};
</script>