<?php
/**
 * CLMS 2.0 — DB.php
 * PDO singleton wrapper. Never use raw SQL strings — always use prepared statements.
 */

class DB
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
            );
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$pdo;
    }

    /**
     * Execute a SELECT query, return all rows
     * @param string $sql   Parameterised SQL
     * @param array  $bind  Positional or named bindings
     */
    public static function rows(string $sql, array $bind = []): array
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($bind);
        return $stmt->fetchAll();
    }

    /**
     * Execute a SELECT query, return single row (or null)
     */
    public static function row(string $sql, array $bind = []): ?array
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($bind);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Execute a SELECT query, return a single scalar value (or null)
     */
    public static function value(string $sql, array $bind = []): mixed
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($bind);
        $val = $stmt->fetchColumn();
        return $val !== false ? $val : null;
    }

    /**
     * Execute INSERT / UPDATE / DELETE, return affected row count
     */
    public static function execute(string $sql, array $bind = []): int
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($bind);
        return $stmt->rowCount();
    }

    /**
     * Insert a row, return last insert ID
     * @param string $table
     * @param array  $data  Associative array of column => value
     */
    public static function insert(string $table, array $data): int
    {
        $columns     = implode(', ', array_map(fn($c) => "`$c`", array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO `$table` ($columns) VALUES ($placeholders)";
        self::execute($sql, array_values($data));
        return (int) self::connection()->lastInsertId();
    }

    /**
     * Update rows matching $where
     * @param string $table
     * @param array  $data   Columns to update
     * @param array  $where  WHERE conditions (column => value, ANDed together)
     */
    public static function update(string $table, array $data, array $where): int
    {
        $set   = implode(', ', array_map(fn($c) => "`$c` = ?", array_keys($data)));
        $cond  = implode(' AND ', array_map(fn($c) => "`$c` = ?", array_keys($where)));
        $sql   = "UPDATE `$table` SET $set WHERE $cond";
        return self::execute($sql, [...array_values($data), ...array_values($where)]);
    }

    /**
     * Soft delete or hard delete
     * @param string $table
     * @param array  $where
     */
    public static function delete(string $table, array $where): int
    {
        $cond = implode(' AND ', array_map(fn($c) => "`$c` = ?", array_keys($where)));
        return self::execute("DELETE FROM `$table` WHERE $cond", array_values($where));
    }

    public static function beginTransaction(): void  { self::connection()->beginTransaction(); }
    public static function commit(): void            { self::connection()->commit(); }
    public static function rollback(): void          { self::connection()->rollBack(); }

    /** Run a callable inside a transaction; auto-rollback on exception */
    public static function transaction(callable $fn): mixed
    {
        self::beginTransaction();
        try {
            $result = $fn();
            self::commit();
            return $result;
        } catch (Throwable $e) {
            self::rollback();
            throw $e;
        }
    }
}
