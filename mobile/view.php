<?php
include "head.php";
?>

<div class="watermark">                   
</div>

<main>
    <div class="title-container">
        <div class="title-wrapper">
            <div class="title">제휴기업 복지몰</div>            
            <div class="summary">여러분들을 환영합니다.</div>
        </div>
    </div>
    <div class="view-container">
        <div class="view-wrapper">
            <div class='info-section'>
                <div class='image-box'>
                    <div class='big-image'></div>
                    <div class='thumbnail-image'></div>
                </div>
                <div class='price-box'>
                    <div class='device-model'></div>
                    <div class='device-name'></div>
                    <hr class='b2' />
                    <div class='block-title'>요금할인유형</div>
                    <div class='btn-content'>
                        <button class='btn-type1 hands active' value='1'>선택약정 할인</button>
                        <button class='btn-type1 hands' value='2'>공시지원금 할인</button>
                    </div>
                    <div class='block-title'>가입유형</div>
                    <div class='btn-content'>
                        <button class='btn-type2 hands active' value='1'>기기 변경</button>
                        <button class='btn-type2 hands' value='2'>통신사 이동</button>
                        <button class='btn-type2 hands' value='3'>신규 가입</button>
                    </div>
                    <div class='block-title'>세대 구분</div>
                    <div class='btn-content'>
                        <button class='btn-type3 hands age1'>전연령</button>
                        <button class='btn-type3 hands age2'>어르신</button>
                        <button class='btn-type3 hands age3'>청소년</button>
                    </div>
                    <div class='price-content'>
                        <div class='price-title'>요금제</div>
                        <div>
                            <select id='payment'>
                                <option>==선택==</option>
                            </select>
                        </div>
                    </div>
                    <div class='price-content'>
                        <div class='price-title'>출고가</div>
                        <div class='price' id='devicePrice'></div>
                    </div>
                    <div class='price-content hiddenRow1'>
                        <div class='price-title'>공시지원금</div>
                        <div class='price' id="supportFund"></div>
                    </div>
                    <div class='price-content'>
                        <div class='price-title'>제휴할인</div>
                        <div class='price' id='discount'></div>
                    </div>
                    <hr class='b1' />
                    <div class='price-content'>
                        <div class='price-title'>할부원금</div>
                        <div class='price' id="mip"></div>
                    </div>

                    <div class='summary-content'>
                        <div class='device-name'>Galaxy s23+ 512G</div>
                        <div class='payment-name'></div>
                        <hr class='b1'>
                        <div class='price-card'>
                            <div class='price-title bold black'>월 단말기 할부금</div>
                            <div class='price bold black' id='deviceMip'></div>
                        </div>
                        <div class='price-card'>
                            <div class='price-title'>월 할부이자</div>
                            <div class='price' id='deviceRateMip'></div>
                        </div>
                        <hr class='b3'>
                        <div class='price-card'>
                            <div class='bold black'>월 통신요금</div>
                            <div class='bold black' id='mcp'></div>
                        </div>
                        <div class='price-card hiddenRow2'>
                            <div>선택약정 요금할인 25%</div>
                            <div id='calChoiceContractSales'></div>
                        </div>
                        <hr class='b3'>
                        <div class='price-card'>
                            <div class='total'>월 납부금 합계</div>
                            <div class='total' id='totalPrice'></div>
                        </div>
                        <div class='right'>
                            월 할부금() + 월 통신요금 ()
                        </div>
                    </div>
                    <div class='btn-section'>
                        <input type='button' id='btnCounseling' value='상담하기' />
                        <input type='button' id='btnBuy' value='구매하기' />
                    </div>
                </div>
                
            </div>
            <hr class='b0' />
            <div class='content-section'>
                <div class='ad-box'>휴대폰 + 인터넷 + IPTV 지원금 65만원 지원</div>
                <div class='content-box'></div>
            </div>
        </div>
    </div>
</main>

<input type='hidden' id='uid' value="<?=$_GET['uid']?>" />

<?php
include "foot.php";
?>

<script>
let changeDevice = 0; // 기기변경
let moveTelecom = 0; // 통신사이동
let joinTelecom = 0; // 신규가입
let supportFund = 0; // 공시지원금
let supportChangeDevice = 0; // 공시지원기기변경
let supportMoveTelecom = 0; // 공시지원통신사이동
let supportJoinTelecom = 0; // 공시지원신규가입
let paymentName = '';
let paymentData = []; // payment 데이터를 전역 변수에 저장

