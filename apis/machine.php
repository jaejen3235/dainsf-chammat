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

    //file_put_contents('log.txt', $query . " success\n", FILE_APPEND);
    
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
        


        //file_put_contents('log.txt', $query . " success\n", FILE_APPEND);
                // ------------------------------------------------------------------
        // [확률 기반 삭제 로직 추가]
        // 10분의 1 확률로만 삭제 로직 실행 (10초에 1번 꼴)
        if (rand(1, 600) === 1) { 
            
            $one_day_in_seconds = 86400;
            $threshold_datetime = date('Y-m-d H:i:s', time() - $one_day_in_seconds);

            // 24시간보다 오래된 데이터 삭제 (DELETE)
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
        // 10번 중 9번은 이 로직을 건너뛰고 INSERT만 실행
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
