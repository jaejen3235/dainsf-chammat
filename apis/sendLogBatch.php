<?php
/**
 * ============================================================================
 * 스마트공장 로그 배치 전송 스크립트
 * ============================================================================
 * 
 * Crontab으로 10분 간격 실행하여 sf_log_queue 테이블의 미전송 로그를
 * API로 전송합니다.
 * 
 * [중요] API 전송 제약사항
 * - 10분 주기당 1건만 전송 가능 (AP1029: 전송주기 초과)
 * - 따라서 1회 실행 시 1건만 전송합니다
 * - 하루 최대 5,000건 전송 제한
 * - 당일 생성된 로그만 전송 가능
 * 
 * [Crontab 등록] 10분 간격
 * 0,10,20,30,40,50 * * * * /usr/bin/php /var/www/html/dainlab/dainsf/chammat/apis/sendLogBatch.php >> /var/www/html/dainlab/dainsf/chammat/log/sf_log_batch.log 2>&1
 */

// 타임존 설정 (서버 시간과 무관하게 한국 시간 사용)
date_default_timezone_set('Asia/Seoul');

// CLI에서만 실행 가능
if (php_sapi_name() !== 'cli') {
    die('CLI에서만 실행할 수 있습니다.');
}

require_once(__DIR__ . '/SmartFactoryLogger.php');

echo "\n[" . date('Y-m-d H:i:s') . "] 스마트공장 로그 배치 전송 시작\n";

// 인증키 확인
if (!defined('SF_LOG_API_KEY') || SF_LOG_API_KEY === 'YOUR_API_KEY_HERE') {
    echo "[오류] SF_LOG_API_KEY가 설정되지 않았습니다.\n";
    exit(1);
}

// DB 연결
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    echo "[오류] DB 연결 실패: " . $conn->connect_error . "\n";
    exit(1);
}
$conn->set_charset('utf8mb4');

$today = date('Y-m-d');

// ============================================================================
// 1. 당일 전송 건수 확인 (5,000건 제한)
// ============================================================================
$countQuery = "SELECT COUNT(*) as cnt FROM sf_log_queue 
               WHERE sentYn = 'Y' AND DATE(sentDt) = '{$today}'";
$countResult = $conn->query($countQuery);
$countRow = $countResult->fetch_assoc();
$sentToday = (int)$countRow['cnt'];

echo "당일 전송 완료 건수: {$sentToday}\n";

if ($sentToday >= 5000) {
    echo "[경고] 하루 최대 전송 건수(5,000건)에 도달하였습니다. 전송 중단.\n";
    $conn->close();
    exit(0);
}

// ============================================================================
// 2. 미전송 로그 1건 조회 (10분 주기당 1건만 전송 가능)
// ============================================================================
$query = "SELECT uid, logDt, useSe, sysUser, conectIp, dataUsgqty 
          FROM sf_log_queue 
          WHERE sentYn = 'N' AND DATE(logDt) = '{$today}'
          ORDER BY uid ASC 
          LIMIT 1";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    echo "전송할 미전송 로그가 없습니다.\n";
    $conn->close();
    exit(0);
}

// ============================================================================
// 3. 1건 전송 처리
// ============================================================================
$row = $result->fetch_assoc();
$uid = $row['uid'];

echo "전송 대상: uid={$uid}, useSe={$row['useSe']}, sysUser={$row['sysUser']}\n";

$logData = [
    'logDt'      => $row['logDt'],
    'useSe'      => $row['useSe'],
    'sysUser'    => $row['sysUser'],
    'conectIp'   => $row['conectIp'],
    'dataUsgqty' => (int)$row['dataUsgqty']
];

// API 전송
$sendResult = SmartFactoryLogger::sendLog($logData);
$now = date('Y-m-d H:i:s');

if ($sendResult['success']) {
    // 전송 성공
    $resultCd = $conn->real_escape_string($sendResult['response']['recptnRsltCd'] ?? '');
    $resultMsg = $conn->real_escape_string(json_encode($sendResult['response'], JSON_UNESCAPED_UNICODE));

    $updateQuery = "UPDATE sf_log_queue 
                    SET sentYn = 'Y', sentDt = '{$now}', 
                        resultCd = '{$resultCd}', resultMsg = '{$resultMsg}' 
                    WHERE uid = {$uid}";
    $conn->query($updateQuery);
    echo "[성공] uid={$uid}, 결과코드={$resultCd}\n";
    echo "당일 누적 전송: " . ($sentToday + 1) . "/5,000건\n";
} else {
    // AP1029: 전송주기 내 중복 전송 → 실패가 아닌 "다음 주기에 재시도"
    $resultCd = '';
    if (isset($sendResult['response']['recptnRsltCd'])) {
        $resultCd = $conn->real_escape_string($sendResult['response']['recptnRsltCd']);
    }

    if ($resultCd === 'AP1029') {
        // 전송주기 초과: sentYn='N' 유지 (다음 주기에 재전송)
        echo "[대기] uid={$uid}, AP1029 - 전송주기 내 중복. 다음 주기에 재시도.\n";
    } else {
        // 기타 실패: sentYn='F'로 표시
        $errorMsg = $conn->real_escape_string($sendResult['error'] ?? '알 수 없는 오류');
        $updateQuery = "UPDATE sf_log_queue 
                        SET sentYn = 'F', sentDt = '{$now}', 
                            resultCd = '{$resultCd}', resultMsg = '{$errorMsg}' 
                        WHERE uid = {$uid}";
        $conn->query($updateQuery);
        echo "[실패] uid={$uid}, 에러: {$sendResult['error']}\n";
    }
}

// ============================================================================
// 4. 미전송 잔여 건수 안내
// ============================================================================
$remainQuery = "SELECT COUNT(*) as cnt FROM sf_log_queue 
                WHERE sentYn = 'N' AND DATE(logDt) = '{$today}'";
$remainResult = $conn->query($remainQuery);
$remainRow = $remainResult->fetch_assoc();
echo "미전송 잔여: {$remainRow['cnt']}건\n";
echo "[" . date('Y-m-d H:i:s') . "] 배치 전송 완료\n";

$conn->close();
?>
