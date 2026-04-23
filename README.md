# 📦 Aplikasi Inventory Lab & Peminjaman

Aplikasi sistem manajemen inventaris laboratorium, peminjaman barang, sistem pengaduan/laporan aset rusak, dan live-chat terintegrasi untuk pengguna dan admin.

## 🌟 Fitur Utama
1. **Autentikasi NIP:** Login aman yang dikunci menggunakan struktur Nomor Induk (NIP).
2. **Katalog Aset & Peminjaman:** Sistem *booking*/peminjaman untuk "Bahan" (habis pakai) dan "Alat" (pengembalian). 
3. **Pengaduan Laporan:** User dapat melampirkan foto barang rusak dan kronologinya, dan direspon otomatis.
4. **Chat Sistem (Real-time Simulation):** Live chat terpadu antara User dan tim Admin. Mendukung kirim pesan dan attachment file (Gambar & Video hingga 10MB).
5. **Notifikasi Pintar & Log Aktivitas:** Admin dan User sama-sama mendapat pemberitahuan untuk *action* penting yang terjadi di dalam web.

---

## 🚀 Panduan Instalasi (Untuk Device Lain)

Jika Anda ingin menjalankan atau membagikan (sharing) *project* ini ke laptop/komputer lain, pastikan komputer tersebut sudah meng-install:
- **PHP** (Minimal versi 8.1+)
- **Composer** (Untuk menginstal package Laravel)
- **Node.js** (Untuk mengompilasi CSS/JS)
- **Laragon / XAMPP** (Untuk menjalankan database MySQL)

### Langkah-langkah Menjalankan:

**1. Salin atau Ekstrak Folder**
Pindahkan folder aplikasi ini (`inventory-lab-revisi-4`) ke komputer tujuan (ideal-nya di `C:\laragon\www\` atau `C:\xampp\htdocs\`).

**2. Buka Terminal / CMD**
Buka terminal dan arahkan ke dalam folder direktori project ini.

**3. Instalasi Dependencies Backend (Composer)**
Jalankan perintah ini untuk menginstal semua kebutuhan utama Laravel:
```bash
composer install
```

**4. Instalasi Dependencies Frontend (NPM)**
Jalankan perintah ini untuk membangun tampilan UI:
```bash
npm install
npm run build
```

**5. Pengaturan Environment (Variabel Sistem)**
Duplikasi atau copy file `.env.example` lalu ubah namanya menjadi **`.env`**. Buka file `.env` yang baru dibuat dengan teks editor, pastikan bagian database-nya sesuai dengan nama database di Laragon/XAMPP Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_inventory
DB_USERNAME=root
DB_PASSWORD=
```
*(Jangan lupa hidupkan MySQL/Database server d Laragon/XAMPP Anda dan buat database kosong bernama `db_inventory` terlebih dahulu di PhpMyAdmin / HeidiSQL).*

**6. Generate Kunci Keamanan Laravel**
Jalankan perintah ini agar sesi login dan enkripsi aman:
```bash
php artisan key:generate
```

**7. Menghubungkan Folder File (Sangat Penting)**
Aplikasi ini memiliki fitur Chat Foto/Video dan Upload Gambar. Wajib jalankan ini agar file yang di-upload bisa tampil di website:
```bash
php artisan storage:link
```

**8. Membangun Tabel & Akun Akun Bawaan (Migrate + Seeder)**
Jalankan perintah ini untuk memasukkan kerangka tabel ke MySQL sekaligus memasukkan data dummy (Admin pertama):
```bash
php artisan migrate:fresh --seed
```

**9. Jalankan Server / Website**
Ketik perintah terakhir ini. Website akan mulai menyala:
```bash
php artisan serve
```
Ketik alamat `http://localhost:8000` di dalam *Google Chrome / Browser* Anda!

---

## 🔑 Akun Bawaan (Default Login)
Setelah menjalankan perintah langkah 8, beberapa akun tes sudah otomatis disiapkan, gunakan ini untuk login ke website:

**Role: Admin**
- NIP: `1234567890`
- Password: `admin123`

**Role: User Biasa**
- NIP: `0987654321`
- Password: `user123`
