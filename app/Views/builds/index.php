<?php
use App\Core\Controller;

$searchQuery = $searchQuery ?? '';
$selectedPurpose = $selectedPurpose ?? '';
$purposes = $purposes ?? [];
?>

<main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 py-10">
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
    <div>
      <span class="text-xs font-mono uppercase tracking-widest text-amber-400">CRUD Tabel `pc_builds` &amp; `build_items` &bull; PDO MySQL</span>
      <h1 class="text-3xl font-extrabold text-white mt-1">Rakitan PC Tersimpan</h1>
      <p class="text-sm text-slate-400 mt-1">
        Daftar seluruh rakitan PC yang tersimpan di database MySQL beserta status kompatibilitas, kebutuhan daya, dan estimasi total harga.
      </p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="<?= Controller::e(Controller::url('/builds/create')) ?>" class="btn btn-primary">
        + Buat Rakitan (Form PHP)
      </a>
      <a href="<?= Controller::e(Controller::url('/')) ?>#builder-section" class="btn btn-secondary">
        Rakit via Konfigurator
      </a>
    </div>
  </div>

  <?php if (!empty($dbError)): ?>
    <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-sm text-rose-300 mb-6">
      <strong>Gagal memuat database:</strong> <?= Controller::e($dbError) ?>
    </div>
  <?php endif; ?>

  <!-- Filter & Pencarian (GET /builds) -->
  <form method="GET" action="<?= Controller::e(Controller::url('/builds')) ?>"
        class="bg-[#141721] border border-white/10 rounded-2xl p-4 mb-6 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
    <div class="md:col-span-5">
      <label for="build-q" class="block text-xs text-slate-400 mb-1">Cari Nama Rakitan / Perakit</label>
      <input type="text" id="build-q" name="q" value="<?= Controller::e($searchQuery) ?>"
             placeholder="Contoh: Esports, Dicky_208, 1440p..."
             class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
    </div>

    <div class="md:col-span-4">
      <label for="build-purpose" class="block text-xs text-slate-400 mb-1">Filter Kebutuhan</label>
      <select id="build-purpose" name="purpose"
              class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
        <option value="">Semua Kebutuhan</option>
        <?php foreach ($purposes as $p): ?>
          <option value="<?= Controller::e($p) ?>" <?= $selectedPurpose === $p ? 'selected' : '' ?>>
            <?= Controller::e($p) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="md:col-span-3 flex gap-2">
      <button type="submit" class="btn btn-primary flex-1">Cari Rakitan</button>
      <?php if ($selectedPurpose !== '' || $searchQuery !== ''): ?>
        <a href="<?= Controller::e(Controller::url('/builds')) ?>" class="btn btn-secondary">Reset</a>
      <?php endif; ?>
    </div>
  </form>

  <?php if (empty($builds)): ?>
    <div class="bg-[#141721] border border-white/10 rounded-2xl p-12 text-center">
      <p class="text-slate-400 text-sm mb-4">Belum ada rakitan tersimpan yang sesuai.</p>
      <a href="<?= Controller::e(Controller::url('/builds/create')) ?>" class="btn btn-primary">Buat Rakitan Pertama</a>
    </div>
  <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <?php foreach ($builds as $b): ?>
        <?php
          $status = (string) $b['compatibility_status'];
          $statusClass = 'text-amber-300 bg-amber-500/10 border-amber-500/30';
          if ($status === '100% Kompatibel') {
              $statusClass = 'text-emerald-300 bg-emerald-500/10 border-emerald-500/30';
          } elseif ($status === 'Tidak Kompatibel') {
              $statusClass = 'text-rose-300 bg-rose-500/10 border-rose-500/30';
          }
        ?>
        <article class="bg-[#141721] border border-white/10 rounded-2xl p-5 flex flex-col justify-between gap-4 hover:border-orange-500/40 transition-colors">
          <div>
            <div class="flex items-center justify-between gap-2 mb-3">
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-orange-500/15 text-orange-300 border border-orange-500/30">
                <?= Controller::e($b['purpose']) ?>
              </span>
              <span class="text-xs font-mono text-slate-400"><?= (int) $b['items_count'] ?> / 7 slot</span>
            </div>

            <h2 class="text-lg font-bold text-white leading-snug"><?= Controller::e($b['build_name']) ?></h2>
            <p class="text-xs text-slate-400 mt-1">Perakit: <strong class="text-slate-200"><?= Controller::e($b['builder_name']) ?></strong></p>

            <?php if (!empty($b['notes'])): ?>
              <p class="text-xs text-slate-400 mt-3 line-clamp-2"><?= Controller::e($b['notes']) ?></p>
            <?php endif; ?>

            <div class="mt-4">
              <span class="inline-block px-2.5 py-1 rounded-lg border text-xs font-semibold <?= $statusClass ?>">
                <?= Controller::e($status) ?>
              </span>
            </div>
          </div>

          <div class="pt-4 border-t border-white/10 space-y-3">
            <div class="flex items-baseline justify-between">
              <span class="text-xs text-slate-400"><?= (int) $b['total_wattage'] ?>W &bull; Min PSU <?= (int) $b['recommended_psu'] ?>W</span>
              <span class="text-base font-bold text-amber-400"><?= Controller::e(Controller::formatRupiah((int) $b['total_price'])) ?></span>
            </div>

            <div class="flex flex-wrap gap-2">
              <a href="<?= Controller::e(Controller::url('/builds/show?id=' . (int) $b['id'])) ?>" class="btn btn-primary btn-sm flex-1 text-center">
                Detail
              </a>
              <a href="<?= Controller::e(Controller::url('/builds/edit?id=' . (int) $b['id'])) ?>" class="btn btn-secondary btn-sm">
                Edit
              </a>
              <form method="POST" action="<?= Controller::e(Controller::url('/builds/delete')) ?>"
                    onsubmit="return confirm('Hapus rakitan <?= Controller::e($b['build_name']) ?>?');"
                    class="inline">
                <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-500/15 text-rose-300 border border-rose-500/30 hover:bg-rose-500/25 transition-colors">
                  Hapus
                </button>
              </form>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

