# 📚 SLiMS Plugin — Peminjaman Fasilitas Perpustakaan

Plugin ini menambahkan fitur **peminjaman fasilitas non-buku** pada sistem **SLiMS (Senayan Library Management System)**.

Perpustakaan tidak hanya meminjamkan buku, tetapi juga berbagai fasilitas seperti:

- Kamera
- Tripod
- Drone
- Sound System
- Lighting
- Proyektor
- Ruang Multimedia
- Peralatan dokumentasi
- dan fasilitas lainnya

Plugin ini dibuat agar peminjaman fasilitas dapat **terkelola rapi, terdokumentasi, dan memiliki surat izin resmi**.

Plugin dirancang **sederhana, ringan, dan mengikuti struktur SLiMS** sehingga mudah dipasang pada sistem yang sudah berjalan.

---

# ✨ Fitur Utama

## 📋 Form Permohonan Fasilitas

Form peminjaman yang dapat diakses oleh pengguna melalui browser.

Form ini dapat dibuka melalui:

```
/plugins/peminjaman_fasilitas/form.php
```

Form dapat diakses dari:

- komputer
- laptop
- tablet
- smartphone

Field yang tersedia:

| Field | Keterangan |
|------|------|
| Nama | Nama lengkap peminjam |
| Kelas | Kelas atau jurusan |
| Nomor Kontak | Nomor telepon / WhatsApp |
| Penanggung Jawab | Guru / Pembina kegiatan |
| Nama Kegiatan | Nama kegiatan |
| Fasilitas | Fasilitas yang dipinjam |
| Lokasi | Indoor / Outdoor |
| Nama Lokasi | Lokasi kegiatan |
| Waktu Pinjam | Tanggal dan jam mulai |
| Waktu Selesai | Tanggal dan jam selesai |

---

# 🔍 Pencarian Fasilitas Otomatis

Fasilitas tidak dibuat tabel baru.

Plugin **menggunakan koleksi yang sudah ada di SLiMS**, yaitu:

- `biblio`
- `item`

Data yang diambil:

| Kolom | Fungsi |
|------|------|
| biblio.title | Nama fasilitas |
| item.item_code | Kode inventaris |
| biblio.spec_detail_info | Keterangan fasilitas |

Contoh tampilan fasilitas:

```
Kamera Sony A6400 (2020.97686.1), 1 Set
Tripod Kamera (2020.97690.1), 1 Unit
```

Hanya koleksi dengan **GMD tertentu** yang ditampilkan.

Contoh:

```
Fasilitas Perpustakaan
```

---

# 🧾 Surat Izin Peminjaman

Plugin dapat menghasilkan **surat izin peminjaman fasilitas secara otomatis**.

Surat berisi:

- nomor surat
- identitas peminjam
- penanggung jawab
- kegiatan
- lokasi
- daftar fasilitas
- waktu peminjaman
- waktu persetujuan
- area tanda tangan
- catatan pengembalian

---

# 🔢 Format Nomor Surat

Nomor surat dibuat otomatis dengan format:

```
YYYYMMDD/PUS/XXX/NNN
```

Contoh:

```
20260311/PUS/IND/008
```

Penjelasan:

| Bagian | Arti |
|------|------|
| YYYYMMDD | tanggal kegiatan |
| PUS | kode perpustakaan |
| XXX | 3 huruf nama peminjam |
| NNN | nomor urut |

---

# ✅ Sistem Persetujuan

Permohonan akan masuk ke halaman admin:

```
Sirkulasi → Peminjaman Fasilitas
```

Petugas dapat:

- Approve
- Reject
- Print Surat
- Hapus permohonan

Status permohonan:

| Status | Keterangan |
|------|------|
| pending | menunggu persetujuan |
| approved | disetujui |
| rejected | ditolak |

---

# 🔒 Sistem Keamanan

Form publik memiliki beberapa proteksi.

## CSRF Protection

