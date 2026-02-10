window.addEventListener('DOMContentLoaded', () => {
    document.querySelector('.btn-toggle').addEventListener('click', function() {
        const leftContainer = document.querySelector('.left-container');
        const mainContainer = document.querySelector('.main-container');
        const hiddenContainer = document.querySelector('.hidden-container');
        const icon = document.querySelector('.btn-toggle i');
    
        leftContainer.classList.toggle('hidden');
        mainContainer.classList.toggle('menu-hidden');
        hiddenContainer.classList.toggle('menu-hidden');
    
        if (leftContainer.classList.contains('hidden')) {
            icon.classList.remove('bx-chevron-left');
            icon.classList.add('bx-chevron-right');
        } else {
            icon.classList.remove('bx-chevron-right');
            icon.classList.add('bx-chevron-left');
        }
    });
});

const clean = () => {
    const inputs = document.querySelectorAll('.input');
    const selects = document.querySelectorAll('select');
    
    inputs.forEach(input => input.value = '');
    selects.forEach(select => select.selectedIndex = 0);
};

const cleanModal = () => {
    const inputs = document.querySelectorAll('.modal .input');
    const selects = document.querySelectorAll('.modal .select');
    
    inputs.forEach(input => input.value = '');
    selects.forEach(select => select.selectedIndex = 0);
};

function openModal(modalId, w, h) {
	const modal = document.getElementById(modalId);
	const modalContent = modal.querySelector('.modal-content');
	const modalBody = modalContent.querySelector('.modal-body');
	let headerHeight = 67;


	modalContent.style.width = w + 'px';
	modalContent.style.height = h + 'px';
	modalBody.style.height = (h - headerHeight) + 'px';
	modal.style.display = 'block';
}

function closeModal(modalId) {
	const modal = document.getElementById(modalId);
	modal.style.display = 'none';
	//$("html, body").css({"overflow":"auto", "height":"auto"});
	$('.modal').unbind('touchmove');
}

function validateFields(fields) {
    for (let field of fields) {
        const element = document.getElementById(field.id);

        // 체크박스인 경우는 checked 상태를 검사
        if (field.type === 'checkbox') {
            if (!element.checked) {
                alert(field.message);
                element.focus();
                return false;
            }
        }
        // 파일 input인 경우 files 목록 확인
        else if (field.type === 'file') {
            if (element.files.length === 0) {
                alert(field.message);
                element.focus();
                return false;
            }
        }
        // selectbox인 경우 value가 0이면 빈 값으로 간주
        else if (field.type === 'select') {
            if (element.value === '0' || element.value === '' || !element.value) {
                alert(field.message);
                element.focus();
                return false;
            }
        }
        // 일반 input, textarea의 경우 value 값 확인
        else {
            if (!element.value.trim()) {
                alert(field.message);
                element.focus();
                return false;
            }
        }
    }
    return true;  // 모든 필드가 유효한 경우 true 반환
}


// 페이지 이동
function movePage(controller, action, uid) {
    if (!controller) {
        const hasExtension = action.endsWith('.php') || action.endsWith('.html');
        const target = hasExtension ? action : `${action}.php`;
        location.href = uid ? `${target}?uid=${uid}` : target;
        return;
    }

    // uid가 존재하면 &uid=uid 추가, 없으면 생략
    const url = `?controller=${controller}&action=${action}` + (uid ? `&uid=${uid}` : '');
    location.href = url;
}

//==========================================================================================================
// Select box 가져오기
//==========================================================================================================

// 1. 데이터를 HTML option으로 변환하는 공용 함수
const generateOptions = (data, valueField, displayField, code = null) => {    
    // 기본 옵션
    const defaultOption = `<option value='0'>== 선택 ==</option>`;

    // 데이터가 없거나 비어 있으면 기본 옵션 반환
    const items = data.data ? data.data : data;

    if (!items || items.length === 0) {
        return defaultOption;
    }

    // 전달받은 필드명을 사용하여 동적으로 옵션 생성
    const options = items.map(item => {
        // code 인자가 null이 아니고 item에 code 존재 시 품명 뒤에 (코드)를 출력, 아니면 그냥 품명만 출력
        let displayText = item[displayField];
        if (code !== null) {
            displayText += ` (${item.item_code})`;
        }
        return `<option value='${item[valueField]}'>${displayText}</option>`;
    }).join('');

    return defaultOption + options;
};


// 2. DOM 업데이트 함수
const updateDropdown = (htmlString, targetSelector) => {
    const target = document.querySelector(targetSelector);
    target.innerHTML = htmlString;
};

// 3. 비동기 함수에서 공용 함수를 사용
const getSelectList = async (mode, valueField, displayField, targetSelector, where = null, code = null) => {  
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', mode);

    if(where) {
        formData.append('where', where);
    }

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        

        // 공용함수 사용 - 필드명을 동적으로 적용
        const optionsHtml = generateOptions(data, valueField, displayField, code);
        updateDropdown(optionsHtml, targetSelector);

    } catch (error) {
        displayError(error);
    }
}
//==========================================================================================================
// Select box 가져오기 끝
//==========================================================================================================

const displayError = (error) => {
    console.error('Error:', error);
}

const cleanSpan = () => {
    document.querySelectorAll('.span').forEach(span => {
        span.innerHTML = '&nbsp;';
    });    
}
