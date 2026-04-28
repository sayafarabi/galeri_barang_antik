# 🏺 Maroon Antique Gallery
## Website E-Commerce Barang Antik | UTP Teknologi Web 2026

---

## 📁 Struktur Project

```
antique-gallery/
├── index.php               ← Beranda (Hero, Kategori, Featured Products)
├── catalog.php             ← Katalog semua produk + filter + search + pagination
├── product.php             ← Detail produk
├── about.php               ← Tentang Kami
├── contact.php             ← Kontak + Form pesan
├── database.sql            ← Script SQL untuk setup database
│
├── includes/
│   ├── config.php          ← Konfigurasi database + helper functions
│   ├── header.php          ← Navbar (sticky, responsive, hamburger menu)
│   └── footer.php          ← Footer + Back to top button
│
├── admin/
│   ├── index.php           ← Dashboard admin (statistik)
│   ├── products.php        ← Daftar produk (READ + DELETE)
│   ├── add_product.php     ← Tambah produk baru (CREATE)
│   ├── edit_product.php    ← Edit produk (UPDATE)
│   ├── categories.php      ← Kelola kategori (CRUD)
│   ├── messages.php        ← Baca pesan dari pengunjung
│   ├── admin_header.php    ← Layout header admin
│   └── admin_footer.php    ← Layout footer admin
│
└── uploads/                ← Folder gambar produk yang diupload
```

---

## ⚙️ Cara Instalasi

### 1. Persiapan
- Pastikan XAMPP / WAMP / Laragon sudah terinstall
- Aktifkan Apache dan MySQL

### 2. Copy Project
```
Salin folder `antique-gallery` ke:
- XAMPP: C:/xampp/htdocs/antique-gallery
- WAMP:  C:/wamp64/www/antique-gallery
```

### 3. Setup Database
1. Buka phpMyAdmin: `http://localhost/phpmyadmin`
2. Buat database baru: **maroon_antique**
3. Klik tab **Import**
4. Upload file `database.sql`
5. Klik **Go**

### 4. Konfigurasi Database
Edit file `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // Username MySQL Anda
define('DB_PASS', '');         // Password MySQL Anda (kosong jika default XAMPP)
define('DB_NAME', 'maroon_antique');
```

### 5. Permission Folder Uploads
Pastikan folder `uploads/` bisa ditulis (write permission):
- Windows XAMPP: biasanya sudah otomatis
- Linux: `chmod 775 uploads/`

### 6. Akses Website
- **Website**: `http://localhost/antique-gallery/`
- **Admin**: `http://localhost/antique-gallery/admin/`

---

## 🌟 Fitur Lengkap

### Halaman Publik (5 Halaman)
| Halaman | Fitur |
|---------|-------|
| **Beranda** | Hero section, kategori, produk unggulan, keunggulan, CTA |
| **Katalog** | Grid produk, filter kategori, filter kondisi, filter harga, pencarian, sort, pagination |
| **Detail Produk** | Foto, spesifikasi lengkap, produk serupa, tombol WhatsApp & kontak |
| **Tentang Kami** | Visi misi, tim kurator, proses verifikasi, statistik |
| **Kontak** | Form pesan (tersimpan di database), info kontak, social media |

### Panel Admin (CRUD Lengkap)
| Fitur | Detail |
|-------|--------|
| **Dashboard** | Statistik total produk, kategori, pesan; produk & pesan terbaru |
| **Kelola Produk** | List semua produk, search, toggle featured, hapus produk |
| **Tambah Produk** | Form lengkap + upload foto + validasi |
| **Edit Produk** | Edit semua field + ganti foto |
| **Kelola Kategori** | Tambah, edit, hapus kategori |
| **Pesan Masuk** | Baca pesan, balas via email, hapus, tandai sudah dibaca |

### Fitur Teknis
- ✅ Responsive mobile (Tailwind CSS)
- ✅ Hamburger menu di mobile
- ✅ Upload & preview gambar produk
- ✅ Pagination di katalog
- ✅ Filter multi-kondisi
- ✅ Notifikasi badge pesan belum dibaca
- ✅ Back to top button
- ✅ WhatsApp direct link dari detail produk
- ✅ BASE_URL otomatis (tidak perlu edit manual)

---

## 🎨 Tech Stack
- **Frontend**: Tailwind CSS (CDN), Font Awesome, Google Fonts (Playfair Display + Lato)
- **Backend**: PHP 7.4+ (MySQLi)
- **Database**: MySQL / MariaDB
- **Server**: Apache (XAMPP/WAMP)

---

## 📌 Catatan
- Tema: **Galeri Barang Antik** (digit NPM terakhir = 1)
- Warna utama: Maroon (#9b2335) + Gold (#c9a84c) + Cream (#faf4e8)
