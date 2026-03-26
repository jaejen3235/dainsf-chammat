<?php
// 에러 리포트 활성화 (개발 환경)
error_reporting(E_ALL);
ini_set('display_errors', 0); // 브라우저에 직접 출력하지 않음
ini_set('log_errors', 1);

date_default_timezone_set('Asia/Seoul'); 

// 에러 핸들러 설정
function custom_error_handler($errno, $errstr, $errfile, $errline) {
    $log_file = __DIR__ . '/log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $error_message = "[{$timestamp}] PHP ERROR [{$errno}]: {$errstr} in {$errfile} on line {$errline}\n";
    file_put_contents($log_file, $error_message, FILE_APPEND);
    
    // 치명적 에러인 경우 JSON 응답 반환
    if ($errno == E_ERROR || $errno == E_PARSE || $errno == E_CORE_ERROR || $errno == E_COMPILE_ERROR) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => "Internal server error",
            "error" => $errstr,
            "file" => basename($errfile),
            "line" => $errline
        ]);
        exit;
    }
    return false;
}
set_error_handler('custom_error_handler');

// 예외 핸들러 설정
function custom_exception_handler($exception) {
    $log_file = __DIR__ . '/log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $error_message = "[{$timestamp}] EXCEPTION: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    file_put_contents($log_file, $error_message, FILE_APPEND);
    
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "error",
        "message" => "Internal server error",
        "error" => $exception->getMessage(),
        "file" => basename($exception->getFile()),
        "line" => $exception->getLine()
    ]);
    exit;
}
set_exception_handler('custom_exception_handler');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 데이터베이스 연결
$conn = mysqli_connect('localhost', 'root', 'since1970', 'chammat');
if (!$conn) {
    $error_msg = 'Connection failed: ' . mysqli_connect_error();
    $log_file = __DIR__ . '/log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] ERROR: {$error_msg}\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
    
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed",
        "details" => mysqli_connect_error()
    ]);
    exit;
}

// 1. 요청 메서드 확인
/*
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Only POST requests are allowed."]);
    exit;
}
    */

// 에러 로그 함수 (함수 정의 전에 사용할 수 있도록 먼저 정의)
function write_error_log($message) {
    $log_file = __DIR__ . '/log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] ERROR: {$message}\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
    error_log($message);
}

// 2. 요청 본문(Raw JSON) 읽기
// JSON 형식의 POST 데이터를 받을 때는 이 방식(php://input)을 사용해야 합니다.
$json_data = file_get_contents('php://input');

// 💡 POST 데이터와 RAW JSON 데이터를 파일에 기록
// POST 배열 내용을 로그에 기록
$request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'UNKNOWN';
$log_prefix = sprintf("[%s] REQUEST METHOD: %s\n", date('Y-m-d H:i:s'), $request_method);
file_put_contents(__DIR__ . '/log.txt', $log_prefix, FILE_APPEND);

$log_prefix = sprintf("[%s] POST DATA: ", date('Y-m-d H:i:s'));
file_put_contents(__DIR__ . '/log.txt', $log_prefix . print_r(isset($_POST) ? $_POST : [], true) . "\n", FILE_APPEND);

$log_prefix = sprintf("[%s] GET DATA: ", date('Y-m-d H:i:s'));
file_put_contents(__DIR__ . '/log.txt', $log_prefix . print_r(isset($_GET) ? $_GET : [], true) . "\n", FILE_APPEND);

// RAW JSON 데이터를 파일에 기록
$log_prefix = sprintf("[%s] RAW DATA (php://input): ", date('Y-m-d H:i:s'));
file_put_contents(__DIR__ . '/log.txt', $log_prefix . ($json_data ? $json_data : '(empty)') . "\n", FILE_APPEND);


// Content-Type 확인
$content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 'not set';
$log_prefix = sprintf("[%s] CONTENT_TYPE: %s\n", date('Y-m-d H:i:s'), $content_type);
file_put_contents(__DIR__ . '/log.txt', $log_prefix, FILE_APPEND);
// -----------------------------------------------------

