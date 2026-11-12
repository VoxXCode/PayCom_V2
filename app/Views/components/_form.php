<?php
use App\Core\Controller;

$isEdit = !empty($component['id']);
$val = fn(string $key, mixed $default = '') => $old[$key] ?? ($component[$key] ?? $default);
?>

<form method="POST" action="<?= Controller::e($formAction) ?>"
      class="bg-[#141721] border border-white/10 rounded-2xl p-6 space-y-5">
  <?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= (int) $component['id'] ?>">
  <?php endif; ?>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
      <label for="comp-type" class="block text-xs font-semibold text-slate-300 mb-1.5">Kategori Komponen *</label>
      <select id="comp-type" name="type"
              class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
        <option value="">-- Pilih Kategori --</option>
        <?php foreach ($typeLabels as $k => $label): ?>
          <option value="<?= Controller::e($k) ?>" <?= $val('type') === $k ? 'selected' : '' ?>>
            <?= Controller::e($label) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (!empty($errors['type'])): ?>
        <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['type']) ?></p>
      <?php endif; ?>
    </div>

    <div>
      <label for="comp-price" class="block text-xs font-semibold text-slate-300 mb-1.5">Estimasi Harga (Rp) *</label>
      <input type="number" id="comp-price" name="base_price" min="0" step="1000"
             value="<?= Controller::e($val('base_price', '0')) ?>"
             placeholder="Contoh: 1950000"
             class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
      <?php if (!empty($errors['base_price'])): ?>
        <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['base_price']) ?></p>
      <?php endif; ?>
    </div>
  </div>

  <div>
    <label for="comp-name" class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Komponen / Merek *</label>
    <input type="text" id="comp-name" name="name"
           value="<?= Controller::e($val('name')) ?>"
           placeholder="Contoh: Intel Core i5-12400F Box"
           class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
    <?php if (!empty($errors['name'])): ?>
      <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['name']) ?></p>
    <?php endif; ?>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div>
      <label for="comp-socket" class="block text-xs font-semibold text-slate-300 mb-1.5">Socket (CPU / Mobo)</label>
      <input type="text" id="comp-socket" name="socket"
             value="<?= Controller::e($val('socket')) ?>"
             placeholder="LGA1700 / AM4 / AM5"
             class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
      <?php if (!empty($errors['socket'])): ?>
        <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['socket']) ?></p>
      <?php endif; ?>
    </div>

    <div>
      <label for="comp-ram-type" class="block text-xs font-semibold text-slate-300 mb-1.5">Tipe RAM (Mobo / RAM)</label>
      <select id="comp-ram-type" name="ram_type"
              class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
        <option value="">-- Tidak Perlu --</option>
        <option value="DDR4" <?= $val('ram_type') === 'DDR4' ? 'selected' : '' ?>>DDR4</option>
        <option value="DDR5" <?= $val('ram_type') === 'DDR5' ? 'selected' : '' ?>>DDR5</option>
      </select>
      <?php if (!empty($errors['ram_type'])): ?>
        <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['ram_type']) ?></p>
      <?php endif; ?>
    </div>

    <div>
      <label for="comp-form-factor" class="block text-xs font-semibold text-slate-300 mb-1.5">Form Factor (Mobo)</label>
      <select id="comp-form-factor" name="form_factor"
              class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
        <option value="">-- Tidak Perlu --</option>
        <option value="ATX" <?= $val('form_factor') === 'ATX' ? 'selected' : '' ?>>ATX</option>
        <option value="mATX" <?= $val('form_factor') === 'mATX' ? 'selected' : '' ?>>mATX</option>
        <option value="ITX" <?= $val('form_factor') === 'ITX' ? 'selected' : '' ?>>ITX</option>
      </select>
      <?php if (!empty($errors['form_factor'])): ?>
        <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['form_factor']) ?></p>
      <?php endif; ?>
    </div>

    <div>
      <label for="comp-tdp" class="block text-xs font-semibold text-slate-300 mb-1.5">TDP (Watt)</label>
      <input type="number" id="comp-tdp" name="tdp" min="0" max="1000"
             value="<?= Controller::e($val('tdp', '0')) ?>"
             class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
      <?php if (!empty($errors['tdp'])): ?>
        <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['tdp']) ?></p>
      <?php endif; ?>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-start">
    <div>
      <label for="comp-wattage" class="block text-xs font-semibold text-slate-300 mb-1.5">Kapasitas PSU (Watt)</label>
      <input type="number" id="comp-wattage" name="wattage" min="0" max="2500"
             value="<?= Controller::e($val('wattage')) ?>"
             placeholder="Contoh: 650"
             class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
      <?php if (!empty($errors['wattage'])): ?>
        <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['wattage']) ?></p>
      <?php endif; ?>
    </div>

    <div>
      <label for="comp-cap-gb" class="block text-xs font-semibold text-slate-300 mb-1.5">Kapasitas RAM (GB)</label>
      <input type="number" id="comp-cap-gb" name="capacity_gb" min="1" max="512"
             value="<?= Controller::e($val('capacity_gb')) ?>"
             placeholder="Contoh: 16"
             class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
      <?php if (!empty($errors['capacity_gb'])): ?>
        <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['capacity_gb']) ?></p>
      <?php endif; ?>
    </div>

    <div>
      <label for="comp-speed-mhz" class="block text-xs font-semibold text-slate-300 mb-1.5">Speed RAM (MHz)</label>
      <input type="number" id="comp-speed-mhz" name="speed_mhz" min="800" max="12000"
             value="<?= Controller::e($val('speed_mhz')) ?>"
             placeholder="Contoh: 3200"
             class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
      <?php if (!empty($errors['speed_mhz'])): ?>
        <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['speed_mhz']) ?></p>
      <?php endif; ?>
    </div>

    <div>
      <label for="comp-supported-ff" class="block text-xs font-semibold text-slate-300 mb-1.5">Support Casing (Koma)</label>
      <input type="text" id="comp-supported-ff" name="supported_form_factors"
             value="<?= Controller::e($val('supported_form_factors')) ?>"
             placeholder="ATX,mATX,ITX"
             class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
      <?php if (!empty($errors['supported_form_factors'])): ?>
        <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['supported_form_factors']) ?></p>
      <?php endif; ?>
    </div>
  </div>

  <div class="flex items-center gap-2.5">
    <input type="checkbox" id="comp-has-igpu" name="has_igpu" value="1"
           <?= !empty($val('has_igpu')) ? 'checked' : '' ?>
           class="w-4 h-4 accent-orange-500 rounded">
    <label for="comp-has-igpu" class="text-sm text-slate-300">
      Prosesor ini memiliki grafis internal (iGPU) bawaan
    </label>
  </div>

  <div>
    <label for="comp-specs" class="block text-xs font-semibold text-slate-300 mb-1.5">Ringkasan Spesifikasi *</label>
    <textarea id="comp-specs" name="specs_summary" rows="2"
              placeholder="Contoh: 6 Core / 12 Thread, 2.5 GHz - 4.4 GHz, TDP 65W, No iGPU"
              class="w-full px-3.5 py-2.5 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none"><?= Controller::e($val('specs_summary')) ?></textarea>
    <?php if (!empty($errors['specs_summary'])): ?>
      <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['specs_summary']) ?></p>
    <?php endif; ?>
  </div>

  <div class="flex items-center gap-3 pt-2">
    <button type="submit" class="btn btn-primary">
      <?= $isEdit ? 'Simpan Perubahan Komponen' : 'Tambahkan Komponen ke Database' ?>
    </button>
    <a href="<?= Controller::e(Controller::url('/components')) ?>" class="btn btn-secondary">Batal</a>
  </div>
</form>

