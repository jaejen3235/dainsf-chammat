<?php
// handler.php에서 controller/mode로 디스패치되는 HACCp CRUD 전용 컨트롤러
// - 요청: application/json
// - 응답: JSON only (handler.php가 출력 버퍼로 수집)

require_once("models/database.php");

class haccpRecords extends Database
{
    private $param;

    public function __construct($param)
    {
        $this->param = $param;
    }

    private function nowTs()
    {
        return date('Y-m-d H:i:s');
    }

    // 폼에서 들어오는 다양한 작성일자 포맷을 MySQL DATE('YYYY-MM-DD')로 정규화
    // - '-' / 공백 => null
    // - 'YYYY-MM-DD' => 그대로
    // - 'YYYY년 MM월 DD일' => 변환
    // - 'MM/DD' or 'MM/DD' => 서버 현재 연도로 변환
    private function normalizeWriteDate($raw)
    {
        if ($raw === null) return null;
        if (!is_string($raw)) return $raw;

        $v = trim($raw);
        if ($v === '' || $v === '-') return null;

        // 이미 ISO 포맷인 경우
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
            return $v;
        }

        // YYYY년 MM월 DD일
        if (preg_match('/(\d{4})\s*년\s*(\d{1,2})\s*월\s*(\d{1,2})\s*일/', $v, $m)) {
            $y = intval($m[1]);
            $mo = intval($m[2]);
            $d = intval($m[3]);
            return sprintf('%04d-%02d-%02d', $y, $mo, $d);
        }

        // MM/DD
        if (preg_match('/^(\d{1,2})\s*\/\s*(\d{1,2})$/', $v, $m)) {
            $y = intval(date('Y'));
            $mo = intval($m[1]);
            $d = intval($m[2]);
            return sprintf('%04d-%02d-%02d', $y, $mo, $d);
        }

        // 기타: 앞쪽에 YYYY년 ... 일 패턴이 포함된 경우(예: '... ~ ...')
        if (preg_match('/(\d{4})\s*년\s*(\d{1,2})\s*월\s*(\d{1,2})\s*일/', $v, $m)) {
            $y = intval($m[1]);
            $mo = intval($m[2]);
            $d = intval($m[3]);
            return sprintf('%04d-%02d-%02d', $y, $mo, $d);
        }

