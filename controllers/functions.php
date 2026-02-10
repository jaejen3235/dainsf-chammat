<?php
require_once("models/database.php");

class Functions extends Database {

	private $param;
	private $now;
	private $nowTime;
    private $response = [];

	public function __construct($param) {
		$this->param = $param;
		$this->now = date("Y-m-d");
		$this->nowTime = date("Y-m-d H:i:s");
	}
	
	public function get() {
		$query = "show full columns from ".$this->param['table'];
		$this->query($query);
		$row = $this->getRows();
		$field = array();

		while($data = $this->fetch()) {
			array_push($field, $data['Field']);
		}

		if($this->param['orderby'] == null) {
			$orderby = "uid"; 
		} else {
			$orderby = $this->param['orderby'];
		}

		if($this->param['asc'] == null) {
			$asc = "desc"; 
		} else {
			$asc = $this->param['asc'];
		}

		if($this->param['per'] == null) {
			$query = "select * from ".$this->param['table']." ".$this->param['where']." order by ".$orderby." ".$asc;
		} else {
			$query = "select * from ".$this->param['table']." ".$this->param['where']." order by ".$orderby." ".$asc." limit ".($this->param['page'] - 1) * $this->param['per'].", ".$this->param['per'];
		}

		$this->query($query);
		$i = 0;
		$re = array(); // $re 배열 초기화

		while($data = $this->fetch()) {
			for($k = 0 ; $k < $row ; $k++) {
				$re[$i][$field[$k]] = $this->convertNull($data[$field[$k]]);
			}
			$i++;
		}

		$encodedData = json_encode($re);
		echo $encodedData;
	}

	public function getOne() {
		$table = $this->param['table'];
		$where = $this->param['where'];

		// 테이블의 구조가져오기
		$query = "show full columns from " . $table;
		$this->query($query);
		$row = $this->getRows();
		$field = array();

		while($data = $this->fetch()) {
			array_push($field, $data['Field']);
		}

		$sql = "select * from " . $table . " " . $where;
		$this->query($sql);
		$data = $this->fetch();

		for($k = 0 ; $k < $row ; $k++) {
			$re[$field[$k]] = $this->convertNull($data[$field[$k]]);
		}

		echo json_encode($re);
	}

	public function getPaging() 
	{
		$query = "select count(".$this->param['select'].") as cnt from ".$this->param['table']." ".$this->param['where'];
		$this->query($query);
		$row = $this->fetch();

		if(($last = ceil($row['cnt']/$this->param['per'])) > 1) 
		{
			if($last < ($defc = 1+$this->param['block']*2)) 
			{
				$i = 1;
				$cond = $last;
			}
			elseif($last >= $defc) 
			{
				if($this->param['page'] < 2+$this->param['block']) {
					$i = 1;
					$cond = $defc/2+$this->param['page'];
					$laston = true;
				}
				elseif($this->param['page'] >= $last-$defc/2) 
				{
					$i = $last+1-$defc;
					$cond = $last;
					$firston = true;
				}
				elseif($this->param['page'] >= 2+$this->param['block']) 
				{
					$i = $this->param['page']-$this->param['block'];
					$cond = $this->param['page']+$this->param['block'];
					$firston = true;
					$laston = true;
				}
			}

			$tag = "<div class='pagination'>";

			if($firston) 
			{
				$tag .= "<i class='bx bx-chevrons-left hands' onclick='".$this->param['setPage']."({page : 1})'></i>";
			}
		
			for($i; $i <= $cond; $i++) 
			{
				if($i == $this->param['page']) 
				{
					$tag .= "<span class='active'>".$i."</span>";
				}
				else 
				{
					$tag .= "<span onclick='".$this->param['setPage']."({page : ".$i."})'>".$i."</span>";
				}
			}

			if($laston) 
			{
				$tag .= "<i class='bx bx-chevrons-right hands' onclick='".$this->param['setPage']."({page : ".$last."})'></i>";
			}
			else {
				$tag .= "";
			}
		
			$tag .= "</div>";
			$re['result'] = $tag;
		} else {
			$re['result'] = '';
		}

		echo json_encode($re);
	}

	// Null 문자 없애기
	public function convertNull($val) {
		if($val == null && $val == 0) return "";
		else return $val;
	}
	
