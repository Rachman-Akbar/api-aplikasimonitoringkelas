# Aplikasi Monitoring Kelas - Backend API

## Deskripsi
Backend API untuk aplikasi monitoring kelas yang digunakan oleh aplikasi Android. Menyediakan sistem otentikasi, manajemen pengguna, jadwal pelajaran, kehadiran, dan fitur lainnya.

## Instalasi

1. Pastikan Anda memiliki PHP 8.0+, Composer, dan MySQL
2. Clone atau download proyek ini
3. Jalankan perintah berikut:

```bash
cd api-aplikasimonitoringkelas
composer install
cp .env.example .env
php artisan key:generate
```

4. Konfigurasi database di file `.env`

## Menjalankan Server untuk Android

### Metode 1: Via Command Line (Direkomendasikan)
```bash
cd C:\xampp\htdocs\ProjectCode\api-aplikasimonitoringkelas
php artisan serve --host=0.0.0.0 --port=8000
```

### Metode 2: Via Batch File
Jalankan file `START_SERVER.bat` yang ada di folder proyek

### Metode 3: Via PowerShell
Jalankan file `START_SERVER.ps1` yang ada di folder proyek

## Koneksi dari Android

### WiFi Connection:
1. Pastikan PC dan Android terhubung ke WiFi yang sama
2. Cari IP address PC Anda dengan perintah `ipconfig`
3. Di aplikasi Android, klik tombol "Setting" di info server
4. Masukkan IP PC Anda (contoh: `http://192.168.1.7:8000`)
5. Klik "Save" dan coba login

### USB Connection (ADB Tunneling):
1. Hubungkan Android ke PC via USB
2. Aktifkan USB Debugging di Android
3. Jalankan perintah:
```bash
adb reverse tcp:8000 tcp:8000
```
4. Di aplikasi Android, gunakan URL: `http://localhost:8000`

## Konfigurasi Firewall Windows
Jika Anda mengalami masalah koneksi, jalankan file `CONFIG_FIREWALL.bat` untuk membuka port 8000 di Windows Firewall, atau jalankan perintah berikut:

```cmd
netsh advfirewall firewall add rule name="Laravel Dev Server Port 8000" dir=in action=allow protocol=TCP localport=8000
```

## Endpoint API

### Otentikasi
- POST `/api/auth/login` - Login pengguna
- POST `/api/auth/logout` - Logout pengguna
- GET `/api/auth/me` - Informasi pengguna saat ini

### Endpoint Dasar
- GET `/api/` - Informasi welcome API
- GET `/api/health` - Status kesehatan API

## Demo Pengguna

### Login Credentials:
- Guru: `guru@sekolah.com` / `password123`
- Siswa: `siswa@sekolah.com` / `password123`
- Kepsek: `kepsek@sekolah.com` / `password123`
- Kurikulum: `kurikulum@sekolah.com` / `password123`

## Troubleshooting

### Jika Android tidak bisa terhubung:
1. Pastikan IP address benar
2. Pastikan firewall tidak memblokir koneksi
3. Pastikan server dijalankan dengan `--host=0.0.0.0`
4. Pastikan Android dan PC terhubung ke jaringan yang sama
5. Coba akses `http://IP_PC:8000/api/health` dari browser Android

### Jika muncul error network:
1. Cek koneksi internet
2. Pastikan server berjalan
3. Tunggu beberapa detik setelah server dinyalakan sebelum mencoba
4. Coba restart aplikasi Android

## Server Requirements
- PHP >= 8.0
- Composer
- MySQL
- Ekstensi PHP: PDO, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON

## Developer Notes
- Gunakan file `.env` untuk konfigurasi lingkungan
- API menggunakan Laravel Sanctum untuk otentikasi
- Endpoint API dapat diakses dari jaringan lokal ketika server dijalankan dengan `--host=0.0.0.0`"# api-aplikasimonitoringkelas" 