// 3. JSON 데이터를 PHP 연관 배열로 디코딩
// 여러 방법으로 데이터 받기 시도
$data = null;

// 방법 1: php://input에서 JSON 읽기 (Raw JSON)
if (!empty($json_data)) {
    $data = json_decode($json_data, true);
    if ($data !== null) {
        write_error_log("Data received via php://input (Raw JSON)");
    }
}

// 방법 2: $_POST 배열에서 JSON 문자열 찾기
if ($data === null && isset($_POST) && !empty($_POST)) {
    // $_POST에 직접 데이터가 있는 경우
    if (isset($_POST['data']) && is_string($_POST['data'])) {
        $data = json_decode($_POST['data'], true);
        if ($data !== null) {
            write_error_log("Data received via \$_POST['data']");
        }
    } else {
        // $_POST 배열 자체를 데이터로 사용
        $data = $_POST;
        write_error_log("Data received via \$_POST array");
    }
}

// 방법 3: GET 파라미터에서 데이터 찾기
if ($data === null && isset($_GET) && !empty($_GET)) {
    // GET 파라미터에 'data' 키가 있고 JSON 문자열인 경우
    if (isset($_GET['data']) && is_string($_GET['data'])) {
        $data = json_decode($_GET['data'], true);
        if ($data !== null) {
            write_error_log("Data received via \$_GET['data'] (JSON string)");
        }
    }
    
    // GET 파라미터에 직접 필수 필드가 있는 경우 (machine, data_type, value)
    if ($data === null && isset($_GET['machine']) && isset($_GET['data_type']) && isset($_GET['value'])) {
        // 단일 객체인지 배열인지 확인
        if (is_array($_GET['machine'])) {
            // 배열 형태: ?machine[]=...&data_type[]=...&value[]=...
            $data = [];
            $count = count($_GET['machine']);
            for ($i = 0; $i < $count; $i++) {
                if (isset($_GET['machine'][$i]) && isset($_GET['data_type'][$i]) && isset($_GET['value'][$i])) {
                    $data[] = [
                        'machine' => $_GET['machine'][$i],
                        'data_type' => $_GET['data_type'][$i],
                        'value' => $_GET['value'][$i]
                    ];
                }
            }
            if (!empty($data)) {
                write_error_log("Data received via \$_GET array (multiple items)");
            }
        } else {
            // 단일 객체: ?machine=...&data_type=...&value=...
            $data = [
                'machine' => $_GET['machine'],
                'data_type' => $_GET['data_type'],
                'value' => $_GET['value']
            ];
            write_error_log("Data received via \$_GET parameters (single item)");
        }
    }
}

// 모든 방법 실패 시 에러
if ($data === null) {
    $error_details = [
        "request_method" => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'UNKNOWN',
        "content_type" => $content_type,
        "php_input_empty" => empty($json_data),
        "post_empty" => !isset($_POST) || empty($_POST),
        "get_empty" => !isset($_GET) || empty($_GET)
    ];
    
    if (!empty($json_data)) {
        $json_error_msg = json_last_error_msg();
        $json_error_code = json_last_error();
        $error_details["json_error"] = $json_error_msg;
        $error_details["json_error_code"] = $json_error_code;
        $error_details["raw_data_preview"] = substr($json_data, 0, 200);
    }
    
    write_error_log("No valid data received. Details: " . json_encode($error_details));
    http_response_code(400); // Bad Request
    echo json_encode([
        "status" => "error", 
        "message" => "JSON data decoding error: No valid data received",
        "details" => $error_details
    ]);
    exit;
}

// JSON 디코딩 실패 체크 (php://input에서 읽은 경우만)
if (!empty($json_data) && $data === null && json_last_error() !== JSON_ERROR_NONE) {
    $json_error_msg = json_last_error_msg();
    $json_error_code = json_last_error();
    write_error_log("JSON decode failed. Error: {$json_error_msg} (Code: {$json_error_code}), Raw data: " . substr($json_data, 0, 500));
    http_response_code(400); // Bad Request
    echo json_encode([
        "status" => "error", 
        "message" => "JSON data decoding error",
        "details" => $json_error_msg,
        "error_code" => $json_error_code
    ]);
    exit;
}

