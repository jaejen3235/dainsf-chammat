/* global HaccpFormClient */

// 폼 “기본값(HTML에 default로 들어있는 값)”을 스냅샷/복원하기 위한 저장소
// - 페이지에서 form state를 수정한 뒤에도 "새로 작성"은 원복해야 하므로,
//   DOMContentLoaded 시점의 값을 스냅샷으로 저장합니다.
const _haccpFormDefaults = new WeakMap();

// 공통: DOM → payload(JSON)
function buildPayloadFromForm(rootEl) {
    const payload = {};
    if (!rootEl) return payload;

    const elements = rootEl.querySelectorAll('input, textarea, select');
    elements.forEach(el => {
        const key = el.name || el.id;
        if (!key) return;

        const tag = (el.tagName || '').toLowerCase();
        const type = (el.type || '').toLowerCase();

        if (type === 'checkbox') {
            payload[key] = !!el.checked;
        } else if (tag === 'textarea' || tag === 'input') {
            payload[key] = el.value ?? '';
        } else {
            payload[key] = el.value ?? '';
        }
    });

    return payload;
}

function snapshotFormDefaults(rootEl) {
    if (!rootEl) return;
    if (_haccpFormDefaults.has(rootEl)) return;

    const defaults = {
        fields: [],
        oxMarks: []
    };

    rootEl.querySelectorAll('input, textarea, select').forEach(el => {
        const type = (el.type || '').toLowerCase();
        const isCheckbox = type === 'checkbox';

        defaults.fields.push({
            el,
            type,
            checked: isCheckbox ? !!el.checked : null,
            datasetChecked: (isCheckbox && el.dataset && typeof el.dataset.checked !== 'undefined') ? el.dataset.checked : undefined,
            value: !isCheckbox ? (el.value ?? '') : undefined,
        });
    });

    // OX UI는 `.ox-mark.selected` 같은 클래스 상태도 초기값을 보존해야 함
    rootEl.querySelectorAll('.ox-mark').forEach(el => {
        defaults.oxMarks.push({ el, className: el.className });
    });

    _haccpFormDefaults.set(rootEl, defaults);
}

function resetFormToDefaults(rootEl) {
    if (!rootEl) return;
    snapshotFormDefaults(rootEl);

    const defaults = _haccpFormDefaults.get(rootEl);
    if (!defaults) return;

    defaults.fields.forEach(f => {
        if (!f || !f.el) return;
        if (f.type === 'checkbox') {
            f.el.checked = !!f.checked;
            if (typeof f.datasetChecked !== 'undefined') {
                f.el.dataset.checked = f.datasetChecked;
            }
        } else {
            f.el.value = f.value ?? '';
        }
    });

    defaults.oxMarks.forEach(m => {
        if (m && m.el) m.el.className = m.className;
    });
}

function updatePaginationUI(paginationEl, currentPage, total, per) {
    if (!paginationEl) return;

    const t = Number(total ?? 0);
    const p = Number(per ?? 10);
    const maxPage = Math.max(1, Math.ceil(t / p));

    paginationEl.querySelectorAll('button').forEach(btn => {
        const txt = (btn.textContent || '').trim();
        const digit = txt.match(/^\d+$/) ? parseInt(txt, 10) : null;

        if (digit !== null) {
            if (digit > maxPage) {
                btn.style.display = 'none';
            } else {
                btn.style.display = '';
            }
            return;
        }

        // 화살표(이전/다음) 버튼
        // « 또는 ‹ 는 prev, › 또는 » 는 next
        if (txt.includes('«') || txt.includes('‹')) {
            btn.disabled = currentPage <= 1;
        } else if (txt.includes('›') || txt.includes('»')) {
            btn.disabled = currentPage >= maxPage;
        }
    });
}

// 공통: payload(JSON) → DOM
function applyPayloadToForm(rootEl, payload) {
    if (!rootEl || !payload) return;

    Object.entries(payload).forEach(([key, value]) => {
        let field = rootEl.querySelector(`[name="${CSS.escape(key)}"]`);
        if (!field) field = rootEl.querySelector(`#${CSS.escape(key)}`);
        if (!field) return;

        const type = (field.type || '').toLowerCase();
        if (type === 'checkbox') {
            field.checked = !!value;
        } else {
            field.value = value ?? '';
        }
    });
}

function getFieldValue(rootEl, name) {
    if (!rootEl || !name) return null;
    let el = rootEl.querySelector(`[name="${CSS.escape(name)}"]`);
    if (!el) el = rootEl.querySelector(`#${CSS.escape(name)}`);
    if (!el) return null;
    if ((el.type || '').toLowerCase() === 'checkbox') return !!el.checked;
    return el.value ?? null;
}

function buildMetaFromForm(rootEl, metaConfig) {
    if (!metaConfig) return {};
    return {
        page_no: metaConfig.page_no ?? 1,
        write_date: getFieldValue(rootEl, metaConfig.write_date),
        writer: getFieldValue(rootEl, metaConfig.writer),
        inspector: getFieldValue(rootEl, metaConfig.inspector),
        action_person: getFieldValue(rootEl, metaConfig.action_person),
    };
}

async function haccpApi({ mode, resourceKey, id = null, meta = {}, payload = null }) {
    const body = {
        controller: 'haccpRecords',
        mode,
        resource_key: resourceKey,
        meta,
    };
    if (id !== null) body.id = id;
    if (payload !== null) body.payload = payload;

    const res = await fetch('./handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    });

    return res.json();
}

const HaccpFormClient = {
    buildPayloadFromForm,
    applyPayloadToForm,
    buildMetaFromForm,
    snapshotDefaults: snapshotFormDefaults,
    resetToDefaults: resetFormToDefaults,
    updatePaginationUI,
    listRecords: async function ({ resourceKey, page = 1, per = 10 }) {
        return haccpApi({ mode: 'list', resourceKey, meta: {}, payload: null, id: null, page, per });
    },
    getOneRecord: async function ({ resourceKey, id }) {
        return haccpApi({ mode: 'getOne', resourceKey, id });
    },
    createRecord: async function ({ resourceKey, meta, payload }) {
        return haccpApi({ mode: 'create', resourceKey, meta, payload });
    },
    updateRecord: async function ({ resourceKey, id, meta, payload }) {
        return haccpApi({ mode: 'update', resourceKey, id, meta, payload });
    },
    deleteRecord: async function ({ resourceKey, id }) {
        return haccpApi({ mode: 'delete', resourceKey, id, meta: {}, payload: null });
    },
};

// list(page/per) params를 지원하기 위한 파라미터 주입
// (handler.php는 JSON도 GET/POST도 동일하게 $param으로 넘기므로, 여기서 payload 대신 meta로 page/per를 넣는다)
// - listRecords는 haccpApi에 page/per를 meta로 섞는 방식이므로 아래처럼 보조 래핑한다.
HaccpFormClient.listRecords = async function ({ resourceKey, page = 1, per = 10 }) {
    const body = {
        controller: 'haccpRecords',
        mode: 'list',
        resource_key: resourceKey,
        meta: {},
        page,
        per,
    };

    const res = await fetch('./handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    });
    return res.json();
};

