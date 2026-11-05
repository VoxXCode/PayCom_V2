# Product Requirements Document (PRD): Platform Perakit PC

## 1. Problem Statement
Orang awam dan pemula yang ingin merakit PC sering kali dirugikan secara finansial dan waktu akibat ketidaktahuan teknis (*socket*, *chipset*, TDP, *form factor*). Membeli komponen yang tidak kompatibel menyebabkan PC tidak bisa menyala atau komponen tidak bisa dipasang. Saat ini, proses validasi spesifikasi mengharuskan mereka meriset di forum atau bergantung penuh pada penjual toko (yang rawan memanipulasi rekomendasi demi stok barang).

## 2. Target User & Persona
**Target User:** Pemula (*beginner*) yang memiliki niat merakit PC sendiri atau membeli PC *custom* di toko fisik/online, tetapi tidak memiliki latar belakang teknis *hardware*.

**Persona 1: Budi (20, Mahasiswa)**
Budget terbatas. Ingin merakit PC gaming dengan mencicil komponen dari marketplace. Sangat takut salah beli motherboard atau RAM yang berujung membuang uang.

**Persona 2: Siti (28, Pekerja Kantoran/Desainer)**
Punya budget pasti, ingin langsung membawa daftar rakitan ke pusat komputer atau toko fisik lokal. Butuh *list* yang terjamin kompatibilitas dan estimasi harganya agar tidak tertipu oleh tawaran *upgrade* yang tidak perlu dari penjual.

## 3. Goals dan Non-Goals
**Goals:**
*   Menyediakan *tool* utilitas gratis yang memvalidasi kompatibilitas antar komponen komputer secara otomatis.
*   Menampilkan estimasi harga *live* menggunakan API pihak ketiga gratis.
*   Menghasilkan daftar komponen (*build list*) akhir yang terstruktur, siap diekspor, dan mudah dipahami oleh teknisi atau penjual toko.

**Non-Goals:**
*   Menjadi *marketplace* atau berjualan langsung.
*   Menjadi platform tutorial perakitan fisik PC (cara pasang kabel, baut, dll).
*   Menyediakan fitur *benchmarking* atau *overclocking* untuk kalangan *enthusiast*.

## 4. User Stories
*   Sebagai **pemula**, saya ingin **pilihan *motherboard* otomatis terfilter berdasarkan CPU yang sudah saya pilih**, supaya **saya tidak salah beli *motherboard* beda *socket***.
*   Sebagai **pemula**, saya ingin **sistem menghitung total kebutuhan daya (Watt)**, supaya **saya tahu persis kapasitas Power Supply (PSU) yang harus dibeli tanpa takut PC mati mendadak**.
*   Sebagai **pemula**, saya ingin **melihat estimasi harga komponen dari platform online**, supaya **saya bisa menyesuaikan rakitan dengan *budget***.
*   Sebagai **pemula**, saya ingin **mengekspor *list* akhir menjadi PDF/Teks yang rapi**, supaya **bisa saya tunjukkan dengan mudah ke penjual toko secara *offline* atau mencari komponennya secara *online***.

## 5. Daftar Fitur
**MVP (Minimum Viable Product):**
1. *Smart Component Selector*: Pemilihan terurut (CPU -> Motherboard -> RAM, dst) dengan *auto-filtering* kompatibilitas dasar.
2. *Wattage Calculator*: Penghitungan estimasi total daya (TDP) otomatis.
3. *Live Price Integration*: Penarikan data harga menggunakan API gratis pihak ketiga.
4. *Build Exporter*: Fitur cetak/simpan dalam format PDF dan teks *copy-paste*.
5. *Hardware Warnings*: Peringatan jika konfigurasi butuh perhatian khusus (misal: CPU tanpa GPU internal).

**v2 (Iterasi Berikutnya):**
1. *Dimension Clearance Check*: Pengecekan dimensi fisik (Panjang GPU vs Casing, tinggi *cooler* vs RAM).
2. *Pre-built Templates*: Pilihan *template* rakitan berdasarkan *budget* atau fungsi (e.g., PC 5 Juta, PC Editing).

**Later (Masa Depan):**
1. *User Account*: Fitur *login* untuk menyimpan beberapa profil rakitan.
2. *Community Sharing*: Fitur publikasi *build* ke komunitas.
3. *Price Drop Alerts*: Notifikasi jika komponen incaran turun harga.

## 6. Functional Requirement (Detail MVP)
*   **FR1 - Smart Component Selector:**
    *   Pengguna harus memilih CPU atau Motherboard terlebih dahulu.
    *   Jika CPU dipilih, *dropdown/list* Motherboard HARUS disaring secara *real-time* dengan kondisi `cpu.socket == motherboard.socket`.
    *   Pilihan RAM HARUS disaring berdasarkan dukungan tipe RAM pada Motherboard (DDR4 atau DDR5).
