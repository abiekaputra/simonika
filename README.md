# siMonika v3 — Sistem Informasi Monitoring Aplikasi

Aplikasi web berbasis Laravel untuk monitoring dan pendataan aplikasi milik OPD (Organisasi Perangkat Daerah), mencakup manajemen pegawai, proyek, linimasa kegiatan, serta pendataan magang.

## Fitur Utama

- **Dashboard** — ringkasan jumlah aplikasi aktif, tidak aktif, dan log pembaruan terakhir
- **Manajemen Aplikasi** — CRUD aplikasi OPD beserta atribut tambahan yang dinamis, ekspor data ke Excel
- **Manajemen Pegawai** — data pegawai yang terlibat dalam pengembangan/pengelolaan aplikasi
- **Manajemen Proyek** — pelacakan proyek pengembangan beserta kategori dan status
- **Linimasa** — pencatatan riwayat kegiatan per aplikasi
- **Pendataan Magang** — manajemen data peserta magang
- **Log Aktivitas** — rekam jejak aksi pengguna (khusus Super Admin)
- **Autentikasi** — login, register, lupa password, dan manajemen profil
- **Ekspor Data** — unduh data aplikasi dalam format Excel

## Peran Pengguna

| Peran | Akses |
|-------|-------|
| **Admin** | Dashboard, aplikasi, pegawai, proyek, linimasa, pendataan, profil |
| **Super Admin** | Semua fitur Admin + manajemen pengguna, log aktivitas, ekspor log |

## Tech Stack

- **Framework** — Laravel 11
- **Frontend** — Blade, Tailwind CSS, Vite
- **Database** — MySQL
- **Bahasa** — PHP 8.x

## Instalasi

### Prasyarat

- PHP >= 8.1
- Composer
- Node.js & npm
- MySQL

### Langkah-langkah

```bash
# 1. Clone repo
git clone https://github.com/abiekaputra/siMonikaVer3.git
cd siMonikaVer3

# 2. Install dependensi PHP
composer install

# 3. Install dependensi frontend
npm install && npm run build

# 4. Salin konfigurasi environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Konfigurasi database di .env
#    DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 7. Jalankan migrasi
php artisan migrate

# 8. (Opsional) Jalankan seeder
php artisan db:seed

# 9. Jalankan server
php artisan serve
```

Akses aplikasi di `http://localhost:8000`.

## Struktur Proyek

```
app/
├── Http/Controllers/      # Controller per modul
├── Models/                # Eloquent models
├── Exports/               # Kelas ekspor Excel
├── Imports/               # Kelas impor data
├── Mail/                  # Mailable (notifikasi email)
└── Traits/                # Trait reusable
database/
├── migrations/            # Skema database
└── seeders/               # Data awal
resources/views/           # Blade templates
routes/web.php             # Definisi routing
```

## Lisensi

Proyek ini dibuat untuk keperluan akademis dan pengembangan portofolio.
