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
- 🧾 Manajemen Data Pasien
- 🎨 Tampilan dashboard sederhana

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
Pastikan nama foldernya "manajemen_rumah_sakit" jika beda bisa diubah dulu
### 2️⃣ Pindahkan ke folder server
XAMPP → htdocs/

Laragon → www/

### 3️⃣ Buat Database
Buka file sql.txt

### 4️⃣ Buat Tabel
Buka file sql.txt

### 5️⃣ Jalankan di browser
http://localhost/manajemen_rumah_sakit
