<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Boxicons CDN 추가 -->    
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />
    <link rel="stylesheet" href="./assets/css/button.css">
    <link rel="stylesheet" href="./assets/css/color.css">
    <link rel="stylesheet" href="./assets/css/nstyle.css">
    
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
</head>
<body>
<header>
    <!-- logo -->
    <div class='logo-container'>
        <div class="hands" onclick="location.href='index.php'" >
            <span class='logo'>MES</span>
            <span class='logo-text'>여수참맛푸드</span>
        </div>
        <!-- logout -->
        <div class='logout-box'>
            <span id='loginName'><?=$_SESSION['loginName']?>님 반갑습니다. </span>
            <input type='button' class='btn-logout' onclick="location.href='logout.php'" value="로그아웃" />
        </div>
    </div>
</header>

<main>
    <!-- left menu -->
    <div class='left-container'>
<?php
include "./include/left_menu.php";
?>
    </div>

    <!-- hidden button -->
    <div class='hidden-container'>
        <div class='btn-toggle'><i class='bx bx-chevron-left'></i></div>
    </div>