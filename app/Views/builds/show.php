<?php
use App\Core\Controller;

$items = $build['items'] ?? [];
$status = (string) ($evaluation['compatibility_status'] ?? $build['compatibility_status']);
$statusClass = 'bg-amber-500/10 border-amber-500/30 text-amber-300';
if ($status === '100% Kompatibel') {
    $statusClass = 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300';
} elseif ($status === 'Tidak Kompatibel') {
    $statusClass = 'bg-rose-500/10 border-rose-500/30 text-rose-300';
}
?>

<main class="flex-1 w-full max-w-5xl mx-auto px-4 sm:px-6 py-10">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
      <a href="<?= Controller::e(Controller::url('/builds')) ?>" class="text-xs text-slate-400 hover:text-amber-400">&larr; Kembali ke Rakitan Tersimpan</a>
      <h1 class="text-3xl font-extrabold text-white mt-2"><?= Controller::e($build['build_name']) ?></h1>
      <p class="text-sm text-slate-400 mt-1">
        Perakit: <strong class="text-slate-200"><?= Controller::e($build['builder_name']) ?></strong> &bull;
        Kebutuhan: <strong class="text-orange-400"><?= Controller::e($build['purpose']) ?></strong>
      </p>
    </div>

    <div class="flex flex-wrap gap-2">
      <a href="<?= Controller::e(Controller::url('/?load_build=' . (int) $build['id'])) ?>#builder-section" class="btn btn-primary btn-sm">
        Muat di Konfigurator
      </a>
      <a href="<?= Controller::e(Controller::url('/builds/edit?id=' . (int) $build['id'])) ?>" class="btn btn-secondary btn-sm">
        Edit Rakitan
      </a>
      <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm">
        Cetak PDF
      </button>
    </div>
  </div>

  <!-- Ringkasan Metrik -->
  <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-[#141721] border border-white/10 rounded-xl p-4">
      <div class="text-xs text-slate-400">Estimasi Total Harga</div>
      <div class="text-xl font-extrabold text-amber-400 mt-1">
        <?= Controller::e(Controller::formatRupiah((int) $build['total_price'])) ?>
      </div>
    </div>
    <div class="bg-[#141721] border border-white/10 rounded-xl p-4">
      <div class="text-xs text-slate-400">Total Beban Daya</div>
      <div class="text-xl font-extrabold text-white mt-1"><?= (int) $build['total_wattage'] ?> Watt</div>
    </div>
    <div class="bg-[#141721] border border-white/10 rounded-xl p-4">
      <div class="text-xs text-slate-400">Rekomendasi PSU</div>
      <div class="text-xl font-extrabold text-amber-300 mt-1">Min <?= (int) $build['recommended_psu'] ?> Watt</div>
    </div>
    <div class="bg-[#141721] border rounded-xl p-4 <?= $statusClass ?>">
      <div class="text-xs opacity-80">Status Kompatibilitas</div>
      <div class="text-sm font-bold mt-1"><?= Controller::e($status) ?></div>
    </div>
  </div>

  <?php if (!empty($evaluation['errors']) || !empty($evaluation['warnings'])): ?>
    <div class="space-y-2 mb-6">
      <?php foreach ($evaluation['errors'] as $err): ?>
        <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
          <strong>Peringatan Kritis:</strong> <?= Controller::e($err) ?>
        </div>
      <?php endforeach; ?>
      <?php foreach ($evaluation['warnings'] as $warn): ?>
        <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-300">
          <strong>Catatan Daya:</strong> <?= Controller::e($warn) ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($build['notes'])): ?>
    <div class="bg-[#141721] border border-white/10 rounded-xl p-4 mb-6 text-sm text-slate-300">
      <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Catatan Perakit</span>
      <?= nl2br(Controller::e($build['notes'])) ?>
    </div>
  <?php endif; ?>

  <!-- Tabel Komponen Terpilih -->
  <div class="bg-[#141721] border border-white/10 rounded-2xl overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
      <thead>
        <tr class="border-b border-white/10 bg-black/30 text-xs uppercase tracking-wider text-slate-400">
          <th class="py-3.5 px-4">Kategori</th>
          <th class="py-3.5 px-4">Komponen Terpilih</th>
          <th class="py-3.5 px-4">Spesifikasi</th>
          <th class="py-3.5 px-4 text-right">Estimasi Harga</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-white/5">
        <?php foreach ($typeLabels as $cat => $label): ?>
          <?php $item = $items[$cat] ?? null; ?>
          <tr>
            <td class="py-3.5 px-4 font-semibold text-slate-300"><?= Controller::e($label) ?></td>
            <?php if ($item): ?>
              <?php $price = $item['custom_price'] !== null ? (int) $item['custom_price'] : (int) $item['base_price']; ?>
              <td class="py-3.5 px-4 text-white font-semibold"><?= Controller::e($item['name']) ?></td>
              <td class="py-3.5 px-4 text-xs text-slate-400"><?= Controller::e($item['specs_summary']) ?></td>
              <td class="py-3.5 px-4 text-right font-semibold text-amber-400 whitespace-nowrap">
                <?= Controller::e(Controller::formatRupiah($price)) ?>
              </td>
            <?php else: ?>
              <td class="py-3.5 px-4 text-slate-500 italic" colspan="2">[Belum Dipilih]</td>
              <td class="py-3.5 px-4 text-right text-slate-500">-</td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

