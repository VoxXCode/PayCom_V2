<?php
/**
 * Base Controller untuk Seluruh Controller PayCom BLD
 */

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /**
     * Merender view di dalam layout header + footer standar PayCom BLD.
     *
     * @param array<string, mixed> $data
     */
    protected function render(string $view, array $data = []): void
    {
        $basePath = Router::basePath();
        $flash = $this->pullFlash();
        $errors = $this->pullErrors();
        $old = $this->pullOld();

        extract($data, EXTR_SKIP);

        $viewFile = dirname(__DIR__) . '/Views/' . ltrim($view, '/') . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View tidak ditemukan: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
            return;
        }

        require dirname(__DIR__) . '/Views/layouts/header.php';
        require $viewFile;
        require dirname(__DIR__) . '/Views/layouts/footer.php';
    }

    protected function redirect(string $path): void
    {
        $target = self::url($path);
        header('Location: ' . $target);
        exit;
    }

    protected function json(mixed $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['_flash'] = [
            'type'    => $type,
            'message' => $message,
        ];
    }

    /**
     * @return array{type: string, message: string}|null
     */
    protected function pullFlash(): ?array
    {
        $flash = $_SESSION['_flash'] ?? null;
        unset($_SESSION['_flash']);
        return is_array($flash) ? $flash : null;
    }

    /**
     * @param array<string, string> $errors
     */
    protected function setErrors(array $errors): void
    {
        $_SESSION['_errors'] = $errors;
    }

    /**
     * @return array<string, string>
     */
    protected function pullErrors(): array
    {
        $errors = $_SESSION['_errors'] ?? [];
        unset($_SESSION['_errors']);
        return is_array($errors) ? $errors : [];
    }

    /**
     * @param array<string, mixed> $input
     */
    protected function setOld(array $input): void
    {
        $_SESSION['_old'] = $input;
    }

    /**
     * @return array<string, mixed>
     */
    protected function pullOld(): array
    {
        $old = $_SESSION['_old'] ?? [];
        unset($_SESSION['_old']);
        return is_array($old) ? $old : [];
    }

    public static function url(string $path = '/'): string
    {
        $base = Router::basePath();
        $clean = '/' . ltrim($path, '/');
        if ($clean === '/') {
            return $base === '' ? '/' : $base . '/';
        }
        return $base . $clean;
    }

    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    public static function formatRupiah(int|float $amount): string
    {
        if ($amount <= 0) {
            return 'Est. Rp 0';
        }
        return 'Est. Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}

