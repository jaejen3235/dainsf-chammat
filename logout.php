<?php
session_start();

// 스마트공장 로그 적재 (로그아웃: DO6002) - 세션 파기 전에 수행
if (!empty($_SESSION['loginId'])) {
    try {
        require_once(__DIR__ . '/apis/SmartFactoryLogger.php');
        SmartFactoryLogger::queueLog(SmartFactoryLogger::USE_LOGOUT, $_SESSION['loginId']);
    } catch (Exception $e) {
        // 로그 적재 실패가 로그아웃에 영향을 주지 않도록 예외 무시
    }
}

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

Header("Location: index.php?controller=shop&action=login");
?>