window.addEventListener('DOMContentLoaded', () => {	
    const logoSection = document.querySelector('.logo-section');
    console.log(logoSection); // .logo-section 요소가 출력되는지 확인
    
    if (logoSection) {
        logoSection.addEventListener('click', () => {
            location.href = `./index.php`;
        });
    }

    try {
        const hamburgerMenu = document.querySelector('.hamburger-menu');
        const mobileMenu = document.querySelector('.mobile-menu');
        const closeMenu = document.querySelector('.close-menu');

        hamburgerMenu.addEventListener('click', function() {
            mobileMenu.classList.add('active');  // 햄버거 메뉴 클릭 시 mobile-menu 활성화
        });

        closeMenu.addEventListener('click', function() {
            mobileMenu.classList.remove('active');  // 닫기 버튼 클릭 시 mobile-menu 비활성화
        });
    } catch(e) {}
});

//콤마찍기
const comma = (str) => {
    str = String(str);
    return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
}


// 페이징 가져오기
function getPaging(table, select, where, page, per, block, setPage){
    const formData = new FormData();
    formData.append('controller', 'functions');
    formData.append('mode', 'getPaging');
    formData.append('table', table);
    formData.append('select', select);
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('block', block);
    formData.append('setPage', setPage);

	fetch('../handler.php', {
		method: 'post',
		body : formData
	})
	.then(response => response.json())
	.then(function(response) {
		document.querySelector('.paging-area').innerHTML = response.result;
	})
	.catch(error => console.log(error));
}

// 원하는 곳에 페이징 넣기
function getPagingTarget(table, select, where, page, per, block, setPage, target){
    const formData = new FormData();
    formData.append('controller', 'functions');
    formData.append('mode', 'getPaging');
    formData.append('table', table);
    formData.append('select', select);
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('block', block);
    formData.append('setPage', setPage);

	fetch('../handler.php', {
		method: 'post',
		body : formData
	})
	.then(response => response.json())
	.then(function(response) {
		if(response != null) document.querySelector('.' + target).innerHTML = response.result;
	})
	.catch(error => console.log(error));
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
            if (element.value === '0') {
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
    // uid가 존재하면 &uid=uid 추가, 없으면 생략
    const url = `?controller=${controller}&action=${action}` + (uid ? `&uid=${uid}` : '');
    location.href = url;
}

//==========================================================================================================
// Select box 가져오기
//==========================================================================================================

// 1. 데이터를 HTML option으로 변환하는 공용 함수
const generateOptions = (data, valueField, displayField) => {
    // 기본 옵션
    const defaultOption = `<option value='0'>== 선택 ==</option>`;

    // data가 없거나 비어있으면 기본 옵션만 반환
    if (!data || data.length === 0) {
        return defaultOption;
    }

    // 전달받은 필드명을 사용하여 동적으로 옵션 생성
    const options = data.map(item => `
        <option value='${item[valueField]}'>${item[displayField]}</option>
    `).join('');

    return defaultOption + options;
};

// 2. DOM 업데이트 함수
const updateDropdown = (htmlString, targetSelector) => {
    const target = document.querySelector(targetSelector);
    target.innerHTML = htmlString;
};

// 3. 비동기 함수에서 공용 함수를 사용
const getSelectList = async (mode, valueField, displayField, targetSelector) => {  
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', mode);

    try {
        const response = await fetch('../handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();

        // 공용함수 사용 - 필드명을 동적으로 적용
        const optionsHtml = generateOptions(data, valueField, displayField);
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