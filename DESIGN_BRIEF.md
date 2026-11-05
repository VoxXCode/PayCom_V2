# Design Brief: Platform Perakit PC (MVP)

## 1. Tujuan Desain dan Kesan yang Ingin Dibangun
*   **Tujuan:** Memandu pengguna awam langkah demi langkah dalam memilih komponen PC tanpa rasa takut salah beli. Antarmuka harus menyembunyikan kerumitan teknis (seperti *filtering* yang terjadi di balik layar) dan menonjolkan kejelasan status.
*   **Kesan (Vibe):** *Dark mode* yang minimalis, modern, bersih (*clean*), dan tidak mengintimidasi (*beginner-friendly*). Tampilan harus terasa profesional dan meyakinkan, seperti asisten teknisi pribadi.

## 2. Palet Warna (Hex) dan Panduan Penggunaan
Desain menggunakan skema *dark mode* dengan kontras yang nyaman untuk mata (tidak menggunakan hitam pekat).

*   **Background (Latar Belakang):**
    *   `#121212` (Main Background)
    *   `#1E1E1E` (Surface/Card Background - untuk slot komponen)
    *   `#2C2C2C` (Hover state pada Surface/Card)
*   **Primary/Action:**
    *   `#3B82F6` (Electric Blue) - Untuk tombol utama (misal: "Pilih Komponen", "Export"). Memberikan kesan teknologi yang modern dan dapat dipercaya.
*   **Teks:**
    *   `#FFFFFF` (White) - Teks utama, *heading*, nama komponen.
    *   `#A1A1AA` (Zinc/Gray 400) - Teks sekunder, *caption*, keterangan teknis minor (TDP, Socket).
*   **Status & Feedback (Wajib didampingi Ikon):**
    *   `#10B981` (Emerald Green) - Status kompatibel, sukses.
    *   `#EF4444` (Coral Red) - *Hardware warnings* kritis (misal: tidak ada GPU), error koneksi.
    *   `#F59E0B` (Amber/Yellow) - Peringatan ringan (misal: API harga gagal, gunakan input manual).

## 3. Tipografi
Menggunakan font *sans-serif* yang sangat terbaca di layar gelap seperti **Inter** atau **Roboto**.
*   **Heading 1 (H1):** 32px, Semi-Bold (Judul Halaman/Aplikasi).
*   **Heading 2 (H2):** 24px, Medium (Judul Bagian/Kategori Komponen, misal: "CPU", "Motherboard").
*   **Body (Primary):** 16px, Regular (Nama perangkat keras, Harga Estimasi).
*   **Body (Secondary/Caption):** 14px, Regular (Spesifikasi kunci seperti TDP, Socket, pesan error).

## 4. Spacing, Grid, dan Radius Sudut
*   **Grid System:** *Desktop-first* dengan 12-kolom, batas lebar maksimal (max-width) 1200px. Tersentralisasi (margin auto kiri-kanan).
*   **Spacing:** Menggunakan kelipatan 8 (Base-8).
    *   *Micro spacing:* 8px (jarak ikon ke teks).
    *   *Component spacing:* 16px (jarak antar teks di dalam *card*).
    *   *Section spacing:* 24px - 32px (jarak antar slot komponen keras).
*   **Radius Sudut (Border Radius):** 
    *   `8px` untuk kartu komponen (*Cards*), tombol (*Buttons*), dan *Dropdown*. Tidak terlalu tajam agar terkesan ramah pemula.

## 5. Komponen UI beserta State-nya
*   **Tombol Utama (Solid Blue):**
    *   *Default:* Bg `#3B82F6`, Text White.
    *   *Hover:* Bg `#2563EB` (Biru lebih gelap).
    *   *Disabled:* Bg `#3F3F46`, Text `#71717A` (Hanya bisa ditekan jika syarat sebelumnya terpenuhi).
*   **Tombol Sekunder (Outlined):**
    *   *Default:* Border `#3F3F46`, Bg Transparan, Text White.
    *   *Hover:* Bg `#2C2C2C`.
*   **Kartu Slot Komponen (*Component Row*):**
    *   *Default (Kosong):* Border putus-putus (*dashed*), warna teks sekunder, tombol "Pilih".
    *   *Terisi:* Border solid transparan, warna *surface* `#1E1E1E`, menampilkan foto mini (jika ada), nama komponen, harga, dan tombol "Ganti" atau hapus ("X").
*   **Status Badges / Peringatan:**
    *   Berbentuk pil (rounded full) atau kotak *alert*.

## 6. Layout Tiap Halaman Utama (Berdasarkan PRD MVP)
Aplikasi hanya memiliki **1 Halaman Utama (Main Builder Interface)** dengan pembagian layout *desktop*:

