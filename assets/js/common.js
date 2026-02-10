/// 오직 숫자만...전화번호 같은 곳에 이용
 $(".only-number").keypress(function (event) {
	if (event.which && (event.which < 48 || event.which > 57)) {   //숫자만 받기
		event.preventDefault();
	}
}).keyup(function () {
	if ($(this).val() != null && $(this).val() != '') {
		var text = $(this).val().replace(/[^0-9]/g, '');
		$(this).val(text);
	}
});

$(document).ready(function() {
	$(".number").keyup(function(){$(this).val( $(this).val().replace(/[^0-9]/g,"") );} );
	/*
	$("input").on("click", function(){
		$(this).select();
	});
	*/
});

$(function() {
	// 투자목표금액 comma
	$("input.number").on("keyup", function() {
		var val = String(this.value.replace(/[^0-9]/g, ""));

		if(val.length < 1)
		return false;

		this.value = number_format(val);
	});
});

// 팝업창 띄우기
function registPopup(title, controller, action, w, h) { // 팝업타이틀, 컨트롤러, 액션, 필드, 값, 넓이, 높이
	// 팝업을 가운데 위치시키기 위해 아래와 같이 값 구하기
	var _left = Math.ceil(( window.screen.width - w )/2);
	var _top = 0;
	//var _top = Math.ceil(( window.screen.height - h )/2); 
	window.open("popup.php?title=" + title + "&controller=" + controller + "&action=" + action , "popup", "width=" + w + ", height=" + h + ", left=" + _left + ", top=" + _top);
}

// 팝업창 띄우기
function viewPopup(title, controller, action, field, v, w, h) { // 팝업타이틀, 컨트롤러, 액션, 필드, 값, 넓이, 높이
	// 팝업을 가운데 위치시키기 위해 아래와 같이 값 구하기
	var _left = Math.ceil(( window.screen.width - w )/2);
	var _top = 0;
	//var _top = Math.ceil(( window.screen.height - h )/2); 
	window.open("popup.php?title=" + title + "&controller=" + controller + "&action=" + action + "&" + field + "=" + v, "popup", "width=" + w + ", height=" + h + ", left=" + _left + ", top=" + _top);
}

// INPUT 에 입력되는 숫자에 comma 붙이기
function number_format(data)
{
	var tmp = '';
	var number = '';
	var cutlen = 3;
	var comma = ',';
	var i;
	var sign = data.match(/^[\+\-]/);
	if(sign) {
		data = data.replace(/^[\+\-]/, "");
	}
	len = data.length;
	mod = (len % cutlen);
	k = cutlen - mod;
	for (i=0; i<data.length; i++)
	{
		number = number + data.charAt(i);
		if (i < data.length - 1)
		{
			k++;
			if ((k % cutlen) == 0)
			{
				number = number + comma;
				k = 0;
			}
		}
	}
	if(sign != null) number = sign+number;
	return number;
}

$(function() {
	$("input.comma").on("keyup", function() {
		var val = String(this.value.replace(/[^0-9]/g, ""));
		if(val.length < 1)
			return false;
			this.value = number_format(val);
		});
	}
);

//콤마찍기
function comma(str) {
    str = String(str);
    return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
}

//콤마풀기
function uncomma(str) {
    str = str.replace(/,/g, ''); // 쉼표 제거
    return str;
}

function check(frm){
	let valid = true;
	const formName = document.querySelector('#' + frm);
	const data = formName.querySelectorAll('input, textarea, select');
	data.forEach(function(e) {
		if(e.getAttribute('validation') === 'yes') {
			if(isEmpty(e.value)){
				alert(e.getAttribute('err'));
				e.style.border = '2px solid #f50057';
				valid = false;				
				return false;
			} else {
				e.style.border = '1px solid #ddd';
			}
		}
	});
	
	if(valid == true) return true;
	else return false;
}

// 공백검사 함수 (false 일 경우 공백이 아님)
function isEmpty(val) {
	if(val == null || typeof val == 'undefind' || val.trim().length < 1) {
		return true;
	}
	return false;
}

function checkIdPattern(){
	var pattern = /^[a-z]+[a-z0-9_]+[a-z0-9_]$/;  // ^ 는 시작하는 문자 $ 는 끝나는 문자 
	   
	   // 아이디 체크 
	var id = $.trim($('#id').val());  //  jQuery를 이용하여 앞뒤 공백 제거 
	if(id=="") { 
		alert("아이디를 입력하세요!"); 
		$('#id').focus(); 
		return false; 
	} else { 
		if(!pattern.test(id)) { // test 는 패턴에 맞으면 true, 맞지 않으면 false 값 return
		alert("아이디는 영문소문자로 시작하고\r\n영문소문자, 숫자, 언더바(_)만 사용하실 수 있습니다! "); 
		$("#id").val("").focus();
			 return false; 
		} 
	}

	return true;
}

