-- ============================================
-- Maroon Company - Galeri Barang Antik
-- Database Setup
-- ============================================

CREATE DATABASE IF NOT EXISTS maroon_antique;
USE maroon_antique;

-- Tabel Kategori
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Produk
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(15,2) NOT NULL,
    stock INT DEFAULT 0,
    era VARCHAR(100),
    origin VARCHAR(100),
    condition_status ENUM('Excellent','Very Good','Good','Fair') DEFAULT 'Good',
    image VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Tabel Pesan / Kontak
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Data Kategori
-- ============================================
INSERT INTO categories (name, slug, description) VALUES
('Furnitur Antik', 'furnitur-antik', 'Kursi, meja, lemari, dan furnitur klasik berusia ratusan tahun'),
('Keramik & Porselen', 'keramik-porselen', 'Guci, piring, cangkir keramik dari berbagai dinasti dan era'),
('Jam & Aksesori', 'jam-aksesori', 'Jam dinding, jam tangan, dan aksesori klasik bernilai sejarah'),
('Lukisan & Seni', 'lukisan-seni', 'Lukisan, ukiran, dan karya seni dari berbagai periode'),
('Perhiasan Antik', 'perhiasan-antik', 'Cincin, kalung, gelang, dan perhiasan klasik dari berbagai era');

-- ============================================
-- Data Produk Sample
-- ============================================
INSERT INTO products (category_id, name, slug, description, price, stock, era, origin, condition_status, is_featured) VALUES
(1, 'Kursi Rotan Victorian', 'kursi-rotan-victorian', 'Kursi rotan asli era Victorian (1870-1900) dengan ukiran tangan yang detail. Material rotan alami dengan sandaran berbentuk mahkota khas gaya Victoria. Kondisi sangat baik dengan sedikit jejak usia yang menambah keautentikannya.', 4500000, 2, '1870-1900', 'Inggris', 'Very Good', 1),
(1, 'Lemari Jati Kolonial', 'lemari-jati-kolonial', 'Lemari kayu jati solid dari era kolonial Belanda (1920-1940). Memiliki panel ukiran bunga dan daun khas Art Nouveau. Kunci dan engsel kuningan asli masih berfungsi dengan baik.', 18500000, 1, '1920-1940', 'Hindia Belanda', 'Good', 1),
(2, 'Guci Keramik Dinasti Ming', 'guci-keramik-ming', 'Guci keramik biru-putih bergaya Dinasti Ming (abad ke-16) dengan motif naga dan awan. Memiliki sertifikat keaslian dari lembaga appraisal independen. Kondisi sempurna tanpa retakan.', 125000000, 1, 'Abad 16', 'Tiongkok', 'Excellent', 1),
(2, 'Set Piring Delft Belanda', 'set-piring-delft', 'Set 6 piring keramik Delft biru-putih asli buatan Belanda (1890-1910). Setiap piring bergambar pemandangan kota dan kincir angin khas Belanda. Sedikit keausan normal sesuai usia.', 8750000, 3, '1890-1910', 'Belanda', 'Very Good', 0),
(3, 'Jam Dinding Grandfather Clock', 'jam-grandfather-clock', 'Jam Grandfather Clock mahoni asli buatan Jerman (1905). Tinggi 210cm dengan mekanisme pendulum masih berfungsi sempurna. Chime berdentang setiap jam dengan suara yang khas dan merdu.', 45000000, 1, '1905', 'Jerman', 'Excellent', 1),
(3, 'Jam Tangan Pocket Watch Gold', 'jam-pocket-watch-gold', 'Jam saku emas 18 karat dari era Edwardian (1910-1920). Casing berukir bunga dengan inisial pemilik lama. Mekanisme manual winding masih berfungsi akurat. Dilengkapi rantai emas asli.', 32000000, 1, '1910-1920', 'Swiss', 'Very Good', 0),
(4, 'Lukisan Cat Minyak Potret Noni', 'lukisan-potret-noni', 'Lukisan cat minyak potret wanita Belanda-Indonesia (Noni) karya pelukis anonim era 1930-an. Kanvas asli dengan bingkai kayu ukir emas. Ukuran 60x80cm. Warna masih cerah dan detail wajah sangat halus.', 22000000, 1, '1930-an', 'Hindia Belanda', 'Good', 1),
(4, 'Ukiran Kayu Garuda Jepara', 'ukiran-garuda-jepara', 'Ukiran kayu jati motif Garuda Pancasila dari pengrajin Jepara (1960-an). Relief tiga dimensi dengan detail yang sangat rumit. Ukuran 80x60cm. Patina alami menambah nilai estetika.', 6500000, 2, '1960-an', 'Indonesia', 'Very Good', 0),
(5, 'Cincin Berlian Edwardian', 'cincin-berlian-edwardian', 'Cincin berlian asli era Edwardian (1910-1915) dengan seting platinum dan gold. Berlian oval 1.2 karat dikelilingi berlian kecil motif floral. Dilengkapi sertifikat GIA dan laporan appraisal.', 85000000, 1, '1910-1915', 'Inggris', 'Excellent', 1),
(5, 'Kalung Cameo Ivory', 'kalung-cameo-ivory', 'Kalung cameo gading asli bergambar dewi Yunani, era Victorian (1880-1895). Bingkai emas 14 karat dengan rantai emas twisted. Ukiran sangat detail menampilkan profil wanita dengan mahkota bunga.', 15500000, 1, '1880-1895', 'Italia', 'Very Good', 0);
