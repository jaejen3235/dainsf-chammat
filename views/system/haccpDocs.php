<?php
/**
 * views/system/haccpDocs.php
 * - Lists files in /assets/images/haccp_docs
 * - Adds a per-file "작성" button that uploads and overwrites that file
 * Note: This file is self-contained and intentionally replaces previous content.
 */

$haccpDir = realpath(__DIR__ . '/../../assets/images/haccp_docs') ?: __DIR__ . '/../../assets/images/haccp_docs';

// Handle AJAX upload requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_POST['action'] ?? '';

    if ($action === 'uploadHaccpDoc') {
        if (!isset($_FILES['file']) || !isset($_POST['filename'])) {
            echo json_encode(['success' => false, 'message' => '파일 업로드 데이터가 없습니다.']);
            exit;
        }

        $filename = basename($_POST['filename']);
        if ($filename === '') {
            echo json_encode(['success' => false, 'message' => '유효하지 않은 파일명입니다.']);
            exit;
        }

        if (!is_dir($haccpDir)) {
            echo json_encode(['success' => false, 'message' => '업로드 폴더가 존재하지 않습니다.']);
            exit;
        }

        $realDir = realpath($haccpDir);
        $targetPath = $realDir . DIRECTORY_SEPARATOR . $filename;

        // ensure the target path is inside the intended folder
        if (strpos($targetPath, $realDir) !== 0) {
            echo json_encode(['success' => false, 'message' => '잘못된 파일명입니다.']);
            exit;
        }

        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            echo json_encode(['success' => false, 'message' => '업로드된 파일을 확인할 수 없습니다.']);
            exit;
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            echo json_encode(['success' => true, 'message' => '파일이 정상적으로 업로드(덮어쓰기)되었습니다.']);
            exit;
        }

        echo json_encode(['success' => false, 'message' => '파일 저장에 실패했습니다.']);
        exit;
    }

    // unsupported action
    echo json_encode(['success' => false, 'message' => '지원하지 않는 작업입니다.']);
    exit;
}

// Build list of files
$files = [];
if (is_dir($haccpDir) && ($dh = opendir($haccpDir))) {
    while (($f = readdir($dh)) !== false) {
        if ($f === '.' || $f === '..') continue;
        $full = $haccpDir . DIRECTORY_SEPARATOR . $f;
        if (is_file($full)) {
            $files[] = [
                'name' => $f,
                'size' => filesize($full),
                'mtime' => filemtime($full)
            ];
        }
    }
    closedir($dh);
    usort($files, function($a, $b){ return strcasecmp($a['name'], $b['name']); });
}

// helper
function hr_filesize($bytes) {
    if ($bytes <= 0) return '0 B';
    $units = ['B','KB','MB','GB','TB'];
    $log = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $log), 2) . ' ' . $units[$log];
}
?>

<style>
.haccp-container { padding: 18px; }
.haccp-title { font-size: 18px; margin-bottom: 12px; }
.haccp-table { width: 100%; border-collapse: collapse; }
.haccp-table th, .haccp-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
.haccp-table th { background: #f6f6f6; }
.btn-write { padding: 6px 10px; background:#2b7cff; color:#fff; border:0; border-radius:4px; cursor:pointer; }
.notice { color:#a94442; background:#f2dede; padding:8px; border-radius:4px; }
.small-note { color:#666; font-size:13px; margin-top:8px; }
</style>

<div class="main-container haccp-container">
    <div class="page-title"><i class="bx bxs-file"></i> HACCP 문서 관리</div>

    <?php if (!is_dir($haccpDir)): ?>
        <div class="notice">HACCP 문서 폴더가 존재하지 않습니다: <?php echo htmlspecialchars($haccpDir); ?></div>
    <?php else: ?>
        <div class="haccp-title">폴더: <code><?php echo htmlspecialchars($haccpDir); ?></code></div>
        <?php if (count($files) === 0): ?>
            <div class="notice">폴더에 문서가 없습니다.</div>
        <?php else: ?>
            <table class="haccp-table">
                <thead>
                    <tr>
                        <th>파일명</th>
                        <th>크기</th>
                        <th>수정일</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $f): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($f['name']); ?></td>
                            <td><?php echo htmlspecialchars(hr_filesize($f['size'])); ?></td>
                            <td><?php echo date('Y-m-d H:i:s', $f['mtime']); ?></td>
                            <td>
                                <button type="button" class="btn-write" data-filename="<?php echo htmlspecialchars($f['name']); ?>">작성</button>
                                &nbsp;
                                <a href="/assets/images/haccp_docs/<?php echo rawurlencode($f['name']); ?>" target="_blank">보기</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="small-note">※ 각 항목의 '작성' 버튼을 눌러 업로드하면 해당 파일을 덮어씁니다. 허용되는 파일 타입은 서버 설정에 따릅니다.</div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.btn-write').forEach(function(btn){
        btn.addEventListener('click', function(){
            var filename = btn.getAttribute('data-filename');
            if (!filename) return;

            var input = document.createElement('input');
            input.type = 'file';
            input.accept = '*/*';
            input.onchange = function(){
                var file = input.files[0];
                if (!file) return;
                if (!confirm('파일 "' + filename + '"을(를) 선택한 파일로 덮어쓰시겠습니까?')) return;

                var fd = new FormData();
                fd.append('action', 'uploadHaccpDoc');
                fd.append('filename', filename);
                fd.append('file', file);

                fetch(window.location.href, {
                    method: 'POST',
                    body: fd,
                }).then(function(res){
                    return res.json();
                }).then(function(json){
                    alert(json.message || '응답이 없습니다.');
                    if (json.success) location.reload();
                }).catch(function(err){
                    console.error(err);
                    alert('업로드 중 오류가 발생했습니다.');
                });
            };
            input.click();
        });
    });
});
</script>
