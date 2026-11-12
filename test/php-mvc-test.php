<?php
/**
 * Pengujian Otomatis Arsitektur PHP MVC, Router, CompatibilityService,
 * dan Validasi Input Controller PayCom BLD.
 */

declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = dirname(__DIR__) . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

use App\Controllers\BuildController;
use App\Controllers\BuilderController;
use App\Controllers\ComponentController;
use App\Core\CompatibilityService;
use App\Core\Router;
use App\Models\ComponentModel;

$passed = 0;
$failed = 0;

function testAssert(bool $cond, string $label): void
{
    global $passed, $failed;
    if ($cond) {
        echo "PASS: {$label}\n";
        $passed++;
    } else {
        echo "FAIL: {$label}\n";
        $failed++;
    }
}

echo "=== PENGUJIAN PHP MVC PAYCOM BLD ===\n";

// 1. Router GET & POST Routes
$router = new Router();
$router->get('/', [BuilderController::class, 'index']);
$router->get('/components', [ComponentController::class, 'index']);
$router->get('/builds', [BuildController::class, 'index']);
$router->post('/components/store', [ComponentController::class, 'store']);
$router->post('/builds/store', [BuildController::class, 'store']);

$routes = $router->getRoutes();
testAssert(count($routes['GET']) >= 2, 'Router memiliki minimal 2 rute GET');
testAssert(count($routes['POST']) >= 2, 'Router memiliki minimal 2 rute POST');

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
testAssert(Router::basePath() === '', 'Router::basePath() mengembalikan string kosong (bukan backslash) di Windows');

// 2. Validasi Input di ComponentController
$compCtrl = new ComponentController();

[$cleanBadCpu, $errBadCpu] = $compCtrl->validateComponentInput([
    'type'          => 'cpu',
    'name'          => 'AB',
    'base_price'    => '-500',
    'socket'        => '',
    'tdp'           => '2000',
    'specs_summary' => '',
]);
testAssert(isset($errBadCpu['name']), 'ComponentController menolak nama < 3 karakter');
testAssert(isset($errBadCpu['base_price']), 'ComponentController menolak harga negatif');
testAssert(isset($errBadCpu['socket']), 'ComponentController mewajibkan socket untuk CPU');
testAssert(isset($errBadCpu['tdp']), 'ComponentController menolak TDP > 1000W');
testAssert(isset($errBadCpu['specs_summary']), 'ComponentController mewajibkan ringkasan spesifikasi');

[$cleanBadPsu, $errBadPsu] = $compCtrl->validateComponentInput([
    'type'          => 'psu',
    'name'          => 'PSU Test 500W',
    'base_price'    => '500000',
    'wattage'       => '50',
    'specs_summary' => '50W tidak valid',
]);
testAssert(isset($errBadPsu['wattage']), 'ComponentController menolak kapasitas PSU < 200W');

[$cleanGoodMobo, $errGoodMobo] = $compCtrl->validateComponentInput([
    'type'          => 'motherboard',
    'name'          => 'MSI PRO B760M-P DDR4',
    'base_price'    => '1650000',
    'socket'        => 'lga1700',
    'ram_type'      => 'DDR4',
    'form_factor'   => 'mATX',
    'tdp'           => '0',
    'specs_summary' => 'Socket LGA1700, mATX, DDR4',
]);
testAssert(empty($errGoodMobo), 'ComponentController meloloskan data Motherboard valid');
testAssert($cleanGoodMobo['socket'] === 'LGA1700', 'ComponentController menormalisasi socket menjadi huruf kapital');

// 3. Validasi Input di BuildController (dengan Stub ComponentModel tanpa DB)
$stubModel = new class extends ComponentModel {
    private array $rows = [
        1 => ['id' => 1, 'slug' => 'cpu-i3', 'type' => 'cpu', 'name' => 'i3-12100F', 'base_price' => 1350000, 'socket' => 'LGA1700', 'tdp' => 58, 'has_igpu' => 0],
        9 => ['id' => 9, 'slug' => 'mobo-h610', 'type' => 'motherboard', 'name' => 'H610M', 'base_price' => 1100000, 'socket' => 'LGA1700', 'ram_type' => 'DDR4', 'form_factor' => 'mATX', 'tdp' => 0],
        18 => ['id' => 18, 'slug' => 'ram-16', 'type' => 'ram', 'name' => '16GB DDR4', 'base_price' => 520000, 'ram_type' => 'DDR4', 'tdp' => 0],
        23 => ['id' => 23, 'slug' => 'rx-6600', 'type' => 'gpu', 'name' => 'RX 6600', 'base_price' => 3250000, 'tdp' => 132],
        28 => ['id' => 28, 'slug' => 'ssd-500', 'type' => 'storage', 'name' => 'SSD 500GB', 'base_price' => 540000, 'tdp' => 5],
        32 => ['id' => 32, 'slug' => 'psu-550', 'type' => 'psu', 'name' => 'PSU 550W', 'base_price' => 630000, 'wattage' => 550, 'tdp' => 0],
        37 => ['id' => 37, 'slug' => 'case-matx', 'type' => 'case', 'name' => 'Case mATX', 'base_price' => 360000, 'supported_form_factors' => 'mATX,ITX', 'tdp' => 0],
    ];

    public function findById(int $id): ?array
    {
        return $this->rows[$id] ?? null;
    }

    public function findBySlug(string $slug): ?array
    {
        foreach ($this->rows as $row) {
            if ($row['slug'] === $slug) {
                return $row;
            }
        }
        return null;
    }
};

