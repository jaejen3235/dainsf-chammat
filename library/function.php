<?php
//' DB에 Insert
function convert_input($tag){
	$tag = str_ireplace("&","&amp;",$tag);
	$tag = str_ireplace('"',"&quot;",$tag);
	$tag = str_ireplace("'","&#039;",$tag);
	$tag = str_ireplace("<","&lt;",$tag);
	$tag = str_ireplace(">","&gt;",$tag);
	return $tag;
}

//' HTML로 OUT
function convert_output($CheckValue){
	$tag = str_ireplace("&","&amp;",$tag);
	$tag = str_ireplace('"',"&quot;",$tag);
	$tag = str_ireplace("'","&#039;",$tag);
	$tag = str_ireplace("<","&lt;",$tag);
	$tag = str_ireplace(">","&gt;",$tag);
	return $tag;
}
	
function show_message($msg){
	header('Content-Type: text/html; charset=UTF-8');
	echo "<script>";
	echo "alert('".$msg."');";
	echo "</script>";
}
    
function show_message_go($msg,$url){
	header('Content-Type: text/html; charset=UTF-8');
	echo "<script>";
	echo "alert('".$msg."');";
	echo "location.href='".$url."';";
	echo "</script>";
}

// 전화번호 등의 하이픈 붙이기
function add_hyphen($num){
	return preg_replace("/(^02.{0}|^01.{1}|[0-9]{3})([0-9]+)([0-9]{4})/", "$1-$2-$3", $num);
}

function go_back($msg='', $url='') {
	header('Content-Type: text/html; charset=UTF-8');
	echo "<script>";
	if($msg) echo 'alert("'.$msg.'");';
	if($url) echo 'location.replace("'.$url.'");';
	else echo 'history.go(-1);';
	echo "</script>";
}


//  현재와 비교할 날짜를 가지고 색상을 반환한다.
function compareCurrentDate($dt){
	$work_dt = explode("-",substr($dt,0,10));
	$work_time = mktime(0,0,0,$work_dt[1],$work_dt[2],$work_dt[0]);
	$current = date("Y-m-d");
	$current_dt = explode("-",substr($current,0,10));
	$current_time = mktime(0,0,0,$current_dt[1],$current_dt[2],$current_dt[0]);
	if($work_time < $current_time) $color = "style='background-color:#febbee'";
	else $color = "";

	return $color;
}

// 공용코드 출력
function printPublicCode($classification, $name, $method) {
	$sql = "select * from publicCode where classification='".$classification."' order by seq asc";
	$result = mysql_query($sql);
	$tag = "";

	switch($method) {
		case "selectbox" :
			$tag .= "<select class='selectbox' name='".$name."' id='".$name."'>";
			$tag .= "<option value='0'>== 선택 ==</option>";
			while($t = mysql_fetch_object($result)) {
				$tag .= "<option value='".$t->value."'>".$t->name."</option>";
			}
			$tag .= "</select>";
		break;

		case "radio" :
		break;

		case "checkbox" :
		break;
	}

	return $tag;
}

// 공용코드 출력 onchange
function printPublicCodeChange($classification, $name, $method, $function) {
	$sql = "select * from publicCode where classification='".$classification."' order by seq asc";
	$result = mysql_query($sql);
	$tag = "";

	switch($method) {
		case "selectbox" :
			$tag .= "<select class='selectbox' name='".$name."' id='".$name."' onchange='".$function."(this.value)'>";
			$tag .= "<option value='0'>== 선택 ==</option>";
			while($t = mysql_fetch_object($result)) {
				$tag .= "<option value='".$t->value."'>".$t->name."</option>";
			}
			$tag .= "</select>";
		break;

		case "radio" :
		break;

		case "checkbox" :
		break;
	}

	return $tag;
}

// 공용코드 출력 page onchange - 이 함수를 쓸때는 넘어오는 function에 classification 을 읽어오는 부분을 만들어야 함
function printPublicCodePageChange($classification, $name, $method, $function) {
	$sql = "select * from publicCode where classification='".$classification."' order by seq asc";
	$result = mysql_query($sql);
	$tag = "";

	switch($method) {
		case "selectbox" :
			$tag .= "<select class='selectbox' name='".$name."' id='".$name."' onchange='".$function."(1)'>";
			$tag .= "<option value='0'>== 선택 ==</option>";
			while($t = mysql_fetch_object($result)) {
				$tag .= "<option value='".$t->value."'>".$t->name."</option>";
			}
			$tag .= "</select>";
		break;

		case "radio" :
		break;

		case "checkbox" :
		break;
	}

	return $tag;
}

function getSelectNameValue($table, $name, $field){ // 읽어올 테이블, html 에 표시될  name, 읽어올 필드
	$fieldArr = explode(":", $field);
	$sql = "select ".$fieldArr[0].",".$fieldArr[1]." from ".$table;
	$result = mysql_query($sql);
	$tag = "<select class='selectbox' name='".$name."' id='".$name."'>";
	$tag .= "<option value='0'>== 선택 ==</option>";
	while($st = mysql_fetch_object($result)) {
		$tag .= "<option value='".$st->$fieldArr[0].":".$st->$fieldArr[1]."'>".$st->$fieldArr[1]."</option>";
	}
	$tag .= "</select>";

	return $tag;
}

function displayYear($name, $v = null) {
	if($v == null) $v = date('Y');
	$str = "<select class='mr5' name='" . $name . "' id='" . $name ."'>";
	for($i = (date('Y') - 5) ; $i <= date("Y") ; $i++) {
		if($i == $v) $selected = "selected";
		else $selected = "";
		$str .= "<option value='" . $i . "' " . $selected . ">" . $i . "년</option>";
	}
	$str .= "</select>";

	return $str;
}

function displayMonth($name, $v = null) {
	if($v == null) $v = date('n');
	$str = "<select name='" . $name . "' id='" . $name ."'>";
	for($i = 1 ; $i < 13 ; $i++) {
		if($i == $v) $selected = "selected";
		else $selected = "";
		$str .= "<option value='" . $i . "' " . $selected . ">" . $i . "월</option>";
	}
	$str .= "</select>";

	return $str;
}

function displayDate($name, $v = null) {
	if($v == null) $v = date('j');
	$str = "<select name='" . $name . "' id='" . $name ."'>";
	for($i = 1 ; $i < 32 ; $i++) {
		if($i == $v) $selected = "selected";
		else $selected = "";
		$str .= "<option value='" . $i . "' " . $selected . ">" . $i . "일</option>";
	}
	$str .= "</select>";

	return $str;
}

function displayTime($name, $v = null) {
	if($v == null) $v = date('H');
	$str = "<select name='" . $name . "' id='" . $name ."'>";
	for($i = 0 ; $i < 25 ; $i++) {
		if($i == $v) $selected = "selected";
		else $selected = "";
		$str .= "<option value='" . $i . "' " . $selected . ">" . $i . "시</option>";
	}
	$str .= "</select>";

	return $str;
}

function displayMinute($name, $v = null) {
	if($v == null) $v = date('i');
	$str = "<select name='" . $name . "' id='" . $name ."'>";
	for($i = 0 ; $i < 60 ; $i++) {
		if($i == $v) $selected = "selected";
		else $selected = "";
		$str .= "<option value='" . $i . "' " . $selected . ">" . $i . "분</option>";
	}
	$str .= "</select>";

	return $str;
}
?>