<?php
/**
 * ============================================================================
 * 스마트공장 로그 수집 API 연동 유틸리티
 * ============================================================================
 * 
 * 정부(스마트공장 사업관리시스템) 지침에 따른 MES 사용 로그 전송 클래스
 * 
 * [API 정보]
 * - 전송 방식: POST
 * - 서비스 URL: https://log.smart-factory.kr/apisvc/sendLogData.json
 * - 데이터 타입: JSON
 * 
 * [접속구분 코드(useSe)]
 * - DO6001: 로그인
 * - DO6002: 로그아웃
 * - DO6003: 데이터 조회(R)
 * - DO6004: 데이터 등록(C)
 * - DO6005: 데이터 수정(U)
 * - DO6006: 데이터 삭제(D)
 * - DO6007: 입출고 시간
 * - DO6999: 테스트
 * 
 * [응답 코드]
 * - AP1002: 정상 수신
 * - AP1004: 데이터 길이 초과 오류
 * - AP1031: 하루 요청 5,000건 초과
 * 
 * [아키텍처 가이드]
 * 1. 실시간 방식: SmartFactoryLogger::sendDirect() 로 즉시 전송
 * 2. 배치 방식 (권장):
 *    - SmartFactoryLogger::queueLog() 로 DB(sf_log_queue)에 적재
 *    - Crontab으로 apis/sendLogBatch.php를 10분 간격 실행
 *    - 배치 스크립트가 미전송 건을 건당 발송 처리
 *    
 *    Crontab 등록 예시:
 *    * /10 * * * * /usr/bin/php /var/www/html/dainlab/dainsf/chammat/apis/sendLogBatch.php >> /var/www/html/dainlab/dainsf/chammat/log/sf_log_batch.log 2>&1
 * 
 * [전송 제약]
 * - 최소 10분 간격으로 전송 및 저장
 * - 하루 최대 5,000건 전송 제한
 * - 당일 생성된 로그만 전송 가능
 */

// 타임존 설정 (서버 시간과 무관하게 한국 시간 사용)
date_default_timezone_set('Asia/Seoul');

// DB 접속 정보 및 인증키 로드
require_once(__DIR__ . '/../include/db_define.php');

class SmartFactoryLogger
{
    // ========================================================================
    // 상수
    // ========================================================================
    const API_URL = 'https://log.smart-factory.kr/apisvc/sendLogData.json';

    // 성공으로 간주하는 응답 코드 목록
    const SUCCESS_CODES = ['AP1002', 'AP1028'];

    // 접속구분 코드
    const USE_LOGIN      = 'DO6001';
    const USE_LOGOUT     = 'DO6002';
    const USE_READ       = 'DO6003';
    const USE_CREATE     = 'DO6004';
    const USE_UPDATE     = 'DO6005';
    const USE_DELETE     = 'DO6006';
    const USE_INOUT      = 'DO6007';
    const USE_TEST       = 'DO6999';

    // ========================================================================
    // 헬퍼 함수
    // ========================================================================

    /**
     * 밀리초를 포함한 현재 시간 생성
     * 형식: YYYY-MM-DD HH:MI:SS.SSS (24시간 기준, 23자)
     * 
     * @return string 예: '2026-03-10 16:05:23.456'
     */
    public static function getLogDt()
    {
        $now = microtime(true);
        $milliseconds = sprintf('%03d', ($now - floor($now)) * 1000);
        return date('Y-m-d H:i:s', (int)$now) . '.' . $milliseconds;
    }

