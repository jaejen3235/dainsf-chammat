<?php
session_start();

$_SESSION = array();

if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();

if (isset($_SESSION['loginStatus']) && $_SESSION['loginStatus'] != "") {
    Header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="kr" dir="ltr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Login</title>
	<link rel="stylesheet" href="./assets/css/login.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="  crossorigin="anonymous"></script>
	<script type="text/javascript" src="./assets/js/common.js"></script>
</head>
<body>
	<div class="container">
		<div class="wrapper">
			<div class="title"><span>ADMIN LOGIN</span></div>
			<form id='frm'>
				<input type="hidden" name="controller" id="controller" value="login" />
				<input type="hidden" name="mode" id="mode" value="login" />
	
				<div class="row">
					<i class="fas fa-user"></i>
					<input type="text" placeholder="User ID" name='id' id='id' required>
				</div>
				<div class="row">
					<i class="fas fa-lock"></i>
					<input type="password" placeholder="User Password" name='pwd' id='pwd' required>
				</div>
				<!--<div class="pass"><a href="#">Forgot password?</a></div>-->

				<div class='remember-me'>
					<input type='checkbox' id='saveIdPw' />&nbsp;
					<label for="saveIdPw">아이디/비번 저장</label>
				</div>

				<div class="row button">
					<input type="button" id='btnLogin' value="Login">
				</div>
				<!--<div class="signup-link">Not a member? <a href="#">Signup now</a></div>-->
				<div class="signup-link">Copyright © 2024 Dainlab. All rights reserved.</div>
			</form>
		</div>
	</div>
</body>
</html>


<script>
const frm = document.getElementById('frm');
const saveIdPw = document.getElementById('saveIdPw');
const id = document.getElementById('id');
const pwd = document.getElementById('pwd');
const btnLogin = document.getElementById('btnLogin');

window.addEventListener('DOMContentLoaded', ()=>{
	if(localStorage.getItem("saveCheck") == "y") {
		if(id.value == "") id.value = localStorage.getItem("id");
		if(pwd.value == "") pwd.value = localStorage.getItem("pwd");
		saveIdPw.checked = true;
	}

	id.addEventListener('keyup', () => {
		if(event.keyCode == 13) login();	
	});

	pwd.addEventListener('keyup', () => {
		if(event.keyCode == 13) login();	
	});

	btnLogin.addEventListener('click', login);

	saveIdPw.addEventListener('click', check);
});


// 아이디/비번 기억
function check() {
	if(saveIdPw.checked) {
		localStorage.setItem("saveCheck", "y");
		if(id.value == "") id.value = localStorage.getItem("id");
		if(pwd.value == "") pwd.value = localStorage.getItem("pwd");
	} else {
		localStorage.setItem("saveCheck", "n");
		localStorage.removeItem("id");
		localStorage.removeItem("pwd");
	}
}

// 로그인 시도
function login() {
	const formData = new FormData(frm);
	fetch('./handler.php', {
		method: 'post',
		body : formData
	})
	.then(response => response.json())
	.then(function(data) {
		if(data.result == "success") {
            // 아이디, 비번 기억 체크가 되어 있다면
			if(saveIdPw.checked) {
				localStorage.setItem("id", id.value);
				localStorage.setItem("pwd", pwd.value);                
			    localStorage.setItem("loginLevel", data.loginLevel);			
			}
			location.href = "./index.php";
		} else {
            alert(data.message);
			//console.log(data.message);
		}
	}).catch(function(error) {
		console.log(error);
	});
}
</script>