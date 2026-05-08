# Sistem Pengelolaan Perjalanan Dinas

Aplikasi internal berbasis Laravel + Livewire untuk mengelola pengajuan perjalanan dinas pegawai secara end-to-end: pengajuan, perhitungan uang saku, approval SDM, dan histori status.

## Fitur

### 1. Autentikasi & Akses Role
- Login username/password
- Middleware `auth`, `active`, dan `role`
- Role utama: `admin`, `pegawai`, `sdm`

### 2. Dashboard Ringkas
- Ringkasan jumlah pengajuan sesuai role
- Total nominal pengajuan approved (admin/SDM)

### 3. Master Data Kota (Admin)
- Tambah, edit, pencarian, aktif/nonaktif kota
- Menyimpan latitude/longitude untuk perhitungan jarak

### 4. User Management (Admin)
- Tambah, edit, aktivasi/nonaktivasi user
- Assign role user

### 5. Pengajuan Perjalanan Dinas
- Draft dan submit pengajuan
- Validasi tanggal dan kota aktif
- Perhitungan otomatis:
  - durasi perjalanan
  - jarak antar kota (Haversine)
  - kategori uang saku
  - nominal per hari dan total

### 6. Approval SDM
- Queue pengajuan berstatus `submitted`
- Approve/reject dengan catatan
- Histori perubahan status tercatat

### 7. Histori & Audit
- Tracking status: `draft`, `submitted`, `approved`, `rejected`, `cancelled`
- Riwayat status per pengajuan

## Tech Stack

- PHP `^8.3`
- Laravel `^13.7`
- Livewire `^4.3`
- PostgreSQL (utama, sesuai PRD)
- Tailwind CSS (asset build sudah tersedia di `public/build`)
- Composer untuk dependency management

## Instalasi Lokal

### Prasyarat
- PHP 8.3+
- Composer
- PostgreSQL

### Langkah
1. Clone project dan masuk ke folder project.
2. Install dependency backend:
```bash
composer install
```
3. Buat file environment:
```bash
cp .env.example .env
```
4. Atur koneksi database PostgreSQL di `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nama_database
DB_USERNAME=username_db
DB_PASSWORD=password_db
```
5. Generate app key:
```bash
php artisan key:generate
```
6. Migrasi database:
```bash
php artisan migrate
```
7. Seed data awal (role dan kota):
```bash
php artisan db:seed
```
8. Jalankan aplikasi:
```bash
php artisan serve
```

Akses aplikasi di:
- `http://127.0.0.1:8000/login`

## Catatan Deploy Hosting

- Folder penting runtime: `app`, `bootstrap`, `config`, `database`, `public`, `resources`, `routes`, `storage`, `vendor`.
- Pastikan folder `storage` dan `bootstrap/cache` writable.
- Jalankan perintah optimasi di server:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

