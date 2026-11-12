<?php
/**
 * Front Controller - PayCom BLD (PHP MVC + MySQL PDO)
 * Seluruh rute GET & POST didaftarkan di sini.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// Autoloader PSR-4 sederhana untuk namespace App\
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

use App\Controllers\BuildController;
use App\Controllers\BuilderController;
use App\Controllers\ComponentController;
use App\Core\Router;

$router = new Router();

// ----------------------------------------------------------------------------
// Rute GET (Halaman & API JSON)
// ----------------------------------------------------------------------------
$router->get('/', [BuilderController::class, 'index']);
$router->get('/api/components', [BuilderController::class, 'apiComponents']);
$router->get('/api/price-estimate', [BuilderController::class, 'apiPriceEstimate']);

// CRUD Katalog Komponen (GET)
$router->get('/components', [ComponentController::class, 'index']);
$router->get('/components/create', [ComponentController::class, 'create']);
$router->get('/components/edit', [ComponentController::class, 'edit']);

// CRUD Rakitan PC Tersimpan (GET)
$router->get('/builds', [BuildController::class, 'index']);
$router->get('/builds/show', [BuildController::class, 'show']);
$router->get('/builds/create', [BuildController::class, 'create']);
$router->get('/builds/edit', [BuildController::class, 'edit']);

// ----------------------------------------------------------------------------
// Rute POST (Mutasi Data + Validasi Input di Controller)
// ----------------------------------------------------------------------------
$router->post('/components/store', [ComponentController::class, 'store']);
$router->post('/components/update', [ComponentController::class, 'update']);
$router->post('/components/delete', [ComponentController::class, 'delete']);

$router->post('/builds/store', [BuildController::class, 'store']);
$router->post('/builds/update', [BuildController::class, 'update']);
$router->post('/builds/delete', [BuildController::class, 'delete']);

$router->dispatch();
