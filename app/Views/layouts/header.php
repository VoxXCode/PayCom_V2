<?php
use App\Core\Controller;

$base = $basePath ?? '';
$assetPrefix = $base === '' ? '' : $base;
$active = $activeNav ?? 'builder';
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= Controller::e($pageTitle ?? 'PayCom BLD - Custom PC Builder') ?></title>
  <meta name="description"
    content="Platform perakit PC modern berbasis PHP MVC & MySQL dengan validasi otomatis socket processor, kompatibilitas RAM, kalkulator daya (Wattage), dan manajemen rakitan.">

  <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">

  <!-- Tailwind CSS v4 Browser Runtime -->
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <style type="text/tailwindcss">
    @theme {
      --color-brand-bg: #0b0c10;
      --color-brand-surface: #141721;
      --color-brand-hover: #1a1e2b;
      --color-brand-primary: #f97316;
      --color-brand-primary-hover: #ea580c;
      --color-brand-gold: #fbbf24;
      --color-brand-success: #10b981;
      --color-brand-danger: #f43f5e;
      --color-brand-warning: #f59e0b;
      --color-brand-muted: #94a3b8;
    }
  </style>

  <!-- Stylesheets -->
  <link rel="stylesheet" href="<?= Controller::e($assetPrefix) ?>/src/main.css">
  <link rel="stylesheet" href="<?= Controller::e($assetPrefix) ?>/css/styles.css?v=8">
  <link rel="stylesheet" href="<?= Controller::e($assetPrefix) ?>/css/print.css">
</head>

<body
  class="bg-[#0b0c10] text-slate-100 min-h-screen flex flex-col font-sans antialiased selection:bg-orange-600 selection:text-white">

  <!-- 1. Announcement Status Bar -->
  <aside class="nzxt-announcement-bar">
    <div class="announcement-inner">
      <span class="pulse-dot"></span>
      <span>GARANSI 100% KOMPATIBILITAS HARDWARE &bull; ARSITEKTUR PHP MVC + MYSQL PDO &bull; SIAP EKSPOR PDF TOKO</span>
    </div>
  </aside>

  <!-- 2. Header & Navigation Bar -->
  <header class="app-header">
    <div class="header-container">

      <!-- Brand Logo & Studio Mark -->
      <a href="<?= Controller::e(Controller::url('/')) ?>" class="brand-area group" aria-label="PayCom BLD Beranda">
        <div class="brand-badge-box">
          <svg class="ui-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect x="4" y="4" width="16" height="16" rx="3"></rect>
            <rect x="9" y="9" width="6" height="6"></rect>
            <line x1="9" y1="1" x2="9" y2="4"></line>
            <line x1="15" y1="1" x2="15" y2="4"></line>
            <line x1="9" y1="20" x2="9" y2="23"></line>
            <line x1="15" y1="20" x2="15" y2="23"></line>
            <line x1="20" y1="9" x2="23" y2="9"></line>
            <line x1="20" y1="14" x2="23" y2="14"></line>
            <line x1="1" y1="9" x2="4" y2="9"></line>
            <line x1="1" y1="14" x2="4" y2="14"></line>
          </svg>
        </div>
        <div class="brand-text-wrap">
          <div class="brand-title-row">
            <span class="brand-title">PAYCOM</span>
            <span class="brand-pill">BLD</span>
          </div>
          <span class="brand-subtitle">CUSTOM RIG STUDIO</span>
        </div>
      </a>

      <!-- Navigation Links -->
      <nav aria-label="Navigasi Utama">
        <ul class="nav-links flex items-center gap-2">
          <li>
            <a href="<?= Controller::e(Controller::url('/')) ?>"
               class="px-3 py-2 rounded-lg text-xs font-semibold tracking-wide transition-colors <?= $active === 'builder' ? 'bg-orange-500/15 text-orange-400 border border-orange-500/30' : 'text-slate-300 hover:text-white hover:bg-white/5' ?>">
              Konfigurator
            </a>
          </li>
          <li>
            <a href="<?= Controller::e(Controller::url('/components')) ?>"
               class="px-3 py-2 rounded-lg text-xs font-semibold tracking-wide transition-colors <?= $active === 'components' ? 'bg-orange-500/15 text-orange-400 border border-orange-500/30' : 'text-slate-300 hover:text-white hover:bg-white/5' ?>">
              Katalog Komponen
            </a>
          </li>
          <li>
            <a href="<?= Controller::e(Controller::url('/builds')) ?>"
               class="px-3 py-2 rounded-lg text-xs font-semibold tracking-wide transition-colors <?= $active === 'builds' ? 'bg-orange-500/15 text-orange-400 border border-orange-500/30' : 'text-slate-300 hover:text-white hover:bg-white/5' ?>">
              Rakitan Tersimpan
            </a>
          </li>
        </ul>
      </nav>

      <!-- Header Tools & Power Gauge -->
      <div class="header-tools">
        <?php if ($active === 'builder'): ?>
          <div class="top-wattage-card" id="top-wattage-display" aria-live="polite">
            <svg class="ui-icon text-amber-400" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
            </svg>
            <span class="wattage-val font-bold text-amber-400">0W</span>
            <span class="wattage-rec text-slate-400 text-xs">(Pilih CPU/GPU)</span>
          </div>

          <button type="button" class="btn btn-secondary btn-sm" id="btn-toggle-api-status"
            title="Uji penanganan kegagalan API Harga sesuai PRD">
            Simulasi: API Normal
          </button>

          <a href="#builder-section" class="btn-header-cta">
            Mulai Rakit
          </a>
        <?php else: ?>
          <a href="<?= Controller::e(Controller::url('/builds/create')) ?>" class="btn btn-secondary btn-sm">
            + Rakitan Baru
          </a>
          <a href="<?= Controller::e(Controller::url('/')) ?>#builder-section" class="btn-header-cta">
            Buka Konfigurator
          </a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <?php if (!empty($flash)): ?>
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 mt-5">
      <div role="alert"
           class="rounded-xl px-4 py-3 border text-sm flex items-center justify-between gap-3 <?= $flash['type'] === 'success' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300' : 'bg-rose-500/10 border-rose-500/30 text-rose-300' ?>">
        <span><?= Controller::e($flash['message']) ?></span>
        <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-xs opacity-75 hover:opacity-100 px-2 py-1">Tutup</button>
      </div>
    </div>
  <?php endif; ?>

