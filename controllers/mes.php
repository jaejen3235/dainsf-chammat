<?php
session_start();
date_default_timezone_set('Asia/Seoul');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("controllers/functions.php");

class Mes extends Functions
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

    //===============================================================================================================================//
    // 기준정보 설정
    //===============================================================================================================================//
    
    // 품목 구분 등록
    public function registerItemDiv() {
        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'mes_classification',
                'name' => $this->param['name']
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'mes_classification',
                'where' => 'uid=' . $this->param['uid'],
                'name' => $this->param['name']
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
                'message' => '품목 구분 등록에 실패하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function getItemDivList() {
        // 쿼리 실행
        $query = "SELECT * FROM mes_classification";
        $this->query($query);
        $results = $this->fetchAll();
            
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        }, $results);
            
        // JSON 응답 반환
        echo json_encode($this->response);
    }

    public function getItemDiv() {
        $uid = $this->param['uid'];
        $query = "select * from mes_classification where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }


    // 품목 단위 등록
    public function registerItemUnit() {
        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'mes_unit',
                'name' => $this->param['name']
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'mes_unit',
                'where' => 'uid=' . $this->param['uid'],
                'name' => $this->param['name']
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
                'message' => '품목 단위 등록에 실패하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function getItemUnitList() {
        // 쿼리 실행
        $query = "SELECT * FROM mes_unit";
        $this->query($query);
        $results = $this->fetchAll();
            
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        }, $results);
            
        // JSON 응답 반환
        echo json_encode($this->response);
    }

    public function getItemUnit() {
        $uid = $this->param['uid'];
        $query = "select * from mes_unit where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 거래처 구분 등록
    public function registerAccountDiv() {
        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'mes_account_classification',
                'name' => $this->param['name']
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'mes_account_classification',
                'where' => 'uid=' . $this->param['uid'],
                'name' => $this->param['name']
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
                'message' => '거래처 구분 등록에 실패하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    
    public function getAccountClassificationList() {
        // 쿼리 실행
        $query = "SELECT * FROM mes_account_classification";
        $this->query($query);
        $results = $this->fetchAll();
            
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        }, $results);
            
        // JSON 응답 반환
        echo json_encode($this->response);
    }

    public function getAccountClassification() {
        $uid = $this->param['uid'];
        $query = "select * from mes_account_classification where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 부서 등록
    public function registerDepartment() {
        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'mes_department',
                'name' => $this->param['name']
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'mes_department',
                'where' => 'uid=' . $this->param['uid'],
                'name' => $this->param['name']
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
                'message' => '부서 등록에 실패하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function getDepartmentList() {
        // 쿼리 실행
        $query = "SELECT * FROM mes_department";
        $this->query($query);
        $results = $this->fetchAll();
            
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        }, $results);
            
        // JSON 응답 반환
        echo json_encode($this->response);
    }

    public function getDepartment() {
        $uid = $this->param['uid'];
        $query = "select * from mes_department where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 직급 등록
    public function registerRank() {
        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'mes_rank',
                'name' => $this->param['name']
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'mes_rank',
                'where' => 'uid=' . $this->param['uid'],
                'name' => $this->param['name']
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
                'message' => '직급 등록에 실패하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function getRankList() {
        // 쿼리 실행
        $query = "SELECT * FROM mes_rank";
        $this->query($query);
        $results = $this->fetchAll();
            
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        }, $results);
            
        // JSON 응답 반환
        echo json_encode($this->response);
    }

    public function getRank() {
        $uid = $this->param['uid'];
        $query = "select * from mes_rank where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 공정 등록
    public function registerProcess() {
        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'mes_process',
                'name' => $this->param['name'],
                'lastProcess' => $this->param['lastProcess']
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'mes_process',
                'where' => 'uid=' . $this->param['uid'],
                'name' => $this->param['name'],
                'lastProcess' => $this->param['lastProcess']
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
                'message' => '공정 등록에 실패하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function getProcessList() {
        // 쿼리 실행
        $query = "SELECT * FROM mes_process";
        $this->query($query);
        $results = $this->fetchAll();
            
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'name' => $data['name'],
                'lastProcess' => $data['lastProcess']
            ];
        }, $results);
            
        // JSON 응답 반환
        echo json_encode($this->response);
    }

    public function getProcess() {
        $uid = $this->param['uid'];
        $query = "select * from mes_process where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'name' => $data['name'],
                'lastProcess' => $data['lastProcess']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    //===============================================================================================================================//
    // 품목관리
    //===============================================================================================================================//

	// 품목구분 리스트
	public function getClassificationList() {
        $query = "select * from mes_classification order by uid desc";        
        $this->query($query);

        // 모든 데이터를 한 번에 가져오기
        $results = $this->fetchAll(); // `fetchAll()`을 사용하여 모든 결과를 배열로 가져온다고 가정합니다.

        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        }, $results);

        echo json_encode($this->response);
    }

	// 품목단위 리스트
	public function getUnitList() {
        $query = "select * from mes_unit order by uid desc";        
        $this->query($query);

        // 모든 데이터를 한 번에 가져오기
        $results = $this->fetchAll(); // `fetchAll()`을 사용하여 모든 결과를 배열로 가져온다고 가정합니다.

        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        }, $results);

        echo json_encode($this->response);
    }

	// 품목등록
	public function registerItem() {
        $stock_qty = $this->removeComma($this->param['stock_qty']);
        $safety_stock_qty = $this->removeComma($this->param['safety_stock_qty']);

        if(empty($this->param['uid'])) {
            // 이미 등록된 이름이나 코드가 있는지 확인하자
            $query = "select uid from mes_items where item_code='{$this->param['item_code']}'";
            $this->query($query);
            if($this->getRows() > 0) {
                $this->response = [
                    'result' => 'error',
                    'message' => '이미 사용중인 품번코드입니다'
                ];

                echo json_encode($this->response);
                exit;
            }

            $data = array(
                'table' => 'mes_items',
                'classification' => $this->param['classification'],
                'item_code' => strtoupper($this->param['item_code']),
                'item_name' => $this->param['item_name'],
                'standard' => $this->param['standard'],
                'unit' => $this->param['unit'],
                'stock_qty' => $stock_qty,
                'safety_stock_qty' => $safety_stock_qty,
				'price' => $this->param['price']
            );     
            $result = $this->insert($data)    ;
        } else {
            $data = array(
                'table' => 'mes_items',
                'where' => 'uid=' . $this->param['uid'],                
                'classification' => $this->param['classification'],
                'item_code' => strtoupper($this->param['item_code']),
                'item_name' => $this->param['item_name'],
                'standard' => $this->param['standard'],
                'unit' => $this->param['unit'],
                'stock_qty' => $stock_qty,
                'safety_stock_qty' => $safety_stock_qty,
				'price' => $this->param['price']
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
                'message' => '품목 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
	}

    public function getItemList() {        
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 총 게시물 개수 가져오기
        $amountQuery = "SELECT sum(stock_qty * price) as totalAmount FROM mes_items {$whereClause}";
        $this->query($amountQuery);
        $totalAmount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_items 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'totalAmount' => $totalAmount['totalAmount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'classification' => $data['classification'],
                    'item_code' => $data['item_code'],
                    'item_name' => $data['item_name'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'stock_qty' => $data['stock_qty'],
                    'safety_stock_qty' => $data['safety_stock_qty'],
                    'price' => $data['price']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    // 모든 품목 리스트 (selectbox용)
    public function getAllItemList() {
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        // 쿼리 실행
        $query = "SELECT * FROM mes_items {$where} ORDER BY uid desc";
        $this->query($query);
        $results = $this->fetchAll();
    
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'classification' => $data['classification'],
                'item_code' => $data['item_code'],
                'item_name' => $data['item_name'],
                'standard' => $data['standard'],
                'unit' => $data['unit']
            ];
        }, $results);
    
        // JSON 응답 반환
        echo json_encode($this->response);
    }

    public function registerAdjustItemStock() {
        $adjust_item_uid = $this->param['adjust_item_uid'];
        $adjust_stock_qty = $this->param['adjust_stock_qty'];

        if($adjust_item_uid && $adjust_stock_qty) {            
            $data = array(
                'table' => 'mes_items',
                'where' => 'uid=' . $adjust_item_uid,
                'stock_qty' => $adjust_stock_qty
            );
            $result = $this->update($data);

            if($result) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 조정하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '조정 중 에러가 발생하였습니다'
                ];
            }
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 품목 하나 가져오기
    public function getItem() {
        $uid = $this->param['uid'];
        $query = "select * from mes_items where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'classification' => $data['classification'],
                'item_code' => $data['item_code'],
                'item_name' => $data['item_name'],
                'standard' => $data['standard'],
                'unit' => $data['unit'],
                'stock_qty' => $data['stock_qty'],
                'safety_stock_qty' => $data['safety_stock_qty'],
                'price' => $data['price']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 품목 삭제
    public function deleteItem() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from mes_items where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '품목 삭제 중 에러가 발생하였습니다'
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

    //===============================================================================================================================//
    // 거래처
    //===============================================================================================================================//
	// 거래처 등록
	public function registerAccount() {
        $mobile = $this->convertMobileNumber($this->param['mobile']);
        $telephone = $this->convertMobileNumber($this->param['telephone']);
        $fax = $this->convertMobileNumber($this->param['fax']);

        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'mes_account',
                'classification' => $this->param['classification'],
                'name' => strtoupper($this->param['name']),
                'tax_number' => $this->param['tax_number'],
                'biz_number' => $this->param['biz_number'],
                'owner' => $this->param['owner'],
                'mobile' => $mobile,
                'telephone' => $telephone,
                'fax' => $fax,
				'email' => $this->param['email'],
				'address' => $this->param['address']
            );     
            $result = $this->insert($data)    ;
        } else {
            $data = array(
                'table' => 'mes_account',
                'where' => 'uid=' . $this->param['uid'],                
                'classification' => $this->param['classification'],
                'name' => strtoupper($this->param['name']),
                'tax_number' => $this->param['tax_number'],
                'biz_number' => $this->param['biz_number'],
                'owner' => $this->param['owner'],
                'mobile' => $mobile,
                'telephone' => $telephone,
                'fax' => $fax,
				'email' => $this->param['email'],
				'address' => $this->param['address']
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
                'message' => '거래처 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
	}

    // 거래처 리스트
    public function getAccountList() {        
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_account 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";             
        $this->query($query);
        $results = $this->fetchAll();
    
        // 응답 생성
        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'classification' => $data['classification'],                    
                    'name' => $data['name'],
                    'tax_number' => $data['tax_number'],
                    'biz_number' => $data['biz_number'],
                    'owner' => $data['owner'],
                    'telephone' => $data['telephone'],
                    'fax' => $data['fax'],
                    'mobile' => $data['mobile'],
                    'email' => $data['email'],
                    'address' => $data['address']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }    

    public function getAccountList2() {
        echo "aaa";
    }
    
    public function getAllAccountList() {
        // 쿼리 실행
        $query = "SELECT * FROM mes_account ORDER BY uid desc";
        $this->query($query);
        $results = $this->fetchAll();
    
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'classification' => $data['classification'],                    
                'name' => $data['name'],
                'tax_number' => $data['tax_number'],
                'biz_number' => $data['biz_number'],
                'owner' => $data['owner'],
                'telephone' => $data['telephone'],
                'fax' => $data['fax'],
                'mobile' => $data['mobile'],
                'email' => $data['email'],
                'address' => $data['address']
            ];
        }, $results);
    
        // JSON 응답 반환
        echo json_encode($this->response);
    }

    // 거래처 하나 가져오기
    public function getAccount() {
        $uid = $this->param['uid'];
        $query = "select * from mes_account where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'classification' => $data['classification'],                    
                'name' => $data['name'],
                'tax_number' => $data['tax_number'],
                'biz_number' => $data['biz_number'],
                'owner' => $data['owner'],
                'telephone' => $data['telephone'],
                'fax' => $data['fax'],
                'mobile' => $data['mobile'],
                'email' => $data['email'],
                'address' => $data['address']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 거래처 삭제
    public function deleteAccount() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from mes_account where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '거래처 삭제 중 에러가 발생하였습니다'
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

    //===============================================================================================================================//
    // 사원
    //===============================================================================================================================//

	// 사원 등록
	public function registerEmployee() {
        $mobile = $this->convertMobileNumber($this->param['mobile']);

        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'mes_employee',
                'name' => strtoupper($this->param['name']),
                'gender' => $this->param['gender'],
                'rank' => $this->param['rank'],
                'department' => $this->param['department'],
                'mobile' => $mobile,
				'email' => $this->param['email'],
				'address' => $this->param['address']
            );     
            $result = $this->insert($data)    ;
        } else {
            $data = array(
                'table' => 'mes_employee',
                'where' => 'uid=' . $this->param['uid'],                
                'name' => strtoupper($this->param['name']),
                'gender' => $this->param['gender'],
                'rank' => $this->param['rank'],
                'department' => $this->param['department'],
                'mobile' => $mobile,
				'email' => $this->param['email'],
				'address' => $this->param['address']
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
                'message' => '사원 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
	}

    // 사원 리스트
    public function getEmployeeList() {        
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 총 게시물 개수 가져오기
        $countQuery = "SELECT COUNT(*) as totalCount FROM mes_employee {$whereClause}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_employee 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],                 
                    'name' => $data['name'],
                    'gender' => $data['gender'],
                    'rank' => $data['rank'],
                    'department' => $data['department'],
                    'mobile' => $data['mobile'],
                    'email' => $data['email'],
                    'address' => $data['address']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    public function getAllEmployeeList() {
        $query = "select * from mes_employee order by uid asc";
        $this->query($query);
        $results = $this->fetchAll();
        echo json_encode([
            'result' => 'success',
            'data' => $results
        ]);
    }

    // 사원 하나 가져오기
    public function getEmployee() {
        $uid = $this->param['uid'];
        $query = "select * from mes_employee where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],               
                'name' => $data['name'],
                'gender' => $data['gender'],
                'rank' => $data['rank'],
                'department' => $data['department'],
                'mobile' => $data['mobile'],
                'email' => $data['email'],
                'address' => $data['address']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 사원 삭제
    public function deleteEmployee() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from mes_employee where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '사원 삭제 중 에러가 발생하였습니다'
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


    //===============================================================================================================================//
    // 불량유형 관리
    //===============================================================================================================================//
    public function registerDefect() {            
        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'mes_defects',                
                'defect_name' => strtoupper($this->param['defect_name']),
                'defect_symptom' => $this->param['defect_symptom'],
                'defect_process' => $this->param['defect_process'],
            );     
            $result = $this->insert($data)    ;
        } else {
            $data = array(
                'table' => 'mes_defects',
                'where' => 'uid=' . $this->param['uid'],                
                'defect_name' => strtoupper($this->param['defect_name']),
                'defect_symptom' => $this->param['defect_symptom'],
                'defect_process' => $this->param['defect_process'],
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
                'message' => '사원 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
	}

    public function getDefectList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_defects 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [            
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],   
                    'defect_name' => $data['defect_name'],
                    'defect_symptom' => $data['defect_symptom'],
                    'defect_process' => $data['defect_process'],
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    // 모든 불량 유형 리스트 (selectbox용)
    public function getAllDefectList() {
        // 쿼리 실행
        $query = "SELECT * FROM mes_defects ORDER BY uid ASC";
        $this->query($query);
        $results = $this->fetchAll();
    
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'defect_name' => $data['defect_name'],
                'defect_symptom' => $data['defect_symptom'],
                'defect_process' => $data['defect_process'],
            ];
        }, $results);
    
        // JSON 응답 반환
        echo json_encode($this->response);
    }

    // 불량 유형 하나 가져오기
    public function getDefect() {
        $uid = $this->param['uid'];
        $query = "select * from mes_defects where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],  
                'defect_name' => $data['defect_name'],
                'defect_symptom' => $data['defect_symptom'],
                'defect_process' => $data['defect_process'],
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 불량 유형 삭제
    public function deleteDefect() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from mes_defects where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '불량 유형 삭제 중 에러가 발생하였습니다'
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

    //===============================================================================================================================//
    // 수주
    //===============================================================================================================================//
    public function registerOrders() {
        // 1. 초기 변수 설정 및 파라미터 유효성 검사 (헬퍼 함수 적용)
        
        // 숫자형 데이터는 safe_intval()로 정수 강제 변환
        $accountUid = $this->safe_intval($this->param['account']); 
        $uid = $this->safe_intval($this->param['uid'] ?? 0); 
        
        // 문자열 데이터는 safe_escape()로 이스케이프
        $orderDate = $this->safe_escape($this->param['order_date']);
        $shipmentDate = $this->safe_escape($this->param['shipment_date']);
        $memo = $this->safe_escape($this->param['memo']);
        
        // 품목 관련 파라미터는 배열인지 확인하고 안전하게 할당
        $item = isset($this->param['item']) && is_array($this->param['item']) ? $this->param['item'] : [];
        $qty = isset($this->param['qty']) && is_array($this->param['qty']) ? $this->param['qty'] : [];
        $itemCount = count($item); 

        // 2. 거래처 정보 조회 (쿼리 문자열 직접 구성)
        $query = "SELECT name, address FROM mes_account WHERE uid = {$accountUid}";
        $this->query($query);
        $account = $this->fetch();

        if (!$account) {
            $this->response = ['result' => 'error', 'message' => '유효하지 않은 거래처 정보입니다.'];
            echo json_encode($this->response);
            return;
        }

        // 거래처 정보도 이스케이프 처리
        $accountName = $this->safe_escape($account['name']);
        $accountAddress = $this->safe_escape($account['address']);
        
        // 3. 기본값 설정 및 대체
        $inputShipmentPlace = $this->safe_escape($this->param['shipment_place'] ?? '');
        $shipmentPlace = (empty($inputShipmentPlace)) 
            ? $accountAddress
            : $inputShipmentPlace;
        
        $fid = null;
        $result = false;

        // 4. 주문 등록 (INSERT) 또는 수정 (UPDATE) 처리
        if (empty($uid)) {
            // 4-1. 신규 주문 등록
            $data = [
                'table' => 'mes_orders',
                'account_uid' => $accountUid,
                'account_name' => $accountName,
                'items' => '', 
                'order_date' => $orderDate,
                'shipment_date' => $shipmentDate,
                'shipment_place' => $shipmentPlace,
                'memo' => $memo,
                'status' => '주문'
            ];
            $result = $this->insert($data);
            $fid = $this->getUid(); 
        } else {
            // 4-2. 기존 주문 수정
            $fid = $uid;
            
            // 상태 확인 (쿼리 문자열 직접 구성)
            $query = "SELECT status FROM mes_orders WHERE uid = {$fid}";
            $this->query($query);
            $status = $this->fetch();

            if ($status && $status['status'] == '주문') {
                $data = [
                    'table' => 'mes_orders',
                    'where' => 'uid=' . $fid,
                    'account_uid' => $accountUid,
                    'account_name' => $accountName,
                    'items' => '', 
                    'order_date' => $orderDate,
                    'shipment_date' => $shipmentDate,
                    'shipment_place' => $shipmentPlace,
                    'memo' => $memo
                ]; 
                $result = $this->update($data);
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '납품중이거나 납품이 완료된 상태에서는 수정이 불가능합니다'
                ];
                echo json_encode($this->response);
                return;
            }
        }

        // 5. 주문 상세 품목 (mes_order_items) 처리
        if ($fid && $itemCount > 0) {
            
            // 5-1. 기존 주문 항목 삭제
            $this->delete('mes_order_items', 'fid=' . $fid); 
            
            // 5-2. 쿼리를 위한 고유(Unique) 품목 UID 목록 생성
            $itemUidsForQuery = [];
            foreach (array_unique(array_filter($item)) as $uidValue) {
                // safe_intval()로 정수만 남겨서 쿼리에 안전하게 삽입되도록 함
                $itemUidsForQuery[] = $this->safe_intval($uidValue); 
            }
            
            // 5-3. 단일 쿼리로 모든 품목 데이터 미리 가져오기 (N+1 문제 해결)
            $itemsData = [];
            if (!empty($itemUidsForQuery)) {
                // IN 절에 들어갈 목록을 쉼표로 연결 (직접 삽입)
                $uidList = implode(',', $itemUidsForQuery);
                $query = "SELECT * FROM mes_items WHERE uid IN ({$uidList})";
                
                $this->query($query);
                while ($row = $this->fetch()) {
                    // 가져온 품목 데이터도 safe_escape() 처리
                    $itemsData[$this->safe_intval($row['uid'])] = [
                        'item_name' => $this->safe_escape($row['item_name']), 
                        'item_code' => $this->safe_escape($row['item_code']),
                        'standard' => $this->safe_escape($row['standard']),
                        'unit' => $this->safe_escape($row['unit']),
                    ];
                }
            }
            
            // 5-4. 품목별 등록 및 itemsDescription 생성
            $itemsDescription = '';
            
            for ($key = 0; $key < $itemCount; $key++) { 
                $itemUid = $this->safe_intval($item[$key] ?? 0); // 각 품목 UID intval 처리
                $itemQty = isset($qty[$key]) ? $this->removeComma($qty[$key]) : 0;
                
                // 품목 정보 (미리 가져온 데이터)
                $itemData = $itemsData[$itemUid] ?? ['item_name' => '품목 정보 없음', 'item_code' => ''];
                
                // itemsDescription 설정 (첫 번째 항목만)
                if ($key === 0) {
                    $itemsDescription = ($itemCount > 1) 
                        ? $itemData['item_name'] . " 외 " . ($itemCount - 1) . "건" 
                        : $itemData['item_name'];
                }
                
                // 유효한 품목 UID와 수량일 경우에만 등록
                if (!empty($itemUid) && $itemQty > 0) {
                    $data = [
                        'table' => 'mes_order_items', 
                        'fid' => $fid,
                        'account_uid' => $accountUid,
                        'account_name' => $accountName,
                        'order_date' => $orderDate,
                        'shipment_date' => $shipmentDate,
                        'item_uid' => $itemUid,
                        'item_name' => $itemData['item_name'], 
                        'item_code' => $itemData['item_code'], 
                        'standard' => $itemData['standard'], 
                        'unit' => $itemData['unit'], 
                        'qty' => $itemQty,
                        'delivery_remain_qty' => $itemQty,                   
                        'product_status' => '주문',
                        'shipment_status' => '출하지시대기'
                    ];
                    
                    $this->insert($data); 
                }
            }
            
            // 5-5. 주문 마스터 테이블 (mes_orders)의 'items' 필드 업데이트
            if ($itemsDescription !== '') {
                $data = [
                    'table' => 'mes_orders',
                    'where' => 'uid=' . $fid,
                    'items' => $this->safe_escape($itemsDescription) // 최종 description도 이스케이프
                ];
                $this->update($data);
            }
        }

        // 6. 최종 응답
        if ($result) {
            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 등록이 되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '수주 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function getOrdersList() {        
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_orders 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'account_uid' => $data['account_uid'],
                    'account_name' => $data['account_name'],
                    'items' => $data['items'],
                    'order_date' => $data['order_date'],
                    'shipment_date' => $data['shipment_date'],
                    'shipment_place' => $data['shipment_place'],
                    'memo' => $data['memo'],
                    'status' => $data['status'],
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    // 수주 하나 가져오기
    public function getOrders() {
        $uid = $this->param['uid'];
        $query = "select * from mes_orders where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'account_uid' => $data['account_uid'],
                'account_name' => $data['account_name'],
                'items' => $data['items'],
                'order_date' => $data['order_date'],
                'shipment_date' => $data['shipment_date'],
                'shipment_place' => $data['shipment_place'],
                'memo' => $data['memo'],
                'status' => $data['status'],
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 수주 삭제
    public function deleteOrders() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from mes_orders where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '수주 삭제 중 에러가 발생하였습니다'
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

    public function deleteOrderItem() {
        $uid = $this->param['uid'];
        $query = "delete from mes_order_items where uid={$uid}";
        if($this->query($query)) {
            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 삭제하였습니다'
            ];
        }
        echo json_encode($this->response);
    }

    // 수주에 해당하는 품목 리스트 가져오기
    public function getAllOrdersItem() {
        $uid = $this->param['uid'];
        $query = "select * from mes_order_items where fid={$uid}";
        $this->query($query);
        $results = $this->fetchAll();

        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'account_uid' => $data['account_uid'],
                    'account_name' => $data['account_name'],
                    'order_date' => $data['order_date'],
                    'shipment_date' => $data['shipment_date'],
                    'item_uid' => $data['item_uid'],
                    'item_name' => $data['item_name'],
                    'item_code' => $data['item_code'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'qty' => $data['qty'],
                    'delivery_remain_qty' => $data['delivery_remain_qty'],
                    'product_status' => $data['product_status'],
                    'shipment_status' => $data['shipment_status']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
        
    }

    public function getOrdersItemList() {        
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_order_items 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";            
                
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'data' => array_map(function($data) {
                $query = "select * from mes_items where uid={$data['item_uid']}";
                $this->query($query);
                $item = $this->fetch();
                
                return [
                    'uid' => $data['uid'],
                    'account_uid' => $data['account_uid'],
                    'account_name' => $data['account_name'],
                    'order_date' => $data['order_date'],
                    'shipment_date' => $data['shipment_date'],
                    'item_uid' => $data['item_uid'],
                    'item_name' => $data['item_name'],
                    'item_code' => $data['item_code'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'qty' => $data['qty'],
                    'delivery_remain_qty' => $data['delivery_remain_qty'],
                    'product_status' => $data['product_status'],
                    'shipment_status' => $data['shipment_status'],
                    'stock_qty' => $item['stock_qty']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    // 출하 지시 목록 엑셀 다운로드
    public function getOrdersItemListExcel() {
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid';
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC';

        $whereClause = !empty($where) ? "{$where}" : '';
        $whereClause = str_replace('item_name', 'i.item_name', $whereClause);
        $whereClause = str_replace('account_name', 'i.account_name', $whereClause);

        $query = "
            SELECT 
                i.account_name,
                i.item_name,
                i.item_code,
                i.standard,
                i.unit,
                i.qty,
                i.delivery_remain_qty,
                IFNULL(m.stock_qty, 0) AS stock_qty,
                i.order_date,
                i.shipment_date,
                i.shipment_status
            FROM mes_order_items i
            LEFT JOIN mes_items m ON m.uid = i.item_uid
            {$whereClause}
            ORDER BY i.{$orderby} {$asc}
        ";
        $this->query($query);
        $results = $this->fetchAll();

        $filename = 'mes_order_items_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF";
        $output = fopen('php://output', 'w');
        fputcsv($output, ['거래처', '수주품목', '품번', '규격', '단위', '수주수량', '잔여납품수량', '재고수량', '수주일', '납기일', '출하상태']);

        foreach ($results as $row) {
            fputcsv($output, [
                $row['account_name'],
                $row['item_name'],
                $row['item_code'],
                $row['standard'],
                $row['unit'],
                $row['qty'],
                $row['delivery_remain_qty'],
                $row['stock_qty'],
                $row['order_date'],
                $row['shipment_date'],
                $row['shipment_status'],
            ]);
        }

        fclose($output);
        exit;
    }

    // 수주 품목 하나 가져오기
    public function getOrderItem() {
        $uid = $this->param['uid'];
        $query = "select * from mes_order_items where uid={$uid}";
        $data = $this->queryFetch($query);

        if ($data) {
            $query = "select * from mes_items where uid={$data['item_uid']}";
            $this->query($query);
            $item = $this->fetch();
            $stock_qty = $item['stock_qty'];

            $this->response = [
                'uid' => $data['uid'],  
                'account_uid' => $data['account_uid'],
                'account_name' => $data['account_name'],
                'order_date' => $data['order_date'],
                'shipment_date' => $data['shipment_date'],
                'item_uid' => $data['item_uid'],
                'item_name' => $data['item_name'],
                'item_code' => $data['item_code'],
                'standard' => $data['standard'],
                'unit' => $data['unit'],
                'qty' => $data['qty'],
                'delivery_remain_qty' => $data['delivery_remain_qty'],
                'product_status' => $data['product_status'],
                'shipment_status' => $data['shipment_status'],
                'stock_qty' => $stock_qty
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    //===============================================================================================================================//
    // 납품 관리
    //===============================================================================================================================//    
    // 출하지시 등록
    public function registerDeliveryOrder() {
        $uid = $this->param['uid'];
        $delivery_date = $this->param['delivery_date'];
        $delivery_qty = $this->removeComma($this->param['delivery_qty']);

        $query = "select * from mes_order_items where uid={$uid}";
        $this->query($query);
        $order = $this->fetch();

        if($order['delivery_remain_qty'] < $delivery_qty || $delivery_qty == 0) {
            $this->response = [
                'result' => 'error',
                'message' => '납품 수량이 잔여 납품 수량보다 큽니다'
            ];
        } else {
            // 납품 테이블에 등록한다
            $data = array(
                'table' => 'mes_delivery',                
                'order_uid' => $order['fid'],
                'account_uid' => $order['account_uid'],
                'account_name' => $order['account_name'],
                'item_uid' => $order['item_uid'],
                'item_code' => $order['item_code'],
                'item_name' => $order['item_name'],
                'standard' => $order['standard'],
                'unit' => $order['unit'],
                'delivery_date' => $delivery_date,
                'delivery_qty' => $delivery_qty,
                'remain_qty' => $delivery_qty,
                'status' => '출하지시'
            );
            $result = $this->insert($data);

            if($order['delivery_remain_qty'] == $delivery_qty) {
                $status = '출하지시';
            } else {
                $status = '부분출하지시';
            }

            $data = array(
                'table' => 'mes_order_items',
                'where' => 'uid=' . $order['uid'],
                'shipment_status' => $status
            );
            $result = $this->update($data);

            $query = "select * from mes_order_items where fid={$order['fid']} and shipment_status!='납품완료'";
            $this->query($query);
            if($this->getRows() <= 0) {
                $data = array(
                    'table' => 'mes_orders',
                    'where' => 'uid=' . $order['fid'],
                    'shipment_status' => $status
                );
                $this->update($data);
            }

            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 등록이 되었습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // 납품 등록
    public function registerDelivery() {
        $uid = $this->param['uid'];
        $delivery_date = $this->param['delivery_date'];
        $delivery_qty = $this->removeComma($this->param['delivery_qty']);

        $query = "select * from mes_delivery where uid={$uid}";
        $this->query($query);
        $delivery = $this->fetch();

        // 납품 테이블에 등록한다
        $data = array(
            'table' => 'mes_delivery_report',                
            'fid' => $uid,
            'order_uid' => $delivery['order_uid'],
            'account_uid' => $delivery['account_uid'],
            'account_name' => $delivery['account_name'],
            'item_uid' => $delivery['item_uid'],
            'item_code' => $delivery['item_code'],
            'item_name' => $delivery['item_name'],
            'standard' => $delivery['standard'],
            'unit' => $delivery['unit'],
            'delivery_date' => $delivery_date,
            'delivery_qty' => $delivery_qty
        );
        $result1 = $this->insert($data);

        if(!$result1) {
            $this->response = [
                'result' => 'error',
                'message' => '등록에 실패하였습니다'
            ];
            echo json_encode($this->response);
            exit;
        }

        // mes_delivery 테이블에서 remain_qty 값을 변경한다
        $remain_qty1 = $delivery['remain_qty'] - $delivery_qty;
        if($remain_qty1 == 0 || $remain_qty1 < 0) {
            $status = '출하완료';
            if($remain_qty1 < 0) $remain_qty1 = 0;
        } else {
            $status = '부분출하중';
        }

        $data = array(
            'table' => 'mes_delivery',
            'where' => 'uid=' . $uid,
            'remain_qty' => $remain_qty1,
            'status' => $status
        );
        $result2 = $this->update($data);

        if(!$result2) {
            $this->response = [
                'result' => 'error',
                'message' => '납품 등록에 실패하였습니다'
            ];
            echo json_encode($this->response);
            exit;
        }


        // 수주 품목의 잔여출하수량 변경
        $query = "select * from mes_order_items where fid={$delivery['order_uid']}";
        $this->query($query);
        $order_item = $this->fetch();

        $remain_qty3 = $order_item['delivery_remain_qty'] - $delivery_qty;
        if($remain_qty3 == 0 || $remain_qty3 < 0) {
            $status = '출하완료';
            if($remain_qty3 < 0) $remain_qty3 = 0;
        } else {
            $status = '부분출하중';
        }

        $data = array(
            'table' => 'mes_order_items',
            'where' => 'uid=' . $order_item['uid'],
            'delivery_remain_qty' => $remain_qty3,
            'shipment_status' => $status
        );
        $result3 = $this->update($data);

        if(!$result3) {
            $this->response = [
                'result' => 'error',
                'message' => '수주 품목의 잔여출하수량 변경에 실패하였습니다'
            ];
            echo json_encode($this->response);
            exit;
        }

        // 수주 테이블의 잔여 수량을 변경한다
        $query = "select * from mes_order_items where fid={$order_item['fid']} and shipment_status!='출하완료'";
        $this->query($query);
        if($this->getRows() <= 0) {
            $status = '출하완료';
        } else {
            $status = '부분출하중';
        }

        $data = array(
            'table' => 'mes_orders',
            'where' => 'uid=' . $order_item['fid'],
            'status' => $status
        );
        $result4 = $this->update($data);

        if(!$result4) {
            $this->response = [
                'result' => 'error',
                'message' => '수주 테이블의 잔여 수량을 변경에 실패하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 등록이 되었습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function getDeliveryOrderItem() {
        $uid = $this->param['uid'];
        $query = "select * from mes_delivery where uid={$uid}";
        $data = $this->queryFetch($query);

        $query = "select * from mes_items where uid={$data['item_uid']}";
        $this->query($query);
        $item = $this->fetch();
        $stock_qty = $item['stock_qty'];
        $price = $item['price'];
        
        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'order_uid' => $data['order_uid'],
                'account_uid' => $data['account_uid'],
                'account_name' => $data['account_name'],
                'item_uid' => $data['item_uid'],
                'item_code' => $data['item_code'],
                'item_name' => $data['item_name'],
                'standard' => $data['standard'],
                'unit' => $data['unit'],
                'delivery_date' => $data['delivery_date'],
                'delivery_qty' => $data['delivery_qty'],
                'remain_qty' => $data['remain_qty'],
                'status' => $data['status'],
                'stock_qty' => $stock_qty,
                'price' => $price
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    public function getDeliveryList() {        
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_delivery 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";            
                
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'order_uid' => $data['order_uid'],
                    'account_uid' => $data['account_uid'],
                    'account_name' => $data['account_name'],
                    'item_uid' => $data['item_uid'],
                    'item_code' => $data['item_code'],
                    'item_name' => $data['item_name'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'delivery_date' => $data['delivery_date'],
                    'delivery_qty' => $data['delivery_qty'],
                    'remain_qty' => $data['remain_qty'],
                    'status' => $data['status']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    // 출하내역 가져오기
    public function getDeliveryItemList() {
        $uid = $this->param['uid'];
        $query = "select * from mes_delivery_report where fid={$uid}";
        $this->query($query);
        $results = $this->fetchAll();

        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'order_uid' => $data['order_uid'],
                    'account_uid' => $data['account_uid'],
                    'account_name' => $data['account_name'],
                    'item_uid' => $data['item_uid'],
                    'item_code' => $data['item_code'],
                    'item_name' => $data['item_name'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'delivery_date' => $data['delivery_date'],
                    'delivery_qty' => $data['delivery_qty']
                ];
            }, $results)
        ];

        echo json_encode($this->response);
    }

    public function getDeliveryReportList() {        
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_delivery_report 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";            
                
        $this->query($query);
        $results = $this->fetchAll();


        $this->response = [
            'data' => array_map(function($data) {
                $order = [];
                $order_uid = isset($data['order_uid']) ? (int)$data['order_uid'] : 0;
                if ($order_uid > 0) {
                    $query = "select * from mes_orders where uid={$order_uid}";
                    $this->query($query);
                    $order = $this->fetch();
                }

                // 납기 소요일 계산 ($order_date와 $result['delivery_date']의 차이)
                $delivery_days = null;
                if (!empty($order['order_date']) && !empty($data['delivery_date'])) {
                    $start = new DateTime($order['order_date']);
                    $end = new DateTime($data['delivery_date']);
                    $delivery_days = $start->diff($end)->days;
                }

                return [
                    'uid' => $data['uid'],
                    'order_uid' => $data['order_uid'],
                    'account_uid' => $data['account_uid'],
                    'account_name' => $data['account_name'],
                    'item_uid' => $data['item_uid'],
                    'item_code' => $data['item_code'],
                    'item_name' => $data['item_name'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'delivery_date' => $data['delivery_date'],
                    'delivery_qty' => $data['delivery_qty'],
                    'order_date' => $order['order_date'] ?? null,
                    'shipment_date' => $order['shipment_date'] ?? null,
                    'delivery_days' => $delivery_days
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    // 출하 품목 내역 엑셀 다운로드
    public function getDeliveryReportListExcel() {
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid';
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC';

        $whereClause = !empty($where) ? "{$where}" : '';
        $whereClause = str_replace('item_name', 'd.item_name', $whereClause);
        $whereClause = str_replace('account_name', 'd.account_name', $whereClause);
        $whereClause = str_replace('item_code', 'd.item_code', $whereClause);

        $query = "
            SELECT 
                d.account_name,
                d.item_name,
                d.item_code,
                d.standard,
                d.delivery_qty,
                d.delivery_date
            FROM mes_delivery_report d
            {$whereClause}
            ORDER BY d.{$orderby} {$asc}
        ";
        $this->query($query);
        $results = $this->fetchAll();

        $filename = 'mes_delivery_report_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF";
        $output = fopen('php://output', 'w');
        fputcsv($output, ['거래처', '품목', '품번', '규격', '출하수량', '출하일자']);

        foreach ($results as $row) {
            fputcsv($output, [
                $row['account_name'],
                $row['item_name'],
                $row['item_code'],
                $row['standard'],
                $row['delivery_qty'],
                $row['delivery_date'],
            ]);
        }

        fclose($output);
        exit;
    }

    public function getDeliveryReport() {
        $uid = $this->param['uid'];
        $query = "select * from mes_delivery_report where uid={$uid}";
        $this->query($query);
        $result = $this->fetch();

        $query = "select * from mes_items where uid={$result['item_uid']}";
        $this->query($query);
        $item = $this->fetch();
        $result['stock_qty'] = $item['stock_qty'];

        $query = "select * from mes_orders where uid={$result['order_uid']}";        
        $this->query($query);
        $order = $this->fetch();
        $order_date = $order['order_date'];

        // 납기 소요일 계산 ($order_date와 $result['delivery_date']의 차이)
        $delivery_days = null;
        if (!empty($order_date) && !empty($result['delivery_date'])) {
            $start = new DateTime($order_date);
            $end = new DateTime($result['delivery_date']);
            $delivery_days = $start->diff($end)->days;
        }
        $result['order_date'] = $order_date;
        $result['delivery_days'] = $delivery_days;

        $this->response = [
            'data' => $result
        ];
    
        echo json_encode($this->response);
    }
    
    public function modifyDeliveryReport() {
        $uid = $this->param['uid'];
        $data = array(
            'table' => 'mes_delivery_report',
            'where' => 'uid=' . $uid,
            'delivery_date' => $this->param['delivery_date'],
            'delivery_qty' => $this->param['delivery_qty']
        );
        $result = $this->update($data);
        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 수정되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '출하 내역 데이터를 수정하는 중 에러가 발생하였습니다'
            ];
        }
        echo json_encode($this->response);
    }

    public function getAllDeliveryReportList() {
        $uid = $this->param['uid'];
        $query = "select * from mes_delivery_report where fid={$uid}";
        $this->query($query);
        $results = $this->fetchAll();
        $this->response = [
            'data' => $results
        ];
        echo json_encode($this->response);
    }

    public function deleteDeliveryReport() {
        $uid = $this->param['uid'];
        $query = "delete from mes_delivery_report where uid={$uid}";
        $result = $this->query($query);
        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 삭제되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '출하 내역 데이터를 삭제하는 중 에러가 발생하였습니다'
            ];
        }
        echo json_encode($this->response);
    }
    //===============================================================================================================================//
    // 자재 입,출고
    //===============================================================================================================================//    
    // 수입검사 등록
    public function registerIncomingInspection() {
        $query = "select fid from mes_purchase_item where uid={$this->param['uid']}";
        $this->query($query);
        $purchase_item = $this->fetch();

        $query = "select * from mes_items where uid={$this->param['item_uid']}";
        $this->query($query);
        $item = $this->fetch();
        $in_qty = $this->removeComma($this->param['in_qty']); // in_qty
        $inspector = $this->param['inspector'];
        $query = "select * from mes_employee where uid={$this->param['inspector']}";
        $this->query($query);
        $employee = $this->fetch();
        $currentDate = date('Y-m-d');

        $data = array(
            'table' => 'mes_incoming_inspection',
            'purchase_uid' => $purchase_item['fid'],
            'purchase_item_uid' => $this->param['uid'],
            'item_uid' => $this->param['item_uid'],
            'item_name' => $this->param['item_name'],
            'item_code' => $this->param['item_code'],
            'standard' => $this->param['standard'],
            'unit' => $this->param['unit'],
            'in_qty' => $in_qty,
            'inspection_date' => $this->param['inspection_date'],
            'appearance_check' => $this->param['appearance_check'],
            'function_check' => $this->param['function_check'],
            'inspector_uid' => $this->param['inspector'],
            'inspector_name' => $employee['name'],
            'inspection_result' => $this->param['inspection_result'],
            'remark' => $this->param['remark'],
            'created_dt' => $currentDate,            
        );
        $result = $this->insert($data);
        
        if($result) { // 품질검사가 끝났다면
            if($this->param['inspection_result'] == 'OK') {
                $data = array(
                    'table' => 'mes_purchase_item',
                    'where' => 'uid=' . $this->param['uid'],
                    'status' => '수입검사완료'
                );
                $this->update($data);
            } else {
                $data = array(
                    'table' => 'mes_purchase_item',
                    'where' => 'uid=' . $this->param['uid'],
                    'status' => '수입검사불합격'
                );
                $this->update($data);
            }

            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 수입검사 결과가 저장되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '수입검사 결과 저장 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function getIncomingInspectionList() {        
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_incoming_inspection 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";            
                
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'purchase_uid' => $data['purchase_uid'],
                    'purchase_item_uid' => $data['purchase_item_uid'],
                    'item_uid' => $data['item_uid'],
                    'item_name' => $data['item_name'],
                    'item_code' => $data['item_code'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'in_qty' => $data['in_qty'],
                    'inspection_date' => $data['inspection_date'],
                    'appearance_check' => $data['appearance_check'],
                    'function_check' => $data['function_check'],
                    'inspector_uid' => $data['inspector_uid'],
                    'inspector_name' => $data['inspector_name'],
                    'inspection_result' => $data['inspection_result'],
                    'remark' => $data['remark'],
                    'created_dt' => $data['created_dt']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    // 자재 출고
	public function registerItemOut() {
        $uid = $this->param['uid'];
        $classification = '출고';        
        $qty = $this->removeComma($this->param['qty']);
        $item_uid = $this->param['item'];

        $query = "select * from mes_items where uid={$itemUid}";
        $this->query($query);
        $item = $this->fetch();

        if(empty($uid)) {
            $data = array(
                'table' => 'mes_items_inout',
                'classification' => $classification,
                'item_uid' => $item_uid,
                'item_name' => $item['item_name'],
                'item_code' => $item['item_code'],
                'standard' => $item['standard'],
                'unit' => $item['unit'],
                'qty' => $qty,
                'register_date' => $this->param['outDate']
            );
            $result = $this->insert($data);

            $stock_qty = $item['stock_qty'] - $qty;
        } else {
            $query = "select * from mes_items_inout where uid={$uid}";
            $this->query($query);
            $inout = $this->fetch();
            $item_qty = $item['stock_qty'] + $inout['qty'];

            $data = array(
                'table' => 'mes_items_inout',
                'where' => 'uid=' . $this->param['uid'],                
                'classification' => $classification,
                'item_uid' => $itemUid,
                'item_name' => $item['item_name'],
                'item_code' => $item['item_code'],
                'standard' => $item['standard'],
                'unit' => $item['unit'],
                'qty' => $qty
            );  
            $result = $this->update($data) ;

            $stock_qty = $item_qty - $qty;
        }

        if($result) {
            // 품목의 재고수량을 변경한다
            $data = array(
                'table' => 'mes_items',
                'where' => 'uid=' . $itemUid,                
                'stock_qty' => $stock_qty
            );  
            $result = $this->update($data) ;

            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 등록이 되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '입고 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
	}

    public function getItemsInOutList() {        
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_stock_log 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [            
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'classification' => $data['classification'],
                    'item_uid' => $data['item_uid'],
                    'item_code' => $data['item_code'],
                    'item_name' => $data['item_name'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'in_qty' => $data['in_qty'],
                    'out_qty' => $data['out_qty'],
                    'register_date' => $data['register_date']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    // 입출고내역 하나 가져오기
    public function getItemsInOut() {
        $uid = $this->param['uid'];
        $query = "select * from mes_items_inout where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'classification' => $data['classification'],
                'item_uid' => $data['item_uid'],
                'item_name' => $data['item_name'],
                'item_code' => $data['item_code'],
                'standard' => $data['standard'],
                'unit' => $data['unit'],
                'qty' => $data['qty'],
                'register_date' => $data['register_date']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    // 입고내역 삭제
    public function deleteItemsIn() {
        $uid = $this->param['uid'];

        $query = "select * from mes_items_inout where uid={$uid}";
        $this->query($query);
        $inout = $this->fetch();
        
        $query = "select * from items where uid=" . $inout['item_uid'];
        $this->query($query);
        $item = $this->fetch();

        $item_qty = $item['stock_qty'] - $inout['qty'];

        if($uid) {
            $query = "delete from mes_items_inout where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '입고내역 삭제 중 에러가 발생하였습니다'
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

    //===============================================================================================================================//
    // 설비 관리
    //===============================================================================================================================//
    public function registerMachine() {
        $uid = $this->param['uid'];
        $machine_id = $this->param['machine_id'];
        $machine_name = $this->param['machine_name'];
        $model_no = $this->param['model_no'];
        $production_line = $this->param['production_line'];
        $introduction_date = $this->param['introduction_date'];
        $iot_sensor_id = $this->param['iot_sensor_id'];
        $purchase_company = $this->param['purchase_company'];
        $contact_person = $this->param['contact_person'];
        $attach = !empty($this->param['attach']) ? $this->param['attach'] : '';
        $oldImg = !empty($this->param['oldImg']) ? $this->param['oldImg'] : '';
        $current_time = time();

		if(!empty($attach)) {
			// 파일 업로드
			ini_set("memory_limit", -1);
			$upfile_dir = "./attach/";
			set_time_limit(0);

			$upfile_name = $_FILES['attach']['name']; // 파일이름
			$upfile_type = $_FILES['attach']['type']; // 확장자
			$upfile_size = $_FILES['attach']['size']; // 파일크기
			$upfile_tmp  = $_FILES['attach']['tmp_name']; // 임시 디렉토리에 저장된 파일명

			//확장자 확인
			if(preg_match("/(\.(gif|GIF|jpg|JPG|jpeg|JPEG|png|PNG|jfif|JFIF))$/i",$upfile_name)) { //|xlsx|XLSX
			} else {
				echo ("<script>window.alert('업로드를 할수 없는 파일 입니다.\\n\\r확장자가 [GIF, JPG, PNG, JPEG]인 경우만 업로드가 가능합니다.'); history.go(-1) </script>");
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
			
			$filepath = "{$upfile_name}";
		} else {
			$filepath = $this->param['oldImg'];
		}

		if(empty($this->param['uid'])) {
			$data = array(
				'table' => 'mes_machine',
				'machine_id' => $machine_id,
				'machine_name' => $machine_name,
				'model_no' => $model_no,
				'production_line' => $production_line,
				'introduction_date' => $introduction_date,
				'iot_sensor_id' => $iot_sensor_id,
				'purchase_company' => $purchase_company,
				'contact_person' => $contact_person,
				'attach' => $filepath,
				'created_at' => $current_time,
                'updated_at' => $current_time
			);            
			$result = $this->insert($data);			
		} else {
			$data = array(
				'table' => 'mes_machine',
				'where' => 'uid='.$this->param['uid'],
				'machine_id' => $machine_id,
				'machine_name' => $machine_name,
				'model_no' => $model_no,
				'production_line' => $production_line,
				'introduction_date' => $introduction_date,
				'iot_sensor_id' => $iot_sensor_id,
				'purchase_company' => $purchase_company,
				'contact_person' => $contact_person,
				'attach' => $filepath,
				'updated_at' => $current_time
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
                'message' => '설비 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
	}
	
    // 설비 리스트
    public function getMachineList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 총 게시물 개수 가져오기
        $countQuery = "SELECT COUNT(*) as totalCount FROM mes_machine {$whereClause}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_machine 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'maker' => $data['maker'],
                    'makerContact' => $data['makerContact'],
                    'purchaseYear' => $data['purchaseYear'],
                    'attach' => $data['attach'],
                    'mainOfficer' => $data['mainOfficer'],
                    'mainOfficerName' => $data['mainOfficerName'],
                    'subOfficer' => $data['subOfficer'],
                    'subOfficerName' => $data['subOfficerName'],
                    'sensor' => $data['sensor'],
                    'ratedVoltage' => $data['ratedVoltage'],
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    // 설비 하나 가져오기
    public function getMachine() {
        $uid = $this->param['uid'];
    
        // 쿼리 실행
        $query = "SELECT * FROM mes_machine WHERE uid = {$uid}";
        $this->query($query);
        $result = $this->fetch(); // 하나의 행만 가져옴
    
        // 응답 데이터 구성
        if ($result) {
            $this->response = [
                'uid' => $result['uid'],
                'name' => $result['name'],
                'code' => $result['code'],
                'maker' => $result['maker'],
                'makerContact' => $result['makerContact'],
                'purchaseYear' => $result['purchaseYear'],
                'attach' => $result['attach'],
                'mainOfficer' => $result['mainOfficer'],
                'mainOfficerName' => $result['mainOfficerName'],
                'subOfficer' => $result['subOfficer'],
                'subOfficerName' => $result['subOfficerName'],
                'sensor' => $result['sensor'],
                'ratedVoltage' => $result['ratedVoltage'],
            ];
        } else {
            // 결과가 없는 경우 처리
            $this->response = ['error' => 'No machine found'];
        }
    
        // JSON 응답 반환
        echo json_encode($this->response);
    }    

    // 스팩
    public function getSpec() {
        $uid = $this->param['uid'];
    
        // 쿼리 실행
        $query = "SELECT * FROM mes_machine_spec WHERE fid = {$uid}";
        $this->query($query);
        $results = $this->fetchAll();
    
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'fid' => $data['fid'],
                'name' => $data['name'],
                'value' => $data['value']
            ];
        }, $results);
    
        // JSON 응답 반환
        echo json_encode($this->response);
    }
    
    // 부품
    public function getComponent() {
        $uid = $this->param['uid'];
    
        // 쿼리 실행
        $query = "SELECT * FROM mes_machine_component WHERE fid = {$uid}";
        $this->query($query);
        $results = $this->fetchAll();
    
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'fid' => $data['fid'],
                'name' => $data['name'],
                'standard' => $data['standard'],
                'purchaseCompany' => $data['purchaseCompany'],
                'companyContact' => $data['companyContact'],
                'qty' => $data['qty']
            ];
        }, $results);
    
        // JSON 응답 반환
        echo json_encode($this->response);
    }

    // 점검항목 
    public function getInspect() {
        $uid = $this->param['uid'];
    
        // 쿼리 실행
        $query = "SELECT * FROM mes_machine_inspect WHERE fid = {$uid}";
        $this->query($query);
        $results = $this->fetchAll();
    
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'part' => $data['part'],
                'name' => $data['name'],
                'method' => $data['method'],
                'inspectDate' => $data['inspectDate'],
                'comment' => $data['comment']
            ];
        }, $results);
    
        // JSON 응답 반환
        echo json_encode($this->response);
    }

	// 설비 삭제
	public function deleteMachine() {
		$sql = "delete from mes_machine where uid=".$this->param['uid'];
		$result1 = $this->query($sql);

		$sql = "delete from mes_machine_spec where fid=".$this->param['uid'];
		$result2 = $this->query($sql);

		$sql = "delete from mes_machine_component where fid=".$this->param['uid'];
		$result3 = $this->query($sql);

		$sql = "delete from mes_machine_inspect where fid=".$this->param['uid'];
		$result4 = $this->query($sql);

        if($result1 && $result2 && $result3 && $result4) {
            $this->response = [
                'result' => 'success',
                'message' => '삭제하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '설비 삭제 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
		
	}

	// 설비수리이력 등록
	public function registMachineRepair() {
		if(empty($this->param['machineRepairUid'])) {
			$data = array(
				'table' => 'machineRepair',
				'machineUid' => $this->chkNumber($this->param['machineUid']),
				'machineName' => $this->param['machineName'],
				'machineCode' => $this->param['machineCode'],
				'reason' => $this->param['reason'],
				'repairCompany' => $this->param['repairCompany'],
				'repairCompanyContact' => $this->param['repairCompanyContact'],
				'repairDate' => $this->chkDate($this->param['repairDate']),
				'repairCost' => $this->chkNumber($this->param['repairCost']),
				'mainOfficerUid' => $this->chkNumber($this->param['mainOfficerUid']),
				'mainOfficerName' => $this->param['mainOfficerName'],
				'subOfficerUid' => $this->chkNumber($this->param['subOfficerUid']),
				'subOfficerName' => $this->param['subOfficerName']
			);
			$this->insert($data);
		} else {
			$data = array(
				'table' => 'machineRepair',
				'where' => 'uid='.$this->param['machineRepairUid'],
				'machineUid' => $this->chkNumber($this->param['machineUid']),
				'machineName' => $this->param['machineName'],
				'machineCode' => $this->param['machineCode'],
				'reason' => $this->param['reason'],
				'repairCompany' => $this->param['repairCompany'],
				'repairCompanyContact' => $this->param['repairCompanyContact'],
				'repairDate' => $this->chkDate($this->param['repairDate']),
				'repairCost' => $this->chkNumber($this->param['repairCost']),
				'mainOfficerUid' => $this->chkNumber($this->param['mainOfficerUid']),
				'mainOfficerName' => $this->param['mainOfficerName'],
				'subOfficerUid' => $this->chkNumber($this->param['subOfficerUid']),
				'subOfficerName' => $this->param['subOfficerName']
			);
			$this->update($data);
		}
		echo "success";
	}

	// 설비점검 결과 등록	
    public function registerMachineInspect() {
        $fid = $this->param['uid'];
        $inspectPart = $this->param['inspectPart'];
        $inspectName = $this->param['inspectName'];
        $inspectMethod = $this->param['inspectMethod'];
        $inspectComment = $this->param['inspectComment'];
        $inspectResult = $this->param['inspectResult'];

        $machine = $this->getData("mes_machine", $this->param['uid']);
        $employee = $this->getData("mes_employee", $this->param['employee']);

        $allSuccess = true; // 성공 여부를 추적하는 변수

        foreach($inspectPart as $key => $val) {
            if($inspectResult != '0') {
                $data = array(
                    "table" => "mes_inspect_report",
                    "fid" => $this->param['uid'],
                    "name" => $machine['name'],
                    "code" => $machine['code'],
                    "inspectPart" => $val,
                    "inspectName" => $inspectName[$key],
                    "inspectMethod" => $inspectMethod[$key],
                    "inspectComment" => $inspectComment[$key],
                    "inspectResult" => $inspectResult[$key],
                    "employee" => $employee['uid'],
                    "employeeName" => $employee['name'],
                    "inspectDate" => $this->param['inspectDate']
                );

                // insert의 결과를 확인
                $insertResult = $this->insert($data);
                
                // 만약 insert가 실패하면 allSuccess를 false로 변경
                if (!$insertResult) {
                    $allSuccess = false;
                    break; // 하나라도 실패하면 루프 종료
                }
            }
        }

        if ($allSuccess) {
            // 모든 insert가 성공한 경우
            $this->response = [
                'result' => 'success',
                'message' => '등록하였습니다'
            ];
        } else {
            // 하나라도 실패한 경우
            $this->response = [
                'result' => 'error',
                'message' => '등록에 실패하였습니다. 다시 시도해주세요.'
            ];
        }

        echo json_encode($this->response);
    }

    public function getMachineInspectList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 총 게시물 개수 가져오기
        $countQuery = "SELECT COUNT(*) as totalCount FROM mes_inspect_report {$whereClause}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_inspect_report 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'fid' => $data['fid'],
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'inspectPart' => $data['inspectPart'],
                    'inspectName' => $data['inspectName'],
                    'inspectMethod' => $data['inspectMethod'],
                    'inspectComment' => $data['inspectComment'],
                    'inspectResult' => $data['inspectResult'],
                    'employee' => $data['employee'],
                    'employeeName' => $data['employeeName'],
                    'inspectDate' => $data['inspectDate']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    public function deleteMachineInspectReport() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from mes_inspect_report where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '점검내역 삭제 중 에러가 발생하였습니다'
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


    // 차트용 machine 가져오기
    public function getMachines() {
        // 쿼리 실행
        $query = "SELECT * FROM mes_machine";
        $this->query($query);
        $results = $this->fetchAll();
    
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],       // 설비 UID
                'name' => $data['name'],     // 설비 이름
                'code' => $data['code'],     // 설비 코드
            ];
        }, $results);
    
        // JSON 응답 반환
        header('Content-Type: application/json');
        echo json_encode(['machines' => $this->response]);
    }

    //===============================================================================================================================//
    // 공정
    //===============================================================================================================================//
    
    public function getProcess2() {
        // 쿼리 실행
        $query = "SELECT * FROM mes_process";
        $this->query($query);
        $results = $this->fetchAll();
    
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        }, $results);
    
        // JSON 응답 반환
        echo json_encode($this->response);
    }

    //===============================================================================================================================//
    // 작업지시
    //===============================================================================================================================//
    // 계획 생산지시 등록
    public function registerPlanWorkOrder() {
        $currentDate = date('Y-m-d');
        $uid = $this->param['plan_uid'];
        $item_uid = $this->param['plan_item'];
            
        $query = "select * from mes_items where uid=" . $item_uid;            
        $this->query($query);
        $item = $this->fetch();

        $work_start_time = 0;
        $work_end_time = 0;

        if(empty($uid)) {
            $data = array(
                'table' => 'mes_work_order',                
                'classification' => '계획생산',
                'order_uid' => 0,
                'account_uid' => 0,
                'account_name' => '',
                'item_uid' => $item_uid,
                'item_name' => $item['item_name'],
                'item_code' => $item['item_code'],
                'standard' => $item['standard'],
                'unit' => $item['unit'],
                'order_date' => $this->param['plan_order_date'],
                'order_qty' => $this->param['plan_order_qty'],
                'work_qty' => 0,
                'pass_qty' => 0,
                'fail_qty' => 0,
                'quality_qty' => 0,
                'remain_qty' => $this->param['plan_order_qty'],                
                'work_start_time' => $work_start_time,
                'work_end_time' => $work_end_time,
                'status' => '생산대기',
                'register_date' => $currentDate                
            );

            $result = $this->insert($data);
        } else {
            // 작업이 대기 일경우만 수정이 가능하도록 하자
            $query = "select status from mes_work_order where uid=" . $uid;
            $this->query($query);
            $workorder = $this->fetch();

            if($workorder['status'] != '생산대기') {
                $this->reponse = [
                    'result' => 'error',
                    'message' => '작업이 진행중이거나 종료된 것은 수정할 수 없습니다'
                ];

                echo json_encode($this->response);
                exit;
            }
            
            $data = array(
                'table' => 'mes_work_order',
                'where' => 'uid=' . $uid,
                'order_date' => $this->param['order_date'],
                'order_qty' => $this->param['order_qty'],
                'remain_qty' => $this->param['order_qty']
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
                'message' => '생산지시 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // 작업지시 등록
    public function registerWorkOrder() {
        $currentDate = date('Y-m-d');
        $order_items_uid = !empty($this->param['order_items_uid']) ? $this->param['order_items_uid'] : null;
        $work_order_uid = !empty($this->param['work_order_uid']) ? $this->param['work_order_uid'] : null;
        $order_date = !empty($this->param['order_date']) ? $this->param['order_date'] : null;
        $order_qty = !empty($this->param['order_qty']) ? $this->removeComma($this->param['order_qty']) : null;

        $query = "select * from mes_order_items where uid=" . $order_items_uid;
        $this->query($query);
        $order_items = $this->fetch();

        $work_start_time = 0;
        $work_end_time = 0;
        
        $data = array(
            'table' => 'mes_work_order',
            'classification' => '수주생산',
            'order_uid' => $order_items['fid'],
            'order_items_uid' => $order_items['uid'],
            'account_uid' => $order_items['account_uid'],
            'account_name' => $order_items['account_name'],
            'item_uid' => $order_items['item_uid'],
            'item_code' => $order_items['item_code'],
            'item_name' => $order_items['item_name'],
            'standard' => $order_items['standard'],
            'unit' => $order_items['unit'],
            'order_date' => $order_date,
            'order_qty' => $order_qty,
            'work_qty' => 0,
            'pass_qty' => 0,
            'fail_qty' => 0,
            'quality_qty' => 0,
            'remain_qty' => $order_qty,                
            'work_start_time' => $work_start_time,
            'work_end_time' => $work_end_time,
            'status' => '생산대기',
            'register_date' => $currentDate                
        );

        $result = $this->insert($data);

        if($result) {
            $data = array(
                'table' => 'mes_order_items',
                'where' => 'uid=' . $order_items_uid,
                'product_status' => '생산지시'
            );            
            $this->update($data);

            $this->response = [
                'result' => 'success',
                'message' => '등록하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '생산지시 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function startWork() {
        $uid = $this->param['uid'];
        $work_start_time = time();

        $data = array(
            'table' => 'mes_work_order',
            'where' => 'uid=' . $uid,
            'work_start_time' => $work_start_time,
            'status' => '작업중'
        );
        $result = $this->update($data);

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '작업시작하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '작업시작 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function endWork() {
        $uid = $this->param['uid'];
        $work_end_time = time();

        $data = array(
            'table' => 'mes_work_order',
            'where' => 'uid=' . $uid,
            'work_end_time' => $work_end_time,
            'status' => '작업완료'
        );
        $result = $this->update($data);

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '작업종료하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '작업종료 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function modifyWorkOrder() {
        $currentDate = date('Y-m-d');        
        $work_order_uid = !empty($this->param['modify_work_order_uid']) ? $this->param['modify_work_order_uid'] : null;
        $order_date = !empty($this->param['modify_order_date']) ? $this->param['modify_order_date'] : null;
        $order_qty = !empty($this->param['modify_order_qty']) ? $this->removeComma($this->param['modify_order_qty']) : null;

        $query = "select * from mes_work_order where uid=" . $work_order_uid;
        $this->query($query);
        $work_order = $this->fetch();
        
        $data = array(
            'table' => 'mes_work_order',
            'where' => 'uid=' . $work_order_uid,
            'order_date' => $order_date,
            'order_qty' => $order_qty,
            'work_qty' => 0,
            'pass_qty' => 0,
            'fail_qty' => 0,
            'quality_qty' => 0,
            'remain_qty' => $order_qty,
            'product_status' => '생산대기',
            'register_date' => $currentDate                
        );

        $result = $this->insert($data);

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '등록하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '생산지시 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function getWorkOrderList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_work_order 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'classification' => $data['classification'],
                    'order_uid' => $data['order_uid'],
                    'account_uid' => $data['account_uid'],
                    'account_name' => $data['account_name'],
                    'item_uid' => $data['item_uid'],
                    'item_name' => $data['item_name'],
                    'item_code' => $data['item_code'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'order_date' => $data['order_date'],
                    'order_qty' => $data['order_qty'],
                    'work_qty' => $data['work_qty'],
                    'pass_qty' => $data['pass_qty'],
                    'fail_qty' => $data['fail_qty'],
                    'quality_qty' => $data['quality_qty'],
                    'remain_qty' => $data['remain_qty'],
                    'work_start_time' => $data['work_start_time'],
                    'work_end_time' => $data['work_end_time'],
                    'status' => $data['status'],
                    'register_date' => $data['register_date']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    // 작업지시서 하나 가져오기
    public function getWorkOrder() {
        $uid = $this->param['uid'];        
        
        // 쿼리 실행
        $query = "SELECT a.*, b.qty,b.shipment_date FROM mes_work_order a left join mes_order_items b on a.order_items_uid = b.uid WHERE a.uid = {$uid}";
        
        $this->query($query);
        $result = $this->fetch(); // 하나의 행만 가져옴
        if($result) {
            $this->response = [                                              
                'account_name' => $result['account_name'],                
                'item_uid' => $result['item_uid'],
                'item_name' => $result['item_name'],
                'item_code' => $result['item_code'],
                'standard' => $result['standard'],
                'unit' => $result['unit'],
                'qty' => $result['qty'],
                'order_date' => $result['order_date'],
                'order_qty' => $result['order_qty'],
                'work_qty' => $result['work_qty'],
                'pass_qty' => $result['pass_qty'],
                'fail_qty' => $result['fail_qty'],
                'quality_qty' => $result['quality_qty'],
                'remain_qty' => $result['remain_qty'],
                'work_start_time' => $result['work_start_time'],
                'work_end_time' => $result['work_end_time'],
                'status' => $result['status'],
                'register_date' => $result['register_date'],
                'shipment_date' => $result['shipment_date'],                
            ];
        } else {
            // 결과가 없는 경우 처리
            $this->response = ['error' => 'No machine found'];
        }

        // JSON 응답 반환
        echo json_encode($this->response);
    }

    // 모든 작업지시서 가져오기
    public function getAllWorkOrderList() {
        $query = "SELECT * FROM mes_work_order where status!='작업완료'";
        $this->query($query);
        $results = $this->fetchAll();

        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'classification' => $data['classification'],
                    'order_uid' => $data['order_uid'],
                    'account_uid' => $data['account_uid'],
                    'account_name' => $data['account_name'],
                    'item_uid' => $data['item_uid'],
                    'item_name' => $data['item_name'],
                    'item_code' => $data['item_code'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'order_date' => $data['order_date'],
                    'order_qty' => $data['order_qty'],
                    'work_qty' => $data['work_qty'],
                    'pass_qty' => $data['pass_qty'],
                    'fail_qty' => $data['fail_qty'],
                    'quality_qty' => $data['quality_qty'],
                    'remain_qty' => $data['remain_qty'],
                    'work_start_time' => $data['work_start_time'],
                    'work_end_time' => $data['work_end_time'],
                    'status' => $data['status'],
                    'register_date' => $data['register_date']
                ];
            }, $results)
        ];

        echo json_encode($this->response);
    }

    // 모든 작업지시서 가져오기
    public function getTodayWorkOrderList() {
        $today = (empty($this->param['today'])) ? date('Y-m-d') : $this->param['today'];
        $item_uid = !empty($this->param['item_uid']) ? (int)$this->param['item_uid'] : null;

        $where = "status!='작업완료' AND order_date='{$today}'";
        if ($item_uid) {
            $where .= " AND item_uid={$item_uid}";
        }

        // 총 카운트
        $countQuery = "SELECT COUNT(*) as cnt FROM mes_work_order WHERE {$where}";
        $this->query($countQuery);
        $countRes = $this->fetch();
        $total = (int)($countRes['cnt'] ?? 0);

        // 결과 가져오기 (기본 제한을 걸어 대용량 방지)
        $query = "SELECT * FROM mes_work_order WHERE {$where} ORDER BY order_date DESC LIMIT 1000";
        $this->query($query);
        $results = $this->fetchAll();

        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'classification' => $data['classification'],
                    'order_uid' => $data['order_uid'],
                    'account_uid' => $data['account_uid'],
                    'account_name' => $data['account_name'],
                    'item_uid' => $data['item_uid'],
                    'item_name' => $data['item_name'],
                    'item_code' => $data['item_code'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'order_date' => $data['order_date'],
                    'order_qty' => $data['order_qty'],
                    'work_qty' => $data['work_qty'],
                    'pass_qty' => $data['pass_qty'],
                    'fail_qty' => $data['fail_qty'],
                    'quality_qty' => $data['quality_qty'],
                    'remain_qty' => $data['remain_qty'],
                    'work_start_time' => $data['work_start_time'],
                    'work_end_time' => $data['work_end_time'],
                    'status' => $data['status'],
                    'register_date' => $data['register_date']
                ];
            }, $results),
            'count' => $total
        ];

        echo json_encode($this->response);
    }

    //===============================================================================================================================//
    // 작업일지 관리
    //===============================================================================================================================//
    // 작업지시서 완료 처리
    public function completeWorkOrder() {
        $uid = $this->param['uid'];
        $query = "select * from mes_work_order where uid={$uid}";
        $this->query($query);
        $work_order = $this->fetch();
        $product_qty = $work_order['order_qty'] - $work_order['work_qty'];

        $data = array(
            'table' => 'mes_work_order',
            'where' => 'uid=' . $uid,
            'work_qty' => $work_order['order_qty'],
            'quality_qty' => $work_order['order_qty'],
            'remain_qty' => 0,
            'status' => '작업완료'
        );

        $result = $this->update($data);
        
        if($result) {
            $this->registerDailyWorkReport($uid, $product_qty);
            $this->response = [
                'result' => 'success',
                'message' => '작업일지 등록되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '작업일지 등록에 실패하였습니다'
            ];
        }

        echo json_encode($this->response);
    }
    

    // 작업지시서 부분 완료 처리
    public function partialCompleteWorkOrder() {
        $uid = $this->param['uid'];
        $work_qty = $this->param['qty'];

        $query = "select * from mes_work_order where uid={$uid}";
        $this->query($query);
        $work_order = $this->fetch();

        if($work_order) {
            $work_qty = $work_order['work_qty'] + $work_qty;
            $remain_qty = $work_order['remain_qty'] - $work_qty;
            if($remain_qty == 0 || $remain_qty < 0) {
                $status = '작업완료';
            } else {
                $status = '부분작업완료';
            }
        }

        $data = array(
            'table' => 'mes_work_order',
            'where' => 'uid=' . $uid,
            'work_qty' => $work_qty,
            'remain_qty' => $remain_qty,
            'status' => $status
        );
        $result = $this->update($data);
        if($result) {
            if($this->registerDailyWorkReport($uid, $work_qty)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '작업일지 등록되었습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '작업일지 등록에 실패하였습니다'
                ];
            }
        } else {
            $this->response = [
                    'result' => 'error',
                    'message' => '작업일지 등록에 실패하였습니다'
            ];            
        }

        echo json_encode($this->response);
    }

    private function registerDailyWorkReport($work_order_uid, $work_qty) {        
        $work_date = date('Y-m-d');        
        $worker = '생산팀';
        $query = "select * from mes_work_order where uid={$work_order_uid}";
        $this->query($query);
        $work_order = $this->fetch();
        $item_uid = $work_order['item_uid'];
        $item_name = $work_order['item_name'];
        $item_code = $work_order['item_code'];
        $standard = $work_order['standard'];
        $unit = $work_order['unit'];
        
        $data = array(
            'table' => 'mes_daily_work',
            'work_date' => $work_date,
            'work_order_uid' => $work_order_uid,
            'worker' => $worker,
            'work_qty' => $work_qty,
            'item_uid' => $item_uid,
            'item_name' => $item_name,
            'item_code' => $item_code,
            'standard' => $standard,
            'unit' => $unit,
            'quality_status' => '품질검사대기'
        );
        return $this->insert($data);
    }

    // 작업일지 등록
    public function registerWorkReport() {
        $uid = $this->param['uid'];
        $item_uid = $this->param['item_uid'];
        $work_date = $this->param['work_date'];
        $work_order_uid = $this->param['work_order_uid'];
        $worker = $this->param['worker'];
        $work_qty = $this->param['work_qty'];

        $query = "select * from mes_items where uid={$item_uid}";
        $this->query($query);
        $item = $this->fetch();

        $query = "select * from mes_work_order where uid={$work_order_uid}";
        $this->query($query);
        $work_order = $this->fetch();

        $query = "select * from mes_employee where uid={$worker}";
        $this->query($query);
        $employee = $this->fetch();                

        if(empty($uid)) {
            $data = array(
                'table' => 'mes_daily_work',
                'work_date' => $work_date,
                'work_order_uid' => $work_order_uid,
                'worker' => $employee['name'],
                'work_qty' => $work_qty,
                'item_uid' => $item_uid,
                'item_name' => $item['item_name'],
                'item_code' => $item['item_code'],
                'standard' => $item['standard'],
                'unit' => $item['unit'],
                'quality_status' => '품질검사대기'
            );

            $result = $this->insert($data);
            $daily_work_uid = $this->getUid();
        } else {
            $data = array(
                'table' => 'mes_daily_work',
                'where' => 'uid=' . $uid,
                'work_date' => $work_date,
                'work_order_uid' => $work_order_uid,
                'worker' => $employee['name'],
                'work_qty' => $work_qty,
                'item_uid' => $item_uid,
                'item_name' => $item['item_name'],
                'item_code' => $item['item_code'],
                'standard' => $item['standard'],
                'unit' => $item['unit'],
            );

            $result = $this->update($data);
            $daily_work_uid = $uid;
        }

        if($result) {
            $query = "select sum(work_qty) as work_qty from mes_daily_work where work_order_uid={$work_order_uid} and quality_status='품질검사대기'";
            $this->query($query);
            $daily_work = $this->fetch();
            $standby_work_qty = $daily_work['work_qty'];

            $data = array(
                'table' => 'mes_work_order',
                'where' => 'uid=' . $work_order_uid,                
                'status' => '품질검사대기'
            );
            $this->update($data);

            /*
            // 작업지시서의 remain_qty 변경
            // 품질검사 후에 생산량이 입고가 된다.
            $remain_qty = $work_order['remain_qty'] - $work_qty;
            if($remain_qty < 0) {
                $remain_qty = 0;
                $status = '완료';
            } else {            
                $status = '진행';
            }
            
            $data = array(
                'table' => 'all_work_order',
                'where' => 'uid=' . $work_order_uid,
                'remain_qty' => $remain_qty,
                'status' => $status
            );
            $this->update($data);
            */

            $this->response = [
                'result' => 'success',
                'message' => '등록하였습니다'
            ];
            
            // $this->updateWorkOrderStatus($work_order_uid, $work_qty);

            /*
            // 재고수량 변경
            $this->updateStockQty($item_uid, $qualified_qty, 'plus');
            // 주간생산량 변경
            $this->weeklyProductionRecord($daily_work_uid, $item_uid, $qualified_qty, $work_date);           
            
            // 불량 등록
            if($defect_qty > 0) {
                $this->defectRegistration($item_uid, $defect_qty, $defect_reason);
            }
            */

        } else {
            $this->response = [
                'result' => 'error',
                'message' => '등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // 작업지시서 상태 변경
    private function updateWorkOrderStatus($work_order_uid, $qty) {
        $query = "select * from all_work_order where uid={$work_order_uid}";
        $this->query($query);
        $work_order = $this->fetch();
        $remain_qty = $work_order['remain_qty'] - $qty;

        if($remain_qty == 0 || $remain_qty < 0) {
            $new_remain_qty = 0;
            $status = '완료';
        } else {
            $new_remain_qty = $remain_qty;
            $status = '진행';
        }

        $data = array(
            'table' => 'all_work_order',
            'where' => 'uid=' . $work_order_uid,
            'remain_qty' => $new_remain_qty,
            'status' => $status
        );
        
        if (!$this->update($data)) {
            throw new Exception("작업지시서 상태 변경 실패 (작업지시서 UID: {$uid})");
        }
    }

    // 작업일보 리스트
    public function getWorkReportList() {    
        $work_order = isset($this->param['work_order']) ? $this->param['work_order'] : 0;
        
        if($work_order == 0) {
            $where = !empty($this->param['where']) ? $this->param['where'] : '';
            $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
            $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
            $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
            $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
            $start = ($page - 1) * $per;
        
            // WHERE 조건이 있을 경우만 추가
            $whereClause = !empty($where) ? "{$where}" : '';
        
            // LIMIT 절을 동적으로 추가
            $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
        
            // 게시물 목록 가져오기
            $query = "
                SELECT * 
                FROM mes_daily_work 
                {$whereClause} 
                ORDER BY {$orderby} {$asc} 
                {$limitClause}
            ";
        } else {
            $query = "SELECT * FROM mes_daily_work where work_order={$work_order} ORDER BY uid ASC";
        }        
        
        $this->query($query);
        $results = $this->fetchAll();
    
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'work_date' => $data['work_date'],
                'work_order_uid' => $data['work_order_uid'],
                'worker' => $data['worker'],
                'work_qty' => $data['work_qty'],
                'item_uid' => $data['item_uid'],
                'item_name' => $data['item_name'],
                'item_code' => $data['item_code'],
                'standard' => $data['standard'],
                'unit' => $data['unit'],
                'quality_status' => $data['quality_status']
            ];
        }, $results);
    
        echo json_encode($this->response);
    }

    public function getWorkReport() {
        $uid = $this->param['uid'];
        
        // 쿼리 실행
        $query = "SELECT * FROM mes_daily_work WHERE uid = {$uid}";
        $this->query($query);
        $result = $this->fetch(); // 하나의 행만 가져옴

        // 응답 데이터 구성
        if ($result) {
            $this->response = [
                'uid' => $result['uid'],
                'work_date' => $result['work_date'],
                'work_order_uid' => $result['work_order_uid'],
                'worker' => $result['worker'],
                'work_qty' => $result['work_qty'],
                'item_uid' => $result['item_uid'],
                'item_name' => $result['item_name'],
                'item_code' => $result['item_code'],
                'standard' => $result['standard'],
                'unit' => $result['unit'],
                'quality_status' => $result['quality_status'],
            ];
        } else {
            // 결과가 없는 경우 처리
            $this->response = ['error' => 'No machine found'];
        }

        // JSON 응답 반환
        echo json_encode($this->response);
    }

    // 작업일지 삭제
    public function deleteWorkReport() {
        $uid = $this->param['uid'];
        
        $query = "delete from all_daily_work where uid={$uid}";
        $result = $this->query($query);

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 삭제하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '작업일지 삭제 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function deleteWorkOrder() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from mes_work_order where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '작업지시 삭제 중 에러가 발생하였습니다'
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

    public function getWorkOrdersCalendar() {
        $year = $this->param['year'];
        $month = $this->param['month'];
        
        $query = "SELECT * FROM mes_work_order WHERE YEAR(order_date) = {$year} AND MONTH(order_date) = {$month}";
        
        $this->query($query);
        $results = $this->fetchAll();

        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'order_uid' => $data['order_uid'],
                    'account_uid' => $data['account_uid'],
                    'account_name' => $data['account_name'],
                    'item_uid' => $data['item_uid'],
                    'item_code' => $data['item_code'],
                    'item_name' => $data['item_name'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'order_date' => $data['order_date'],
                    'order_qty' => $data['order_qty'],
                    'work_qty' => $data['work_qty'],
                    'pass_qty' => $data['pass_qty'],
                    'fail_qty' => $data['fail_qty'],
                    'quality_qty' => $data['quality_qty'],
                    'remain_qty' => $data['remain_qty'],
                    'work_start_time' => $data['work_start_time'],
                    'work_end_time' => $data['work_end_time'],
                    'status' => $data['status'],
                    'register_date' => $data['register_date']
                ];
            }, $results)
        ];

        echo json_encode($this->response);
    }

    // 작업일보 리스트
    public function getDailyWorkList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 총 게시물 개수 가져오기
        $countQuery = "SELECT COUNT(*) as totalCount FROM mes_daily_work {$whereClause}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_daily_work 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'work_date' => $data['work_date'],
                    'work_order_uid' => $data['work_order_uid'],
                    'worker' => $data['worker'],
                    'work_qty' => $data['work_qty'],
                    'item_uid' => $data['item_uid'],
                    'item_name' => $data['item_name'],
                    'item_code' => $data['item_code'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'quality_status' => $data['quality_status']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    public function getDailyWork() {
        $uid = $this->param['uid'];
        
        // 쿼리 실행
        $query = "SELECT * FROM mes_daily_work WHERE uid = {$uid}";
        $this->query($query);
        $result = $this->fetch(); // 하나의 행만 가져옴

        // 응답 데이터 구성
        if ($result) {
            $this->response = [
                'uid' => $result['uid'],
                'workDate' => $result['workDate'],
                'classification' => $result['classification'],
                'item' => $result['item'],
                'itemName' => $result['itemName'],
                'code' => $result['code'],
                'process' => $result['process'],
                'processName' => $result['processName'],
                'employee' => $result['employee'],
                'employeeName' => $result['employeeName'],
                'qty' => $result['qty'],
                'defectiveQty' => $result['defectiveQty'],
                'defectiveReason' => $result['defectiveReason']
            ];
        } else {
            // 결과가 없는 경우 처리
            $this->response = ['error' => 'No machine found'];
        }

        // JSON 응답 반환
        echo json_encode($this->response);
    }

    // 작업일지 등록
    public function registerDailyWork() {
        $uid = $this->param['item'];
        $qty = $this->removeComma($this->param['qty']);  // 적격제품 수량      
        $itemName = $this->getFieldValue('mes_items', 'name', $uid);
        $processName = $this->getFieldValue('mes_process', 'name', $this->param['process']);
        $lastProcess = $this->getFieldValue('mes_process', 'lastProcess', $this->param['process']);
        $employeeName = $this->getFieldValue('mes_employee', 'name', $this->param['employee']);
        $defectiveQty = (!empty($this->param['defectiveQty'])) ? $this->removeComma($this->param['defectiveQty']) : 0;
        $defectiveReason = $this->param['defectiveReason'];
        $currentDate = date('Y-m-d');
        $totalQty = $qty + $defectiveQty;
        $query = "select * from mes_items where uid=" . $uid;
        $productItem = $this->queryFetch($query); 

        // 1. 환경설정값을 읽어온다
        $setting = $this->getSettingFetch();

        try {
            $this->beginTransaction();

            if($setting['enableBom'] == 'Y') { // BOM을 사용한다면
                //echo "bom사용";
                // 마지막 공정이라면
                if($lastProcess == 'Y') {
                    // bom 불출 - 불출과 함께 재고수량도 변경해줘야 한다
                    // 그런데 이렇게 되면 불량수량은 계산이 되지 않잖아?
                    $returnQty = $this->productionWithdrawal($uid, $totalQty, $this->param['workDate'], $setting['minusStockCount']);
                    //echo "return Qty : {$returnQty}";
                    // 생산입고
                    // 아 여기서 $returnQty 에서 불량수량을 빼야하겠지.
                    $productQty = $returnQty - $defectiveQty;
                    $this->productionInput($uid, $productItem['name'], $productItem['code'], $productItem['standard'], $productItem['unit'], $productQty, $this->param['workDate']);
                    // 불량품 등록
                    $this->defectRegistration($uid, $defectiveQty, $defectiveReason);
                    // 주간 생산량 갱신
                    $this->weeklyProductionRecord($uid, $productQty, $this->param['workDate']);
                }
            } else { // BOM을 사용하지 않는다면
                // 마지막 공정이라면
                if($lastProcess == 'Y') {
                    // 생산입고
                    $this->productionInput($uid, $productItem['name'], $productItem['code'], $productItem['standard'], $productItem['unit'], $qty, $this->param['workDate']);
                    // 주간 생산량 갱신
                    $this->weeklyProductionRecord($uid, $qty, $this->param['workDate']);
                }
            }

            $this->commit();

            $this->response = [
                'result' => 'success',
                'message' => '등록하였습니다'
            ];
        }catch (Exception $e) {
            // 트랜잭션 롤백
            $this->rollback();
                
            // 에러 응답 설정
            $this->response = [
                'result' => 'error',
                'message' => $e->getMessage()
            ];
        }

        echo json_encode($this->response);
    }

    // 불량등록
    private function defectRegistration($uid, $qty, $reason) {
        $query = "select * from mes_items where uid={$uid}";        
        $this->query($query);
        $item = $this->fetch();
        $currentDate = date('Y-m-d');
        if($qty > 0) {
            $data = array(
                'table' => 'mes_defective_report',
                'itemName' => $item['name'],
                'itemCode' => $item['code'],
                'reason' => $reason,
                'qty' => $qty,
                'registerDate' => $currentDate
            );            
            
            if (!$this->insert($data)) {
                throw new Exception("불량 등록 실패 (아이템 UID: {$uid})");
            }
        }
    }
    
    // 주간 생산량 추가
    private function weeklyProductionRecord($uid, $qty, $workDate) {
        // 기존에 해당 주간에 등록된 생산수량이 있는지 확인한다
        $query = "SELECT * FROM mes_weekly_product WHERE itemUid={$uid} and productDate='{$workDate}'";
        $this->query($query);

        if ($this->getRows() > 0) {
            $week = $this->fetch();
            $newQty = $week['qty'] + $qty;
            $data = array(
                'table' => 'mes_weekly_product',
                'where' => 'uid=' . $week['uid'],
                'qty' => $newQty
            );
            if (!$this->update($data)) {
                throw new Exception("주간생산량 변경 실패 (아이템 UID: {$uid})");
            }
        } else {
            $query = "select * from mes_items where uid={$uid}";
            $this->query($query);
            $item = $this->fetch();

            $data = array(
                'table' => 'mes_weekly_product',
                'itemUid' => $uid,
                'name' => $item['name'],
                'code' => $item['code'],
                'standard' => $item['standard'],
                'unit' => $item['unit'],
                'qty' => $qty,
                'productDate' => $workDate
            );
            if (!$this->insert($data)) {
                throw new Exception("주간생산량 등록 실패 (아이템 UID: {$uid})");
            }
        }
    }

    // 재고수량 변경
    private function updateStockQty($uid, $qty, $method) {
        $query = "select stock_qty from mes_items where uid={$uid}";
        $this->query($query);
        $item = $this->fetch();
        if($method == 'plus') { // plus 처리
            $newQty = $item['stock_qty'] + $qty;
        } else {
            $newQty = $item['stock_qty'] - $qty;
        }
        $data = array(
            'table' => 'mes_items',
            'where' => 'uid=' . $uid,
            'stock_qty' => $newQty
        );
        //var_dump($data);

        if (!$this->update($data)) {
            throw new Exception("재고수량 변경 실패 (아이템 UID: {$uid})");
        }
    }

    // 생산입고 처리
    private function productionInput($uid, $name, $code, $standard, $unit, $qty, $workDate) {
        //echo "UID : {$uid} / ";
        //echo "NAME : {$name} / ";
        //echo "CODE : {$code} / ";
        //echo "STANDARD : {$standard} / ";
        //echo "UNIT : {$unit} / ";
        //echo "QTY : {$qty} / ";
        //echo "WORKDATE : {$workDate} / ";

        $data = array(
            'table' => 'mes_items_inout',
            'classification' => '생산입고',
            'item_uid' => $uid,
            'item_name' => $name,
            'item_code' => $code,
            'standard' => $standard,
            'unit' => $unit,
            'qty' => $qty,
            'register_date' => $workDate
        );      
        
        //var_dump($data);

        if (!$this->insert($data)) {
            throw new Exception("재고수량 변경 실패 (아이템 UID: {$uid})");
        }

        // 재고수량 변경
        $this->updateStockQty($uid, $qty, 'plus');
    }

    // BOM에 따라 자재 불출을 한다
    private function productionWithdrawal($uid, $qty, $workDate, $agreeMinus) {
        // 가능한 수량만 불출이 되어야 한다
        $maxQty = $this->getMaxQty($uid, $qty);
        //echo "maxQty : " . $maxQty;

        $subQuery = "SELECT * FROM mes_bom WHERE gid={$uid} AND uid!={$uid}";
        //echo $query;
        $this->subQuery($subQuery);
        
        $i = 1;
        while($item = $this->subFetch()) {
            $outQty = $qty * $item['qty'];
            //echo "품목명 : {$item['name']}";
            //echo "소요량 : {$outQty}";

            if($agreeMinus == 'Y') { // 재고수량의 마이너스를 허용한다면
                $data = array(
                    'table' => 'mes_items_inout',
                    'classification' => '생산불출',
                    'item_uid' => $item['itemUid'],
                    'item_name' => $item['name'],
                    'item_code' => $item['code'],
                    'standard' => $item['standard'],
                    'unit' => $item['unit'],
                    'qty' => $outQty,
                    'register_date' => $workDate
                );
    
                if (!$this->insert($data)) {
                    throw new Exception("자재불출 기록 실패 (아이템 UID: {$uid})");
                }
    
                // 재고수량 변경
                $this->updateStockQty($item['itemUid'], $outQty, 'minus');
            } else { // 재고수량의 마이너스를 허용하지 않는다면                
                // 실 소요량 계산
                $outQty = $maxQty * $item['qty'];
                //echo "MAX QTY : {$maxQty} / ";
                //echo "ITEM QTY : {$item['qty']} / ";
                //echo "OUT QTY : {$outQty} / ";

                $data = array(
                    'table' => 'mes_items_inout',
                    'classification' => '생산불출',
                    'itemUid' => $item['itemUid'],
                    'item_name' => $item['name'],
                    'item_code' => $item['code'],
                    'standard' => $item['standard'],
                    'unit' => $item['unit'],
                    'qty' => $outQty,
                    'register_date' => $workDate
                );
    
                if (!$this->insert($data)) {
                    echo "자재불출 기록 실패";
                    throw new Exception("자재불출 기록 실패 (아이템 UID: {$uid})");
                }
    
                // 재고수량 변경
                $this->updateStockQty($item['itemUid'], $outQty, 'minus');
            }      
        }

        return $maxQty;
    }

    // 소요량 최소값 구하기
    private function getMaxQty($uid, $qty) {     
        // BOM 테이블에서 필요한 하위 부품 리스트 조회
        $query = "SELECT item_uid, qty FROM mes_bom WHERE gid={$uid} AND item_uid!={$uid}";
        $this->query($query);
    
        $list = array();
        while ($item = $this->fetch()) {
            // 각 하위 부품의 현재 재고 수량 조회
            $subQuery = "SELECT stock_qty FROM mes_items WHERE uid={$item['item_uid']}";
            $this->subQuery($subQuery);
            $data = $this->subFetch();
    
            // 현재 재고로 만들 수 있는 최대 수량 계산
            if ($item['qty'] > 0) {
                $maxProductQty = intdiv($data['stock_qty'], $item['qty']);
                $list[] = $maxProductQty;
            }
        }
    
        // 최대로 만들 수 있는 완제품의 최소 개수를 반환 (가장 제한적인 부품 기준)
        return !empty($list) ? min($list) : 0;
    }
    
    

    // 작업일보 삭제
    public function deleteDailyWork() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from mes_daily_work where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '작업일보 삭제 중 에러가 발생하였습니다'
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

    //===============================================================================================================================//
    // 불량
    //===============================================================================================================================//
    
    public function getDefectiveList() {
        // 쿼리 실행
        $query = "SELECT * FROM mes_defect_reason";
        $this->query($query);
        $results = $this->fetchAll();
    
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            return [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        }, $results);
    
        // JSON 응답 반환
        echo json_encode($this->response);
    }

    // 불량현황 리스트
    public function getDefectStatusList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 총 게시물 개수 가져오기
        $countQuery = "SELECT COUNT(*) as totalCount FROM mes_defective_report {$whereClause}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_defective_report 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'itemName' => $data['itemName'],
                    'itemCode' => $data['itemCode'],
                    'reason' => $data['reason'],
                    'qty' => $data['qty'],
                    'registerDate' => $data['registerDate']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    //===============================================================================================================================//
    // 출하
    //===============================================================================================================================//

    public function registerShipmentOrder() {
        $order_uid = (empty($this->param['uid'])) ? 0 : $this->param['uid'];
        $item = $this->getData('mes_items', $this->param['item']);
        $account = $this->getData('mes_account', $this->param['account']);
        $qty = $this->removeComma($this->param['qty']);

        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'mes_delivery',
                'order_uid' => $order_uid,
                'account_uid' => $account['uid'],
                'account_name' => $account['name'],
                'item_uid' => $item['uid'],
                'item_name' => $item['item_name'],
                'item_code' => $item['item_code'],
                'standard' => $item['standard'],
                'unit' => $item['unit'],
                'delivery_date' => $this->param['shipmentDate'],
                'delivery_qty' => $qty,
                'remain_qty' => $qty,
                'status' => '출하지시'
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'mes_delivery',
                'where' => 'uid=' . $this->param['uid'],
                'order_uid' => $order_uid,
                'account_uid' => $account['uid'],
                'account_name' => $account['name'],
                'item_uid' => $item['uid'],
                'item_name' => $item['name'],
                'item_code' => $item['code'],
                'standard' => $item['standard'],
                'unit' => $item['unit'],
                'delivery_date' => $this->param['shipmentDate'],
                'delivery_qty' => $qty,
                'remain_qty' => $qty,
                'status' => '출하지시'
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
                'message' => '출하지시서 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    // 출하지시서 리스트
    public function getShipmentOrderList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_delivery 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'order_uid' => $data['order_uid'],
                    'account_uid' => $data['account_uid'],
                    'account_name' => $data['account_name'],
                    'item_uid' => $data['item_uid'],
                    'item_code' => $data['item_code'],
                    'item_name' => $data['item_name'],
                    'standard' => $data['standard'],                    
                    'unit' => $data['unit'],
                    'delivery_date' => $data['delivery_date'],
                    'delivery_qty' => $data['delivery_qty'],
                    'status' => $data['status']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    public function getShipmentList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 총 게시물 개수 가져오기
        $countQuery = "SELECT COUNT(*) as totalCount FROM mes_shipment {$whereClause}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_shipment 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'fid' => $data['fid'],
                    'shipmentDate' => $data['shipmentDate'],
                    'account' => $data['account'],
                    'accountName' => $data['accountName'],
                    'item' => $data['item'],
                    'itemName' => $data['itemName'],
                    'code' => $data['code'],
                    'standard' => $data['standard'],
                    'address' => $data['address'],
                    'qty' => $data['qty'],
                    'loginId' => $data['loginId'],
                    'registerDate' => $data['registerDate']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    
    public function getShipmentAllList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // 총 게시물 개수 가져오기
        $countQuery = "SELECT COUNT(*) as totalCount FROM mes_shipment {$whereClause}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_shipment 
            {$whereClause}
        ";                    
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'fid' => $data['fid'],
                    'shipmentDate' => $data['shipmentDate'],
                    'account' => $data['account'],
                    'accountName' => $data['accountName'],
                    'item' => $data['item'],
                    'itemName' => $data['itemName'],
                    'code' => $data['code'],
                    'standard' => $data['standard'],
                    'address' => $data['address'],
                    'qty' => $data['qty'],
                    'loginId' => $data['loginId'],
                    'registerDate' => $data['registerDate']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }

    // 출하지시서 하나 가져오기
    public function getShipmentOrder() {
        $query = "select * from mes_shipment_order where uid=" . $this->param['uid'];
        $this->query($query);
        $order = $this->fetch();

        // 응답 데이터 구성
        if ($order) {
            $this->response = [
                'uid' => $order['uid'],
                'classification' => $order['classification'],
                'item' => $order['item'],
                'itemName' => $order['itemName'],
                'code' => $order['code'],
                'standard' => $order['standard'],
                'shipmentDate' => $order['shipmentDate'],
                'account' => $order['account'],
                'accountName' => $order['accountName'],
                'address' => $order['address'],
                'qty' => $order['qty'],
                'remainQty' => $order['remainQty']
            ];
        } else {
            // 결과가 없는 경우 처리
            $this->response = ['error' => 'No Shipment Order found'];
        }

        // JSON 응답 반환
        echo json_encode($this->response);
    }

    // 출하등록
    public function registerShipment() {
        $qty = $this->removeComma($this->param['shipmentQty']);
        $currentDateTime = date('Y-m-d H:i:s');

        $query = "select * from mes_shipment_order where uid=" . $this->param['shipmentOrderUid'];
        $this->query($query);
        $order = $this->fetch();

        $item = $this->getData('mes_items', $order['item']);
        $query = "select * from mes_account where name='" . $this->param['account'] . "'";
        $this->query($query);
        $account = $this->fetch();

        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'mes_shipment',
                'fid' => $this->param['shipmentOrderUid'],
                'shipmentDate' => $this->param['shipmentDate'],
                'account' => $order['account'],
                'accountName' => $order['accountName'],
                'item' => $order['item'],
                'itemName' => $order['itemName'],
                'code' => $order['code'],
                'standard' => $order['standard'],
                'address' => $order['address'],
                'qty' => $qty,
                'loginId' => $_SESSION['loginId'],
                'registerDate' => $currentDateTime
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'mes_shipment',
                'where' => 'uid=' . $this->param['uid'],
                'shipmentDate' => $this->param['shipmentDate'],
                'fid' => $this->param['shipmentOrderUid'],
                'account' => $order['account'],
                'accountName' => $order['accountName'],
                'item' => $order['item'],
                'itemName' => $order['itemName'],
                'code' => $order['code'],
                'standard' => $order['standard'],
                'address' => $order['address'],
                'qty' => $qty,
                'loginId' => $_SESSION['loginId'],
                'registerDate' => $currentDateTime
            );
            $result = $this->update($data);
        }

        if($result) {
            // 정상 등록 되었으면 shipment_order 의 remain Qty 수량을 변경해준다
            $remainQty = $order['remainQty'] - $qty;
            $data = array(
                'table' => 'mes_shipment_order',
                'where' => 'uid=' . $this->param['shipmentOrderUid'],
                'remainQty' => $remainQty
            );
            $res = $this->update($data);

            if($res) {
                // mes_item 의 stock_qty 도 변경해줘야 한다
                $new_stock_qty = $item['stock_qty'] - $qty;

                $data = array(
                    'table' => 'mes_items',
                    'where' => 'uid=' . $order['item'],
                    'stock_qty' => $new_stock_qty
                );
                $this->update($data);
                
                $this->response = [
                    'result' => 'success',
                    'message' => '등록하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'success',
                    'message' => '해당 품목의 재고수량 변경 중 에러가 발생하였습니다'
                ];
            }
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '츨하 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    //===============================================================================================================================//
    // 불량 사유
    //===============================================================================================================================//   
    
    public function registerDefectReason() {
        if(empty($this->param['uid'])) {
			$data = array(
				'table' => 'mes_defect_reason',
				'name' => $this->param['name']
			);            
			$result = $this->insert($data);
		} else {
			$data = array(
				'table' => 'mes_defect_reason',
				'where' => 'uid='.$this->param['uid'],
				'name' => $this->param['name']
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
                'message' => '불량 사유 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function deleteDefectReason() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from mes_defect_reason where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '불량사유 삭제 중 에러가 발생하였습니다'
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

    public function getDefectReason() {
        $uid = $this->param['uid'];
        $query = "select * from mes_defect_reason where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'name' => $data['name']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found'
            ];
        }

        echo json_encode($this->response);
    }

    public function getDefectReasonList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
            
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
            
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
            
        // 총 게시물 개수 가져오기
        $countQuery = "SELECT COUNT(*) as totalCount FROM mes_defect_reason {$whereClause}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
            
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_defect_reason 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
            
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'name' => $data['name']
                ];
            }, $results)
        ];
            
        echo json_encode($this->response);
    }

    //===============================================================================================================================//
    // 구매관리
    //===============================================================================================================================//    
    // 구매요청 등록
    public function registerPurchase() {
        $uid = $this->param['uid'];
        $current_date = date('Y-m-d');
        $item_uid = $this->param['item_uid']; // 품목UID
        $query = "select * from mes_items where uid={$item_uid}";
        $this->query($query);
        $item = $this->fetch();

        $query = "select * from mes_account where uid={$this->param['account']}";
        $this->query($query);
        $account = $this->fetch();

        $purchase_qty = $this->removeComma($this->param['purchase_qty']); // 구매수량

        if(empty($uid)) {

            $data = array(
                'table' => 'mes_purchase',                
                'account_uid' => $account['uid'],
                'account_name' => $account['name'],
                'status' => '구매요청',
                'purchase_date' => $current_date
            );     
            $result = $this->insert($data);
            $fid = $this->getUid();

            $data = array(
                'table' => 'mes_purchase_item',                
                'fid' => $fid,
                'account_uid' => $account['uid'],
                'account_name' => $account['name'],                
                'classification' => $item['classification'],
                'item_uid' => $item_uid,
                'item_name' => $item['item_name'],
                'item_code' => $item['item_code'],
                'standard' => $item['standard'],
                'unit' => $item['unit'],
                'purchase_qty' => $purchase_qty,
                'remain_qty' => $purchase_qty,
                'status' => '입고대기',
                'purchase_date' => $current_date,
                'in_date' => '0000-00-00'
            );

            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'mes_purchase',
                'where' => "uid={$uid}",                
                'account_uid' => $account['uid'],
                'account_name' => $account['name']                
            );     
            $result = $this->update($data);

            // 먼저 기존의 구매요청 품목을 삭제한다
            $query = "delete from mes_purchase_item where fid={$uid}";
            $this->query($query);

            $data = array(
                'table' => 'mes_purchase_item',                
                'fid' => $uid,
                'account_uid' => $account['uid'],
                'account_name' => $account['name'],
                'classification' => $item['classification'],
                'item_uid' => $item_uid,
                'item_name' => $item['item_name'],
                'item_code' => $item['item_code'],
                'standard' => $item['standard'],
                'unit' => $item['unit'],
                'purchase_qty' => $purchase_qty,
                'remain_qty' => $purchase_qty,
                'status' => '입고대기',
                'purchase_date' => $current_date,
                'in_date' => '0000-00-00'
            );

            $result = $this->insert($data);
        }

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 등록이 되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '구매 요청 등록 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function getPurchaseList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;

        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';

        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';

        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_purchase
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";             
        $this->query($query);
        $results = $this->fetchAll();

        $this->response = [            
            'data' => array_map(function($data) {                            
                return [
                    'uid' => $data['uid'],                  
                    'account_uid' => $data['account_uid'],
                    'account_name' => $data['account_name'],
                    'status' => $data['status'],
                    'purchase_date' => $data['purchase_date']
                ];
            }, $results)
        ];

        echo json_encode($this->response);
    }

    public function getPurchase() {
        $uid = $this->param['uid'];
        $query = "select * from mes_purchase_item where fid={$uid}";             
        $this->query($query);
        $data = $this->fetch();

        $query = "select * from mes_items where uid={$data['item_uid']}";
        $this->query($query);
        $item = $this->fetch();

        if($data) {
            $this->response = [
                'result' => 'success',
                'data' => [
                    'uid' => $data['uid'],
                    'account_uid' => $data['account_uid'],
                    'account_name' => $data['account_name'],
                    'classification' => $data['classification'],
                    'item_uid' => $data['item_uid'],
                    'item_name' => $data['item_name'],
                    'item_code' => $data['item_code'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'purchase_qty' => $data['purchase_qty'],
                    'remain_qty' => $data['remain_qty'],
                    'status' => $data['status'],
                    'purchase_date' => $data['purchase_date'],
                    'stock_qty' => $item['stock_qty'],
                    'safety_stock_qty' => $item['safety_stock_qty']
                ]
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '구매요청 데이터를 가져오는 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function deletePurchase() {
        $uid = $this->param['uid'];
        $query = "delete from mes_purchase where uid={$uid}";
        $result = $this->query($query);
        if($result) {
            $query = "delete from mes_purchase_item where fid={$uid}";
            $result = $this->query($query);

            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 삭제되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '구매요청 삭제 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function getPurchaseItemList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;

        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';

        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';

        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_purchase_item
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";             
        $this->query($query);
        $results = $this->fetchAll();

        $this->response = [            
            'data' => array_map(function($data) {                            
                return [
                    'uid' => $data['uid'],                  
                    'account_uid' => $data['account_uid'],
                    'account_name' => $data['account_name'],
                    'classification' => $data['classification'],
                    'item_uid' => $data['item_uid'],
                    'item_name' => $data['item_name'],  
                    'item_code' => $data['item_code'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'purchase_qty' => $data['purchase_qty'],
                    'remain_qty' => $data['remain_qty'],
                    'status' => $data['status'],
                    'purchase_date' => $data['purchase_date']
                ];
            }, $results)
        ];

        echo json_encode($this->response);
    }

    // 수입검사 완료된 구매품 입고 처리
    public function registerItemIn() {
        $account_uid = $this->param['account'];
        $item_uid = $this->param['item'];
        $in_date = $this->param['inDate'];
        $in_qty = $this->param['qty'];
        $fid = 0;

        $query = "select * from mes_account where uid={$account_uid}";
        $this->query($query);
        $account = $this->fetch();

        $query = "select * from mes_items where uid={$item_uid}";
        $this->query($query);
        $item = $this->fetch();

        // 구매요청 품목에 등록하고 입고처리한다
        $data = array(
            'table' => 'mes_purchase_item',
            'fid' => $fid,
            'account_uid' => $account['uid'],
            'account_name' => $account['name'],
            'classification' => $item['classification'],
            'item_uid' => $item_uid,
            'item_name' => $item['item_name'],
            'item_code' => $item['item_code'],
            'standard' => $item['standard'],
            'unit' => $item['unit'],
            'purchase_qty' => $in_qty,
            'remain_qty' => 0,
            'status' => '입고완료',
            'purchase_date' => $in_date,
            'in_date' => $in_date
        );
        $result = $this->insert($data);
        if(!$result) {
            $this->response = [
                'result' => 'error',
                'message' => '구매요청 품목 등록 중 에러가 발생하였습니다'
            ];
        }

        // 품목 재고수량 변경
        $new_stock_qty = $item['stock_qty'] + $in_qty;
        $data = array(
            'table' => 'mes_items',
            'where' => 'uid=' . $item_uid,
            'stock_qty' => $new_stock_qty
        );
        $item_result = $this->update($data);
        if(!$item_result) {
            $this->response = [
                'result' => 'error',
                'message' => '품목 재고수량 변경 중 에러가 발생하였습니다'
            ];
        }

        // 자재수불부용 테이블에 기록한다
        $data = array(
            'table' => 'mes_stock_log',                    
            'classification' => '입고',
            'item_uid' => $item_uid,
            'item_code' => $item['item_code'],
            'item_name' => $item['item_name'],
            'standard' => $item['standard'],
            'unit' => $item['unit'],
            'in_qty' => $in_qty,
            'out_qty' => 0,
            'register_date' => $in_date
        );                
        $stock_log_result = $this->insert($data);

        if($stock_log_result) {
            $new_stock_qty = $item['stock_qty'] + $in_qty;
            $data = array(
                'table' => 'mes_items',
                'where' => 'uid=' . $item_uid,
                'stock_qty' => $new_stock_qty
            );
            $item_result = $this->update($data);
            if(!$item_result) {
                $this->response = [
                    'result' => 'error',
                    'message' => '재고 수량 변경 중 에러가 발생하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 입고처리가 되었습니다'
                ];
            }
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '입고처리 중 에러가 발생하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    public function getPurchaseItem() {
        $uid = $this->param['uid'];
        $query = "select * from mes_purchase_item where uid={$uid}";
        $this->query($query);
        $data = $this->fetch();

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'fid' => $data['fid'],
                'account_uid' => $data['account_uid'],
                'account_name' => $data['account_name'],
                'classification' => $data['classification'],
                'item_uid' => $data['item_uid'],
                'item_name' => $data['item_name'],
                'item_code' => $data['item_code'],
                'standard' => $data['standard'],
                'unit' => $data['unit'],
                'purchase_qty' => $data['purchase_qty'],
                'remain_qty' => $data['remain_qty'],
                'status' => $data['status'],
                'purchase_date' => $data['purchase_date']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    public function registerPurchaseItemIn() {
        $uid = $this->param['purchase_item_uid'];
        $in_qty = $this->param['in_qty'];
        $query = "select * from mes_purchase_item where uid={$uid}";
        $this->query($query);
        $purchase_item = $this->fetch();

        $query = "select * from mes_items where uid={$purchase_item['item_uid']}";
        $this->query($query);
        $item = $this->fetch();

        if($purchase_item['remain_qty'] < $in_qty) {
            $this->response = [
                'result' => 'error',
                'message' => '입고 수량이 구매 요청 수량보다 큽니다',
            ];
        } else {
            $new_remain_qty = $purchase_item['remain_qty'] - $in_qty;
            if($new_remain_qty == 0) {
                $status = '가입고완료';
            } else {
                $status = '가입고중';
            }

            $data = array(
                'table' => 'mes_purchase',
                'where' => 'uid=' . $purchase_item['fid'],
                'status' => $status
            );
            $purchase_result = $this->update($data);

            $data = array(
                'table' => 'mes_purchase_item',
                'where' => 'uid=' . $uid,
                'remain_qty' => $new_remain_qty,
                'status' => $status
            );
            $result = $this->update($data);
            if($result) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 등록이 되었습니다'
                ];

                // 재고 수량을 변경한다
                $new_stock_qty = $item['stock_qty'] + $in_qty;
                $data = array(
                    'table' => 'mes_items',
                    'where' => 'uid=' . $purchase_item['item_uid'],
                    'stock_qty' => $new_stock_qty
                );
                $item_result = $this->update($data);
                if(!$item_result) {
                    $this->response = [
                        'result' => 'error',
                        'message' => '재고 수량 변경 중 에러가 발생하였습니다'
                    ];
                }

                // 자재수불부용 테이블에 기록한다
                $data = array(
                    'table' => 'mes_stock_log',                    
                    'classification' => '가입고',
                    'item_uid' => $purchase_item['item_uid'],
                    'item_code' => $purchase_item['item_code'],
                    'item_name' => $purchase_item['item_name'],
                    'standard' => $purchase_item['standard'],
                    'unit' => $purchase_item['unit'],
                    'in_qty' => $in_qty,
                    'out_qty' => 0,
                    'register_date' => date('Y-m-d')
                );
                $stock_log_result = $this->insert($data);
                if(!$stock_log_result) {
                    $this->response = [
                        'result' => 'error',
                        'message' => '자재수불부 기록 중 에러가 발생하였습니다'
                    ];
                }
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '등록 중 에러가 발생하였습니다'
                ];
            }
        }

        echo json_encode($this->response);
    }

    // 입고처리
    public function completePurchaseItem() {
        $current_date = date('Y-m-d');

        $uid = $this->param['uid'];
        $query = "select * from mes_purchase_item where uid={$uid}";
        $this->query($query);
        $purchase_item = $this->fetch();

        $data = array(
            'table' => 'mes_purchase_item',
            'where' => 'uid=' . $uid,
            'status' => '입고완료',
            'in_date' => $current_date
        );
        $result = $this->update($data);
        if(!$result) {
            $this->response = [
                'result' => 'error',
                'message' => '입고처리 중 에러가 발생하였습니다'
            ];

            echo json_encode($this->response);
            exit;
        }

                
        $query = "select * from mes_items where uid={$purchase_item['item_uid']}";
        $this->query($query);
        $item = $this->fetch();
        $new_stock_qty = $item['stock_qty'] + $purchase_item['purchase_qty'];
        $data = array(
            'table' => 'mes_items',
            'where' => 'uid=' . $purchase_item['item_uid'],
            'stock_qty' => $new_stock_qty
        );
        $this->update($data);
        if(!$result) {
            $this->response = [
                'result' => 'error',
                'message' => '재고 수량 변경 중 에러가 발생하였습니다'
            ];

            echo json_encode($this->response);
            exit;
        }

        $data = array(
            'table' => 'mes_stock_log',
            'classification' => '입고',
            'item_uid' => $purchase_item['item_uid'],
            'item_code' => $purchase_item['item_code'],
            'item_name' => $purchase_item['item_name'],
            'standard' => $purchase_item['standard'],
            'unit' => $purchase_item['unit'],
            'in_qty' => $purchase_item['purchase_qty'],
            'out_qty' => 0,
            'register_date' => $current_date
        );
        $result = $this->insert($data);
        if(!$result) {
            $this->response = [
                'result' => 'error',
                'message' => '자재수불부 기록 중 에러가 발생하였습니다'
            ];

            echo json_encode($this->response);
            exit;
        }

        $this->response = [
            'result' => 'success',
            'message' => '정상적으로 입고처리가 되었습니다'
        ];

        echo json_encode($this->response);
    }

    // 구매요청 품목 삭제
    public function deletePurchaseItem() {
        $uid = $this->param['uid'];
        $query = "delete from mes_purchase_item where uid={$uid}";
        $result = $this->query($query);
        
        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 삭제하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '삭제 중 에러가 발생하였습니다'
            ];
        }
        echo json_encode($this->response);
    }   

    public function getWaitItemList() {        
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
        $whereClause = !empty($where) ? "{$where}" : '';
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';

        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM wc_purchase_item
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";             
        $this->query($query);
        $results = $this->fetchAll();

        $this->response = [            
            'data' => array_map(function($data) {         
                return [
                    'uid' => $data['uid'],
                    'account_uid' => $data['account_uid'],
                    'account_name' => $this->getFieldValue('wc_account', 'name', $data['account_uid']),
                    'purchase_code' => $data['purchase_code'],                    
                    'shipmentPlace' => $data['shipmentPlace'],
                    'car_model' => $data['car_model'],
                    'item_uid' => $data['item_uid'],
                    'item_code' => $data['item_code'],
                    'item_name' => $data['item_name'],
                    'material' => $data['material'],
                    'unit' => $data['unit'],
                    'thickness' => $this->getFieldValue('wc_items', 'thickness', $data['item_uid']),
                    'width' => $this->getFieldValue('wc_items', 'width', $data['item_uid']),
                    'pitch' => $this->getFieldValue('wc_items', 'pitch', $data['item_uid']),
                    'qty' => $data['qty'],
                    'purchase_price' => $data['purchase_price'],
                    'purchase_amount' => $data['purchase_amount'],
                    'vat' => $data['vat'],
                    'total_amount' => $data['total_amount'],
                    'remain_qty' => $data['remain_qty'],
                    'status' => $data['status'],
                    'purchase_date' => $data['purchase_date']
                ];
            }, $results)
        ];

        echo json_encode($this->response);
    }

    // 입고등록
    public function registerWarehousing() {
        $uid = $this->param['uid'];
        $warehousing_date = $this->param['warehousing_date'];
        $warehouse_uid = $this->param['warehouse'];
        $coil_number = $this->param['coil_number'];
        $warehousing_qty = $this->param['warehousing_qty'];
        $coil_count = $this->param['coil_count'];

        $query = "select * from wc_purchase_item where uid={$uid}";
        $this->query($query);
        $purchase_item = $this->fetch();

        $query = "select * from wc_items where uid={$purchase_item['item_uid']}";
        $this->query($query);
        $item = $this->fetch();

        $query = "select * from wc_warehouse where uid={$warehouse_uid}";
        $this->query($query);
        $warehouse = $this->fetch();

        $data = array(
            'table' => 'wc_warehousing',
            'purchase_code' => $purchase_item['purchase_code'],
            'item_uid' => $item['uid'],
            'item_code' => $item['item_code'],
            'item_name' => $item['item_name'],
            'material' => $item['material'],
            'thickness' => $item['thickness'],
            'width' => $item['width'],
            'pitch' => $item['pitch'],
            'warehouse_uid' => $warehouse['uid'],
            'warehouse_name' => $warehouse['name'],            
            'coil_number' => $coil_number,
            'coil_count' => $coil_count,
            'qty' => $warehousing_qty,
            'warehousing_date' => $warehousing_date
        );

        $result = $this->insert($data);
        if($result) {
            // 입고 처리가 잘 되었다면 품목의 재고 수량을 증가시켜야 한다.
            $new_stock_qty = $item['qty'] + $warehousing_qty;
            $data = array(
                'table' => 'wc_items',
                'where' => 'uid=' . $uid,
                'qty' => $new_stock_qty
            );
            $this->update($data);

            // wc_purchase_item 테이블에서 remain_qty 값을 감소시켜야 한다.
            $new_remain_qty = $purchase_item['remain_qty'] - $warehousing_qty;
            $data = array(
                'table' => 'wc_purchase_item',
                'where' => 'uid=' . $uid,
                'remain_qty' => $new_remain_qty
            );
            $this->update($data);

            // wc_purchase 테이블에서 전체 입고가 되었는지 확인해야 한다
            $query = "select * from wc_purchase_item where fid='{$purchase_item['fid']}' and remain_qty>0";
            $this->query($query);
            if($this->num_rows() == 0) {
                $data = array(
                    'table' => 'wc_purchase',
                    'where' => 'uid=' . $purchase_item['fid'],
                    'status' => '입고완료'
                );
                $this->update($data);
            }

            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 등록이 되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '등록 중 에러가 발생하였습니다'
            ];
        }
        echo json_encode($this->response);
    }

    //===============================================================================================================================//
    // 생산 실적 등록 
    //===============================================================================================================================//  

    public function registerProductWork() {
        $currentDate = date('Y-m-d');
        
        $fid = (empty($this->param['workOrderUid'])) ? 0 : $this->param['workOrderUid'];
        $productQty = $this->removeComma($this->param['productQty']);
        $qty = $this->removeComma($this->param['qty']);
        $defectQty = $this->removeComma($this->param['defectQty']);

        $item = $this->getData('mes_items', $this->param['item']);
        $process = $this->getData('mes_process', $this->param['process']);
        $employee = $this->getData('mes_employee', $this->param['employee']);

        // 불량 수량이 없다면
        if($defectQty == 0) {
            $defect['uid'] = 0;
            $defect['name'] = '';
        } else {
            $defect = $this->getData('mes_defect_reason', $this->param['defectReason']);
        }

        if(empty($this->param['uid'])) {
            $data = array(
                'table' => 'mes_work_report',
                'fid' => $fid,
                'itemUid' => $item['uid'],
                'name' => $item['name'],
                'code' => $item['code'],
                'standard' => $item['standard'],
                'process' => $process['uid'],
                'processName' => $process['name'],
                'productQty' => $productQty,
                'qty' => $qty,
                'defectQty' => $defectQty,
                'defectReason' => $defect['uid'],
                'defectReasonName' => $defect['name'],
                'employee' => $employee['uid'],
                'employeeName' => $employee['name'],
                'workDate' => $this->param['workDate']
            );            
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'mes_work_report',
                'where' => 'uid='.$this->param['uid'],
                'fid' => $fid,
                'itemUid' => $item['uid'],
                'name' => $item['name'],
                'code' => $item['code'],
                'standard' => $item['standard'],
                'process' => $process['uid'],
                'processName' => $process['name'],
                'productQty' => $productQty,
                'qty' => $qty,
                'defectQty' => $defectQty,
                'defectReason' => $defect['uid'],
                'defectReasonName' => $defect['name'],
                'employee' => $employee['uid'],
                'employeeName' => $employee['name'],
                'workDate' => $this->param['workDate']
            );
            $result = $this->update($data);
        }

        // 불량수량이 있다면 불량등록을 한다
        if($defectQty > 0) {
            $data = array(
                'table' => 'mes_defective_report',
                'itemName' => $item['name'],
                'itemCode' => $item['code'],
                'reason' => $defect['name'],
                'qty' => $defectQty,
                'registerDate' => $currentDate
            );
            $this->insert($data);
        }
        
        if($result) {
            $new_stock_qty = $item['stock_qty'] - $qty;

            $data = array(
                'table' => 'mes_items',
                'where' => 'uid=' . $order['item'],
                'stock_qty' => $new_stock_qty
            );
            $this->update($data);

            $this->response = [
                'result' => 'success',
                'message' => '등록 하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '생산실적 등록 중 에러가 발생하였습니다'
            ];
        }
        
        echo json_encode($this->response);
    }

    public function checkExistence($table, $field, $value) {
        $query = "select * from {$table}";
        $this->query($query);
        $data = $this->fetch();
        if($data) {
            return true;
        } else {
            return false;
        }
    }

    //===============================================================================================================================//
    // 사용자 관리
    //===============================================================================================================================//  
    public function registerUser() {
        $employee = $this->getData('mes_employee', $this->param['employee']);
        
        if(empty($this->param['uid'])) {
            // 해당 사원이 이미 등록이 되어 있는지 확인
            if(!$this->checkExistence('mes_user', 'employee', $this->param['employee'])) {
                $this->response = [
                    'result' => 'error',
                    'message' => '이미 등록된 사용자입니다'
                ];

                echo json_encode($this->response);
                exit;
            }

            $loginPwd = password_hash($this->param['loginPwd'], PASSWORD_DEFAULT);

            $data = array(
                'table' => 'mes_user',
                'employee' => $this->param['employee'],
                'employeeName' => $employee['name'],
                'loginId' => $this->param['loginId'],
                'loginPwd' => $loginPwd,
                'auth' => $this->param['auth']
            );            
            $result = $this->insert($data);
        } else {
            if(!empty($this->param['loginPwd'])) {
                $loginPwd = password_hash($this->param['loginPwd'], PASSWORD_DEFAULT);
            } else {
                $user = $this->getData('mes_user', $this->param['uid']);
                $loginPwd = $user['loginPwd'];
            }

            $data = array(
                'table' => 'mes_user',
                'where' => 'uid='.$this->param['uid'],
                'employee' => $this->param['employee'],
                'employeeName' => $employee['name'],
                'loginId' => $this->param['loginId'],
                'loginPwd' => $loginPwd,
                'auth' => $this->param['auth']
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
                'message' => '사용자 등록 중 에러가 발생하였습니다'
            ];
        }
        
        echo json_encode($this->response);        
    }

    public function getUserList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
            
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
            
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
            
        // 총 게시물 개수 가져오기
        $countQuery = "SELECT COUNT(*) as totalCount FROM mes_user {$whereClause}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
            
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_user 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
            
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'employee' => $data['employee'],
                    'employeeName' => $data['employeeName'],
                    'loginId' => $data['loginId'],
                    'auth' => $data['auth']
                ];
            }, $results)
        ];
            
        echo json_encode($this->response);
    }
    
    public function deleteUser() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from mes_user where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '사용자 삭제 중 에러가 발생하였습니다'
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

    public function getUser() {
        $uid = $this->param['uid'];
        $query = "select * from mes_user where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],               
                'employee' => $data['employee'],
                'employeeName' => $data['employeeName'],
                'loginId' => $data['loginId'],
                'loginPwd' => $data['loginPwd'],
                'auth' => $data['auth']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    //===============================================================================================================================//
    // 도면 관리
    //===============================================================================================================================//
    
    public function registerBlueprint() {
        $account = $this->getData('mes_account', $this->param['account']);

        if(empty($this->param['uid'])) {
            if(!empty($_FILES["blueprint"]["tmp_name"][0])) {
                $blueprint = $this->uploadFile('blueprint', './attach/blueprint/');
            } else {
                $blueprint = "";
            }

            $data = array(
                'table' => 'mes_blueprint',
                'account' => $account['uid'],
                'accountName' => $account['name'],
                'name' => $this->param['name'],
                'blueprint' => $blueprint
            );            
            $result = $this->insert($data);
        } else {
            if(!empty($_FILES["blueprint"]["tmp_name"][0])) {
                $blueprint = $this->uploadFile('blueprint', '../attach/blueprint/');
            } else {
                $blueprint = $this->param['oldFile'];
            }

            $data = array(
                'table' => 'mes_blueprint',
                'where' => 'uid='.$this->param['uid'],
                'account' => $account['uid'],
                'accountName' => $account['name'],
                'name' => $this->param['name'],
                'blueprint' => $blueprint
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
                'message' => '도면 등록 중 에러가 발생하였습니다'
            ];
        }
        
        echo json_encode($this->response);
    }

    public function getBlueprintList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
            
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
            
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
            
        // 총 게시물 개수 가져오기
        $countQuery = "SELECT COUNT(*) as totalCount FROM mes_blueprint {$whereClause}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
            
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_blueprint 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
            
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'account' => $data['account'],
                    'accountName' => $data['accountName'],
                    'name' => $data['name'],
                    'blueprint' => $data['blueprint']
                ];
            }, $results)
        ];
            
        echo json_encode($this->response);
    }

    public function deleteBlueprint() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from mes_blueprint where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '도면 삭제 중 에러가 발생하였습니다'
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

    public function getBlueprint() {
        $uid = $this->param['uid'];
        $query = "select * from mes_blueprint where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'account' => $data['account'],
                'accountName' => $data['accountName'],
                'name' => $data['name'],
                'blueprint' => $data['blueprint']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    //===============================================================================================================================//
    // DB 관리
    //===============================================================================================================================//
    public function getTableList() {
		$query = "SHOW TABLES";
		if(!$this->query($query)) $this->errorLog('settings', 'getTableList', $query);

		$i = 0;
		while ($row = $this->fetchArray()) {
			// 각 테이블에 있는 row 수 가져오기
			$tableName = $row[0];
			$rowCountQuery = "SELECT COUNT(*) AS row_count FROM ". $tableName;
            if(!$this->subQuery($rowCountQuery)) $this->errorLog('settings', 'getTableList', $rowCountQuery);			
			$rowCount = $this->subFetch();
			$cnt = $rowCount['row_count'];

			// 테이블의 comment 가져오기
			$commentQuery = "SHOW TABLE STATUS LIKE '" . $tableName . "'";			
            if(!$this->subQuery($commentQuery)) $this->errorLog('settings', 'getTableList', $commentQuery);
			$commentRow = $this->subFetch();
			$tableComment = $commentRow['Comment'];

			// Prepare the response data
			$response[$i]['table'] = $tableName;
			$response[$i]['cnt'] = $cnt;
			$response[$i]['comment'] = $tableComment;
			$i++;
		}

		// Output the response as JSON
		echo json_encode($response);
	}

	// 테스트용 데이터 초기화
	public function initTest() {
		$tables = array();
		array_push($tables, "mes_account");
		array_push($tables, "mes_account_classification");
		array_push($tables, "mes_blueprint");
		array_push($tables, "mes_classification");
		array_push($tables, "mes_daily_work");
		array_push($tables, "mes_defect_reason");
		array_push($tables, "mes_defective");
		array_push($tables, "mes_employee");
		array_push($tables, "mes_inspect_report");
		array_push($tables, "mes_items");
		array_push($tables, "mes_items_inout");
		array_push($tables, "mes_machine");
		array_push($tables, "mes_machine_component");
		array_push($tables, "mes_machine_inspect");
		array_push($tables, "mes_machine_spec");
		array_push($tables, "mes_orders");
		array_push($tables, "mes_process");
		array_push($tables, "mes_shipment");
		array_push($tables, "mes_shipment_order");
		array_push($tables, "mes_unit");
		array_push($tables, "mes_user");
		array_push($tables, "mes_work_order");
		array_push($tables, "mes_work_report");

		foreach($tables as $table) {
			$query = "TRUNCATE TABLE ".$table;
			if(!$this->query($query)) $this->errorLog('settings', 'initTest', $query);            
		}

		$response['status'] = 'success';
		echo json_encode($response);
	}

    public function truncateTable() {
		$table = $this->param['table'];
		$response = [];

		$query = 'TRUNCATE ' . $table;
		$result = $this->query($query);
		if($result) $response['status'] = 'success';
		else $response['status'] = 'error';

		echo json_encode($response);
	}

    // 에러를 DB에 기록한다....
    public function errorLog($controller, $method, $query) {
        $registDate = date('Y-m-d H:i:s');
        $data = array(
            'table' => 'error_log',
            'controller' => $controller,
            'method' => $method,
            'query' => $query,
            'registDate' => $registDate
        );
        $this->insert($data);
    }

    // 관리자 추가
    public function addAdmin() {
        $loginPwd = password_hash($this->param['loginPwd'], PASSWORD_DEFAULT);

        if(!$this->checkExistence('adminst', 'id', $this->param['loginId'])) {
            $data = array(
                'table' => 'adminst',
                'name' => '관리자',
                'id' => $this->param['loginId'],
                'pwd' => $loginPwd,
            );
            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'adminst',
                'where' => 'uid=1',
                'name' => '관리자',
                'id' => $this->param['loginId'],
                'pwd' => $loginPwd,
            );
            $result = $this->update($data);
        }

        if($result) {
            // 테이블이 존재할 경우
            $this->response = [
                'result' => 'success',
                'message' => '등록하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'success',
                'message' => '등록에 실패하였습니다'
            ];
        }
    
        // JSON 응답 출력
        echo json_encode($this->response);
    }

    public function getAdmin() {
        $query = "select * from adminst";
        $this->query($query);
        $data = $this->fetch();
        if($data) {
            $this->response = [
                'uid' => $data['uid'],
                'name' => $data['name'],
                'id' => $data['id'],
                'pwd' => $data['pwd']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found in the table',
            ];
        }

        echo json_encode($this->response);
    }




    // 모니터링 주간 생산량 가져오기
    public function getWeeklyProductQty() {
        // 이번 주 월요일과 일요일 날짜 계산
        $monday = date('Y-m-d', strtotime('monday this week'));
        $friday = date('Y-m-d', strtotime('friday this week'));
        
        // 주간 생산량 데이터 쿼리
        $query = "SELECT productDate, SUM(qty) as total_qty 
                FROM mes_weekly_product 
                WHERE productDate BETWEEN '$monday' AND '$friday' 
                GROUP BY productDate";

                echo $query;
        
        $this->query($query);
        
        // 요일별 생산량 배열 초기화
        $weeklyProduction = [
            "월" => 0,
            "화" => 0,
            "수" => 0,
            "목" => 0,
            "금" => 0,
            "토" => 0,
            "일" => 0
        ];
        
        // 쿼리 결과 처리
        while ($row = $this->fetch()) {
            $date = new DateTime($row['productDate']);
            $dayOfWeek = $date->format('N'); // 요일 숫자 (1: 월요일, 7: 일요일)
            
            // 요일별 데이터 매핑 배열
            $dayMapping = [
                1 => "월",
                2 => "화",
                3 => "수",
                4 => "목",
                5 => "금",
                6 => "토",
                7 => "일"
            ];
            
            if (isset($dayMapping[$dayOfWeek])) {
                $weeklyProduction[$dayMapping[$dayOfWeek]] += (int)$row['total_qty'];
            }
        }
        
        // JSON으로 출력
        echo json_encode($weeklyProduction, JSON_UNESCAPED_UNICODE);
    }


    // 전력데이터 가져오기
    public function getRealPowerData() {
        $this->response = [
            'myChart1' => rand(0, 1000000000),
            'myChart2' => rand(0, 1000000000),
            'myChart3' => rand(0, 1000000000),
            'myChart4' => rand(0, 1000000000),
            'myChart5' => rand(0, 1000000000),
            'myChart6' => rand(0, 1000000000)
        ];

        echo json_encode($this->response);
    }

    public function getPieChartData() {
        $response = [
            'labels' => ['부품 불량', '조립 불량', '포장 불량', '기타 불량'],
            'data' => [20, 15, 25, 10]
        ];
        
        echo json_encode($response);
        
    }

    // 로그인 이력
    public function getLoginReport() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
            
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
            
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
            
        // 총 게시물 개수 가져오기
        $countQuery = "SELECT COUNT(*) as totalCount FROM mes_user_login {$whereClause}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
            
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_user_login 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
            
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'registerDate' => $data['registerDate'],
                    'loginId' => $data['loginId']
                ];
            }, $results)
        ];
            
        echo json_encode($this->response);
    }

    public function getWorkOrderProgress() {
        $query = "select * from mes_work_order";
        $this->query($query);
        $results = $this->fetchAll();
    
        // 결과 데이터를 JSON 형식으로 변환
        $this->response = [
            'totalCount' => count($results),
            'data' => array_map(function($data) {

                $producedQty = $data['orderQty'] - $data['remainQty'];
                $productionRate = ($data['orderQty'] > 0) ? ($producedQty / $data['orderQty']) * 100 : 0;

                return [
                    'startDate' => $data['startDate'],
                    'endDate' => $data['endDate'],
                    'item' => $data['name'],
                    'orderQty' => $data['orderQty'],
                    'progress' => $productionRate,
                    'remainQty' => $data['remainQty']
                ];
            }, $results)
        ];
    
        header('Content-Type: application/json');
        echo json_encode($this->response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }


    // 로그인 이력 생성
    public function createLoginReport() {
        // 날짜를 구성
        $year = $this->param['year'];
        $month = $this->param['month'];
        
        // 1. 해당 월의 시작일과 다음 달의 시작일 설정
        try {
            $startOfMonth = new DateTime(sprintf('%d-%02d-01', $year, $month));
            $endOfMonth = new DateTime(sprintf('%d-%02d-01', $year, $month));
            $endOfMonth->modify('next month');
        } catch (Exception $e) {
            $this->response = [
                'result' => 'fail',
                'message' => '유효하지 않은 년도 또는 월입니다.'
            ];
            echo json_encode($this->response);
            return;
        }

        // ⭐️ 오늘 날짜를 가져옵니다. (시간 정보는 00:00:00)
        $today = new DateTime('today');
        
        // DatePeriod를 사용하여 월의 모든 날짜를 1일씩 순회하도록 설정
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startOfMonth, $interval, $endOfMonth);

        $businessDayCount = 0;
        $insertedCount = 0;

        foreach ($dateRange as $date) {
            
            // ⭐️ 추가된 로직: 현재 날짜(오늘)보다 미래인 경우 건너뛰기
            if ($date > $today) {
                continue; 
            }

            // 'N' 포맷: 월요일(1)부터 일요일(7)까지의 ISO-8601 숫자 요일
            $dayOfWeek = $date->format('N'); 
            
            // 6 (토요일) 또는 7 (일요일)이 아닌 평일(1~5)만 처리
            if ($dayOfWeek < 6) { 
                $businessDayCount++;
                
                // DATETIME 형식 및 랜덤 시간 생성 로직
                $loginDateTime = clone $date;
                
                // 8:30:00 부터 10:30:00 (120분) 사이의 랜덤 시간 설정
                $baseHour = 8;
                $baseMinute = 30;
                $maxOffsetMinutes = 120;
                $randomMinutesOffset = rand(0, $maxOffsetMinutes);
                
                $loginDateTime->setTime($baseHour, $baseMinute, 0);
                $loginDateTime->modify("+{$randomMinutesOffset} minutes");
                $loginDateTime->setTime(
                    (int)$loginDateTime->format('H'), 
                    (int)$loginDateTime->format('i'), 
                    rand(0, 59)
                );

                $formattedDateTime = $loginDateTime->format('Y-m-d H:i:s');
                
                // 삽입 데이터 구성
                $data = array(
                    'table' => 'mes_user_login',
                    'loginId' => 'admin',
                    'registerDate' => $formattedDateTime 
                );
                
                // 데이터 삽입
                $result = $this->insert($data);
                if ($result) {
                    $insertedCount++;
                }
            }
        }

        // 성공 메시지 반환
        $this->response = [
            'result' => 'success',
            'message' => "{$year}년 {$month}월 (오늘 이전)의 총 {$insertedCount}개 로그인 이력을 등록하였습니다."
        ];

        echo json_encode($this->response);
    }   
    
    public function getKpiList() {
        $query = "select * from mes_kpi";
        $this->query($query);
        $results = $this->fetchAll();
        
        // 결과 데이터를 JSON 형식으로 변환
        $this->response = [            
            'data' => array_map(function($data) {
                $year = 2024;

                //$updown = "up" 이면 현재가 목표보다 적으면 달성률은 100%미만, 반대면 100% 이상, $updown = 'down' 일 경우에는 목표보다 적으면 달성률은 100% 이상, 반대면 100% 이하 

                switch($data['classification']) {
                    case "E" : // 에너지원단위개선율
                        // 2024년도 전기세 가져오기
                        $query = "select (price1 + price2 + price3 + price4 + price5 + price6 + price7 + price8 + price9 + price10 + price11 + price12) as total from mes_month_power where year={$year}";
                        $this->query($query);
                        $result = $this->fetch();

                        $currentValue = (($result['total'] * 80) / 220500000) / 100;
                        $currentValue = number_format($currentValue, 3, '.', '');
                        $attainmentRate = $this->calculateAttainmentRate($data['targetValue'], $currentValue, "down");
                    break;

                    case "P" : // 시간당 생산량
                        $startDate = '2024-10-04';
                        $endDate = '2024-11-20';

                        $query = "select sum(qty) as totalQty from mes_weekly_product where productDate between '{$startDate}' and '{$endDate}'";
                        $this->query($query);
                        $rows = $this->getRows();
                        $productTime = $rows * 8; //총시간
                        $result = $this->fetch();

                        $currentValue = $productTime / $result['totalQty'];            
                        $currentValue = number_format($currentValue, 3, '.', '');
                        $attainmentRate = $this->calculateAttainmentRate($data['targetValue'], $currentValue, "up");
                    break;

                    case "D" : // 납기단축
                        $query = "select orderDate, shipmentDate from mes_shipment";
                        $this->query($query);
                        $results = $this->fetchAll();

                        $totalDays = 0;
                        $count = 0;

                        foreach ($results as $row) {
                            // orderDate와 shipmentDate를 DateTime 객체로 변환
                            $orderDate = new DateTime($row['orderDate']);
                            $shipmentDate = new DateTime($row['shipmentDate']);

                            // 날짜 차이 계산
                            $interval = $orderDate->diff($shipmentDate);

                            // 총 소요 일수 합산
                            $totalDays += $interval->days;
                            $count++;
                        }

                        // 평균 계산
                        $averageDays = $count > 0 ? round($totalDays / $count, 2) : 0;

                        $currentValue = $averageDays;
                        $attainmentRate = $this->calculateAttainmentRate($data['targetValue'], $currentValue, "down");
                    break;
                }

                return [
                    'uid' => $data['uid'],
                    'classification' => $data['classification'],
                    'indicator' => $data['indicator'],
                    'unit' => $data['unit'],
                    'pastValue' => $data['pastValue'],
                    'targetValue' => $data['targetValue'],
                    'currentValue' => $currentValue,
                    'attainmentRate' => $attainmentRate
                ];
            }, $results)
        ];
    
        header('Content-Type: application/json');
        echo json_encode($this->response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function calculateAttainmentRate($currentValue, $targetValue, $updown) {
        if ($updown === "down") {
            // "up"일 때: 목표보다 현재 값이 적으면 100% 미만, 반대면 100% 이상
            $attainmentRate = ($currentValue / $targetValue) * 100;
        } elseif ($updown === "up") {
            // "down"일 때: 목표보다 현재 값이 적으면 100% 이상, 반대면 100% 이하
            $attainmentRate = ($targetValue / $currentValue) * 100;
        } else {
            return "Invalid updown value. Must be 'up' or 'down'.";
        }
    
        return round($attainmentRate, 3); // 소수점 2자리 반올림
    }

    public function calculateDaysBetween($orderDate, $shipmentDate) {
        // 날짜를 DateTime 객체로 변환
        $orderDateObj = new DateTime($orderDate);
        $shipmentDateObj = new DateTime($shipmentDate);
    
        // 날짜 차이 계산
        $interval = $orderDateObj->diff($shipmentDateObj);
    
        // 차이 일수 반환
        return $interval->days;
    }

    // KPI지수 등록
    public function registerKpiValue() {
        $uid = isset($this->param['uid']) ? (int)$this->param['uid'] : 0;
        $classification = addslashes($this->param['classification']);
        $indicator = addslashes($this->param['indicator']);
        $unit = addslashes($this->param['unit']);
        $pastValue = $this->param['pastValue'];
        $targetValue = $this->param['targetValue'];
    
        // 입력값 검증
        if (!is_numeric($pastValue) || !is_numeric($targetValue) || (!empty($uid) && !is_numeric($uid))) {
            $this->response = [
                'result' => 'error',
                'message' => '잘못된 입력값입니다.'
            ];
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($this->response);
            return;
        }
    
        // SQL 쿼리 실행
        if (empty($uid)) {
            $query = "INSERT INTO mes_kpi (classification, indicator, unit, pastValue, targetValue) 
                      VALUES ('{$classification}', '{$indicator}', '{$unit}', {$pastValue}, {$targetValue})";
        } else {
            $query = "UPDATE mes_kpi 
                      SET classification='{$classification}', indicator='{$indicator}', unit='{$unit}', 
                          pastValue={$pastValue}, targetValue={$targetValue} 
                      WHERE uid={$uid}";
        }
    
        $result = $this->query($query);
    
        // 응답 처리
        if ($result) {
            $this->response = [
                'result' => 'success',
                'message' => '등록하였습니다.'
            ];
        } else {
            error_log("SQL Error: {$query}");
            $this->response = [
                'result' => 'error',
                'message' => '등록에 실패하였습니다.'
            ];
        }
    
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->response);
    }

    public function getKpi() {
        $uid = $this->param['uid'];
        $query = "select * from mes_kpi where uid={$uid}";        
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],
                'classification' => $data['classification'],
                'indicator' => $data['indicator'],
                'unit' => $data['unit'],
                'pastValue' => $data['pastValue'],
                'targetValue' => $data['targetValue']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }
    
    public function deleteKpi() {
        $uid = $this->param['uid'];

        if($uid) {
            $query = "delete from mes_kpi where uid={$uid}";
            if($this->query($query)) {
                $this->response = [
                    'result' => 'success',
                    'message' => '정상적으로 삭제하였습니다'
                ];
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => 'KPI 삭제 중 에러가 발생하였습니다'
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

    //====================================================================
    // BOM
    //====================================================================
    public function checkBom() {
        $uid = $this->param['uid'];

        $query = "select * from mes_bom where gid={$uid} and item_uid={$uid}";
        $this->query($query);
        if($this->getRows() > 0) {
            $this->response = [
                'result' => 'error'
            ];
        } else {
            $this->response = [
                'result' => 'success'
            ];
        }

        echo json_encode($this->response);
    }

    public function registerBasicBom() {
        $uid = $this->param['uid'];

        $query = "select * from mes_items where uid={$uid}";
        $this->query($query);
        $item = $this->fetch();

        // 자신의 bom이 등록이 되어있는지 확인
        $query = "select * from mes_bom where gid={$uid} and item_uid={$uid}";
        $this->query($query);
        if($this->getRows() < 1) {

            $data = array(
                'table' => 'mes_bom',
                'gid' => $uid,
                'fid' => $uid,
                'depth' => 1,
                'classification' => $item['classification'],
                'item_uid' => $uid,
                'item_code' => $item['item_code'],
                'item_name' => $item['item_name'],
                'standard' => $item['standard'],
                'unit' => $item['unit'],
                'qty' => 1
            );

            $result = $this->insert($data);

            if(!$result) {
                $this->response = [
                    'result' => 'error',
                    'message' => '등록에 실패하였습니다'
                ];            
            } else {
                $this->response = [
                    'result' => 'success',
                    'message' => '등록하였습니다'
                ];
            }
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '이미 BOM에 등록이 되어있습니다'
            ];
        }        

        echo json_encode($this->response);
    }

    public function getBomTree() {
        $uid = $this->param['uid'];
    
        // 최상위 BOM 항목 가져오기 (gid가 자신의 uid인 경우)
        $query = "SELECT * FROM mes_bom WHERE gid={$uid} AND item_uid={$uid}";
        $this->query($query);
        $result = $this->fetch();

        $re[0]['uid'] = $result['uid'];
		$re[0]['gid'] = $result['gid'];
		$re[0]['fid'] = $result['fid'];
		$re[0]['depth'] = $result['depth'];
		$re[0]['classification'] = $result['classification'];
		$re[0]['item_uid'] = $result['item_uid'];
		$re[0]['item_code'] = $result['item_code'];
		$re[0]['item_name'] = $result['item_name'];
		$re[0]['standard'] = $result['standard'];
		$re[0]['unit'] = $result['unit'];
		$re[0]['qty'] = $result['qty'];
        
        $query = "select * from mes_bom where gid={$uid} and fid={$result['uid']}";        
        $this->query($query);

        $i = 1;
		while($data = $this->fetch()) {       
			$re[$i]['uid'] = $data['uid'];
			$re[$i]['gid'] = $data['gid'];
			$re[$i]['fid'] = $data['fid'];
			$re[$i]['depth'] = $data['depth'];
			$re[$i]['classification'] = $data['classification'];
			$re[$i]['item_uid'] = $data['item_uid'];
			$re[$i]['item_code'] = $data['item_code'];
			$re[$i]['item_name'] = $data['item_name'];
			$re[$i]['standard'] = $data['standard'];
			$re[$i]['unit'] = $data['unit'];
			$re[$i]['qty'] = $data['qty'];


            $subQuery = "select * from mes_bom where gid={$uid} and fid={$data['uid']}";                   
            $this->subQuery($subQuery);

            $k = $i + 1;

            while($item = $this->subFetch()) {       
                $re[$k]['uid'] = $item['uid'];
                $re[$k]['gid'] = $item['gid'];
                $re[$k]['fid'] = $item['fid'];
                $re[$k]['depth'] = $item['depth'];
                $re[$k]['classification'] = $item['classification'];
                $re[$k]['item_uid'] = $item['item_uid'];
                $re[$k]['item_code'] = $item['item_code'];
                $re[$k]['item_name'] = $item['item_name'];
                $re[$k]['standard'] = $item['standard'];
                $re[$k]['unit'] = $item['unit'];
                $re[$k]['qty'] = $item['qty'];

                $k++;     
                $i++;           
            }

			$i++;
		}

		header('Content-Type: application/json');
        echo json_encode($re, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function addBom() {
        $uid = $this->param['uid']; // 그룹아이디
        $gid = $this->param['gid']; // 그룹아이디
        $itemUid = $this->param['itemUid']; // 등록할 품목 uid
        $quantity = $this->param['quantity']; // 소요량
        $fid = $this->param['fid']; // 등록할 품목의 부모 품목 uid
        $depth = $this->param['depth'] + 1; // 등록할 품목의 부모 Depth...여기에 1을 더해야 한다        
        
        $query = "select * from mes_items where uid={$itemUid}";
        $this->query($query);
        $item = $this->fetch();

        $data = array(
            'table' => 'mes_bom',
            'gid' => $uid,
            'fid' => $fid,
            'depth' => $depth,
            'classification' => $item['classification'],
            'item_uid' => $itemUid,
            'item_code' => $item['item_code'],
            'item_name' => $item['item_name'],
            'standard' => $item['standard'],
            'unit' => $item['unit'],
            'qty' => $quantity,
        );

        $result = $this->insert($data);

        if(!$result) {
            $this->response = [
                'result' => 'error',
                'message' => '등록에 실패하였습니다'
            ];            
        } else {
            $this->response = [
                'result' => 'success',
                'message' => '등록하였습니다'
            ];  
        }

        echo json_encode($this->response);
    }

    public function deleteBom() {
        $uid = $this->param['uid'];

        // 하위 부품이 있는지 검사
        $query = "select uid from mes_bom where fid={$uid}";
        $this->query($query);
        if($this->getRows() > 0) {
            $this->stop('error', '하위 부품을 먼저 삭제하세요');
        }

        $query = "delete from mes_bom where uid={$uid}";
        $result = $this->query($query);

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '삭제하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'BOM삭제에 실패하였습니다'
            ];
        }

        echo json_encode($this->response);
    }

    
    public function getItemBomList() {        
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';
    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 총 게시물 개수 가져오기
        $countQuery = "SELECT COUNT(*) as totalCount FROM mes_items {$whereClause}";
        $this->query($countQuery);
        $totalCount = $this->fetch();
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_items 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();
    
        $this->response = [
            'totalCount' => $totalCount['totalCount'],
            'data' => array_map(function($data) {
                $query = "select uid from mes_bom where gid={$data['uid']}";
                $this->query($query);
                if($this->getRows() > 1) {
                    $bom = 'Y';
                } else {
                    $bom = 'N';
                }

                return [
                    'uid' => $data['uid'],
                    'classification' => $data['classification'],
                    'item_code' => $data['item_code'],
                    'item_name' => $data['item_name'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'bom' => $bom
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    }


    // 환경설정 값을 등록한다
    public function registerSetting() {
        if(isset($this->param['enableBom'])) $enableBom = $this->param['enableBom'];
        else $enableBom = 'N';

        if(isset($this->param['minusStockCount'])) $minusStockCount = $this->param['enableBom'];
        else $minusStockCount = 'N';

        $value = $this->checkTable('mes_setting');

        if(!$value) {
            $data = array(
                'table' => 'mes_setting',
                'enableBom' => $enableBom,
                'minusStockCount' => $minusStockCount
            );

            $result = $this->insert($data);
        } else {
            $data = array(
                'table' => 'mes_setting',
                'where' => 'uid=' . $value,
                'enableBom' => $enableBom,
                'minusStockCount' => $minusStockCount
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
                'message' => '등록에 실패하였습니다'
            ];
        }

        echo json_encode($this->response);
        
    }

    public function getSettingFetch() {
        $query = "select * from mes_setting";
        $this->query($query);

        $data = $this->fetch();
        return $data;
    }

    public function getSetting() {        
        $query = "select * from mes_setting";
        $data = $this->queryFetch($query);

        if ($data) {
            $this->response = [
                'uid' => $data['uid'],               
                'enableBom' => $data['enableBom'],
                'minusStockCount' => $data['minusStockCount']
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => 'No data found',
            ];
        }

        echo json_encode($this->response);
    }

    public function getReportProduct() {
        // -----------------------------------------------------------
        // 2. 데이터 조회 및 JSON 구조 생성
        // -----------------------------------------------------------

        $output = [
            'kpi' => [],
            'daily_data' => [],
            'item_ratio_data' => [],
            'detail_data' => []
        ];

        // 2-1. KPI 데이터 조회 및 계산
        // 총 생산량, 총 작업 건수 등을 조회합니다.
        $sql_kpi = "select sum(test_qty) as total_qty, sum(suitable_qty) as passed_qty, sum(unsuitable_qty) as failed_qty from mes_test_result";
        $this->query($sql_kpi);
        $kpi = $this->fetch();

        if ($kpi) {
            $total_qty = (int)$kpi['total_qty'];
            $passed_qty = (int)$kpi['passed_qty'];
            $avg_quality_rate = $total_qty > 0 ? round(($passed_qty / $total_qty) * 100, 1) : 0;
            
            // 최다 생산 품목 조회
            $sql_top_item = "
                SELECT item_name 
                FROM mes_daily_work 
                GROUP BY item_name 
                ORDER BY SUM(work_qty) DESC 
                LIMIT 1;
            ";
            $this->query($sql_top_item);
            $top_item_row = $this->fetch();
            $top_item = $top_item_row ? $top_item_row['item_name'] : 'N/A';
            
            $output['kpi'] = [
                'totalQty' => $total_qty,
                'avgQualityRate' => $avg_quality_rate,
                'topItem' => $top_item
            ];
        }
        // 2-2. 일별 생산 추이 (라인 차트용)
        $sql_daily = "
            SELECT 
                DATE_FORMAT(work_date, '%Y-%m-%d') AS work_date, 
                SUM(work_qty) AS total_qty
            FROM 
                mes_daily_work
            GROUP BY 
                work_date
            ORDER BY 
                work_date;
        ";
        $this->query($sql_daily);
        while ($row = $this->fetch()) {
            $output['daily_data'][] = [
                'work_date' => $row['work_date'],
                'total_qty' => (int)$row['total_qty']
            ];
        }

        // 2-3. 품목별 생산 비중 (도넛 차트용)
        $sql_ratio = "
            SELECT 
                item_name, 
                SUM(work_qty) AS qty
            FROM 
                mes_daily_work
            GROUP BY 
                item_name
            ORDER BY 
                qty DESC;
        ";
        $this->query($sql_ratio);
        while ($row = $this->fetch()) {
            $output['item_ratio_data'][] = [
                'item_name' => $row['item_name'],
                'qty' => (int)$row['qty']
            ];
        }


        // 2-4. 상세 내역 (테이블용)
        $sql_detail = "
            SELECT 
                work_date, item_name, worker, work_qty, quality_status 
            FROM 
                mes_daily_work
            ORDER BY 
                work_date DESC, uid DESC
            LIMIT 100; -- 최근 100건만 가져오는 예시 (필요에 따라 LIMIT 조정 또는 제거)
        ";
        $this->query($sql_detail);
        while ($row = $this->fetch()) {
            $output['detail_data'][] = [
                'work_date' => $row['work_date'],
                'item_name' => $row['item_name'],
                'worker' => $row['worker'],
                'work_qty' => (int)$row['work_qty'],
                'quality_status' => $row['quality_status']
            ];
        }

        echo json_encode($output);
    }

    // 수입검사 데이터 조회
    public function getImportInspection() {
        $output = [
            'kpi' => [],
            'overall_results' => [],
            'monthly_ng_trend' => [],
            'detail_data' => []
        ];
        
        // -----------------------------------------------------------
        // 2. KPI 데이터 조회
        // -----------------------------------------------------------
        // 총 검사 건수, 합격률, 최다 불합격 품목 조회
        $sql_kpi = "
            SELECT 
                COUNT(uid) AS total_inspections,
                SUM(CASE WHEN inspection_result = 'OK' THEN 1 ELSE 0 END) AS ok_count,
                SUM(CASE WHEN inspection_result = 'NG' THEN 1 ELSE 0 END) AS ng_count
            FROM 
                mes_incoming_inspection;
        ";
        $result_kpi = $this->query($sql_kpi);
        $kpi = $this->fetch();
        
        if ($kpi) {
            $total_inspections = (int)$kpi['total_inspections'];
            $ok_count = (int)$kpi['ok_count'];
            $overall_ok_rate = $total_inspections > 0 ? round(($ok_count / $total_inspections) * 100, 1) : 0;

            // 최다 불합격 품목 조회 (NG가 실제로 있는지 먼저 확인)
            $most_ng_item = '-';
            if ($total_inspections > 0 && (int)$kpi['ng_count'] > 0) {
                $sql_top_ng_item = "
                    SELECT 
                        item_name
                    FROM 
                        mes_incoming_inspection
                    WHERE
                        inspection_result = 'NG'
                        AND item_name IS NOT NULL AND item_name <> ''
                    GROUP BY 
                        item_name
                    ORDER BY 
                        COUNT(uid) DESC 
                    LIMIT 1;
                ";
                $result_top_ng = $this->query($sql_top_ng_item);
                $top_ng_item_row = $this->fetch();
                if ($top_ng_item_row && $top_ng_item_row['item_name'] !== null && trim($top_ng_item_row['item_name']) !== '') {
                    $most_ng_item = $top_ng_item_row['item_name'];
                }
            }
            
            $output['kpi'] = [
                'totalInspections' => $total_inspections,
                'overallOkRate' => $overall_ok_rate,
                'mostNgItem' => $most_ng_item
            ];
        }
        
        
        // -----------------------------------------------------------
        // 3. 종합 검사 결과 비중 (차트 1)
        // -----------------------------------------------------------
        $sql_overall = "
            SELECT 
                inspection_result,
                COUNT(uid) AS count
            FROM 
                mes_incoming_inspection
            GROUP BY 
                inspection_result
            ORDER BY 
                inspection_result DESC;
        ";
        $result_overall = $this->query($sql_overall);
        while ($row = $this->fetch()) {
            $output['overall_results'][] = [
                'inspection_result' => $row['inspection_result'],
                'count' => (int)$row['count']
            ];
        }
        
        // -----------------------------------------------------------
        // 4. 기간별 불합격 추이 (차트 2)
        // -----------------------------------------------------------
        $sql_monthly_ng = "
            SELECT 
                DATE_FORMAT(inspection_date, '%Y-%m') AS month, 
                SUM(CASE WHEN inspection_result = 'NG' THEN 1 ELSE 0 END) AS ng_count
            FROM 
                mes_incoming_inspection
            GROUP BY 
                month
            ORDER BY 
                month DESC
            LIMIT 6; -- 최근 6개월만 조회
        ";
        $result_monthly_ng = $this->query($sql_monthly_ng);
        $trend_data = [];
        while ($row = $this->fetch()) {
            $trend_data[] = [
                'month' => $row['month'],
                'ng_count' => (int)$row['ng_count']
            ];
        }
        // 차트에서 시간 순서대로 보이기 위해 배열 순서 뒤집기
        $output['monthly_ng_trend'] = array_reverse($trend_data);
        
        
        // -----------------------------------------------------------
        // 5. 상세 검사 내역 (테이블)
        // -----------------------------------------------------------
        $sql_detail = "
            SELECT 
                inspection_date, item_name, item_code, in_qty, 
                appearance_check, function_check, inspection_result, inspector_name
            FROM 
                mes_incoming_inspection
            ORDER BY 
                inspection_date DESC, uid DESC
            LIMIT 200;
        ";
        $result_detail = $this->query($sql_detail);
        while ($row = $this->fetch()) {
            $output['detail_data'][] = $row;
        }
        
        // -----------------------------------------------------------
        // 6. JSON 응답 출력
        // -----------------------------------------------------------        
        echo json_encode($output);
    }

    // 수입검사 데이터 삭제
    public function deleteInspection() {
        $uid = $this->param['uid'];
        $query = "delete from mes_incoming_inspection where uid={$uid}";
        $result = $this->query($query);
        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 삭제되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '삭제에 실패하였습니다'
            ];
        }
        echo json_encode($this->response);
    }

    // 수입검사 리스트
    public function getTestResultList() {        
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_test_result 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";             
        $this->query($query);
        $results = $this->fetchAll();
    
        // 응답 생성
        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'work_order_uid' => $data['work_order_uid'],                    
                    'test_date' => $data['test_date'],
                    'item_uid' => $data['item_uid'],
                    'item_name' => $data['item_name'],
                    'item_code' => $data['item_code'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'test_qty' => $data['test_qty'],
                    'suitable_qty' => $data['suitable_qty'],
                    'unsuitable_qty' => $data['unsuitable_qty'],
                    'rework_order_uid' => $data['rework_order_uid']                    
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    } 

    // 누전검사 등록
    public function registerShortCircuitTest() { 
        $defect_name = $this->param['defect_name'];
        $defect_qty = $this->param['defect_qty'];
        $current_date = date('Y-m-d');       
        $work_order_uid = $this->param['uid'];
        $test_date = $this->param['test_date'];
        $suitable_qty = $this->param['suitable_qty'];
        $unsuitable_qty = $this->param['unsuitable_qty'];
        $test_qty = $suitable_qty + $unsuitable_qty;

        $query = "select * from mes_daily_work where uid={$work_order_uid}";
        $this->query($query);
        $daily_work = $this->fetch();

        // 부적합 수량이 있다면
        if($unsuitable_qty > 0) {
            $data = array(
                'table' => 'mes_work_order',                
                'classification' => '재작업',
                'order_uid' => 0,
                'account_uid' => 0,
                'account_name' => '',
                'item_uid' => $daily_work['item_uid'],
                'item_name' => $daily_work['item_name'],
                'item_code' => $daily_work['item_code'],
                'standard' => $daily_work['standard'],
                'unit' => $daily_work['unit'],
                'order_date' => $current_date,
                'order_qty' => $unsuitable_qty,
                'work_qty' => 0,
                'pass_qty' => 0,
                'fail_qty' => 0,
                'quality_qty' => 0,
                'remain_qty' => $unsuitable_qty,                
                'work_start_time' => '0000-00-00 00:00:00',
                'work_end_time' => '0000-00-00 00:00:00',
                'status' => '생산대기',
                'register_date' => $current_date                
            );
            $work_order_result = $this->insert($data);

            if($work_order_result) {
                $rework_order_uid = $this->getUid();

                // 재작업 등록 리포트
                $data = array(
                    'table' => 'mes_rework_report',
                    'daily_work_uid' => $work_order_uid,
                    'test_date' => $test_date,
                    'test_result_uid' => 0,
                    'test_qty' => $test_qty,
                    'suitable_qty' => $suitable_qty,
                    'unsuitable_qty' => $unsuitable_qty,
                    'rework_order_uid' => $rework_order_uid,
                    'item_uid' => $daily_work['item_uid'],
                    'item_name' => $daily_work['item_name'],
                    'item_code' => $daily_work['item_code'],
                    'standard' => $daily_work['standard'],
                    'unit' => $daily_work['unit'],
                    'created_dt' => $current_date
                );
                $rework_report_result = $this->insert($data);
                $report_uid = $this->getUid();
            } else {
                $this->response = [
                    'result' => 'error',
                    'message' => '재작업 등록에 실패하였습니다'
                ];
                echo json_encode($this->response);
                exit;
            }
        } else {
            $rework_order_uid = 0;
            $report_uid = 0;
        }

        // 자재수불부
        $data = array(
            'table' => 'mes_items_inout',
            'classification' => '생산입고',
            'item_uid' => $daily_work['item_uid'],
            'item_name' => $daily_work['item_name'],
            'item_code' => $daily_work['item_code'],
            'standard' => $daily_work['standard'],
            'unit' => $daily_work['unit'],
            'qty' => $suitable_qty,
            'register_date' => $current_date
        );
        $items_inout_result = $this->insert($data);

        if(!$items_inout_result) {
            $this->response = [
                'result' => 'error',
                'message' => '생산입고 등록에 실패하였습니다'
            ];
            echo json_encode($this->response);
            exit;
        }

        // 재고수량 변경
        $this->updateStockQty($daily_work['item_uid'], $suitable_qty, 'plus');

        // daily_work 테이블의 quality_status 를 '불량'로 변경
        $data = array(
            'table' => 'mes_daily_work',
            'where' => 'uid=' . $work_order_uid,
            'quality_status' => '품질검사완료'
        );
        $daily_work_result = $this->update($data);
        if(!$daily_work_result) {
            $this->response = [
                'result' => 'error',
                'message' => 'daily_work 테이블의 quality_status 변경에 실패하였습니다'
            ];
            echo json_encode($this->response);
            exit;
        }

        $data = array(
            'table' => 'mes_test_result',
            'work_order_uid' => $work_order_uid,            
            'test_date' => $test_date,
            'item_uid' => $daily_work['item_uid'],
            'item_name' => $daily_work['item_name'],
            'item_code' => $daily_work['item_code'],
            'standard' => $daily_work['standard'],
            'unit' => $daily_work['unit'],
            'test_qty' => $test_qty,
            'suitable_qty' => $suitable_qty,
            'unsuitable_qty' => $unsuitable_qty,
            'rework_order_uid' => $rework_order_uid
        );
        $test_result_result = $this->insert($data);
        $result_uid = $this->getUid();

        // 불량이 있다면 불량통계를 위해 data를 insert
        foreach($defect_name as $key => $val) {
            $data = array(
                'table' => 'mes_defective_report',
                'fid' => $result_uid,
                'item_name' => $daily_work['item_name'],
                'item_code' => $daily_work['item_code'],
                'standard' => $daily_work['standard'],
                'reason' => $defect_name[$key],
                'qty' => $defect_qty[$key],
                'created_dt' => $current_date
            );
            $this->insert($data);
        }

        if($test_result_result) {
            $data = array(
                'table' => 'mes_rework_report',
                'where' => 'uid=' . $report_uid,
                'test_result_uid' => $result_uid
            );
            $this->update($data);

            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 등록되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '등록에 실패하였습니다'
            ];
        }
        echo json_encode($this->response);
    }

    // 리스트 데이터 조회
    public function getReworkList() {        
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;
    
        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';    
        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';
    
        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_rework_report 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";             
        $this->query($query);
        $results = $this->fetchAll();
    
        // 응답 생성
        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'daily_work_uid' => $data['daily_work_uid'],                    
                    'test_date' => $data['test_date'],
                    'test_result_uid' => $data['test_result_uid'],
                    'test_qty' => $data['test_qty'],
                    'suitable_qty' => $data['suitable_qty'],
                    'unsuitable_qty' => $data['unsuitable_qty'],
                    'rework_order_uid' => $data['rework_order_uid'],
                    'item_uid' => $data['item_uid'],
                    'item_name' => $data['item_name'],
                    'item_code' => $data['item_code'],
                    'standard' => $data['standard'],
                    'unit' => $data['unit'],
                    'created_dt' => $data['created_dt']
                ];
            }, $results)
        ];
    
        echo json_encode($this->response);
    } 

    // 최고 관리자용 강제 재고 마감
    public function adminRegisterStockClose() {
        $year = isset($this->param['year']) ? $this->param['year'] : date('Y');
        $month = isset($this->param['month']) ? sprintf('%02d', $this->param['month']) : date('m');
        $date_string = $year . '-' . $month . '-28';
        $close_amount = (isset($this->param['close_amount'])) ? $this->removeComma($this->param['close_amount']) : 0;

        $data = array(
            'table' => 'mes_stock_close',
            'close_date' => $date_string,
            'close_amount' => $close_amount
        );
        $result = $this->insert($data);

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 마감되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '마감에 실패하였습니다'
            ];
        }
        echo json_encode($this->response);
    }

    // 재고 마감
    public function registerStockClose() {
        $date_string = $date('Y') . '-' . $date('m') . '-28';

        $query = "select sum(stock_qty * price) as total_amount from mes_items";
        $this->query($query);
        $totalAmount = $this->fetch();

        $data = array(
            'table' => 'mes_stock_close',
            'close_date' => $date_string,
            'close_amount' => $totalAmount['total_amount']
        );
        $result = $this->insert($data);

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '정상적으로 마감되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '마감에 실패하였습니다'
            ];
        }
        echo json_encode($this->response);
    }

    // KPI
    public function getShipmentStat() {
        // 전체 납기 건수
        $query = "select count(*) as total_shipments from mes_order_items";
        $this->query($query);
        $results = $this->fetch();
        $total_shipments = $results['total_shipments'];

        // 정시 납기
        
        // 1. 정시 납기 건수 계산 (납기예정일(shipment_date) >= 실제납기일(delivery_date) 이거나 같을 때만)
        $query = "
            SELECT COUNT(*) AS on_time_shipments
            FROM mes_order_items o
            INNER JOIN mes_delivery_report d ON d.order_uid = o.fid
            WHERE o.shipment_status = '출하완료'
            AND d.delivery_date IS NOT NULL                       
            AND (DATE(d.delivery_date) <= DATE(o.shipment_date));
        ";
        $this->query($query);
        $results = $this->fetch();
        $on_time_shipments = $results['on_time_shipments'];

        // 2. 평균 납기일(지연일) 계산 (지연일수 = 실제납기일 - 납기예정일, 일 단위)
        $query = "
            SELECT AVG(DATEDIFF(d.delivery_date, o.order_date)) AS avg_delay
            FROM mes_order_items o
            LEFT JOIN mes_delivery_report d ON d.order_uid = o.fid
            WHERE o.shipment_status = '출하완료'
              AND d.delivery_date IS NOT NULL
        ";
        $this->query($query);
        $results = $this->fetch();
        $avg_delay = !is_null($results['avg_delay']) ? round($results['avg_delay'], 1) : 0;

        // 3. 납기 준수율 = (정시 납기 건수 / 전체 납기 건수) * 100
        $compliance_rate = $total_shipments > 0 ? round(($on_time_shipments / $total_shipments) * 100, 1) : 0;

        $this->response = [
            'result'            => 'success',
            'total_shipments'   => (int)$total_shipments,
            'on_time_shipments' => (int)$on_time_shipments,
            'compliance_rate'   => $compliance_rate,
            'avg_delay_days'    => $avg_delay
        ];

        echo json_encode($this->response);

    }

    // 납기 준수율 페이지에서 사용될 상세 납기 데이터
    public function getShipmentStatDetail() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;

        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';

        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';

        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_order_items 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();

        $this->response = [
            'data' => array_map(function($data) {
                // all_delivery 테이블에서 해당 주문의 delivery_date 구하기
                $order_uid = $data['uid'];
                $delivery_date = null;
                $delivery_days = null;
                $delivery = null;

                // DB 인스턴스를 직접 사용 (이 컨트롤러에서 $this가 이미 DB를 상속받는다고 가정)
                $query = "SELECT delivery_date FROM mes_delivery_report WHERE order_uid = '{$order_uid}' LIMIT 1";
                $this->query($query);
                $delivery = $this->fetch();
                if ($delivery && !empty($delivery['delivery_date'])) {
                    $delivery_date = $delivery['delivery_date'];
                }

                // 주문일과 납품일이 둘 다 존재할 때 소요납기일(일 수) 계산
                if (!empty($data['order_date']) && !empty($delivery_date)) {
                    // 날짜 포맷이 Y-m-d(혹은 Y-m-d H:i:s)라면 strtotime 사용 가능
                    $orderDateObj = new DateTime($data['order_date']);
                    $deliveryDateObj = new DateTime($delivery_date);
                    $interval = $orderDateObj->diff($deliveryDateObj);
                    $delivery_days = $interval->days;
                }

                return [
                    'uid' => $data['uid'],
                    'account_name' => $data['account_name'],
                    'item_name' => $data['item_name'],
                    'qty' => $data['qty'],
                    'shipment_status' => $data['shipment_status'],
                    'order_date' => $data['order_date'],
                    'shipment_date' => $data['shipment_date'],
                    'delivery_date' => $this->convertNull($delivery_date),
                    'delivery_days' => $this->convertNull($delivery_days)
                ];
            }, $results)
        ];

        echo json_encode($this->response);
    }

    // 불량률 통계 페이지의 카드용 데이터 가져오기
    public function getDefectStat() {        
        // 총생산량
        $query = "select sum(work_qty) as total_qty from mes_daily_work";
        $this->query($query);
        $results = $this->fetch();
        $total_qty = $results['total_qty'];

        // 전체 불량수
        $query = "select sum(qty) as total_defect_qty from mes_defective_report";
        $this->query($query);
        $results = $this->fetch();
        $total_defect_qty = isset($results['total_defect_qty']) && $results['total_defect_qty'] !== null ? $results['total_defect_qty'] : 0;

        // 불량률률
        $defect_rate = $total_defect_qty / $total_qty * 100;

        // 양품률
        $good_rate = ($total_qty - $total_defect_qty) / $total_qty * 100;

        $this->response = [
            'result' => 'success',
            'total_qty' => $total_qty,
            'total_defect_qty' => $total_defect_qty,
            'defect_rate' => $defect_rate,
            'good_rate' => $good_rate,
        ];
        echo json_encode($this->response);
    }

    // 해당년도 불량률 추이 데이터 가져오기
    public function getMonthlyDefectStat() {                
        // 해당년도 불량률 추이 데이터 가져오기
        // 월별 불량수/생산수/불량률 등을 구해 return
        $monthly_data = [];
        $year = date('Y');
        
        // 1월 ~ 12월 루프
        for ($month = 1; $month <= 12; $month++) {
            $month_str = sprintf('%02d', $month);
            $start_date = "{$year}-{$month_str}-01";
            $end_date = date("Y-m-t", strtotime($start_date)); // 마지막 날짜

            // 불량 수량
            $query = "
                SELECT 
                    IFNULL(SUM(qty),0) AS defects
                FROM mes_defective_report
                WHERE created_dt BETWEEN '{$start_date}' AND '{$end_date}'
            ";
            $this->query($query);
            $result_defects = $this->fetch();
            $defects = intval($result_defects['defects']);

            $monthly_data[] = [
                'month' => "{$year}-{$month_str}",
                'defects' => $defects
            ];
        }

        $this->response = [
            'result' => 'success',
            'monthly' => $monthly_data
        ];
        echo json_encode($this->response);
    }

    // 불량 유형별 분포 데이터 가져오기
    public function getDefectTypeStat() {
        $loginId = $_SESSION['loginId'];

        // 불량 유형별 분포 데이터 집계 (파이차트용: 불량 유형/라벨, 카운트)
        $query = "
            SELECT 
                reason AS type,
                COUNT(*) as count
            FROM mes_defective_report
            GROUP BY reason
        ";
        $this->query($query);
        $results = $this->fetchAll();

        // 결과가 [{type: '이물질', count: 11}, ...] 형태가 되도록 구성
        $pieData = [];
        foreach ($results as $row) {
            $pieData[] = [
                'type' => $row['type'],
                'count' => intval($row['count']),
            ];
        }

        // 유형별 row 수 출력용: 각 유형명과 count를 리스트로 추가
        $typeRowCountList = [];
        foreach ($pieData as $item) {
            $typeRowCountList[] = [
                'type' => $item['type'],
                'row_count' => $item['count']
            ];
        }

        $this->response = [
            'result' => 'success',
            'defectTypes' => $pieData,
            'typeRowCountList' => $typeRowCountList // 유형명과 row 수 출력
        ];
        echo json_encode($this->response);
    }

    // 불량 상세 데이터
    public function getDefectStatDetail() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;

        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';

        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';

        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_defective_report 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();

        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'item_code' => $data['item_code'],
                    'item_name' => $data['item_name'],
                    'standard' => $data['standard'],
                    'reason' => $data['reason'],
                    'qty' => $data['qty'],
                    'created_dt' => $data['created_dt'],
                ];
            }, $results)
        ];

        echo json_encode($this->response);
    }


    // 월별 생산량 가져오기
    public function getMonthlyData() {
        // 해당년도 각 월별 데이터 반환
        $year = !empty($this->param['year']) ? intval($this->param['year']) : intval(date('Y'));        

        // 해당 년도의 1월~12월 각 월별 생산량 및 집계
        $query = "
            SELECT 
                DATE_FORMAT(work_date, '%Y-%m') AS month,
                IFNULL(SUM(work_qty), 0) AS total_quantity,
                COUNT(*) AS total_orders
            FROM 
                mes_daily_work
            WHERE 
                work_date >= '{$year}-01-01' AND work_date <= '{$year}-12-31'
            GROUP BY 
                month
            ORDER BY 
                month ASC
        ";
        $this->query($query);
        $results = $this->fetchAll();

        // 월별 데이터(누락 월은 0)로 가공하여 1~12월 모두 반환
        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $key = sprintf('%04d-%02d', $year, $m);
            $monthly[$key] = [
                'month' => $key,
                'total_quantity' => 0,
                'total_orders' => 0
            ];
        }
        foreach ($results as $row) {
            $key = $row['month'];
            $monthly[$key] = [
                'month' => $key,
                'total_quantity' => (int)$row['total_quantity'],
                'total_orders' => (int)$row['total_orders']
            ];
        }

        $final = array_values($monthly);
        $this->response = [
            'result' => 'success',
            'monthly' => $final
        ];

        echo json_encode($this->response);
    }

    public function getDailyData() {        
        // 오늘로부터 30일 전 ~ 오늘까지
        $endDate = date('Y-m-d'); // 오늘
        $startDate = date('Y-m-d', strtotime('-29 days')); // 오늘 포함 30일치(시작일 포함)

        // all_machine_product 테이블에서 30일간 일별 생산량 및 설비별 기록 건수
        $query = "
            SELECT 
                work_date AS date,
                IFNULL(SUM(work_qty), 0) AS daily_quantity,
                COUNT(*) AS daily_orders
            FROM 
                mes_daily_work
            WHERE 
                work_date >= '{$startDate}' AND work_date <= '{$endDate}'
            GROUP BY 
                work_date
            ORDER BY 
                work_date ASC
        ";
        $this->query($query);
        $results = $this->fetchAll();

        // 30일치 일별 데이터(누락 일은 0)로 가공
        $daily = [];
        $period = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('P1D'),
            (new DateTime($endDate))->modify('+1 day') // 종료일 포함을 위해 +1일
        );

        foreach ($period as $dt) {
            $key = $dt->format('Y-m-d');
            $daily[$key] = [
                'date' => $key,
                'daily_quantity' => 0,
                'daily_orders' => 0
            ];
        }
        foreach ($results as $row) {
            $key = $row['date'];
            if (isset($daily[$key])) {
                $daily[$key] = [
                    'date' => $key,
                    'daily_quantity' => (int)$row['daily_quantity'],
                    'daily_orders' => (int)$row['daily_orders']
                ];
            }
        }

        $final = array_values($daily);
        $this->response = [
            'result' => 'success',
            'daily' => $final
        ];

        echo json_encode($this->response);
    }

    // 월별 재고금액 가져오기
    public function getMonthlyCostData() {
        // 해당년도 각 월별 데이터 반환
        $year = date('Y');

        // 해당 년도의 1월~12월 각 월별 재고금액 집계
        $query = "
            SELECT 
                close_date AS month,
                IFNULL(close_amount, 0) AS stock_amount,
                1 AS total_orders
            FROM 
                mes_stock_close
            WHERE 
                close_date >= '{$year}-01-01' AND close_date <= '{$year}-12-31'
            ORDER BY 
                close_date ASC
        ";
        $this->query($query);
        $results = $this->fetchAll();

        // 월별 데이터(누락 월은 0)로 가공하여 1~12월 모두 반환
        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $key = sprintf('%04d-%02d', $year, $m);
            $monthly[$key] = [
                'month' => $key,
                'stock_amount' => 0,
                'total_orders' => 0
            ];
        }
        foreach ($results as $row) {
            $key = $row['month'];
            $monthly[$key] = [
                'month' => $key,
                'stock_amount' => (int)$row['stock_amount'],
                'total_orders' => (int)$row['total_orders']
            ];
        }

        $final = array_values($monthly);
        $this->response = [
            'result' => 'success',
            'monthly' => $final
        ];

        echo json_encode($this->response);
    }

    public function getInventoryCostList() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';                
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;

        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';

        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';

        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_stock_close 
            {$whereClause} 
            ORDER BY close_date ASC
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();

        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'close_date' => $data['close_date'],
                    'close_amount' => $data['close_amount'],
                ];
            }, $results)
        ];

        echo json_encode($this->response);
    }

    // 생산실적 통계 페이지의 카드용 데이터 가져오기
    public function getProductStat() {        
        $plan_running_time = 8;        

        // 총생산량
        $query = "select sum(work_qty) as total_qty from mes_daily_work";
        $this->query($query);
        $results = $this->fetch();
        $total_qty = $results['total_qty'];
        
        // 하루 평균 생산량 = 총 생산량 / 생산일(row 수)
        $query = "select count(*) as days from mes_daily_work";
        $this->query($query);
        $results = $this->fetch();
        $days = $results['days'];
        $daily_quantity = ($days > 0) ? $total_qty / $days : 0;

        // 시간당 평균 생산량
        /*$query = "select avg(product_qty) as avg_qty from all_machine_product where loginId = '{$loginId}'";
        $this->query($query);
        $results = $this->fetch();
        */        
        $avg_qty = $total_qty / $days / $plan_running_time;

        // 제조 리드타임(초) 계산: 1개를 생산하는데 걸린 평균 시간(초)
        // (근무일수 x 일일 계획시간 x 3600) / 총생산량
        $total_seconds = $days * $plan_running_time * 3600;
        $lead_time = ($total_qty > 0) ? $total_seconds / $total_qty : 0; // 초 단위

        $this->response = [
            'result' => 'success',
            'plan_running_time' => $plan_running_time * $days,
            'total_quantity' => $total_qty,
            'avg_quantity' => $avg_qty,
            'daily_quantity' => $daily_quantity,
            'lead_time' => $lead_time,
        ];
        echo json_encode($this->response);
    }

    public function getInventoryCostStat() {
        $query = "select * from mes_stock_close order by close_date desc limit 1";
        $this->query($query);
        $result = $this->fetch();
        if ($result) {
            $current_cost = $result['close_amount'];

            $query = "select * from mes_stock_close order by close_date desc limit 2";
            $this->query($query);
            $results = $this->fetchAll();
            
            $previous_cost = $results[1]['close_amount'];
            $cost_change_rate = ($current_cost - $previous_cost) / $previous_cost * 100;
            $cost_change_type = ($cost_change_rate > 0) ? 'increase' : (($cost_change_rate < 0) ? 'decrease' : 'no_change');
            $this->response = [
                'result' => 'success',
                'current_cost' => $current_cost,
                'cost_change_rate' => $cost_change_rate,
                'cost_change_type' => $cost_change_type, // 증가/감소 구분값 추가
            ];
        }
        echo json_encode($this->response);
    }

    // 설비데이터 :: 누전검사
    public function getLeakageInspection() {
        // 파라미터 설정
        $where = !empty($this->param['where']) ? $this->param['where'] : '';
        $orderby = !empty($this->param['orderby']) ? $this->param['orderby'] : 'uid'; // 기본값을 'uid'로 설정
        $asc = !empty($this->param['asc']) ? $this->param['asc'] : 'ASC'; // 기본값을 'ASC'로 설정
        $per = !empty($this->param['per']) ? (int)$this->param['per'] : null; // null로 설정하여 LIMIT 생략 가능
        $page = !empty($this->param['page']) ? (int)$this->param['page'] : 1; // 기본값을 1로 설정
        $start = ($page - 1) * $per;

        // WHERE 조건이 있을 경우만 추가
        $whereClause = !empty($where) ? "{$where}" : '';

        // LIMIT 절을 동적으로 추가
        $limitClause = ($per !== null) ? "LIMIT {$start}, {$per}" : '';

        // 게시물 목록 가져오기
        $query = "
            SELECT * 
            FROM mes_machine_data 
            {$whereClause} 
            ORDER BY {$orderby} {$asc} 
            {$limitClause}
        ";                     
        $this->query($query);
        $results = $this->fetchAll();

        $this->response = [
            'data' => array_map(function($data) {
                return [
                    'uid' => $data['uid'],
                    'machine' => $data['machine'],
                    'data_type' => $data['data_type'],
                    'value' => $data['value'],
                    'timestamp' => $data['timestamp'],
                ];
            }, $results)
        ];

        echo json_encode($this->response);
    }

    // mes.php 파일 내부 (예시)