    /**
     * 클라이언트 IP 주소 추출
     * 프록시 환경까지 고려하여 IP를 가져옴
     * 
     * @return string IP 주소 (최대 30자)
     */
    public static function getClientIp()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ipList[0]);
        }
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }

    // ========================================================================
    // DB 큐 적재 (배치 전송용)
    // ========================================================================

    /**
     * 로그를 로컬 DB 큐 테이블(sf_log_queue)에 적재
     * Cron 배치 스크립트(sendLogBatch.php)가 10분 단위로 전송 처리
     * 
     * @param string $useSe     접속구분 코드 (DO6001~DO6999)
     * @param string $sysUser   사용자 ID/이름/사번 (최대 60자)
     * @param string $conectIp  클라이언트 IP (최대 30자)
     * @param int    $dataUsgqty 데이터 사용량(byte), 없으면 0
     * @return bool 적재 성공 여부
     */
    public static function queueLog($useSe, $sysUser, $conectIp = '', $dataUsgqty = 0)
    {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            if ($conn->connect_error) {
                error_log('[SmartFactoryLogger] DB 연결 실패: ' . $conn->connect_error);
                return false;
            }
            $conn->set_charset('utf8mb4');

            $logDt = self::getLogDt();
            if (empty($conectIp)) {
                $conectIp = self::getClientIp();
            }

            $stmt = $conn->prepare(
                "INSERT INTO sf_log_queue (logDt, useSe, sysUser, conectIp, dataUsgqty) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param('ssssi', $logDt, $useSe, $sysUser, $conectIp, $dataUsgqty);
            $result = $stmt->execute();

            if (!$result) {
                error_log('[SmartFactoryLogger] 로그 적재 실패: ' . $stmt->error);
            }

            $stmt->close();
            $conn->close();
            return $result;
        } catch (Exception $e) {
            error_log('[SmartFactoryLogger] 로그 적재 예외: ' . $e->getMessage());
            return false;
        }
    }

    // ========================================================================
    // API 전송
    // ========================================================================

    /**
     * 단건 API 전송 (cURL)
     * 
     * @param array $logData 전송할 로그 데이터
     *   - crtfcKey:    인증키 (자동 설정됨)
     *   - logDt:       로그일시
     *   - useSe:       접속구분
     *   - sysUser:     사용자
     *   - conectIp:    IP정보
     *   - dataUsgqty:  데이터사용량
     * @return array ['success' => bool, 'response' => array|null, 'error' => string|null]
     */
    public static function sendLog($logData)
    {
        // 인증키 설정
        $logData['crtfcKey'] = defined('SF_LOG_API_KEY') ? SF_LOG_API_KEY : '';

        if (empty($logData['crtfcKey'])) {
            return [
                'success' => false,
                'response' => null,
                'error' => '인증키(SF_LOG_API_KEY)가 설정되지 않았습니다'
            ];
        }

        // dataUsgqty: 값이 없으면 0으로 전송 (Integer)
        if (!isset($logData['dataUsgqty']) || $logData['dataUsgqty'] === '') {
            $logData['dataUsgqty'] = 0;
        }
        $logData['dataUsgqty'] = (int)$logData['dataUsgqty'];

        $jsonPayload = json_encode($logData, JSON_UNESCAPED_UNICODE);

        // cURL 전송
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => self::API_URL,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonPayload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json; charset=UTF-8',
                'Content-Length: ' . strlen($jsonPayload)
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $responseBody = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // cURL 에러 처리
        if ($responseBody === false || !empty($curlError)) {
            $errorMsg = "cURL 전송 실패 (HTTP {$httpCode}): {$curlError}";
            error_log('[SmartFactoryLogger] ' . $errorMsg);
            return [
                'success' => false,
                'response' => null,
                'error' => $errorMsg
            ];
        }

        // 응답 JSON 파싱
        $responseData = json_decode($responseBody, true);
        if ($responseData === null) {
            $errorMsg = "응답 JSON 파싱 실패: {$responseBody}";
            error_log('[SmartFactoryLogger] ' . $errorMsg);
            return [
                'success' => false,
                'response' => null,
                'error' => $errorMsg
            ];
        }

        /**
         * 실제 응답 구조: {"result": {"recptnDt": "...", "recptnRsltCd": "...", ...}}
         * result 키 안에 래핑되어 있으므로 언래핑 처리
         */
        if (isset($responseData['result']) && is_array($responseData['result'])) {
            $responseData = $responseData['result'];
        }

        /**
         * 응답 데이터 구조:
         * - recptnDt:      수신일시
         * - recptnRsltCd:  수신결과코드 (AP1002/AP1028 = 정상)
         * - recptnRslt:    수신결과코드설명
         * - recptnRsltDtl: 수신결과상세설명
         */
        $resultCode = isset($responseData['recptnRsltCd']) ? $responseData['recptnRsltCd'] : '';
        $isSuccess = in_array($resultCode, self::SUCCESS_CODES, true);

        if (!$isSuccess) {
            $resultMsg = isset($responseData['recptnRslt']) ? $responseData['recptnRslt'] : '';
            $resultDtl = isset($responseData['recptnRsltDtl']) ? $responseData['recptnRsltDtl'] : '';
            $errorMsg = "API 전송 실패 [{$resultCode}] {$resultMsg} - {$resultDtl}";
            error_log('[SmartFactoryLogger] ' . $errorMsg);
        }

        return [
            'success' => $isSuccess,
            'response' => $responseData,
            'error' => $isSuccess ? null : ($errorMsg ?? null)
        ];
    }

    /**
     * 즉시 전송 (DB 큐 없이 실시간으로 API 호출)
     * 
     * @param string $useSe     접속구분 코드
     * @param string $sysUser   사용자 ID
     * @param string $conectIp  클라이언트 IP (빈 값이면 자동 감지)
     * @param int    $dataUsgqty 데이터 사용량(byte)
     * @return array sendLog() 반환값과 동일
     */
    public static function sendDirect($useSe, $sysUser, $conectIp = '', $dataUsgqty = 0)
    {
        if (empty($conectIp)) {
            $conectIp = self::getClientIp();
        }

        $logData = [
            'logDt'      => self::getLogDt(),
            'useSe'      => $useSe,
            'sysUser'    => $sysUser,
            'conectIp'   => $conectIp,
            'dataUsgqty' => (int)$dataUsgqty
        ];

        return self::sendLog($logData);
    }

    // ========================================================================
    // handler.php 후킹용 메서드
    // ========================================================================

    /**
     * handler.php에서 컨트롤러 액션 실행 후 호출
     * mode명을 분석하여 적절한 useSe 코드를 자동 매핑, 로그를 DB 큐에 적재
     * 
     * @param string $mode 실행된 컨트롤러 mode명
     * @return void
     */
    public static function hookAfterAction($mode)
    {
        // 세션에서 사용자 정보 가져오기
        $sysUser = isset($_SESSION['loginId']) ? $_SESSION['loginId'] : '';
        if (empty($sysUser)) {
            return; // 비로그인 상태에서는 로그를 적재하지 않음
        }

        // mode명을 useSe 코드로 매핑
        $useSe = self::mapModeToUseSe($mode);
        if ($useSe === null) {
            return; // 매핑되지 않는 mode는 무시
        }

        // payload 크기 계산 (POST body)
        $dataUsgqty = 0;
        $rawInput = file_get_contents('php://input');
        if (!empty($rawInput)) {
            $dataUsgqty = strlen($rawInput);
        } else if (!empty($_POST)) {
            $dataUsgqty = strlen(http_build_query($_POST));
        }

        // 큐에 적재
        self::queueLog($useSe, $sysUser, self::getClientIp(), $dataUsgqty);
    }

    /**
     * mode명 → useSe 코드 자동 매핑
     * 
     * @param string $mode 컨트롤러 mode명
     * @return string|null useSe 코드 또는 null(매핑 불가)
     */
    public static function mapModeToUseSe($mode)
    {
        $modeLower = strtolower($mode);

        // 삭제 (D) - DO6006
        if (preg_match('/^(delete|remove)/', $modeLower)) {
            return self::USE_DELETE;
        }

        // 등록 (C) - DO6004
        if (preg_match('/^(register|insert|add|create)/', $modeLower)) {
            return self::USE_CREATE;
        }

        // 수정 (U) - DO6005
        if (preg_match('/^(update|modify|edit|change)/', $modeLower)) {
            return self::USE_UPDATE;
        }

        // 조회 (R) - DO6003
        if (preg_match('/^(get|list|search|find|fetch|load|read)/', $modeLower)) {
            return self::USE_READ;
        }

        // 입출고 - DO6007
        if (preg_match('/(warehousing|shipment|inout|stock|complete)/', $modeLower)) {
            return self::USE_INOUT;
        }

        // 기타 mode는 로그 적재하지 않음
        return null;
    }
}