// 4. 데이터가 배열인지 단일 객체인지 확인
// 단일 객체로 왔든, 객체 배열로 왔든 일관된 처리를 위해 배열 형태로 변환합니다.
$is_array = is_array($data) && isset($data[0]);
$data_list = $is_array ? $data : [$data];

// 5. 각 데이터 항목에 대한 유효성 검사 및 처리
$processed_data = [];
$errors = [];

foreach ($data_list as $index => $item) {

    // 필수 데이터 유효성 검사
    if (!isset($item['machine'], $item['data_type'], $item['value'])) {
        $errors[] = "Item " . ($index + 1) . ": Required fields (machine, data_type, value) are missing.";
        continue;
    }
    
    // 데이터 추출 및 처리 (XSS 방지 및 타입 강제 변환)
    $machine = htmlspecialchars($item['machine']);
    $data_type = htmlspecialchars($item['data_type']);
    $value = (float) $item['value'];
    
    // 처리된 데이터 저장
    
    $processed_item = [
        "MACHINE" => $machine,
        "DATA TYPE" => $data_type,
        "VALUE" => $value,
        "TIMESTAMP" => date('Y-m-d H:i:s')
    ];
    
    $processed_data[] = $processed_item;
    
    // 로그 파일에 항목별 데이터 기록
    $log_entry = sprintf(
        "[%s] Machine: %s, Data Type: %s, Value: %.2f\n",
        $processed_item['TIMESTAMP'],
        $processed_item['MACHINE'],
        $processed_item['DATA TYPE'],
        $processed_item['VALUE']
    );
    //file_put_contents('log.txt', $log_entry, FILE_APPEND);
    $timestamp = date('Y-m-d H:i:s');

    $query = "insert into mes_machine_data (machine, data_type, value, timestamp) values ('{$machine}', '{$data_type}', '{$value}', '{$timestamp}')";
    $result = mysqli_query($conn, $query);

    file_put_contents('log.txt', $query . " success\n", FILE_APPEND);
    
    if(!$result) {
        $error_message = "Error occurred while saving data. Query: {$query}, Error: " . mysqli_error($conn);
        write_error_log($error_message);
        $response = [
            "status" => "error",
            "message" => "Error occurred while saving data.",
        ];
        echo json_encode($response);
        exit;
    } else {
        // cleaner current 값 기반 가동/정지 이력 관리 (값 흔들림 방지: 연속 3회 기준)
        if ($machine === 'cleaner' && $data_type === 'current') {
            $threshold_on = 0.4;   // 이 값 초과: 가동
            $threshold_off = 0.4;  // 이 값 이하: 정지
            $confirm_count = 3;    // 연속 3회 카운트
            $now_ts = date('Y-m-d H:i:s');

            $is_high = $value > $threshold_on;

            // DB에 저장되는 상태를 이용해 연속 카운트를 관리한다(요청은 stateless).
            // cleaner_run_state: 현재 가동 여부/연속 카운트/현재 open run id
            // cleaner_run_history: 가동/정지 세션 기록
            mysqli_begin_transaction($conn);
            $ok = false;

            try {
                // 1) 현재 상태 row 잠금 조회
                $state_select_sql = "SELECT is_running, on_count, off_count, current_run_id 
                                      FROM cleaner_run_state 
                                      WHERE machine=? FOR UPDATE";
                $stmt = mysqli_prepare($conn, $state_select_sql);
                if (!$stmt) {
                    throw new Exception("cleaner_run_state prepare failed: " . mysqli_error($conn));
                }

                mysqli_stmt_bind_param($stmt, "s", $machine);
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("cleaner_run_state execute failed: " . mysqli_stmt_error($stmt));
                }

                $state_row = null;
                $res = mysqli_stmt_get_result($stmt);
                if ($res) {
                    $state_row = mysqli_fetch_assoc($res);
                }

                $is_running = 0;
                $on_count = 0;
                $off_count = 0;
                $current_run_id = 0;

                if ($state_row) {
                    $is_running = (int)($state_row['is_running'] ?? 0);
                    $on_count = (int)($state_row['on_count'] ?? 0);
                    $off_count = (int)($state_row['off_count'] ?? 0);
                    $current_run_id = (int)($state_row['current_run_id'] ?? 0);
                } else {
                    // 상태 row이 없으면 생성
                    $state_insert_sql = "INSERT INTO cleaner_run_state (machine, is_running, on_count, off_count, current_run_id, last_current, updated_at)
                                          VALUES (?, 0, 0, 0, 0, ?, NOW())
                                          ON DUPLICATE KEY UPDATE last_current=VALUES(last_current), updated_at=NOW()";
                    $stmt2 = mysqli_prepare($conn, $state_insert_sql);
                    if (!$stmt2) {
                        throw new Exception("cleaner_run_state insert prepare failed: " . mysqli_error($conn));
                    }
                    mysqli_stmt_bind_param($stmt2, "sd", $machine, $value);
                    if (!mysqli_stmt_execute($stmt2)) {
                        throw new Exception("cleaner_run_state insert execute failed: " . mysqli_stmt_error($stmt2));
                    }
                    mysqli_stmt_close($stmt2);
                }

                mysqli_stmt_close($stmt);

                // 2) 연속 카운트 업데이트
                if ($is_high) {
                    $on_count = min($confirm_count, $on_count + 1);
                    $off_count = 0;
                } else {
                    $off_count = min($confirm_count, $off_count + 1);
                    $on_count = 0;
                }

                $new_is_running = $is_running;
                $new_current_run_id = $current_run_id;

                // 3) 전환 조건 체크 (연속 confirm_count 충족 시에만 상태 전환)
                if ($is_running === 0 && $on_count >= $confirm_count) {
                    // 가동 시작 (새 세션 생성)
                    $history_insert_sql = "INSERT INTO cleaner_run_history 
                                            (machine, started_at, start_current, threshold_on, threshold_off, confirm_count)
                                            VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt3 = mysqli_prepare($conn, $history_insert_sql);
                    if (!$stmt3) {
                        throw new Exception("cleaner_run_history insert prepare failed: " . mysqli_error($conn));
                    }
                    mysqli_stmt_bind_param($stmt3, "ssdddi", $machine, $now_ts, $value, $threshold_on, $threshold_off, $confirm_count);
                    if (!mysqli_stmt_execute($stmt3)) {
                        throw new Exception("cleaner_run_history insert execute failed: " . mysqli_stmt_error($stmt3));
                    }

                    $new_current_run_id = (int)mysqli_insert_id($conn);
                    $new_is_running = 1;

                    // 가동 전환 후에는 카운트를 초기화
                    $on_count = 0;
                    $off_count = 0;

                    mysqli_stmt_close($stmt3);
                } elseif ($is_running === 1 && $off_count >= $confirm_count) {
                    // 정지 종료 (open 세션 종료)
                    if ($current_run_id === 0) {
                        // state에 open run id가 없을 경우를 대비해 open run을 찾아본다.
                        $open_select_sql = "SELECT id FROM cleaner_run_history 
                                             WHERE machine=? AND ended_at IS NULL
                                             ORDER BY started_at DESC
                                             LIMIT 1 FOR UPDATE";
                        $stmt_open = mysqli_prepare($conn, $open_select_sql);
                        if (!$stmt_open) {
                            throw new Exception("cleaner_run_history open select prepare failed: " . mysqli_error($conn));
                        }
                        mysqli_stmt_bind_param($stmt_open, "s", $machine);
                        if (!mysqli_stmt_execute($stmt_open)) {
                            throw new Exception("cleaner_run_history open select execute failed: " . mysqli_stmt_error($stmt_open));
                        }
                        $open_res = mysqli_stmt_get_result($stmt_open);
                        if ($open_res) {
                            $open_row = mysqli_fetch_assoc($open_res);
                            $current_run_id = (int)($open_row['id'] ?? 0);
                        }
                        mysqli_stmt_close($stmt_open);
                    }

                    $history_update_sql = "UPDATE cleaner_run_history 
                                            SET ended_at=?, end_current=?
                                            WHERE id=? AND ended_at IS NULL";
                    $stmt4 = mysqli_prepare($conn, $history_update_sql);
                    if (!$stmt4) {
                        throw new Exception("cleaner_run_history update prepare failed: " . mysqli_error($conn));
                    }
                    mysqli_stmt_bind_param($stmt4, "sdi", $now_ts, $value, $current_run_id);
                    if (!mysqli_stmt_execute($stmt4)) {
                        throw new Exception("cleaner_run_history update execute failed: " . mysqli_stmt_error($stmt4));
                    }
                    mysqli_stmt_close($stmt4);

                    $new_is_running = 0;
                    $new_current_run_id = 0;

                    // 정지 전환 후에는 카운트를 초기화
                    $on_count = 0;
                    $off_count = 0;
                }

                // 4) 상태 row 업데이트
                $state_update_sql = "UPDATE cleaner_run_state
                                      SET is_running=?, on_count=?, off_count=?, current_run_id=?, last_current=?, updated_at=NOW()
                                      WHERE machine=?";
                $stmt5 = mysqli_prepare($conn, $state_update_sql);
                if (!$stmt5) {
                    throw new Exception("cleaner_run_state update prepare failed: " . mysqli_error($conn));
                }
                mysqli_stmt_bind_param($stmt5, "iiiids", $new_is_running, $on_count, $off_count, $new_current_run_id, $value, $machine);
                if (!mysqli_stmt_execute($stmt5)) {
                    throw new Exception("cleaner_run_state update execute failed: " . mysqli_stmt_error($stmt5));
                }
                mysqli_stmt_close($stmt5);

                mysqli_commit($conn);
                $ok = true;
            } catch (Throwable $e) {
                mysqli_rollback($conn);
                write_error_log("cleaner run state update failed: " . $e->getMessage());
            }

            // cleaner 일별 소비전력 누적 (mes_day_power)
            // 전제:
            // - 설비: 3상 380V
            // - 수신 current: 1개 상 전류값(line current)
            // - 수신 주기: 약 5초
            try {
                $current_ampere = max(0, (float)$value);
                $voltage = 380.0;
                $power_factor = 0.9;
                $sample_seconds = 5.0;
                $sqrt3 = 1.7320508;

                // P(kW) = sqrt(3) * V * I * PF / 1000
                $power_kw = ($sqrt3 * $voltage * $current_ampere * $power_factor) / 1000.0;
                $energy_kwh = $power_kw * ($sample_seconds / 3600.0); // 샘플 구간 에너지(kWh)

                if ($energy_kwh > 0) {
                    $year = (int)date('Y');
                    $month = (int)date('n');
                    $day = (int)date('j');
                    $day_col = 'day' . $day; // day1 ~ day31

                    $check_sql = "SELECT uid FROM mes_day_power WHERE year=? AND month=? LIMIT 1";
                    $stmt_check = mysqli_prepare($conn, $check_sql);
                    if (!$stmt_check) {
                        throw new Exception("mes_day_power check prepare failed: " . mysqli_error($conn));
                    }
                    mysqli_stmt_bind_param($stmt_check, "ii", $year, $month);
                    if (!mysqli_stmt_execute($stmt_check)) {
                        throw new Exception("mes_day_power check execute failed: " . mysqli_stmt_error($stmt_check));
                    }

                    $check_result = mysqli_stmt_get_result($stmt_check);
                    $row = $check_result ? mysqli_fetch_assoc($check_result) : null;
                    mysqli_stmt_close($stmt_check);

                    if ($row && isset($row['uid'])) {
                        $uid = (int)$row['uid'];
                        $update_sql = "UPDATE mes_day_power SET `{$day_col}` = COALESCE(`{$day_col}`, 0) + ? WHERE uid=?";
                        $stmt_update = mysqli_prepare($conn, $update_sql);
                        if (!$stmt_update) {
                            throw new Exception("mes_day_power update prepare failed: " . mysqli_error($conn));
                        }
                        mysqli_stmt_bind_param($stmt_update, "di", $energy_kwh, $uid);
                        if (!mysqli_stmt_execute($stmt_update)) {
                            throw new Exception("mes_day_power update execute failed: " . mysqli_stmt_error($stmt_update));
                        }
                        mysqli_stmt_close($stmt_update);
                    } else {
                        $columns = ['year', 'month'];
                        $placeholders = ['?', '?'];
                        $types = "ii";
                        $params = [$year, $month];

                        for ($i = 1; $i <= 31; $i++) {
                            $columns[] = "day{$i}";
                            $placeholders[] = '?';
                            $types .= 'd';
                            $params[] = ($i === $day) ? $energy_kwh : 0.0;
                        }

                        $insert_sql = "INSERT INTO mes_day_power (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
                        $stmt_insert = mysqli_prepare($conn, $insert_sql);
                        if (!$stmt_insert) {
                            throw new Exception("mes_day_power insert prepare failed: " . mysqli_error($conn));
                        }

                        $bind_params = [];
                        $bind_params[] = &$types;
                        foreach ($params as $k => $v) {
                            $bind_params[] = &$params[$k];
                        }

                        if (!call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt_insert], $bind_params))) {
                            throw new Exception("mes_day_power insert bind failed: " . mysqli_stmt_error($stmt_insert));
                        }
                        if (!mysqli_stmt_execute($stmt_insert)) {
                            throw new Exception("mes_day_power insert execute failed: " . mysqli_stmt_error($stmt_insert));
                        }
                        mysqli_stmt_close($stmt_insert);
                    }
                }
            } catch (Throwable $e) {
                write_error_log("mes_day_power cleaner energy update failed: " . $e->getMessage());
            }

            // $ok는 현재 사용하지 않지만 디버깅용으로 남겨둘 수 있다.
        }

        $work_date = date('Y-m-d');
        $current_item_query = "select * from mes_current_item";
        $current_item_result = mysqli_query($conn, $current_item_query);
        
        if (!$current_item_result) {
            write_error_log("mes_current_item query failed: " . mysqli_error($conn));
        }
        
        $current_item = mysqli_fetch_assoc($current_item_result);
        $current_item_name = '';
        $current_item_uid = 0;
        $item_name = '';
        $item_code = '';
        $standard = '';
        
        if($current_item) {
            $current_item_name = $current_item['item_name'];
            $current_item_uid = $current_item['item_uid'];

            $item_query = "select * from mes_items where uid=?";
            $stmt = mysqli_prepare($conn, $item_query);
            if (!$stmt) {
                write_error_log("mes_items prepare failed: " . mysqli_error($conn));
            } else {
                mysqli_stmt_bind_param($stmt, "i", $current_item_uid);
                if (!mysqli_stmt_execute($stmt)) {
                    write_error_log("mes_items execute failed: " . mysqli_stmt_error($stmt));
                } else {
                    $result = mysqli_stmt_get_result($stmt);
                    if (!$result) {
                        write_error_log("mes_items get_result failed: " . mysqli_error($conn));
                    } else {
                        $item = mysqli_fetch_assoc($result);
                        if ($item) {
                            $item_name = $item['item_name'];
                            $item_code = $item['item_code'];
                            $standard = $item['standard'];
                        } else {
                            write_error_log("Item with uid={$current_item_uid} not found in mes_items.");
                        }
                    }
                }
            }
        } else {
            write_error_log("Current item is not set in mes_current_item.");
        }
        

        // mes_current_item 테이블에서 현재의 품목을 읽어온다
        
        if($data_type == 'metal_detect' && $value > 0) { // 데이터를 준 설비가 금속검출기면서 검출이 되었다면
            if (!$current_item || $current_item_uid == 0) {
                write_error_log("metal_detect processing failed: Current item is not set.");
            } else {
                // 먼저 오늘 날짜로 등록된 검출된 수량이 있는지 확인하자
                $created_at = date('Y-m-d');
                $query = "select * from mes_metal_detect where DATE(created_at)=? and item_uid=?";
                $stmt = mysqli_prepare($conn, $query);
                if (!$stmt) {
                    write_error_log("metal_detect select prepare error: " . mysqli_error($conn));
                } else {
                    if (!mysqli_stmt_bind_param($stmt, "si", $created_at, $current_item_uid)) {
                        write_error_log("metal_detect select bind_param error: " . mysqli_stmt_error($stmt));
                    } else {
                        if (!mysqli_stmt_execute($stmt)) {
                            write_error_log("metal_detect select execute error: " . mysqli_stmt_error($stmt));
                        } else {
                            $result = mysqli_stmt_get_result($stmt);
                            if (!$result) {
                                write_error_log("metal_detect select get_result error: " . mysqli_error($conn));
                            } else {
                                $metal_detect = mysqli_fetch_assoc($result);
                                if($metal_detect) {
                                    $metal_detect_qty = $metal_detect['qty'] + 1;
                                    $query = "update mes_metal_detect set qty=? where uid=?";
                                    $stmt = mysqli_prepare($conn, $query);
                                    if (!$stmt) {
                                        write_error_log("metal_detect update prepare error: " . mysqli_error($conn));
                                    } else {
                                        if (!mysqli_stmt_bind_param($stmt, "ii", $metal_detect_qty, $metal_detect['uid'])) {
                                            write_error_log("metal_detect update bind_param error: " . mysqli_stmt_error($stmt));
                                        } else {
                                            if (!mysqli_stmt_execute($stmt)) {
                                                write_error_log("metal_detect update execute error: " . mysqli_stmt_error($stmt));
                                            }
                                        }
                                    }
                                } else {
                                    if (empty($item_name) || empty($item_code)) {
                                        write_error_log("metal_detect insert failed: item_name or item_code is empty. current_item_uid: {$current_item_uid}");
                                    } else {
                                        $created_at_full = date('Y-m-d H:i:s');
                                        $query = "insert into mes_metal_detect (item_uid, item_name, item_code, standard, qty, created_at) values (?, ?, ?, ?, ?, ?)";
                                        $stmt = mysqli_prepare($conn, $query);
                                        if (!$stmt) {
                                            write_error_log("metal_detect insert prepare error: " . mysqli_error($conn));
                                        } else {
                                            $qty_value = 1;
                                            if (!mysqli_stmt_bind_param($stmt, "isssis", $current_item_uid, $item_name, $item_code, $standard, $qty_value, $created_at_full)) {
                                                write_error_log("metal_detect insert bind_param error: " . mysqli_stmt_error($stmt));
                                            } else {
                                                if (!mysqli_stmt_execute($stmt)) {
                                                    write_error_log("metal_detect insert execute error: " . mysqli_stmt_error($stmt));
                                                }
                                            }
                                        }
                                    }
                                }

                                $query = "update metal_detection_status set detected_qty = detected_qty + 1, produced_qty = produced_qty + 1 where id=1";
                                $stmt = mysqli_prepare($conn, $query);
                                if (!$stmt) {
                                    write_error_log("metal_detection_status update prepare error: " . mysqli_error($conn));
                                } else {
                                    if (!mysqli_stmt_execute($stmt)) {
                                        write_error_log("metal_detection_status update execute error: " . mysqli_stmt_error($stmt));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // mes_current_item 테이블에서 현재의 품목을 읽어온다
        // value 변수 오타(value -> $value)
        if($data_type == 'product_detect' && $value > 0) { // 데이터를 준 설비가 수량센서일 경우에 체크            
            if($current_item) {
                // 작업일지에 생산수량 증가
                // 같은 날 해당 품목이 있다면 update, 없다면 insert
                $daily_work_query = "select * from mes_daily_work where work_date=? and item_uid=?";
                $stmt = mysqli_prepare($conn, $daily_work_query);
                if (!$stmt) {
                    write_error_log("daily_work select prepare error: " . mysqli_error($conn));
                } else {
                    if (!mysqli_stmt_bind_param($stmt, "si", $work_date, $current_item_uid)) {
                        write_error_log("daily_work select bind_param error: " . mysqli_stmt_error($stmt));
                    } else {
                        if (!mysqli_stmt_execute($stmt)) {
                            write_error_log("daily_work select execute error: " . mysqli_stmt_error($stmt));
                        } else {
                            $daily_work_result = mysqli_stmt_get_result($stmt);
                            if (!$daily_work_result) {
                                write_error_log("daily_work select get_result error: " . mysqli_error($conn));
                            } else {
                                $daily_work = mysqli_fetch_assoc($daily_work_result);

                                if($daily_work) {
                                    $daily_work_qty = $daily_work['work_qty'] + 1;
                                    $query = "update mes_daily_work set work_qty=? where uid=?";
                                    $stmt = mysqli_prepare($conn, $query);
                                    if (!$stmt) {
                                        write_error_log("daily_work update prepare error: " . mysqli_error($conn));
                                    } else {
                                        if (!mysqli_stmt_bind_param($stmt, "ii", $daily_work_qty, $daily_work['uid'])) {
                                            write_error_log("daily_work update bind_param error: " . mysqli_stmt_error($stmt));
                                        } else {
                                            if (!mysqli_stmt_execute($stmt)) {
                                                write_error_log("daily_work update execute error: " . mysqli_stmt_error($stmt));
                                            }
                                        }
                                    }
                                } else {
                                    if (empty($current_item_name)) {
                                        write_error_log("daily_work insert failed: current_item_name is empty. current_item_uid: {$current_item_uid}");
                                    } else {
                                        $query = "insert into mes_daily_work (work_date, work_order_uid, worker, work_qty, item_uid, item_name, item_code, standard, unit, quality_status) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                        $stmt = mysqli_prepare($conn, $query);
                                        if (!$stmt) {
                                            write_error_log("daily_work insert prepare error: " . mysqli_error($conn));
                                        } else {
                                            $worker = '';
                                            $item_code_insert = $item_code ? $item_code : '';
                                            $standard_insert = $standard ? $standard : '';
                                            $unit = '';
                                            $work_order_uid = 0;
                                            $work_qty = 1;
                                            $quality_status = '품질검사완료';
                                            if (!mysqli_stmt_bind_param($stmt, "sisiisssss", $work_date, $work_order_uid, $worker, $work_qty, $current_item_uid, $current_item_name, $item_code_insert, $standard_insert, $unit, $quality_status)) {
                                                write_error_log("daily_work insert bind_param error: " . mysqli_stmt_error($stmt));
                                            } else {
                                                if (!mysqli_stmt_execute($stmt)) {
                                                    write_error_log("daily_work insert execute error: " . mysqli_stmt_error($stmt));
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $query = "update metal_detection_status set good_qty = good_qty + 1, produced_qty = produced_qty + 1 where id=1";
                $stmt = mysqli_prepare($conn, $query);
                if (!$stmt) {
                    write_error_log("metal_detection_status update prepare error: " . mysqli_error($conn));
                } else {
                    if (!mysqli_stmt_execute($stmt)) {
                        write_error_log("metal_detection_status update execute error: " . mysqli_stmt_error($stmt));
                    }
                }

                // 품목의 재고수량 변경
            }
        }
        // [확률 기반 삭제 로직 추가]
        if (rand(1, 600) === 1) { 
            
            $retention_seconds = 14 * 86400; // 2주(14일)
            $threshold_datetime = date('Y-m-d H:i:s', time() - $retention_seconds);

            // 14일보다 오래된 데이터 삭제 (DELETE)
            $delete_query = "DELETE FROM mes_machine_data WHERE timestamp < ?";
            $stmt = mysqli_prepare($conn, $delete_query);
            if (!$stmt) {
                write_error_log("DELETE prepare error: " . mysqli_error($conn));
            } else {
                if (!mysqli_stmt_bind_param($stmt, "s", $threshold_datetime)) {
                    write_error_log("DELETE bind_param error: " . mysqli_stmt_error($stmt));
                } else {
                    if (!mysqli_stmt_execute($stmt)) {
                        write_error_log("DELETE execute error: " . mysqli_stmt_error($stmt));
                    }
                }
            }
        }
    }
        
}


// 8. 성공 응답 전송
http_response_code(200); // OK
echo json_encode([
    "status" => "success",
    "message" => "Sensor data has been successfully received and processed.",
    "processed_count" => count($processed_data),
    "received_data" => $processed_data,
]);
?>
