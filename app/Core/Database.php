<?php
/**
 * Core Database Connection Wrapper (PDO MySQL Singleton)
 */

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static ?string $lastError = null;

    /**
     * Mengambil instance PDO aktif (atau null jika koneksi gagal).
     */
    public static function getConnection(): ?PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $config = require dirname(__DIR__, 2) . '/config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['dbname'],
            $config['charset']
        );

        try {
            self::$instance = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            self::$lastError = null;
            return self::$instance;
        } catch (PDOException $e) {
            self::$lastError = $e->getMessage();
            return null;
        }
    }

    /**
     * Mengambil instance PDO dan melempar exception jika database belum terhubung.
     *
     * @throws PDOException
     */
    public static function requireConnection(): PDO
    {
        $pdo = self::getConnection();
        if ($pdo === null) {
            throw new PDOException(self::$lastError ?? 'Koneksi ke database MySQL gagal.');
        }
        return $pdo;
    }

    public static function getLastError(): ?string
    {
        return self::$lastError;
    }

    /**
     * Memungkinkan injeksi koneksi PDO (berguna untuk pengujian otomatis).
     */
    public static function setConnection(?PDO $pdo): void
    {
        self::$instance = $pdo;
    }
}

