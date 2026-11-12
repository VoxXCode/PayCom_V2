<?php
/**
 * Controller Halaman Utama & Konfigurator PC Builder
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\BuildModel;
use App\Models\ComponentModel;
use Throwable;

class BuilderController extends Controller
{
    public function index(): void
    {
        $dbConnected = false;
        $dbError = null;
        $groupedComponents = null;
        $recentBuilds = [];
        $preloadedBuild = null;
        $totalComponents = 0;
        $totalBuilds = 0;

        try {
            $componentModel = new ComponentModel();
            $buildModel = new BuildModel();

            $groupedComponents = $componentModel->getGroupedByType();
            $totalComponents = $componentModel->countTotal();
            $totalBuilds = $buildModel->countTotal();
            $recentBuilds = array_slice($buildModel->getAll(), 0, 3);

            // Jika ada parameter ?load_build=ID, muat rakitan dari database ke konfigurator
            $loadBuildId = isset($_GET['load_build']) ? (int) $_GET['load_build'] : 0;
            if ($loadBuildId > 0) {
                $loaded = $buildModel->findWithItems($loadBuildId);
                if ($loaded !== null) {
                    $preloadedBuild = $loaded;
                }
            }

            $dbConnected = true;
        } catch (Throwable $e) {
            $dbError = Database::getLastError() ?: $e->getMessage();
        }

        $this->render('builder/index', [
            'pageTitle'         => 'PayCom BLD - Custom PC Builder & Platform Perakit PC Cerdas',
            'activeNav'         => 'builder',
            'dbConnected'       => $dbConnected,
            'dbError'           => $dbError,
            'groupedComponents' => $groupedComponents,
            'recentBuilds'      => $recentBuilds,
            'preloadedBuild'    => $preloadedBuild,
            'totalComponents'   => $totalComponents,
            'totalBuilds'       => $totalBuilds,
        ]);
    }

    public function apiComponents(): void
    {
        try {
            $model = new ComponentModel();
            $this->json([
                'status' => 'success',
                'data'   => $model->getGroupedByType(),
            ]);
        } catch (Throwable $e) {
            $this->json([
                'status'  => 'error',
                'message' => 'Gagal memuat katalog komponen dari MySQL: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function apiPriceEstimate(): void
    {
        $query = trim((string) ($_GET['query'] ?? ''));
        $this->json([
            'status'    => 'success',
            'query'     => $query,
            'timestamp' => date(DATE_ATOM),
            'note'      => 'API Penarikan Estimasi Harga Online Terintegrasi (PHP MVC)',
        ]);
    }
}

