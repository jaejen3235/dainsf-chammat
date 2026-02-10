    <input type='hidden' id='currentPage' value='1'>
</body>
</html>

<!-- datepicker -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script src="./assets/js/script.js"></script>  
<script src="./assets/js/common.js"></script>

<script>
/*
const btnLogout = document.getElementById('btnLogout');
*/
window.addEventListener('DOMContentLoaded', () => {        
	document.querySelectorAll('.p').forEach(element => {
		const widthValue = element.getAttribute('data-width');
		element.style.width = widthValue + '%';
	});

    try {
        const btnLogout = document.querySelector('.btn-logout');
        if (btnLogout) {
            btnLogout.addEventListener('click', () => {
                location.href = 'logout.php';
            });
        } else {
            console.log('Logout button not found');
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }
   
    // checkbox
	try{
		const chkAll = document.getElementById('chkAll');
		chkAll.addEventListener('click', ()=>{            
			if(chkAll.checked) checkAll('chk');
			else checkAllDisolve('chk');
		});
	} catch(e) {}
});


$(function(){
    $('.datepicker').datepicker();
});

( function( factory ) {
	if ( typeof define === "function" && define.amd ) {
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
}( function( datepicker ) {

	datepicker.regional.ko = {
		closeText: "닫기",
		prevText: "이전달",
		nextText: "다음달",
		currentText: "오늘",
		monthNames: [ "1월","2월","3월","4월","5월","6월",
		"7월","8월","9월","10월","11월","12월" ],
		monthNamesShort: [ "1월","2월","3월","4월","5월","6월",
		"7월","8월","9월","10월","11월","12월" ],
		dayNames: [ "일요일","월요일","화요일","수요일","목요일","금요일","토요일" ],
		dayNamesShort: [ "일","월","화","수","목","금","토" ],
		dayNamesMin: [ "일","월","화","수","목","금","토" ],
		weekHeader: "주",
		dateFormat: "yy-mm-dd",
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: true,
		changeMonth: true, // 월을 바꿀수 있는 셀렉트 박스를 표시한다.
		changeYear: true,
		yearSuffix: "년",
		yearRange: "-80:+0"
	};
	datepicker.setDefaults( datepicker.regional.ko );

	return datepicker.regional.ko;

}));

// 모든 메뉴 항목과 서브메뉴를 가져옵니다.
const menuTitles = document.querySelectorAll('.menu-title');
const submenus = document.querySelectorAll('.submenu');

// 메뉴 클릭 이벤트
menuTitles.forEach((menuTitle, index) => {
    menuTitle.addEventListener('click', function(event) {
        event.preventDefault();
        const submenu = submenus[index];

        // 다른 메뉴들의 active 클래스 제거
        menuTitles.forEach((otherTitle, otherIndex) => {
            if (otherTitle !== menuTitle) {
                otherTitle.classList.remove('active');
                submenus[otherIndex].classList.remove('active');
                submenus[otherIndex].style.height = '0'; // 다른 서브메뉴를 닫습니다.
            }
        });

        // 현재 클릭된 메뉴에 active 클래스 토글
        menuTitle.classList.toggle('active');
        submenu.classList.toggle('active');

        // 서브메뉴 열고 닫기
        if (submenu.classList.contains('active')) {
            submenu.style.height = submenu.scrollHeight + 'px'; // 서브메뉴 열기
        } else {
            submenu.style.height = '0'; // 서브메뉴 닫기
        }
    });
});
</script>
<!-- datepicker -->

<!-- // 우편번호 찾기 ------------------------------------------------------------------------------------------------------->
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
function openAddressModal(target) {
    new daum.Postcode({
        oncomplete: function(data) {

            var roadAddr = data.roadAddress; // 도로명 주소 변수
            var extraRoadAddr = ''; // 참고 항목 변수

            if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                extraRoadAddr += data.bname;
            }
            // 건물명이 있고, 공동주택일 경우 추가한다.
            if(data.buildingName !== '' && data.apartment === 'Y'){
                extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
            }
            // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
            if(extraRoadAddr !== ''){
                extraRoadAddr = ' (' + extraRoadAddr + ')';
            }
		
            switch(target) {
                case 'start' :
                    document.getElementById('startZipcode').value = data.zonecode;
                    document.getElementById("startAddress1").value = roadAddr;
                break;

                case 'destination' :
                    document.getElementById('destinationZipcode').value = data.zonecode;
                    document.getElementById("destinationAddress1").value = roadAddr;
                break;

                default :
                    document.getElementById('zipcode').value = data.zonecode;
                    document.getElementById("address1").value = roadAddr;
                    //document.getElementById("sample4_jibunAddress").value = data.jibunAddress;
                break;
            }
        }
    }).open();
}
</script>