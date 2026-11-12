<?php
use App\Core\Controller;

$isEdit = !empty($build['id']);
$val = fn(string $key, mixed $default = '') => $old[$key] ?? ($build[$key] ?? $default);
$oldComponents = $old['components'] ?? [];
$existingItems = $build['items'] ?? [];
?>

<form method="POST" action="<?= Controller::e($formAction) ?>"
      class="bg-[#141721] border border-white/10 rounded-2xl p-6 space-y-6">
  <?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= (int) $build['id'] ?>">
  <?php endif; ?>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
      <label for="form-build-name" class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Rakitan *</label>
      <input type="text" id="form-build-name" name="build_name"
             value="<?= Controller::e($val('build_name')) ?>"
             placeholder="Contoh: PC Gaming 1440p Ultra"
             class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
      <?php if (!empty($errors['build_name'])): ?>
        <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['build_name']) ?></p>
      <?php endif; ?>
    </div>

    <div>
      <label for="form-builder-name" class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Perakit *</label>
      <input type="text" id="form-builder-name" name="builder_name"
             value="<?= Controller::e($val('builder_name')) ?>"
             placeholder="Contoh: Devano_211"
             class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
      <?php if (!empty($errors['builder_name'])): ?>
        <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['builder_name']) ?></p>
      <?php endif; ?>
    </div>

    <div>
      <label for="form-purpose" class="block text-xs font-semibold text-slate-300 mb-1.5">Tujuan Penggunaan *</label>
      <select id="form-purpose" name="purpose"
              class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
        <?php foreach ($purposes as $p): ?>
          <option value="<?= Controller::e($p) ?>" <?= $val('purpose', 'Gaming') === $p ? 'selected' : '' ?>>
            <?= Controller::e($p) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (!empty($errors['purpose'])): ?>
        <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['purpose']) ?></p>
      <?php endif; ?>
    </div>
  </div>

  <div>
    <label for="form-notes" class="block text-xs font-semibold text-slate-300 mb-1.5">Catatan Rakitan (Opsional)</label>
    <textarea id="form-notes" name="notes" rows="2"
              placeholder="Catatan spesifikasi, target resolusi, atau catatan untuk teknisi toko..."
              class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none"><?= Controller::e($val('notes')) ?></textarea>
    <?php if (!empty($errors['notes'])): ?>
      <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['notes']) ?></p>
    <?php endif; ?>
  </div>

  <div class="pt-2 border-t border-white/10">
    <h2 class="text-sm font-bold uppercase tracking-wider text-amber-400 mb-3">Pilih Komponen dari Katalog MySQL</h2>
    <?php if (!empty($errors['components'])): ?>
      <p class="text-xs text-rose-400 mb-3"><?= Controller::e($errors['components']) ?></p>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <?php foreach ($typeLabels as $cat => $label): ?>
        <?php
          $options = $groupedComponents[$cat] ?? [];
          $selectedId = $oldComponents[$cat] ?? ($existingItems[$cat]['component_id'] ?? '');
        ?>
        <div>
          <label for="select-comp-<?= Controller::e($cat) ?>" class="block text-xs font-semibold text-slate-300 mb-1.5">
            <?= Controller::e($label) ?>
          </label>
          <select id="select-comp-<?= Controller::e($cat) ?>" name="components[<?= Controller::e($cat) ?>]"
                  class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
            <option value="">-- Tidak Dipilih --</option>
            <?php foreach ($options as $opt): ?>
              <?php $dbId = (string) $opt['db_id']; ?>
              <option value="<?= Controller::e($dbId) ?>" <?= (string) $selectedId === $dbId || (string) $selectedId === (string) $opt['id'] ? 'selected' : '' ?>>
                <?= Controller::e($opt['name']) ?> (<?= Controller::e(Controller::formatRupiah((int) $opt['base_price'])) ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (!empty($errors['components_' . $cat])): ?>
            <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['components_' . $cat]) ?></p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="flex items-center gap-3 pt-2">
    <button type="submit" class="btn btn-primary">
      <?= $isEdit ? 'Simpan Perubahan Rakitan' : 'Simpan Rakitan Baru' ?>
    </button>
    <a href="<?= Controller::e(Controller::url('/builds')) ?>" class="btn btn-secondary">Batal</a>
  </div>
</form>

