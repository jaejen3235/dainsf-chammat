<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 금속검출 관리</div>
    <div class="summary-stats">
            <div class="summary-card combined-card">
                <div class="card-title">현재 검사 품목</div>
                <div class="card-value" id="current_item_md01">--</div>
            </div>
            <div class="summary-card target-card">
                <div class="card-title">금일 누적 검사 수량 (OK + NG)</div>
                <div class="card-value ok" id="total_check_count">--</div>
            </div>
            <div class="summary-card avg-card">
                <div class="card-title">금일 검출 수량 (NG)</div>
                <div class="card-value ng" id="total_ng_count">--</div>
            </div>
        </div>
    <div class='content-wrapper'>
        <div class='flex'>              
            <div class="table-title">📅 금속검출 이력</div>
            <div class='btn-box'>
                <input type='text' class='datepicker' id='start_date' placeholder='검색일' />
                <input type='text' class='datepicker' id='end_date' placeholder='검색일' />
                <input type='text' class='input' id='item_name' placeholder='품목' />
                <input type='button' class='btn-small primary' value='검색' onclick='getData({page: 1})' />
                <select id='per_page' class='input' onchange='getData({page: 1})'>
                    <option value='10'>10</option>
                    <option value='15'>15</option>
                    <option value='20'>20</option>
                    <option value='25'>25</option>
                    <option value='30'>30</option>
                    <option value='35'>35</option>
                    <option value='40'>40</option>
                    <option value='45'>45</option>
                    <option value='50'>50</option>
                </select>
                <input type='button' class='btn-small danger' id='btnDeleteSelected' value='선택 삭제' />
                <input type='button' class='btn-small' value='엑셀 다운로드' onclick='downloadMetalDetectionExcel()' />
            </div>
        </div>
        <table class='list mt10'>
            <colgroup>
                <col style="width: 6%;">
                <col style="width: 20%;">
                <col style="width: 16%;">
                <col style="width: 16%;">
                <col style="width: 14%;">
                <col style="width: 14%;">
                <col style="width: 14%;">
            </colgroup>
            <thead>
                <tr>
                    <th>
                        <label class="custom-checkbox">
                            <input type="checkbox" id='chkAll'>
                            <span class="checkmark"></span>
                        </label>
                    </th>
                    <th>품목</th>
                    <th>시작시간</th>
                    <th>종료시간</th>
                    <th>검출수량</th>
                    <th>양품수량</th>
                    <th>생산수량</th>
                </tr>
            </thead>
            <tbody id="item-check-summary-body"></tbody>
        </table>
        <div class="paging-area mt20"></div>
    </div>
</div>

<script>
// ===============================================
// Utility Functions
// ===============================================

/** 숫자 포맷팅 (콤마) */
function formatNumber(num) {
    return num.toLocaleString();
}

// ===============================================
// Card Update Functions
// ===============================================

/** 실시간 요약 카드 업데이트 */
async function updateSummaryCards() {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'updateMetalStats');

    try {
        const itemNameEl = document.getElementById('current_item_md01');
        const checkedSumEl = document.getElementById('total_check_count');
        const detectedSumEl = document.getElementById('total_ng_count');

        if (itemNameEl) {
            itemNameEl.innerText = '없음';
        }
        if (checkedSumEl) {
            checkedSumEl.innerText = '0';
        }
        if (detectedSumEl) {
            detectedSumEl.innerText = '0';
        }

        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error('검출 현황 조회 중 문제가 발생했습니다.');
        }

        const data = await response.json();

        if (data['result'] == 'success') {
            if (checkedSumEl) {
                checkedSumEl.innerText = formatNumber(data.total_checked_sum ?? 0);
            }
            if (detectedSumEl) {
                detectedSumEl.innerText = formatNumber(data.total_detected_sum ?? 0);
            }
        } else {
            alert('검출 현황 조회 실패: ' + result.message);
        }
    } catch (error) {
        console.error('검출 현황 조회 중 오류:', error);
        alert('검출 현황 조회 중 오류가 발생했습니다.');
    }

    updateCurrentItemCard();
}

async function updateCurrentItemCard() {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDetectionStatus');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData,
        });
        const data = await response.json();
        const itemNameEl = document.getElementById('current_item_md01');
        if (!itemNameEl) {
            return;
        }
        if (data.result === 'success' && data.data && data.data.item_name) {
            itemNameEl.innerText = data.data.item_name;
        } else {
            itemNameEl.innerText = '없음';
        }
    } catch (error) {
        console.error('현재 검사 품목 조회 중 오류:', error);
    }
}

// ===============================================
// Table Rendering Functions
// ===============================================
/** 일일 품목별 검출 집계 테이블 렌더링 */
const DEFAULT_PER_PAGE = 10;
const DEFAULT_PAGE_BLOCK = 4;

const getFilters = () => ({
    start_date: document.getElementById('start_date').value,
    end_date: document.getElementById('end_date').value,
    item_name: document.getElementById('item_name').value.trim(),
});

