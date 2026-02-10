<?php
include "./include/db_define.php";

class Database {
    private PDO $pdo;
    private ?PDOStatement $stmt = null;
    private ?PDOStatement $subStmt = null;

    public function __construct() {
        $this->connectDatabase();
    }

    private function connectDatabase() {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASSWORD,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
            );
        } catch (PDOException $e) {
            $this->logError("Connection failed: " . $e->getMessage());
            throw $e;
        }
    }

    public function insert(array $data): ?int {
        $table = $data['table'];
        unset($data['table']);

        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $query = "INSERT INTO $table ($fields) VALUES ($placeholders)";

        try {
            $this->stmt = $this->pdo->prepare($query);
            $this->stmt->execute(array_values($data));

            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->logError('insert', $query, $e);
            return null;
        } finally {
            $this->closeCursor();
        }
    }

    public function update(array $data): bool {
        $table = $data['table'];
        $where = $data['where'] ?? '';
        unset($data['table'], $data['where']);

        $set = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
        $query = "UPDATE $table SET $set" . ($where ? " WHERE $where" : '');

        try {
            $this->stmt = $this->pdo->prepare($query);
            return $this->stmt->execute(array_values($data));
        } catch (PDOException $e) {
            $this->logError('update', $query, $e);
            return false;
        } finally {
            $this->closeCursor();
        }
    }

    public function delete(string $table, int $uid): bool {
        $query = "DELETE FROM $table WHERE uid = ?";
        try {
            $this->stmt = $this->pdo->prepare($query);
            return $this->stmt->execute([$uid]);
        } catch (PDOException $e) {
            $this->logError('delete', $query, $e);
            return false;
        } finally {
            $this->closeCursor();
        }
    }

    public function fetch(string $query, array $params = []): ?array {
        try {
            $this->stmt = $this->pdo->prepare($query);
            $this->stmt->execute($params);
            return $this->stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logError('fetch', $query, $e);
            return null;
        } finally {
            $this->closeCursor();
        }
    }

    public function fetchAll(string $query, array $params = []): array {
        try {
            $this->stmt = $this->pdo->prepare($query);
            $this->stmt->execute($params);
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logError('fetchAll', $query, $e);
            return [];
        } finally {
            $this->closeCursor();
        }
    }

    public function beginTransaction(): void {
        $this->pdo->beginTransaction();
    }

    public function commit(): void {
        $this->pdo->commit();
    }

    public function rollBack(): void {
        $this->pdo->rollBack();
    }

    public function logError(string $method, string $query, PDOException $exception): void {
        $logData = [
            'table' => 'error_log',
            'method' => $method,
            'query' => $query,
            'error' => $exception->getMessage(),
            'registDate' => date('Y-m-d H:i:s')
        ];
        $this->insert($logData);
    }

    private function closeCursor(): void {
        if ($this->stmt) {
            $this->stmt->closeCursor();
        }
    }

    // 기타 추가 메서드
    public function checkRowExists(string $table, array $conditions): bool {
        $where = implode(' AND ', array_map(fn($key) => "$key = ?", array_keys($conditions)));
        $query = "SELECT COUNT(*) FROM $table WHERE $where";

        try {
            $this->stmt = $this->pdo->prepare($query);
            $this->stmt->execute(array_values($conditions));
            return (bool) $this->stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->logError('checkRowExists', $query, $e);
            return false;
        } finally {
            $this->closeCursor();
        }
    }
}
?>