        return null;
    }

    private function buildResponse($data)
    {
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    // -----------------------------
    // CREATE
    // -----------------------------
    public function create()
    {
        $resourceKey = $this->param['resource_key'] ?? null;
        $meta = $this->param['meta'] ?? [];
        $payload = $this->param['payload'] ?? null;

        if (!$resourceKey || $payload === null) {
            $this->buildResponse([
                'result' => 'error',
                'message' => 'resource_key 또는 payload가 누락되었습니다.',
            ]);
            return;
        }

        $pageNo = intval($meta['page_no'] ?? ($this->param['page_no'] ?? 1));
        $writeDate = $this->normalizeWriteDate($meta['write_date'] ?? null);
        $writer = $meta['writer'] ?? null;
        $inspector = $meta['inspector'] ?? null;
        $actionPerson = $meta['action_person'] ?? null;

        $payloadJson = is_string($payload) ? $payload : json_encode($payload, JSON_UNESCAPED_UNICODE);

        $data = [
            'table' => 'haccp_records',
            'resource_key' => $resourceKey,
            'page_no' => $pageNo,
            'write_date' => $writeDate,
            'writer' => $writer,
            'inspector' => $inspector,
            'action_person' => $actionPerson,
            'payload_json' => $payloadJson,
        ];

        $ok = $this->insert($data);
        if ($ok) {
            $this->buildResponse([
                'result' => 'success',
                'uid' => $this->getUid(),
                'meta' => [
                    'resource_key' => $resourceKey,
                    'page_no' => $pageNo,
                    'write_date' => $writeDate,
                    'writer' => $writer,
                    'inspector' => $inspector,
                    'action_person' => $actionPerson,
                ],
            ]);
        } else {
            $this->buildResponse([
                'result' => 'error',
                'message' => '저장에 실패했습니다.',
            ]);
        }
    }

    // -----------------------------
    // LIST (collection)
    // -----------------------------
    public function list()
    {
        $resourceKey = $this->param['resource_key'] ?? null;
        if (!$resourceKey) {
            $this->buildResponse(['result' => 'error', 'message' => 'resource_key가 누락되었습니다.']);
            return;
        }

        $page = intval($this->param['page'] ?? 1);
        $per = intval($this->param['per'] ?? 10);
        if ($page < 1) $page = 1;
        if ($per < 1) $per = 10;

        $offset = ($page - 1) * $per;

        $resourceKeyEsc = $this->escapeString($resourceKey);

        $countQuery = "SELECT COUNT(*) AS cnt FROM haccp_records WHERE resource_key='{$resourceKeyEsc}'";
        $countRow = $this->queryFetch($countQuery);
        $total = $countRow ? intval($countRow['cnt']) : 0;

        $query = "SELECT uid, resource_key, page_no, write_date, writer, inspector, action_person, updated_at
                  FROM haccp_records
                  WHERE resource_key='{$resourceKeyEsc}'
                  ORDER BY uid DESC
                  LIMIT {$offset}, {$per}";

        $this->query($query);
        $rows = $this->fetchAll();

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'uid' => intval($r['uid']),
                'resource_key' => $r['resource_key'],
                'page_no' => intval($r['page_no']),
                'write_date' => $r['write_date'],
                'writer' => $r['writer'],
                'inspector' => $r['inspector'],
                'action_person' => $r['action_person'],
                'updated_at' => $r['updated_at'],
            ];
        }

        $this->buildResponse([
            'result' => 'success',
            'data' => $data,
            'page' => $page,
            'per' => $per,
            'total' => $total,
        ]);
    }

    // -----------------------------
    // GET ONE
    // -----------------------------
    public function getOne()
    {
        $id = intval($this->param['id'] ?? 0);
        if (!$id) {
            $this->buildResponse(['result' => 'error', 'message' => 'id가 누락되었습니다.']);
            return;
        }

        $query = "SELECT * FROM haccp_records WHERE uid={$id}";
        $row = $this->queryFetch($query);
        if (!$row) {
            $this->buildResponse(['result' => 'error', 'message' => '데이터를 찾을 수 없습니다.']);
            return;
        }

        $payload = null;
        try {
            $payload = json_decode($row['payload_json'], true);
        } catch (Throwable $e) {
            $payload = null;
        }

        $this->buildResponse([
            'result' => 'success',
            'uid' => intval($row['uid']),
            'resource_key' => $row['resource_key'],
            'page_no' => intval($row['page_no']),
            'write_date' => $row['write_date'],
            'writer' => $row['writer'],
            'inspector' => $row['inspector'],
            'action_person' => $row['action_person'],
            'payload' => $payload,
        ]);
    }

    // -----------------------------
    // UPDATE
    // -----------------------------
    public function update($data = null)
    {
        // (internal) Database::update 호출용
        if (is_array($data)) {
            return parent::update($data);
        }

        // (handler mode) CRUD 업데이트
        $id = intval($this->param['id'] ?? 0);
        $resourceKey = $this->param['resource_key'] ?? null;
        $meta = $this->param['meta'] ?? [];
        $payload = $this->param['payload'] ?? null;

        if (!$id || !$resourceKey || $payload === null) {
            $this->buildResponse([
                'result' => 'error',
                'message' => 'id/resource_key/payload가 누락되었습니다.',
            ]);
            return;
        }

        $pageNo = intval($meta['page_no'] ?? ($this->param['page_no'] ?? 1));
        $writeDate = $this->normalizeWriteDate($meta['write_date'] ?? null);
        $writer = $meta['writer'] ?? null;
        $inspector = $meta['inspector'] ?? null;
        $actionPerson = $meta['action_person'] ?? null;

        $payloadJson = is_string($payload) ? $payload : json_encode($payload, JSON_UNESCAPED_UNICODE);

        $updateData = [
            'table' => 'haccp_records',
            'where' => 'uid=' . intval($id),
            'resource_key' => $resourceKey,
            'page_no' => $pageNo,
            'write_date' => $writeDate,
            'writer' => $writer,
            'inspector' => $inspector,
            'action_person' => $actionPerson,
            'payload_json' => $payloadJson,
        ];

        $ok = parent::update($updateData);
        if ($ok) {
            $this->buildResponse([
                'result' => 'success',
                'uid' => $id,
            ]);
        } else {
            $this->buildResponse([
                'result' => 'error',
                'message' => '수정에 실패했습니다.',
            ]);
        }
    }

    // -----------------------------
    // DELETE
    // -----------------------------
    public function deleteRecord()
    {
        $id = intval($this->param['id'] ?? 0);
        if (!$id) {
            $this->buildResponse(['result' => 'error', 'message' => 'id가 누락되었습니다.']);
            return;
        }

        $ok = $this->deleteQuery('haccp_records', $id);
        if ($ok) {
            $this->buildResponse(['result' => 'success', 'uid' => $id]);
        } else {
            $this->buildResponse(['result' => 'error', 'message' => '삭제에 실패했습니다.']);
        }
    }
}

