<link rel="stylesheet" type="text/css" href="/assets/css/CCP-2BP_모니터링일지_style.css" />
<style>
    .ccp1bp-split-container {
        display: flex;
        width: 100%;
        min-height: calc(100vh - 120px);
    }
    .ccp1bp-left-panel {
        width: 30%;
        border-right: 1px solid #ccc;
        padding: 10px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }
    .ccp1bp-right-panel {
        width: 70%;
        padding: 15px;
        box-sizing: border-box;
        overflow-y: auto;
    }

    /* 좌측 패널 - 상단 버튼 영역 */
    .ccp1bp-action-bar {
        display: flex;
        gap: 6px;
        margin-bottom: 10px;
        flex-shrink: 0;
    }
    .ccp1bp-action-bar button {
        padding: 6px 14px;
        border: 1px solid #aaa;
        background: #f5f5f5;
        cursor: pointer;
        font-size: 12px;
        border-radius: 3px;
        white-space: nowrap;
    }
    .ccp1bp-action-bar button:hover {
        background: #e0e0e0;
    }
    .ccp1bp-action-bar .btn-new {
        background: #1976d2;
        color: #fff;
        border-color: #1565c0;
    }
    .ccp1bp-action-bar .btn-new:hover {
        background: #1565c0;
    }
    .ccp1bp-action-bar .btn-print {
        background: #fff;
        color: #333;
    }
    .ccp1bp-action-bar .btn-save {
        margin-left: auto;
        background: #2e7d32;
        color: #fff;
        border-color: #1b5e20;
    }
    .ccp1bp-action-bar .btn-save:hover {
        background: #1b5e20;
    }

    /* 좌측 패널 - 목록 테이블 */
    .ccp1bp-list-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }
    .ccp1bp-list-table thead th {
        background: #f0f0f0;
        border: 1px solid #ccc;
        padding: 5px 3px;
        text-align: center;
        font-weight: 600;
        white-space: nowrap;
        position: sticky;
        top: 0;
    }
    .ccp1bp-list-table tbody td {
        border: 1px solid #ddd;
        padding: 4px 3px;
        text-align: center;
        vertical-align: middle;
    }
    .ccp1bp-list-table tbody tr:hover {
        background: #e3f2fd;
    }
    .ccp1bp-list-table tbody tr.selected {
        background: #bbdefb;
    }
    .ccp1bp-list-table .btn-tbl {
        padding: 2px 6px;
        border: 1px solid #bbb;
        background: #fafafa;
        cursor: pointer;
        font-size: 10px;
        border-radius: 2px;
        margin: 1px;
    }
    .ccp1bp-list-table .btn-tbl:hover {
        background: #e0e0e0;
    }
    .ccp1bp-list-table .btn-view { color: #1976d2; }
    .ccp1bp-list-table .btn-edit { color: #f57c00; }
    .ccp1bp-list-table .btn-del  { color: #d32f2f; }

    /* 좌측 패널 - 페이징 */
    .ccp1bp-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 2px;
        padding: 8px 0 2px;
        flex-shrink: 0;
    }
    .ccp1bp-pagination button {
        min-width: 26px;
        height: 26px;
        border: 1px solid #ccc;
        background: #fff;
        cursor: pointer;
        font-size: 11px;
        border-radius: 3px;
    }
    .ccp1bp-pagination button:hover {
        background: #e3f2fd;
    }
    .ccp1bp-pagination button.active {
        background: #1976d2;
        color: #fff;
        border-color: #1565c0;
        font-weight: bold;
    }
    .ccp1bp-pagination button:disabled {
        opacity: 0.4;
        cursor: default;
    }

    .ox-cell {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        width: 100% !important;
        height: 100% !important;
        left: 0 !important;
        top: 0 !important;
    }

    .ox-text {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        height: 100% !important;
        pointer-events: none;
        text-align: center !important;
    }

    .ox-mark.selected {
        color: #d32f2f;
        font-weight: bold;
        text-shadow: 0 0 1px rgba(0, 0, 0, 0.5);
    }

    .ox-hit input[type="radio"] {
        display: none;
    }

    .ox-hit {
        position: absolute !important;
        width: 45% !important;
        height: 100% !important;
        cursor: pointer;
        z-index: 10;
    }

    .ox-left {
        left: 0 !important;
    }

    .ox-right {
        right: 0 !important;
    }

    /* 인쇄 시 우측 문서 영역만 출력 */
    @media print {
        html, body {
            overflow: visible !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* 헤더, 좌측메뉴, 토글버튼, 좌측패널 숨김 */
        header,
        .left-container,
        .hidden-container,
        .ccp1bp-left-panel { display: none !important; }

        /* 컨테이너 레이아웃 초기화 */
        main, .main-container, .content-wrapper,
        .ccp1bp-split-container {
            display: block !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            overflow: visible !important;
            height: auto !important;
            min-height: 0 !important;
            border: none !important;
        }

        /* 우측 패널 전체폭 */
        .ccp1bp-right-panel {
            width: 100% !important;
            padding: 0 !important;
            overflow: visible !important;
            height: auto !important;
            border: none !important;
        }

        /* 문서 테두리/그림자 제거 */
        .hpa {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
        }
    }
</style>

<script src="/views/haccp/haccpFormClient.js"></script>
<script>
    function toggleOX(el) {
        const parent = el.closest('.ox-cell');
        const marks = parent.querySelectorAll('.ox-mark');
        const isChecked = el.dataset.checked === 'true';

        const radios = document.getElementsByName(el.name);
        radios.forEach(r => {
            r.checked = false;
            r.dataset.checked = 'false';
        });
        marks.forEach(m => m.classList.remove('selected'));

        if (!isChecked) {
            el.checked = true;
            el.dataset.checked = 'true';
            const markClass = (el.value === 'O') ? '.ox-o' : '.ox-x';
            parent.querySelector(markClass).classList.add('selected');
        }
    }

    // ── 좌측 패널 CRUD 함수 ──
    const HaccpResourceKey = 'HC02_CCP-2BP_모니터링일지';
    const MetaConfig = {
        page_no: 1,
        write_date: 'inspectionDate',
        writer: 'writer_name',
        inspector: 'checker_name',
        action_person: 'approver_name',
    };

    let currentUid = null;
    let currentPage = 1;
    const per = 10;

    function getFormRoot() {
        return document.querySelector('form#monitoringForm') || document.querySelector('form');
    }

    function clearForm() {
        const root = getFormRoot();
        if (!root) return;
        HaccpFormClient.resetToDefaults(root);
    }

    function ccp1bpNew() {
        currentUid = null;
        clearForm();
    }
    function ccp1bpPrint() {
        window.print();
    }
    async function ccp1bpSave() {
        const root = getFormRoot();
        if (!root) return;

        const payload = HaccpFormClient.buildPayloadFromForm(root);
        const meta = HaccpFormClient.buildMetaFromForm(root, MetaConfig);

        if (currentUid) {
            const res = await HaccpFormClient.updateRecord({
                resourceKey: HaccpResourceKey,
                id: currentUid,
                meta,
                payload
            });
            if (res && res.result === 'success') {
                await ccp1bpGoPage(currentPage);
            } else {
                alert(res?.message || '수정 실패');
            }
        } else {
            const res = await HaccpFormClient.createRecord({
                resourceKey: HaccpResourceKey,
                meta,
                payload
            });
            if (res && res.result === 'success') {
                currentUid = res.uid;
                await ccp1bpGoPage(currentPage);
            } else {
                alert(res?.message || '저장 실패');
            }
        }
    }
    async function ccp1bpView(uid) {
        const root = getFormRoot();
        if (!root) return;

        const res = await HaccpFormClient.getOneRecord({ resourceKey: HaccpResourceKey, id: uid });
        if (res && res.result === 'success') {
            currentUid = uid;
            HaccpFormClient.applyPayloadToForm(root, res.payload || {});

            document.querySelectorAll('#ccp1bp-list-body tr').forEach(tr => tr.classList.remove('selected'));
            const tr = document.querySelector(`#ccp1bp-list-body tr[data-uid="${uid}"]`);
            if (tr) tr.classList.add('selected');
        } else {
            alert(res?.message || '데이터 조회 실패');
        }
    }
    function ccp1bpEdit(uid) {
        ccp1bpView(uid);
    }
    async function ccp1bpDelete(uid) {
        if (confirm('삭제하시겠습니까?')) {
            const res = await HaccpFormClient.deleteRecord({ resourceKey: HaccpResourceKey, id: uid });
            if (res && res.result === 'success') {
                if (currentUid === uid) currentUid = null;
                await ccp1bpGoPage(currentPage);
            } else {
                alert(res?.message || '삭제 실패');
            }
        }
    }

    function renderRows(items) {
        const tbody = document.getElementById('ccp1bp-list-body');
        if (!tbody) return;

        if (!items || items.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8">검색된 자료가 없습니다</td></tr>`;
            return;
        }

        tbody.innerHTML = items.map(item => `
            <tr data-uid="${item.uid}">
                <td>${item.write_date || ''}</td>
                <td>${item.page_no ?? ''}</td>
                <td>${item.writer || ''}</td>
                <td>${item.inspector || ''}</td>
                <td>${item.action_person || ''}</td>
                <td><button class="btn-tbl btn-view" onclick="ccp1bpView(${item.uid})">보기</button></td>
                <td><button class="btn-tbl btn-edit" onclick="ccp1bpEdit(${item.uid})">수정</button></td>
                <td><button class="btn-tbl btn-del" onclick="ccp1bpDelete(${item.uid})">삭제</button></td>
            </tr>
        `).join('');
    }

    function setActivePagination(page) {
        const buttons = document.querySelectorAll('.ccp1bp-pagination button');
        buttons.forEach(btn => btn.classList.remove('active'));
        buttons.forEach(btn => {
            if ((btn.textContent || '').trim() === String(page)) btn.classList.add('active');
        });
    }

    async function ccp1bpGoPage(page) {
        currentPage = page;
        setActivePagination(page);

        const res = await HaccpFormClient.listRecords({ resourceKey: HaccpResourceKey, page, per });
        if (!res || res.result !== 'success') {
            alert(res?.message || '목록 조회 실패');
            return;
        }

        HaccpFormClient.updatePaginationUI(
            document.querySelector('.ccp1bp-pagination'),
            page,
            res.total,
            per
        );
        renderRows(res.data);
    }

    document.addEventListener('DOMContentLoaded', function () {
        HaccpFormClient.snapshotDefaults(getFormRoot());
        ccp1bpGoPage(1);
    });
</script>

<div class='main-container'>
    <div class='content-wrapper'>
        <div class="ccp1bp-split-container">
            <!-- 왼쪽 영역 (30%) : 문서 목록 -->
            <div class="ccp1bp-left-panel">
                <!-- 상단 버튼 -->
                <div class="ccp1bp-action-bar">
                    <button class="btn-new" onclick="ccp1bpNew()">📝 새로 작성</button>
                    <button class="btn-print" onclick="ccp1bpPrint()">🖨️ 인쇄</button>
                    <button class="btn-save" onclick="ccp1bpSave()">💾 저장</button>
                </div>

                <!-- 문서 목록 테이블 -->
                <table class="ccp1bp-list-table">
                    <thead>
                        <tr>
                            <th>작성일자</th>
                            <th>페이지</th>
                            <th>작성자</th>
                            <th>점검자</th>
                            <th>조치자</th>
                            <th colspan="3">관리</th>
                        </tr>
                    </thead>
                    <tbody id="ccp1bp-list-body">
                        <!-- DB 연동 전 화면 표시용 더미 제거 -->
                    </tbody>
                </table>

                <!-- 페이징 -->
                <div class="ccp1bp-pagination">
                    <button onclick="ccp1bpGoPage(1)" disabled>&laquo;</button>
                    <button onclick="ccp1bpGoPage(1)" disabled>&lsaquo;</button>
                    <button class="active">1</button>
                    <button onclick="ccp1bpGoPage(2)">2</button>
                    <button onclick="ccp1bpGoPage(3)">3</button>
                    <button onclick="ccp1bpGoPage(2)">&rsaquo;</button>
                    <button onclick="ccp1bpGoPage(3)">&raquo;</button>
                </div>
            </div>
            <!-- 오른쪽 영역 (70%) -->
            <div class="ccp1bp-right-panel">
                <?php
                    $lines = file(__DIR__ . '/HC02_CCP-2BP_모니터링일지.html');

                    // form 시작/종료 태그 기준으로 동적으로 슬라이스
                    $startIndex = null;
                    $endIndex   = null;

                    foreach ($lines as $idx => $line) {
                        if ($startIndex === null && strpos($line, '<form') !== false) {
                            $startIndex = $idx;
                        }
                        if (strpos($line, '</form>') !== false) {
                            $endIndex = $idx;
                            break;
                        }
                    }

                    if ($startIndex !== null && $endIndex !== null && $endIndex >= $startIndex) {
                        $length = $endIndex - $startIndex + 1;
                        echo implode('', array_slice($lines, $startIndex, $length));
                    }
                ?>
            </div>
        </div>
    </div>
</div>
