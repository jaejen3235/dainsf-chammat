<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("controllers/functions.php");

class Agency extends Functions
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

    // 협력사 등록
    public function registerAgency() {
        $adminName = $this->encryptString('dainlab', $this->param['adminName']);
        $adminMobile = $this->convertMobileNumber($this->param['adminMobile']);
        $adminMobile = $this->encryptPhoneNumber('dainlab', $adminMobile);
        if(!empty($this->param['loginPwd'])) $loginPwd = password_hash($this->param['loginPwd'], PASSWORD_DEFAULT);

        if(empty($this->param['uid'])) {
            // 이미 등록된 이름이나 코드가 있는지 확인하자
            $query = "select uid from sk_agency where name='{$this->param['name']}' or code='{$this->param['code']}'";
            $this->query($query);
            if($this->getRows() > 0) {
                $this->response = [
                    'result' => 'error',
                    'message' => '이미 사용중인 협력사명이거나 코드명입니다'
                ];

                echo json_encode($this->response);
                exit;
            }
            $data = array(
                'table' => 'sk_agency',
                'distributor' => $_SESSION['distributorCode'],
                'name' => $this->param['name'],
                'code' => strtoupper($this->param['code']),
                'adminName' => $adminName,
                'adminMobile' => $adminMobile,
                'loginId' => $this->param['loginId'],
                'loginPwd' => $loginPwd
            );     
            $result = $this->insert($data)    ;
        } else {
            $data = array(
                'table' => 'sk_agency',
                'where' => 'uid=' . $this->param['uid'],                
                'name' => $this->param['name'],
                'code' => $this->param['code'],
                'adminName' => $adminName,
                'adminMobile' => $adminMobile,
                'loginId' => $this->param['loginId']
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
                'message' => '협력사 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // 협력사 리스트
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

    public function getAgencyListWithNo() {        
        $where = $this->param['where'];
        $orderby = $this->param['orderby'];
        $asc = $this->param['asc'];
        $per = $this->param['per'];
        $start = ($this->param['page'] - 1) * $per;
    
        // 총 게시물 개수 가져오기
        $countQuery = "select count(*) as totalCount from sk_agency {$where}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "select * from sk_agency {$where} order by {$orderby} {$asc} limit {$start}, {$per}";             
        $this->query($query);
    
        $results = $this->fetchAll();
    
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'adminName' => $this->decryptString('dainlab', $data['adminName']),
                    'adminMobile' => $this->decryptPhoneNumber('dainlab', $data['adminMobile']),
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    // 협력사 하나 가져오기
    public function getAgency() {
        $uid = $this->param['uid'];
        $query = "select * from sk_agency where uid={$uid}";        
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

    // 협력사 삭제
    public function deleteAgency() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from sk_agency where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '협력사 삭제 중 에러가 발생하였습니다'
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

    // 고객사 등록
    public function registerClient() {
        $name = $this->param['name'];
        
        if(empty($this->param['uid'])) {
            if(!empty($_FILES["logo"]["tmp_name"][0])) {
                $logo = $this->uploadFile('logo', '../attach/client/');
            } else {
                $logo = "";
            }
        } else {
            if(!empty($_FILES["logo"]["tmp_name"][0])) {
                $logo = $this->uploadFile('logo', '../attach/client/');
            } else {
                $logo = $this->param['oldFile'];
            }
        }

        if(empty($this->param['uid'])) {            
            $data = array(
                'table' => 'sk_client',
                'name' => $name,
                'logo' => $logo
            );     
            $result = $this->insert($data)    ;
        } else {
            $data = array(
                'table' => 'sk_client',
                'where' => 'uid=' . $this->param['uid'],                
                'name' => $name,
                'logo' => $logo
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
                'message' => '고객사 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // 고객사 리스트
    public function getClientList() {
        $where = $this->param['where'];
        $orderby = $this->param['orderby'];
        $asc = $this->param['asc'];
        $per = $this->param['per'];
        $start = ($this->param['page'] - 1) * $per;

        $query = "select * from sk_client {$where} order by {$orderby} {$asc} limit {$start}, {$per}";        
        $this->query($query);

        // 모든 데이터를 한 번에 가져오기
        $results = $this->fetchAll(); // `fetchAll()`을 사용하여 모든 결과를 배열로 가져온다고 가정합니다.

        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'name' => $data['name'],
                'logo' => $data['logo']
            ];
        }, $results);

        echo json_encode($this->response);
    }

    // 고객사 리스트
    public function getClientInfo() {
        $orderby = $this->param['orderby'];
        $asc = $this->param['asc'];
        if(isset($this->param['where'])) {
            $where = $this->param['where'];
            $query = "select * from sk_client {$where} order by {$orderby} {$asc}";        
        } else {
            $query = "select * from sk_client order by {$orderby} {$asc}";        
        }

        
        $this->query($query);

        // 모든 데이터를 한 번에 가져오기
        $results = $this->fetchAll(); // `fetchAll()`을 사용하여 모든 결과를 배열로 가져온다고 가정합니다.

        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'name' => $data['name'],
                'logo' => $data['logo']
            ];
        }, $results);

        echo json_encode($this->response);
    }

    // 고객사 하나 가져오기
    public function getClient() {
        $uid = $this->param['uid'];
        $query = "select * from sk_client where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'name' => $data['name'],
                'logo' => $data['logo'],
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 고객사 삭제
    public function deleteClient() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from sk_client where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '고객사 삭제 중 에러가 발생하였습니다'
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

    // 기기 카테고리 등록
    public function registerCategory() {
        // 이미 해당 테이블, 해당 필드에 같은 값이 있는지 검사
        if(!$this->checkSameFieldValue('sk_categorys', 'categoryName', $this->param['categoryName'])) {
            $this->response = [
                'result' => 'error',
                'message' => '이미 같은 이름이 있습니다'
            ];
        } else {
            if(empty($this->param['uid'])) {
                $data = array(
                    'table' => 'sk_categorys',
                    'categoryName' => $this->param['categoryName']
                );
                $result = $this->insert($data);
            } else {
                $data = array(
                    'table' => 'sk_categorys',
                    'where' => 'uid=' . $this->param['uid'],
                    'categoryName' => $this->param['categoryName']
                );
                $result = $this->update($data);
            }

            if($result) {
                $this->response = [
                    'result' => 'success',
                    'message' => '등록하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '카테고리 등록에 실패하였습니다'
                ];
            }
        }

        echo json_encode($this->response);
    }

    // 카테고리 리스트
    public function getCategoryList() {
        $query = "select * from sk_categorys order by uid asc";        
        $this->query($query);

        // 모든 데이터를 한 번에 가져오기
        $results = $this->fetchAll();

        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'categoryName' => $data['categoryName']                
            ];
        }, $results);

        echo json_encode($this->response);
    }

    // 카테고리 하나 가져오기
    public function getCategory() {
        $uid = $this->param['uid'];
        $query = "select * from sk_categorys where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'categoryName' => $data['categoryName']                
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    public function deleteCategory() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from sk_categorys where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '고객사 삭제 중 에러가 발생하였습니다'
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

    // 기기 등록
    public function registerDevice() {
        $price = $this->removeComma($this->param['price']);

        if(empty($this->param['uid'])) {
            if(!empty($_FILES["thumb1"]["tmp_name"][0])) {
                $thumb1 = $this->uploadFile('thumb1', '../attach/device/');
            } else {
                $thumb1 = "";
            }

            if(!empty($_FILES["thumb2"]["tmp_name"][0])) {
                $thumb2 = $this->uploadFile('thumb2', '../attach/device/');
            } else {
                $thumb2 = "";
            }

            if(!empty($_FILES["thumb3"]["tmp_name"][0])) {
                $thumb3 = $this->uploadFile('thumb3', '../attach/device/');
            } else {
                $thumb3 = "";
            }

            if(!empty($_FILES["content1"]["tmp_name"][0])) {
                $content1 = $this->uploadFile('content1', '../attach/device/');
            } else {
                $content1 = "";
            }

            if(!empty($_FILES["content2"]["tmp_name"][0])) {
                $content2 = $this->uploadFile('content2', '../attach/device/');
            } else {
                $content2 = "";
            }
        } else {
            if(!empty($_FILES["thumb1"]["tmp_name"][0])) {
                $thumb1 = $this->uploadFile('thumb1', '../attach/device/');
            } else {
                $thumb1 = $this->param['oldThumb1'];
            }

            if(!empty($_FILES["thumb2"]["tmp_name"][0])) {
                $thumb2 = $this->uploadFile('thumb2', '../attach/device/');
            } else {
                $thumb2 = $this->param['oldThumb2'];
            }

            if(!empty($_FILES["thumb3"]["tmp_name"][0])) {
                $thumb3 = $this->uploadFile('thumb3', '../attach/device/');
            } else {
                $thumb3 = $this->param['oldThumb3'];
            }

            if(!empty($_FILES["content1"]["tmp_name"][0])) {
                $content1 = $this->uploadFile('content1', '../attach/device/');
            } else {
                $content1 = $this->param['oldContent1'];
            }

            if(!empty($_FILES["content2"]["tmp_name"][0])) {
                $content2 = $this->uploadFile('content2', '../attach/device/');
            } else {
                $content2 = $this->param['oldContent2'];
            }
        }

        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'sk_devices',
                'deviceName' => $this->param['deviceName'],
                'model' => $this->param['model'],
                'price' => $price,
                'display' => $this->param['display'],
                'thumb1' => $thumb1,
                'thumb2' => $thumb2,
                'thumb3' => $thumb3,
                'content1' => $content1,
                'content2' => $content2,
                'etc' => $this->param['etc']
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'sk_devices',
                'where' => 'uid=' . $this->param['uid'],
                'deviceName' => $this->param['deviceName'],
                'model' => $this->param['model'],
                'price' => $price,
                'display' => $this->param['display'],
                'thumb1' => $thumb1,
                'thumb2' => $thumb2,
                'thumb3' => $thumb3,
                'content1' => $content1,
                'content2' => $content2,
                'etc' => $this->param['etc']
            );
            $result = $this->update($data);
        }

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '등록하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '기기 등록에 실패하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // 기기 리스트
    public function getDeviceList() {        
        $where = $this->param['where'];
        $orderby = $this->param['orderby'];
        $asc = $this->param['asc'];
        $per = $this->param['per'];
        $start = ($this->param['page'] - 1) * $per;

        $query = "select * from sk_devices {$where} order by {$orderby} {$asc} limit {$start}, {$per}";        
        $this->query($query);

        // 모든 데이터를 한 번에 가져오기
        $results = $this->fetchAll(); // `fetchAll()`을 사용하여 모든 결과를 배열로 가져온다고 가정합니다.

        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'category' => $data['category'],
                'deviceName' => $data['deviceName'],
                'model' => $data['model'],
                'price' => $data['price'],
                'display' => $data['display'],
                'thumb1' => $data['thumb1'],
                'thumb2' => $data['thumb2'],
                'thumb3' => $data['thumb3'],
            ];
        }, $results);

        echo json_encode($this->response);
    }

    // 기기 하나 가져오기
    public function getDevice() {
        $uid = $this->param['uid'];
        $query = "select * from sk_devices where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'category' => $data['category'],
                'deviceName' => $data['deviceName'],
                'model' => $data['model'],
                'price' => $data['price'],
                'display' => $data['display'],
                'thumb1' => $data['thumb1'],
                'thumb2' => $data['thumb2'],
                'thumb3' => $data['thumb3'],
                'content1' => $data['content1'],
                'content2' => $data['content2'],
                'etc' => $data['etc'],
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    public function deleteDevice() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "select * from sk_devices where uid={$uid}";
            $this->query($query);
            $img = $this->fetch();

            $query = "delete from sk_devices where uid={$uid}";
            if($this->query($query)) {
                $this->deleteImage("../attach/device/" . $img['thumb1']);
                $this->deleteImage("../attach/device/" . $img['thumb2']);
                $this->deleteImage("../attach/device/" . $img['thumb3']);
                $this->deleteImage("../attach/device/" . $img['content1']);
                $this->deleteImage("../attach/device/" . $img['content2']);

                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '기기 삭제 중 에러가 발생하였습니다'
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

    public function deleteDeviceImg() {
        $uid = $this->param['uid'];
        $fieldName = $this->param['fieldName'];
    
        // 허용된 필드명 리스트 (필요에 따라 추가)
        $allowedFields = ['thumb1', 'thumb2', 'thumb3', 'content1', 'content2'];  // 실제 데이터베이스의 필드명으로 수정 필요
    
        if ($uid && in_array($fieldName, $allowedFields)) {
            $query = "select {$fieldName} from sk_devices where uid={$uid}";
            $this->query($query);
            $img = $this->fetch();
            $file = $img[$fieldName];

            $this->deleteImage("../attach/device/" . $file);

            // 쿼리 실행
            $query = "UPDATE sk_devices SET {$fieldName} = '' WHERE uid = {$uid}";
            if ($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];


            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '이미지 삭제 중 에러가 발생하였습니다'
                ];
            }
        } else {
            $this->response = [
                'result' => 'error',
                'message' => $uid ? '잘못된 필드명이 전달되었습니다.' : 'UID 값이 넘어오지 않았습니다.'
            ];
        }
    
        echo json_encode($this->response);
    }

    public function deleteImage($imagePath) {
        // 이미지 파일이 실제로 존재하는지 확인
        if (file_exists($imagePath)) {
            // 파일 삭제
            if (@unlink($imagePath)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // 요금제 등록
    public function registerPayment() {
        $payment = $this->removeComma($this->param['payment']);

        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'sk_payment',
                'category' => $this->param['category'],
                'age' => $this->param['age'],
                'paymentName' => $this->param['paymentName'],
                'dataName' => $this->param['dataName'],
                'payment' => $payment,
                'display' => $this->param['display']
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'sk_payment',
                'where' => 'uid=' . $this->param['uid'],
                'category' => $this->param['category'],
                'age' => $this->param['age'],
                'paymentName' => $this->param['paymentName'],
                'dataName' => $this->param['dataName'],
                'payment' => $payment,
                'display' => $this->param['display']
            );
            $result = $this->update($data);
        }

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '등록하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '요금제 등록에 실패하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // 요금제 리스트
    public function getPaymentList() {        
        $where = $this->param['where'];
        $orderby = $this->param['orderby'];
        $asc = $this->param['asc'];
        $per = $this->param['per'];
        $start = ($this->param['page'] - 1) * $per;

        $query = "select * from sk_payment {$where} order by {$orderby} {$asc} limit {$start}, {$per}";        
        $this->query($query);

        // 모든 데이터를 한 번에 가져오기
        $results = $this->fetchAll(); // `fetchAll()`을 사용하여 모든 결과를 배열로 가져온다고 가정합니다.

        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'category' => $data['category'],
                'age' => $data['age'],
                'paymentName' => $data['paymentName'],
                'dataName' => $data['dataName'],
                'payment' => $data['payment'],
                'display' => $data['display'],
            ];
        }, $results);

        echo json_encode($this->response);
    }

    // 상품용 요금제 가져오기
    public function getAllPaymentList() {        
        $query = "select * from sk_payment";
        $this->query($query);

        // 모든 데이터를 한 번에 가져오기
        $results = $this->fetchAll(); // `fetchAll()`을 사용하여 모든 결과를 배열로 가져온다고 가정합니다.

        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'category' => $data['category'],
                'age' => $data['age'],
                'paymentName' => $data['paymentName'],
                'dataName' => $data['dataName'],
                'payment' => $data['payment'],
                'display' => $data['display'],
            ];
        }, $results);

        echo json_encode($this->response);
    }

    // 요금제 하나 가져오기
    public function getPayment() {
        $uid = $this->param['uid'];
        $query = "select * from sk_payment where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'category' => $data['category'],
                'age' => $data['age'],
                'paymentName' => $data['paymentName'],
                'dataName' => $data['dataName'],
                'payment' => $data['payment'],
                'display' => $data['display']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    public function getPaymentPrice() {
        $payment = $this->param['payment'];
        $query = "select * from sk_payment where paymentName='{$payment}'";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'result' => 'success',
                'age' => $data['age'],
                'payment' => $data['payment']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 요금제 삭제
    public function deletePayment() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from sk_payment where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '요금제 삭제 중 에러가 발생하였습니다'
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

    // 지원금 등록
    public function registerDiscount() {        
        $discounts = array_map([$this, 'removeComma'], $this->param['supportAmount']); // 지원금 입력 배열 처리
    
        // 유효성 검사: 각 클라이언트의 지원금 데이터가 이미 존재하는지 확인
        foreach ($this->param['clientUid'] as $index => $clientUid) {
            $query = "SELECT uid FROM sk_support WHERE agencyUid = '{$this->param['agencyUid']}' AND clientUid = '{$clientUid}'";
            $this->query($query);
    
            if ($this->getRows() > 0) {
                // 데이터가 존재할 경우 업데이트 로직
                $res = $this->fetch();

                $data = array(
                    'table' => 'sk_support',
                    'where' => 'uid=' . $res['uid'],
                    'discount' => $discounts[$index] // 현재 지원금 입력값
                );
                $result = $this->update($data); // 데이터베이스 업데이트
            } else {
                // 데이터가 존재하지 않을 경우 삽입 로직
                $data = array(
                    'table' => 'sk_support',
                    'agencyUid' => $this->param['agencyUid'],
                    'clientUid' => $clientUid,
                    'discount' => $discounts[$index] // 현재 지원금 입력값
                );
                $result = $this->insert($data); // 데이터베이스에 삽입
            }
        }
    
        // 최종 응답 처리
        if ($result) {
            $this->response = [
                'result' => 'success',
                'message' => '등록 하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '지원금 등록 중 에러가 발생하였습니다'
            ];
        }
    
        echo json_encode($this->response);
    }      

    // 지원금 삭제
    public function deleteDiscount() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from sk_support where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '지원금 삭제 중 에러가 발생하였습니다'
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

    // 지원금 가져오기
    public function getSupport() {
        $agencyUid = $this->param['agencyUid'];
        $clientUid = $this->param['clientUid'];
    
        $query = "SELECT * FROM sk_support WHERE agencyUid={$agencyUid} AND clientUid={$clientUid}";
        $this->query($query);
    
        if ($this->getRows() > 0) {
            $data = $this->fetch();
            $discount = isset($data['discount']) ? $data['discount'] : 0; // discount가 없을 경우 0으로 설정
    
            $this->response = [
                'result' => 'success',
                'uid' => $data['uid'],
                'discount' => $discount
            ];
        } else {
            // 데이터가 없을 경우 discount를 0으로 설정
            $this->response = [
                'result' => 'nothing',
                'discount' => 0 // 기본값으로 0 반환
            ];
        }
    
        echo json_encode($this->response);
    }
    

    // 상품 엑셀 등록
    public function registerGoodsExcel() {
        extract($_POST);
        ini_set("memory_limit", -1);
        error_reporting(E_ALL ^ E_NOTICE);
        require_once './library/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php';
        //echo ("<meta http-equiv='content-type' content='text/html; charset=utf-8'>");
    
        // 저장될 디렉토리
        $upfile_dir = "./attach/excel/";
        //CSV데이타 추출시 한글깨짐방지
        setlocale(LC_CTYPE, 'ko_KR.eucKR'); // CSV 한글 깨짐 문제
        //장시간 데이터 처리될경우
        set_time_limit(0);
    
        $upfile_name = $_FILES['excel']['name']; // 파일이름
        $upfile_type = $_FILES['excel']['type']; // 확장자
        $upfile_size = $_FILES['excel']['size']; // 파일크기
        $upfile_tmp  = $_FILES['excel']['tmp_name']; // 임시 디렉토리에 저장된 파일명
    
        //확장자 확인
        if(preg_match("/(\.(xls|XLS|xlsx|XLSX))$/i",$upfile_name)) { //|xlsx|XLSX
        } else {
            echo ("<script>window.alert('업로드를 할수 없는 파일 입니다.\\n\\r확장자가 [xls]인 경우만 업로드가 가능합니다.'); history.go(-1) </script>");
            exit;
        }
    
        if ($upfile_name){
        //폴더내에 동일한 파일이 있는지 검사하고 있으면 삭제
            if (file_exists("{$upfile_dir}/{$upfile_name}") ) { unlink("{$upfile_dir}/{$upfile_name}"); }
            if ( strlen($upfile_size) < 7 ) {
                $filesize = sprintf("%0.2f KB", $upfile_size/100000);
            } else{
                    $filesize = sprintf("%0.2f MB", $upfile_size/100000000);
            }

            if (move_uploaded_file($upfile_tmp,"{$upfile_dir}/{$upfile_name}")) {                
            } else {
                echo ("<script>window.alert('디렉토리에 복사실패'); history.go(-1) </script>");
                exit;
            }
            chmod("{$upfile_dir}/{$upfile_name}",0777); 
            //chown("{$upfile_dir}/{$upfile_name}",'nobody'); 
        }
            
        $filepath = "{$upfile_dir}/{$upfile_name}";
    
        $fid = $this->param['certifyKey'];
        $year = $this->param['year'];
        $month = $this->param['month'];
        try {
            $filetype = PHPExcel_IOFactory::identify($filepath);
            $reader = PHPExcel_IOFactory::createReader($filetype);
            $php_excel = $reader->load($filepath);
    
            $getSheetNames = $php_excel->getSheetNames();
    
            for($i = 0 ; $i < sizeof($getSheetNames) ; $i++ ){ 
                //echo "[[".$getSheetNames[$i]."]]";
                
                $sheet = $php_excel->getSheet($i);           // 시트번호
                $maxRow = $sheet->getHighestRow();          // 마지막 라인
                $maxColumn = $sheet->getHighestColumn();    // 마지막 칼럼
    
                $target = "A"."1".":"."$maxColumn"."$maxRow";
                $lines = $sheet->rangeToArray($target, NULL, TRUE, FALSE);
                    
                //echo sizeof($lines);
                $k = 0;
                foreach ($lines as $key => $line) {
                    $col = 0;
                    $item = array(
                        "A"=>$line[$col++],   
                        "B"=>$line[$col++],   
                        "C"=>$line[$col++],
                        "D"=>$line[$col++],
                        "E"=>$line[$col++],
                        "F"=>$line[$col++],
                        "G"=>$line[$col++],
                        "H"=>$line[$col++],
                        "I"=>$line[$col++],
                        "J"=>$line[$col++],
                    );
                        
                    //print_r($item["A"] .",". $item["B"].",". $item["C"].",". $item["D"].",". $item["E"].",". $item["F"] ."<br/>");
                    // $i == 1 일때는 건너 뛰기
                    // $i >= 2 일때부터 자료 입력
                    
                    if($k >= 1) { // 제목줄은 스킵
                        $category = $item['A']; // 카테고리
                        $model = $item['B']; // 모델
                        $paymentName = $item['C']; // 요금제명
                        $changeDevice = $item['D']; // 기기변경
                        $moveTelecom = $item['E']; // 통신사이동
                        $joinTelecom = $item['F']; // 신규가입
                        $supportFund = $item['G']; // 공시지원금
                        $supportChangeDevice = $item['H']; // 공시지원기기변경
                        $supportMoveTelecom = $item['I']; // 공시지원통신사이동
                        $supportJoinTelecom = $item['J']; // 공시지원신규가입

                        $supplement = '';
                        $integrity = '';
                        $integrity1 = true;
                        $integrity2 = true;
                        $integrity3 = true;
                        
                        // 카테고리 모델, 요금제 비교하여 격리 시키자
                        $query = "SELECT * FROM sk_categorys WHERE LOWER(categoryName) = LOWER('{$category}')";                        
                        $this->query($query);
                        if($this->getRows() <= 0) {
                            $integrity1 = false;
                            $supplement = '카테고리 불일치';
                        }

                        $query = "SELECT * FROM sk_devices WHERE LOWER(model) = LOWER('{$model}')";                        
                        $this->query($query);
                        if($this->getRows() <= 0) {
                            $integrity2 = false;
                            if($supplement != '') $supplement .= ", 기기모델 불일치";
                            else $supplement = "기기모델 불일치";
                        }

                        $query = "SELECT * FROM sk_payment WHERE LOWER(paymentName) = LOWER('{$paymentName}')";                        
                        $this->query($query);
                        if($this->getRows() <= 0) {
                            $integrity3 = false;
                            if($supplement != '') $supplement .= ", 요금제 불일치";
                            else $supplement = "요금제 불일치";
                        }

                        if(!$integrity1 || !$integrity2 || !$integrity3) $integrity = 'N';
                        else $integrity = 'Y';
                        

                        $data = array(
                            'table' => 'sk_goods',
                            'category' => $category,
                            'model' => $model,
                            'paymentName' => $paymentName,
                            'changeDevice' => $changeDevice,
                            'moveTelecom' => $moveTelecom,
                            'joinTelecom' => $joinTelecom,
                            'supportFund' => $supportFund,
                            'supportChangeDevice' => $supportChangeDevice,
                            'supportMoveTelecom' => $supportMoveTelecom,
                            'supportJoinTelecom' => $supportJoinTelecom,
                            'integrity' => $integrity,
                            'supplement' => $supplement
                        );
                        $this->insert($data);								                                            
                    }

                    $k++;
                }
            }

            $this->response = [
                'result' => 'success',
                'message' => '등록하였습니다'
            ];
        }
        catch (exception $e) {
            $this->response = [
                'result' => 'error',
                'message' => '엑셀파일 로드 중 에러가 발생하였습니다'
            ];
        }
        @unlink("{$upfile_dir}/{$upfile_name}");

        echo json_encode($this->response);
    }

    // 상품 등록
    public function registerGoods() {        
        $changeDevice = $this->removeComma($this->param['changeDevice']);
        $moveTelecom = $this->removeComma($this->param['moveTelecom']);
        $joinTelecom = $this->removeComma($this->param['joinTelecom']);
        $supportFund = $this->removeComma($this->param['supportFund']);
        $supportChangeDevice = $this->removeComma($this->param['supportChangeDevice']);
        $supportMoveTelecom = $this->removeComma($this->param['supportMoveTelecom']);
        $supportJoinTelecom = $this->removeComma($this->param['supportJoinTelecom']);

        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'sk_goods',
                'category' => $this->param['modalCategory'],
                'model' => $this->param['model'],
                'paymentName' => $this->param['paymentName'],
                'changeDevice' => $changeDevice,
                'moveTelecom' => $moveTelecom,
                'joinTelecom' => $joinTelecom,
                'supportFund' => $supportFund,
                'supportChangeDevice' => $supportChangeDevice,
                'supportMoveTelecom' => $supportMoveTelecom,
                'supportJoinTelecom' => $supportJoinTelecom
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'sk_goods',
                'where' => 'uid=' . $this->param['uid'],
                'category' => $this->param['modalCategory'],
                'model' => $this->param['model'],
                'paymentName' => $this->param['paymentName'],
                'changeDevice' => $changeDevice,
                'moveTelecom' => $moveTelecom,
                'joinTelecom' => $joinTelecom,
                'supportFund' => $supportFund,
                'supportChangeDevice' => $supportChangeDevice,
                'supportMoveTelecom' => $supportMoveTelecom,
                'supportJoinTelecom' => $supportJoinTelecom
            );
            $result = $this->update($data);
        }

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '등록 하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '상품 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // 상품 리스트
    public function getGoodsList() {        
        $where = $this->param['where'];
        $orderby = $this->param['orderby'];
        $asc = $this->param['asc'];
        $per = $this->param['per'];
        $start = ($this->param['page'] - 1) * $per;

        $query = "select * from sk_goods {$where} order by {$orderby} {$asc} limit {$start}, {$per}";        
        $this->query($query);

        // 모든 데이터를 한 번에 가져오기
        $results = $this->fetchAll(); // `fetchAll()`을 사용하여 모든 결과를 배열로 가져온다고 가정합니다.

        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'category' => $data['category'],
                'model' => $data['model'],
                'paymentName' => $data['paymentName'],
                'changeDevice' => $data['changeDevice'],
                'moveTelecom' => $data['moveTelecom'],
                'joinTelecom' => $data['joinTelecom'],
                'supportFund' => $data['supportFund'],
                'supportChangeDevice' => $data['supportChangeDevice'],
                'supportMoveTelecom' => $data['supportMoveTelecom'],
                'supportJoinTelecom' => $data['supportJoinTelecom'],
                'integrity' => $data['integrity'],
                'supplement' => $data['supplement']
            ];
        }, $results);

        echo json_encode($this->response);
    }

    // 상품 하나 가져오기
    public function getGoods() {
        $uid = $this->param['uid'];
        $query = "select * from sk_goods where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'category' => $data['category'],
                'model' => $data['model'],
                'paymentName' => $data['paymentName'],
                'changeDevice' => $data['changeDevice'],
                'moveTelecom' => $data['moveTelecom'],
                'joinTelecom' => $data['joinTelecom'],
                'supportFund' => $data['supportFund'],
                'supportChangeDevice' => $data['supportChangeDevice'],
                'supportMoveTelecom' => $data['supportMoveTelecom'],
                'supportJoinTelecom' => $data['supportJoinTelecom']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 상품 삭제
    public function deleteGoods() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from sk_goods where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '상품 삭제 중 에러가 발생하였습니다'
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

    // 메인슬라이드 이미지 등록
    public function registerBanner() {                
        if(empty($this->param['uid'])) {
            if(!empty($_FILES["banner"]["tmp_name"][0])) {
                $banner = $this->uploadFile('banner', '../attach/banner/');
            } else {
                $banner = "";
            }
        } else {
            if(!empty($_FILES["banner"]["tmp_name"][0])) {
                $banner = $this->uploadFile('banner', '../attach/banner/');
            } else {
                $banner = $this->param['oldFile'];
            }
        }

        if(empty($this->param['uid'])) {
            // seq 구하기
            $query = "select max(seq) as seq from sk_banner";
            $this->query($query);

            if($this->getRows() > 0) {
                $data = $this->fetch();
                $seq = $data['seq'] + 1;
            } else {
                $seq = 1;
            }

            $data = array(
                'table' => 'sk_banner',
                'banner' => $banner,
                'seq' => $seq,
                'used' => $this->param['used']
            );     
            $result = $this->insert($data)    ;
        } else {
            $data = array(
                'table' => 'sk_banner',
                'where' => 'uid=' . $this->param['uid'],                
                'banner' => $banner,
                'used' => $this->param['used']
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
                'message' => '메인슬라이드 이미지 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // 메인 슬라이드 리스트
    public function getBannerList() {        
        $orderby = $this->param['orderby'];
        $asc = $this->param['asc'];

        $query = "select * from sk_banner order by {$orderby} {$asc}";        
        $this->query($query);

        // 모든 데이터를 한 번에 가져오기
        $results = $this->fetchAll(); // `fetchAll()`을 사용하여 모든 결과를 배열로 가져온다고 가정합니다.

        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'banner' => $data['banner'],
                'seq' => $data['seq'],
                'used' => $data['used']
            ];
        }, $results);

        echo json_encode($this->response);
    }

    // 출력순서 변경
    public function changeBannerSeq() {
        $uid = $this->param['uid'];
        $seq = $this->param['seq'];
        $updown = $this->param['updown'];
        $new = $seq;
    
        // seq가 1이면 down할 수 없도록 처리
        if ($updown == 'down' && $seq == 1) {
            $this->response = [
                'result' => 'error',
                'message' => '최저 순서는 1입니다. 더 이상 내릴 수 없습니다.'
            ];
            echo json_encode($this->response);
            return;
        }
    
        if ($updown == 'up') {
            // 자신보다 큰 seq 중 가장 작은 배너를 조회
            $query = "SELECT uid FROM sk_banner WHERE seq > {$seq} ORDER BY seq ASC LIMIT 1";
            $this->query($query);
            $nextBanner = $this->fetch();
    
            if ($nextBanner) {
                // 가장 작은 seq 값을 가진 배너의 seq를 1 감소
                $queryUpdateNext = "UPDATE sk_banner SET seq = seq - 1 WHERE uid = {$nextBanner['uid']}";
                $this->query($queryUpdateNext);
    
                // 현재 배너의 seq를 1 증가
                $queryUpdateCurrent = "UPDATE sk_banner SET seq = seq + 1 WHERE uid = {$uid}";
                $this->query($queryUpdateCurrent);
            }
    
        } else if ($updown == 'down') {
            // 자신보다 작은 seq 중 가장 큰 배너를 조회
            $query = "SELECT uid FROM sk_banner WHERE seq < {$seq} ORDER BY seq desc LIMIT 1";
            $this->query($query);
            $prevBanner = $this->fetch();
    
            if ($prevBanner) {
                // 가장 큰 seq 값을 가진 배너의 seq를 1 증가
                $queryUpdatePrev = "UPDATE sk_banner SET seq = seq + 1 WHERE uid = {$prevBanner['uid']}";
                $this->query($queryUpdatePrev);
    
                // 현재 배너의 seq를 1 감소
                $queryUpdateCurrent = "UPDATE sk_banner SET seq = seq - 1 WHERE uid = {$uid}";
                $this->query($queryUpdateCurrent);
            }
        }
    
        $this->response = [
            'result' => 'success',
            'message' => '순서 변경이 완료되었습니다.'
        ];
    
        echo json_encode($this->response);
    }
    
    // 베너 하나 가져오기
    public function getBanner() {
        $uid = $this->param['uid'];
        $query = "select * from sk_banner where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'banner' => $data['banner'],
                'seq' => $data['seq'],
                'used' => $data['used']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 베너 삭제
    public function deleteBanner() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "select banner from sk_banner where uid={$uid}";
            $this->query($query);
            $banner = $this->fetch();

            $query = "delete from sk_banner where uid={$uid}";
            if($this->query($query)) {
                $this->deleteImage("../attach/banner/" . $banner['banner']);

                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '메인슬라이드 삭제 중 에러가 발생하였습니다'
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

    // 사이트 정보 등록
    public function registerInfo() {
        $uid = $this->param['uid'];
        
        if(empty($this->param['uid'])) {
            if(!empty($_FILES["favicon"]["tmp_name"][0])) {
                $favicon = $this->uploadFile('favicon', '../attach/');
            } else {
                $favicon = "";
            }
        } else {
            if(!empty($_FILES["favicon"]["tmp_name"][0])) {
                $favicon = $this->uploadFile('favicon', '../attach/');
            } else {
                $favicon = $this->param['oldFile'];
            }
        }        

        if(!$uid) {
            $data = array(
                'table' => 'sk_info',
                'title' => $this->param['title'],
                'favicon' => $favicon,
                'name' => $this->param['name'],
                'owner' => $this->param['owner'],
                'address' => $this->param['address'],
                'telephone' => $this->param['telephone'],
                'fax' => $this->param['fax'],
                'email' => $this->param['email'],
                'receiver' => $this->param['receiver'],
                'policy1' => $this->param['policy1'],
                'policy2' => $this->param['policy2'],
                'policy3' => $this->param['policy3']
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'sk_info',
                'where' => 'uid=' . $uid,
                'title' => $this->param['title'],
                'favicon' => $favicon,
                'name' => $this->param['name'],
                'owner' => $this->param['owner'],
                'address' => $this->param['address'],
                'telephone' => $this->param['telephone'],
                'fax' => $this->param['fax'],
                'email' => $this->param['email'],
                'receiver' => $this->param['receiver'],
                'policy1' => $this->param['policy1'],
                'policy2' => $this->param['policy2'],
                'policy3' => $this->param['policy3']
            );
            $result = $this->update($data);
        }

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '등록 하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // 정보 하나 가져오기
    public function getInfo() {
        $query = "select * from sk_info";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'title' => $data['title'],
                'favicon' => $data['favicon'],
                'name' => $data['name'],
                'owner' => $data['owner'],
                'address' => $data['address'],
                'telephone' => $data['telephone'],
                'fax' => $data['fax'],
                'email' => $data['email'],
                'receiver' => $data['receiver'],
                'policy1' => $data['policy1'],
                'policy2' => $data['policy2'],
                'policy3' => $data['policy3']
            ];

            $_SESSION['receiver'] = $data['receiver'];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // (홈페이지용) 약관 하나 가져오기
    public function getPolicy() {
        $query = "select * from sk_info";        
        $data = $this->queryFetch($query);

        switch($this->param['div']) {
            case "1" :
                $policy = $data['policy1'];
                break;

            case "2" :
                $policy = $data['policy2'];
                break;
            
            case "3" :
                $policy = $data['policy3'];
                break;
        }

        if ($data) {
            $this->response = [
                'policy' => $policy,
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 공지사항 등록
    public function registerNotice() {      
        $currentDateTime = date('Y-m-d H:i:s')  ;

        if(empty($this->param['uid'])) {
            if(!empty($_FILES["attach"]["tmp_name"][0])) {
                $attach = $this->uploadFile('attach', './attach/board/');
            } else {
                $attach = "";
            }
        } else {
            if(!empty($_FILES["attach"]["tmp_name"][0])) {
                $attach = $this->uploadFile('attach', './attach/board/');
            } else {
                $attach = $this->param['oldFile'];
            }
        }

        $fixed = (isset($this->param['fixed'])) ? 'Y' : 'N';

        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'sk_board',
                'title' => $this->param['title'],
                'fixed' => $fixed,
                'attach' => $attach,
                'content' => $this->param['content'],
                'registerName' => $this->decryptString('dainlab', $_SESSION['loginName']),
                'registerDateTime' => $currentDateTime,
                'viewCnt' => 0
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'sk_board',
                'where' => 'uid=' . $this->param['uid'],
                'title' => $this->param['title'],
                'fixed' => $fixed,
                'attach' => $attach,
                'content' => $this->param['content'],
                'registerName' => $this->decryptString('dainlab', $_SESSION['loginName']),
                'registerDateTime' => $currentDateTime
            );
            $result = $this->update($data);
        }

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '등록 하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '공지사항 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // 공지사항 리스트
    public function getNoticeList() {        
        $where = $this->param['where'];
        $orderby = $this->param['orderby'];
        $asc = $this->param['asc'];
        $per = $this->param['per'];
        $start = ($this->param['page'] - 1) * $per;
    
        // 총 게시물 개수 가져오기
        $countQuery = "select count(*) as totalCount from sk_board {$where}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "select * from sk_board {$where} order by {$orderby} {$asc} limit {$start}, {$per}";        
        $this->query($query);
    
        $results = $this->fetchAll();
    
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'title' => $data['title'],
                    'fixed' => $data['fixed'],
                    'attach' => $data['attach'],
                    'content' => $data['content'],
                    'registerName' => $data['registerName'],
                    'registerDateTime' => $data['registerDateTime'],
                    'viewCnt' => $data['viewCnt']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    public function getFixedNotice() {        
        // 고정된 게시물 목록 가져오기
        $query = "SELECT * FROM sk_board WHERE fixed = 'Y' ORDER BY uid DESC";        
        $this->query($query);
    
        // 쿼리 결과 가져오기
        $results = $this->fetchAll();
    
        // 응답 배열 생성
        $response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'title' => $data['title'],
                'fixed' => $data['fixed'],
                'attach' => $data['attach'],
                'content' => $data['content'],
                'registerName' => $data['registerName'],
                'registerDateTime' => $data['registerDateTime'],
                'viewCnt' => $data['viewCnt']
            ];
        }, $results);
    
        // 결과를 JSON으로 반환
        echo json_encode($response);
    }
    
    

    // 공지사항 하나 가져오기
    public function getNotice() {
        $uid = $this->param['uid'];
        $query = "select * from sk_board where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $date = substr($data['registerDateTime'], 0, 10);
            $this->response = [
                'uid' => $data['uid'],
                'title' => $data['title'],
                'fixed' => $data['fixed'],
                'attach' => $data['attach'],
                'content' => $data['content'],
                'registerName' => $data['registerName'],
                'registerDateTime' => $data['registerDateTime'],                
                'date' => $date,
                'viewCnt' => $data['viewCnt']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 공지사항 삭제
    public function deleteNotice() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from sk_board where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '상품 삭제 중 에러가 발생하였습니다'
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

    // 홈페이지에서 에이전시 로그인
    public function agencyLogin() {
		$agencyCode = strtoupper($this->param['agencyCode']);

        $query = "select * from sk_agency where code='{$agencyCode}'";
        $this->query($query);

        if($this->getRows() > 0) {
            $data = $this->fetch();

            $this->response = [
                'result' => 'success',
                'message' => '로그인 하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '존재하지 않는 기업코드입니다'
            ];
        }
        echo json_encode($this->response);	
    }

    public function getDevicePaymentList() {
        $uid = $this->param['uid'];
        $query = "select model from sk_devices where uid={$uid}";
        $device = $this->queryFetch($query);


    }

    // 선택된 기기의 요금제 가져오기
    public function getDevicePayment() {
        $uid = $this->param['uid'];
        $query = "select * from sk_devices where uid={$uid}";
        $device = $this->queryFetch($query);

        $model = $device['model'];

        $query = "select * from sk_goods where model='{$model}'";
        $this->query($query);    
        $results = $this->fetchAll();
        
        $this->response = [
            'deviceName' => $device['deviceName'],
            'model' => $device['model'],
            'price' => $device['price'],
            'thumb1' => $device['thumb1'],
            'thumb2' => $device['thumb2'],
            'thumb3' => $device['thumb3'],
            'content1' => $device['content1'],
            'content2' => $device['content2'],
            'data' => array_map(function($data) {
                return [
                    'paymentName' => $data['paymentName'],
                    'changeDevice' => $data['changeDevice'],
                    'moveTelecom' => $data['moveTelecom'],
                    'joinTelecom' => $data['joinTelecom'],
                    'supportFund' => $data['supportFund'],
                    'supportChangeDevice' => $data['supportChangeDevice'],
                    'supportMoveTelecom' => $data['supportMoveTelecom'],
                    'supportJoinTelecom' => $data['supportJoinTelecom']
                ];
            }, $results)
        ];

        echo json_encode($this->response);
    }

    // 상담신청 등록
    public function registerCounseling() {
        $currentDate = date('Y-m-d');
        $data = array(
            'table' => 'sk_counseling',
            'name' => $this->param['name'],
            'mobile' => $this->param['mobile'],
            'email' => $this->param['email'],
            'memo' => $this->param['memo'],
            'deviceName' => $this->param['deviceName'],
            'deviceModel' => $this->param['deviceModel'],
            'payment' => $this->param['payment'],
            'deviceMip' => $this->param['deviceMip'],
            'deviceRateMip' => $this->param['deviceRateMip'],
            'communicationPrice' => $this->param['communicationPrice'],
            'choiceContractSales' => $this->param['choiceContractSales'],
            'totalPrice' => $this->param['totalPrice'],
            'client' => $this->param['client'],
            'agency' => $this->param['agency'],
            'registerDate' => $currentDate,
            'state' => '신규'
        );

        $result = $this->insert($data);

        if($result) {
            $this->sendSms('상담신청이 접수되었습니다');
            $this->response = [
                'result' => 'success'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '상담신청 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // 상담신청 관리자 메모 등록
    public function registerCounselingMemo() {        
        $data = array(
            'table' => 'sk_counseling',
            'where' => 'uid=' . $this->param['uid'],
            'state' => $this->param['state'],
            'adminMemo' => $this->param['adminMemo']
        );

        $result = $this->update($data);

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '등록하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '상담신청 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function registerTelephoneCounseling() {
        $currentDate = date('Y-m-d');
        $data = array(
            'table' => 'sk_telephone_counseling',
            'name' => $this->param['name'],
            'mobile' => $this->param['mobile'],
            'memo' => $this->param['memo'],
            'registerDate' => $currentDate,
            'state' => '신규'
        );

        $result = $this->insert($data);

        if($result) {
            $this->sendSms('전화상담신청이 접수되었습니다');

            $this->response = [
                'result' => 'success',
                'message' => '전화상담신청을 등록하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '상담신청 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // 상담신청 리스트
    public function getCounselingList() {        
        $where = $this->param['where'];
        $orderby = $this->param['orderby'];
        $asc = $this->param['asc'];
        $per = $this->param['per'];
        $start = ($this->param['page'] - 1) * $per;
    
        // 총 게시물 개수 가져오기
        $countQuery = "SELECT count(*) as totalCount FROM sk_counseling {$where}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "SELECT * FROM sk_counseling {$where} ORDER BY {$orderby} {$asc} LIMIT {$start}, {$per}";        
        $this->query($query);
        $results = $this->fetchAll();
    
        // 데이터를 map하여 각 상담 항목별로 협력사 및 고객사 정보를 가져옴
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                // 협력사 이름 가져오기
                $agencyQuery = "SELECT name FROM sk_agency WHERE code='" . strtoupper($data['agency']) . "'";
                $this->query($agencyQuery);
                $agency = $this->fetch();
    
                // 고객사 이름 가져오기
                $clientQuery = "SELECT name FROM sk_client WHERE uid=" . $data['client'];
                $this->query($clientQuery);
                $client = $this->fetch();
    
                return [
                    'uid' => $data['uid'],
                    'name' => $data['name'],
                    'mobile' => $data['mobile'],
                    'email' => $data['email'],
                    'deviceName' => $data['deviceName'],
                    'deviceModel' => $data['deviceModel'],
                    'payment' => $data['payment'],
                    'deviceMip' => $data['deviceMip'],
                    'deviceRateMip' => $data['deviceRateMip'],
                    'communicationPrice' => $data['communicationPrice'],
                    'choiceContractSales' => $data['choiceContractSales'],
                    'totalPrice' => $data['totalPrice'],
                    'client' => $client['name'],
                    'agency' => $agency['name'],
                    'registerDate' => $data['registerDate'],
                    'state' => $data['state']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }
    

    // 상담신청 하나 가져오기
    public function getCounseling() {
        $uid = $this->param['uid'];
        $query = "select * from sk_counseling where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {            
            $this->response = [
                'uid' => $data['uid'],
                'name' => $data['name'],
                'mobile' => $data['mobile'],
                'email' => $data['email'],
                'memo' => $data['memo'],
                'deviceName' => $data['deviceName'],
                'deviceModel' => $data['deviceModel'],
                'payment' => $data['payment'],
                'deviceMip' => $data['deviceMip'],
                'deviceRateMip' => $data['deviceRateMip'],
                'communicationPrice' => $data['communicationPrice'],
                'choiceContractSales' => $data['choiceContractSales'],
                'totalPrice' => $data['totalPrice'],
                'client' => $data['client'],
                'agency' => $data['agency'],
                'registerDate' => $data['registerDate'],
                'state' => $data['state'],
                'adminMemo' => $data['adminMemo']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 상담신청 삭제
    public function deleteCounseling() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from sk_counseling where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '상담신청 삭제 중 에러가 발생하였습니다'
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

    // 전화상담신청 리스트
    public function getTelephoneCounselingList() {        
        $where = $this->param['where'];
        $orderby = $this->param['orderby'];
        $asc = $this->param['asc'];
        $per = $this->param['per'];
        $start = ($this->param['page'] - 1) * $per;
    
        // 총 게시물 개수 가져오기
        $countQuery = "select count(*) as totalCount from sk_telephone_counseling {$where}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "select * from sk_telephone_counseling {$where} order by {$orderby} {$asc} limit {$start}, {$per}";        
        $this->query($query);
    
        $results = $this->fetchAll();
    
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'name' => $data['name'],
                    'mobile' => $data['mobile'],
                    'memo' => $data['memo'],
                    'registerDate' => $data['registerDate'],
                    'state' => $data['state']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }
    

    // 전화상담신청 하나 가져오기
    public function getTelephoneCounseling() {
        $uid = $this->param['uid'];
        $query = "select * from sk_telephone_counseling where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $date = substr($data['registerDateTime'], 0, 10);
            $this->response = [
                'uid' => $data['uid'],
                'name' => $data['name'],
                'mobile' => $data['mobile'],
                'memo' => $data['memo'],
                'registerDate' => $data['registerDate'],
                'state' => $data['state']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 전화상담신청 삭제
    public function deleteTelephoneCounseling() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from sk_telephone_counseling where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '상담신청 삭제 중 에러가 발생하였습니다'
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

    public function registerBuy() {
        $currentDate = date('Y-m-d');

        if($this->param['tab1'] == '1') {
            $paymentType = '선택약정 할인';
        } else if($this->param['tab1'] == '2') {
            $paymentType = '공시지원금 할인';
        }

        if($this->param['tab2'] == '1') {
            $joinType = '기기 변경';
        } else if($this->param['tab2'] == '2') {
            $joinType = '통신사 이동';
        } else if($this->param['tab2'] == '3') {
            $joinType = '신규 가입';
        }

        // agency
        $query = "select name from sk_agency where code='" . strtoupper($this->param['agency']) . "'";
        $this->query($query);
        $agency = $this->fetch();

        // client
        $query = "select name from sk_client where uid=" . $this->param['client'];
        $this->query($query);
        $client = $this->fetch();

        $data = array(
            'table' => 'sk_buy',
            'agency' => $this->param['agency'],
            'agencyName' => $agency['name'],
            'client' => $this->param['client'],
            'clientName' => $client['name'],
            'mobile' => $this->param['mobile'],
            'device' => $this->param['device'],
            'deviceName' => $this->param['deviceName'],
            'deviceModel' => $this->param['deviceModel'],
            'paymentType' => $paymentType,
            'joinType' => $joinType,
            'age' => $this->param['age'],
            'payment' => $this->param['payment'],
            'deviceMip' => $this->param['deviceMip'],
            'deviceRateMip' => $this->param['deviceRateMip'],
            'communicationPrice' => $this->param['communicationPrice'],
            'choiceContractSales' => $this->param['choiceContractSales'],
            'totalPrice' => $this->param['totalPrice'],
            'registerDate' => $currentDate,
            'state' => '신규'
        );

        $result = $this->insert($data);

        if($result) {
            $this->sendSms($this->receiver, '구매신청이 접수되었습니다');
            $this->response = [
                'result' => 'success',
                'message' => '구매신청을 하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '구매신청 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // FAQ 등록
    public function registerFaq() {

        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'sk_faq',
                'title' => $this->param['title'],
                'content' => $this->param['content']
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'sk_faq',
                'where' => 'uid=' . $this->param['uid'],
                'title' => $this->param['title'],
                'content' => $this->param['content']
            );
            $result = $this->update($data);
        }

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '등록 하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'FAQ 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // FAQ 리스트
    public function getFaqList() {        
        $where = $this->param['where'];
        $orderby = $this->param['orderby'];
        $asc = $this->param['asc'];
        $per = $this->param['per'];
        $start = ($this->param['page'] - 1) * $per;
    
        // 총 게시물 개수 가져오기
        $countQuery = "select count(*) as totalCount from sk_board {$where}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "select * from sk_faq {$where} order by {$orderby} {$asc} limit {$start}, {$per}";        
        $this->query($query);
    
        $results = $this->fetchAll();
    
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'title' => $data['title'],
                    'content' => $data['content'],
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }
    

    // FAQ 하나 가져오기
    public function getFaq() {
        $uid = $this->param['uid'];
        $query = "select * from sk_faq where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {            
            $this->response = [
                'uid' => $data['uid'],
                'title' => $data['title'],
                'content' => $data['content']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // FAQ 삭제
    public function deleteFaq() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from sk_faq where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => 'FAQ 삭제 중 에러가 발생하였습니다'
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

    // BUY 리스트
    public function getBuyList() {        
        $where = $this->param['where'];
        $orderby = $this->param['orderby'];
        $asc = $this->param['asc'];
        $per = $this->param['per'];
        $start = ($this->param['page'] - 1) * $per;
    
        // 총 게시물 개수 가져오기
        $countQuery = "select count(*) as totalCount from sk_buy {$where}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "select * from sk_buy {$where} order by {$orderby} {$asc} limit {$start}, {$per}";        
        $this->query($query);
    
        $results = $this->fetchAll();
    
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'agencyName' => $data['agencyName'],
                    'clientName' => $data['clientName'],
                    'mobile' => $data['mobile'],
                    'deviceName' => $data['deviceName'],
                    'deviceModel' => $data['deviceModel'],
                    'paymentType' => $data['paymentType'],
                    'joinType' => $data['joinType'],
                    'age' => $data['age'],
                    'payment' => $data['payment'],
                    'registerDate' => $data['registerDate'],
                    'state' => $data['state']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    public function getBuy() {
        $uid = $this->param['uid'];
        $query = "select * from sk_buy where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {            
            $this->response = [
                'uid' => $data['uid'],
                'agencyName' => $data['agencyName'],
                'clientName' => $data['clientName'],
                'mobile' => $data['mobile'],
                'deviceName' => $data['deviceName'],
                'deviceModel' => $data['deviceModel'],
                'paymentType' => $data['paymentType'],
                'joinType' => $data['joinType'],
                'age' => $data['age'],
                'payment' => $data['payment'],
                'deviceMip' => $data['deviceMip'],
                'deviceRateMip' => $data['deviceRateMip'],
                'communicationPrice' => $data['communicationPrice'],
                'choiceContractSales' => $data['choiceContractSales'],
                'payment' => $data['payment'],
                'totalPrice' => $data['totalPrice'],
                'registerDate' => $data['registerDate'],
                'state' => $data['state']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    public function registerBuyMemo() {        
        $data = array(
            'table' => 'sk_buy',
            'where' => 'uid=' . $this->param['uid'],
            'state' => $this->param['state']
        );

        $result = $this->update($data);

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '등록하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function sendSms($msg) {
        if(empty($_SESSION['receiver'])) {
            echo "알림문자 수신자가 정의되지 않았습니다";
            exit;
        }
        /****************** 인증정보 시작 ******************/
		$sms_url = "https://apis.aligo.in/send/"; 
		$sms['user_id'] = "kiwankoo";
		$sms['key'] = "ipttuj3x94qsoo4r06cj5xnlr2p2ixvh";
		/****************** 인증정보 끝 ********************/

		$sms['msg'] =  $msg; 
		$sms['receiver'] = $_SESSION['receiver'];
		$sms['destination'] = '';
		$sms['sender'] = "010-3408-1864";
		$sms['testmode_yn'] = 'N';
		$sms['title'] = "안내";
		$sms['msg_type'] = 'SMS';

		$host_info = explode("/", $sms_url);
		$port = $host_info[0] == 'https:' ? 443 : 80;

		$oCurl = curl_init();
		curl_setopt($oCurl, CURLOPT_PORT, $port);
		curl_setopt($oCurl, CURLOPT_URL, $sms_url);
		curl_setopt($oCurl, CURLOPT_POST, 1);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS, $sms);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		$ret = curl_exec($oCurl);
		curl_close($oCurl);
		
		$retArr = json_decode($ret, true); // 결과배열
				
		if ($retArr !== null) {
			if (isset($retArr['message'])) {
				$message = $retArr['message'];
						
				// 결과에 따라 처리
				if ($message === 'success') {
                    $this->registLog("결과 값이 없습니다.");
				} else {
					$this->registLog("결과 값이 없습니다.");
				}
			} else {
				$this->registLog("JSON 디코드 실패");				
			}
        }
    }

    // 전화상담 신청의 상태값을 바꾼다
    public function changeTelephoneCounselingState() {
        $data = array(
            'table' => 'sk_telephone_counseling',
            'where' => 'uid='. $this->param['uid'],
            'state' => $this->param['state']
        );

        $result = $this->update($data);

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '상태를 변경하였습니다'
            ];            
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '상태 변경에 실패하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    //=================================================== 로그 기록 ===========================================================//
	public function registLog($log)
	{
		$logdate = date("Ymd");
		$myfile = fopen("./log/log_" . $logdate . ".txt", "a") or die("Unable to open file");
		$text = "\r\n[" . date("Y-m-d H:i:s") . "] - " . $log;
		fwrite($myfile, $text);
		fclose($myfile);
	}    
}
?>