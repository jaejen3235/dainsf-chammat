<?php
include "./include/db_define.php";

class Database {
    private $result;
    private $subResult;
    private $conn;
    private $insertUid;

    public function __construct() {
        $this->connectDatabase();
    }

    public function connectDatabase() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($this->conn->connect_error) {
            $this->registLog("Connection failed: " . $this->conn->connect_error);
        }
    }

    // 트랜잭션 시작
    public function beginTransaction() {
        $this->conn->begin_transaction();
    }

    // 커밋
    public function commit() {
        $this->conn->commit();
    }

    // 롤백
    public function rollback() {
        $this->conn->rollback();
    }

    public function escapeString($string) {
        return $this->conn->real_escape_string($string);
    }

    private function bindParams($stmt, $paramTypes, $params) {
        $bindParams = array_merge(array($paramTypes), $params);
        $bindParamsRefs = array();
        foreach ($bindParams as $key => $value) {
            $bindParamsRefs[$key] = &$bindParams[$key];
        }
        return call_user_func_array(array($stmt, 'bind_param'), $bindParamsRefs);
    }

    public function insert($data) {
        $fields = "";
        $placeholders = "";
        $paramTypes = "";
        $params = array();

        foreach ($data as $key => $value) {
            if ($key === "table") continue;
            $fields .= $key . ",";
            $placeholders .= "?,";
            $params[] = $value;
            $paramTypes .= (is_numeric($value) && !is_float($value)) ? "i" : "s";
        }

        $table = $data['table'];
        $query = "INSERT INTO $table (" . rtrim($fields, ",") . ") VALUES (" . rtrim($placeholders, ",") . ")";
        
        if ($stmt = $this->conn->prepare($query)) {
            $this->bindParams($stmt, $paramTypes, $params);
            if ($stmt->execute()) {
                $this->insertUid = $stmt->insert_id;
                return true;
            } else {
                $this->errorLog('database', 'insert', $query);
                return false;
            }
        } else {
            $this->errorLog('database', 'insert', $query);
            return false;
        }
    }

    public function update($data) {
        $query = "UPDATE " . $data['table'] . " SET ";
        $fieldValuePairs = array();
        $types = "";
        $params = array();

        foreach ($data as $key => $value) {
            if ($key === "table" || $key === "where") continue;
            $fieldValuePairs[] = $key . " = ?";
            $params[] = $value;
            $types .= (is_numeric($value) && !is_float($value)) ? "i" : "s";
        }

        $query .= implode(", ", $fieldValuePairs);
        $query .= isset($data['where']) ? " WHERE " . $data['where'] : "";

        if ($stmt = $this->conn->prepare($query)) {
            $this->bindParams($stmt, $types, $params);
            return $stmt->execute();
        } else {
            $this->errorLog('database', 'update', $query);
            return false;
        }
    }

    public function query($query) {
        $this->result = $this->conn->query($query);
        return $this->result ? true : false;
    }

    public function queryFetch($query) {
		$this->result = $this->conn->query($query);

		if ($this->result) {
			return $this->result->fetch_assoc();
		} else {
            $this->errorLog('database', 'queryFetch', $query); 
			return false;
		}
	}

    public function subQuery($query) {
        $this->subResult = $this->conn->query($query);
        if (!$this->subResult) $this->errorLog('database', 'subQuery', $query);
        return $this->subResult ? true : false;
    }

    public function deleteQuery($table, $uid) {
        $query = "DELETE FROM $table WHERE uid=?";
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param('i', $uid);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } else {
            $this->errorLog('database', 'deleteQuery', $query);
            return false;
        }
    }

    public function deleteCompareQuery($table, $field, $uid) {
        $query = "DELETE FROM $table WHERE $field=?";
        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param('i', $uid);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } else {
            $this->errorLog('database', 'deleteCompareQuery', $query);
            return false;
        }
    }

    public function getUid() {
        return $this->insertUid;
    }

    public function getField($table, $fields, $val) {
        $query = "SELECT $fields FROM $table WHERE uid=?";
        $paramType = is_numeric($val) ? 'i' : 's';

        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param($paramType, $val);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
            }
        }
        $this->errorLog('database', 'getField', $query);
        return null;
    }

    public function getWhere($table, $fields, $where) {
        $query = "SELECT $fields FROM $table WHERE $where";
        if ($stmt = $this->conn->prepare($query)) {
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
            }
        }
        $this->errorLog('database', 'getWhere', $query);
        return null;
    }

    public function fetch() {
        return $this->result ? $this->result->fetch_assoc() : null;
    }

    public function fetchArray() {
        return $this->result ? $this->result->fetch_array() : null;
    }

    public function subFetch() {
        return $this->subResult ? $this->subResult->fetch_assoc() : null;
    }

    public function fetchAll() {
        return $this->result ? $this->result->fetch_all(MYSQLI_ASSOC) : array();
    }

    public function getRows() {
        return $this->result ? $this->result->num_rows : -1;
    }

    public function getSubRows() {
        return $this->subResult ? $this->subResult->num_rows : -1;
    }

    public function checkDataRow($table, $where) {
        $whereClause = '';
        foreach ($where as $key => $value) {
            $whereClause .= is_null($value) ? "$key IS NULL AND " : "$key = '" . $this->escapeString($value) . "' AND ";
        }
        $whereClause = rtrim($whereClause, ' AND ');

        $query = "SELECT * FROM $table WHERE $whereClause";
        $this->query($query);
        if ($this->getRows() > 0) return true;
        $this->errorLog('database', 'checkDataRow', $query);
        return false;
    }

    public function errorLog($controller, $method, $query) {
        $data = array(
            'table' => 'error_log',
            'controller' => $controller,
            'method' => $method,
            'query' => $query,
            'registDate' => date('Y-m-d H:i:s')
        );
        $this->insert($data);
    }

    public function registLog($log) {
        $logdate = date("Ymd");
        $myfile = fopen("log/error_".$logdate.".txt", "a") or die("Unable to open file");
        fwrite($myfile, "\r\n[" . date("Y-m-d H:i:s") . "] - " . $log);
        fclose($myfile);
    }
}
?>