// ============================================================================
// CLI 테스트 모드
// ============================================================================
// 사용법: php SmartFactoryLogger.php test
// DO6999 테스트 코드로 API 전송 테스트를 수행합니다
if (php_sapi_name() === 'cli' && isset($argv[1]) && $argv[1] === 'test') {
    echo "=== 스마트공장 로그 API 전송 테스트 ===\n\n";

    // 인증키 확인
    $apiKey = defined('SF_LOG_API_KEY') ? SF_LOG_API_KEY : '';
    if (empty($apiKey) || $apiKey === 'YOUR_API_KEY_HERE') {
        echo "[오류] 인증키가 설정되지 않았습니다.\n";
        echo "include/db_define.php에서 SF_LOG_API_KEY를 설정하세요.\n\n";
        exit(1);
    }

    echo "인증키: " . substr($apiKey, 0, 10) . "...\n";
    echo "로그일시: " . SmartFactoryLogger::getLogDt() . "\n";
    echo "API URL: " . SmartFactoryLogger::API_URL . "\n\n";

    echo "DO6999 테스트 전송 중...\n";
    $result = SmartFactoryLogger::sendDirect(
        SmartFactoryLogger::USE_TEST,
        'test_user',
        '127.0.0.1',
        0
    );

    echo "\n[전송 결과]\n";
    echo "성공 여부: " . ($result['success'] ? '성공' : '실패') . "\n";

    if ($result['response']) {
        echo "수신일시: " . ($result['response']['recptnDt'] ?? '-') . "\n";
        echo "결과코드: " . ($result['response']['recptnRsltCd'] ?? '-') . "\n";
        echo "결과설명: " . ($result['response']['recptnRslt'] ?? '-') . "\n";
        echo "상세설명: " . ($result['response']['recptnRsltDtl'] ?? '-') . "\n";
    }

    if ($result['error']) {
        echo "에러: " . $result['error'] . "\n";
    }

    echo "\n=== 테스트 완료 ===\n";
}
?>
