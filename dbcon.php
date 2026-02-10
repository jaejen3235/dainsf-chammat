<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("db.php");

class Dbcon extends Database
{
    private $param;
    private $now;
    private $nowTime;
    private $response = [];    

    public function __construct($param) {
        parent::__construct();  // Database 클래스의 생성자 호출
        $this->param = $param;
        $this->now = date("Y-m-d");
        $this->nowTime = date("Y-m-d H:i:s");     
        
        parent::connect(); // 부모 클래스의 connect 메서드를 호출
    }

    public function test() {
        if($this->param['uid'] == '') {
            $data = array(
                'table' => 'test',
                'name' => '김현수'
            );
            $this->insert($data);
        } else {
            $data = array(
                'table' => 'test',
                'where' => 'uid=' . $this->param['uid'],
                'name' => '김현수다'
            );
            $this->update($data);
        }        
    }

    private function removeComma($str) {
        return str_replace(',', '', $str);
    }
}
