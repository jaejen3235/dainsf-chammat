<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'since1970');
define('DB_NAME', 'chammat');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (!$conn) {
    die("Connect Error: " . mysqli_connect_error());
}
mysqli_select_db($conn, DB_NAME);
mysqli_query($conn, "SET NAMES 'utf8'");

// 스마트공장 로그 API 인증키 (발급받은 인증키를 입력하세요)
define('SF_LOG_API_KEY', '$5$API$/8hK9E0CqJ8s1TrU/P86tX7VPR7eytk3tAobersgnO6');
?>