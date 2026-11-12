<?php
use App\Core\Controller;
?>

<main class="flex-1 w-full max-w-4xl mx-auto px-4 sm:px-6 py-10">
  <div class="mb-6">
    <a href="<?= Controller::e(Controller::url('/components')) ?>" class="text-xs text-slate-400 hover:text-amber-400">&larr; Kembali ke Katalog Komponen</a>
    <h1 class="text-2xl font-extrabold text-white mt-2">Edit Komponen Hardware</h1>
    <p class="text-sm text-slate-400 mt-1">
      Perbarui informasi spesifikasi atau estimasi harga untuk <strong class="text-white"><?= Controller::e($component['name'] ?? '') ?></strong>.
    </p>
  </div>

  <?php
  $formAction = Controller::url('/components/update');
  require __DIR__ . '/_form.php';
  ?>
</main>

