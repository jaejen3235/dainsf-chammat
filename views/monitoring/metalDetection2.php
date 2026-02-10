<div class="main-container md2-container">
    <div class="content-wrapper md2-wrapper">
        <div class="md2-root" id="md2Root">
            <div class="md2-dashboard" id="mainPanel">
                <section class="md2-hero">
                    <div class="md2-card md2-selected-card" id="selectedCard">
                        <div class="md2-selected-label">검사 중 품목</div>
                        <div class="md2-selected-name-row">
                            <div class="md2-selected-name" id="selectedItemName">-</div>
                            <img id="selectedInspectIcon" class="md2-selected-icon" src="/assets/images/inspecting.gif" alt="금속검출 진행 중" />
                        </div>
                        <div class="md2-selected-standard" id="selectedItemStandard">-</div>
                        <div class="md2-status-summary">
                            <div class="md2-status-grid">
                                <div class="md2-status-item">시작시간<strong id="inspectingStartTime">-</strong></div>
                                <div class="md2-status-item">누적수량<strong id="inspectingProduced">0</strong></div>
                                <div class="md2-status-item">검출수량<strong id="inspectingDetected">0</strong></div>
                                <div class="md2-status-item">양품수량<strong id="inspectingGood">0</strong></div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<style>
    .md2-container {
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }

    .md2-wrapper {
        flex: 1;
        padding: 16px;
    }

    .md2-root {
        height: 100%;
        border-radius: 14px;
        background: linear-gradient(135deg, #f4f7fb 0%, #e9eff6 100%);
        padding: 18px;
    }

    .md2-dashboard {
        height: 100%;
        display: grid;
        grid-template-rows: 1fr;
        gap: 16px;
    }

    .md2-hero,
    .md2-list-panel {
        min-height: 0;
    }

    .md2-card {
        background: #ffffff;
        border: 1px solid #dbe3f1;
        border-radius: 18px;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    }

    .md2-selected-card {
        position: relative;
        height: 100%;
        padding: 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-align: center;
    }

    .md2-selected-label {
        font-size: 24px;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: #6c7a96;
    }

    .md2-selected-name {
        font-size: clamp(4.8rem, 7.2vw, 9.2rem);
        font-weight: 700;
        color: #1f2937;
        word-break: break-word;
    }

    .md2-selected-name-row {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .md2-selected-standard {
        font-size: 2rem;
        color: #6b7280;
    }

    .md2-status-summary {
        width: 100%;
        background: #f4f7fb;
        border: 1px solid #dbe3f1;
        border-radius: 14px;
        padding: 12px 16px;
        display: grid;
        gap: 8px;
    }

    .md2-status-title {
        font-size: 24px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
    }

    .md2-status-primary {
        font-size: 40px;
        font-weight: 700;
        color: #1f2937;
    }

    .md2-status-grid {
        margin-top: 20px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 50px;
        font-size: 40px;
        color: #64748b;
        text-align: center;
    }

    .md2-status-item strong {
        display: block;
        font-size: 60px;
        color: #1f2937;
    }

    .md2-btn {
        border: none;
        border-radius: 10px;
        padding: 12px 18px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s ease, background-color 0.2s ease;
    }

    .md2-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .md2-btn-primary {
        background: #1a73e8;
        color: #ffffff;
    }

    .md2-btn-primary:hover:not(:disabled) {
        background: #165fba;
    }

    .md2-btn-danger {
        background: #dc3545;
        color: #ffffff;
    }

    .md2-btn-danger:hover:not(:disabled) {
        background: #b02a37;
    }

    .md2-selected-icon {
        width: 46px;
        display: none;
    }

    .md2-list-panel {
        background: #ffffff;
        border: 1px solid #dbe3f1;
        border-radius: 18px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .md2-list-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .md2-list-title {
        font-size: 16px;
        font-weight: 600;
        color: #374151;
    }

    .md2-list-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .md2-toggle {
        border: 1px solid #dbe3f1;
        background: #ffffff;
        color: #374151;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .md2-toggle:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .md2-item-scroller {
        flex: 1;
        display: flex;
        gap: 14px;
        overflow-x: auto;
        padding: 8px 4px 4px;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
    }

    .md2-item-card {
        position: relative;
        flex: 0 0 auto;
        width: 160px;
        aspect-ratio: 2 / 3;
        background-color: #ffffff;
        border: 1px solid #dbe3f1;
        border-radius: 16px;
        padding: 12px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        scroll-snap-align: start;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .md2-item-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
        border-color: #b6c4e1;
    }

    .md2-item-card.selected {
        border: 2px solid #1a73e8;
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 10px 20px rgba(26, 115, 232, 0.18);
    }

    .md2-item-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        text-align: center;
    }

    .md2-item-standard {
        font-size: 0.85rem;
        color: #6b7280;
        text-align: center;
    }

    .md2-inspecting-icon {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 26px;
        display: none;
    }

    .md2-loading,
    .md2-error {
        text-align: center;
        padding: 16px;
        color: #64748b;
        min-width: 200px;
    }

    .md2-error {
        color: #dc3545;
    }

    .md2-dashboard.is-inspecting .md2-item-card {
        pointer-events: none;
        opacity: 0.6;
    }

    .md2-root.theme-dark {
        background: linear-gradient(135deg, #0f172a 0%, #111827 100%);
        color: #e5e7eb;
    }

    .md2-root.theme-dark .md2-card,
    .md2-root.theme-dark .md2-list-panel,
    .md2-root.theme-dark .md2-item-card,
    .md2-root.theme-dark .md2-status-summary {
        background: #111827;
        border-color: #1f2a44;
    }

    .md2-root.theme-dark .md2-status-summary {
        background: #111c30;
    }

    .md2-root.theme-dark .md2-selected-label,
    .md2-root.theme-dark .md2-status-title,
    .md2-root.theme-dark .md2-status-grid,
    .md2-root.theme-dark .md2-loading {
        color: #94a3b8;
    }

    .md2-root.theme-dark .md2-selected-name,
    .md2-root.theme-dark .md2-status-primary,
    .md2-root.theme-dark .md2-status-item strong,
    .md2-root.theme-dark .md2-item-name,
    .md2-root.theme-dark .md2-list-title {
        color: #e5e7eb;
    }

    .md2-root.theme-dark .md2-selected-standard,
    .md2-root.theme-dark .md2-item-standard {
        color: #9ca3af;
    }

    .md2-root.theme-dark .md2-refresh,
    .md2-root.theme-dark .md2-toggle {
        color: #e5e7eb;
        border-color: #1f2a44;
        background: #111827;
    }

    .md2-root.theme-dark .md2-item-card.selected {
        border-color: #38bdf8;
        box-shadow: 0 10px 20px rgba(56, 189, 248, 0.2);
    }

    .md2-root.theme-dark .md2-selected-icon,
    .md2-root.theme-dark .md2-inspecting-icon {
        background: #0b1220;
        border-radius: 50%;
        padding: 4px;
    }

    @media (max-width: 1200px) {
        .md2-status-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .md2-wrapper {
            padding: 12px;
        }

        .md2-root {
            padding: 12px;
        }

        .md2-dashboard {
            grid-template-rows: auto;
        }

        .md2-selected-card {
            padding: 18px;
        }

        .md2-selected-actions {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
let selectedItemId = null;
let selectedItemName = null;
let selectedItemStandard = null;
let inspectingItemId = null;
let inspectingItemName = null;
let inspectingStartTime = null;
let inspectingDetectedQty = 0;
let inspectingGoodQty = 0;
let inspectingProducedQty = 0;
let statusPollId = null;

window.addEventListener('DOMContentLoaded', async () => {
    initThemeToggle();
    await loadDetectionStatus();
    getItemList();
    startStatusPolling();

    
});

const startStatusPolling = () => {
    if (statusPollId) {
        clearInterval(statusPollId);
    }
    statusPollId = setInterval(() => {
        loadDetectionStatus();
    }, 3000);
};

const initThemeToggle = () => {
    const themeToggle = document.getElementById('themeToggle');
    const root = document.getElementById('md2Root');
    if (!themeToggle || !root) {
        return;
    }

    const savedTheme = localStorage.getItem('itemTheme');
    if (savedTheme === 'dark') {
        root.classList.add('theme-dark');
    }

    const updateLabel = () => {
        const isDark = root.classList.contains('theme-dark');
        themeToggle.textContent = isDark ? 'White' : 'Dark';
    };

    updateLabel();

    themeToggle.addEventListener('click', () => {
        root.classList.toggle('theme-dark');
        const isDark = root.classList.contains('theme-dark');
        localStorage.setItem('itemTheme', isDark ? 'dark' : 'light');
        updateLabel();
    });
};

// 품목 목록 가져오기
const getItemList = async () => {
    const itemScroller = document.getElementById('itemScroller');
    itemScroller.innerHTML = '<div class="md2-loading">품목 목록을 불러오는 중...</div>';

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getAllItemList');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        itemScroller.innerHTML = generateItemCards(data);

        // 카드 클릭 이벤트 바인딩
        document.querySelectorAll('.md2-item-card').forEach(card => {
            card.addEventListener('click', () => {
                if (inspectingItemId) {
                    return;
                }
                // 기존 선택 해제
                document.querySelectorAll('.md2-item-card').forEach(c => {
                    c.classList.remove('selected');
                });
                // 현재 카드 선택
                card.classList.add('selected');
                selectedItemId = card.dataset.itemId;
                selectedItemName = card.dataset.itemName;
                selectedItemStandard = card.dataset.itemStandard || '-';
                updateSelectedDisplay();

                const scroller = document.getElementById('itemScroller');
                if (scroller) {
                    scroller.scrollTo({
                        left: card.offsetLeft - 16,
                        behavior: 'smooth'
                    });
                }
            });
        });

        if (inspectingItemId) {
            const inspectingCard = document.querySelector(`.md2-item-card[data-item-id="${inspectingItemId}"]`);
            if (inspectingCard) {
                inspectingCard.classList.add('selected');
                selectedItemId = inspectingItemId;
                selectedItemName = inspectingItemName;
                selectedItemStandard = inspectingCard.dataset.itemStandard || '-';
                updateSelectedDisplay();
            }
        }

        updateInspectingUI();
    } catch (error) {
        console.error('품목 데이터를 가져오는 중 오류가 발생했습니다:', error);
        itemScroller.innerHTML = `<div class="md2-error">오류: ${error.message}</div>`;
    }
};

// 품목 카드 생성
const generateItemCards = (data) => {
    const items = data.data || data || [];

    if (!items || items.length === 0) {
        return `<div class="md2-error">검색된 자료가 없습니다</div>`;
    }

    const orderedItems = [...items];
    if (inspectingItemId) {
        orderedItems.sort((a, b) => {
            const aId = String(a.uid);
            const bId = String(b.uid);
            if (aId === String(inspectingItemId)) return -1;
            if (bId === String(inspectingItemId)) return 1;
            return 0;
        });
    }

    let html = '';
    orderedItems.forEach(item => {
        const itemId = item.uid;
        const itemName = item.item_name || '품목명 없음';
        const standard = item.standard || '규격 없음';

        html += `
            <div class="md2-item-card" data-item-id="${itemId}" data-item-name="${itemName}" data-item-standard="${standard}">
                <div class="md2-item-name">${itemName}</div>
                <div class="md2-item-standard">${standard}</div>
                <img class="md2-inspecting-icon" src="/assets/images/inspecting.gif" alt="금속검출 진행 중" />
            </div>
        `;
    });

    return html;
};

// 금속검출 시작 함수
const startDetection = async (itemName, itemId) => {
    if (inspectingItemId) {
        return;
    }
    if (!confirm(`${itemName} 품목으로 금속검출을 시작합니다.`)) {
        return;
    }
    inspectingItemId = itemId;
    inspectingItemName = itemName;
    updateInspectingUI();
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'startDetection');
    formData.append('item_name', itemName);
    formData.append('item_uid', itemId);

    const response = await fetch('./handler.php', {
        method: 'POST',
        body: formData
    });
    const data = await response.json();
    if (data.result === 'success') {
        alert(data.message);
        await loadDetectionStatus();
    } else {
        alert(data.message);
    }
};

// 금속검출 종료 함수
const stopDetection = async () => {
    if (!confirm(`금속검출을 종료합니다.`)) {
        return;
    }
    inspectingItemId = null;
    inspectingItemName = null;
    inspectingStartTime = null;
    inspectingDetectedQty = 0;
    inspectingGoodQty = 0;
    inspectingProducedQty = 0;
    updateInspectingUI();
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'stopDetection');

    const response = await fetch('./handler.php', {
        method: 'POST',
        body: formData
    });

    const data = await response.json();
    if (data.result === 'success') {
        alert(data.message);
    } else {
        alert(data.message);
    }
};

const updateSelectedDisplay = () => {
    const selectedNameEl = document.getElementById('selectedItemName');
    const selectedStandardEl = document.getElementById('selectedItemStandard');

    if (selectedNameEl) {
        selectedNameEl.textContent = selectedItemName || '품목을 선택하세요';
    }
    if (selectedStandardEl) {
        selectedStandardEl.textContent = selectedItemStandard || '-';
    }
};

const updateInspectingUI = () => {
    const inspectingText = document.getElementById('inspectingText');
    const inspectingStartEl = document.getElementById('inspectingStartTime');
    const inspectingDetectedEl = document.getElementById('inspectingDetected');
    const inspectingGoodEl = document.getElementById('inspectingGood');
    const inspectingProducedEl = document.getElementById('inspectingProduced');
    const mainPanel = document.getElementById('mainPanel');
    const selectedInspectIcon = document.getElementById('selectedInspectIcon');

    if (inspectingText) {
        const text = inspectingItemName ? inspectingItemName : '-';
        inspectingText.textContent = text;
    }
    if (inspectingStartEl) {
        inspectingStartEl.textContent = inspectingStartTime || '-';
    }
    if (inspectingDetectedEl) {
        inspectingDetectedEl.textContent = inspectingDetectedQty ?? 0;
    }
    if (inspectingGoodEl) {
        inspectingGoodEl.textContent = inspectingGoodQty ?? 0;
    }
    if (inspectingProducedEl) {
        inspectingProducedEl.textContent = inspectingProducedQty ?? 0;
    }

    if (mainPanel) {
        mainPanel.classList.toggle('is-inspecting', Boolean(inspectingItemId));
    }

    if (selectedInspectIcon) {
        const showSelectedIcon = inspectingItemId && selectedItemId === inspectingItemId;
        selectedInspectIcon.style.display = showSelectedIcon ? 'block' : 'none';
    }

    document.querySelectorAll('.md2-item-card').forEach(card => {
        const icon = card.querySelector('.md2-inspecting-icon');
        if (!icon) {
            return;
        }
        if (inspectingItemId && card.dataset.itemId === String(inspectingItemId)) {
            icon.style.display = 'block';
        } else {
            icon.style.display = 'none';
        }
    });
};

const loadDetectionStatus = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDetectionStatus');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.result === 'success' && data.data) {
            inspectingItemId = data.data.item_uid;
            inspectingItemName = data.data.item_name;
            inspectingStartTime = data.data.started_at || '-';
            inspectingDetectedQty = data.data.detected_qty || 0;
            inspectingGoodQty = data.data.good_qty || 0;
            inspectingProducedQty = data.data.produced_qty || 0;
            selectedItemId = inspectingItemId;
            selectedItemName = inspectingItemName;
            selectedItemStandard = null;
        } else {
            inspectingItemId = null;
            inspectingItemName = null;
            inspectingStartTime = null;
            inspectingDetectedQty = 0;
            inspectingGoodQty = 0;
            inspectingProducedQty = 0;
        }
        updateSelectedDisplay();
        updateInspectingUI();
    } catch (error) {
        console.error('금속검출 상태를 가져오는 중 오류가 발생했습니다:', error);
    }
};
</script>
