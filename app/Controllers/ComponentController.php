<?php
/**
 * Controller CRUD Katalog Komponen Hardware (`components`)
 * Menerapkan validasi input lengkap langsung di controller.
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ComponentModel;
use Throwable;

class ComponentController extends Controller
{
    public function index(): void
    {
        $selectedType = trim((string) ($_GET['type'] ?? ''));
        $searchQuery = trim((string) ($_GET['q'] ?? ''));

        $components = [];
        $dbError = null;

        try {
            $model = new ComponentModel();
            $components = $model->getAll($selectedType !== '' ? $selectedType : null, $searchQuery);
        } catch (Throwable $e) {
            $dbError = $e->getMessage();
        }

        $this->render('components/index', [
            'pageTitle'    => 'Katalog Komponen Hardware - PayCom BLD',
            'activeNav'    => 'components',
            'components'   => $components,
            'selectedType' => $selectedType,
            'searchQuery'  => $searchQuery,
            'typeLabels'   => ComponentModel::TYPE_LABELS,
            'dbError'      => $dbError,
        ]);
    }

    public function create(): void
    {
        $this->render('components/create', [
            'pageTitle'  => 'Tambah Komponen Baru - PayCom BLD',
            'activeNav'  => 'components',
            'typeLabels' => ComponentModel::TYPE_LABELS,
        ]);
    }

    public function store(): void
    {
        $input = $_POST;
        [$clean, $errors] = $this->validateComponentInput($input);

        if (!empty($errors)) {
            $this->setErrors($errors);
            $this->setOld($input);
            $this->setFlash('error', 'Validasi gagal. Mohon periksa kembali isian formulir komponen.');
            $this->redirect('/components/create');
        }

        try {
            $model = new ComponentModel();
            $clean['slug'] = $model->generateUniqueSlug($clean['type'], $clean['name']);
            $clean['is_custom'] = 1;
            $model->create($clean);

            $this->setFlash('success', 'Komponen "' . $clean['name'] . '" berhasil ditambahkan ke katalog database.');
            $this->redirect('/components');
        } catch (Throwable $e) {
            $this->setOld($input);
            $this->setFlash('error', 'Gagal menyimpan komponen ke database: ' . $e->getMessage());
            $this->redirect('/components/create');
        }
    }

    public function edit(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            $this->setFlash('error', 'ID komponen tidak valid.');
            $this->redirect('/components');
        }

        try {
            $model = new ComponentModel();
            $component = $model->findById($id);
            if ($component === null) {
                $this->setFlash('error', 'Komponen tidak ditemukan di database.');
                $this->redirect('/components');
            }

            $this->render('components/edit', [
                'pageTitle'  => 'Edit Komponen - PayCom BLD',
                'activeNav'  => 'components',
                'component'  => $component,
                'typeLabels' => ComponentModel::TYPE_LABELS,
            ]);
        } catch (Throwable $e) {
            $this->setFlash('error', 'Gagal mengambil data komponen: ' . $e->getMessage());
            $this->redirect('/components');
        }
    }

    public function update(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            $this->setFlash('error', 'ID komponen tidak valid.');
            $this->redirect('/components');
        }

        $input = $_POST;
        [$clean, $errors] = $this->validateComponentInput($input);

        if (!empty($errors)) {
            $this->setErrors($errors);
            $this->setOld($input);
            $this->setFlash('error', 'Validasi gagal. Periksa kembali kolom yang bertanda merah.');
            $this->redirect('/components/edit?id=' . $id);
        }

        try {
            $model = new ComponentModel();
            $existing = $model->findById($id);
            if ($existing === null) {
                $this->setFlash('error', 'Komponen yang ingin diubah tidak ditemukan.');
                $this->redirect('/components');
            }

            $model->update($id, $clean);
            $this->setFlash('success', 'Data komponen "' . $clean['name'] . '" berhasil diperbarui.');
            $this->redirect('/components');
        } catch (Throwable $e) {
            $this->setOld($input);
            $this->setFlash('error', 'Gagal memperbarui komponen: ' . $e->getMessage());
            $this->redirect('/components/edit?id=' . $id);
        }
    }

    public function delete(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            $this->setFlash('error', 'ID komponen tidak valid untuk dihapus.');
            $this->redirect('/components');
        }

        try {
            $model = new ComponentModel();
            $component = $model->findById($id);
            if ($component === null) {
                $this->setFlash('error', 'Komponen tidak ditemukan.');
                $this->redirect('/components');
            }

            if ($model->isUsedInBuilds($id)) {
                $this->setFlash(
                    'error',
                    'Komponen "' . $component['name'] . '" tidak dapat dihapus karena sedang digunakan pada rakitan tersimpan.'
                );
                $this->redirect('/components');
            }

            $model->delete($id);
            $this->setFlash('success', 'Komponen "' . $component['name'] . '" berhasil dihapus.');
        } catch (Throwable $e) {
            $this->setFlash('error', 'Gagal menghapus komponen: ' . $e->getMessage());
        }

        $this->redirect('/components');
    }

    /**
     * Validasi Input Komponen di Controller.
     * Memastikan tipe kategori, harga, TDP, socket, RAM type, form factor, dan panjang string sesuai aturan.
     *
     * @param array<string, mixed> $input
     * @return array{0: array<string, mixed>, 1: array<string, string>}
     */
    public function validateComponentInput(array $input): array
    {
        $errors = [];

        $type = trim((string) ($input['type'] ?? ''));
        $name = trim((string) ($input['name'] ?? ''));
        $basePriceRaw = trim((string) ($input['base_price'] ?? ''));
        $socket = strtoupper(trim((string) ($input['socket'] ?? '')));
        $tdpRaw = trim((string) ($input['tdp'] ?? '0'));
        $hasIgpu = !empty($input['has_igpu']) ? 1 : 0;
        $formFactor = trim((string) ($input['form_factor'] ?? ''));
        $ramType = strtoupper(trim((string) ($input['ram_type'] ?? '')));
        $capacityGbRaw = trim((string) ($input['capacity_gb'] ?? ''));
        $speedMhzRaw = trim((string) ($input['speed_mhz'] ?? ''));
        $wattageRaw = trim((string) ($input['wattage'] ?? ''));
        $supportedFfRaw = trim((string) ($input['supported_form_factors'] ?? ''));
        $specsSummary = trim((string) ($input['specs_summary'] ?? ''));

        if (!in_array($type, ComponentModel::ALLOWED_TYPES, true)) {
            $errors['type'] = 'Kategori komponen tidak valid.';
        }

        if ($name === '') {
            $errors['name'] = 'Nama komponen wajib diisi.';
        } elseif (mb_strlen($name) < 3 || mb_strlen($name) > 150) {
            $errors['name'] = 'Nama komponen harus antara 3 hingga 150 karakter.';
        }

        if ($basePriceRaw === '' || !ctype_digit($basePriceRaw)) {
            $errors['base_price'] = 'Estimasi harga harus berupa angka bulat positif (tanpa titik/koma).';
        } elseif ((int) $basePriceRaw > 500000000) {
            $errors['base_price'] = 'Estimasi harga terlalu besar (maksimal Rp 500.000.000).';
        }

        if ($tdpRaw !== '' && (!ctype_digit($tdpRaw) || (int) $tdpRaw > 1000)) {
            $errors['tdp'] = 'TDP harus berupa angka antara 0 hingga 1000 Watt.';
        }

        // Validasi kondisional per kategori hardware
        if (in_array($type, ['cpu', 'motherboard'], true)) {
            if ($socket === '') {
                $errors['socket'] = 'Socket wajib diisi untuk kategori CPU dan Motherboard (contoh: LGA1700, AM4, AM5).';
            } elseif (!preg_match('/^[A-Z0-9\-]{2,30}$/', $socket)) {
                $errors['socket'] = 'Format socket hanya boleh berisi huruf kapital, angka, dan tanda hubung.';
            }
        }

        if (in_array($type, ['motherboard', 'ram'], true)) {
            if (!in_array($ramType, ['DDR4', 'DDR5'], true)) {
                $errors['ram_type'] = 'Tipe RAM wajib dipilih (DDR4 atau DDR5).';
            }
        }

        if ($type === 'motherboard') {
            if (!in_array($formFactor, ['ATX', 'mATX', 'ITX'], true)) {
                $errors['form_factor'] = 'Form factor motherboard wajib dipilih (ATX, mATX, atau ITX).';
            }
        }

        if ($type === 'ram') {
            if ($capacityGbRaw !== '' && (!ctype_digit($capacityGbRaw) || (int) $capacityGbRaw < 1 || (int) $capacityGbRaw > 512)) {
                $errors['capacity_gb'] = 'Kapasitas RAM harus antara 1 hingga 512 GB.';
            }
            if ($speedMhzRaw !== '' && (!ctype_digit($speedMhzRaw) || (int) $speedMhzRaw < 800 || (int) $speedMhzRaw > 12000)) {
                $errors['speed_mhz'] = 'Kecepatan RAM harus antara 800 hingga 12000 MHz.';
            }
        }

        if ($type === 'psu') {
            if ($wattageRaw === '' || !ctype_digit($wattageRaw) || (int) $wattageRaw < 200 || (int) $wattageRaw > 2500) {
                $errors['wattage'] = 'Kapasitas daya PSU wajib diisi antara 200 hingga 2500 Watt.';
            }
        }

        $normalizedSupportedFf = null;
        if ($type === 'case') {
            if ($supportedFfRaw === '') {
                $errors['supported_form_factors'] = 'Dukungan form factor casing wajib diisi (contoh: ATX,mATX,ITX).';
            } else {
                $parts = array_values(array_filter(array_map('trim', explode(',', $supportedFfRaw))));
                $validFf = ['ATX', 'mATX', 'ITX'];
                $invalidParts = array_diff($parts, $validFf);
                if (empty($parts) || !empty($invalidParts)) {
                    $errors['supported_form_factors'] = 'Gunakan daftar form factor valid dipisahkan koma: ATX, mATX, ITX.';
                } else {
                    $normalizedSupportedFf = implode(',', array_unique($parts));
                }
            }
        }

        if ($specsSummary === '') {
            $errors['specs_summary'] = 'Ringkasan spesifikasi wajib diisi agar mudah dipahami pemula.';
        } elseif (mb_strlen($specsSummary) > 255) {
            $errors['specs_summary'] = 'Ringkasan spesifikasi maksimal 255 karakter.';
        }

        $clean = [
            'type'                   => $type,
            'name'                   => $name,
            'base_price'             => ctype_digit($basePriceRaw) ? (int) $basePriceRaw : 0,
            'socket'                 => in_array($type, ['cpu', 'motherboard'], true) ? $socket : null,
            'tdp'                    => ctype_digit($tdpRaw) ? (int) $tdpRaw : 0,
            'has_igpu'               => $type === 'cpu' ? $hasIgpu : 0,
            'form_factor'            => $type === 'motherboard' ? $formFactor : null,
            'ram_type'               => in_array($type, ['motherboard', 'ram'], true) ? $ramType : null,
            'capacity_gb'            => ($type === 'ram' && ctype_digit($capacityGbRaw)) ? (int) $capacityGbRaw : null,
            'speed_mhz'              => ($type === 'ram' && ctype_digit($speedMhzRaw)) ? (int) $speedMhzRaw : null,
            'wattage'                => ($type === 'psu' && ctype_digit($wattageRaw)) ? (int) $wattageRaw : null,
            'supported_form_factors' => $type === 'case' ? $normalizedSupportedFf : null,
            'specs_summary'          => $specsSummary,
        ];

        return [$clean, $errors];
    }
}

