<?php
use App\Core\Controller;

$basePath = \App\Core\Router::basePath();
$pageTitle = '404 - Halaman Tidak Ditemukan | PayCom BLD';
$activeNav = '';
require dirname(__DIR__) . '/layouts/header.php';
?>

<main class="flex-1 w-full max-w-4xl mx-auto px-4 py-20 text-center">
  <div class="bg-[#141721] border border-white/10 rounded-2xl p-10">
    <div class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs font-mono mb-4">
      HTTP 404 &bull; NOT FOUND
    </div>
    <h1 class="text-3xl font-extrabold text-white mb-3">Halaman Tidak Ditemukan</h1>
    <p class="text-slate-400 text-sm mb-6">
      Rute <code class="px-2 py-0.5 rounded bg-black/40 text-amber-400 font-mono"><?= Controller::e($requestedPath ?? '/') ?></code> tidak terdaftar pada router MVC PayCom BLD.
    </p>
    <div class="flex flex-wrap justify-center gap-3">
      <a href="<?= Controller::e(Controller::url('/')) ?>" class="btn btn-primary">Kembali ke Konfigurator</a>
      <a href="<?= Controller::e(Controller::url('/components')) ?>" class="btn btn-secondary">Katalog Komponen</a>
      <a href="<?= Controller::e(Controller::url('/builds')) ?>" class="btn btn-secondary">Rakitan Tersimpan</a>
    </div>
  </div>
</main>

<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>

