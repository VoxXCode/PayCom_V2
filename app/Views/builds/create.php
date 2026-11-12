<?php
use App\Core\Controller;
?>

<main class="flex-1 w-full max-w-5xl mx-auto px-4 sm:px-6 py-10">
  <div class="mb-6">
    <a href="<?= Controller::e(Controller::url('/builds')) ?>" class="text-xs text-slate-400 hover:text-amber-400">&larr; Kembali ke Rakitan Tersimpan</a>
    <h1 class="text-2xl font-extrabold text-white mt-2">Buat &amp; Simpan Rakitan PC Baru</h1>
    <p class="text-sm text-slate-400 mt-1">
      Controller akan memvalidasi seluruh input dan menghitung kompatibilitas, total daya, serta estimasi harga secara otomatis di server.
    </p>
  </div>

  <?php if (!empty($dbError)): ?>
    <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-sm text-rose-300 mb-6">
      <strong>Database belum terhubung:</strong> <?= Controller::e($dbError) ?>
    </div>
  <?php endif; ?>

  <?php
  $build = [];
  $formAction = Controller::url('/builds/store');
  require __DIR__ . '/_form.php';
  ?>
</main>

