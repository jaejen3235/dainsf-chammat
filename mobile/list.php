<?php
include "head.php";
?>

<div class="watermark">                   
</div>

<main>
    <div class="swiper-container">
        <div class="swiper-wrapper"></div>            
        <div class="swiper-pagination"></div>
    </div>
    <div class="list-container">
        <div class="search-wrapper">
            <div class='category'></div>    
            <div class="search-section">
                <input type="text" id="searchText" placeholder="기기명/모델명">
                <i class="fa fa-search" aria-hidden="true"></i>
            </div>
        </div>
        <div class="list-wrapper"></div>
        <div class="paging-area mt" data-margin='20'></div>
    </div>
</main>

<?php
include "foot.php";
?>

<script>
let selectedCategory = '';

window.addEventListener('DOMContentLoaded', ()=>{
    if(localStorage.getItem('agency') == '') {
        alert('해당 페이지는 로그인하셔야 접근이 가능합니다.');
        location.href = `index.php`;
    }

    try {
        const btnSearch = document.querySelector('.fa-search');
        btnSearch.addEventListener('click', function() {
            getDeviceList({page:1});
        });
    } catch(e) {}

    try {
        document.getElementById('searchText').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {  // Enter 키를 감지
                getDeviceList({page:1});
            }
        });
    } catch(e) {}

    getSwiper();
    getCategoryList();
    getDeviceList({page : 1});
});

const getSwiper = async () => {
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getBannerList');
    formData.append('orderby', 'seq');
    formData.append('asc', 'asc');

    try {
        const response = await fetch('./webadm/handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setSwiper(data);
    } catch (error) {
        displayError(error);
    }
}

const setSwiper = (data) => {
    const swiperWrapper = document.querySelector('.swiper-wrapper');
    swiperWrapper.innerHTML = ''; // 기존 슬라이드 초기화

    // data가 배열인 경우 각 항목에 대해 처리
    data.forEach(item => {
        const slide = document.createElement('div');
        slide.classList.add('swiper-slide');
        slide.innerHTML = `
            <div class="swiper-slide"><img src="./attach/banner/${item.banner}"></div>
        `;

        swiperWrapper.appendChild(slide);
    });

    // Swiper 재초기화
    const swiper = new Swiper('.swiper-container', {
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
    });
}

const getCategoryList = () => {
	let tag = `<input type="button" class="btn-category  active" value="전체" />`;

    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getCategoryList');

	fetch('./webadm/handler.php', {
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
		if(data != '') {
			data.forEach(item => {
                tag += `<input type="button" class="btn-category" value="${item.categoryName}" />`;
            });
		}

		document.querySelector('.category').innerHTML = tag;

        // 카테고리 버튼 클릭시
        document.querySelectorAll('.btn-category').forEach(button => {
            button.addEventListener('click', function() {
                // 모든 버튼에서 active 클래스 제거
                document.querySelectorAll('.btn-category').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // 클릭한 버튼에 active 클래스 추가
                this.classList.add('active');

                // 클릭한 버튼의 value 가져오기
                selectedCategory = (this.value == '전체') ? '' : this.value;                
                getDeviceList({page:1});
            });
        });
	})
	.catch(error => console.log(error));
}


// 상수 정의
const CONTROLLER = 'agency';
const MODE = 'getDeviceList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'desc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getDeviceList = async ({ page, per = 13, block = 4, where = 'where 1=1', orderBy = DEFAULT_ORDER_BY, order = DEFAULT_ORDER }) => {
    return new Promise((resolve, reject) => {
        try {            
            const searchText = document.getElementById('searchText');

            if(searchText.value != '') {
                where += ` and (deviceName like '%${searchText.value}%' or model like '%${searchText.value}%')`;
            }
        } catch(e) {}

        if(selectedCategory != '') {
            where += ` and category='${selectedCategory}'`;
        }

        const formData = new FormData();
        formData.append('controller', CONTROLLER);
        formData.append('mode', MODE);
        formData.append('where', where);
        formData.append('page', page);
        formData.append('per', per);
        formData.append('orderby', orderBy);
        formData.append('asc', order);

        fetch('./webadm/handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const listWrapper = document.querySelector('.list-wrapper');
            listWrapper.innerHTML = generateTableContent(data);

            getPaging('sk_devices', 'uid', where, page, per, block, 'getDeviceList');
            resolve();  // 디바이스 리스트 설정이 완료되면 resolve 호출
        })
        .catch(error => {
            console.error('데이터를 가져오는 중 오류가 발생했습니다:', error);
            reject(error);  // 에러 발생 시 reject 호출
        });
    });
};

const generateTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<div class='center'>${NO_DATA_MESSAGE}</div>`;
    }

    return data.map(item => `     
        <div class="product-section hands" onclick='detail(${item.uid})'>
            <div class="image-box">
                <img src="./webadm/attach/device/${item.thumb1}">                
            </div>
            <div class="info-box">
                <div class="product-model">${item.model}</div>                
                <div class="product-name">${item.deviceName}</div>
                <div class="product-price">${comma(item.price)}원</div>
            </div>
        </div>
    `).join('');
};

const detail = (uid) => {
    location.href = `view.php?uid=${uid}`;
}
</script>