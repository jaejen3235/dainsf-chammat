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

// 컨트롤러 내부 PHP Notice/Warning이 JSON 응답에 섞이지 않도록 화면 출력 비활성화
ini_set('display_errors', '0');

$jsonOutput = '';

try {
    $ajax = new $param['controller']($param);
    $ajax->connectDatabase();

    // 컨트롤러 출력(JSON)만 클라이언트로 보내기 위해 출력 버퍼 사용
    ob_start();
    $ajax->{$param['mode']}();
    $jsonOutput = ob_get_clean();
} catch (Throwable $e) {
    if (ob_get_level()) ob_end_clean();
    $jsonOutput = json_encode([
        'result' => 'error',
        'message' => '처리 중 오류가 발생했습니다. (' . $e->getMessage() . ')'
    ]);
    error_log('[handler.php] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
}

// 스마트공장 로그API 적재 (MES 컨트롤러 액션 후) - 여기서 나오는 경고/노티스는 응답에 섞이지 않도록 버퍼로 흡수
if ($param['controller'] === 'mes' && $jsonOutput !== '') {
    ob_start();
    try {
        require_once("apis/SmartFactoryLogger.php");
        SmartFactoryLogger::hookAfterAction($param['mode']);
    } catch (Exception $e) {
        // 로그 적재 실패가 MES 기능에 영향을 주지 않도록 예외 무시
    }
    ob_end_clean();
}

echo $jsonOutput;
?>