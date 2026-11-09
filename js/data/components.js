// Data komponen PC realistis pasar Indonesia dengan spesifikasi teknis lengkap
// Sesuai sketsa data model PRD: id, type, name, base_price, specs, dan visual gambar (Modul 1 Hal. 20)

const createSvgDataUri = (svgStr) => `data:image/svg+xml;utf8,${encodeURIComponent(svgStr.trim())}`;

export const COMPONENT_IMAGES = {
  cpu: createSvgDataUri(`
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none">
      <rect x="10" y="10" width="44" height="44" rx="4" fill="#262626" stroke="#3b82f6" stroke-width="2"/>
      <rect x="18" y="18" width="28" height="28" rx="2" fill="#3f3f46"/>
      <path d="M26 26h12v12H26z" fill="#1e1e1e"/>
      <circle cx="14" cy="14" r="1.5" fill="#eab308"/>
      <path d="M10 22h-4m4 8h-4m4 8h-4m4 8h-4M54 22h4m-4 8h4m-4 8h4m-4 8h4M22 10V6m8 4V6m8 4V6m8 4V6M22 54v4m8-4v4m8-4v4m8-4v4" stroke="#71717a" stroke-width="2" stroke-linecap="round"/>
    </svg>
  `),
  motherboard: createSvgDataUri(`
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none">
      <rect x="8" y="8" width="48" height="48" rx="4" fill="#1c1917" stroke="#10b981" stroke-width="2"/>
      <rect x="16" y="14" width="16" height="16" rx="2" fill="#292524" stroke="#a1a1aa"/>
      <rect x="38" y="14" width="4" height="22" rx="1" fill="#3b82f6"/>
      <rect x="46" y="14" width="4" height="22" rx="1" fill="#3b82f6"/>
      <rect x="16" y="38" width="34" height="6" rx="1" fill="#eab308"/>
      <circle cx="12" cy="12" r="2" fill="#71717a"/>
      <circle cx="52" cy="12" r="2" fill="#71717a"/>
      <circle cx="12" cy="52" r="2" fill="#71717a"/>
      <circle cx="52" cy="52" r="2" fill="#71717a"/>
    </svg>
  `),
  ram: createSvgDataUri(`
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none">
      <rect x="6" y="20" width="52" height="24" rx="2" fill="#18181b" stroke="#3b82f6" stroke-width="2"/>
      <rect x="12" y="24" width="8" height="10" rx="1" fill="#3f3f46"/>
      <rect x="24" y="24" width="8" height="10" rx="1" fill="#3f3f46"/>
      <rect x="36" y="24" width="8" height="10" rx="1" fill="#3f3f46"/>
      <rect x="48" y="24" width="6" height="10" rx="1" fill="#3f3f46"/>
      <path d="M10 44v4m6-4v4m6-4v4m6-4v4m6-4v4m6-4v4m6-4v4m6-4v4" stroke="#eab308" stroke-width="2" stroke-linecap="round"/>
    </svg>
  `),
  gpu: createSvgDataUri(`
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none">
      <rect x="6" y="14" width="52" height="34" rx="4" fill="#27272a" stroke="#8b5cf6" stroke-width="2"/>
      <circle cx="23" cy="30" r="9" fill="#18181b" stroke="#52525b" stroke-width="2"/>
      <circle cx="43" cy="30" r="9" fill="#18181b" stroke="#52525b" stroke-width="2"/>
      <circle cx="23" cy="30" r="3" fill="#a1a1aa"/>
      <circle cx="43" cy="30" r="3" fill="#a1a1aa"/>
      <rect x="14" y="48" width="30" height="4" fill="#eab308"/>
      <rect x="2" y="18" width="4" height="24" rx="1" fill="#71717a"/>
    </svg>
  `),
  storage: createSvgDataUri(`
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none">
      <rect x="8" y="24" width="48" height="16" rx="2" fill="#18181b" stroke="#06b6d4" stroke-width="2"/>
      <rect x="14" y="28" width="10" height="8" rx="1" fill="#3f3f46"/>
      <rect x="28" y="28" width="16" height="8" rx="1" fill="#27272a"/>
      <circle cx="50" cy="32" r="2.5" fill="#eab308"/>
      <path d="M8 28h-3v8h3" fill="#71717a"/>
    </svg>
  `),
  psu: createSvgDataUri(`
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none">
      <rect x="10" y="12" width="44" height="40" rx="4" fill="#18181b" stroke="#f59e0b" stroke-width="2"/>
      <circle cx="32" cy="32" r="12" fill="#27272a" stroke="#52525b" stroke-width="1.5"/>
      <circle cx="32" cy="32" r="4" fill="#a1a1aa"/>
      <path d="M26 18h12M18 26v12M46 26v12M26 46h12" stroke="#71717a" stroke-linecap="round"/>
      <rect x="42" y="18" width="8" height="5" rx="1" fill="#ef4444"/>
      <rect x="14" y="42" width="12" height="6" rx="1" fill="#3f3f46"/>
    </svg>
  `),
  case: createSvgDataUri(`
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none">
      <rect x="16" y="8" width="32" height="48" rx="3" fill="#18181b" stroke="#64748b" stroke-width="2"/>
      <rect x="20" y="12" width="24" height="36" rx="2" fill="#0f172a" fill-opacity="0.8" stroke="#334155"/>
      <path d="M42 12v36" stroke="#475569" stroke-dasharray="2 2"/>
      <circle cx="32" cy="11" r="1.5" fill="#3b82f6"/>
      <rect x="20" y="50" width="24" height="3" rx="1" fill="#334155"/>
    </svg>
  `)
};

