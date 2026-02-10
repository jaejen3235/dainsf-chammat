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
?>