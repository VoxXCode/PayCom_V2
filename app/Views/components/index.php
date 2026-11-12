<?php
use App\Core\Controller;

$selectedType = $selectedType ?? '';
$searchQuery = $searchQuery ?? '';
$typeLabels = $typeLabels ?? [];
?>

<main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 py-10">
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
    <div>
      <span class="text-xs font-mono uppercase tracking-widest text-amber-400">CRUD Tabel `components` &bull; PDO MySQL</span>
      <h1 class="text-3xl font-extrabold text-white mt-1">Katalog Komponen Hardware</h1>
      <p class="text-sm text-slate-400 mt-1">
        Kelola daftar CPU, Motherboard, RAM, GPU, Storage, PSU, dan Casing yang tersimpan di database MySQL.
      </p>
    </div>
    <a href="<?= Controller::e(Controller::url('/components/create')) ?>" class="btn btn-primary shrink-0">
      + Tambah Komponen Baru
    </a>
  </div>

  <?php if (!empty($dbError)): ?>
    <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-sm text-rose-300 mb-6">
      <strong>Gagal memuat database:</strong> <?= Controller::e($dbError) ?>
    </div>
  <?php endif; ?>

  <!-- Filter & Pencarian (GET /components) -->
  <form method="GET" action="<?= Controller::e(Controller::url('/components')) ?>"
        class="bg-[#141721] border border-white/10 rounded-2xl p-4 mb-6 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
    <div class="md:col-span-5">
      <label for="filter-q" class="block text-xs text-slate-400 mb-1">Cari Nama / Spesifikasi / Socket</label>
      <input type="text" id="filter-q" name="q" value="<?= Controller::e($searchQuery) ?>"
             placeholder="Contoh: LGA1700, RTX 4060, DDR5..."
             class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
    </div>

    <div class="md:col-span-4">
      <label for="filter-type" class="block text-xs text-slate-400 mb-1">Filter Kategori</label>
      <select id="filter-type" name="type"
              class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
        <option value="">Semua Kategori</option>
        <?php foreach ($typeLabels as $typeKey => $typeLabel): ?>
          <option value="<?= Controller::e($typeKey) ?>" <?= $selectedType === $typeKey ? 'selected' : '' ?>>
            <?= Controller::e($typeLabel) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="md:col-span-3 flex gap-2">
      <button type="submit" class="btn btn-primary flex-1">Terapkan Filter</button>
      <?php if ($selectedType !== '' || $searchQuery !== ''): ?>
        <a href="<?= Controller::e(Controller::url('/components')) ?>" class="btn btn-secondary">Reset</a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Tabel Daftar Komponen -->
  <div class="bg-[#141721] border border-white/10 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-sm">
        <thead>
          <tr class="border-b border-white/10 bg-black/30 text-xs uppercase tracking-wider text-slate-400">
            <th class="py-3.5 px-4">Kategori</th>
            <th class="py-3.5 px-4">Nama Komponen</th>
            <th class="py-3.5 px-4">Spesifikasi Kunci</th>
            <th class="py-3.5 px-4">Estimasi Harga</th>
            <th class="py-3.5 px-4 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          <?php if (empty($components)): ?>
            <tr>
              <td colspan="5" class="py-10 px-4 text-center text-slate-400">
                Belum ada komponen yang sesuai dengan filter pencarian.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($components as $item): ?>
              <tr class="hover:bg-white/[0.02] transition-colors">
                <td class="py-3.5 px-4 align-top">
                  <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-mono uppercase bg-orange-500/15 text-orange-300 border border-orange-500/30">
                    <?= Controller::e($item['type']) ?>
                  </span>
                </td>
                <td class="py-3.5 px-4 align-top">
                  <div class="font-semibold text-white">
                    <?= Controller::e($item['name']) ?>
                    <?php if (!empty($item['is_custom'])): ?>
                      <span class="ml-1.5 px-1.5 py-0.5 rounded text-[10px] bg-amber-500/20 text-amber-300 border border-amber-500/30">Custom</span>
                    <?php endif; ?>
                  </div>
                  <div class="text-xs text-slate-400 mt-0.5"><?= Controller::e($item['specs_summary']) ?></div>
                </td>
                <td class="py-3.5 px-4 align-top text-xs text-slate-300 font-mono space-y-1">
                  <?php if (!empty($item['socket'])): ?>
                    <div>Socket: <span class="text-amber-300"><?= Controller::e($item['socket']) ?></span></div>
                  <?php endif; ?>
                  <?php if (!empty($item['ram_type'])): ?>
                    <div>RAM: <span class="text-amber-300"><?= Controller::e($item['ram_type']) ?></span></div>
                  <?php endif; ?>
                  <?php if (!empty($item['form_factor'])): ?>
                    <div>Form Factor: <span class="text-amber-300"><?= Controller::e($item['form_factor']) ?></span></div>
                  <?php endif; ?>
                  <?php if (!empty($item['supported_form_factors'])): ?>
                    <div>Support: <span class="text-amber-300"><?= Controller::e($item['supported_form_factors']) ?></span></div>
                  <?php endif; ?>
                  <?php if ((int) $item['tdp'] > 0): ?>
                    <div>TDP: <span class="text-emerald-400"><?= (int) $item['tdp'] ?>W</span></div>
                  <?php endif; ?>
                  <?php if (!empty($item['wattage'])): ?>
                    <div>Kapasitas: <span class="text-emerald-400"><?= (int) $item['wattage'] ?>W</span></div>
                  <?php endif; ?>
                  <?php if ($item['type'] === 'cpu'): ?>
                    <div>iGPU: <?= !empty($item['has_igpu']) ? '<span class="text-emerald-400">Ada</span>' : '<span class="text-rose-400">Tidak</span>' ?></div>
                  <?php endif; ?>
                </td>
                <td class="py-3.5 px-4 align-top font-semibold text-amber-400 whitespace-nowrap">
                  <?= Controller::e(Controller::formatRupiah((int) $item['base_price'])) ?>
                </td>
                <td class="py-3.5 px-4 align-top text-right whitespace-nowrap">
                  <div class="inline-flex items-center gap-2">
                    <a href="<?= Controller::e(Controller::url('/components/edit?id=' . (int) $item['id'])) ?>"
                       class="btn btn-secondary btn-sm">
                      Edit
                    </a>
                    <form method="POST" action="<?= Controller::e(Controller::url('/components/delete')) ?>"
                          onsubmit="return confirm('Hapus komponen <?= Controller::e($item['name']) ?>?');"
                          class="inline">
                      <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                      <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-500/15 text-rose-300 border border-rose-500/30 hover:bg-rose-500/25 transition-colors">
                        Hapus
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