export const HARDWARE_DATABASE = {
  cpu: [
    {
      id: "cpu-intel-i3-12100f",
      type: "cpu",
      name: "Intel Core i3-12100F (Tray + Cooler)",
      base_price: 1350000,
      socket: "LGA1700",
      tdp: 58,
      has_igpu: false,
      specs_summary: "4 Core / 8 Thread, 3.3 GHz - 4.3 GHz, TDP 58W, No iGPU",
      image: COMPONENT_IMAGES.cpu
    },
    {
      id: "cpu-intel-i5-12400f",
      type: "cpu",
      name: "Intel Core i5-12400F Box",
      base_price: 1950000,
      socket: "LGA1700",
      tdp: 65,
      has_igpu: false,
      specs_summary: "6 Core / 12 Thread, 2.5 GHz - 4.4 GHz, TDP 65W, No iGPU",
      image: COMPONENT_IMAGES.cpu
    },
    {
      id: "cpu-intel-i5-13400",
      type: "cpu",
      name: "Intel Core i5-13400 Box (with UHD 730)",
      base_price: 3450000,
      socket: "LGA1700",
      tdp: 65,
      has_igpu: true,
      specs_summary: "10 Core / 16 Thread, 2.5 GHz - 4.6 GHz, TDP 65W, Intel UHD 730",
      image: COMPONENT_IMAGES.cpu
    },
    {
      id: "cpu-intel-i7-14700kf",
      type: "cpu",
      name: "Intel Core i7-14700KF Box",
      base_price: 6450000,
      socket: "LGA1700",
      tdp: 125,
      has_igpu: false,
      specs_summary: "20 Core / 28 Thread, Up to 5.6 GHz, TDP 125W, No iGPU",
      image: COMPONENT_IMAGES.cpu
    },
    {
      id: "cpu-amd-r5-5600",
      type: "cpu",
      name: "AMD Ryzen 5 5600 Box (with Wraith Stealth)",
      base_price: 1750000,
      socket: "AM4",
      tdp: 65,
      has_igpu: false,
      specs_summary: "6 Core / 12 Thread, 3.5 GHz - 4.4 GHz, TDP 65W, No iGPU",
      image: COMPONENT_IMAGES.cpu
    },
    {
      id: "cpu-amd-r5-5600g",
      type: "cpu",
      name: "AMD Ryzen 5 5600G Box (with Radeon Graphics)",
      base_price: 1900000,
      socket: "AM4",
      tdp: 65,
      has_igpu: true,
      specs_summary: "6 Core / 12 Thread, 3.9 GHz - 4.4 GHz, TDP 65W, Vega 7 iGPU",
      image: COMPONENT_IMAGES.cpu
    },
    {
      id: "cpu-amd-r5-7600",
      type: "cpu",
      name: "AMD Ryzen 5 7600 Box (with Radeon Graphics)",
      base_price: 3250000,
      socket: "AM5",
      tdp: 65,
      has_igpu: true,
      specs_summary: "6 Core / 12 Thread, 3.8 GHz - 5.1 GHz, TDP 65W, Radeon RDNA2 iGPU",
      image: COMPONENT_IMAGES.cpu
    },
    {
      id: "cpu-amd-r7-7800x3d",
      type: "cpu",
      name: "AMD Ryzen 7 7800X3D Box",
      base_price: 6800000,
      socket: "AM5",
      tdp: 120,
      has_igpu: true,
      specs_summary: "8 Core / 16 Thread, 3D V-Cache, 4.2 GHz - 5.0 GHz, TDP 120W, Radeon iGPU",
      image: COMPONENT_IMAGES.cpu
    }
  ],

  motherboard: [
    {
      id: "mobo-asrock-h610m-hdv",
      type: "motherboard",
      name: "ASRock H610M-HDV/M.2",
      base_price: 1100000,
      socket: "LGA1700",
      form_factor: "mATX",
      ram_type: "DDR4",
      specs_summary: "Socket LGA1700, mATX, 2x DDR4 Slot, PCIe 4.0, M.2 NVMe",
      image: COMPONENT_IMAGES.motherboard
    },
    {
      id: "mobo-msi-pro-b760m-p-ddr4",
      type: "motherboard",
      name: "MSI PRO B760M-P DDR4",
      base_price: 1650000,
      socket: "LGA1700",
      form_factor: "mATX",
      ram_type: "DDR4",
      specs_summary: "Socket LGA1700, mATX, 4x DDR4 Slot, 2x M.2 PCIe 4.0",
      image: COMPONENT_IMAGES.motherboard
    },
    {
      id: "mobo-asus-prime-b760-plus-ddr5",
      type: "motherboard",
      name: "ASUS Prime B760-PLUS DDR5",
      base_price: 2600000,
      socket: "LGA1700",
      form_factor: "ATX",
      ram_type: "DDR5",
      specs_summary: "Socket LGA1700, ATX, 4x DDR5 Slot, 3x M.2 PCIe 4.0, USB Type-C",
      image: COMPONENT_IMAGES.motherboard
    },
    {
      id: "mobo-asrock-b760i-lightning-wifi",
      type: "motherboard",
      name: "ASRock B760I Lightning WiFi Mini-ITX",
      base_price: 2950000,
      socket: "LGA1700",
      form_factor: "ITX",
      ram_type: "DDR5",
      specs_summary: "Socket LGA1700, Mini-ITX, 2x DDR5 Slot, Wi-Fi 6E, Ultra Compact",
      image: COMPONENT_IMAGES.motherboard
    },
    {
      id: "mobo-asrock-b450m-hdv",
      type: "motherboard",
      name: "ASRock B450M-HDV R4.0",
      base_price: 850000,
      socket: "AM4",
      form_factor: "mATX",
      ram_type: "DDR4",
      specs_summary: "Socket AM4, mATX, 2x DDR4 Slot, 1x Ultra M.2, Hemat Budget",
      image: COMPONENT_IMAGES.motherboard
    },
    {
      id: "mobo-msi-b550m-pro-vdh-wifi",
      type: "motherboard",
      name: "MSI B550M PRO-VDH WiFi",
      base_price: 1600000,
      socket: "AM4",
      form_factor: "mATX",
      ram_type: "DDR4",
      specs_summary: "Socket AM4, mATX, 4x DDR4 Slot, PCIe 4.0, Wi-Fi AC onboard",
      image: COMPONENT_IMAGES.motherboard
    },
    {
      id: "mobo-asrock-b650m-hdv-m2",
      type: "motherboard",
      name: "ASRock B650M-HDV/M.2",
      base_price: 2050000,
      socket: "AM5",
      form_factor: "mATX",
      ram_type: "DDR5",
      specs_summary: "Socket AM5, mATX, 2x DDR5 Slot, Blazing M.2 Gen5, VRM 8+2+1",
      image: COMPONENT_IMAGES.motherboard
    },
    {
      id: "mobo-gigabyte-b650-aorus-elite-ax",
      type: "motherboard",
      name: "Gigabyte B650 AORUS ELITE AX",
      base_price: 3850000,
      socket: "AM5",
      form_factor: "ATX",
      ram_type: "DDR5",
      specs_summary: "Socket AM5, ATX, 4x DDR5 Slot, Wi-Fi 6E, PCIe 5.0 M.2, Heavy Heatsink",
      image: COMPONENT_IMAGES.motherboard
    },
    {
      id: "mobo-asus-rog-strix-b650i-gaming-wifi",
      type: "motherboard",
      name: "ASUS ROG STRIX B650E-I GAMING WIFI Mini-ITX",
      base_price: 4950000,
      socket: "AM5",
      form_factor: "ITX",
      ram_type: "DDR5",
      specs_summary: "Socket AM5, Mini-ITX, 2x DDR5 Slot, PCIe 5.0 Ready, Wi-Fi 6E",
      image: COMPONENT_IMAGES.motherboard
    }
  ],

  ram: [
    {
      id: "ram-team-elite-plus-16gb-ddr4",
      type: "ram",
      name: "Team Elite Plus Black 16GB (2x8GB) DDR4 3200MHz",
      base_price: 520000,
      ram_type: "DDR4",
      capacity_gb: 16,
      speed_mhz: 3200,
      specs_summary: "16GB Kit (2x8GB), DDR4 3200MHz, CL22, Heatspreader",
      image: COMPONENT_IMAGES.ram
    },
    {
      id: "ram-corsair-vengeance-lpx-32gb-ddr4",
      type: "ram",
      name: "Corsair Vengeance LPX 32GB (2x16GB) DDR4 3200MHz",
      base_price: 1120000,
      ram_type: "DDR4",
      capacity_gb: 32,
      speed_mhz: 3200,
      specs_summary: "32GB Kit (2x16GB), DDR4 3200MHz, Low Profile Heatsink",
      image: COMPONENT_IMAGES.ram
    },
    {
      id: "ram-kingston-fury-beast-16gb-ddr5",
      type: "ram",
      name: "Kingston FURY Beast 16GB (2x8GB) DDR5 5600MHz",
      base_price: 980000,
      ram_type: "DDR5",
      capacity_gb: 16,
      speed_mhz: 5600,
      specs_summary: "16GB Kit (2x8GB), DDR5 5600MHz, Intel XMP 3.0 & AMD EXPO",
      image: COMPONENT_IMAGES.ram
    },
    {
      id: "ram-corsair-vengeance-32gb-ddr5-6000",
      type: "ram",
      name: "Corsair Vengeance 32GB (2x16GB) DDR5 6000MHz CL36",
      base_price: 1750000,
      ram_type: "DDR5",
      capacity_gb: 32,
      speed_mhz: 6000,
      specs_summary: "32GB Kit (2x16GB), DDR5 6000MHz, Optimal untuk AM5 / Intel 13th-14th",
      image: COMPONENT_IMAGES.ram
    }
  ],

  gpu: [
    {
      id: "gpu-none",
      type: "gpu",
      name: "Tanpa VGA Card Tambahan (Gunakan Grafis Internal CPU)",
      base_price: 0,
      tdp: 0,
      specs_summary: "Mengandalkan iGPU bawaan prosesor (Pastikan prosesor memiliki iGPU)",
      image: COMPONENT_IMAGES.gpu
    },
    {
      id: "gpu-amd-rx-6600-8gb",
      type: "gpu",
      name: "Sapphire Pulse AMD Radeon RX 6600 8GB GDDR6",
      base_price: 3250000,
      tdp: 132,
      specs_summary: "8GB GDDR6, 128-bit, TDP 132W, Rekomendasi Gaming 1080p Hemat Daya",
      image: COMPONENT_IMAGES.gpu
    },
    {
      id: "gpu-nvidia-rtx-3050-6gb",
      type: "gpu",
      name: "ZOTAC GAMING GeForce RTX 3050 6GB GDDR6",
      base_price: 2650000,
      tdp: 70,
      specs_summary: "6GB GDDR6, TDP 70W, Tanpa kabel power tambahan PCIe",
      image: COMPONENT_IMAGES.gpu
    },
    {
      id: "gpu-nvidia-rtx-4060-8gb",
      type: "gpu",
      name: "Colorful GeForce RTX 4060 NB DUO 8GB GDDR6",
      base_price: 4850000,
      tdp: 115,
      specs_summary: "8GB GDDR6, TDP 115W, DLSS 3 Frame Generation, Sangat Efisien",
      image: COMPONENT_IMAGES.gpu
    },
    {
      id: "gpu-nvidia-rtx-4070-super-12gb",
      type: "gpu",
      name: "ZOTAC GAMING GeForce RTX 4070 SUPER Twin Edge 12GB",
      base_price: 10250000,
      tdp: 220,
      specs_summary: "12GB GDDR6X, TDP 220W, Performa 1440p Ultra & Ray Tracing",
      image: COMPONENT_IMAGES.gpu
    },
    {
      id: "gpu-amd-rx-7800-xt-16gb",
      type: "gpu",
      name: "PowerColor Hellhound Radeon RX 7800 XT 16GB",
      base_price: 8950000,
      tdp: 263,
      specs_summary: "16GB GDDR6, 256-bit, TDP 263W, VRAM Besar untuk Rendering & 1440p",
      image: COMPONENT_IMAGES.gpu
    }
  ],

  storage: [
    {
      id: "storage-kioxia-exceria-g2-500gb",
      type: "storage",
      name: "Kioxia Exceria G2 500GB NVMe M.2 SSD",
      base_price: 540000,
      tdp: 5,
      specs_summary: "500GB M.2 2280 PCIe 3.0, Read up to 2,100 MB/s with DRAM",
      image: COMPONENT_IMAGES.storage
    },
    {
      id: "storage-adata-legend-850-1tb",
      type: "storage",
      name: "ADATA Legend 850 LITE 1TB PCIe Gen4 x4 NVMe M.2",
      base_price: 1050000,
      tdp: 6,
      specs_summary: "1TB M.2 NVMe Gen4, Read up to 5,000 MB/s, Dilengkapi Heatsink Tipis",
      image: COMPONENT_IMAGES.storage
    },
    {
      id: "storage-samsung-980-pro-1tb",
      type: "storage",
      name: "Samsung 980 PRO 1TB PCIe 4.0 NVMe M.2 SSD",
      base_price: 1650000,
      tdp: 7,
      specs_summary: "1TB M.2 NVMe Gen4 Flagship, Read up to 7,000 MB/s, DRAM Cache",
      image: COMPONENT_IMAGES.storage
    },
    {
      id: "storage-lexar-ns100-512gb-sata",
      type: "storage",
      name: "Lexar NS100 512GB 2.5 inch SATA III SSD",
      base_price: 430000,
      tdp: 4,
      specs_summary: "512GB 2.5\" SATA III, Kecepatan hingga 550 MB/s",
      image: COMPONENT_IMAGES.storage
    }
  ],

  psu: [
    {
      id: "psu-fsp-hv-pro-550w",
      type: "psu",
      name: "FSP HV PRO 550W 80+ Bronze",
      base_price: 630000,
      wattage: 550,
      specs_summary: "550 Watt, 80 Plus Bronze Certified, Flat Black Cable",
      image: COMPONENT_IMAGES.psu
    },
    {
      id: "psu-deepcool-pk650d-650w",
      type: "psu",
      name: "DeepCool PK650D 650W 80+ Bronze",
      base_price: 770000,
      wattage: 650,
      specs_summary: "650 Watt, 80 Plus Bronze, ErP Lot 3 2014, Proteksi Lengkap OVP/SCP/OPP",
      image: COMPONENT_IMAGES.psu
    },
    {
      id: "psu-corsair-cx750-750w",
      type: "psu",
      name: "Corsair CX750 750W 80+ Bronze",
      base_price: 1050000,
      wattage: 750,
      specs_summary: "750 Watt, 80 Plus Bronze, Single +12V Rail, Kipas 120mm Hening",
      image: COMPONENT_IMAGES.psu
    },
    {
      id: "psu-seasonic-focus-gx750-gold",
      type: "psu",
      name: "Seasonic FOCUS GX-750 750W 80+ Gold Fully Modular",
      base_price: 1850000,
      wattage: 750,
      specs_summary: "750 Watt, 80 Plus Gold, Full Modular, Garansi Resmi 10 Tahun",
      image: COMPONENT_IMAGES.psu
    },
    {
      id: "psu-msi-mag-a850gl-850w-pcie5",
      type: "psu",
      name: "MSI MAG A850GL PCIE5 850W 80+ Gold ATX 3.0",
      base_price: 1890000,
      wattage: 850,
      specs_summary: "850 Watt, 80 Plus Gold, Native 16-pin 12V-2x6 PCIe 5.0, Full Modular",
      image: COMPONENT_IMAGES.psu
    }
  ],

  case: [
    {
      id: "case-paradox-gaming-trinity-matx",
      type: "case",
      name: "Paradox Gaming Trinity (mATX / Mini-ITX)",
      base_price: 360000,
      supported_form_factors: ["mATX", "ITX"],
      specs_summary: "Mendukung Motherboard mATX dan Mini-ITX, Tempered Glass samping, 2x USB 2.0 + 1x USB 3.0",
      image: COMPONENT_IMAGES.case
    },
    {
      id: "case-cube-gaming-byron-atx",
      type: "case",
      name: "Cube Gaming Byron Mesh Black (ATX / mATX / ITX)",
      base_price: 450000,
      supported_form_factors: ["ATX", "mATX", "ITX"],
      specs_summary: "Mendukung ATX, mATX, ITX, Front Mesh High Airflow, Free 3 Fan 120mm",
      image: COMPONENT_IMAGES.case
    },
    {
      id: "case-montech-air-903-base-atx",
      type: "case",
      name: "Montech AIR 903 BASE Black (E-ATX / ATX / mATX / ITX)",
      base_price: 880000,
      supported_form_factors: ["ATX", "mATX", "ITX"],
      specs_summary: "Support ATX & mATX, Clearance GPU hingga 400mm, USB Type-C Depan",
      image: COMPONENT_IMAGES.case
    },
    {
      id: "case-jonsbo-t8-plus-itx",
      type: "case",
      name: "Jonsbo T8 Plus Aluminium Mini-ITX Portable",
      base_price: 990000,
      supported_form_factors: ["ITX"],
      specs_summary: "Hanya mendukung Motherboard Mini-ITX (Khusus rakitan mini kompak)",
      image: COMPONENT_IMAGES.case
    }
  ]
};

