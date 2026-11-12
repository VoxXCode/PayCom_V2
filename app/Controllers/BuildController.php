<?php
/**
 * Controller CRUD Rakitan PC Tersimpan (`pc_builds` & `build_items`)
 * Menerapkan validasi input lengkap di controller + kalkulasi kompatibilitas server-side.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\CompatibilityService;
use App\Core\Controller;
use App\Models\BuildModel;
use App\Models\ComponentModel;
use Throwable;

class BuildController extends Controller
{
    public function index(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $purpose = trim((string) ($_GET['purpose'] ?? ''));
        $builds = [];
        $dbError = null;

        try {
            $model = new BuildModel();
            $builds = $model->getAll($search, $purpose);
        } catch (Throwable $e) {
            $dbError = $e->getMessage();
        }

        $this->render('builds/index', [
            'pageTitle'       => 'Rakitan PC Tersimpan - PayCom BLD',
            'activeNav'       => 'builds',
            'builds'          => $builds,
            'searchQuery'     => $search,
            'selectedPurpose' => $purpose,
            'purposes'        => BuildModel::ALLOWED_PURPOSES,
            'dbError'         => $dbError,
        ]);
    }

    public function show(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            $this->setFlash('error', 'ID rakitan tidak valid.');
            $this->redirect('/builds');
        }

        try {
            $model = new BuildModel();
            $build = $model->findWithItems($id);
            if ($build === null) {
                $this->setFlash('error', 'Data rakitan PC tidak ditemukan.');
                $this->redirect('/builds');
            }

            $evaluation = CompatibilityService::evaluate($build['items']);

            $this->render('builds/show', [
                'pageTitle'  => $build['build_name'] . ' - Detail Rakitan PayCom BLD',
                'activeNav'  => 'builds',
                'build'      => $build,
                'evaluation' => $evaluation,
                'typeLabels' => ComponentModel::TYPE_LABELS,
            ]);
        } catch (Throwable $e) {
            $this->setFlash('error', 'Gagal memuat detail rakitan: ' . $e->getMessage());
            $this->redirect('/builds');
        }
    }

    public function create(): void
    {
        $groupedComponents = [];
        $dbError = null;

        try {
            $componentModel = new ComponentModel();
            $groupedComponents = $componentModel->getGroupedByType();
        } catch (Throwable $e) {
            $dbError = $e->getMessage();
        }

        $this->render('builds/create', [
            'pageTitle'         => 'Buat & Simpan Rakitan Baru - PayCom BLD',
            'activeNav'         => 'builds',
            'groupedComponents' => $groupedComponents,
            'purposes'          => BuildModel::ALLOWED_PURPOSES,
            'typeLabels'        => ComponentModel::TYPE_LABELS,
            'dbError'           => $dbError,
        ]);
    }

    public function store(): void
    {
        $input = $_POST;
        $redirectTarget = !empty($input['from_builder']) ? '/' : '/builds/create';

        try {
            $componentModel = new ComponentModel();
            [$cleanBuild, $cleanItems, $errors] = $this->validateBuildInput($input, $componentModel);

            if (!empty($errors)) {
                $this->setErrors($errors);
                $this->setOld($input);
                $firstError = reset($errors);
                $this->setFlash('error', 'Validasi rakitan gagal: ' . ($firstError ?: 'Periksa kembali input Anda.'));
                $this->redirect($redirectTarget);
            }

            $buildModel = new BuildModel();
            $newId = $buildModel->createWithItems($cleanBuild, $cleanItems);

            $this->setFlash('success', 'Rakitan "' . $cleanBuild['build_name'] . '" berhasil disimpan ke database MySQL!');
            $this->redirect('/builds/show?id=' . $newId);
        } catch (Throwable $e) {
            $this->setOld($input);
            $this->setFlash('error', 'Gagal menyimpan rakitan: ' . $e->getMessage());
            $this->redirect($redirectTarget);
        }
    }

    public function edit(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            $this->setFlash('error', 'ID rakitan tidak valid.');
            $this->redirect('/builds');
        }

        try {
            $buildModel = new BuildModel();
            $build = $buildModel->findWithItems($id);
            if ($build === null) {
                $this->setFlash('error', 'Rakitan tidak ditemukan.');
                $this->redirect('/builds');
            }

            $componentModel = new ComponentModel();
            $groupedComponents = $componentModel->getGroupedByType();

            $this->render('builds/edit', [
                'pageTitle'         => 'Edit Rakitan - ' . $build['build_name'],
                'activeNav'         => 'builds',
                'build'             => $build,
                'groupedComponents' => $groupedComponents,
                'purposes'          => BuildModel::ALLOWED_PURPOSES,
                'typeLabels'        => ComponentModel::TYPE_LABELS,
            ]);
        } catch (Throwable $e) {
            $this->setFlash('error', 'Gagal memuat formulir edit rakitan: ' . $e->getMessage());
            $this->redirect('/builds');
        }
    }

    public function update(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            $this->setFlash('error', 'ID rakitan tidak valid.');
            $this->redirect('/builds');
        }

        $input = $_POST;

        try {
            $buildModel = new BuildModel();
            $existing = $buildModel->findWithItems($id);
            if ($existing === null) {
                $this->setFlash('error', 'Rakitan yang ingin diperbarui tidak ditemukan.');
                $this->redirect('/builds');
            }

            $componentModel = new ComponentModel();
            [$cleanBuild, $cleanItems, $errors] = $this->validateBuildInput($input, $componentModel);

            if (!empty($errors)) {
                $this->setErrors($errors);
                $this->setOld($input);
                $this->setFlash('error', 'Validasi perubahan rakitan gagal. Mohon periksa kembali formulir.');
                $this->redirect('/builds/edit?id=' . $id);
            }

            $buildModel->updateWithItems($id, $cleanBuild, $cleanItems);
            $this->setFlash('success', 'Rakitan "' . $cleanBuild['build_name'] . '" berhasil diperbarui!');
            $this->redirect('/builds/show?id=' . $id);
        } catch (Throwable $e) {
            $this->setOld($input);
            $this->setFlash('error', 'Gagal memperbarui rakitan: ' . $e->getMessage());
            $this->redirect('/builds/edit?id=' . $id);
        }
    }

    public function delete(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            $this->setFlash('error', 'ID rakitan tidak valid.');
            $this->redirect('/builds');
        }

        try {
            $model = new BuildModel();
            $existing = $model->findWithItems($id);
            if ($existing === null) {
                $this->setFlash('error', 'Rakitan tidak ditemukan.');
                $this->redirect('/builds');
            }

            $model->delete($id);
            $this->setFlash('success', 'Rakitan "' . $existing['build_name'] . '" berhasil dihapus dari database.');
        } catch (Throwable $e) {
            $this->setFlash('error', 'Gagal menghapus rakitan: ' . $e->getMessage());
        }

        $this->redirect('/builds');
    }

    /**
     * Validasi Input Rakitan PC di Controller.
     * - Memvalidasi nama rakitan, nama perakit, tujuan penggunaan, catatan.
     * - Memvalidasi setiap ID/slug komponen terhadap tabel `components` di database.
     * - Menghitung ulang total harga, wattage, PSU minimum, dan status kompatibilitas di server.
     *
     * @param array<string, mixed> $input
     * @return array{0: array<string, mixed>, 1: array<string, array{component_id: int, custom_price: ?int}>, 2: array<string, string>}
     */
    public function validateBuildInput(array $input, ComponentModel $componentModel): array
    {
        $errors = [];

        $buildName = trim((string) ($input['build_name'] ?? ''));
        $builderName = trim((string) ($input['builder_name'] ?? ''));
        $purpose = trim((string) ($input['purpose'] ?? ''));
        $notes = trim((string) ($input['notes'] ?? ''));

        if ($buildName === '') {
            $errors['build_name'] = 'Nama rakitan wajib diisi.';
        } elseif (mb_strlen($buildName) < 3 || mb_strlen($buildName) > 120) {
            $errors['build_name'] = 'Nama rakitan harus antara 3 hingga 120 karakter.';
        }

        if ($builderName === '') {
            $errors['builder_name'] = 'Nama perakit wajib diisi.';
        } elseif (mb_strlen($builderName) < 2 || mb_strlen($builderName) > 80) {
            $errors['builder_name'] = 'Nama perakit harus antara 2 hingga 80 karakter.';
        }

        if (!in_array($purpose, BuildModel::ALLOWED_PURPOSES, true)) {
            $errors['purpose'] = 'Tujuan penggunaan rakitan tidak valid.';
        }

        if (mb_strlen($notes) > 500) {
            $errors['notes'] = 'Catatan rakitan maksimal 500 karakter.';
        }

        // Validasi komponen yang dikirim melalui `components[category]` (bisa berupa DB id atau slug)
        $rawComponents = is_array($input['components'] ?? null) ? $input['components'] : [];
        $rawCustomPrices = is_array($input['custom_prices'] ?? null) ? $input['custom_prices'] : [];

        $cleanItems = [];
        $selectedRows = [];

        foreach (CompatibilityService::CATEGORIES as $cat) {
            $val = trim((string) ($rawComponents[$cat] ?? ''));
            if ($val === '') {
                continue;
            }

            $compRow = ctype_digit($val)
                ? $componentModel->findById((int) $val)
                : $componentModel->findBySlug($val);

            if ($compRow === null) {
                $errors['components_' . $cat] = 'Komponen untuk kategori ' . strtoupper($cat) . ' tidak ditemukan di database.';
                continue;
            }

            if ($compRow['type'] !== $cat) {
                $errors['components_' . $cat] = 'Kategori komponen tidak sesuai untuk slot ' . strtoupper($cat) . '.';
                continue;
            }

            $customPrice = null;
            if (isset($rawCustomPrices[$cat]) && trim((string) $rawCustomPrices[$cat]) !== '') {
                $cpRaw = trim((string) $rawCustomPrices[$cat]);
                if (!ctype_digit($cpRaw) || (int) $cpRaw > 500000000) {
                    $errors['components_' . $cat] = 'Harga manual untuk ' . strtoupper($cat) . ' tidak valid.';
                    continue;
                }
                $customPrice = (int) $cpRaw;
            }

            $compRow['custom_price'] = $customPrice;
            $selectedRows[$cat] = $compRow;
            $cleanItems[$cat] = [
                'component_id' => (int) $compRow['id'],
                'custom_price' => $customPrice,
            ];
        }

        if (empty($cleanItems)) {
            $errors['components'] = 'Pilih minimal 1 komponen hardware sebelum menyimpan rakitan.';
        }

        $eval = CompatibilityService::evaluate($selectedRows);

        $cleanBuild = [
            'build_name'           => $buildName,
            'builder_name'         => $builderName,
            'purpose'              => $purpose,
            'notes'                => $notes,
            'total_price'          => $eval['total_price'],
            'total_wattage'        => $eval['total_wattage'],
            'recommended_psu'      => $eval['recommended_psu'],
            'compatibility_status' => $eval['compatibility_status'],
        ];

        return [$cleanBuild, $cleanItems, $errors];
    }
}