	// Comma 없애기
	public function removeComma($str) {
		// 값이 없거나, 숫자가 아닌 경우 0 반환
		if (empty($str) || !is_numeric(str_replace(',', '', $str))) {
			return 0;
		}
	
		// 숫자라면 콤마를 제거하고 반환
		return str_replace(',', '', $str);
	}

	public function uploadFile($file, $targetDir) {
		// 허용되는 파일 확장자 목록
		$allowedExtensions = array("jpg", "JPG", "jpeg", "JPEG", "gif", "GIF", "png", "PNG", "zip", "ZIP", "xlsx", "XLSX", "xls", "XLS", "ico", "ICO");

		// 파일 업로드가 성공했는지 확인
		if (isset($_FILES[$file]['name']) && $_FILES[$file]['error'] === 0) {
			// 타겟 디렉토리가 존재하는지 확인하고 없으면 생성
			if (!file_exists($targetDir)) {
				mkdir($targetDir, 0755, true);
			}

			// 파일명 중복 방지를 위해 고유한 파일명 생성
			$prefix = time(); // 현재 시간을 접두사로 사용
			$uploadedExtension = pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION);

			// 허용되는 파일 확장자인지 확인
			if (!in_array(strtolower($uploadedExtension), $allowedExtensions)) {
				return false; // 허용되지 않는 파일 확장자
			}

			// 파일명 UTF-8 인코딩
			$newFileName = $prefix . "_" . uniqid() . "." . $uploadedExtension;
			$newFileName = mb_convert_encoding($newFileName, "UTF-8", "auto");

			// 파일을 타겟 디렉토리로 이동
			if (move_uploaded_file($_FILES[$file]['tmp_name'], $targetDir . $newFileName)) {
				return $newFileName;
			} else {
				return false; // 파일 업로드 실패
			}
		} else {
			return false; // 파일 업로드 오류
		}
	}

	// 유효한 휴대폰 번호인지 검사
	public function isValidPhoneNumber($phoneNumber) { // isValidPhoneNumber
		// 휴대폰 번호의 유효성을 검사하기 위한 정규식
		$pattern = '/^(010|011|016|017|018|019)-[0-9]{3,4}-[0-9]{4}$/';

		// 정규식을 통해 유효성 검사
		if (preg_match($pattern, $phoneNumber)) {
			return $phoneNumber; // 유효한 휴대폰 번호
		} else {
			return false; // 유효하지 않은 휴대폰 번호
		}
	}
	
	// 몇주차인지 반환
	public function getWeekOfMonth($dateString) {
		$date = new DateTime($dateString);
		$firstDayOfMonth = new DateTime($date->format('Y-m-01'));
		$dayOfWeek = $firstDayOfMonth->format('w');
		$day = $date->format('j');

		return ceil(($day + $dayOfWeek) / 7);
	}
	
	// 무슨요일인지 반환
	public function getDayOfWeek($dateString) {
		$daysOfWeek = array('일', '월', '화', '수', '목', '금', '토');
		$timestamp = strtotime($dateString);
		$dayOfWeekNumber = date('w', $timestamp);
		$dayOfWeek = $daysOfWeek[$dayOfWeekNumber];
		return $dayOfWeek;
	}

	// 주어진 문장의 바이트 수 계산 함수
	public function getByteCount($string) {
		return strlen(mb_convert_encoding($string, 'UTF-8', 'UTF-8'));
	}

	// SMS 타입과 포인트 결정 함수
	public function getSmsTypeAndPoint($message) {
		$byteCount = $this->getByteCount($message);

		if ($byteCount > 90) {
			$smsType = "lms";
			$smsPoint = 40;
		} else {
			$smsType = "sms";
			$smsPoint = 20;
		}

		return array("smsType" => $smsType, "smsPoint" => $smsPoint);
	}

	function manipulateDate($currentDate, $unit, $amount) {
		$date = new DateTime($currentDate);

		switch ($unit) {
			case 'day':
				$date->modify("+$amount days");
				break;
			case 'month':
				$date->modify("+$amount months");
				break;
			case 'year':
				$date->modify("+$amount years");
				break;
			default:
				return "Invalid unit";
		}

		return $date->format('Y-m-d');
	}
	

	// 인자로 받을 날짜와 오늘을 비교하여 결과값 리턴
	public function compareDateToToday($dbDate) {
		if($dbDate == null || $dbDate == "") {
			return false;
		}
		$today = new DateTime();
		$dbDateTime = new DateTime($dbDate);

		if ($dbDateTime < $today) {
			return false;
		} elseif ($dbDateTime > $today) {
			return true;
		} else {
			return true;
		}
	}

    // 공백 제거
	function checkAndProcessString($inputString, $action = null) {
		// 문자열 앞뒤의 공백을 제거합니다.
		$trimmedString = trim($inputString);

		// 공백을 제거한 후 문자열이 비어 있는지 확인합니다.
		if (empty($trimmedString)) {
			// 문자열이 공백인 경우
			return false;
		} else {
			// 문자열이 공백이 아닌 경우
			if ($action === 'sanitize') {
				// 'sanitize' 액션을 수행할 경우, HTML 태그와 스크립트를 제거합니다.
				$sanitizedString = strip_tags($trimmedString);
				return $sanitizedString;
			} else {
				// 다른 액션이 필요한 경우, 여기에서 필요한 작업을 수행합니다.
				return $trimmedString;
			}
		}

		// 사용 예제:
		/*
		$input = "  This is a test string.  ";
		$result = checkAndProcessString($input, 'sanitize');

		if ($result === false) {
			echo "문자열에 공백이 있거나 비어 있습니다.";
		} else {
			echo "처리된 문자열: " . $result;
		}
		*/
	}

    // 넘어온 인자를 - 로 연결하여 반환
    public function joinWithDash(...$args) {
        return implode('-', $args);
    }

    // 전화번호에 "-" 붙이기
	public function convertMobileNumber($num) {
		if(stristr($num,"-") === FALSE) {
			$t2Len = strlen($num) - 7;
			$t1 = substr($num,0,3);
			$t2 = substr($num,3,$t2Len);
			$t3 = substr($num,-4);

			$newStr = $t1."-".$t2."-".$t3;
			return $newStr;
		} else {
			return $num;
		}
	}

    // 숫자 암호화
    public function encryptPhoneNumber($encryption_key, $phone_number) {        
        // AES-256-CBC 암호화
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($iv_length);
    
        $encrypted = openssl_encrypt($phone_number, 'aes-256-cbc', $encryption_key, 0, $iv);
    
        // 암호문과 초기화 벡터를 Base64로 인코딩하여 저장
        $result = base64_encode($encrypted . '::' . $iv);
        return $result;
    }
    
    /**
     * 암호화된 전화번호를 복호화하는 함수
     *
     * @param string $encrypted_phone 암호화된 전화번호 (Base64 인코딩된 문자열)
     * @return string 원본 전화번호 (평문)
     */
    public function decryptPhoneNumber($encryption_key, $encrypted_phone) {        
        // Base64 디코딩
        $decoded = base64_decode($encrypted_phone);
    
        // 초기화 벡터와 암호문 분리
        list($encrypted_data, $iv) = explode('::', $decoded);
    
        // AES-256-CBC 복호화
        $decrypted = openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    
        return $decrypted;
    }

    // 문자 암호화
    public function encryptString($encryption_key, $plaintext) {        
        // AES-256-CBC 암호화
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($iv_length);
    
        $encrypted = openssl_encrypt($plaintext, 'aes-256-cbc', $encryption_key, 0, $iv);
    
        // 암호문과 초기화 벡터를 Base64로 인코딩하여 저장
        $result = base64_encode($encrypted . '::' . $iv);
        return $result;
    }
    
    /**
     * 암호화된 문자열을 복호화하는 함수
     *
     * @param string $encrypted_text 암호화된 문자열 (Base64 인코딩된 문자열)
     * @return string 원본 문자열 (UTF-8 인코딩)
     */
    public function decryptString($encryption_key, $encrypted_text) {        
        // Base64 디코딩
        $decoded = base64_decode($encrypted_text);
    
        // 초기화 벡터와 암호문 분리
        list($encrypted_data, $iv) = explode('::', $decoded);
    
        // AES-256-CBC 복호화
        $decrypted = openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    
        return $decrypted;
    }

    // 마지막 문자 삭제
	public function cutLastStr($str) {
		return substr($str, 0, -1);
	}

    // 선택 삭제
    public function deleteSelected() {        
		$uids = $this->cutLastStr($this->param['uids']);        
        $uidArr = explode(",", $uids);
        $table = $this->param['table'];

        foreach ($uidArr as $uid) {
            if (!$this->deleteQuery($table, $uid)) {
                $this->response = [
                    'result' => 'error',
                    'message' => '삭제에 실패하였습니다'
                ];
                    
                echo json_encode($this->response);
                exit;
            } else {
                $this->response = [
                    'result' => 'success',
                    'message' => '데이터를 삭제하였습니다'
                ];                
            }
        }        

		echo json_encode($this->response);
	}

    // 특정 테이블의 특정 필드에 같은 값이 있는지 검사
    public function checkSameFieldValue($table, $field, $value) {
        $query = "select * from {$table} where {$field}='{$value}'"; //문자열만 비교
        $this->subQuery($query);

        if($this->getSubRows() > 0) return false;
        else return true;
    }

    // 테이블 특정 row 삭제
    public function deleteRow() {
        $result = $this->deleteQuery($this->param['table'], $this->param['uid']);

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '삭제하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '삭제에 실패하였습니다'
            ];
        }

        echo json_encode($this->response);
    }
    public function getFieldValue($table, $field, $uid) {
        $query = "SELECT {$field} FROM {$table} WHERE uid = {$uid}";
        $this->query($query);
        $data = $this->fetch();

        return $data[$field]; // 동적 필드명을 배열로 접근할 때 대괄호 사용
    }
    
    // DB table 의 한줄을 가져오기
    public function getData($table, $uid) {
        $query = "select * from {$table} where uid={$uid}";
        $this->subQuery($query);
        $data = $this->subFetch();

        return $data;
    }

    // 메시지를 출력하고 프로그램을 중단한다
    public function stop($result, $message) {
        $response = [
            'result' => $result,
            'message' => $message
        ];

        echo json_encode($response);
        exit;
    }

	// table에 값이 있는지 검사
	public function checkTable($table) {
		$query = "select uid from {$table}";
		$this->query($query);
		$data = $this->fetch();

		if($this->getRows() > 0) return $data['uid'];
		else return 0;
	}

    public function safe_intval($value) {
        // PHP의 기본 intval() 함수를 사용하여 입력 값을 정수로 강제 변환합니다.
        return intval($value);
    }
    
    // 사용 예: $accountUid = safe_intval($this->param['account']);
    // (주의: PHP 7.0 이상부터 intval('abc')는 0을 반환합니다.)

    public function safe_escape($value) {
        if (is_array($value)) {
            // 배열은 처리하지 않고 빈 문자열 반환 (배열 통째로 쿼리에 들어가는 것 방지)
            return '';
        }
        // null이나 정의되지 않은 경우를 대비해 (string)으로 캐스팅 후 trim
        $safe_value = trim((string)($value ?? '')); 
        
        // PHP의 addslashes() 함수를 사용하여 작은따옴표('), 큰따옴표("), 백슬래시(\) 등을 이스케이프 처리합니다.
        return addslashes($safe_value);
    }

    public function delete($table, $where) {
        // 1. 테이블 이름을 안전하게 처리 (테이블 이름은 쿼리 구조에 영향을 미치므로 검증 필요)
        // 여기서는 간단히 문자열로 가정하고, 테이블 이름 자체에 대한 공격은 외부에서 방지되었다고 가정합니다.
        $safe_table = trim($table); 
    
        // 2. WHERE 절이 비어있는지 확인
        // WHERE 절이 없으면 테이블 전체가 삭제되므로, 안전을 위해 WHERE 절이 비어있으면 오류를 반환하거나 실행을 막아야 합니다.
        $safe_where = trim($where);
        if (empty($safe_where)) {
            // 실제 운영 환경에서는 로그를 남기거나 예외를 발생시켜야 합니다.
            // echo "ERROR: DELETE 쿼리에는 반드시 WHERE 조건이 포함되어야 합니다."; 
            return false;
        }
    
        // 3. DELETE 쿼리 생성
        // $safe_table과 $safe_where를 사용하여 쿼리를 조합합니다.
        $query = "DELETE FROM " . $safe_table . " WHERE " . $safe_where;
    
        // 4. 쿼리 실행
        // 이 클래스에 정의된 $this->query() 함수를 사용합니다.
        // $this->query()는 실행 성공 시 true, 실패 시 false를 반환한다고 가정합니다.
        $result = $this->query($query);
    
        return $result;
    }
}
?>