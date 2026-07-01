<?php
/**
 * Database — Clase de Conexión (Singleton)
 * Gestiona la conexión PDO y expone métodos reutilizables.
 */
class Database
{
    private static ?PDO $conn = null;

    // ── Credenciales ──────────────────────────────────────────
    private string $host   = '127.1.1.1';
    private string $dbname = 'parcial_practico';
    private string $user   = 'root';
    private string $pass   = '';

    private function __construct() {}   // No instanciar directamente

    // ── Obtener conexión (crea una sola vez) ──────────────────
    public static function getConnection(): PDO
    {
        if (self::$conn === null) {
            $db = new self();
            try {
                self::$conn = new PDO(
                    "mysql:host={$db->host};dbname={$db->dbname};charset=utf8mb4",
                    $db->user,
                    $db->pass,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                http_response_code(500);
                die('<h3 style="color:red">Error de conexión BD: ' . htmlspecialchars($e->getMessage()) . '</h3>');
            }
        }
        return self::$conn;
    }

    // ── Preparar y ejecutar ───────────────────────────────────
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // ── Devuelve todos los registros ──────────────────────────
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    // ── Devuelve un solo registro ─────────────────────────────
    public static function fetchOne(string $sql, array $params = []): array|false
    {
        return self::query($sql, $params)->fetch();
    }

    // ── Último ID insertado ───────────────────────────────────
    public static function lastId(): string
    {
        return self::getConnection()->lastInsertId();
    }

    // ── Transacción ───────────────────────────────────────────
    public static function beginTransaction(): void
    {
        self::getConnection()->beginTransaction();
    }

    public static function commit(): void
    {
        self::getConnection()->commit();
    }

    public static function rollBack(): void
    {
        self::getConnection()->rollBack();
    }
}
