<?php
session_start();
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once("controllers/functions.php");

class Distributor extends Functions
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

    public function registerDistributor() {
        $adminName = $this->encryptString('dainlab', $this->param['adminName']);
        $adminMobile = $this->convertMobileNumber($this->param['adminMobile']);
        $adminMobile = $this->encryptPhoneNumber('dainlab', $adminMobile);
        $loginPwd = password_hash($this->param['loginPwd'], PASSWORD_DEFAULT);

        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'sk_distributor',
                'name' => $this->param['name'],
                'code' => $this->param['code'],
                'adminName' => $adminName,
                'adminMobile' => $adminMobile,
                'loginId' => $this->param['loginId'],
                'loginPwd' => $loginPwd
            );     
            $result = $this->insert($data)    ;
        } else {
            $data = array(
                'table' => 'sk_distributor',
                'where' => 'uid=' . $this->param['uid'],
                'name' => $this->param['name'],
                'code' => $this->param['code'],
                'adminName' => $adminName,
                'adminMobile' => $adminMobile,
                'loginId' => $this->param['loginId'],
                'loginPwd' => $loginPwd
            );  
            $result = $this->update($data) ;
        }

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 등록이 되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '총판 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function getDistributorList() {
        $where = $this->param['where'];
        $orderby = $this->param['orderby'];
        $asc = $this->param['asc'];
        $per = $this->param['per'];
        $start = ($this->param['page'] - 1) * $per;

        $query = "select * from sk_distributor {$where} order by {$orderby} {$asc} limit {$start}, {$per}";
        $this->query($query);

        // 모든 데이터를 한 번에 가져오기
        $results = $this->fetchAll(); // `fetchAll()`을 사용하여 모든 결과를 배열로 가져온다고 가정합니다.

        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'name' => $data['name'],
                'code' => $data['code'],
                'adminName' => $this->decryptString('dainlab', $data['adminName']),
                'adminMobile' => $this->decryptPhoneNumber('dainlab', $data['adminMobile']),
                'loginId' => $data['loginId'],
            ];
        }, $results);

        echo json_encode($this->response);
    }

    public function getDistributor() {
        $uid = $this->param['uid'];
        $query = "select * from sk_distributor where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'name' => $data['name'],
                'code' => $data['code'],
                'adminName' => $this->decryptString('dainlab', $data['adminName']),
                'adminMobile' => $this->decryptPhoneNumber('dainlab', $data['adminMobile']),
                'loginId' => $data['loginId'],
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    public function deleteDistributor() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from sk_distributor where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '총판 삭제 중 에러가 발생하였습니다'
                ];
            }
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'UID 값이 넘어오지 않았습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function getAgencyList() {
        $where = $this->param['where'];
        $orderby = $this->param['orderby'];
        $asc = $this->param['asc'];
        $per = $this->param['per'];
        $start = ($this->param['page'] - 1) * $per;

        $query = "select * from sk_agency {$where} order by {$orderby} {$asc} limit {$start}, {$per}";        
        $this->query($query);

        // 모든 데이터를 한 번에 가져오기
        $results = $this->fetchAll(); // `fetchAll()`을 사용하여 모든 결과를 배열로 가져온다고 가정합니다.

        $this->response = array_map(function($data) {
            return [
                'name' => $data['name'],
                'code' => $data['code'],
                'adminName' => $this->decryptString('dainlab', $data['adminName']),
                'adminMobile' => $this->decryptPhoneNumber('dainlab', $data['adminMobile']),
                'loginId' => $data['loginId'],
            ];
        }, $results);

        echo json_encode($this->response);
    }
}
?>