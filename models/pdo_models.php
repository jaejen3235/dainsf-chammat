<?php
include "./include/db_define.php";

class Database {
    private PDO $pdo;
    private ?PDOStatement $stmt = null;
    private ?PDOStatement $subStmt = null;

    public function __construct() {
        $this->connectDatabase();
    }

    public function connectDatabase() {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASSWORD,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            $this->registLog("Connection failed: " . $e->getMessage());
            throw $e;
        }
    }

    public function escapeString($string) {
        return substr($this->pdo->quote($string), 1, -1);
    }

    public function insert($data) {
        $table = $data['table'];
        unset($data['table']);

        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $query = "INSERT INTO $table ($fields) VALUES ($placeholders)";

        try {
            $this->stmt = $this->pdo->prepare($query);
            $result = $this->stmt->execute(array_values($data));
            
            if ($result) {
                return $this->pdo->lastInsertId();
            }
        } catch (PDOException $e) {
            $this->errorLog('database', 'insert', $query);
            throw $e;
        }

        return false;
    }

    public function update($data) {
        $table = $data['table'];
        $where = $data['where'] ?? '';
        unset($data['table'], $data['where']);

        $set = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
        $query = "UPDATE $table SET $set" . ($where ? " WHERE $where" : '');

        try {
            $this->stmt = $this->pdo->prepare($query);
            return $this->stmt->execute(array_values($data));
        } catch (PDOException $e) {
            $this->errorLog('database', 'update', $query);
            throw $e;
        }
    }

    public function query($query) {
        try {
            $this->stmt = $this->pdo->query($query);
            return true;
        } catch (PDOException $e) {
            $this->errorLog('database', 'query', $query);
            return false;
        }
    }

    public function subQuery($query) {
        try {
            $this->subStmt = $this->pdo->query($query);
            return true;
        } catch (PDOException $e) {
            $this->errorLog('database', 'subQuery', $query);
            return false;
        }
    }

    public function deleteQuery($table, $uid) {
        $query = "DELETE FROM $table WHERE uid = ?";
        try {
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([$uid]);
        } catch (PDOException $e) {
            $this->errorLog('database', 'deleteQuery', $query);
            return false;
        }
    }

    public function deleteCompareQuery($table, $field, $uid) {
        $query = "DELETE FROM $table WHERE $field = ?";
        try {
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([$uid]);
        } catch (PDOException $e) {
            $this->errorLog('database', 'deleteCompareQuery', $query);
            return false;
        }
    }

    public function getField($table, $fields, $val) {
        $query = "SELECT $fields FROM $table WHERE uid = ?";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$val]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->errorLog('database', 'getField', $query);
            return null;
        }
    }

    public function getWhere($table, $fields, $where) {
        $query = "SELECT $fields FROM $table WHERE $where";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->errorLog('database', 'getWhere', $query);
            return null;
        }
    }

    public function getCompareField($table, $fields, $compare, $val) {
        $query = "SELECT $fields FROM $table WHERE $compare = ?";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$val]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->errorLog('database', 'getCompareField', $query);
            return null;
        }
    }

    public function queryFetch($query) {
        try {
            $stmt = $this->pdo->query($query);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->errorLog('database', 'queryFetch', $query);
            return false;
        }
    }

    public function remove($query) {
        try {
            return $this->pdo->exec($query) !== false;
        } catch (PDOException $e) {
            $this->errorLog('database', 'remove', $query);
            return false;
        }
    }

    public function fetch() {
        return $this->stmt ? $this->stmt->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function fetchArray() {
        return $this->stmt ? $this->stmt->fetch(PDO::FETCH_BOTH) : null;
    }

    public function subFetch() {
        return $this->subStmt ? $this->subStmt->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function fetchAll() {
        return $this->stmt ? $this->stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function getRows() {
        return $this->stmt ? $this->stmt->rowCount() : -1;
    }

    public function getSubRows() {
        return $this->subStmt ? $this->subStmt->rowCount() : -1;
    }

    public function checkDataRow($table, $where) {
        $whereClause = [];
        $params = [];
        foreach ($where as $key => $value) {
            if (is_null($value)) {
                $whereClause[] = "$key IS NULL";
            } else {
                $whereClause[] = "$key = ?";
                $params[] = $value;
            }
        }
        $whereString = implode(' AND ', $whereClause);
        
        $query = "SELECT * FROM $table WHERE $whereString";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->errorLog('database', 'checkDataRow', $query);
            return false;
        }
    }

    public function errorLog($controller, $method, $query) {
        $registDate = date('Y-m-d H:i:s');
        $data = [
            'table' => 'error_log',
            'controller' => $controller,
            'method' => $method,
            'query' => $query,
            'registDate' => $registDate
        ];
        $this->insert($data);
    }

    public function registLog($log) {
        $logdate = date("Ymd");
        $myfile = fopen("log/error_".$logdate.".txt", "a") or die ("Unable to open file");
        $text = "\r\n[".date("Y-m-d H:i:s")."] - ".$log;
        fwrite($myfile, $text);
        fclose($myfile);
    }
}