let calDevicePrice = 0; // 기기가격
let calSupportFund = 0; // 공시지원금
let calDiscount = 0; // 기기 할인 금액
let calChoiceContractSales = 27250; // 선택약정할인금액
let calDevieRate = 0.0625; // 단말기 2년 할부 적용
let calCommunicationPrice = 0; // 통신요금
let calCommunicationDiscount = 0;
let deviceMip = 0;
let deviceRateMip = 0; 

let tab1 = '1';
let tab2 = '1';
let tab3 = '1';

const payment = document.getElementById('payment');

document.querySelectorAll('.btn-type1').forEach(button => {
    button.addEventListener('click', function() {
        // 모든 버튼에서 active 클래스 제거
        document.querySelectorAll('.btn-type1').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // 클릭한 버튼에 active 클래스 추가
        this.classList.add('active');

        // 클릭한 버튼의 value 가져오기
        tab1 = this.value;

        if(tab1 == '1') {
            localStorage.setItem('paymentType', '선택약정 할인');
        } else if(tab1 == '2') {
            localStorage.setItem('paymentType', '공시지원금 할인');
        }
        changePayment();
    });
});

document.querySelectorAll('.btn-type2').forEach(button => {
    button.addEventListener('click', function() {
        // 모든 버튼에서 active 클래스 제거
        document.querySelectorAll('.btn-type2').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // 클릭한 버튼에 active 클래스 추가
        this.classList.add('active');

        // 클릭한 버튼의 value 가져오기
        tab2 = this.value;

        if(tab2 == '1') {
            localStorage.setItem('joinType', '전연령');
        } else if(tab2 == '2') {
            localStorage.setItem('joinType', '어르신');
        } else if(tab2 == '3') {
            localStorage.setItem('joinType', '청소년');
        }

        changePayment();
    });
});

window.addEventListener('DOMContentLoaded', () => {
    // 로그인 된 사람만 접근 가능하도록 처리
    localStorage.setItem('paymentType', '선택약정 할인');
    localStorage.setItem('joinType', '전연령');

    const uid = document.getElementById('uid');
    if(uid) {
        getData(uid.value);
    } else {
        console.log('Uid Not Found!!');
    }

    const btnCounseling = document.getElementById('btnCounseling');
    btnCounseling.addEventListener('click', ()=>{
        saveData();        
    });

    const btnBuy = document.getElementById('btnBuy');
    btnBuy.addEventListener('click', ()=>{
        buy();        
    });

    payment.addEventListener('change', changePayment);
});

const buy = () => {
    if(confirm('구매신청 하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'agency');
        formData.append('mode', 'registerBuy');
        formData.append('device', localStorage.getItem('device')); // 선택한 기기
        formData.append('deviceName', localStorage.getItem('deviceName')); // 선택한 기기
        formData.append('deviceModel', localStorage.getItem('deviceModel')); // 선택한 기기
        formData.append('tab1', localStorage.getItem('tab1')); // 요금할인유형
        formData.append('tab2', localStorage.getItem('tab2')); // 가입유형
        formData.append('age', localStorage.getItem('age')); // 가입유형
        formData.append('payment', localStorage.getItem('payment')); // 선택한 요금제
        formData.append('deviceMip', localStorage.getItem('deviceMip')); // 단말기 월 할부금
        formData.append('deviceRateMip', localStorage.getItem('deviceRateMip')); // 단말기 할부금 월 이자
        formData.append('communicationPrice', localStorage.getItem('calCommunicationPrice')); // 월 통신요금
        formData.append('choiceContractSales', localStorage.getItem('calChoiceContractSales')); // 선택약정 요금할인
        formData.append('totalPrice', localStorage.getItem('totalPrice')); // 선택약정 요금할인
        formData.append('agency', localStorage.getItem('agency')); // 선택약정 요금할인
        formData.append('client', localStorage.getItem('client')); // 선택약정 요금할인
        formData.append('mobile', localStorage.getItem('mobile')); // 선택약정 요금할인

        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);  // 현재 인덱스의 키 가져오기
            const value = localStorage.getItem(key);  // 해당 키에 해당하는 값 가져오기
            console.log(key + ": " + value);  // 키와 값을 콘솔에 출력
        }

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
            if(data != null) {
                if(data.result == 'success') {
                    location.href = `https://tgateplus.sktelecom.com/main/main.do?p=8e6deed4fe42efe9a6e570ee7fd1182ee1b9b116e6f50e7137b4edc19ca2f0d8`;
                }
            }
        })
        .catch(error => console.log(error));
    }
}