// Urutan pemilihan komponen sesuai alur panduan pemula PRD (FR1)
export const COMPONENT_CATEGORIES = [
  { id: "cpu", label: "CPU (Prosesor)", required: true, description: "Otak utama komputer. Tentukan socket awal untuk motherboard." },
  { id: "motherboard", label: "Motherboard", required: true, description: "Papan sirkuit penghubung. Harus memiliki socket yang sama dengan CPU." },
  { id: "ram", label: "RAM (Memori)", required: true, description: "Memori kerja sistem. Harus cocok dengan tipe DDR pada motherboard." },
  { id: "gpu", label: "GPU (Kartu Grafis / VGA)", required: false, description: "Pengolah grafis untuk monitor dan gaming." },
  { id: "storage", label: "Storage (SSD / Penyimpanan)", required: true, description: "Media penyimpanan sistem operasi dan berkas." },
  { id: "psu", label: "Power Supply Unit (PSU)", required: true, description: "Penyuplai daya listrik. Pastikan kapasitas melebihi kebutuhan sistem." },
  { id: "case", label: "Casing Komputer", required: true, description: "Wadah fisik pelindung komponen. Sesuaikan dengan ukuran motherboard." }
];

// Template Rakitan Siap Pakai Ala NZXT Player Series (v2 Feature PRD)
export const PREBUILT_TEMPLATES = [
  {
    id: "player-starter",
    badge: "PC-1",
    title: "1080p Esports & Casual Rig",
    description: "Ideal untuk gaming kompetitif (Valorant, CS2, Dota 2, GTA V) dengan efisiensi daya maksimal dan budget ramah mahasiswa.",
    target_res: "1080p FHD",
    target_fps: "120+ FPS",
    estimated_price_label: "Est. Rp 8.850.000",
    components: {
      cpu: "cpu-intel-i3-12100f",
      motherboard: "mobo-asrock-h610m-hdv",
      ram: "ram-team-elite-plus-16gb-ddr4",
      gpu: "gpu-amd-rx-6600-8gb",
      storage: "storage-kioxia-exceria-g2-500gb",
      psu: "psu-fsp-hv-pro-550w",
      case: "case-paradox-gaming-trinity-matx"
    }
  },
  {
    id: "player-performance",
    badge: "PC-2",
    title: "1440p High-FPS Sweet Spot",
    description: "Kombinasi prosesor 6-core dengan RTX 4060 dan RAM 32GB untuk gaming AAA 1440p dengan DLSS 3 Frame Generation dan video editing.",
    target_res: "1440p QHD",
    target_fps: "90+ FPS Ultra",
    estimated_price_label: "Est. Rp 14.890.000",
    is_popular: true,
    components: {
      cpu: "cpu-intel-i5-12400f",
      motherboard: "mobo-msi-pro-b760m-p-ddr4",
      ram: "ram-corsair-vengeance-lpx-32gb-ddr4",
      gpu: "gpu-nvidia-rtx-4060-8gb",
      storage: "storage-adata-legend-850-1tb",
      psu: "psu-deepcool-pk650d-650w",
      case: "case-cube-gaming-byron-atx"
    }
  },
  {
    id: "player-ultimate",
    badge: "PC-3",
    title: "4K Flagship & Creative Station",
    description: "Monster gaming bertenaga Ryzen 7 7800X3D (3D V-Cache) dan RTX 4070 SUPER 12GB dengan RAM DDR5 6000MHz untuk resolusi 4K dan 3D rendering.",
    target_res: "4K UHD / Ray Tracing",
    target_fps: "144+ FPS",
    estimated_price_label: "Est. Rp 28.530.000",
    components: {
      cpu: "cpu-amd-r7-7800x3d",
      motherboard: "mobo-gigabyte-b650-aorus-elite-ax",
      ram: "ram-corsair-vengeance-32gb-ddr5-6000",
      gpu: "gpu-nvidia-rtx-4070-super-12gb",
      storage: "storage-samsung-980-pro-1tb",
      psu: "psu-seasonic-focus-gx750-gold",
      case: "case-montech-air-903-base-atx"
    }
  }
];
