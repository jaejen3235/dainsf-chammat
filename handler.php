<?php
error_reporting(0);
session_start();
header("Content-Type: application/json; charset=UTF-8");

$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if ($contentType === "application/json") {
	$content = trim(file_get_contents("php://input"));
	$param = json_decode($content, true);
} else {
	if(sizeof($_GET) > 0) $param = $_GET['param'];
	else if(sizeof($_POST) > 0) $param = $_POST;
}

require_once("controllers/".$param['controller'].".php");

$ajax = new $param['controller']($param);
$ajax->connectDatabase();

$ajax->{$param['mode']}();
?>