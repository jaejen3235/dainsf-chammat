<?php
include "./include/db_define.php";

class Database {
    private $pdo;
    private $lastInsertId;
    
    public function __construct() {
        $this->connect();  // 생성자에서 connect() 호출
    }
    
    // connect() 메서드를 protected로 변경
    public function connect() { // protected에서 public으로 변경
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            $this->pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
        } catch (PDOException $e) {
            $this->errorLog('database', 'connection', $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public function insert($data) {
        try {
            $fields = array_keys(array_diff_key($data, ['table' => '']));
            $placeholders = array_fill(0, count($fields), '?');
            
            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                $data['table'],
                implode(', ', $fields),
                implode(', ', $placeholders)
            );
            
            $values = array_values(array_diff_key($data, ['table' => '']));
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($values);
            
            if ($result) {
                $this->lastInsertId = $this->pdo->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            $this->errorLog('database', 'insert', $e->getMessage());
            return false;
        }
    }
    
    public function update($data) {
        try {
            $sets = [];
            $values = [];
            
            foreach ($data as $key => $value) {
                if ($key !== 'table' && $key !== 'where') {
                    $sets[] = "$key = ?";
                    $values[] = $value;
                }
            }
            
            $sql = sprintf(
                "UPDATE %s SET %s WHERE %s",
                $data['table'],
                implode(', ', $sets),
                $data['where']
            );
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            $this->errorLog('database', 'update', $e->getMessage());
            return false;
        }
    }
    
    public function getFieldValue($table, $field, $id) {
        try {
            $stmt = $this->pdo->prepare("SELECT $field FROM $table WHERE uid = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            return $result ? $result[$field] : null;
        } catch (PDOException $e) {
            $this->errorLog('database', 'getFieldValue', $e->getMessage());
            return null;
        }
    }
    
    public function getLastInsertId() {
        return $this->lastInsertId;
    }
    
    private function errorLog($class, $method, $message) {
        // Implement your error logging logic here
        error_log("[$class][$method] $message");
    }
}