<?php
include "../library/dbconnect_class.php";


extract($_POST);
extract($_GET);

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
	
	//' DB에 Insert
	public function convert_input($tag){
		$tag = str_ireplace("&","&amp;",$tag);
		$tag = str_ireplace('"',"&quot;",$tag);
		$tag = str_ireplace("'","&#039;",$tag);
		$tag = str_ireplace("<","&lt;",$tag);
		$tag = str_ireplace(">","&gt;",$tag);
		return $tag;
	}

	//' HTML로 OUT
	public function convert_output($CheckValue){
		$tag = str_ireplace("&","&amp;",$tag);
		$tag = str_ireplace('"',"&quot;",$tag);
		$tag = str_ireplace("'","&#039;",$tag);
		$tag = str_ireplace("<","&lt;",$tag);
		$tag = str_ireplace(">","&gt;",$tag);
		return $tag;
	}
	
	public function show_message($msg){
    	header('Content-Type: text/html; charset=UTF-8');
        echo "<script>";
        echo "alert('".$msg."');";
        echo "</script>";
    }
    
    public function show_message_go($msg,$url){
    	header('Content-Type: text/html; charset=UTF-8');
        echo "<script>";
        echo "alert('".$msg."');";
        echo "location.href='".$url."';";
        echo "</script>";
    }	
}

$core = new Core;
?>