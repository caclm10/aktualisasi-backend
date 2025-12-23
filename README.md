# Aktualisasi (Backend)

**Sistem Informasi Manajemen Jaringan & Inventaris**

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)

Repositori Backend API untuk aktualisasi dari aplikasi manajemen aset perangkat jaringan, dirancang untuk mengelola aset perangkat jaringan dan log pemeliharaan di lingkungan instansi. Dibangun menggunakan framework **Laravel** dengan penekanan pada integritas data dan rekam jejak audit (_Audit Trail_) yang mendetail.

---

## 🚀 Fitur Utama

- **Otentikasi Aman**: Menggunakan **Laravel Sanctum** untuk keamanan akses API.
    - _Keuntungan:_ URL sulit ditebak (mencegah _Enumeration Attack_), lebih aman, dan siap untuk skalabilitas horizontal.
- **Audit Trail**:
    - **Journey Logs**: Mencatat riwayat mutasi/perpindahan aset antar ruangan.
    - **Maintenance Logs**: Mencatat riwayat teknis (Update OS, Perbaikan).
- **Manajemen Aset**: Mengelola aset dan status aset mulai dari Pengadaan (_Procurement_) hingga Penghapusan (_Disposal_).
- **Dokumentasi API Otomatis**: Tersedia dokumentasi OpenAPI/Swagger yang digenerate otomatis menggunakan **Dedoc Scramble**.

---

## 🛠️ Teknologi yang Digunakan

- **Framework**: Laravel 12
- **Database**: MySQL 5.7+ / MariaDB 10.2+
- **Library ID**: `hidehalo/nanoid-php`
- **Penyimpanan**: Local Storage (untuk Gambar Aset)

---

## ⚙️ Panduan Instalasi & Setup

### Prasyarat

- PHP >= 8.2
- Composer
- MySQL atau MariaDB

### 1. Clone Repositori

```bash
git clone [https://github.com/caclm10/aktualisasi-backend.git](https://github.com/caclm10/aktualisasi-backend.git)
cd aktualisasi-backend
```

### 2. Install Dependensi

```bash
composer install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
```

Buka file `.env` dan sesuaikan konfigurasi berikut:

```text
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=username
DB_PASSWORD=password

# Konfigurasi CORS & Sanctum (Penting untuk koneksi ke React)
FRONTEND_URL=http://localhost:3000
SANCTUM_STATEFUL_DOMAINS=localhost:3000
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Jalankan Migrasi Database

```bash
php artisan migrate
```

_(Opsional) Isi data dummy untuk testing:_

```bash
php artisan db:seed
```

### 6. Link Storage

```bash
php artisan storage:link
```

### 7. Jalankan Server

```bash
php artisan serve
```

Aplikasi backend akan berjalan di `http://localhost:8000`

## 📖 Dokumentasi API

Proyek ini menggunakan **[Dedoc Scramble](https://scramble.dedoc.co/)** untuk men-generate dokumentasi API secara otomatis (tanpa perlu menulis anotasi manual).

Setelah server berjalan, akses dokumentasi lengkap di:

```
http://localhost:8000/docs/api
```

> **Note:** Dokumentasi akan otomatis di-update setiap kali ada perubahan pada controller.

## 🧩 Sorotan Teknis (Technical Highlights)

Bagian ini menjelaskan implementasi teknis khusus yang diterapkan dalam sistem:

### 1. Implementasi NanoID via Macro

Sistem menggunakan Custom Macro pada AppServiceProvider untuk memudahkan pembuatan kolom ID dan Foreign Key bertipe Char(21):

```php
// Contoh penggunaan di file migrasi
$table->nanoid(); // Membuat Primary Key CHAR(21)
$table->foreignNanoid('office_id'); // Membuat Foreign Key CHAR(21) 2. Hybrid JSON Logging
Tabel maintenance_logs menggunakan pendekatan hibrida. Data inti (User, Aset, Lokasi) disimpan secara relasional (SQL), sedangkan data detail yang dinamis disimpan dalam format JSON:
```

### 2. Hybrid JSON Logging

Tabel maintenance_logs menggunakan pendekatan hibrida. Data inti (User, Aset, Lokasi) disimpan secara relasional (SQL), sedangkan data detail yang dinamis disimpan dalam format JSON:

```sql
`category` ENUM('perjalanan', 'pemeliharaan')
`properties` JSON -- Menyimpan { "old_os": "v1.0", "new_os": "v2.0" }
Pendekatan ini memungkinkan sistem mencatat atribut log yang berbeda-beda (misal: log mutasi vs log update firmware) dalam satu tabel yang sama secara efisien.
```
