<?php
use App\Core\Controller;

$oldBuildName = $old['build_name'] ?? '';
$oldBuilderName = $old['builder_name'] ?? '';
$oldPurpose = $old['purpose'] ?? 'Gaming';
$oldNotes = $old['notes'] ?? '';
?>

<main class="flex-1 w-full">

  <?php if (!empty($dbError)): ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-5">
      <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-xs text-amber-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
          <strong class="text-amber-400">Database MySQL belum terhubung:</strong>
          <span><?= Controller::e($dbError) ?>. Impor file <code class="px-1.5 py-0.5 rounded bg-black/40 font-mono">database/paycom_builder.sql</code> dan aktifkan MySQL (Laragon/XAMPP) untuk fitur simpan rakitan &amp; CRUD katalog.</span>
        </div>
        <span class="font-mono text-[11px] text-amber-300 shrink-0">Mode Fallback Aktif</span>
      </div>
    </div>
  <?php endif; ?>

  <!-- 3. High-Impact Hero Section -->
  <section class="hero-section">
    <div class="hero-container">

      <div class="hero-pill">
        <span class="hero-pill-dot"></span>
        <span>Intelligent Custom PC Builder &bull; PHP MVC + MySQL</span>
      </div>

      <h1 class="hero-title">
        Perakitan PC Custom.<br>
        <span class="hero-title-gradient">Tanpa Risiko Salah Beli.</span>
      </h1>

      <p class="hero-desc">
        Bangun komputer gaming dan kerja impian Anda dengan percaya diri. Sistem kami menyaring kecocokan socket
        processor, generasi RAM, kapasitas casing, hingga rekomendasi daya PSU secara otomatis dan menyimpannya ke database MySQL.
      </p>

      <div class="hero-actions">
        <a href="#builder-section" class="btn-hero-primary">
          <span>Buka Konfigurator</span>
          <svg class="ui-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </a>
        <a href="<?= Controller::e(Controller::url('/components')) ?>" class="btn-hero-secondary">
          Kelola Katalog Komponen (<?= (int) ($totalComponents ?? 0) ?>)
        </a>
        <a href="<?= Controller::e(Controller::url('/builds')) ?>" class="btn-hero-secondary">
          Lihat Rakitan Tersimpan (<?= (int) ($totalBuilds ?? 0) ?>)
        </a>
      </div>

      <!-- 4 Core Trust Badges -->
      <div class="hero-trust-grid">
        <div class="trust-card">
          <div class="trust-card-tag text-amber-400">100% Akurat</div>
          <div class="trust-card-desc">Validasi Socket &amp; RAM Otomatis</div>
        </div>
        <div class="trust-card">
          <div class="trust-card-tag text-orange-400">Wattage Intelligence</div>
          <div class="trust-card-desc">Kalkulator Beban Daya Listrik Real-time</div>
        </div>
        <div class="trust-card">
          <div class="trust-card-tag text-emerald-400">MySQL PDO CRUD</div>
          <div class="trust-card-desc">Simpan, Edit &amp; Kelola Rakitan Permanen</div>
        </div>
        <div class="trust-card">
          <div class="trust-card-tag text-yellow-400">Siap Bawa Toko</div>
          <div class="trust-card-desc">Format Ekspor PDF &amp; Salin WhatsApp</div>
        </div>
      </div>

    </div>
  </section>

  <!-- 4. The Player Series (Pre-Configured Builds) -->
  <section id="player-series" class="player-section">
    <div class="player-section-header">
      <div>
        <h2 class="player-section-title">Template Rakitan Siap Pakai</h2>
      </div>
      <p class="player-section-desc">
        Pilihan rakitan yang dirancang khusus oleh teknisi hardware. Muat langsung ke dalam konfigurator dalam 1 klik
        untuk melihat rincian spesifikasi dan memodifikasi komponen.
      </p>
    </div>

    <div class="player-cards-grid">

      <!-- Player One -->
      <article class="player-card">
        <div>
          <div class="player-illustration-wrap">
            <svg class="player-illustration" viewBox="0 0 200 180" fill="none" xmlns="http://www.w3.org/2000/svg"
              aria-label="Ilustrasi PC Player One">
              <defs>
                <linearGradient id="p1-outer" x1="0" y1="0" x2="200" y2="180" gradientUnits="userSpaceOnUse">
                  <stop offset="0%" stop-color="#1d202b" />
                  <stop offset="100%" stop-color="#0e1017" />
                </linearGradient>
                <linearGradient id="p1-glass" x1="40" y1="20" x2="160" y2="160" gradientUnits="userSpaceOnUse">
                  <stop offset="0%" stop-color="#f97316" stop-opacity="0.12" />
                  <stop offset="50%" stop-color="#0f1118" stop-opacity="0.8" />
                  <stop offset="100%" stop-color="#050609" stop-opacity="0.95" />
                </linearGradient>
                <linearGradient id="p1-fan" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0%" stop-color="#fbbf24" />
                  <stop offset="100%" stop-color="#f97316" />
                </linearGradient>
              </defs>
              <rect x="36" y="14" width="128" height="152" rx="8" fill="url(#p1-outer)" stroke="#32384a" stroke-width="2" />
              <path d="M142 16H160C162.2 16 164 17.8 164 20V160C164 162.2 162.2 164 160 164H142V16Z" fill="#12141c" stroke="#252a38" stroke-width="1.5" />
              <line x1="149" y1="28" x2="149" y2="152" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-dasharray="3 4" opacity="0.6" />
              <line x1="155" y1="28" x2="155" y2="152" stroke="#334155" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="2 3" />
              <rect x="42" y="20" width="96" height="140" rx="5" fill="url(#p1-glass)" stroke="#262c3d" stroke-width="1.5" />
              <rect x="48" y="26" width="84" height="90" rx="3" fill="#0d0f17" />
              <path d="M54 36H66M54 44H62M54 52H70" stroke="#262d3d" stroke-width="1" stroke-linecap="round" />
              <rect x="62" y="38" width="34" height="38" rx="3" fill="#181d29" stroke="#384259" stroke-width="1.5" />
              <circle cx="79" cy="57" r="13" fill="#0d1017" stroke="url(#p1-fan)" stroke-width="2" />
              <circle cx="79" cy="57" r="5" fill="#fbbf24" opacity="0.8" />
              <line x1="62" y1="46" x2="96" y2="46" stroke="#384259" stroke-width="1" />
              <line x1="62" y1="52" x2="96" y2="52" stroke="#384259" stroke-width="1" />
              <line x1="62" y1="62" x2="96" y2="62" stroke="#384259" stroke-width="1" />
              <line x1="62" y1="68" x2="96" y2="68" stroke="#384259" stroke-width="1" />
              <rect x="102" y="38" width="3.5" height="34" rx="1.5" fill="#fbbf24" />
              <rect x="108" y="38" width="3.5" height="34" rx="1.5" fill="#f97316" />
              <rect x="48" y="86" width="84" height="24" rx="3" fill="#151924" stroke="#2e3649" stroke-width="1.5" />
              <rect x="52" y="90" width="76" height="3" rx="1" fill="#f97316" opacity="0.9" />
              <circle cx="70" cy="100" r="6" fill="#0c0e14" stroke="#f97316" stroke-width="1.5" />
              <circle cx="98" cy="100" r="6" fill="#0c0e14" stroke="#f97316" stroke-width="1.5" />
              <rect x="42" y="122" width="96" height="38" fill="#0d0e14" stroke="#212738" stroke-width="1" />
              <text x="50" y="145" fill="#64748b" font-size="7.5" font-family="'JetBrains Mono', monospace" font-weight="700" letter-spacing="0.1em">PAYCOM 1080P</text>
              <rect x="48" y="166" width="18" height="5" rx="2" fill="#202433" />
              <rect x="134" y="166" width="18" height="5" rx="2" fill="#202433" />
              <line x1="46" y1="26" x2="120" y2="152" stroke="#ffffff" stroke-width="1" opacity="0.08" stroke-linecap="round" />
            </svg>
          </div>

          <div class="player-meta-bar">
            <span class="player-badge-pill bg-amber-500/10 text-amber-400 border border-amber-500/25">PLAYER: ONE</span>
            <span class="text-xs text-slate-400 font-semibold">1080p Esports</span>
          </div>

          <h3 class="player-card-title">Esports &amp; Casual Gamer</h3>
          <p class="player-card-desc">
            Konfigurasi efisien dan hemat daya untuk game kompetitif (Valorant, CS2, Dota 2, GTA V) dengan budget ramah pelajar.
          </p>

          <div class="player-specs-box">
            <div class="player-spec-row"><span class="player-spec-dot"></span><span>Intel Core i3-12100F Box</span></div>
            <div class="player-spec-row"><span class="player-spec-dot"></span><span>Sapphire Pulse RX 6600 8GB</span></div>
            <div class="player-spec-row"><span class="player-spec-dot"></span><span>Team Elite Plus 16GB DDR4</span></div>
            <div class="player-spec-row"><span class="player-spec-dot"></span><span>Kioxia Exceria 500GB NVMe</span></div>
          </div>
        </div>

        <div>
          <div class="player-price-row">
            <span class="player-price-label">Estimasi Siap Rakit</span>
            <div class="player-price-val text-emerald-400">Est. Rp 7.750.000</div>
          </div>
          <button type="button" class="btn-player-load" data-action="load-template" data-template-id="player-starter">
            <svg class="ui-icon text-amber-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
            </svg>
            <span>Muat ke Builder</span>
          </button>
        </div>
      </article>

      <!-- Player Two (Featured / Popular) -->
      <article class="player-card featured">
        <div class="player-popular-badge">MOST POPULAR</div>
        <div>
          <div class="player-illustration-wrap">
            <svg class="player-illustration" viewBox="0 0 200 180" fill="none" xmlns="http://www.w3.org/2000/svg"
              aria-label="Ilustrasi PC Player Two">
              <defs>
                <linearGradient id="p2-outer" x1="0" y1="0" x2="200" y2="180" gradientUnits="userSpaceOnUse">
                  <stop offset="0%" stop-color="#232838" />
                  <stop offset="100%" stop-color="#0f1118" />
                </linearGradient>
                <linearGradient id="p2-glass" x1="40" y1="20" x2="160" y2="160" gradientUnits="userSpaceOnUse">
                  <stop offset="0%" stop-color="#f97316" stop-opacity="0.18" />
                  <stop offset="50%" stop-color="#11141e" stop-opacity="0.8" />
                  <stop offset="100%" stop-color="#05070b" stop-opacity="0.95" />
                </linearGradient>
                <linearGradient id="p2-flame" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0%" stop-color="#fbbf24" />
                  <stop offset="50%" stop-color="#f97316" />
                  <stop offset="100%" stop-color="#ea580c" />
                </linearGradient>
              </defs>
              <rect x="34" y="14" width="132" height="152" rx="8" fill="url(#p2-outer)" stroke="#f97316" stroke-width="2" stroke-opacity="0.65" />
              <path d="M142 16H162C164.2 16 166 17.8 166 20V160C166 162.2 164.2 164 162 164H142V16Z" fill="#131722" stroke="#2e3549" stroke-width="1.5" />
              <circle cx="154" cy="55" r="10" stroke="url(#p2-flame)" stroke-width="2" fill="#0d0f17" />
              <circle cx="154" cy="55" r="4" fill="#fbbf24" />
              <circle cx="154" cy="115" r="10" stroke="url(#p2-flame)" stroke-width="2" fill="#0d0f17" />
              <circle cx="154" cy="115" r="4" fill="#fbbf24" />
              <rect x="40" y="20" width="98" height="140" rx="5" fill="url(#p2-glass)" stroke="#2b344a" stroke-width="1.5" />
              <rect x="46" y="24" width="86" height="8" rx="2" fill="#1f2638" stroke="#f97316" stroke-width="1" />
              <line x1="56" y1="25" x2="56" y2="31" stroke="#fbbf24" stroke-width="1.5" />
              <line x1="72" y1="25" x2="72" y2="31" stroke="#fbbf24" stroke-width="1.5" />
              <line x1="90" y1="25" x2="90" y2="31" stroke="#fbbf24" stroke-width="1.5" />
              <line x1="106" y1="25" x2="106" y2="31" stroke="#fbbf24" stroke-width="1.5" />
              <circle cx="76" cy="58" r="15" fill="#141926" stroke="url(#p2-flame)" stroke-width="2.5" />
              <circle cx="76" cy="58" r="9" fill="#1e2538" stroke="#fed7aa" stroke-width="1" />
              <circle cx="76" cy="58" r="3.5" fill="#ffffff" />
              <path d="M76 49 C76 36, 96 34, 104 32" stroke="#525f7a" stroke-width="3" fill="none" stroke-linecap="round" />
              <path d="M80 51 C84 38, 102 36, 110 32" stroke="#3b4458" stroke-width="3" fill="none" stroke-linecap="round" />
              <rect x="104" y="42" width="3.5" height="34" rx="1.5" fill="#fbbf24" />
              <rect x="110" y="42" width="3.5" height="34" rx="1.5" fill="#f97316" />
              <rect x="46" y="86" width="86" height="24" rx="3.5" fill="#181e2c" stroke="#3e4a68" stroke-width="1.5" />
              <rect x="50" y="90" width="78" height="3.5" rx="1" fill="#f97316" />
              <circle cx="68" cy="100" r="5.5" fill="#0e111a" stroke="#f97316" stroke-width="1.5" />
              <circle cx="92" cy="100" r="5.5" fill="#0e111a" stroke="#f97316" stroke-width="1.5" />
              <text x="110" y="103" fill="#fed7aa" font-size="5" font-family="'JetBrains Mono', monospace" font-weight="800">RTX</text>
              <rect x="40" y="122" width="98" height="38" fill="#0d0f17" stroke="#262f43" stroke-width="1" />
              <text x="48" y="145" fill="#f97316" font-size="7.5" font-family="'JetBrains Mono', monospace" font-weight="800" letter-spacing="0.1em">PLAYER TWO 1440P</text>
              <rect x="46" y="166" width="18" height="5" rx="2" fill="#202433" />
              <rect x="136" y="166" width="18" height="5" rx="2" fill="#202433" />
              <line x1="46" y1="24" x2="124" y2="152" stroke="#ffffff" stroke-width="1.2" opacity="0.1" stroke-linecap="round" />
            </svg>
          </div>

          <div class="player-meta-bar">
            <span class="player-badge-pill bg-orange-500/15 text-orange-400 border border-orange-500/30">PLAYER: TWO</span>
            <span class="text-xs text-orange-400 font-semibold">1440p High FPS</span>
          </div>

          <h3 class="player-card-title">1440p Sweet Spot Gaming</h3>
          <p class="player-card-desc">
            Kombinasi 6-core Intel Core i5 dengan GeForce RTX 4060 dan RAM 32GB untuk gaming AAA ultra serta video editing lancar.
          </p>

          <div class="player-specs-box">
            <div class="player-spec-row"><span class="player-spec-dot"></span><span>Intel Core i5-12400F Box</span></div>
            <div class="player-spec-row"><span class="player-spec-dot"></span><span>Colorful RTX 4060 8GB DLSS 3</span></div>
            <div class="player-spec-row"><span class="player-spec-dot"></span><span>Corsair LPX 32GB (2x16GB)</span></div>
            <div class="player-spec-row"><span class="player-spec-dot"></span><span>ADATA Legend 850 1TB Gen4</span></div>
          </div>
        </div>

        <div>
          <div class="player-price-row">
            <span class="player-price-label">Estimasi Siap Rakit</span>
            <div class="player-price-val text-amber-400">Est. Rp 11.840.000</div>
          </div>
          <button type="button" class="btn-player-load" data-action="load-template" data-template-id="player-performance">
            <svg class="ui-icon text-white" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
            </svg>
            <span>Muat ke Builder</span>
          </button>
        </div>
      </article>

      <!-- Player Three -->
      <article class="player-card">
        <div>
          <div class="player-illustration-wrap">
            <svg class="player-illustration" viewBox="0 0 200 180" fill="none" xmlns="http://www.w3.org/2000/svg"
              aria-label="Ilustrasi PC Player Three">
              <defs>
                <linearGradient id="p3-outer" x1="0" y1="0" x2="200" y2="180" gradientUnits="userSpaceOnUse">
                  <stop offset="0%" stop-color="#24211a" />
                  <stop offset="100%" stop-color="#0e0d0b" />
                </linearGradient>
                <linearGradient id="p3-glass" x1="30" y1="15" x2="170" y2="165" gradientUnits="userSpaceOnUse">
                  <stop offset="0%" stop-color="#fbbf24" stop-opacity="0.14" />
                  <stop offset="50%" stop-color="#12100d" stop-opacity="0.8" />
                  <stop offset="100%" stop-color="#060504" stop-opacity="0.95" />
                </linearGradient>
                <linearGradient id="p3-gold" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0%" stop-color="#fef08a" />
                  <stop offset="100%" stop-color="#f59e0b" />
                </linearGradient>
              </defs>
              <rect x="30" y="12" width="140" height="154" rx="9" fill="url(#p3-outer)" stroke="#f59e0b" stroke-width="2" stroke-opacity="0.5" />
              <path d="M144 14H165C167.8 14 170 16.2 170 19V157C170 159.8 167.8 162 165 162H144V14Z" fill="#14120e" stroke="#383020" stroke-width="1.5" />
              <circle cx="157" cy="42" r="8" stroke="url(#p3-gold)" stroke-width="1.8" fill="#0d0c09" />
              <circle cx="157" cy="88" r="8" stroke="url(#p3-gold)" stroke-width="1.8" fill="#0d0c09" />
              <circle cx="157" cy="134" r="8" stroke="url(#p3-gold)" stroke-width="1.8" fill="#0d0c09" />
              <rect x="36" y="18" width="104" height="142" rx="6" fill="url(#p3-glass)" stroke="#3a301e" stroke-width="1.5" />
              <rect x="42" y="22" width="92" height="9" rx="2" fill="#241e12" stroke="#f59e0b" stroke-width="1" />
              <line x1="54" y1="23" x2="54" y2="30" stroke="#fbbf24" stroke-width="1.5" />
              <line x1="72" y1="23" x2="72" y2="30" stroke="#fbbf24" stroke-width="1.5" />
              <line x1="90" y1="23" x2="90" y2="30" stroke="#fbbf24" stroke-width="1.5" />
              <line x1="108" y1="23" x2="108" y2="30" stroke="#fbbf24" stroke-width="1.5" />
              <line x1="124" y1="23" x2="124" y2="30" stroke="#fbbf24" stroke-width="1.5" />
              <rect x="68" y="46" width="24" height="24" rx="4" fill="#14110b" stroke="#fbbf24" stroke-width="2" />
              <text x="71" y="61" fill="#fbbf24" font-size="7" font-family="'JetBrains Mono', monospace" font-weight="900">38°C</text>
              <path d="M80 46 C80 34, 102 32, 110 30" stroke="#ca8a04" stroke-width="2.5" fill="none" stroke-linecap="round" />
              <path d="M84 48 C88 36, 110 34, 118 30" stroke="#a16207" stroke-width="2.5" fill="none" stroke-linecap="round" />
              <rect x="104" y="40" width="3.5" height="34" rx="1.5" fill="#fef08a" />
              <rect x="110" y="40" width="3.5" height="34" rx="1.5" fill="#f59e0b" />
              <rect x="42" y="86" width="92" height="26" rx="4" fill="#241f16" stroke="#504226" stroke-width="1.5" />
              <rect x="46" y="90" width="84" height="3.5" rx="1.5" fill="#fbbf24" />
              <circle cx="56" cy="101" r="5" fill="#0f0d0a" stroke="#f59e0b" stroke-width="1.5" />
              <circle cx="76" cy="101" r="5" fill="#0f0d0a" stroke="#f59e0b" stroke-width="1.5" />
              <circle cx="96" cy="101" r="5" fill="#0f0d0a" stroke="#f59e0b" stroke-width="1.5" />
              <rect x="36" y="124" width="104" height="36" fill="#120f0b" stroke="#382e1b" stroke-width="1" />
              <text x="44" y="145" fill="#fbbf24" font-size="7.5" font-family="'JetBrains Mono', monospace" font-weight="900" letter-spacing="0.1em">FLAGSHIP 4K PRO</text>
              <rect x="44" y="166" width="20" height="5" rx="2" fill="#292318" />
              <rect x="140" y="166" width="20" height="5" rx="2" fill="#292318" />
              <line x1="42" y1="22" x2="132" y2="154" stroke="#ffffff" stroke-width="1.2" opacity="0.1" stroke-linecap="round" />
            </svg>
          </div>

          <div class="player-meta-bar">
            <span class="player-badge-pill bg-amber-500/10 text-amber-400 border border-amber-500/25">PLAYER: THREE</span>
            <span class="text-xs text-slate-400 font-semibold">4K / Ray Tracing</span>
          </div>

          <h3 class="player-card-title">Flagship 4K &amp; Creator Station</h3>
          <p class="player-card-desc">
            Monster bertenaga AMD Ryzen 7 7800X3D (3D V-Cache) dan RTX 4070 SUPER 12GB dengan memori DDR5 6000MHz performa tertinggi.
          </p>

          <div class="player-specs-box">
            <div class="player-spec-row"><span class="player-spec-dot"></span><span>AMD Ryzen 7 7800X3D Box</span></div>
            <div class="player-spec-row"><span class="player-spec-dot"></span><span>ZOTAC RTX 4070 SUPER 12GB</span></div>
            <div class="player-spec-row"><span class="player-spec-dot"></span><span>Corsair 32GB DDR5 6000MHz</span></div>
            <div class="player-spec-row"><span class="player-spec-dot"></span><span>Samsung 980 PRO 1TB Gen4</span></div>
          </div>
        </div>

        <div>
          <div class="player-price-row">
            <span class="player-price-label">Estimasi Siap Rakit</span>
            <div class="player-price-val text-emerald-400">Est. Rp 27.030.000</div>
          </div>
          <button type="button" class="btn-player-load" data-action="load-template" data-template-id="player-ultimate">
            <svg class="ui-icon text-amber-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
            </svg>
            <span>Muat ke Builder</span>
          </button>
        </div>
      </article>

    </div>
  </section>

  <!-- 5. THE CORE CONFIGURATOR SECTION (FR1, FR2, FR3, FR4, FR5) -->
  <section id="builder-section" class="builder-section">
    <div class="builder-header">
      <div class="builder-header-pill">
        <span class="builder-header-pill-dot"></span>
        <span class="builder-header-tag">Interactive Custom Rig Builder</span>
      </div>
      <h2 class="builder-title">PayCom Configurator</h2>
      <p class="builder-subtitle">
        Pilih komponen secara bertahap. Sistem menyaring kompatibilitas socket processor, tipe memori RAM, dan form
        factor casing secara real-time, lalu simpan hasilnya ke database MySQL.
      </p>
    </div>

    <!-- Banner Peringatan Gangguan API Harga (Failure State 1 PRD) -->
    <div id="api-status-banner" class="api-warning-banner hidden mb-6" role="alert">
      <div class="api-warning-content">
        <svg class="ui-icon text-amber-400 shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
          <line x1="12" y1="9" x2="12" y2="13"></line>
          <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
        <span><strong>Layanan Harga sedang gangguan.</strong> Anda tetap bisa cek kompatibilitas dan menginput harga manual per komponen.</span>
      </div>
    </div>

    <!-- 2-Column Grid Layout: Komponen di Kiri, Ringkasan di Kanan -->
    <div class="builder-layout" id="builder-container">

      <!-- Kolom Kiri: Smart Component Selector Slots 1 s/d 7 -->
      <section class="selector-column" aria-labelledby="builder-section" id="component-selector">
        <div class="component-slots-list" id="component-slots-container">
          <!-- Di-render secara dinamis oleh js/app.js -->
        </div>
      </section>

      <!-- Kolom Kanan: Sticky Sidebar Ringkasan Rakitan & Simpan ke MySQL -->
      <aside class="summary-column" aria-label="Ringkasan Rakitan">
        <div class="summary-card">
          <div class="summary-header">
            <h3 class="summary-title">Ringkasan Rakitan</h3>
            <span class="text-[11px] font-mono text-slate-500 uppercase tracking-widest">PAYCOM BLD</span>
          </div>

          <!-- Indikator Status Kompatibilitas Besar -->
          <div id="compatibility-status-badge" class="status-badge badge-incomplete" aria-live="polite">
            <span class="badge-icon">
              <svg class="ui-icon text-slate-400" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
              </svg>
            </span>
            <span class="badge-text">Konfigurasi Belum Lengkap</span>
          </div>

          <!-- Rincian Metrik & Daya Listrik (FR3) -->
          <div class="summary-metrics">
            <div class="metric-row">
              <span class="metric-label">Estimasi Beban Daya:</span>
              <span class="metric-value text-white" id="summary-wattage-total">0 Watt</span>
            </div>

            <div>
              <div class="flex justify-between text-[11px] text-slate-400 font-mono mb-1">
                <span>Power Headroom</span>
                <span>Max 850W</span>
              </div>
              <div class="wattage-gauge-track">
                <div class="wattage-gauge-fill" id="summary-wattage-bar"></div>
              </div>
            </div>

            <div class="metric-row">
              <span class="metric-label">Saran Minimal PSU:</span>
              <span class="metric-value text-amber-300" id="summary-psu-recommendation">-</span>
            </div>

            <div class="metric-row metric-total">
              <span class="metric-label">Total Estimasi Harga:</span>
              <span class="metric-value text-amber-400" id="summary-total-price">Est. Rp 0</span>
            </div>
          </div>

          <!-- Panel Hardware Warnings (FR4) -->
          <div class="warnings-section">
            <div class="warnings-header-label">Validasi Kompatibilitas</div>
            <div id="hardware-warnings-panel" class="warnings-list" aria-live="assertive">
              <div class="warning-empty">
                <svg class="ui-icon text-emerald-400 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Belum ada komponen yang dipilih.</span>
              </div>
            </div>
          </div>

          <!-- Form Simpan Rakitan Langsung ke Database MySQL (POST /builds/store) -->
          <form method="POST" action="<?= Controller::e(Controller::url('/builds/store')) ?>"
                id="save-build-db-form"
                class="mt-5 pt-5 border-t border-white/10 space-y-3">
            <input type="hidden" name="from_builder" value="1">
            <?php foreach (['cpu', 'motherboard', 'ram', 'gpu', 'storage', 'psu', 'case'] as $cat): ?>
              <input type="hidden" name="components[<?= $cat ?>]" id="db-input-<?= $cat ?>" value="">
              <input type="hidden" name="custom_prices[<?= $cat ?>]" id="db-price-<?= $cat ?>" value="">
            <?php endforeach; ?>

            <div class="text-xs font-bold uppercase tracking-wider text-amber-400">Simpan ke Database MySQL</div>

            <div>
              <label for="builder-save-name" class="block text-xs text-slate-300 mb-1">Nama Rakitan</label>
              <input type="text" id="builder-save-name" name="build_name"
                     value="<?= Controller::e($oldBuildName) ?>"
                     placeholder="Contoh: PC Gaming 1080p Budget"
                     class="w-full px-3 py-2 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
              <?php if (!empty($errors['build_name'])): ?>
                <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['build_name']) ?></p>
              <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
              <div>
                <label for="builder-save-author" class="block text-xs text-slate-300 mb-1">Nama Perakit</label>
                <input type="text" id="builder-save-author" name="builder_name"
                       value="<?= Controller::e($oldBuilderName) ?>"
                       placeholder="Contoh: Dicky_208"
                       class="w-full px-3 py-2 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
                <?php if (!empty($errors['builder_name'])): ?>
                  <p class="text-xs text-rose-400 mt-1"><?= Controller::e($errors['builder_name']) ?></p>
                <?php endif; ?>
              </div>

              <div>
                <label for="builder-save-purpose" class="block text-xs text-slate-300 mb-1">Kebutuhan</label>
                <select id="builder-save-purpose" name="purpose"
                        class="w-full px-3 py-2 rounded-lg bg-[#0c0e14] border border-white/15 text-sm text-white focus:border-orange-500 focus:outline-none">
                  <?php foreach (\App\Models\BuildModel::ALLOWED_PURPOSES as $purposeOpt): ?>
                    <option value="<?= Controller::e($purposeOpt) ?>" <?= $oldPurpose === $purposeOpt ? 'selected' : '' ?>>
                      <?= Controller::e($purposeOpt) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div>
              <label for="builder-save-notes" class="block text-xs text-slate-300 mb-1">Catatan (Opsional)</label>
              <textarea id="builder-save-notes" name="notes" rows="2"
                        placeholder="Catatan tambahan untuk toko/teknisi..."
                        class="w-full px-3 py-2 rounded-lg bg-[#0c0e14] border border-white/15 text-xs text-white focus:border-orange-500 focus:outline-none"><?= Controller::e($oldNotes) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block" id="btn-save-build-db" disabled>
              <span>Simpan Rakitan ke MySQL</span>
            </button>
          </form>

          <!-- Tombol Aksi Ekspor (FR5) -->
          <div class="summary-actions mt-4">
            <button type="button" class="btn btn-secondary btn-block" id="btn-export-build" disabled>
              <svg class="ui-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
              </svg>
              <span>Cetak / Simpan PDF</span>
            </button>
            <button type="button" class="btn btn-secondary btn-block" id="btn-copy-build" disabled>
              <svg class="ui-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
              </svg>
              <span>Salin Teks Ringkasan</span>
            </button>
            <button type="button" class="btn btn-secondary btn-block" id="btn-reset-build">
              <svg class="ui-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                <path d="M3 3v5h5"></path>
                <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"></path>
                <path d="M16 16h5v5"></path>
              </svg>
              <span>Reset Rakitan</span>
            </button>
          </div>
        </div>
      </aside>

    </div>
  </section>

  <?php if (!empty($recentBuilds)): ?>
    <!-- 6. Rakitan Terbaru dari Database MySQL -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 pb-16">
      <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
          <span class="text-xs font-mono uppercase tracking-widest text-amber-400">Database MySQL Live</span>
          <h2 class="text-2xl font-bold text-white mt-1">Rakitan Tersimpan Terbaru</h2>
        </div>
        <a href="<?= Controller::e(Controller::url('/builds')) ?>" class="text-sm font-semibold text-orange-400 hover:text-orange-300">
          Lihat Semua Rakitan &rarr;
        </a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <?php foreach ($recentBuilds as $b): ?>
          <article class="bg-[#141721] border border-white/10 rounded-2xl p-5 flex flex-col justify-between gap-4 hover:border-orange-500/40 transition-colors">
            <div>
              <div class="flex items-center justify-between gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-orange-500/15 text-orange-300 border border-orange-500/30">
                  <?= Controller::e($b['purpose']) ?>
                </span>
                <span class="text-[11px] font-mono text-slate-400"><?= (int) $b['items_count'] ?> komponen</span>
              </div>
              <h3 class="text-lg font-bold text-white"><?= Controller::e($b['build_name']) ?></h3>
              <p class="text-xs text-slate-400 mt-1">Perakit: <strong class="text-slate-200"><?= Controller::e($b['builder_name']) ?></strong></p>
              <div class="mt-3 text-xs text-slate-300">
                Status: <span class="font-semibold text-emerald-400"><?= Controller::e($b['compatibility_status']) ?></span>
              </div>
            </div>

            <div class="pt-3 border-t border-white/10 flex items-center justify-between gap-2">
              <div>
                <div class="text-xs text-slate-400"><?= (int) $b['total_wattage'] ?>W &bull; PSU min <?= (int) $b['recommended_psu'] ?>W</div>
                <div class="text-base font-bold text-amber-400"><?= Controller::e(Controller::formatRupiah((int) $b['total_price'])) ?></div>
              </div>
              <a href="<?= Controller::e(Controller::url('/builds/show?id=' . (int) $b['id'])) ?>" class="btn btn-secondary btn-sm">
                Detail
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- 7. FAQ Accordion Section -->
  <section id="faq-section" class="faq-section">
    <div class="faq-header">
      <h2 class="player-section-title">Pertanyaan yang Sering Diajukan</h2>
    </div>

    <div class="space-y-3">
      <div class="faq-item">
        <button type="button" class="faq-question-btn">
          <span>Bagaimana jika prosesor saya tidak memiliki kartu grafis internal (iGPU)?</span>
          <svg class="faq-chevron ui-icon text-slate-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <div class="faq-answer hidden">
          Jika prosesor berakhiran F (seperti Intel i3-12100F atau i5-12400F), komputer wajib menggunakan VGA Card
          tambahan (GPU diskrit). Tanpa VGA, monitor tidak akan menampilkan sinyal gambar sama sekali.
        </div>
      </div>

      <div class="faq-item">
        <button type="button" class="faq-question-btn">
          <span>Mengapa kapasitas Power Supply (PSU) yang disarankan lebih besar dari total daya?</span>
          <svg class="faq-chevron ui-icon text-slate-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <div class="faq-answer hidden">
          Komponen komputer memiliki lonjakan daya sesaat (transient power spike) saat bermain game atau menjalankan
          beban komputasi berat. Margin 1.5x menjaga PSU bekerja pada rentang efisiensi puncak (50-70% beban) sehingga
          komponen lebih awet dan tidak mati mendadak.
        </div>
      </div>

      <div class="faq-item">
        <button type="button" class="faq-question-btn">
          <span>Apakah motherboard Micro-ATX (mATX) bisa dipasang di Casing ATX biasa?</span>
          <svg class="faq-chevron ui-icon text-slate-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <div class="faq-answer hidden">
          Bisa. Casing ATX standar selalu mendukung motherboard berukuran lebih kecil seperti Micro-ATX dan Mini-ITX.
          Namun sebaliknya, motherboard ATX berukuran besar tidak akan muat jika dipasang di casing Mini-ITX.
        </div>
      </div>

      <div class="faq-item">
        <button type="button" class="faq-question-btn">
          <span>Apakah estimasi harga ini merupakan harga jaminan pasti di toko fisik?</span>
          <svg class="faq-chevron ui-icon text-slate-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <div class="faq-answer hidden">
          Harga yang dicantumkan merupakan estimasi rata-rata marketplace online Indonesia. Harga di toko fisik lokal
          dapat berbeda tergantung promo toko, garansi distributor, dan ketersediaan stok fisik.
        </div>
      </div>
    </div>
  </section>