function checkPwdPattern(){
	var num = /^[0-9]+$/;    
	   
	// 비밀번호 체크 
	var pss = $.trim($('#pwd').val()); // jQuery를 이용하여 앞뒤 공백 제거 
	if(pss=="") { 
		alert("비밀번호를 입력하세요!"); 
		$('#pwd').focus();   
		return false; 
	} else { 
		if(!num.test(pss)) { 
			alert("비밀번호는 숫자만 가능합니다!");
			$("#pwd").val("");
			$('#pwd').focus();
			return false; 
		} 
	}

	return true;
}

// 브라우저에 따라 체크되는 방식이 다르기 때문에 original script 로 처리
function check_str(str,txt){
	//var str = document.getElementById(str);

	if( str == '' || str == null ){
		alert( txt + '을(를) 입력해주세요' );
		return false;
	}

	var blank_pattern = /^\s+|\s+$/g;
	if( str.replace( blank_pattern, '' ) == "" ){
		alert(' 공백만 입력되었습니다 ');
		return false;
	}

	return true;
}

// 브라우저에 따라 체크되는 방식이 다르기 때문에 original script 로 처리
function check_str2(str,txt){
	//var str = document.getElementById(str);

	if( str == '' || str == null ){
		alert( txt + '을(를) 입력해주세요' );
		return false;
	}

	var blank_pattern = /^\s+|\s+$/g;
	if( str.replace( blank_pattern, '' ) == "" ){
		alert(' 공백만 입력되었습니다 ');
		return false;
	}

	//공백 금지
	//var blank_pattern = /^\s+|\s+$/g;(/\s/g
	/*var blank_pattern = /[\s]/g;
	if( blank_pattern.test(str) == true){
		alert(' 공백은 사용할 수 없습니다. ');
		return false;
	}*/


	var special_pattern = /[`~!#$%^&*|\\\'\";:\/?]/gi;

	if( special_pattern.test(str) == true ){
		alert('특수문자는 사용할 수 없습니다.');
		return false;
	}
	
	return true;
}

function check_empty(str) {
	var blank_pattern = /^\s+|\s+$/g;
	if( str.replace( blank_pattern, '' ) == "" ){
		alert(' 공백만 입력되었습니다 ');
		return false;
	}

	return true;
}

function removeComma(n) {  // 콤마제거
	if ( typeof n == "undefined" || n == null || n == "" ) {
		return "";
	}
	var txtNumber = '' + n;
	return txtNumber.replace(/(,)/g, "");
}

function isNum(s) {
	s += ''; // 문자열로 변환
	s = s.replace(/^\s*|\s*$/g, ''); // 좌우 공백 제거
	if (s == '' || isNaN(s)) return false;
	return true;
}

// 페이지 세트
function setPage(page){
	$("#page").val(page);
	getData(page);
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

	fetch('./handler.php', {
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

	fetch('./handler.php', {
		method: 'post',
		body : formData
	})
	.then(response => response.json())
	.then(function(response) {
		if(response != null) document.querySelector('.' + target).innerHTML = response.result;
	})
	.catch(error => console.log(error));
}

// 선택 삭제
function deleteSelect(table){
	$(".chk").each(function(){
		if($(this).prop('checked')) {
			var new_uid = $("#check_uids").val() + "," + $(this).val();
			$("#check_uids").val(new_uid);
		}
	});
	
	if($("#check_uids").val() == "") {
		alert("삭제할 데이터를 선택하세요");
		return;
	}
	
	var controller ="functions";
	var mode = "deleteSelect";
	var uids = $("#check_uids").val();
	var parameter = {controller : controller, mode : mode, table : table, uids : uids};
	$.ajax({
		type : "post",
		url : "ajax.php",
		data : parameter,
		async : false,
		success : function(){
			$("#checkedAll").prop('checked',false);
			$("#check_uids").val("");
			getData(1);
		}
	});
}

function showAlert(txt) {
	$("#message").html(txt);
	$("#alertModal").modal("show");
}

function showModal(modal_nm) {
	$("#" + modal_nm).modal('show');
}

function hideModal(modal_nm) {
	$("#" + modal_nm).modal("hide");
}

function lastStringCut(str) {
	var ret = str.substr(0, str.length -1)
	return ret;
}

// n을 0으로 채우기
function numberPad(n, width) {
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join('0') + n;
}

// 전화번호 하이픈 붙이기
function phonePad(n) {
	return n.replace(/(^02.{0}|^01.{1}|[0-9]{3})([0-9]+)([0-9]{4})/,"$1-$2-$3");
}

// 오늘 날짜 가져오기
function getToday() {
	let today = new Date();   

	let year = today.getFullYear(); // 년도
	let month = today.getMonth() + 1;  // 월
	let date = today.getDate();  // 날짜
	let day = today.getDay();  // 요일
	let re = year + '-' + numberPad(month, 2) + '-' + numberPad(date, 2);
	return re;
}
// 오늘의 날짜를 해당 Input 에 세팅
function setDate(id) {
	document.querySelector('#' + id).value = getToday();
}
function setTime(id) {
	let d = new Date();
	let tag;
	let txt;
	let selected;
	for(let i = 0 ; i < 23 ; i++) {
		if(i < 10) txt = '0' + i;
		else txt = i;
		if(i == d.getHours()) selected = 'selected';
		else selected = '';
		tag += `<option value='${txt}' ${selected}>${txt}시</option>`;
	}
	document.querySelector('#' + id).innerHTML = tag;
}
function setMinute(id) {
	let d = new Date();
	let tag;
	let txt;
	let selected;
	for(let i = 0 ; i < 60 ; i++) {
		if(i < 10) txt = '0' + i;
		else txt = i;
		if(i == d.getMinutes()) selected = 'selected';
		else selected = '';
		tag += `<option value='${txt}' ${selected}>${txt}분</option>`;
	}
	document.querySelector('#' + id).innerHTML = tag;
}
// 해당월의 첫째날 가져오기
function getFirstDate() {
	let today = new Date();   

	let year = today.getFullYear(); // 년도
	let month = today.getMonth() + 1;  // 월
	let date = today.getDate();  // 날짜

	let re = year + '-' + numberPad(month, 2) + '-' + numberPad(1, 2);
	return re;
}

// 요일 리턴
function dayOfWeek(str) {
	let week = ['일', '월', '화', '수', '목', '금', '토'];
	let dayOfWeek = week[new Date(str).getDay()];
	return dayOfWeek;
}

// 요일 리턴
function checkHoliday(str) {
	let week = ['일', '월', '화', '수', '목', '금', '토'];
	let a = new Date(str).getDay();
	if(a == 0 || a == 6) {
		return "휴일";
	} else {
		return "평일";
	}
}

// 해당월의 마지막 날짜 구하기
function lastDateOfMonth() {
	// 3월 1일
	let date = new Date(2019, 2, 1); // 마지막의 1을 하루를 더한다는 소리다
	document.write(date.toLocaleString()  + '<br>');

	// 2월 28일(말일)
	let lastDate = new Date(2019, 2, 0);
	document.write(lastDate.toLocaleString()  + '<br>');
	document.write(lastDate.getDate() + '<br>');

	// 2월 27일
	let lastDateBefore = new Date(2019, 2, -1);
	document.write(lastDateBefore.toLocaleString()  + '<br>');
}

// placeholder reset
function resetPlaceholder(id, str) {
	const t = document.querySelector('#' + id);
	t.value = '';
	t.placeholder = str;
}

function getByteLength(str) {
	let len = 0;
	for (let i = 0 ; i < str.length ; i++) {
		if (escape(str.charAt(i)).length == 6) {
			len++;
		}
		len++;
	}
	return len;
}

// 폼에 있는 값들을 배열로 넘겨받아 초기화 시키기
function formReset() {
	const radioGroups = {};

	// 텍스트 입력 필드 초기화
	document.querySelectorAll('.text').forEach((elem) => {
		elem.value = '';
	});

	// 셀렉트 박스 초기화
	document.querySelectorAll('.selectbox').forEach((elem) => {
		elem.options[0].selected = true;
	});

	// 라디오 버튼 초기화 (같은 name 속성 그룹에서 첫 번째 항목 선택)
	document.querySelectorAll('.radio').forEach((elem) => {
		const groupName = elem.name;
		if (!radioGroups[groupName]) {
			radioGroups[groupName] = true;
			elem.checked = true;
		}
	});

	// 체크박스 초기화 (해제)
	document.querySelectorAll('.checkbox').forEach((elem) => {
		elem.checked = false;
	});
}

// Table 의 Row 삭제
// deleteRow(this) 로 호출
function deleteRow(e) {
	 e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode);
	
	// row 의 카운터를 줄여준다
	 try
	 {
		row--;
	 }
	 catch (e)
	 {
		 console.log(e);
	 }
}

function checkAll(chk) {
	document.querySelectorAll(`.${chk}`).forEach((elem, index) => {
		elem.checked = true;
	});
}

function checkAllDisolve(chk) {
	try {
		document.querySelector('#chkAll').checked = false;
	} catch (error) {
		console.error(error); // 오류 메시지를 콘솔에 출력
	    // 또는 alert("오류가 발생했습니다"); 등의 코드를 사용하여 경고창 표시
	}

	document.querySelectorAll(`.${chk}`).forEach((elem, index) => {
		elem.checked = false;
	});
}

// 버튼 작동 제어
function button(btn, text, removeClass, addClass) {
	var button = document.getElementById(btn);
	button.value = text;
	if (removeClass) {
		button.classList.remove(removeClass);
	}
	if (addClass) {
		button.classList.add(addClass);
	}
	button.disabled = !button.disabled;
}

function isValidPhoneNumber(phoneNumber) {
	// 휴대폰 번호의 유효성을 검사하기 위한 정규식
	var pattern = /^(010|011|016|017|018|019)-[0-9]{3,4}-[0-9]{4}$/;

	// 정규식을 통해 유효성 검사
	if (pattern.test(phoneNumber)) {
		return true; // 유효한 휴대폰 번호
	} else {
		return false; // 유효하지 않은 휴대폰 번호
	}
}

// input 값 비우기
const clearField = (field) => {
    document.getElementById(field).value = '';
}

// input 에서 enter 키 막기 위함
const preventEnterKey = (event) => {
    if (event.key === "Enter") {
        event.preventDefault();  // 기본 동작 차단 (submit 또는 줄바꿈)
    }
}