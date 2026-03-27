<style>
.ems-kpi-row {
    display: flex;
    flex-direction: row;
    gap: 16px;
    align-items: stretch;
}
.ems-kpi-row .kpi-card {
    flex: 1 1 50%;
    min-width: 0;
    color: #000;
}
.ems-kpi-row .kpi-card h3 {
    color: #666;
    margin-bottom: 12px;
}
.ems-kpi-row .kpi-card .kpi-value {
    color: #0b57d0;
    font-weight: 700;
}
#ems-history-toolbar { margin-bottom: 12px; }
.sort-btns { margin-left: 4px; vertical-align: middle; }
.sort-btns .sort-asc, .sort-btns .sort-desc { cursor: pointer; opacity: 0.5; padding: 0 1px; }
.sort-btns .sort-asc:hover, .sort-btns .sort-desc:hover { opacity: 1; }
.sort-btns .sort-active { opacity: 1; font-weight: bold; }
</style>
<div class='main-container'>
    <div class='content-wrapper'>
        <div>
            <div class="kpi-summary ems-kpi-row">
                <div class="kpi-card">
                    <h3>주간 소비전력량</h3>
                    <div class='flex-center'>
                        <p id="weekPowerKwh" class="kpi-value">로딩 중...</p>
                        <p style="color:#666;">kWh</p>
                    </div>
                    <p class="mt10"">월요일 00:00 ~ 현재</p>
                </div>
                <div class="kpi-card">
                    <h3>일간 소비전력량</h3>
                    <div class='flex-center'>
                        <p id="dayPowerKwh" class="kpi-value">로딩 중...</p>
                        <p style="color:#666;">kWh</p>
                    </div>
                    <p class="mt10"">오늘</p>
                </div>
            </div>
        </div>

        <div>
            <div class="table-section">
                <div class="flex" id="ems-history-toolbar">
                    <div class="title red">세척기 가동 / 정지 이력</div>
                    <div class='right' id='ems-history-search'>
                        <input type='text' class='input datepicker' id='history_start_date' placeholder='시작일' />
                        <input type='text' class='input datepicker' id='history_end_date' placeholder='종료일' />
                        <input type='button' class='btn-middle primary' value='조회' id='btnSearchHistory' />
                        <input type='button' class='btn-middle success' value='엑셀 다운로드' id='btnHistoryExcelDownload' />
                    </div>
                </div>
                <table class="list">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th class='center'>가동 시작 <span id='started_at_sort' class='sort-btns' data-order='desc'><span class='sort-asc' title='오름차순'>▲</span><span class='sort-desc' title='내림차순'>▼</span></span></th>
                            <th>가동 종료</th>
                            <th>시작 전류(A)</th>
                            <th>종료 전류(A)</th>
                            <th>가동 시간</th>
                            <th>상태</th>
                        </tr>
                    </thead>
                    <tbody id="cleaner-run-tbody"></tbody>
                </table>
                <div class="paging-area mt20"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const today = new Date().toISOString().slice(0, 10);
    try {
        const startEl = document.getElementById('history_start_date');
        const endEl = document.getElementById('history_end_date');
        if (startEl && !startEl.value) startEl.value = today;
        if (endEl && !endEl.value) endEl.value = today;
    } catch (e) {}

    try {
        const btnSearchHistory = document.getElementById('btnSearchHistory');
        if (btnSearchHistory) {
            btnSearchHistory.addEventListener('click', () => {
                cleanerHistoryPage = 1;
                getCleanerRunHistory({ page: cleanerHistoryPage, per: CLEANER_PER_PAGE, block: PAGING_BLOCK, orderBy: cleanerHistoryOrderBy, order: cleanerHistoryOrder });
            });
        }
        const btnHistoryExcelDownload = document.getElementById('btnHistoryExcelDownload');
        if (btnHistoryExcelDownload) {
            btnHistoryExcelDownload.addEventListener('click', downloadCleanerRunHistoryExcel);
        }

        const startedSort = document.getElementById('started_at_sort');
        if (startedSort) {
            startedSort.querySelector('.sort-asc').addEventListener('click', () => {
                setCleanerStartedSort('asc');
                cleanerHistoryPage = 1;
                getCleanerRunHistory({ page: cleanerHistoryPage, per: CLEANER_PER_PAGE, block: PAGING_BLOCK, orderBy: cleanerHistoryOrderBy, order: cleanerHistoryOrder });
            });
            startedSort.querySelector('.sort-desc').addEventListener('click', () => {
                setCleanerStartedSort('desc');
                cleanerHistoryPage = 1;
                getCleanerRunHistory({ page: cleanerHistoryPage, per: CLEANER_PER_PAGE, block: PAGING_BLOCK, orderBy: cleanerHistoryOrderBy, order: cleanerHistoryOrder });
            });
            setCleanerStartedSort('desc');
        }
    } catch (e) {}

    repeatEmsRefresh();
});

const INTERVAL_MS = 5000;
const EMS_HISTORY_START = '2020-01-01';
const CLEANER_PER_PAGE = 20;
const PAGING_BLOCK = 4;

let cleanerHistoryPage = 1;
let cleanerHistoryOrderBy = 'started_at';
let cleanerHistoryOrder = 'desc';

