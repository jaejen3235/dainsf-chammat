<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 로그인 이력</div>
    <div class='content-wrapper'>
        <div>
            <table class='list'>
                <colgroup>
                    <col />
                    <col />
                </colgroup>
                <thead>
                    <tr>
                        <th>로그인 일시</th>
                        <th>로그인 아이디</th>
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
    getLoginReport({page : 1});
});

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getLoginReport';
const DEFAULT_ORDER_BY = 'registerDate';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getLoginReport = async ({
    page,
    per = 15,
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

        getPaging('mes_user_login', 'uid', where, page, per, block, 'getLoginReport');
    } catch (error) {
        console.error('대리점 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='pd center'>${item.registerDate}</td>
            <td class='pd center'>${item.loginId}</td>
        </tr>
    `).join('');
};
</script>