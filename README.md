# 💰 Sistem Pengajuan Transaksi Pengeluaran

Sistem Pengajuan Transaksi Pengeluaran adalah aplikasi berbasis web yang dikembangkan menggunakan **Laravel 13** untuk mengelola proses pengajuan transaksi pengeluaran perusahaan melalui mekanisme **Workflow Approval**.

Sistem menerapkan **Role-Based Access Control (RBAC)** sehingga setiap pengguna memiliki hak akses sesuai perannya. Selain itu, sistem dilengkapi dengan validasi budget, notifikasi email otomatis, activity log, audit trail, dan REST API untuk mendukung proses bisnis yang lebih efektif.

> Project ini dibuat sebagai pemenuhan **Tes Karyawan IT – Web Application Developer**.

---

# 🚀 Teknologi

- Laravel 13
- PHP 8.3
- MySQL
- Bootstrap 5
- HTML5
- CSS3
- JavaScript
- Spatie Laravel Permission
- Laravel Mail (Resend)
- REST API
- Git & GitHub

---

# ✨ Fitur

## Authentication

- Login
- Logout
- Role Based Access Control (RBAC)

---

## User Management (Admin)

- Kelola User
- Kelola Role User

---

## Pengajuan Transaksi

- Membuat pengajuan transaksi
- Upload dokumen pendukung
- Melihat detail pengajuan
- Riwayat pengajuan

---

## Workflow Approval

- Approval Supervisor
- Approval Manager
- Approval Direktur
- Riwayat Approval
- Catatan Approval / Reject

---

## Finance

- Validasi Budget
- Proses Pembayaran
- Status Paid
- Status Rejected

---

## Monitoring

- Dashboard Statistik
- Activity Log
- Audit Trail

---

## Fitur Tambahan

- Email Notification
- Export Excel
- REST API

---

# 👥 Role User

| Role | Hak Akses |
|------|-----------|
| **Admin** | Mengelola User Management, Activity Log, dan Audit Trail. |
| **Staff** | Membuat pengajuan transaksi, melihat status dan riwayat pengajuan. |
| **Supervisor (SPV)** | Melakukan approval tahap pertama. |
| **Manager** | Melakukan approval tahap kedua. |
| **Direktur** | Melakukan approval akhir sesuai workflow. |
| **Finance** | Melakukan validasi budget dan proses pembayaran. |

> **Catatan:** Role **Admin** merupakan pengembangan tambahan untuk kebutuhan administrasi sistem dan tidak terlibat dalam proses workflow approval.

---

# 🔄 Workflow Approval

Workflow approval mengikuti aturan bisnis pada soal tes.

### 1. Kategori **PO Produk**

```
Staff
   │
Direktur
   │
Finance
```

---

### 2. Kategori selain **PO Produk**

Nominal ≤ Rp5.000.000

```
Staff
   │
Supervisor
   │
Finance
```

---

### 3. Kategori selain **PO Produk**

Nominal > Rp5.000.000

```
Staff
   │
Supervisor
   │
Manager
   │
Finance
```

---

### 4. Nominal > Rp10.000.000

```
Staff
   │
Supervisor
   │
Manager
   │
Direktur
   │
Finance
```

---

# 💰 Budget Validation

Validasi budget dilakukan oleh **Finance** sebelum proses pembayaran.

Sistem akan membandingkan nilai pengajuan dengan sisa budget kategori.

Apabila budget mencukupi:

- Finance dapat memproses pembayaran.
- Status berubah menjadi **Paid**.

Apabila budget tidak mencukupi:

- Sistem menampilkan informasi bahwa budget tidak mencukupi.
- Tombol **Paid** dinonaktifkan.
- Finance tetap dapat melakukan **Reject**.
- Status berubah menjadi **Rejected**.

---

# 📧 Email Notification

Sistem mengirimkan email secara otomatis pada setiap perubahan status pengajuan menggunakan **Laravel Mail** yang terintegrasi dengan **Resend**.

### Alur Notifikasi

| Kondisi | Penerima |
|----------|----------|
| Staff membuat pengajuan | Supervisor (SPV) |
| Supervisor menyetujui | Manager (sesuai workflow) |
| Manager menyetujui | Direktur (jika diperlukan) |
| Seluruh approval selesai | Finance |
| Finance melakukan Paid | Staff |
| Supervisor / Manager / Direktur melakukan Reject | Staff |
| Finance melakukan Reject | Staff |