$buildCtrl = new BuildController();

[$badBuild, $badItems, $errBadBuild] = $buildCtrl->validateBuildInput([
    'build_name'   => 'A',
    'builder_name' => '',
    'purpose'      => 'Cryptomining',
    'components'   => [],
], $stubModel);
testAssert(isset($errBadBuild['build_name']), 'BuildController menolak nama rakitan < 3 karakter');
testAssert(isset($errBadBuild['builder_name']), 'BuildController menolak nama perakit kosong');
testAssert(isset($errBadBuild['purpose']), 'BuildController menolak purpose di luar whitelist');
testAssert(isset($errBadBuild['components']), 'BuildController mewajibkan minimal 1 komponen');

[$goodBuild, $goodItems, $errGoodBuild] = $buildCtrl->validateBuildInput([
    'build_name'   => 'PC Esports 1080p',
    'builder_name' => 'Dicky_208',
    'purpose'      => 'Gaming',
    'notes'        => 'Build hemat',
    'components'   => [
        'cpu'         => '1',
        'motherboard' => '9',
        'ram'         => '18',
        'gpu'         => '23',
        'storage'     => '28',
        'psu'         => '32',
        'case'        => '37',
    ],
], $stubModel);
testAssert(empty($errGoodBuild), 'BuildController meloloskan rakitan lengkap & valid');
testAssert($goodBuild['total_price'] === 7750000, 'Server-side total harga akurat (Rp 7.750.000)');
testAssert($goodBuild['total_wattage'] === 240, 'Server-side total wattage akurat (240W)');
testAssert($goodBuild['recommended_psu'] === 400, 'Server-side rekomendasi PSU akurat (400W)');
testAssert($goodBuild['compatibility_status'] === '100% Kompatibel', 'Server-side status kompatibilitas akurat (100% Kompatibel)');

// 4. CompatibilityService Deteksi Ketidakcocokan
$mismatchEval = CompatibilityService::evaluate([
    'cpu'         => ['id' => 1, 'socket' => 'LGA1700', 'tdp' => 58, 'has_igpu' => 0, 'base_price' => 1350000],
    'motherboard' => ['id' => 13, 'socket' => 'AM4', 'ram_type' => 'DDR4', 'form_factor' => 'ATX', 'base_price' => 850000],
    'case'        => ['id' => 40, 'supported_form_factors' => 'ITX', 'base_price' => 990000],
]);
testAssert($mismatchEval['compatibility_status'] === 'Tidak Kompatibel', 'CompatibilityService mendeteksi status Tidak Kompatibel');
testAssert(count($mismatchEval['errors']) === 3, 'CompatibilityService mendeteksi 3 error (socket, form factor, no iGPU)');

// 5. Render Smoke Test untuk Rute GET Utama (tanpa error/notice PHP)
$renderCases = [
    '/'                  => 'PayCom Configurator',
    '/components'        => 'Katalog Komponen Hardware',
    '/components/create' => 'Tambah Komponen Hardware Baru',
    '/builds'            => 'Rakitan PC Tersimpan',
    '/builds/create'     => 'Buat &amp; Simpan Rakitan PC Baru',
    '/rute-tidak-ada'    => 'Halaman Tidak Ditemukan',
];

foreach ($renderCases as $uri => $expectedNeedle) {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    http_response_code(200);
    ob_start();
    require dirname(__DIR__) . '/index.php';
    $html = (string) ob_get_clean();
    testAssert(str_contains($html, $expectedNeedle), "Render rute GET {$uri} memuat '{$expectedNeedle}'");
    if ($uri === '/') {
        testAssert(str_contains($html, 'href="/css/styles.css?v=8"'), 'Link CSS utama menggunakan path /css/styles.css yang valid');
        testAssert(!str_contains($html, '\\/css/styles.css'), 'Link CSS tidak mengandung backslash Windows');
    }
}

echo "\n=== HASIL PENGUJIAN PHP MVC: {$passed} PASS, {$failed} FAIL ===\n";
exit($failed > 0 ? 1 : 0);
