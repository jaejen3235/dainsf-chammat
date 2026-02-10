<?php
session_start();
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once("controllers/functions.php");

class Member extends Functions
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

    // 배송지 리스트 가져오기
    public function getDeliveryList() {
        $query = "select * from member_delivery where memberUid=" . $_SESSION['loginUid'];
        $this->query($query);

        $i = 0;
        while($item = $this->fetch()) {
            $this->response[$i]['receiver'] = $item['receiver'];
            $this->response[$i]['mobile'] = $item['mobile'];
            $this->response[$i]['zipcode'] = $item['zipcode'];
            $this->response[$i]['address'] = $item['address'];
            $this->response[$i]['detailAddress'] = $item['detailAddress'];
            $this->response[$i]['basicPlace'] = $item['basicPlace'];
            $i++;
        }

        echo json_encode($this->response);
    }

    // 배송지 등록하기
    public function registDelivery() {
        $deliveryUid = $this->param['deliveryUid'];
        $mobile = $this->convertMobileNumber($this->param['mobile']);

        if(empty($deliveryUid)) {
            $data = array(
                'table' => 'member_delivery',
                'memberUid' => $_SESSION['loginUid'],
                'receiver' => $this->param['receiver'],
                'zipcode' => $this->param['zipcode'],
                'address' => $this->param['address1'],
                'detailAddress' => $this->param['address2'],
                'mobile' => $mobile,
                'basicPlace' => 'n'                
            );

            $result = $this->insert($data);

            if($result) {
                $this->response['result'] = 'success';
                $this->response['message'] = '배송지를 등록하였습니다';
            } else {
                $this->response['result'] = 'error';
                $this->response['message'] = '배송지 등록 중 에러가 발생하였습니다';
            }
        } else {
            $data = array(
                'table' => 'member_delivery',
                'where' => 'uid=' . $deliveryUid,
                'memberUid' => $_SESSION['loginUid'],
                'receiver' => $this->param['receiver'],
                'zipcode' => $this->param['zipcode'],
                'address' => $this->param['address1'],
                'detailAddress' => $this->param['address2'],
                'mobile' => $mobile,
                'basicPlace' => 'n'                
            );

            $result = $this->update($data);

            if($result) {
                $this->response['result'] = 'success';
                $this->response['message'] = '배송지를 등록하였습니다';
            } else {
                $this->response['result'] = 'error';
                $this->response['message'] = '배송지 수정 중 에러가 발생하였습니다';
            }
        }
    }
}