const saveData = () => {
    location.href = 'counseling.php';
}

const getData = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'agency');
    formData.append('mode', 'getDevicePayment');
    formData.append('uid', uid);

    try {
        const response = await fetch('./webadm/handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setData(data);
    } catch (error) {
        displayError(error);
    }
}

const setData = (data) => {
    if (data) {       
        localStorage.setItem('deviceName', data.deviceName);
        localStorage.setItem('deviceModel', data.model);

        document.querySelector('.big-image').innerHTML = `<img src='./attach/device/${data.thumb1}' id='bigImage' class='big' />`;

        let thumbnail = `<img src='./attach/device/${data.thumb1}' class='thumbnail hands' onclick="changeImg('${data.thumb1}')" />`;
        thumbnail += `<img src='./attach/device/${data.thumb2}' class='thumbnail hands' onclick="changeImg('${data.thumb2}')" />`;
        thumbnail += `<img src='./attach/device/${data.thumb3}' class='thumbnail hands' onclick="changeImg('${data.thumb3}')" />`;

        document.querySelector('.thumbnail-image').innerHTML = thumbnail;
        document.querySelector('.content-box').innerHTML = `<img src='./attach/device/${data.content1}' />`;

        // 출고가
        document.getElementById('devicePrice').innerText = `${comma(data.price)}원`;
        devicePrice = data.price;
        calDevicePrice = data.price;
        
        paymentData = data.data; // 받아온 데이터를 전역 변수에 저장

        let i = 0;
        let tag = '';
        
        data.data.forEach(item => {
            tag += `<option value='${item.paymentName}'>${item.paymentName}</option>`;            
        });

        document.getElementById('payment').innerHTML = tag;
        changePayment();
    }
}

// 특정 paymentName을 기준으로 값을 찾기
const findPaymentData = (paymentName) => {
    return paymentData.find(item => item.paymentName === paymentName);
}

const changePayment = async () => {
    const paymentName = document.getElementById('payment').value;
    document.querySelector('.payment-name').innerText = paymentName;
    const result = findPaymentData(paymentName);

    // 요금제 가져오는 fetch
    await getPayment(paymentName);


    changeDevice = result.changeDevice; // 기기변경
    moveTelecom = result.moveTelecom; // 통신사이동
    joinTelecom = result.joinTelecom; // 신규가입
    supportFund = result.supportFund; // 공시지원금
    supportChangeDevice = result.supportChangeDevice; // 공시지원기기변경
    supportMoveTelecom = result.supportMoveTelecom; // 공시지원통신사이동
    supportJoinTelecom = result.supportJoinTelecom; // 공시지원신규가입

    // 공시지원금
    document.getElementById('supportFund').innerText = `-${comma(supportFund)}원`;

    // 통신요금 선택약정할인 금액
    document.getElementById('calChoiceContractSales').innerText = `-${comma(calChoiceContractSales)}원`;
    
    switch(tab1) {
        case "1" : // 선택약정할인
            document.querySelector('.hiddenRow1').style.display = 'none';
            document.querySelector('.hiddenRow2').style.display = 'flex';
            calSupportFund = 0;
            calCommunicationDiscount = calChoiceContractSales;

            if(tab2 == '1') {
                document.getElementById('discount').innerText = `-${comma(changeDevice)}원`;
                calDiscount = changeDevice;
            } else if(tab2 == '2') {
                document.getElementById('discount').innerText = `-${comma(moveTelecom)}원`;
                calDiscount = moveTelecom;
            } else if(tab2 == '3') {
                document.getElementById('discount').innerText = `-${comma(joinTelecom)}원`;
                calDiscount = joinTelecom;
            }
            break;

        case "2" : // 공시지원금 할인
            document.querySelector('.hiddenRow1').style.display = 'flex';
            document.querySelector('.hiddenRow2').style.display = 'none';
            calSupportFund = supportFund; // 공시지원금
            calCommunicationDiscount = 0;

            if(tab2 == '1') {
                document.getElementById('discount').innerText = `-${comma(supportChangeDevice)}원`;
                calDiscount = supportChangeDevice;
            } else if(tab2 == '2') {
                document.getElementById('discount').innerText = `-${comma(supportMoveTelecom)}원`;
                calDiscount = supportMoveTelecom;
            } else if(tab2 == '3') {
                document.getElementById('discount').innerText = `-${comma(supportJoinTelecom)}원`;
                calDiscount = supportJoinTelecom;
            }            
            break;
    }

    // 데이터를 localStorage 에 저장한다
    // 선택한 기기
    localStorage.setItem('device', document.getElementById('uid').value);
    // 요금할인유형
    localStorage.setItem('tab1', tab1);
    // 가입유형
    localStorage.setItem('tab2', tab2);
    // 선택한 요금제
    localStorage.setItem('payment', document.getElementById('payment').value);

    cal();
}

