<?php

$db = new dbconn;
$db->dbconn();

Class dbconn
{
	public function dbconn()
	{
		$this->conn = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME) or die ("Connect Error");
		$status = mysql_select_db(DB_NAME);
		mysql_query("set names 'utf8'");
		if(!$status) msg("Connect Error");
	}

	public function query($sql)
	{
		$result = @mysql_query($sql,$this->conn);
		if(!$result) return false;
		else return $result;
	}

	public function num_rows($sql)
	{
		$result = $this->query($sql);
		$num = @mysql_num_rows($result);
		return $num;
	}

	public function fetch_array($sql)
	{
		$num_row = $this->num_rows($sql);
		if($num_row == 0)
		{
			return 0;
		} else {
			$result = $this->query($sql);
			$i = 0;
			while($rows = @mysql_fetch_array($result,MYSQL_ASSOC))
			{
				$arydata[$i] = $rows;
				$i++;
			}

			return $arydata;
		}
	}

	public function fetch_rows($sql)
	{
		$num_row = $this->num_rows($sql);
		if($num_row == 0)
		{
			return 0;
		} else {
			$result = $this->query($sql);
			$i = 0;
			while($rows = @mysql_fetch_row($result))
			{
				$rowdata[$i] = $rows;
				$i++;
			}

			return $rowdata;
		}
	}

	public function sql_result($sql,$int)
	{
		$result = $this->query($sql);
		$value = @mysql_result($result, 0, $int);
		return $value;
	}

	public function fetch_row($sql)
	{
		$num_row = $this->num_rows($sql);
		if($num_row == 0)
		{
			return 0;
		} else {
			$result = $this->query($sql);
			$rows = @mysql_fetch_row($result);
			return $rows;
		}
	}

	public function fetch_array_one($sql)
	{
		$num_row = $this->num_rows($sql);
		if($num_row == 0)
		{
			return 0;
		} else {
			$result = $this->query($sql);
			$rows = @mysql_fetch_array($result);
			return $rows;
		}
	}

	public function fetch_object_one($sql)
	{
		$result = $this->query($sql);
		$res = @mysql_fetch_object($result);
		return $res;
	}

	public function checkQuery($sql) {
		$res = $this->query($sql);
		if($res) return true;
		else return false;
	}

	public function dbClose() {
		mysql_close($this->conn);
	}
}

?>