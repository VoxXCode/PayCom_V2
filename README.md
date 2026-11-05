# PayCom Builder: Platform Perakit PC Cerdas untuk Pemula (MVP)

Aplikasi web utilitas gratis untuk membantu orang awam dan pemula merakit PC secara terstruktur, aman dari risiko ketidakcocokan perangkat keras (*socket*, *chipset*, *form factor*), menghitung kebutuhan daya listrik (TDP & Wattage) secara otomatis, serta menampilkan estimasi harga *live* komponen komputer.

Dibangun dengan mematuhi secara ketat spesifikasi pada [PRD.md](file:///c:/Users/Hp/Documents/Code/PayCom_V2/PRD.md) dan panduan desain visual pada [DESIGN_BRIEF.md](file:///c:/Users/Hp/Documents/Code/PayCom_V2/DESIGN_BRIEF.md).

---

## Fitur Utama (Sesuai PRD MVP)

### 1. Smart Component Selector (FR1)
- Alur pemilihan bertahap yang membimbing pemula: **CPU → Motherboard → RAM → GPU → Storage → PSU → Casing**.
- Pilihan Motherboard otomatis terkunci (*disabled*) sebelum CPU dipilih.
- Saat CPU dipilih, daftar Motherboard otomatis disaring secara *real-time* berdasarkan kesamaan *socket* (`cpu.socket == motherboard.socket`).
- Pilihan RAM otomatis disaring berdasarkan tipe memori yang didukung oleh Motherboard (`DDR4` atau `DDR5`).
- Pilihan Casing otomatis disaring berdasarkan kesesuaian ukuran *form factor* Motherboard (`ATX`, `mATX`, `Mini-ITX`).

### 2. Integrasi Estimasi Harga Live (FR2)
- Menampilkan estimasi harga per komponen dan total biaya sistem.
- Sesuai panduan UI/UX, seluruh harga berlabel **"Est."** (bukan harga mutlak toko fisik).
- Menggunakan format mata uang Rupiah (`Rp`) yang rapi.

### 3. Wattage Calculator (FR3)
- Kalkulasi otomatis total kebutuhan daya puncak sistem:
  $$\text{Total Wattage} = \text{TDP CPU} + \text{TDP GPU} + 50\text{W (Baseline Mobo/SSD/Kipas)}$$
- Rekomendasi kapasitas minimum Power Supply (PSU) aman dengan rumus:
  $$\text{Rekomendasi PSU} = \text{Total Wattage} \times 1.5$$ (dibulatkan ke kelipatan 50W standar pasaran).
- Tampilan indikator daya *live* di *top bar* (misal: `⚡ 350W`) dan rincian lengkap pada *sidebar*.

### 4. Hardware Warnings Engine (FR4)
- **Peringatan Kritis Output Grafis:** Jika prosesor yang dipilih tidak memiliki grafis internal (`cpu.has_igpu == false`) dan belum ada VGA tambahan yang dipilih, sistem menampilkan peringatan tebal berlatar Coral Red (`#EF4444`) dengan ikon ❌: *"PC Anda tidak akan menampilkan gambar tanpa VGA tambahan"*.
- **Peringatan Kapasitas PSU:** Jika kapasitas PSU yang dipilih lebih kecil dari rekomendasi daya aman sistem, sistem menampilkan peringatan Amber (`#F59E0B`) dengan ikon ⚠️.
- **Peringatan Form Factor:** Memperingatkan jika ukuran motherboard melebihi kapasitas fisik casing.

### 5. Build Exporter (FR5)
- **Cetak / Simpan PDF:** Format lembar cetak (*printable sheet*) profesional dengan tabel rapi, rincian per komponen, harga estimasi, total daya, dan stempel tebal **"100% KOMPATIBEL"**. Siap dicetak langsung atau disimpan sebagai PDF untuk dibawa ke toko fisik komputer.
- **Salin Teks Ringkasan:** Menghasilkan ringkasan rapi dalam satu klik untuk dikirim via WhatsApp atau catatan ponsel ke teknisi/penjual toko.
- **Reset Rakitan:** Mengosongkan seluruh slot rakitan dengan konfirmasi pengguna.

---

## Penanganan Edge Case & Failure States

1. **Failure State 1 (Layanan API Harga Gangguan/Rate Limit):**
   - Web tetap berjalan normal tanpa *crash*.
   - Muncul *banner* peringatan berwarna Amber: *"⚠️ Layanan Harga sedang gangguan. Anda tetap bisa cek kompatibilitas."*
   - Label harga per komponen berubah menjadi *"Est. Rp - (Isi Manual)"* disertai kolom input harga manual.
   - Total harga dan hasil ekspor memperhitungkan harga manual yang diinput pengguna.
   - *Catatan Pengujian:* Tersedia tombol **"Simulasi: API Normal / Gangguan"** di *header* untuk menguji skenario ini secara langsung.

2. **Failure State 2 (Komponen Tidak Ada di Basis Data):**
   - Di dalam jendela pemilihan komponen, terdapat tombol **"＋ Komponen Tidak Ada? Input Custom"**.
   - Pengguna dapat memasukkan nama, estimasi harga, dan spesifikasi teknis (*socket*, TDP, *form factor*, tipe RAM).
   - Setelah ditambahkan, status rakitan otomatis berubah menjadi **"⚠️ Kompatibilitas Custom Tidak Dijamin"** sesuai ketentuan PRD.

3. **Edge Case Penggantian Komponen di Tengah Sesi:**
   - Jika pengguna mengganti CPU ke *socket* yang berbeda (misal dari Intel LGA1700 ke AMD AM5), sistem secara otomatis mereset slot Motherboard dan RAM terkait, disertai notifikasi transparan, agar pemula tidak bingung dengan konfigurasi yang bertabrakan.

---

## Standar Desain Visual, Tailwind CSS v4 & Aksesibilitas

- **Integrasi Tailwind CSS v4 ([src/main.css](file:///c:/Users/Hp/Documents/Code/PayCom_V2/src/main.css)):**
  - Menerapkan konfigurasi `@theme` sesuai Modul 1 Hal. 20 untuk mendefinisikan variabel tema warna modern (`--color-brand-bg`, `--color-brand-surface`, `--color-brand-blue`, dll).
  - Menggunakan utilitas kelas Tailwind CSS (`flex`, `grid`, `rounded-*`, `p-*`, `m-*`, breakpoint `md:`, `lg:`).
  - Terdapat **Hero Section** interaktif di bagian atas yang menyajikan pengenalan alur rakitan pemula.
  - Setiap slot kartu komponen terisi dan kartu modal dilengkapi foto/ilustrasi hardware (`<img>`) sesuai standar Codelab Modul 1.
- **Palet Warna Dark Mode Terkalibrasi:**
  - Latar Belakang Utama: `#121212` (bukan hitam pekat `#000000` demi kenyamanan mata).
  - Kartu/Surface: `#1E1E1E`, Hover: `#2C2C2C`.
  - Tombol Utama: Electric Blue `#3B82F6` (Hover `#2563EB`, Disabled `#3F3F46`).
  - Teks Utama: `#FFFFFF` (Rasio kontras > 16:1, melebihi standar WCAG AA 4.5:1).
  - Teks Sekunder: `#A1A1AA` (Rasio kontras 6.5:1 - 7.3:1).
- **Tipografi:** Sans-serif sistem berkecepatan tinggi dengan skala hierarki H1 32px, H2 24px, Body 16px, Caption 14px.
- **Spacing & Radius:** Sistem *base-8* (8px, 16px, 24px, 32px), radius sudut konsisten 8px.
- **Aksesibilitas Manusia & Layar:**
  - Area klik tombol minimal **44x44 pixel** untuk kemudahan klik *trackpad* atau sentuhan jempol di ponsel.
  - Indikator ganda (*dual indicators*): warna selalu didampingi teks deskriptif dan ikon simbolik (Centang ✅, Peringatan ⚠️, Silang ❌) untuk kemudahan pengguna buta warna.
  - Navigasi keyboard penuh (`Tab`, `Shift+Tab`, `Enter`, dan `Escape` untuk menutup dialog modal).
  - *Responsive layout*: reflow mulus di layar laptop, tablet, dan smartphone tanpa *overflow* horizontal.

---

## Cara Menjalankan Aplikasi

Aplikasi dibangun menggunakan arsitektur web modern tanpa ketergantungan paket berat pihak ketiga sehingga dapat langsung dijalankan:

### Opsi 1: Menjalankan Server Lokal (Node.js)
```bash
# Jalankan server native
node server.js
```
Buka peramban (*browser*) Anda dan akses:
```
http://localhost:3000
```

### Opsi 2: Buka Langsung Berkas HTML
Anda juga dapat membuka berkas [index.html](file:///c:/Users/Hp/Documents/Code/PayCom_V2/index.html) langsung dengan klik ganda (*double-click*) di File Explorer atau membukanya di browser apa pun (Chrome, Edge, Firefox, Safari).

---

## Menjalankan Pengujian Otomatis (Automated Tests)

Untuk memvalidasi seluruh fungsi logika kompatibilitas, kalkulator daya, dan ekspor data:
```bash
node test/run-tests.js
```
Hasil pengujian: **15 skenario pengujian unit & integrasi lulus (100% PASS)**.

