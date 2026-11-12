<?php
use App\Core\Controller;

$base = $basePath ?? '';
$assetPrefix = $base === '' ? '' : $base;
$active = $activeNav ?? 'builder';
?>
  <!-- Toast Notification untuk Umpan Balik Tindakan Pengguna -->
  <div id="app-toast" class="toast-notice hidden" role="status" aria-live="polite"></div>

  <!-- 8. Footer -->
  <footer class="app-footer">
    <div class="footer-container">
      <div class="footer-grid">
        <div>
          <div class="text-white font-bold uppercase tracking-wider mb-3">PayCom BLD</div>
          <p class="leading-relaxed text-slate-400 text-xs">
            Platform perakit PC cerdas berbasis PHP MVC &amp; MySQL (PDO) untuk memvalidasi kompatibilitas hardware dan estimasi biaya secara otomatis.
          </p>
        </div>
        <div>
          <div class="text-white font-bold uppercase tracking-wider mb-3">Template Kelompok</div>
          <ul class="space-y-2 text-slate-400 text-xs">
            <li><a href="<?= Controller::e(Controller::url('/')) ?>#player-series" class="hover:text-amber-400 transition-colors">Dicky_208</a></li>
            <li><a href="<?= Controller::e(Controller::url('/')) ?>#player-series" class="hover:text-amber-400 transition-colors">Devano_211</a></li>
            <li><a href="<?= Controller::e(Controller::url('/')) ?>#player-series" class="hover:text-amber-400 transition-colors">Himam_179</a></li>
          </ul>
        </div>
        <div>
          <div class="text-white font-bold uppercase tracking-wider mb-3">Navigasi MVC</div>
          <ul class="space-y-2 text-slate-400 text-xs">
            <li><a href="<?= Controller::e(Controller::url('/')) ?>" class="hover:text-amber-400 transition-colors">Konfigurator PC Builder</a></li>
            <li><a href="<?= Controller::e(Controller::url('/components')) ?>" class="hover:text-amber-400 transition-colors">Katalog Komponen (CRUD)</a></li>
            <li><a href="<?= Controller::e(Controller::url('/builds')) ?>" class="hover:text-amber-400 transition-colors">Rakitan Tersimpan (CRUD)</a></li>
          </ul>
        </div>
        <div>
          <div class="text-white font-bold uppercase tracking-wider mb-3">Bantuan &amp; Info</div>
          <ul class="space-y-2 text-slate-400 text-xs">
            <li><a href="<?= Controller::e(Controller::url('/')) ?>#faq-section" class="hover:text-amber-400 transition-colors">Pertanyaan Umum (FAQ)</a></li>
            <li><span class="text-slate-500">MySQL PDO Prepared Statements</span></li>
            <li><span class="text-slate-500">Validasi Input di Controller</span></li>
          </ul>
        </div>
      </div>

      <div
        class="pt-6 border-t border-slate-800/60 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left text-slate-400 text-xs">
        <div>&copy; 2026 PayCom BLD Platform. Proyek Kelompok PHP MVC + MySQL.</div>
        <div>Estimasi harga bersumber dari rata-rata online &bull; Bukan jaminan mutlak harga toko fisik.</div>
      </div>
    </div>
  </footer>

  <?php if ($active === 'builder'): ?>
    <!-- Script Aplikasi Modular -->
    <script type="module" src="<?= Controller::e($assetPrefix) ?>/js/app.js"></script>
  <?php endif; ?>
</body>

</html>