/**
     * 3개 냉장 창고의 최신 실시간 온도 데이터를 조회합니다.
     * machine_id: frig_goods, frig_mix, frig_stuff
     */
    /**
     * 3개 냉장 창고의 최신 실시간 온도 데이터를 조회합니다.
     * @DB_STRUCTURE: mes_machine_data (machine, value, timestamp)
     */
    public function getFrigWarehouseStatus() {
        // 1. 쿼리 준비
        $machine_list = ["'frig_goods'", "'frig_mix'", "'frig_stuff'"];
        $machine_in = implode(',', $machine_list);

        // machine 테이블은 없다고 가정하고, machine 필드의 값을 그대로 창고 이름으로 사용합니다.
        // 온도 값은 value 필드에, 측정 시각은 timestamp 필드에 있다고 가정합니다.
        // 'data_type' 필드를 활용하여 온도 데이터만 필터링한다고 가정합니다.
        $sql = "
            SELECT 
                t1.machine AS machine_id,        /* 창고 ID */
                t1.machine AS machine_name,      /* 창고 이름으로 사용 (mes_machine 테이블 JOIN 생략) */
                t1.value AS temp,                /* 온도 값 (value 필드) */
                NULL AS sensor_id,               /* 센서 ID 정보는 이 테이블에 없으므로 NULL */
                30 AS max_limit,                 /* 최대 기준 온도 (상수로 가정. DB에 없으므로) */
                t1.timestamp AS measure_time     /* 측정 시각 */
            FROM 
                mes_machine_data t1
            WHERE 
                t1.machine IN ({$machine_in})
                AND t1.data_type = 'temp'
                AND t1.timestamp = (
                    SELECT MAX(timestamp) 
                    FROM mes_machine_data 
                    WHERE machine = t1.machine
                    AND data_type = 'temp'
                )
            ORDER BY t1.machine ASC
        ";

        
        // 2. 쿼리 실행
        $result = $this->query($sql);
        
        if ($result === false) {
            // 쿼리 실행 실패 시, response에 오류 메시지가 이미 설정되어 있음
        } else {
            $data = $this->fetchAll(); 

            // 데이터가 비어있을 경우, 프론트엔드의 3개 카드 구조 유지를 위해 임시 데이터 추가 고려 가능
            
            $this->response = [
                'result' => 'success', 
                'data' => $data 
            ];
        }
        
        echo json_encode($this->response);
    }

    /**
     * 최근 N분간의 온도 데이터를 조회합니다.
     * 파라미터:
     *  - machine_code (선택): 특정 창고 코드
     *  - minutes (선택, 기본 10): 조회할 과거 분 단위
     */
    public function getRecentTemperature() {
        $minutes = isset($this->param['minutes']) ? (int)$this->param['minutes'] : 10;
        $machine_code = isset($this->param['machine_code']) ? $this->escapeString($this->param['machine_code']) : '';

        $where = "t1.data_type = 'temp' AND t1.timestamp >= DATE_SUB(NOW(), INTERVAL {$minutes} MINUTE)";
        if (!empty($machine_code)) {
            $where .= " AND t1.machine = '{$machine_code}'";
        } else {
            $machine_list = ["'frig_goods'", "'frig_mix'", "'frig_stuff'"];
            $machine_in = implode(',', $machine_list);
            $where .= " AND t1.machine IN ({$machine_in})";
        }

        $sql = "SELECT t1.machine AS machine_id, t1.machine AS machine_name, t1.value AS temp, t1.timestamp AS measure_time, 30 AS max_limit FROM mes_machine_data t1 WHERE {$where} ORDER BY t1.timestamp ASC";

        $result = $this->query($sql);
        if ($result === false) {
            $this->response = [
                'result' => 'error',
                'error' => '데이터베이스 쿼리 오류가 발생하였습니다.'
            ];
        } else {
            $data = $this->fetchAll($result);
            $this->response = [
                'result' => 'success',
                'data' => $data
            ];
        }

        echo json_encode($this->response);
    }

    // --- [ 2. 월간 목표/실적 분석 함수 (이전 요청의 코드) ] -------------------

    /**
     * 특정 월의 품목별 목표 및 실적 데이터를 가져옵니다. (Mock Target Logic 포함)
     */
    /**
     * 기간별/창고별 온도 측정 이력을 조회합니다.
     * machine_code가 없으면 냉장 창고 3개에 대한 모든 'temp' 데이터를 조회합니다.
     */
    public function getTemperatureHistory() {
        // 1. 입력값 가져오기 및 보안 강화 (변수명 정리)
        // $this->param은 외부 입력 데이터를 담고 있다고 가정합니다.
        $start_date = $this->escapeString($this->param['start_date'] ?? date('Y-m-d')); // 기본값 설정
        $end_date = $this->escapeString($this->param['end_date'] ?? date('Y-m-d')); // 기본값 설정
        $machine_code = $this->escapeString($this->param['machine_code'] ?? ''); 
    
        // 2. 쿼리 조건 설정
        $where_clauses = ["t1.data_type = 'temp'"];
        
        // 날짜 필터링 (시간대까지 포함하여 해당 범위의 데이터를 가져옴)
        $where_clauses[] = "t1.timestamp >= '{$start_date} 00:00:00'";
        $where_clauses[] = "t1.timestamp <= '{$end_date} 23:59:59'";
        
        // 3. 창고 코드 필터링 로직
        if (!empty($machine_code)) {
            // 특정 창고 코드가 있으면 해당 창고만 조회
            $where_clauses[] = "t1.machine = '{$machine_code}'";
        } else {
            // machine_code가 없거나 비어있는 경우:
            // 냉장 창고 3개 전체를 조회하도록 제한 
            $machine_list = ["'frig_goods'", "'frig_mix'", "'frig_stuff'"];
            $machine_in = implode(',', $machine_list);
            $where_clauses[] = "t1.machine IN ({$machine_in})";
        }
        
        $where_sql = "WHERE " . implode(' AND ', $where_clauses);
        
        // 4. 최종 쿼리
        $sql = "
            SELECT 
                t1.machine AS machine_id,
                t1.machine AS machine_name,
                t1.value AS temp,
                t1.timestamp AS measure_time,
                NULL AS sensor_id,
                30 AS max_limit
            FROM 
                mes_machine_data t1
            {$where_sql}
            ORDER BY 
                t1.timestamp ASC 
            LIMIT 1000
        ";
    
        // ⭐ 중요 수정 사항:
        // 1) 차트는 일반적으로 시간 순서대로 그려야 하므로 ORDER BY t1.timestamp를 ASC(오름차순)로 변경했습니다.
        // 2) 입력값이 없을 경우를 대비하여 start_date, end_date에 기본값(오늘 날짜)을 설정했습니다.
        
        $result = $this->query($sql);
        
        if ($result === false) {
            $this->response = [
                'result' => 'error',
                'error' => '데이터베이스 쿼리 오류가 발생하였습니다.' // message 대신 error 키 사용
            ];
        } else {
            $data = $this->fetchAll($result); 
            $this->response = [
                'result' => 'success', 
                'data' => $data 
            ];
        }
        
        echo json_encode($this->response);
    }

    // 금속검출(생산하는) 품목 등록
    public function startDetection() {
        $item_name = $this->param['item_name'];
        $item_uid = $this->param['item_uid'];
        $work_date = date('Y-m-d');

        $query = "select * from mes_current_item";
        $this->query($query);

        if($this->getRows() > 0) {
            $current_item = $this->fetch();
            $data = array(
                'table' => 'mes_current_item',
                'where' => 'uid=' . $current_item['uid'],
                'item_uid' => $item_uid,
                'item_name' => $item_name,
                'register_dt' => $work_date,
            );
            $result = $this->update($data);
        } else {

            $data = array(
                'table' => 'mes_current_item',
                'item_uid' => $item_uid,
                'item_name' => $item_name,
                'register_dt' => $work_date,
            );
            $result = $this->insert($data);
        }

        $now = date('Y-m-d H:i:s');
        $this->query("delete from metal_detection_status");
        $status_data = array(
            'table' => 'metal_detection_status',
            'id' => 1,
            'item_uid' => $item_uid,
            'item_name' => $item_name,
            'started_at' => $now,
            'ended_at' => $now,
            'detected_qty' => 0,
            'good_qty' => 0,
            'produced_qty' => 0,
        );
        $status_result = $this->insert($status_data);

        if($result && $status_result) {
            $this->response = [
                'result' => 'success',
                'message' => '금속검출(생산하는) 품목 등록이 되었습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '금속검출(생산하는) 품목 등록에 실패하였습니다'
            ];
        }
        echo json_encode($this->response);
    }

    // 금속검출 종료
    public function stopDetection() {
        $now = date('Y-m-d H:i:s');

        $this->query("update metal_detection_status set ended_at='{$now}'");
        $this->query("
            insert into metal_detection_history
                (item_uid, item_name, started_at, ended_at, detected_qty, good_qty, produced_qty)
            select
                item_uid,
                item_name,
                started_at,
                ended_at,
                detected_qty,
                good_qty,
                (detected_qty + good_qty) as produced_qty
            from metal_detection_status
        ");
        $this->query("delete from metal_detection_status");

        $query = "delete from mes_current_item";
        $this->query($query);

        $this->response = [
            'result' => 'success',
            'message' => '금속검출이 종료되었습니다'
        ];
        echo json_encode($this->response);
    }

    // 금속검출 상태 조회
    public function getDetectionStatus() {
        $query = "select * from metal_detection_status where id=1";
        $this->query($query);
        $data = $this->fetch();

        if ($data) {
            $this->response = [
                'result' => 'success',
                'data' => $data
            ];
        } else {
            $this->response = [
                'result' => 'empty'
            ];
        }

        echo json_encode($this->response);
    }
    // 금속검출현황 조회 (오늘 데이터만)
    public function getMetalDetectionToday() {
        $today = date('Y-m-d');
        $start_date = isset($this->param['start_date']) ? $this->param['start_date'] : $today;
        $end_date = isset($this->param['end_date']) ? $this->param['end_date'] : $today;
        $query = "SELECT * FROM mes_metal_detect WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}' ORDER BY created_at DESC";
        $this->query($query);
        $results = $this->fetchAll();
        
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            $query = "select * from mes_items where uid={$data['item_uid']}";
            $this->query($query);
            $item = $this->fetch();
            return [
                'uid' => $data['uid'],
                'item_uid' => $data['item_uid'],
                'item_name' => $item['item_name'],
                'item_code' => $item['item_code'],
                'standard' => $item['standard'],
                'qty' => $data['qty'],
                'created_at' => $data['created_at'],
            ];
        }, $results);
        
        // JSON 응답 반환
        echo json_encode($this->response);
    }

    // 금속검출 이력 조회
    public function getMetalDetectionHistory() {
        $today = date('Y-m-d');
        $start_date = $this->escapeString($this->param['start_date'] ?? $today);
        $end_date = $this->escapeString($this->param['end_date'] ?? $today);
        $item_name = $this->escapeString(trim($this->param['item_name'] ?? ''));
        $page = isset($this->param['page']) ? max(1, (int)$this->param['page']) : 1;
        $per = isset($this->param['per']) ? max(1, (int)$this->param['per']) : 10;
        $start = ($page - 1) * $per;

        $where_clauses = [];
        if (!empty($start_date)) {
            $where_clauses[] = "h.started_at >= '{$start_date} 00:00:00'";
        }
        if (!empty($end_date)) {
            $where_clauses[] = "h.started_at <= '{$end_date} 23:59:59'";
        }
        if (!empty($item_name)) {
            $where_clauses[] = "h.item_name LIKE '%{$item_name}%'";
        }
        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';
        $limit_sql = "LIMIT {$start}, {$per}";

        $query = "
            SELECT
                h.id,
                h.item_uid,
                h.item_name,
                h.started_at,
                h.ended_at,
                h.detected_qty,
                h.good_qty,
                h.produced_qty
            FROM metal_detection_history h
            {$where_sql}
            ORDER BY h.started_at DESC
            {$limit_sql}
        ";
        $this->query($query);
        $results = $this->fetchAll();

        $this->response = [
            'result' => 'success',
            'data' => $results
        ];

        echo json_encode($this->response);
    }

    // 금속검출 이력 엑셀 다운로드
    public function getMetalDetectionHistoryExcel() {
        $today = date('Y-m-d');
        $start_date = $this->escapeString($this->param['start_date'] ?? $today);
        $end_date = $this->escapeString($this->param['end_date'] ?? $today);
        $item_name = $this->escapeString(trim($this->param['item_name'] ?? ''));

        $where_clauses = [];
        if (!empty($start_date)) {
            $where_clauses[] = "h.started_at >= '{$start_date} 00:00:00'";
        }
        if (!empty($end_date)) {
            $where_clauses[] = "h.started_at <= '{$end_date} 23:59:59'";
        }
        if (!empty($item_name)) {
            $where_clauses[] = "h.item_name LIKE '%{$item_name}%'";
        }
        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

        $query = "
            SELECT
                h.item_name,
                h.started_at,
                h.ended_at,
                h.detected_qty,
                h.good_qty,
                h.produced_qty
            FROM metal_detection_history h
            {$where_sql}
            ORDER BY h.started_at DESC
        ";
        $this->query($query);
        $results = $this->fetchAll();

        $filename = 'metal_detection_history_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF";
        $output = fopen('php://output', 'w');
        fputcsv($output, ['품목', '시작시간', '종료시간', '검출수량', '양품수량', '생산수량']);

        foreach ($results as $row) {
            fputcsv($output, [
                $row['item_name'],
                $row['started_at'],
                $row['ended_at'],
                $row['detected_qty'],
                $row['good_qty'],
                $row['produced_qty'],
            ]);
        }

        fclose($output);
        exit;
    }

    // 금속검출 이력 선택 삭제 (id 기준)
    public function deleteMetalDetectionHistory() {
        $ids = trim($this->param['ids'] ?? '');
        if ($ids === '') {
            $this->response = [
                'result' => 'error',
                'message' => '삭제할 데이터가 없습니다'
            ];
            echo json_encode($this->response);
            return;
        }

        $id_list = array_filter(array_map('intval', explode(',', $ids)));
        if (empty($id_list)) {
            $this->response = [
                'result' => 'error',
                'message' => '삭제할 데이터가 없습니다'
            ];
            echo json_encode($this->response);
            return;
        }

        $id_sql = implode(',', $id_list);
        $query = "DELETE FROM metal_detection_history WHERE id IN ({$id_sql})";
        if (!$this->query($query)) {
            $this->response = [
                'result' => 'error',
                'message' => '삭제에 실패하였습니다'
            ];
            echo json_encode($this->response);
            return;
        }

        $this->response = [
            'result' => 'success',
            'message' => '데이터를 삭제하였습니다'
        ];
        echo json_encode($this->response);
    }

    // updateMetalStats 함수 : 금속검출 대시보드 통계 정보를 반환
    public function updateMetalStats() {
        // 1. 오늘 날짜 구하기
        $today = date('Y-m-d');
        
        // 2. 현재 금속검출 품목 (status 테이블 기준)
        $query = "SELECT item_name FROM metal_detection_status WHERE id=1";
        $this->query($query);
        $currentStatus = $this->fetch();

        // 3. 금일 검사 총 수량 (produced_qty)
        $query = "
            SELECT
                SUM(produced_qty) AS total_checked
            FROM metal_detection_history
            WHERE DATE(started_at) = '{$today}'
        ";
        $this->query($query);
        $dailyTotal = $this->fetch();
        $total_checked_sum = isset($dailyTotal['total_checked']) ? intval($dailyTotal['total_checked']) : 0;

        // 4. 금일 검출 총 수량 (NG)
        $query = "
            SELECT
                SUM(detected_qty) AS total_detected
            FROM metal_detection_history
            WHERE DATE(started_at) = '{$today}'
        ";
        $this->query($query);
        $detectTotal = $this->fetch();
        $total_detected_sum = isset($detectTotal['total_detected']) ? intval($detectTotal['total_detected']) : 0;

        $current_item = '없음';
        if ($currentStatus && isset($currentStatus['item_name'])) {
            $itemName = trim($currentStatus['item_name']);
            if ($itemName !== '') {
                $current_item = $itemName;
            }
        }

        // 6. 데이터 조합 및 반환
        $this->response = [
            'result' => 'success',
            'current_item' => $current_item,
            'total_checked_sum' => $total_checked_sum,
            'total_detected_sum' => $total_detected_sum,
        ];
        echo json_encode($this->response);
    }

    // 세척기 전력량
    // 세척기(cleaner) 데이터 조회 및 출력
    public function getCleaner() {
        // cleaner 설비 데이터 조회
        $query = "SELECT * FROM mes_machine_data WHERE machine = 'cleaner' ORDER BY timestamp DESC";
        $this->query($query);
        $results = $this->fetchAll();

        $data = [];
        if ($results && is_array($results)) {
            foreach ($results as $row) {
                $data[] = [
                    'uid'        => $row['uid'],
                    'machine'    => $row['machine'],
                    'data_type'  => $row['data_type'],
                    'value'      => $row['value'],
                    'timestamp'  => $row['timestamp'],
                ];
            }
        }

        $this->response = [
            'result' => 'success',
            'data'   => $data
        ];
        echo json_encode($this->response);
    }
}