const getPerPage = () => {
    const perPageEl = document.getElementById('per_page');
    const value = perPageEl ? parseInt(perPageEl.value, 10) : DEFAULT_PER_PAGE;
    return Number.isNaN(value) ? DEFAULT_PER_PAGE : value;
};

const escapeSqlString = (value) => value.replace(/'/g, "''");

const buildWhereClause = ({ start_date, end_date, item_name }) => {
    const clauses = [];
    if (start_date) {
        clauses.push(`started_at >= '${escapeSqlString(start_date)} 00:00:00'`);
    }
    if (end_date) {
        clauses.push(`started_at <= '${escapeSqlString(end_date)} 23:59:59'`);
    }
    if (item_name) {
        clauses.push(`item_name LIKE '%${escapeSqlString(item_name)}%'`);
    }

    return clauses.length ? `WHERE ${clauses.join(' AND ')}` : '';
};

async function getData({ page = 1, per = getPerPage(), block = DEFAULT_PAGE_BLOCK } = {}) {
    // 서버로 보낼 데이터 준비
    const filters = getFilters();
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getMetalDetectionHistory');
    formData.append('start_date', filters.start_date);
    formData.append('end_date', filters.end_date);
    formData.append('item_name', filters.item_name);
    formData.append('page', page);
    formData.append('per', per);

    //console.log(formData);

    try {
        // 서버에 데이터 전송
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error('검출 현황 조회 중 문제가 발생했습니다.');
        }

        const result = await response.json();

        const tableBody = document.querySelector('#item-check-summary-body');
        tableBody.innerHTML = generateTableContent(result.data || []);

        const where = buildWhereClause(filters);
        getPaging('metal_detection_history', 'id', where, page, per, block, 'getData');
        
    } catch (error) {
        console.error('검출 현황 조회 중 오류:', error);        
    }
}

const generateTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center' colspan='7'>데이터가 없습니다.</td></tr>`;
    }

    return data.map(item => `
        <tr>
            <td class='pd center'>
                <label class="custom-checkbox">
                    <input type="checkbox" class='chk' value='${item.id}'>
                    <span class="checkmark"></span>
                </label>
            </td>
            <td class='pd center'>${item.item_name}</td>
            <td class='pd center'>${item.started_at}</td>
            <td class='pd center'>${item.ended_at}</td>
            <td class='pd center'>${item.detected_qty}</td>
            <td class='pd center'>${item.good_qty}</td>
            <td class='pd center'>${item.produced_qty}</td>
        </tr>
    `).join('');
};

// ===============================================
// Initial Load
// ===============================================
window.onload = () => {
    // 날짜 기본값 설정
    const today = new Date();
    const oneWeekAgo = new Date();
    oneWeekAgo.setDate(today.getDate() - 7);
    
    // 날짜를 YYYY-MM-DD 형식으로 포맷팅
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    document.getElementById('start_date').value = formatDate(today);
    document.getElementById('end_date').value = formatDate(today);
    
    // 날짜 표시 업데이트 (테이블 제목)
    //document.querySelector('.table-title').textContent = `📅 오늘 (${formatDate(today)}) 금속검출 이력`;
            
    // 1. 요약 카드 업데이트
    updateSummaryCards();
            
    // 2. 테이블 데이터 렌더링
    getData({ page: 1 }); 

    const chkAll = document.getElementById('chkAll');
    if (chkAll) {
        chkAll.addEventListener('click', () => {
            if (chkAll.checked) {
                checkAll('chk');
            } else {
                checkAllDisolve('chk');
            }
        });
    }

    const btnDeleteSelected = document.getElementById('btnDeleteSelected');
    if (btnDeleteSelected) {
        btnDeleteSelected.addEventListener('click', deleteSelected);
    }
};

const downloadMetalDetectionExcel = () => {
    const filters = getFilters();
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = './handler.php';
    form.target = '_blank';
    form.style.display = 'none';

    const fields = {
        controller: 'mes',
        mode: 'getMetalDetectionHistoryExcel',
        start_date: filters.start_date,
        end_date: filters.end_date,
        item_name: filters.item_name,
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

const deleteSelected = () => {
    let ids = '';
    document.querySelectorAll('.chk').forEach((elem) => {
        if (elem.checked) {
            ids += elem.value + ",";
        }
    });

    if (ids === '') {
        alert('삭제하실 데이터를 선택하세요');
        return;
    }

    if (confirm('선택하신 DATA를 삭제하시겠습니까? 삭제 후에는 복구가 불가능합니다')) {
        const formData = new FormData();
        formData.append('controller', 'mes');
        formData.append('mode', 'deleteMetalDetectionHistory');
        formData.append('ids', ids);

        fetch('./handler.php', {
            method: 'post',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(function (data) {
                alert(data.message);
                if (data.result === 'success') {
                    const chkAll = document.getElementById('chkAll');
                    if (chkAll) {
                        chkAll.checked = false;
                    }
                    getData({ page: 1 });
                }
            })
            .catch(error => console.log(error));
    }
};
</script>
