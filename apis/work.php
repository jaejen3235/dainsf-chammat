<?php
// 작업지시 API

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 데이터베이스 연결
$conn = mysqli_connect('localhost', 'root', 'since1970', 'chammat');
if (!$conn) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'DB 연결 실패: ' . mysqli_connect_error()
    ]);
    exit;
}
mysqli_set_charset($conn, 'utf8');

// 요청 메서드 확인
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// OPTIONS 요청 처리 (CORS preflight)
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    if ($method === 'GET' && $action === 'list') {
        // 작업지시 목록 조회
        // mes_work_order 또는 mes_daily_work 테이블에서 데이터 조회
        // 실제 테이블 구조에 맞게 수정 필요
        
        $query = "
            SELECT 
                wo.uid,
                wo.order_qty,
                wo.work_qty,
                wo.status,
                i.item_name,
                i.standard,
                wo.created_at,
                wo.updated_at
            FROM mes_work_order wo
            LEFT JOIN mes_item i ON wo.item_uid = i.uid
            WHERE wo.status != 'completed' OR wo.work_qty < wo.order_qty
            ORDER BY wo.created_at DESC
        ";
        
        // 테이블이 없을 수 있으므로 대안 쿼리
        // 실제 테이블 구조에 맞게 수정 필요
        $query = "
            SELECT 
                dw.uid,
                dw.work_qty as order_qty,
                COALESCE(SUM(md.qty), 0) as work_qty,
                CASE 
                    WHEN COALESCE(SUM(md.qty), 0) >= dw.work_qty THEN 'completed'
                    ELSE 'pending'
                END as status,
                i.item_name,
                i.standard,
                dw.work_date as created_at,
                dw.updated_at
            FROM mes_daily_work dw
            LEFT JOIN mes_item i ON dw.item_uid = i.uid
            LEFT JOIN mes_metal_detect md ON md.item_uid = dw.item_uid AND DATE(md.created_at) = dw.work_date
            WHERE dw.work_date = CURDATE()
            GROUP BY dw.uid, dw.work_qty, i.item_name, i.standard, dw.work_date, dw.updated_at
            HAVING work_qty < order_qty OR work_qty = 0
            ORDER BY dw.created_at DESC
        ";
        
        // 더 간단한 쿼리로 시도 (mes_daily_work 테이블 기준)
        $query = "
            SELECT 
                dw.uid,
                dw.work_qty as order_qty,
                dw.work_qty as work_qty,
                'pending' as status,
                i.item_name,
                i.standard,
                dw.work_date as created_at,
                dw.updated_at
            FROM mes_daily_work dw
            LEFT JOIN mes_item i ON dw.item_uid = i.uid
            WHERE dw.work_date = CURDATE()
            ORDER BY dw.created_at DESC
            LIMIT 100
        ";
        
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            throw new Exception('쿼리 실행 실패: ' . mysqli_error($conn));
        }
        
        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = [
                'uid' => (int)$row['uid'],
                'item_name' => $row['item_name'] ?? '',
                'standard' => $row['standard'] ?? '',
                'order_qty' => (int)($row['order_qty'] ?? 0),
                'work_qty' => (int)($row['work_qty'] ?? 0),
                'status' => $row['status'] ?? 'pending',
                'created_at' => $row['created_at'] ?? '',
                'updated_at' => $row['updated_at'] ?? ''
            ];
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $orders,
            'count' => count($orders)
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
    } elseif ($method === 'POST') {
        // 작업 완료 처리
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);
        
        if (!$data || !isset($data['action']) || $data['action'] !== 'complete') {
            throw new Exception('잘못된 요청입니다.');
        }
        
        if (!isset($data['uid']) || empty($data['uid'])) {
            throw new Exception('작업지시 UID가 필요합니다.');
        }
        
        $uid = (int)$data['uid'];
        
        // 작업 완료 처리 (실제 로직에 맞게 수정 필요)
        // 예: mes_daily_work 테이블의 상태 업데이트 또는 완료 플래그 설정
        
        $updateQuery = "
            UPDATE mes_daily_work 
            SET status = 'completed', 
                updated_at = NOW()
            WHERE uid = ?
        ";
        
        $stmt = mysqli_prepare($conn, $updateQuery);
        if (!$stmt) {
            throw new Exception('쿼리 준비 실패: ' . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, 'i', $uid);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('쿼리 실행 실패: ' . mysqli_stmt_error($stmt));
        }
        
        mysqli_stmt_close($stmt);
        
        echo json_encode([
            'status' => 'success',
            'message' => '작업이 완료 처리되었습니다.'
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
    } else {
        throw new Exception('지원하지 않는 요청입니다.');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} finally {
    mysqli_close($conn);
}
?>
