<?php
include "head.php";
?>
    
<main>
    <div class="title-container">
        <div class="title-wrapper">
            <div class="title">FAQ</div>            
            <div class="summary">여러분들을 환영합니다.</div>
        </div>
    </div>
    <div class="faq-container">
        <div class="faq-wrapper">
            <!--
            <div class="search-section">
                <div class="btn-box">                        
                    <input type="button" class="btn-category  active" value="전체" />
                    <input type="button" class="btn-category" value="개통" />
                    <input type="button" class="btn-category" value="계정" />
                </div>
                <div class="search-box">
                    <input type="text" placeholder="제목" />
                    <i class="fa fa-search" aria-hidden="true"></i>
                </div>
            </div>
            -->
            <div class="list-section">                  
            </div>

            <div class="paging-area mt" data-margin='20'></div>
        </div>
    </div>
</main>

<?php
include "foot.php";
?>

<script>
window.addEventListener('DOMContentLoaded', () => {
    getFaqList({page:1});
});

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
        const selectedValue = this.value;
        console.log(selectedValue);  // 여기에 fetch를 추가하면 됩니다.
        // 예: fetchData(selectedValue);
    });
});

// 필요시 fetchData 함수 정의
function fetchData(value) {
    // fetch 로직을 여기에 추가
}

// 상수 정의
const CONTROLLER = 'agency';
const MODE = 'getFaqList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'asc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getFaqList = async ({ page, per = 13, block = 4, where = 'where 1=1', orderBy = DEFAULT_ORDER_BY, order = DEFAULT_ORDER }) => {
    return new Promise((resolve, reject) => {
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
            const tableBody = document.querySelector('.list-section');
            tableBody.innerHTML = generateTableContent(data);

            getPaging('sk_faq', 'uid', where, page, per, block, 'getFaqList');
            resolve();  // 디바이스 리스트 설정이 완료되면 resolve 호출
        })
        .catch(error => {
            console.error('데이터를 가져오는 중 오류가 발생했습니다:', error);
            reject(error);  // 에러 발생 시 reject 호출
        });
    });
};

const generateTableContent = (data) => {
    const totalCount = data.totalCount;
    const listData = data.data;

    if (!listData || listData.length === 0) {
        return `<div class="faq-box">등록된 FAQ가 없습니다</div>`;
    }

    return listData.map((item, index) => {
        const no = totalCount - index;  // 총 게시물에서 현재 index를 빼서 no 계산

        return `
            <div class="faq-box">
                <div class="question-content">
                    <div>
                        <span class="red">Q.</span>
                        <span class="faq-title">${item.title}</span>
                    </div>
                    <div>
                        <i class="fa fa-chevron-down"></i>
                    </div>
                </div>
                <div class="answer-content">
                    <div class="answer">${item.content}</div>
                </div>
            </div>     
        `;
    }).join('');
};

// 이벤트 위임 방식으로 faq-box 클릭 이벤트 처리
document.querySelector('.list-section').addEventListener('click', (event) => {
    const faqBox = event.target.closest('.faq-box');
    if (!faqBox) return; // faq-box 요소가 아니면 아무것도 하지 않음

    const questionContent = faqBox.querySelector('.question-content');
    const answerContent = faqBox.querySelector('.answer-content');

    // 질문을 클릭했는지 확인
    if (event.target.closest('.question-content')) {
        // 이미 열려있는 다른 답변을 모두 닫음
        document.querySelectorAll('.faq-box').forEach(box => {
            const otherAnswerContent = box.querySelector('.answer-content');
            const otherQuestionContent = box.querySelector('.question-content');
            if (box !== faqBox) {
                otherAnswerContent.classList.remove('open');
                otherAnswerContent.style.maxHeight = null;  // 높이를 초기화해서 닫힘
                otherQuestionContent.classList.remove('active');  // 아이콘 초기화
            }
        });

        // 현재 클릭한 답변 토글
        const isOpen = answerContent.classList.contains('open');
        if (isOpen) {
            answerContent.classList.remove('open');
            answerContent.style.maxHeight = null;  // 닫을 때 높이를 없앰
            questionContent.classList.remove('active');  // 아이콘 회전 원상복귀
        } else {
            answerContent.classList.add('open');
            answerContent.style.maxHeight = answerContent.scrollHeight + 'px';  // 열릴 때 내부 내용 높이에 맞춤
            questionContent.classList.add('active');  // 아이콘 회전
        }
    }
});
</script>
