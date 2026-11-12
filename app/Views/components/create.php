<?php
use App\Core\Controller;
?>

<main class="flex-1 w-full max-w-4xl mx-auto px-4 sm:px-6 py-10">
  <div class="mb-6">
    <a href="<?= Controller::e(Controller::url('/components')) ?>" class="text-xs text-slate-400 hover:text-amber-400">&larr; Kembali ke Katalog Komponen</a>
    <h1 class="text-2xl font-extrabold text-white mt-2">Tambah Komponen Hardware Baru</h1>
    <p class="text-sm text-slate-400 mt-1">
      Semua kolom wajib divalidasi langsung di <code class="text-amber-300 font-mono text-xs">ComponentController::validateComponentInput()</code> sebelum disimpan ke MySQL.
    </p>
  </div>

  <?php
  $component = [];
  $formAction = Controller::url('/components/store');
  require __DIR__ . '/_form.php';
  ?>
</main>

