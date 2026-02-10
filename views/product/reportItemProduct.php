<div class='main-container'>
    <div class='content-wrapper'>            
        <div class='title flex'>
            <div>📊 제품별 생산 실적 요약</div>
            <div class='btn-box'>
                <input type='text' class='input datepicker' name='start_date' id='start_date' placeholder='시작일' value="2025-11-01" />
                <input type='text' class='input datepicker' name='end_date' id='end_date' placeholder='종료일' value="2025-11-30" />
                <select class="select" id="item_select">
                    <option value="">전체 품목</option>
                    <option value="FGP-001">스마트칩</option>
                    <option value="FGP-002">모듈케이스</option>
                    <option value="FGP-003">센서부품</option>
                </select>
                <input type='button' class='btn-middle secondary' value='검색' onclick='searchProductPerformance()' />
            </div>
        </div>
            
        <table class='product-performance-list list mt10'>
            <colgroup>
                <col style="width: 15%;" />
                <col style="width: 15%;" />
                <col style="width: 15%;" />
                <col style="width: 10%;" />
                <col style="width: 10%;" />
                <col style="width: 10%;" />
                <col style="width: 10%;" />
                <col style="width: 10%;" />
            </colgroup>
            <thead>
                <tr>
                    <th>품번</th>
                    <th>품목명</th>
                    <th>규격</th>
                    <th>지시 총 수량</th>
                    <th>생산 완료 수량</th>
                    <th>합격 수량</th>
                    <th>불량 수량</th>
                    <th>합격률</th>
                </tr>
            </thead>
            <tbody id="product-performance-body">
                <tr><td class='center' colspan='8'>검색된 제품별 생산 실적이 없습니다</td></tr>
            </tbody>
        </table>

        <div class="paging-area mt30 center"></div>
    </div>
</div>

<script>
// ===============================================
// Mock Data: 품목별 집계 데이터 (기간 내 모든 지시서 합산)
// ===============================================
const mockProductData = [
    { item_code: 'SM-C001', item_name: '스마트칩', spec: 'A급(5x5)', ordered_total: 5000, worked_total: 4800, pass_total: 4700, fail_total: 100 },
    { item_code: 'MO-K101', item_name: '모듈케이스', spec: 'B급(10x5)', ordered_total: 1500, worked_total: 1500, pass_total: 1485, fail_total: 15 },
    { item_code: 'SE-P005', item_name: '센서부품', spec: 'A급(소)', ordered_total: 2000, worked_total: 1000, pass_total: 990, fail_total: 10 },
];

const tableBody = document.getElementById('product-performance-body');

// ===============================================
// Utility Functions
// ===============================================

/**
 * 합격률을 계산하고 색상을 적용합니다.
 */
function calculateAndRenderRate(pass, worked) {
    if (worked === 0) return { rate: 'N/A', color: '' };
            
    const rate = (pass / worked) * 100;
    let color = '';
            
    if (rate >= 99) {
        color = 'var(--status-finish)'; // 99% 이상: 녹색
    } else if (rate >= 90) {
        color = 'orange'; // 90% 이상: 주황색
    } else {
        color = 'var(--status-fail)'; // 90% 미만: 빨간색
    }

    return { rate: `${rate.toFixed(1)}%`, color: color };
}

/**
 * 제품별 실적 목록을 화면에 렌더링합니다.
 * @param {Array} data - 집계 데이터 배열
 */
function renderPerformanceList(data) {
    tableBody.innerHTML = '';
            
    if (data.length === 0) {
        tableBody.innerHTML = `<tr><td class='center' colspan='8'>검색된 제품별 생산 실적이 없습니다</td></tr>`;
        return;
    }

    let grandTotalOrdered = 0;
    let grandTotalWorked = 0;
    let grandTotalPass = 0;
    let grandTotalFail = 0;

    data.forEach(item => {
        const { rate, color } = calculateAndRenderRate(item.pass_total, item.worked_total);
                
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.item_code}</td>
            <td>${item.item_name}</td>
            <td>${item.spec}</td>
            <td>${item.ordered_total.toLocaleString()}</td>
            <td>${item.worked_total.toLocaleString()}</td>
            <td style="color: var(--status-finish);">${item.pass_total.toLocaleString()}</td>
            <td style="color: var(--status-fail);">${item.fail_total.toLocaleString()}</td>
            <td style="font-weight: 700; color: ${color};">${rate}</td>
        `;
        tableBody.appendChild(row);

        // 전체 합계 계산
        grandTotalOrdered += item.ordered_total;
        grandTotalWorked += item.worked_total;
        grandTotalPass += item.pass_total;
        grandTotalFail += item.fail_total;
    });
            
    // 전체 합계 행 추가
    const totalRow = document.createElement('tr');
    totalRow.className = 'total-row';
    const { rate: totalRate, color: totalColor } = calculateAndRenderRate(grandTotalPass, grandTotalWorked);

    totalRow.innerHTML = `
        <td colspan="3">전체 합계</td>
        <td>${grandTotalOrdered.toLocaleString()}</td>
        <td>${grandTotalWorked.toLocaleString()}</td>
        <td>${grandTotalPass.toLocaleString()}</td>
        <td>${grandTotalFail.toLocaleString()}</td>
        <td style="color: ${totalColor};">${totalRate}</td>
    `;
    tableBody.appendChild(totalRow);
}

// ===============================================
// Event Handlers
// ===============================================

/** 제품별 생산 실적 검색 */
function searchProductPerformance() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const itemCode = document.getElementById('item_select').value;
            
    console.log(`[제품별 실적] 검색 기간: ${startDate} ~ ${endDate}, 품목 코드: ${itemCode}`);
            
    // TODO: 실제 API 호출 (예: /api/production/product_summary?start=${startDate}&end=${endDate}&item=${itemCode})

    let filteredData = mockProductData;
            
    // 품목 필터링 (Mockup 시뮬레이션)
    if (itemCode) {
        filteredData = mockProductData.filter(item => item.item_code.startsWith(itemCode.substring(0, 2))); // 단순 시뮬레이션
    }

    renderPerformanceList(filteredData); 
}

// ===============================================
// Initial Load
// ===============================================
window.onload = () => {
    searchProductPerformance();
};
</script>