</main>

<!-- Modal Dialog Pemilihan Komponen (Pop-up Window FR1 & Custom Input Failure State 2) -->
<div id="component-modal-overlay" class="modal-overlay hidden" role="dialog" aria-modal="true"
  aria-labelledby="modal-category-title">
  <div class="modal-dialog">
    <div class="modal-header">
      <h3 id="modal-category-title" class="modal-title">Pilih Komponen</h3>
      <button type="button" class="btn btn-secondary btn-sm" id="btn-close-modal" aria-label="Tutup jendela pemilihan">
        <svg class="ui-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
          stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <div class="modal-search-box">
      <input type="text" id="modal-search-input" class="search-input"
        placeholder="Ketik nama komponen atau nomor seri spesifikasi..." aria-label="Cari komponen" />
    </div>

    <div class="modal-body">
      <div id="modal-component-list" class="flex flex-col gap-2"></div>

      <div id="modal-custom-form" class="custom-component-form hidden">
        <h4>Input Komponen Custom</h4>
        <p class="body-secondary">
          Gunakan fitur ini jika komponen incaran Anda tidak ada dalam daftar bawaan. Status rakitan akan berubah
          menjadi <em>"Kompatibilitas Custom Tidak Dijamin"</em>.
        </p>

        <div class="form-group">
          <label for="custom-comp-name">Nama Komponen / Merek:</label>
          <input type="text" id="custom-comp-name" class="form-input" placeholder="Contoh: Intel Core i5-11400F" />
        </div>

        <div class="form-group">
          <label for="custom-comp-price">Estimasi Harga (Rp):</label>
          <input type="number" id="custom-comp-price" class="form-input" placeholder="Contoh: 1800000" />
        </div>

        <div id="custom-dynamic-fields"></div>

        <div style="display: flex; gap: 8px; margin-top: 12px;">
          <button type="button" class="btn btn-primary" id="btn-save-custom">Tambahkan ke Rakitan</button>
          <button type="button" class="btn btn-secondary" id="btn-cancel-custom">Batal</button>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-secondary btn-sm" id="btn-open-custom-form">
        <svg class="ui-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
          stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Komponen Tidak Ada? Input Custom</span>
      </button>
    </div>
  </div>
