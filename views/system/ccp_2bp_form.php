<?php
// DB 연결 설정 (환경에 맞게 수정하세요)
$host = 'localhost';
$db   = 'your_database';
$user = 'your_username';
$pass = 'your_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $user, $pass);

// 폼 제출 처리 (간단한 예시)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. 마스터 데이터 저장
    $stmt = $pdo->prepare("INSERT INTO ccp2bp_logs (work_date, checker_name, writer_name, approver_name) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['work_date'], $_POST['checker_name'], $_POST['writer_name'], $_POST['approver_name']]);
    $log_id = $pdo->lastInsertId();

    // 2. 상세 데이터 저장 (배열 반복)
    $stmtItem = $pdo->prepare("INSERT INTO ccp2bp_items (log_id, row_type, measure_time, water_volume, input_weight, wash_count, wash_time, wash_method, water_change_am, water_change_pm) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($_POST['items'] as $item) {
        $stmtItem->execute([
            $log_id, $item['type'], $item['time'], $item['volume'], $item['weight'], 
            $item['count'], $item['duration'], $item['method'], 
            $item['change_am'] ?? '', $item['change_pm'] ?? ''
        ]);
    }
    
    // 3. 이탈조치 저장 (생략 가능)
    // ... 구현 필요 시 추가
    
    echo "<script>alert('저장되었습니다.'); location.href='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>CCP-2BP 모니터링일지</title>
    <style>
        body { font-family: 'Malgun Gothic', dotum, sans-serif; font-size: 12px; background: #f0f0f0; padding: 20px; }
        .page-container { width: 210mm; min-height: 297mm; background: white; padding: 20mm; margin: 0 auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); box-sizing: border-box; }
        
        /* 테이블 스타일링 */
        table { width: 100%; border-collapse: collapse; margin-bottom: 5px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle; word-break: break-all; }
        
        /* 입력 필드 스타일 (인쇄 시 숨겨진 테두리처럼 보이게) */
        input[type="text"], input[type="date"], input[type="time"], input[type="number"], select {
            width: 95%; border: none; text-align: center; font-family: inherit; font-size: inherit; outline: none; background: transparent;
        }
        input:focus { background-color: #eef; }
        textarea { width: 98%; border: none; resize: none; font-family: inherit; font-size: inherit; outline: none; }

        /* 특정 섹션 스타일 */
        .title { font-size: 18px; font-weight: bold; padding: 10px; border: none; text-align: center; }
        .header-table td { border: none; }
        .sign-box { width: 80px; height: 60px; }
        .bg-gray { background-color: #f0f0f0; }
        .text-left { text-align: left !important; padding-left: 5px; }
        .font-small { font-size: 11px; }

        /* 인쇄 설정 */
        @media print {
            body { background: white; padding: 0; }
            .page-container { box-shadow: none; margin: 0; width: 100%; }
            button { display: none; }
        }
    </style>
</head>
<body>

<form method="post" class="page-container">
    <table style="border: none;">
        <tr>
            <td colspan="8" class="title" style="border:none; text-align: center;">
                CCP-2BP 모니터링일지<br>[ 세척공정 : 기타농산물 ]
            </td>
            <td colspan="4" style="border:none; padding:0;">
                <table style="width: 100%;">
                    <tr>
                        <td rowspan="2" style="width: 20px; background: #eee;">결<br>재</td>
                        <td>작성자</td>
                        <td>승인자</td>
                    </tr>
                    <tr>
                        <td class="sign-box"><input type="text" name="writer_name"></td>
                        <td class="sign-box"><input type="text" name="approver_name"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="bg-gray" style="width: 15%;">작성일자</td>
            <td colspan="5"><input type="date" name="work_date" value="<?php echo date('Y-m-d'); ?>"></td>
            <td class="bg-gray" style="width: 15%;">점검자</td>
            <td colspan="5"><input type="text" name="checker_name"></td>
        </tr>
    </table>

    <table>
        <tr class="bg-gray">
            <td rowspan="5" style="width: 10%;">한계<br>기준</td>
            <td>세척수량</td>
            <td>세척횟수</td>
            <td>원물투입량</td>
            <td>품목</td>
            <td>세척시간</td>
            <td>세척방법</td>
            <td>세척수 교체주기</td>
        </tr>
        <tr>
            <td rowspan="4">1분당<br>20리터 이상</td>
            <td rowspan="4">총 2회</td>
            <td rowspan="4">5kg 이하</td>
            <td>배</td>
            <td>60초 이상</td>
            <td>하나씩 문질러서</td>
            <td rowspan="4">오전작업 후<br>오후작업 후</td>
        </tr>
        <tr>
            <td>양파, 마늘, 생강<br>고추, 파프리카</td>
            <td>60초 이상</td>
            <td>소쿠리에 담아<br>골고루 문질러서</td>
        </tr>
        <tr>
            <td>총각무, 파, 열무</td>
            <td>60초 이상</td>
            <td rowspan="2">상하좌우<br>3회 이상 흔들어서</td>
        </tr>
        <tr>
            <td>고들빼기</td>
            <td>120초 이상</td>
        </tr>
        
        <tr>
            <td class="bg-gray">주기</td>
            <td colspan="7" class="text-left">작업시작 시 / 작업 중 1시간 마다 / 작업종료 시</td>
        </tr>
        <tr>
            <td class="bg-gray">방법</td>
            <td colspan="7" class="text-left font-small" style="line-height: 1.4;">
                ① 수도미터를 확인하여 세척수량이 1분에 20리터 이상인지 확인한다.<br>
                ② 저울을 세척하는 원료를 계량하여 확인한다.<br>
                ③ 세척작업자가 세척횟수 및 방법을 지키는지 육안으로 확인한다.<br>
                ④ 초시계를 이용하여 세척시간이 원료별로 세척하는 지 확인한다.<br>
                ⑤ 세척수를 세척수 교체주기마다 교체하는 지 확인한다.<br>
                □ CCP-2BP 모니터링담당자는 한계기준 이탈 시, HACCP팀장에게 보고 후 개선조치 실시 및 결과 기록.
            </td>
        </tr>
    </table>

    <table>
        <tr class="bg-gray">
            <td style="width: 10%;">구분</td>
            <td style="width: 15%;">품명</td>
            <td>측정시각</td>
            <td>세척수량<br>(L/분)</td>
            <td>원물투입량<br>(KG)</td>
            <td>세척횟수</td>
            <td>세척시간</td>
            <td>세척방법</td>
            <td style="width: 10%;">세척수교체<br>(오전/오후)</td>
        </tr>

        <tr>
            <td class="bg-gray">작업 전</td>
            <td><input type="text" name="items[0][product]" placeholder="품명"></td>
            <td><input type="time" name="items[0][time]"></td>
            <td><input type="text" name="items[0][volume]"></td>
            <td>-</td> <td>-</td>
            <td>-</td>
            <td>-</td>
            <td rowspan="8">
                오전: <select name="items[0][change_am]"><option value=""></option><option value="O">O</option><option value="X">X</option></select><br><br>
                오후: <select name="items[0][change_pm]"><option value=""></option><option value="O">O</option><option value="X">X</option></select>
            </td>
            <input type="hidden" name="items[0][type]" value="작업전">
        </tr>

        <?php for($i=1; $i<=7; $i++): ?>
        <tr>
            <td><input type="text" name="items[<?=$i?>][type]" value="" placeholder="입력"></td> <td><input type="text" name="items[<?=$i?>][product]"></td>
            <td><input type="time" name="items[<?=$i?>][time]"></td>
            <td><input type="text" name="items[<?=$i?>][volume]"></td>
            <td><input type="text" name="items[<?=$i?>][weight]"></td>
            <td><input type="text" name="items[<?=$i?>][count]"></td>
            <td><input type="text" name="items[<?=$i?>][duration]"></td>
            <td><input type="text" name="items[<?=$i?>][method]"></td>
        </tr>
        <?php endfor; ?>

        <tr>
            <td class="bg-gray">작업 종료</td>
            <td><input type="text" name="items[8][product]"></td>
            <td><input type="time" name="items[8][time]"></td>
            <td><input type="text" name="items[8][volume]"></td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>
                <select name="items[8][change]"><option value=""></option><option value="O">O</option><option value="X">X</option></select>
            </td>
            <input type="hidden" name="items[8][type]" value="작업종료">
        </tr>
    </table>

    <table>
        <tr>
            <td class="bg-gray" style="width: 10%;">개선조치<br>방법</td>
            <td class="text-left font-small" style="line-height: 1.4;">
                □ 세척수량, 원료투입량, 세척횟수, 세척시간, 세척방법, 세척수교체주기 이탈 시<br>
                &nbsp;① 모니터링담당자는 즉시 세척작업자에게 공정품 다시 세척을 실시한다.<br>
                &nbsp;② 다시 세척 후에는 모니터링담당자(전무)는 공정품을 분리하고 즉시 HACCP팀장에게 보고한 후 공정품 검사를 실시한다.<br>
                &nbsp;③ 검사결과에 이상이 없으면 다음 공정을 진행하고, 기준을 벗어나면 폐기하도록 즉시 조치한다.<br>
                &nbsp;④ 모니터링담당자는 이탈발생 내역을 CCP-1BP 모니터링 점검표 개선조치란에 기록하고, HACCP팀장에게 보고한다.<br>
                
                □ 기계적 고장인 경우(세척기, 수도미터, 급수배관)<br>
                &nbsp;① 세척담당자는 즉시 세척작업을 중지하고 공정품을 보류한 뒤, HACCP팀장에게 보고한다.<br>
                &nbsp;② HACCP팀장은 기계를 즉시 수리하고, 수리가 불가능할 경우 협력업체에 수리 의뢰한다.<br>
                &nbsp;③ 수리 완료 후 부적합 공정품에 대하여 재세척 실시 및 보고.
            </td>
        </tr>
    </table>

    <table style="height: 150px;">
        <tr class="bg-gray" style="height: 30px;">
            <td style="width: 40%;">한계기준 이탈내용</td>
            <td style="width: 40%;">개선조치 및 결과</td>
            <td style="width: 10%;">조치자</td>
            <td style="width: 10%;">확인</td>
        </tr>
        <tr>
            <td style="vertical-align: top;"><textarea name="deviation_content" style="height: 100%;"></textarea></td>
            <td style="vertical-align: top;"><textarea name="action_result" style="height: 100%;"></textarea></td>
            <td><input type="text" name="action_actor"></td>
            <td><input type="text" name="action_checker"></td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 20px;">
        <button type="submit" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">일지 저장하기</button>
    </div>

</form>

<script>
    // 간단한 JavaScript: 페이지 로드 시 필요한 초기화가 있다면 여기에 작성
    document.addEventListener("DOMContentLoaded", function() {
        console.log("Monitoring Log Loaded");
    });
</script>

</body>
</html>