*   **Top Bar/Header:** Logo Aplikasi (kiri), dan "Wattage Calculator" ringkas di kanan (menampilkan estimasi daya *live*, misal: "⚡ 350W").
*   **Kolom Kiri (Main Area - 8 Kolom): "Smart Component Selector"**
    *   Daftar vertikal urutan rakitan: 1. CPU, 2. Motherboard, 3. RAM, 4. GPU, 5. Storage, 6. PSU, 7. Case.
    *   Tiap baris memiliki area untuk menampilkan komponen yang dipilih. Jika belum, berisi tombol "Pilih [Kategori]".
    *   *Modal/Pop-up Window:* Muncul saat user klik "Pilih". Berisi daftar komponen yang sudah ter-*filter* otomatis berdasarkan pilihan sebelumnya.
*   **Kolom Kanan (Sticky Sidebar - 4 Kolom): "Build Summary"**
    *   Estimasi Total Harga (*Live Price Integration*).
    *   Panel *Hardware Warnings* (Kosong jika aman, muncul *box* merah/kuning jika ada peringatan FR4).
    *   Indikator status besar: "✅ 100% Kompatibel" (jika semua komponen inti sudah dipilih).
    *   Tombol utama "Export Build" (FR5).

## 7. Empty State, Loading, dan Pesan Error
*   **Empty State (List Komponen Modal):** Jika hasil filter kosong, tampilkan "Komponen tidak ditemukan. Coba hapus filter atau periksa kompatibilitas pilihan sebelumnya."
*   **Loading State:**
    *   Gunakan efek *Skeleton Loading* (animasi *shimmer* abu-abu gelap) pada daftar komponen saat API Harga atau API spesifikasi sedang memuat (*fetching*).
*   **Pesan Error (Failure State dari PRD):**
    *   *API Harga Down:* Munculkan *banner* kuning di atas daftar: "⚠️ Layanan Harga sedang gangguan. Anda tetap bisa cek kompatibilitas." Kolom harga di komponen diubah menjadi "Rp - (Isi Manual)".

## 8. Aturan Aksesibilitas
*   **Kontras Warna:** Pastikan rasio kontras teks terhadap *background* minimal 4.5:1 (WCAG AA). Teks abu-abu (`#A1A1AA`) pada latar `#121212` sudah memenuhi standar ini.
*   **Ukuran Tap Target:** Meskipun *desktop-first*, semua tombol utama, ikon hapus komponen, dan baris *dropdown* wajib memiliki tinggi/lebar area klik minimal **44x44 pixel** agar mudah diklik pengguna menggunakan *trackpad* atau *mouse* tanpa meleset.
*   **Indikator Ganda (Buta Warna):** *Dilarang keras hanya mengandalkan warna hijau/merah untuk kompatibilitas.* 
    *   Warna hijau HARUS didampingi ikon Centang (✅ atau ✓).
    *   Warna merah HARUS didampingi ikon Silang (❌) atau Peringatan (⚠️).

## 9. Do & Don't
*   **DO:**
    *   Sembunyikan spesifikasi rumit di awal. Tampilkan nama komponen, harga, dan indikator kompatibilitas sebagai informasi utama.
    *   Kunci ( *disable*) tombol pemilihan Motherboard jika CPU belum dipilih, untuk memandu alur berpikir pemula (FR1).
*   **DON'T:**
    *   Jangan gunakan *background* hitam murni `#000000` karena terlalu kontras dan melelahkan mata.
    *   Jangan tampilkan harga sebagai angka mutlak. Selalu gunakan *prefix* "Est." atau "Estimasi" (FR2).
    *   Jangan menyembunyikan peringatan kritis di bawah halaman. Peringatan GPU/CPU (FR4) harus *sticky* atau berada di dekat harga total.

## 10. Open Questions (Desain UI/UX)
*   **Pembatalan Komponen Tengah:** Jika *user* sudah merakit sampai RAM, lalu tiba-tiba menghapus/mengganti pilihan CPU, apakah sistem akan otomatis me-*reset* (mengosongkan) slot Motherboard dan RAM, atau memberi *warning highlight* merah pada Motherboard bahwa "CPU baru Anda tidak cocok dengan Motherboard ini"? *(Rekomendasi UI: Reset otomatis lebih aman untuk pemula agar tidak bingung).*
*   **Tampilan Harga (Range vs Average):** API akan memunculkan harga *live*. Apakah secara visual kita ingin menampilkan harga sebagai *range* (misal: Rp 1.500.000 - Rp 1.700.000) agar user ekspektasinya terjaga, atau satu angka rata-rata tetap demi kesederhanaan UI?