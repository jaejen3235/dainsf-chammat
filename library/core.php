<?php
include "library/dbconnect.php";

extract($_POST);
extract($_GET);

$now = date("Y-m-d H:i:s");

class Core extends dbconn {
	public function insert($data){
		foreach($data as $key => $value){
			if($key == "table") {
				$query = "insert into ".$value." ";
			} else {
				$field .= $key.",";
				if(is_numeric($value)) {
					$prefix = "";
					$suffix = "";
				} else {
					$prefix = "'";
					$suffix = "'";
				}

				$val .= $prefix.$value.$suffix.",";
			}
		}
			
		$query = $query."(".substr($field, 0, -1).")values(".substr($val, 0, -1).")";
		//echo $query;
		if($this->query($query)) return true;
		else return false;
	}

	public function update($data){
		foreach($data as $key => $value){
			if($key == "table") {
				$query = "update ".$value." set ";
			} else if($key == "where") {
				$where = " where ".$value;
			} else {
				$field .= $key."=";
				if(is_numeric($value)) {
					$prefix = "";
					$suffix = "";
				} else {
					$prefix = "'";
					$suffix = "'";
				}

				$field .= $prefix.$value.$suffix.",";
			}
		}
			
		$query = $query.substr($field, 0, -1).$where;
		//echo $query;
		if($this->query($query)) return true;
		else return false;
	}

	public function json_get_all($table,$order = null, $by = null){
		if($order != null && $by != null) $this->db->order_by($order,$by);

		$result = $this->get($table);
		if($result->num_rows() > 0){
			foreach($result->result() as $row){
				$data[] = $row;
			}
			return $data;
		}
	}

	public function json_get_where($table,$where){
		//var_dump($where);
		foreach($where as $key => $value){
			$this->where($key,$value);
		}
		$result = $this->get($table);
		//echo $this->db->last_query();
		if($result->num_rows() > 0){
			foreach($result->result() as $row){
				$data[] = $row;
			}
			return $data;
		}
	}

	public function delete($table,$uid){
		$this->where("uid",$uid);
		$result = $this->delete($table);
		if($result) return true;
		else return false;
	}
}

$core = new Core;
?>