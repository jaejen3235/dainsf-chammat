<div class='main-container'>
    <div class='content-wrapper'>        
        <div class="summary-stats">
            <div class="summary-card">
                <h4>전체 납기 건수</h4>
                <div class="number" id="totalShipments">0</div>
                <div class="unit">건</div>
            </div>
            
            <div class="summary-card">
                <h4>정시 납기</h4>
                <div class="number" id="onTimeShipments">0</div>
                <div class="unit">건</div>
            </div>
            
            <div class="summary-card">
                <h4>납기 준수율</h4>
                <div class="number" id="complianceRate">0</div>
                <div class="unit">%</div>
            </div>
            
            <div class="summary-card">
                <h4>평균 납기일</h4>
                <div class="number" id="avgDelayDays">0</div>
                <div class="unit">일</div>
            </div>
        </div>
        
        <!-- 상세 데이터 테이블 -->
        <div class="data-table-container">
            <h3 style="margin-bottom: 20px; color: #333;">📋 상세 납기 데이터</h3>
            <table class="list">
                <thead>
                    <tr>
                        <th>고유번호</th>
                        <th>거래처</th>
                        <th>제품명</th>
                        <th>수량</th>
                        <th>주문일</th>
                        <th>납기 예정일</th>
                        <th>실제 납기일</th>
                        <th>납기 소요일</th>
                    </tr>
                </thead>
                <tbody id="shipmentTableBody">
                    <!-- JavaScript로 동적 생성 -->
                </tbody>
            </table>
        </div>
        <div class="paging-area mt20"></div>
    </div>
</div> 

 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>


// 통계 계산 및 표시
function updateSummaryStats() {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getShipmentStat');

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
        if (data && data.result === 'success') {
            
            // 받아온 값들을 카드에 출력
            const totalShipments = Number(data.total_shipments) || 0;
            const onTimeShipments = Number(data.on_time_shipments) || 0;
            const complianceRate = Number(data.compliance_rate) || 0;
            const avgDelayDays = Number(data.avg_delay_days) || 0;

            document.getElementById('totalShipments').innerHTML = totalShipments;
            document.getElementById('onTimeShipments').innerHTML = onTimeShipments;
            document.getElementById('complianceRate').innerHTML = complianceRate;
            document.getElementById('avgDelayDays').innerHTML = avgDelayDays;
                
        } else if (data && data.message) {
            console.log(data.message);
        }
    })
    .catch(error => console.log(error));
}

// 상수 정의
const CONTROLLER = 'mes';
const MODE = 'getDeliveryReportList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getDeliveryReportList = async ({
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

        getPaging('mes_delivery_report', 'uid', where, page, per, block, 'getDeliveryReportList');
    } catch (error) {
        console.error('설비 가동률 상세 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        return `<tr><td class='center' colspan='20'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.data.map(item => `
        <tr>
            <td class='center'>${item.uid}</td>
            <td class='center'>${item.account_name}</td>
            <td class='center'>${item.item_name}</td>
            <td class='center'>${comma(item.delivery_qty)}</td>
            <td class='center'>${item.order_date}</td>
            <td class='center'>${item.shipment_date}</td>
            <td class='center'>${item.delivery_date}</td>
            <td class='center'>${item.delivery_days}</td>
        </tr>
    `).join('');
};


// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', function() {
    updateSummaryStats();
    getDeliveryReportList({page: 1});
});
</script>