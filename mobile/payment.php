<?php
include "head.php";
?>
    
<main>
    <div class="title-container">
        <div class="title-wrapper">
            <div class="title">요금제 안내</div>            
            <div class="summary">여러분들을 환영합니다.</div>
        </div>
    </div>
    <div class="payment-container">
        <div class="payment-wrapper">
            <div class="btn-section">
                <div class="btn-box">
                    <div class="btn-title">데이터 유형</div>                        
                    <div>
                        <input type="button" class="btn-category group1  active" value="전체" />
                        <input type="button" class="btn-category group1" value="5G" />
                        <input type="button" class="btn-category group1" value="LTE" />
                    </div>
                </div>
                <div class="btn-box">
                    <div class="btn-title">세대 유형</div>                        
                    <div>
                        <input type="button" class="btn-category group2 active" value="전체" />
                        <input type="button" class="btn-category group2" value="전연령" />
                        <input type="button" class="btn-category group2" value="청소년" />
                        <input type="button" class="btn-category group2" value="어르신" />
                    </div>
                </div>
            </div>
            <div class="list-section">
                <div class="table-section">
                    <table id="list">
                        <colgroup>
                            <col width="300" />
                            <col width="100" />
                            <col width="100" />
                            <col width="100" />
                            <col width="150" />
                        </colgroup>
                        <tbody></tbody>                        
                    </table>
                    <div class="paging-area mt20"></div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include "foot.php";
?>

<script>
let dataType = '';
let ageType = '';

// 카테고리 버튼 클릭시
document.querySelectorAll('.group1').forEach(button => {
    button.addEventListener('click', function() {
        // 모든 버튼에서 active 클래스 제거
        document.querySelectorAll('.group1').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // 클릭한 버튼에 active 클래스 추가
        this.classList.add('active');

        // 클릭한 버튼의 value 가져오기
        dataType = (this.value == '전체') ? '' : this.value;        
        getData({page:1});
    });
});

document.querySelectorAll('.group2').forEach(button => {
    button.addEventListener('click', function() {
        // 모든 버튼에서 active 클래스 제거
        document.querySelectorAll('.group2').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // 클릭한 버튼에 active 클래스 추가
        this.classList.add('active');

        // 클릭한 버튼의 value 가져오기
        ageType = (this.value == '전체') ? '' : this.value;   
        getData({page:1});
    });
});


window.addEventListener('DOMContentLoaded', () => {
    getData({page:1});
});

// 상수 정의
const CONTROLLER = 'agency';
const MODE = 'getPaymentList';
const DEFAULT_ORDER_BY = 'uid';
const DEFAULT_ORDER = 'asc';
const NO_DATA_MESSAGE = '검색된 자료가 없습니다';

const getData = async ({ page, per = 13, block = 2, where = 'where 1=1', orderBy = DEFAULT_ORDER_BY, order = DEFAULT_ORDER }) => {
    return new Promise((resolve, reject) => {

        if(dataType != '') {
            where += ` and category='${dataType}'`;
        }

        if(ageType != '') {
            where += ` and age='${ageType}'`;
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
            const tableBody = document.querySelector('#list tbody');
            tableBody.innerHTML = generateTableContent(data);

            getPaging('sk_payment', 'uid', where, page, per, block, 'getData');
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
        return `<tr><td class='center' colspan='8'>${NO_DATA_MESSAGE}</td></tr>`;
    }

    return data.map((item, index) => {
        return `
            <tr>
                <td class="left">
                    <span class="list-title">${item.paymentName}</span>
                </td>
                <td class="center">
                    <div class="data-type">데이터유형</div>
                    <div class="data">${item.category}</div>
                </td>
                <td class="center">
                    <div class="data-type">세대유형</div>
                    <div class="data">${item.age}</div>
                </td>
                <td class="center">
                    <div class="data-type">데이터</div>
                    <div class="data">${item.dataName}</div>
                </td>
                <td class="right">
                    <div><span class="price">${comma(item.payment)}</span> 원</div>
                    <div class="vat">(VAT포함)</div>
                </td>
            </tr>    
        `;
    }).join('');
};
</script>