const repeatEmsRefresh = async () => {
    try {
        await fetchEmsPowerKpi();
        await getCleanerRunHistory({ page: cleanerHistoryPage, per: CLEANER_PER_PAGE, block: PAGING_BLOCK, orderBy: cleanerHistoryOrderBy, order: cleanerHistoryOrder });
    } catch (error) {
        console.error('EMS 데이터 갱신 중 오류:', error);
    }
    setTimeout(repeatEmsRefresh, INTERVAL_MS);
};

const fetchEmsPowerKpi = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getEmsPowerKpi');

    const response = await fetch('./handler.php', {
        method: 'POST',
        body: formData,
    });
    const data = await response.json();

    const weekEl = document.getElementById('weekPowerKwh');
    const dayEl = document.getElementById('dayPowerKwh');
    if (data.result === 'success') {
        if (weekEl) weekEl.innerText = Number(data.week_kwh ?? 0).toLocaleString();
        if (dayEl) dayEl.innerText = Number(data.day_kwh ?? 0).toLocaleString();
    } else {
        if (weekEl) weekEl.innerText = '-';
        if (dayEl) dayEl.innerText = '-';
    }
};

/** 페이징 클릭 시 handler.php → getPaging이 호출하는 전역 콜백 */
window.loadCleanerRunHistory = function (opts) {
    const p = opts && opts.page ? parseInt(opts.page, 10) : 1;
    cleanerHistoryPage = Number.isNaN(p) ? 1 : p;
    getCleanerRunHistory({ page: cleanerHistoryPage, per: CLEANER_PER_PAGE, block: PAGING_BLOCK, orderBy: cleanerHistoryOrderBy, order: cleanerHistoryOrder });
};

function setCleanerStartedSort(dir) {
    const wrap = document.getElementById('started_at_sort');
    if (!wrap) return;
    cleanerHistoryOrderBy = 'started_at';
    cleanerHistoryOrder = dir;
    wrap.setAttribute('data-order', dir);
    wrap.querySelectorAll('.sort-asc, .sort-desc').forEach(el => {
        el.classList.remove('sort-active');
        if ((el.classList.contains('sort-asc') && dir === 'asc') || (el.classList.contains('sort-desc') && dir === 'desc')) {
            el.classList.add('sort-active');
        }
    });
}

const getCleanerRunHistory = async ({ page, per = CLEANER_PER_PAGE, block = PAGING_BLOCK, orderBy = cleanerHistoryOrderBy, order = cleanerHistoryOrder }) => {
    cleanerHistoryOrderBy = orderBy;
    cleanerHistoryOrder = order;
    const endDateDefault = new Date().toISOString().slice(0, 10);
    const startDate = (document.getElementById('history_start_date')?.value || EMS_HISTORY_START).trim();
    const endDate = (document.getElementById('history_end_date')?.value || endDateDefault).trim();

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getCleanerRunHistory');
    formData.append('start_date', startDate);
    formData.append('end_date', endDate);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('orderby', orderBy);
    formData.append('asc', order);

    const response = await fetch('./handler.php', {
        method: 'POST',
        body: formData,
    });
    const data = await response.json();

    const tbody = document.getElementById('cleaner-run-tbody');
    if (!tbody) return;

    if (!data || data.result !== 'success' || !data.data || data.data.length === 0) {
        tbody.innerHTML = `<tr><td class='center' colspan='7'>등록된 이력이 없습니다</td></tr>`;
    } else {
        tbody.innerHTML = data.data.map((item, idx) => {
            const rowNo = (page - 1) * per + idx + 1;
            const ended = item.ended_at;
            const status = ended ? '정지' : '가동중';
            const dur = formatDurationSeconds(item.duration_seconds);
            const endCell = ended ? escapeHtml(String(ended)) : '-';
            return `
            <tr>
                <td class='center'>${rowNo}</td>
                <td class='center'>${escapeHtml(item.started_at || '')}</td>
                <td class='center'>${endCell}</td>
                <td class='center'>${item.start_current != null ? escapeHtml(String(item.start_current)) : '-'}</td>
                <td class='center'>${item.end_current != null ? escapeHtml(String(item.end_current)) : '-'}</td>
                <td class='center'>${dur}</td>
                <td class='center'>${status}</td>
            </tr>`;
        }).join('');
    }

    const where =
        `WHERE machine='cleaner' AND started_at <= '${endDate} 23:59:59' AND (ended_at IS NULL OR ended_at >= '${startDate} 00:00:00')`;
    if (typeof getPaging === 'function') {
        getPaging('cleaner_run_history', 'id', where, page, per, block, 'loadCleanerRunHistory');
    }
};

const downloadCleanerRunHistoryExcel = () => {
    const endDateDefault = new Date().toISOString().slice(0, 10);
    const startDate = (document.getElementById('history_start_date')?.value || EMS_HISTORY_START).trim();
    const endDate = (document.getElementById('history_end_date')?.value || endDateDefault).trim();

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = './handler.php';
    form.target = '_blank';
    form.style.display = 'none';

    const fields = {
        controller: 'mes',
        mode: 'getCleanerRunHistoryExcel',
        start_date: startDate,
        end_date: endDate
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

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDurationSeconds(sec) {
    const n = parseInt(sec, 10);
    if (Number.isNaN(n) || n < 0) return '-';
    const h = Math.floor(n / 3600);
    const m = Math.floor((n % 3600) / 60);
    const s = n % 60;
    return [h, m, s].map((v) => String(v).padStart(2, '0')).join(':');
}
</script>
