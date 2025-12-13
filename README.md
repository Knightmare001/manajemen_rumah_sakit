# manajemen_rumah_sakit

# 🏥 Sistem Manajemen Pasien & Jadwal Dokter

Project ini adalah **aplikasi web sederhana berbasis PHP & MySQL** yang digunakan untuk mengelola:
- Data pasien
- Jadwal praktik dokter
- Autentikasi user (admin)

Project dibuat dan dijalankan secara **local (localhost)** menggunakan **XAMPP atau Laragon**.

---

## ✨ Fitur
- 🔐 Login Admin
- 👤 Manajemen User
- 🧑‍⚕️ Manajemen Data Dokter
- 📅 Manajemen Jadwal Praktik Dokter
- 🧾 Manajemen Data Pasien
- 🎨 Tampilan dashboard sederhana & responsive

---

## 🛠️ Teknologi yang Digunakan
- **PHP (Native)**
- **MySQL**
- **HTML5**
- **CSS3**
- **JavaScript (basic)**
- **XAMPP / Laragon**

---

## 📂 Struktur Database

### 1️⃣ Tabel `users`
| Field | Tipe | Keterangan |
|-----|------|-----------|
| id | INT | Primary Key |
| nama | VARCHAR | Nama user |
| email | VARCHAR | Email user |
| password | VARCHAR | Password (hashed) |

---

### 2️⃣ Tabel `dokter`
| Field | Tipe | Keterangan |
|-----|------|-----------|
| id_dokter | INT | Primary Key |
| nama_dokter | VARCHAR | Nama dokter |
| spesialis | VARCHAR | Spesialis |
| hari_praktik | VARCHAR | Hari praktik |
| jam_mulai | TIME | Jam mulai |
| jam_selesai | TIME | Jam selesai |

---

### 3️⃣ Tabel `pasien`
| Field | Tipe | Keterangan |
|-----|------|-----------|
| id | INT | Primary Key |
| nama | VARCHAR | Nama pasien |
| deskripsi_keluhan | TEXT | Keluhan |
| id_dokter | INT | Relasi ke dokter |
| hari_praktik | VARCHAR | Hari praktik |
| jam_mulai | TIME | Jam mulai |
| email | VARCHAR | Email pasien |

---

## ⚙️ Cara Menjalankan Project

### 1️⃣ Download Folder atau ZIPnya
### 2️⃣ Pindahkan ke folder server
XAMPP → htdocs/

Laragon → www/

### 3️⃣ Buat Database
CREATE DATABASE klinik;
USE db_rumah_sakit;

### 4️⃣ Buat Tabel

#Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

####Table Dokter
CREATE TABLE dokter (
    id_dokter INT AUTO_INCREMENT PRIMARY KEY,
    nama_dokter VARCHAR(100) NOT NULL,
    spesialis VARCHAR(100) NOT NULL,
    hari_praktik VARCHAR(20) NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL
);

INSERT INTO dokter (nama_dokter, spesialis, hari_praktik, jam_mulai, jam_selesai) VALUES
('Dr. Siti Aminah', 'Umum', 'Senin', '08:00:00', '12:00:00'),
('Dr. Hartono', 'THT', 'Selasa', '09:00:00', '12:00:00');
('Dr. Setiawan', 'Gigi', 'Rabu', '09:00:00', '12:00:00');


#Table Pasien
CREATE TABLE pasien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    deskripsi_keluhan TEXT NOT NULL,
    id_dokter INT NOT NULL,
    hari_praktik VARCHAR(20) NOT NULL,
    jam_mulai TIME NOT NULL,
    email VARCHAR(100),

    CONSTRAINT fk_pasien_dokter
        FOREIGN KEY (id_dokter)
        REFERENCES dokter(id_dokter)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

### 5️⃣ Jalankan di browser
arduino
Salin kode
http://localhost/nama-folder-project
