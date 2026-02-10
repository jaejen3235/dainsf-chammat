<?php
session_start();
require_once('./include/db_define.php');

function is_mobile() {
    // User-Agent에 모바일 기기의 키워드가 포함되어 있는지 확인
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $mobile_keywords = ['iphone', 'android', 'ipad', 'ipod', 'blackberry', 'windows phone', 'webos'];

    foreach ($mobile_keywords as $keyword) {
        if (strpos($user_agent, $keyword) !== false) {
            return true;
        }
    }

    return false;
}

/*
if (is_mobile()) {
    // 모바일 기기라면 mobile 폴더의 index.php로 리다이렉트
    header('Location: ./mobile/index.php');
    exit();
}
*/

if(!isset($_SESSION['loginId'])) {
	header("Location: login.php");
}

extract($_POST);
extract($_GET);

if(!isset($controller) && !isset($action)) {
	$controller = 'product';
	$action = 'workOrderManagement';

	require_once ('./views/layout.php');
} else {
	require_once ('./views/layout.php');
}
?>