</div>

<!-- Sheet Dokumen Cetak / Export PDF (FR5) -->
<div id="printable-build-sheet" aria-hidden="true">
  <div class="print-header">
    <div class="print-brand">
      <h1>DAFTAR SPESIFIKASI RAKITAN PC</h1>
      <p>Digenerate melalui PayCom Builder - Format Validasi Toko Komputer &amp; Teknisi</p>
    </div>
    <div class="print-meta">
      <div id="print-status-badge" class="print-badge">100% KOMPATIBEL</div>
      <div id="print-date" class="print-date"></div>
    </div>
  </div>

  <div class="print-summary-box">
    <div class="print-summary-item">
      <span>Total Kebutuhan Daya:</span> <strong id="print-total-wattage">0 Watt</strong>
    </div>
    <div class="print-summary-item">
      <span>Rekomendasi Kapasitas PSU:</span> <strong id="print-psu-rec">-</strong>
    </div>
    <div class="print-summary-item">
      <span>Estimasi Total Biaya:</span> <strong id="print-total-price">Est. Rp 0</strong>
    </div>
  </div>

  <table class="print-table">
    <thead>
      <tr>
        <th class="col-num">No</th>
        <th class="col-cat">Kategori</th>
        <th class="col-name">Spesifikasi Komponen</th>
        <th class="col-price">Estimasi Harga</th>
      </tr>
    </thead>
    <tbody id="print-table-body"></tbody>
  </table>

  <div class="print-footer-info">
    <p><strong>Catatan Teknis untuk Toko/Teknisi:</strong></p>
    <p>1. Daftar ini telah melalui pemeriksaan kompatibilitas otomatis meliputi socket prosesor, slot RAM, dan form factor motherboard.</p>
    <p>2. Harga yang tercantum merupakan estimasi rata-rata online dan dapat berbeda dengan ketersediaan stok fisik di toko.</p>
  </div>
</div>

<?php if (!empty($groupedComponents)): ?>
<script>
  window.__PAYCOM_DB_COMPONENTS__ = <?= json_encode($groupedComponents, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  <?php if (!empty($preloadedBuild)): ?>
  window.__PAYCOM_PRELOADED_BUILD__ = <?= json_encode($preloadedBuild, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  <?php endif; ?>
</script>
<?php endif; ?>

