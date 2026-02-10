<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("controllers/functions.php");

class Login extends Functions
{
	private $param;
	private $now;
	private $nowTime;
    private $response = [];

	public function __construct($param) {
		$this->param = $param;
		$this->now = date("Y-m-d");
		$this->nowTime = date("Y-m-d H:i:s");
	}

	public function login() {
        $id = $this->param['id'];
        $pwd = $this->param['pwd'];
        $response = [];
    
        if (!isset($_SESSION['loginId'])) {
            $query = "SELECT * FROM system_admin WHERE adminId='" . $id . "'";
            if(!$this->query($query)) $this->errorLog('academy', 'login', $query);
            $sa = $this->fetch();			
    
            // Check if $sa is not null before accessing its keys
            if ($sa && $id == $sa['adminId'] && password_verify($pwd, $sa['adminPwd'])) {
                $_SESSION['loginUid'] = 10000;
                $_SESSION['loginId'] = "super";
                $_SESSION['loginName'] = "슈퍼관리자";
                $_SESSION['loginLevel'] = "1000";
    
                $this->response = [
                    'result' => 'success',
                    'name' => '슈퍼관리자',
                    'loginLevel' => $_SESSION['loginLevel']
                ];
                $this->registerLoginUser($_SESSION['loginId']);
            } else {
                $query = "SELECT * FROM adminst WHERE id='" . $id . "'";
                if(!$this->query($query)) $this->errorLog('academy', 'login', $query);
                $admin = $this->fetch();			
        
                // Check if $sa is not null before accessing its keys
                if ($admin && $id == $admin['id'] && password_verify($pwd, $admin['pwd'])) {
                    $_SESSION['loginUid'] = $admin['uid'];
                    $_SESSION['loginId'] = $admin['id'];
                    $_SESSION['loginName'] = $admin['name'];
                    $_SESSION['loginLevel'] = "100";

                    $this->response = [
                        'result' => 'success',
                        'name' => '관리자',
                        'loginLevel' => $_SESSION['loginLevel']
                    ];

                    $this->registerLoginUser($_SESSION['loginId']);
                } else {
                    $query = "SELECT * FROM mes_user WHERE loginId='" . $id . "'";
                    if(!$this->query($query)) $this->errorLog('login', 'login', $query);
        
                    if($this->getRows() > 0) {
                        $user = $this->fetch();
        
                        if(password_verify($pwd, $user['loginPwd'])) {
                            $_SESSION['loginUid'] = 0;
                            $_SESSION['loginId'] = $id;
                            $_SESSION['loginName'] = $user['employeeName'];
                            $_SESSION['loginLevel'] = $user['auth'];
                            $_SESSION['academyUid'] = 999998;
        
                            $this->response = [
                                'result' => 'success',
                                'name' => $user['employeeName'],
                                'loginLevel' => $_SESSION['loginLevel']
                            ];

                            $this->registerLoginUser($_SESSION['loginId']);
                        } else {
                            $this->response = [
                                'result' => 'error',
                                'message' => '비밀번호가 일치하지 않습니다'
                            ];
                        }
                    } else {
                        $this->response = [
                            'result' => 'error',
                            'message' => '일치하는 사용자가 없습니다'
                        ];							
                    } 
                }
            }
    
            // Always echo $this->response instead of $response
            echo json_encode($this->response);
        }
    }
    
    public function registerLoginUser($loginId) {
        $currentDateTime = date('Y-m-d H:i:s');

        $data = array(
            'table' => 'mes_user_login',
            'loginId' => $loginId,
            'registerDate' => $currentDateTime
        );
        $this->insert($data);
    }
}
?>