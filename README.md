# PayCom BLD: Platform Perakit PC Cerdas (PHP MVC + MySQL PDO)

Aplikasi web **PHP MVC** dengan database **MySQL (PDO, Prepared Statements)** untuk membantu pemula merakit PC secara terstruktur, memvalidasi kompatibilitas perangkat keras (*socket*, *RAM type*, *form factor*, *iGPU*), menghitung kebutuhan daya listrik (*TDP & Wattage*), serta menyimpan dan mengelola daftar rakitan PC secara permanen.

---

## Pemenuhan Ketentuan Proyek Kelompok (Pengganti UAS)

1. **Kelompok 3 Orang — Satu Repositori GitHub Bersama:**
   - Anggota: `Dicky_208`, `Devano_211`, `Himam_179`
   - Alur kerja Git: pembuatan *branch* per fitur (`fitur/...`), pengajuan *Pull Request* ke `master`, dan *code review* oleh rekan tim sebelum *merge*.
2. **Aplikasi Web PHP dengan Arsitektur MVC + Database MySQL (PDO, Prepared Statement):**
   - **Front Controller & Router:** `index.php`, `router.php`, `app/Core/Router.php`, `app/Core/Controller.php`, `app/Core/Database.php`
   - **Models (PDO Prepared Statements):**
     - `app/Models/ComponentModel.php` (tabel `components`)
     - `app/Models/BuildModel.php` (tabel `pc_builds` & `build_items` dengan *database transaction*)
   - **Controllers + Validasi Input:**
     - `app/Controllers/BuilderController.php`
     - `app/Controllers/ComponentController.php` (`validateComponentInput()`)
     - `app/Controllers/BuildController.php` (`validateBuildInput()`)
   - **Views:** `app/Views/layouts/*`, `app/Views/builder/*`, `app/Views/components/*`, `app/Views/builds/*`, `app/Views/errors/404.php`
3. **Minimal 2 Tabel Database (Tersedia 3 Tabel Relasional):**
   - `components` — Katalog komponen hardware (CPU, Motherboard, RAM, GPU, Storage, PSU, Casing)
   - `pc_builds` — Data master rakitan PC tersimpan
   - `build_items` — Relasi *foreign key* komponen per rakitan (`build_id -> pc_builds.id`, `component_id -> components.id`)
4. **CRUD Lengkap (Create, Read, Update, Delete):**
   - **Katalog Komponen (`/components`):** Tambah komponen, lihat/cari/filter katalog, ubah spesifikasi & harga, hapus komponen (dengan proteksi *foreign key*).
   - **Rakitan PC (`/builds`):** Simpan rakitan (baik langsung dari Konfigurator maupun form PHP), lihat daftar & detail rakitan, edit metadata & komponen rakitan, hapus rakitan.
5. **Rute GET & POST (10 Rute GET + 6 Rute POST):**

| Method | Rute | Controller & Method | Fungsi |
| :--- | :--- | :--- | :--- |
| `GET` | `/` | `BuilderController::index` | Halaman utama & Konfigurator PC Builder |
| `GET` | `/api/components` | `BuilderController::apiComponents` | Endpoint JSON katalog komponen dari MySQL |
| `GET` | `/api/price-estimate` | `BuilderController::apiPriceEstimate` | Endpoint status API estimasi harga |
| `GET` | `/components` | `ComponentController::index` | Daftar & filter katalog komponen (Read) |
| `GET` | `/components/create` | `ComponentController::create` | Form tambah komponen baru |
| `POST` | `/components/store` | `ComponentController::store` | Validasi input & simpan komponen (Create) |
| `GET` | `/components/edit` | `ComponentController::edit` | Form edit komponen |
| `POST` | `/components/update` | `ComponentController::update` | Validasi input & perbarui komponen (Update) |
| `POST` | `/components/delete` | `ComponentController::delete` | Hapus komponen (Delete) |
| `GET` | `/builds` | `BuildController::index` | Daftar & filter rakitan tersimpan (Read) |
| `GET` | `/builds/show` | `BuildController::show` | Detail lengkap komponen & daya rakitan (Read) |
| `GET` | `/builds/create` | `BuildController::create` | Form buat rakitan baru |
| `POST` | `/builds/store` | `BuildController::store` | Validasi input & simpan rakitan (Create) |
| `GET` | `/builds/edit` | `BuildController::edit` | Form edit rakitan |
| `POST` | `/builds/update` | `BuildController::update` | Validasi input & perbarui rakitan (Update) |
| `POST` | `/builds/delete` | `BuildController::delete` | Hapus rakitan (Delete) |

6. **Validasi Input di Controller:**
   - `ComponentController::validateComponentInput()` memvalidasi kategori *enum*, panjang nama, harga bulat positif, rentang TDP & kapasitas PSU, format *socket*, tipe RAM (`DDR4`/`DDR5`), *form factor* (`ATX`/`mATX`/`ITX`), dan ringkasan spesifikasi.
   - `BuildController::validateBuildInput()` memvalidasi nama rakitan, nama perakit, tujuan penggunaan (*whitelist*), panjang catatan, keberadaan ID/slug komponen serta kesesuaian kategori di tabel `components`, serta menghitung ulang total harga, wattage, dan status kompatibilitas di server (`CompatibilityService::evaluate()`).

---

## Cara Menjalankan Aplikasi (Laragon / XAMPP / PHP CLI)

### 1. Impor Database MySQL
Jalankan MySQL pada Laragon/XAMPP, lalu impor berkas `database/paycom_builder.sql` melalui phpMyAdmin, HeidiSQL, atau terminal:
```bash
mysql -u root < database/paycom_builder.sql
```
Secara default `config/database.php` terhubung ke:
- Host: `127.0.0.1` (Port `3306`)
- Database: `paycom_builder`
- User: `root` (Password kosong, dapat diubah via *environment variable* `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).

### 2. Jalankan Web Server PHP
**Opsi A — PHP Built-in Server:**
```bash
php -S localhost:8000 router.php
```
Lalu buka `http://localhost:8000` di browser.

**Opsi B — Laragon / XAMPP Apache:**
Letakkan folder proyek di `laragon/www/PayCom_V2` (atau `htdocs/PayCom_V2`), nyalakan Apache + MySQL, dan akses `http://localhost/PayCom_V2/`.

---

## Pengujian Otomatis

1. **Pengujian Sintaks, Controller Validation, CompatibilityService, & Router PHP:**
```bash
php test/php-mvc-test.php
```
2. **Pengujian Engine JavaScript & Verifikasi UI Interaktif:**
```bash
node test/run-tests.js
node test/verify-r35.js
```