Setiap form memiliki **CSRF token** untuk mencegah serangan CSRF.

## Math Challenge

Pengguna harus menjawab soal matematika sederhana sebelum submit.

Contoh:

```
Berapa hasil: 7 + 3 ?
```

## Form Lock

Setelah submit berhasil:

- form akan terkunci
- countdown 3 detik
- form reset otomatis

Ini membantu mencegah spam.

---

# 📱 Responsive Design

Form dirancang agar:

- nyaman digunakan di smartphone
- tidak perlu zoom
- tombol besar
- input besar

Form akan menyesuaikan ukuran layar secara otomatis.

---

# 🗄 Struktur Database

Plugin menggunakan tabel:

```
facility_loan
```

Struktur tabel:

| Kolom | Tipe | Keterangan |
|-------|------|------|
| loan_id | INT | ID permohonan |
| borrower_name | VARCHAR | nama peminjam |
| borrower_class | VARCHAR | kelas |
| contact | VARCHAR | nomor kontak |
| supervisor | VARCHAR | penanggung jawab |
| activity_name | VARCHAR | nama kegiatan |
| location_type | VARCHAR | indoor / outdoor |
| location_name | VARCHAR | lokasi kegiatan |
| items | TEXT | daftar fasilitas |
| start_datetime | DATETIME | waktu pinjam |
| end_datetime | DATETIME | waktu selesai |
| status | VARCHAR | status permohonan |
| loan_number | VARCHAR | nomor surat |
| created_at | DATETIME | waktu permohonan |
| approved_at | DATETIME | waktu persetujuan |
| rejected_at | DATETIME | waktu penolakan |
| returned_at | DATETIME | waktu pengembalian |

Plugin akan otomatis:

- membuat tabel jika belum ada
- menambahkan kolom jika terjadi update

---

# 📂 Struktur Plugin

```
plugins/
└── peminjaman_fasilitas
    ├── peminjaman_fasilitas.plugin.php
    ├── index.php
    ├── form.php
    ├── search_item.php
    └── print.php
```

Penjelasan:

| File | Fungsi |
|------|------|
| peminjaman_fasilitas.plugin.php | registrasi plugin |
| index.php | halaman admin |
| form.php | form peminjaman |
| search_item.php | pencarian fasilitas |
| print.php | cetak surat |

---

# ⚙️ Instalasi

1. Download plugin

2. Letakkan pada folder:

```
slims/plugins/
```

3. Struktur akhir:

```
slims/plugins/peminjaman_fasilitas
```

4. Aktifkan plugin melalui **Plugin Manager SLiMS**

5. Menu akan muncul pada:

```
Sirkulasi → Peminjaman Fasilitas
```

---

# 🔄 Alur Sistem

```
User membuka form
        ↓
Mengisi permohonan
        ↓
Data tersimpan di database
        ↓
Petugas melakukan approval
        ↓
Surat peminjaman dicetak
        ↓
Fasilitas digunakan
        ↓
Dicatat saat pengembalian
```

---

# 🧩 Integrasi dengan SLiMS

Plugin menggunakan tabel bawaan SLiMS:

- `biblio`
- `item`

Sehingga fasilitas dapat dikelola seperti koleksi perpustakaan biasa.

---

# 🚀 Pengembangan Selanjutnya

Beberapa fitur yang bisa ditambahkan:

- deteksi bentrok jadwal fasilitas
- kalender penggunaan fasilitas
- status fasilitas tersedia / dipakai
- statistik penggunaan fasilitas
- QR code verifikasi surat
- dashboard penggunaan fasilitas
- notifikasi email / WhatsApp

---

# 👨‍💻 Author

**Indra Febriana Rulliawan**

GitHub

```
https://github.com/indra-f-r
```

---

# 📜 License

Plugin ini dibuat untuk komunitas pengguna **SLiMS** dan dapat dikembangkan lebih lanjut sesuai kebutuhan perpustakaan.
