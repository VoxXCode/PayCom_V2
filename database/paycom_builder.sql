-- ============================================================================
-- PayCom BLD - Skema Database MySQL & Data Awal (Seed)
-- Arsitektur: PHP MVC + MySQL (PDO, Prepared Statements)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `paycom_builder`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `paycom_builder`;

-- ----------------------------------------------------------------------------
-- Tabel 1: components (Katalog Komponen Hardware PC)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `components` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(100) NOT NULL,
  `type` ENUM('cpu', 'motherboard', 'ram', 'gpu', 'storage', 'psu', 'case') NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `base_price` INT UNSIGNED NOT NULL DEFAULT 0,
  `socket` VARCHAR(30) DEFAULT NULL,
  `tdp` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `has_igpu` TINYINT(1) NOT NULL DEFAULT 0,
  `form_factor` VARCHAR(20) DEFAULT NULL,
  `ram_type` VARCHAR(20) DEFAULT NULL,
  `capacity_gb` SMALLINT UNSIGNED DEFAULT NULL,
  `speed_mhz` SMALLINT UNSIGNED DEFAULT NULL,
  `wattage` SMALLINT UNSIGNED DEFAULT NULL,
  `supported_form_factors` VARCHAR(100) DEFAULT NULL,
  `specs_summary` VARCHAR(255) NOT NULL DEFAULT '',
  `is_custom` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_components_slug` (`slug`),
  KEY `idx_components_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabel 2: pc_builds (Daftar Rakitan PC Tersimpan)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pc_builds` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `build_name` VARCHAR(120) NOT NULL,
  `builder_name` VARCHAR(80) NOT NULL,
  `purpose` ENUM('Gaming', 'Produktivitas', 'Editing & 3D', 'Kantor / Umum', 'Lainnya') NOT NULL DEFAULT 'Gaming',
  `notes` TEXT DEFAULT NULL,
  `total_price` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `total_wattage` INT UNSIGNED NOT NULL DEFAULT 0,
  `recommended_psu` INT UNSIGNED NOT NULL DEFAULT 0,
  `compatibility_status` VARCHAR(60) NOT NULL DEFAULT 'Konfigurasi Belum Lengkap',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_builds_purpose` (`purpose`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabel 3: build_items (Relasi Komponen per Rakitan PC)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `build_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `build_id` INT UNSIGNED NOT NULL,
  `component_id` INT UNSIGNED NOT NULL,
  `category` ENUM('cpu', 'motherboard', 'ram', 'gpu', 'storage', 'psu', 'case') NOT NULL,
  `custom_price` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_build_category` (`build_id`, `category`),
  KEY `idx_build_items_component` (`component_id`),
  CONSTRAINT `fk_build_items_build`
    FOREIGN KEY (`build_id`) REFERENCES `pc_builds` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_build_items_component`
    FOREIGN KEY (`component_id`) REFERENCES `components` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Seed Data: 32 Komponen Hardware Pasar Indonesia
-- ----------------------------------------------------------------------------
INSERT IGNORE INTO `components`
(`id`, `slug`, `type`, `name`, `base_price`, `socket`, `tdp`, `has_igpu`, `form_factor`, `ram_type`, `capacity_gb`, `speed_mhz`, `wattage`, `supported_form_factors`, `specs_summary`, `is_custom`) VALUES
-- CPU (8)
(1, 'cpu-intel-i3-12100f', 'cpu', 'Intel Core i3-12100F (Tray + Cooler)', 1350000, 'LGA1700', 58, 0, NULL, NULL, NULL, NULL, NULL, NULL, '4 Core / 8 Thread, 3.3 GHz - 4.3 GHz, TDP 58W, No iGPU', 0),
(2, 'cpu-intel-i5-12400f', 'cpu', 'Intel Core i5-12400F Box', 1950000, 'LGA1700', 65, 0, NULL, NULL, NULL, NULL, NULL, NULL, '6 Core / 12 Thread, 2.5 GHz - 4.4 GHz, TDP 65W, No iGPU', 0),
(3, 'cpu-intel-i5-13400', 'cpu', 'Intel Core i5-13400 Box (with UHD 730)', 3450000, 'LGA1700', 65, 1, NULL, NULL, NULL, NULL, NULL, NULL, '10 Core / 16 Thread, 2.5 GHz - 4.6 GHz, TDP 65W, Intel UHD 730', 0),
(4, 'cpu-intel-i7-14700kf', 'cpu', 'Intel Core i7-14700KF Box', 6450000, 'LGA1700', 125, 0, NULL, NULL, NULL, NULL, NULL, NULL, '20 Core / 28 Thread, Up to 5.6 GHz, TDP 125W, No iGPU', 0),
(5, 'cpu-amd-r5-5600', 'cpu', 'AMD Ryzen 5 5600 Box (with Wraith Stealth)', 1750000, 'AM4', 65, 0, NULL, NULL, NULL, NULL, NULL, NULL, '6 Core / 12 Thread, 3.5 GHz - 4.4 GHz, TDP 65W, No iGPU', 0),
(6, 'cpu-amd-r5-5600g', 'cpu', 'AMD Ryzen 5 5600G Box (with Radeon Graphics)', 1900000, 'AM4', 65, 1, NULL, NULL, NULL, NULL, NULL, NULL, '6 Core / 12 Thread, 3.9 GHz - 4.4 GHz, TDP 65W, Vega 7 iGPU', 0),
(7, 'cpu-amd-r5-7600', 'cpu', 'AMD Ryzen 5 7600 Box (with Radeon Graphics)', 3250000, 'AM5', 65, 1, NULL, NULL, NULL, NULL, NULL, NULL, '6 Core / 12 Thread, 3.8 GHz - 5.1 GHz, TDP 65W, Radeon RDNA2 iGPU', 0),
(8, 'cpu-amd-r7-7800x3d', 'cpu', 'AMD Ryzen 7 7800X3D Box', 6800000, 'AM5', 120, 1, NULL, NULL, NULL, NULL, NULL, NULL, '8 Core / 16 Thread, 3D V-Cache, 4.2 GHz - 5.0 GHz, TDP 120W, Radeon iGPU', 0),

-- Motherboard (9)
(9, 'mobo-asrock-h610m-hdv', 'motherboard', 'ASRock H610M-HDV/M.2', 1100000, 'LGA1700', 0, 0, 'mATX', 'DDR4', NULL, NULL, NULL, NULL, 'Socket LGA1700, mATX, 2x DDR4 Slot, PCIe 4.0, M.2 NVMe', 0),
(10, 'mobo-msi-pro-b760m-p-ddr4', 'motherboard', 'MSI PRO B760M-P DDR4', 1650000, 'LGA1700', 0, 0, 'mATX', 'DDR4', NULL, NULL, NULL, NULL, 'Socket LGA1700, mATX, 4x DDR4 Slot, 2x M.2 PCIe 4.0', 0),
(11, 'mobo-asus-prime-b760-plus-ddr5', 'motherboard', 'ASUS Prime B760-PLUS DDR5', 2600000, 'LGA1700', 0, 0, 'ATX', 'DDR5', NULL, NULL, NULL, NULL, 'Socket LGA1700, ATX, 4x DDR5 Slot, 3x M.2 PCIe 4.0, USB Type-C', 0),
(12, 'mobo-asrock-b760i-lightning-wifi', 'motherboard', 'ASRock B760I Lightning WiFi Mini-ITX', 2950000, 'LGA1700', 0, 0, 'ITX', 'DDR5', NULL, NULL, NULL, NULL, 'Socket LGA1700, Mini-ITX, 2x DDR5 Slot, Wi-Fi 6E, Ultra Compact', 0),
(13, 'mobo-asrock-b450m-hdv', 'motherboard', 'ASRock B450M-HDV R4.0', 850000, 'AM4', 0, 0, 'mATX', 'DDR4', NULL, NULL, NULL, NULL, 'Socket AM4, mATX, 2x DDR4 Slot, 1x Ultra M.2, Hemat Budget', 0),
(14, 'mobo-msi-b550m-pro-vdh-wifi', 'motherboard', 'MSI B550M PRO-VDH WiFi', 1600000, 'AM4', 0, 0, 'mATX', 'DDR4', NULL, NULL, NULL, NULL, 'Socket AM4, mATX, 4x DDR4 Slot, PCIe 4.0, Wi-Fi AC onboard', 0),
(15, 'mobo-asrock-b650m-hdv-m2', 'motherboard', 'ASRock B650M-HDV/M.2', 2050000, 'AM5', 0, 0, 'mATX', 'DDR5', NULL, NULL, NULL, NULL, 'Socket AM5, mATX, 2x DDR5 Slot, Blazing M.2 Gen5, VRM 8+2+1', 0),
(16, 'mobo-gigabyte-b650-aorus-elite-ax', 'motherboard', 'Gigabyte B650 AORUS ELITE AX', 3850000, 'AM5', 0, 0, 'ATX', 'DDR5', NULL, NULL, NULL, NULL, 'Socket AM5, ATX, 4x DDR5 Slot, Wi-Fi 6E, PCIe 5.0 M.2, Heavy Heatsink', 0),
(17, 'mobo-asus-rog-strix-b650i-gaming-wifi', 'motherboard', 'ASUS ROG STRIX B650E-I GAMING WIFI Mini-ITX', 4950000, 'AM5', 0, 0, 'ITX', 'DDR5', NULL, NULL, NULL, NULL, 'Socket AM5, Mini-ITX, 2x DDR5 Slot, PCIe 5.0 Ready, Wi-Fi 6E', 0),

-- RAM (4)
(18, 'ram-team-elite-plus-16gb-ddr4', 'ram', 'Team Elite Plus Black 16GB (2x8GB) DDR4 3200MHz', 520000, NULL, 0, 0, NULL, 'DDR4', 16, 3200, NULL, NULL, '16GB Kit (2x8GB), DDR4 3200MHz, CL22, Heatspreader', 0),
(19, 'ram-corsair-vengeance-lpx-32gb-ddr4', 'ram', 'Corsair Vengeance LPX 32GB (2x16GB) DDR4 3200MHz', 1120000, NULL, 0, 0, NULL, 'DDR4', 32, 3200, NULL, NULL, '32GB Kit (2x16GB), DDR4 3200MHz, Low Profile Heatsink', 0),
(20, 'ram-kingston-fury-beast-16gb-ddr5', 'ram', 'Kingston FURY Beast 16GB (2x8GB) DDR5 5600MHz', 980000, NULL, 0, 0, NULL, 'DDR5', 16, 5600, NULL, NULL, '16GB Kit (2x8GB), DDR5 5600MHz, Intel XMP 3.0 & AMD EXPO', 0),
(21, 'ram-corsair-vengeance-32gb-ddr5-6000', 'ram', 'Corsair Vengeance 32GB (2x16GB) DDR5 6000MHz CL36', 1750000, NULL, 0, 0, NULL, 'DDR5', 32, 6000, NULL, NULL, '32GB Kit (2x16GB), DDR5 6000MHz, Optimal untuk AM5 / Intel 13th-14th', 0),

-- GPU (6)
(22, 'gpu-none', 'gpu', 'Tanpa VGA Card Tambahan (Gunakan Grafis Internal CPU)', 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'Mengandalkan iGPU bawaan prosesor (Pastikan prosesor memiliki iGPU)', 0),
(23, 'gpu-amd-rx-6600-8gb', 'gpu', 'Sapphire Pulse AMD Radeon RX 6600 8GB GDDR6', 3250000, NULL, 132, 0, NULL, NULL, NULL, NULL, NULL, NULL, '8GB GDDR6, 128-bit, TDP 132W, Rekomendasi Gaming 1080p Hemat Daya', 0),
(24, 'gpu-nvidia-rtx-3050-6gb', 'gpu', 'ZOTAC GAMING GeForce RTX 3050 6GB GDDR6', 2650000, NULL, 70, 0, NULL, NULL, NULL, NULL, NULL, NULL, '6GB GDDR6, TDP 70W, Tanpa kabel power tambahan PCIe', 0),
(25, 'gpu-nvidia-rtx-4060-8gb', 'gpu', 'Colorful GeForce RTX 4060 NB DUO 8GB GDDR6', 4850000, NULL, 115, 0, NULL, NULL, NULL, NULL, NULL, NULL, '8GB GDDR6, TDP 115W, DLSS 3 Frame Generation, Sangat Efisien', 0),
(26, 'gpu-nvidia-rtx-4070-super-12gb', 'gpu', 'ZOTAC GAMING GeForce RTX 4070 SUPER Twin Edge 12GB', 10250000, NULL, 220, 0, NULL, NULL, NULL, NULL, NULL, NULL, '12GB GDDR6X, TDP 220W, Performa 1440p Ultra & Ray Tracing', 0),
(27, 'gpu-amd-rx-7800-xt-16gb', 'gpu', 'PowerColor Hellhound Radeon RX 7800 XT 16GB', 8950000, NULL, 263, 0, NULL, NULL, NULL, NULL, NULL, NULL, '16GB GDDR6, 256-bit, TDP 263W, VRAM Besar untuk Rendering & 1440p', 0),

-- Storage (4)
(28, 'storage-kioxia-exceria-g2-500gb', 'storage', 'Kioxia Exceria G2 500GB NVMe M.2 SSD', 540000, NULL, 5, 0, NULL, NULL, NULL, NULL, NULL, NULL, '500GB M.2 2280 PCIe 3.0, Read up to 2,100 MB/s with DRAM', 0),
(29, 'storage-adata-legend-850-1tb', 'storage', 'ADATA Legend 850 LITE 1TB PCIe Gen4 x4 NVMe M.2', 1050000, NULL, 6, 0, NULL, NULL, NULL, NULL, NULL, NULL, '1TB M.2 NVMe Gen4, Read up to 5,000 MB/s, Dilengkapi Heatsink Tipis', 0),
(30, 'storage-samsung-980-pro-1tb', 'storage', 'Samsung 980 PRO 1TB PCIe 4.0 NVMe M.2 SSD', 1650000, NULL, 7, 0, NULL, NULL, NULL, NULL, NULL, NULL, '1TB M.2 NVMe Gen4 Flagship, Read up to 7,000 MB/s, DRAM Cache', 0),
(31, 'storage-lexar-ns100-512gb-sata', 'storage', 'Lexar NS100 512GB 2.5 inch SATA III SSD', 430000, NULL, 4, 0, NULL, NULL, NULL, NULL, NULL, NULL, '512GB 2.5" SATA III, Kecepatan hingga 550 MB/s', 0),

-- PSU (5)
(32, 'psu-fsp-hv-pro-550w', 'psu', 'FSP HV PRO 550W 80+ Bronze', 630000, NULL, 0, 0, NULL, NULL, NULL, NULL, 550, NULL, '550 Watt, 80 Plus Bronze Certified, Flat Black Cable', 0),
(33, 'psu-deepcool-pk650d-650w', 'psu', 'DeepCool PK650D 650W 80+ Bronze', 770000, NULL, 0, 0, NULL, NULL, NULL, NULL, 650, NULL, '650 Watt, 80 Plus Bronze, ErP Lot 3 2014, Proteksi Lengkap OVP/SCP/OPP', 0),
(34, 'psu-corsair-cx750-750w', 'psu', 'Corsair CX750 750W 80+ Bronze', 1050000, NULL, 0, 0, NULL, NULL, NULL, NULL, 750, NULL, '750 Watt, 80 Plus Bronze, Single +12V Rail, Kipas 120mm Hening', 0),
(35, 'psu-seasonic-focus-gx750-gold', 'psu', 'Seasonic FOCUS GX-750 750W 80+ Gold Fully Modular', 1850000, NULL, 0, 0, NULL, NULL, NULL, NULL, 750, NULL, '750 Watt, 80 Plus Gold, Full Modular, Garansi Resmi 10 Tahun', 0),
(36, 'psu-msi-mag-a850gl-850w-pcie5', 'psu', 'MSI MAG A850GL PCIE5 850W 80+ Gold ATX 3.0', 1890000, NULL, 0, 0, NULL, NULL, NULL, NULL, 850, NULL, '850 Watt, 80 Plus Gold, Native 16-pin 12V-2x6 PCIe 5.0, Full Modular', 0),

-- Case (4)
(37, 'case-paradox-gaming-trinity-matx', 'case', 'Paradox Gaming Trinity (mATX / Mini-ITX)', 360000, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'mATX,ITX', 'Mendukung Motherboard mATX dan Mini-ITX, Tempered Glass samping, 2x USB 2.0 + 1x USB 3.0', 0),
(38, 'case-cube-gaming-byron-atx', 'case', 'Cube Gaming Byron Mesh Black (ATX / mATX / ITX)', 450000, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'ATX,mATX,ITX', 'Mendukung ATX, mATX, ITX, Front Mesh High Airflow, Free 3 Fan 120mm', 0),
(39, 'case-montech-air-903-base-atx', 'case', 'Montech AIR 903 BASE Black (E-ATX / ATX / mATX / ITX)', 880000, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'ATX,mATX,ITX', 'Support ATX & mATX, Clearance GPU hingga 400mm, USB Type-C Depan', 0),
(40, 'case-jonsbo-t8-plus-itx', 'case', 'Jonsbo T8 Plus Aluminium Mini-ITX Portable', 990000, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 'ITX', 'Hanya mendukung Motherboard Mini-ITX (Khusus rakitan mini kompak)', 0);

-- ----------------------------------------------------------------------------
-- Seed Data: 3 Contoh Rakitan PC Tersimpan (Player One, Two, Three)
-- ----------------------------------------------------------------------------
INSERT IGNORE INTO `pc_builds` (`id`, `build_name`, `builder_name`, `purpose`, `notes`, `total_price`, `total_wattage`, `recommended_psu`, `compatibility_status`) VALUES
(1, 'PC-1 Esports & Casual 1080p', 'Dicky_208', 'Gaming', 'Rakitan efisien dan hemat daya untuk game kompetitif 1080p (Valorant, CS2, Dota 2) dengan budget ramah mahasiswa.', 7750000, 240, 400, '100% Kompatibel'),
(2, 'PC-2 Sweet Spot Gaming 1440p', 'Devano_211', 'Gaming', 'Kombinasi Intel Core i5-12400F, RTX 4060 DLSS 3, dan RAM 32GB untuk gaming 1440p ultra serta editing video.', 11840000, 230, 350, '100% Kompatibel'),
(3, 'PC-3 Flagship 4K & Creator Station', 'Himam_179', 'Editing & 3D', 'Konfigurasi performa tinggi Ryzen 7 7800X3D + RTX 4070 SUPER + DDR5 6000MHz untuk rendering 3D & 4K.', 27030000, 390, 600, '100% Kompatibel');

INSERT IGNORE INTO `build_items` (`build_id`, `component_id`, `category`, `custom_price`) VALUES
-- Build 1 (PC-1)
(1, 1, 'cpu', NULL),
(1, 9, 'motherboard', NULL),
(1, 18, 'ram', NULL),
(1, 23, 'gpu', NULL),
(1, 28, 'storage', NULL),
(1, 32, 'psu', NULL),
(1, 37, 'case', NULL),

-- Build 2 (PC-2)
(2, 2, 'cpu', NULL),
(2, 10, 'motherboard', NULL),
(2, 19, 'ram', NULL),
(2, 25, 'gpu', NULL),
(2, 29, 'storage', NULL),
(2, 33, 'psu', NULL),
(2, 38, 'case', NULL),

-- Build 3 (PC-3)
(3, 8, 'cpu', NULL),
(3, 16, 'motherboard', NULL),
(3, 21, 'ram', NULL),
(3, 26, 'gpu', NULL),
(3, 30, 'storage', NULL),
(3, 35, 'psu', NULL),
(3, 39, 'case', NULL);
