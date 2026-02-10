</body>
</html>

<!-- datepicker -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
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

function removeComma(n) {  // 콤마제거
	if ( typeof n == "undefined" || n == null || n == "" ) {
		return "";
	}
	var txtNumber = '' + n;
	return txtNumber.replace(/(,)/g, "");
}
</script>    