const cal = () => {
    // 단말기 금액 계산
    let mip = 0; // monthly installment plan (할부원금)
    mip = Number(calDevicePrice) - Number(calSupportFund) - Number(calDiscount);
    document.getElementById('mip').innerText = `${comma(mip)}원`;

    deviceMip = Math.floor(mip / 24);  // 소수점 이하 절삭
    deviceRateMip = Math.floor(deviceMip * calDevieRate);  // 소수점 이하 절삭

    document.getElementById('deviceMip').innerText = `${comma(deviceMip)}원`;
    document.getElementById('deviceRateMip').innerText = `${comma(deviceRateMip)}원`;


    //console.log(`단말기 할부금 : ${comma(deviceMip)}`);
    localStorage.setItem('deviceMip', deviceMip);

    //console.log(`월 할부이자 : ${comma(deviceRateMip)}`);
    localStorage.setItem('deviceRateMip', deviceRateMip);

    //console.log(`월 통신요금 : ${comma(calCommunicationPrice)}`);
    localStorage.setItem('calCommunicationPrice', calCommunicationPrice);

    //console.log(`선택약정 요금할인 : ${comma(calChoiceContractSales)}`);
    localStorage.setItem('calChoiceContractSales', calChoiceContractSales);

    let totalPrice = Number(deviceMip) + Number(deviceRateMip) + Number(calCommunicationPrice) - Number(calChoiceContractSales);
    document.getElementById('totalPrice').innerText = `${comma(totalPrice)}원`;

    // 합계금액
    localStorage.setItem('totalPrice', totalPrice);
}

const changeImg = (url) => {
    document.getElementById('bigImage').src = `./attach/device/${url}`;
}

const displayError = (e) => {
    console.log(e);
}

// 요금제 요금 가져오기
const getPayment = (payment) => {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('controller', 'agency');
        formData.append('mode', 'getPaymentPrice');
        formData.append('payment', payment);

        fetch('./webadm/handler.php', {
            method: 'post',
            body : formData
        })
        .then(response => {
            if (!response.ok) {
                reject('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(function(data) {
            if(data != '') {
                if(data.result == 'success') {
                    document.getElementById('mcp').innerText = `${comma(data.payment)}원`;
                    calCommunicationPrice = Number(data.payment);   

                    // 모든 연령대 클래스에서 'active' 제거
                    document.querySelector('.age1').classList.remove('active');
                    document.querySelector('.age2').classList.remove('active');
                    document.querySelector('.age3').classList.remove('active');

                    // 나이에 따라 'active' 클래스 추가
                    if (data.age === '전연령') {
                        document.querySelector('.age1').classList.add('active');
                        localStorage.setItem('age', '전연령');
                    } else if (data.age === '어르신') {
                        document.querySelector('.age2').classList.add('active');
                        localStorage.setItem('age', '어르신');
                    } else if (data.age === '청소년') {
                        document.querySelector('.age3').classList.add('active');
                        localStorage.setItem('age', '청소년');
                    }
                    resolve(); // 성공 시 resolve 호출
                } else {
                    console.log(data.message);
                    reject(data.message); // 실패 시 reject 호출
                }
            }
        })
        .catch(error => reject(error));
    });
};
</script>