Dengan mekanisme ini setiap pihak yang terlibat akan menerima informasi secara otomatis tanpa harus selalu membuka aplikasi.

---

# 🗄️ Struktur Database

Tabel utama yang digunakan:

| Tabel | Deskripsi |
|--------|-----------|
| users | Data pengguna |
| roles | Data role |
| permissions | Data permission |
| submissions | Data pengajuan |
| approvals | Riwayat approval |
| categories | Master kategori |
| budgets | Master budget |
| payments | Data pembayaran |
| activity_log | Activity Log |

---

# 🔗 Database Relationship

Relasi antar tabel pada sistem adalah sebagai berikut.

| Relasi | Jenis | Keterangan |
|--------|-------|------------|
| Roles → Users | One-to-Many | Satu role dapat dimiliki oleh banyak user. |
| Users → Submissions | One-to-Many | Satu user dapat membuat banyak pengajuan. |
| Categories → Submissions | One-to-Many | Satu kategori dapat digunakan oleh banyak pengajuan. |
| Categories → Budgets | One-to-Many | Setiap kategori memiliki data budget. |
| Submissions → Approvals | One-to-Many | Satu pengajuan memiliki beberapa riwayat approval. |
| Users → Approvals | One-to-Many | User dapat melakukan banyak approval sesuai role. |
| Submissions → Payments | One-to-One | Setiap pengajuan memiliki satu data pembayaran. |
| Users → Payments | One-to-Many | Finance dapat memproses banyak pembayaran. |

### Entity Relationship Diagram (ERD)

```
roles
   │
   └──────< users
               │
               ├────────< submissions >──────── categories
               │               │                     │
               │               │                     └──────< budgets
               │               │
               │               ├────────< approvals
               │               │              │
               │               │              └──── approved by users
               │               │
               │               └──────── payments
               │
               └────────< payments
```

---
# ⚙️ Cara Instalasi

Clone repository

```bash
git clone https://github.com/USERNAME/employee-test.git
```

Masuk ke folder project

```bash
cd employee-test
```

Install dependency PHP

```bash
composer install
```

Install dependency frontend

```bash
npm install
```

Salin file environment

```bash
cp .env.example .env
```

Generate application key

```bash
php artisan key:generate
```

Konfigurasikan database pada file `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=expenditure_system
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration dan seeder

```bash
php artisan migrate --seed
```

Buat symbolic link

```bash
php artisan storage:link
```

Compile asset

```bash
npm run build
```

---

# ▶️ Cara Menjalankan Project

Jalankan Laravel

```bash
php artisan serve
```

Jalankan Vite

```bash
npm run dev
```

Buka browser

```
http://127.0.0.1:8000
```

---

# 👤 Akun Login Testing

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@test.com | password |
| Staff | staff@test.com | password |
| Supervisor (SPV) | spv@test.com | password |
| Manager | manager@test.com | password |
| Direktur | direktur@test.com | password |
| Finance | finance@test.com | password |

> **Catatan:**
>
> - Untuk menguji fitur **Email Notification**, gunakan alamat email yang aktif.
> - Mengganti **Email**, bisa lewat profile akun user atau dari akun Admin.
> - Apabila email tidak ditemukan di **Inbox**, silakan periksa folder **Spam/Junk**.
> - Domain pengirim menggunakan domain yang relatif baru sehingga beberapa penyedia layanan email (misalnya Gmail atau Outlook) dapat mengklasifikasikan email sebagai **Spam** hingga reputasi domain meningkat. Hal ini tidak memengaruhi fungsi pengiriman email pada aplikasi.

---

# 📂 Struktur Folder

```
app/
bootstrap/
config/
database/
public/
resources/
routes/
storage/
```

---

# 🚀 Pengembangan Tambahan

Selain memenuhi requirement pada soal tes, project ini juga mengimplementasikan beberapa pengembangan tambahan, yaitu:

- Role Admin
- User Management
- Activity Log
- Audit Trail
- Email Notification menggunakan Resend
- Export Excel
- REST API

Seluruh pengembangan tersebut ditambahkan tanpa mengubah alur **Workflow Approval** yang menjadi requirement utama.

---

# 👨‍💻 Author

**Ariya Handika Mulyana**

S1 Sistem Informasi - Universitas Galuh

- **Email** : ariyahandika04@gmail.com
- **Alamat** : Ciamis, Jawa Barat
- **No. HP** : 085624440728
- **GitHub** : https://github.com/ariyahandikam