*   **FR2 - API Integrasi Harga:**
    *   Sistem memanggil *endpoint* API gratis untuk mengambil rata-rata harga atau harga teratas berdasarkan kata kunci nama komponen.
    *   Sistem HARUS menampilkan indikator "Estimasi Harga" (bukan jaminan harga mutlak di toko fisik).
*   **FR3 - Wattage Calculator:**
    *   Sistem menjumlahkan *field* `tdp` dari CPU + GPU + (baseline/estimasi 50W untuk Mobo/Storage/Fan).
    *   Sistem merekomendasikan PSU minimum dengan rumus `(Total Wattage * 1.5)`.
*   **FR4 - Hardware Warnings Engine:**
    *   Sistem memvalidasi konfigurasi grafis: Jika `cpu.has_integrated_graphics == false` DAN `gpu == null`, tampilkan peringatan tebal/merah: "PC Anda tidak akan menampilkan gambar tanpa VGA tambahan".
*   **FR5 - Build Exporter:**
    *   Menyediakan tombol "Export". Output berisi: Nama Komponen, Harga Estimasi per item, Total Harga, Total Daya, dan label tebal "100% Kompatibel".

## 7. Sketsa Data Model
*   **`Component`** (Tabel Utama/Supertype)
    *   `id` (UUID, Primary Key)
    *   `type` (Enum: cpu, motherboard, ram, gpu, psu, storage, case)
    *   `name` (String)
    *   `base_price` (Integer)
*   **`CPU_Specs`**
    *   `component_id` (FK to Component)
    *   `socket` (String)
    *   `tdp` (Integer)
    *   `has_igpu` (Boolean)
*   **`Mobo_Specs`**
    *   `component_id` (FK to Component)
    *   `socket` (String)
    *   `form_factor` (String: ATX, mATX, ITX)
    *   `ram_type` (String: DDR4, DDR5)
*   **`Build_Session`**
    *   `session_id` (UUID)
    *   `cpu_id`, `mobo_id`, `gpu_id`, `ram_id`, `psu_id` (FK to Component)
    *   `total_estimated_price` (Integer)

## 8. Edge Case dan Failure State
*   **Edge Case:** Pengguna memilih *casing* Mini-ITX tapi memilih *motherboard* ukuran standar ATX.
    *   *Penanganan:* Casing harus difilter otomatis sesuai dengan `form_factor` motherboard, atau muncul pesan *error* jika memilih *casing* terlebih dahulu.
*   **Failure State 1 (API Down/Rate Limit):** API harga pihak ketiga mati atau mencapai batas kuota gratis.
    *   *Penanganan:* Web tidak boleh *crash*. *Hide* bagian harga, tampilkan pesan peringatan *"Layanan Harga sedang gangguan. Anda tetap bisa cek kompatibilitas"*, dan sediakan kolom harga kosong yang bisa diisi manual oleh user di hasil *export*.
*   **Failure State 2 (Komponen Tidak Ada):** Komponen incaran user tidak ada di database.
    *   *Penanganan:* Tampilkan tombol "Input Custom Komponen". *Flag* status rakitan berubah dari "100% Kompatibel" menjadi *"Kompatibilitas Custom Tidak Dijamin"*.

## 9. Success Metrics
1.  **Completion Rate:** >60% sesi pengguna berakhir dengan menekan tombol "Export".
2.  **Compatibility Error Rate:** 0% keluhan fatal bahwa *list export* dari web menyebabkan ketidakcocokan perangkat keras utama (*socket* & konektor daya).
3.  **API Uptime/Success Rate:** >95% pemanggilan ke API harga pihak ketiga berhasil memunculkan data (sebagai evaluasi keandalan vendor API gratis yang dipakai).

## 10. Open Questions (Perlu Diputuskan Tim)
*   *(Requirement Ambigu)* **Sumber Data Spesifikasi Hardware:** Dari mana kita akan mendapatkan data komponen teknis (socket, TDP, dll) ini? Apakah menggunakan basis data terbuka seperti *PCPartPicker API* (jika masih *public*), membuat sistem *scraping*, atau menggunakan *dump data* statis yang diperbarui manual per bulan?
*   *(Requirement Ambigu)* **Vendor API Harga Gratis:** API harga gratis mana yang spesifik akan kita gunakan? Mengingat mayoritas *marketplace* tidak memiliki API publik tanpa otentikasi *seller*, kita harus menentukan apakah akan menggunakan *aggregator* gratisan (jika ada) atau API khusus yang tidak melanggar ToS pihak ketiga.