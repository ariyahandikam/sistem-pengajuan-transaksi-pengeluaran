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
- Spatie Activity Log
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

### Tabel Utama

| Tabel | Deskripsi |
|--------|-----------|
| users | Data pengguna dengan kolom `role_id` untuk role primary |
| roles | Master role (Staff, Supervisor, Manager, Direktur, Finance, Admin) |
| submissions | Data pengajuan transaksi pengeluaran |
| approvals | Riwayat approval dengan multi-level workflow |
| categories | Master kategori pengajuan |
| budgets | Master budget per kategori per tahun |
| payments | Data pembayaran yang sudah di-approve |
| activity_log | Activity Log untuk audit trail |

### Tabel Spatie Laravel Permission

| Tabel | Deskripsi |
|--------|-----------|
| permissions | Master permission untuk fine-grained access control |
| role_has_permissions | Pivot table relasi Many-to-Many antara `roles` dan `permissions` |
| model_has_roles | Pivot table relasi Many-to-Many antara `users` dan `roles` |
| model_has_permissions | Direct permission assignment untuk users |

> **Catatan:** Package **Spatie Laravel Permission** secara otomatis membuat dan mengelola tabel pivot (`model_has_roles`, `model_has_permissions`, dan `role_has_permissions`) untuk mengelola Role-Based Access Control (RBAC) yang lebih fleksibel dan scalable.
---

# 🔗 Database Relationship

Relasi antar tabel pada sistem adalah sebagai berikut.

| Relasi | Jenis | Keterangan |
|--------|-------|------------|
| Roles ↔ Users | Many-to-Many | Menggunakan tabel pivot `model_has_roles` dari Spatie Laravel Permission. Satu user dapat memiliki banyak role, dan satu role dapat dimiliki oleh banyak user. |
| Users → Submissions | One-to-Many | Satu user (staff) dapat membuat banyak pengajuan. |
| Categories → Submissions | One-to-Many | Satu kategori dapat digunakan oleh banyak pengajuan. |
| Categories → Budgets | One-to-Many | Satu kategori memiliki data budget per tahun. |
| Submissions → Approvals | One-to-Many | Satu pengajuan memiliki beberapa riwayat approval (multi-level workflow). |
| Users → Approvals | One-to-Many | User dapat melakukan banyak approval sesuai role (nullable saat pending). |
| Submissions → Payments | One-to-One | Setiap pengajuan memiliki satu data pembayaran (unique constraint). |
| Users → Payments | One-to-Many | Finance user dapat memproses banyak pembayaran. |
| Roles ↔ Permissions | Many-to-Many | Menggunakan tabel pivot `role_has_permissions` dari Spatie Laravel Permission. |

### Entity Relationship Diagram (ERD)

```
                            permissions
                                 │
                                 │
                         role_has_permissions
                                 │
                            roles
                                 │
                        model_has_roles
                                 │
                            users
                 ┌──────────────┬─────────────┐
                 │              │             │
                 │              │             └──────────< payments
                 │              │                          │
                 │              │                          └── processed_by
                 │              │
                 └────────< submissions >──────────── categories
                                 │                         │
                                 │                         └───< budgets
                                 │
                         └────< approvals >────────── users
                                               (approved_by)
```

### Foreign Keys

| Tabel | Foreign Key | Referensi |
|--------|-------------|-----------|
| `users` | `role_id` | `roles.id` |
| `submissions` | `user_id` | `users.id` |
| `submissions` | `category_id` | `categories.id` |
| `budgets` | `category_id` | `categories.id` |
| `approvals` | `submission_id` | `submissions.id` |
| `approvals` | `user_id` | `users.id` (nullable) |
| `payments` | `submission_id` | `submissions.id` (unique) |
| `payments` | `user_id` | `users.id` |
| `model_has_roles` | `role_id`, `model_id` | `roles.id`, `users.id` |
| `role_has_permissions` | `role_id`, `permission_id` | `roles.id`, `permissions.id` |
| `model_has_permissions` | `permission_id`, `model_id` | `permissions.id`, `users.id` |

### Column Details

#### Tabel `roles`
| Kolom | Tipe | Deskripsi |
|--------|------|-----------|
| `id` | bigint | Primary key |
| `name` | string | Nama role (e.g., "Staff", "Supervisor", "Manager", "Direktur", "Finance", "Admin") |
| `guard_name` | string | Guard name dari Spatie (default: "web") |
| `slug` | string | Slug unik untuk identifikasi (e.g., "staff", "spv", "manager") |
| `timestamps` | - | `created_at`, `updated_at` |

#### Tabel `submissions`
| Kolom | Tipe | Deskripsi |
|--------|------|-----------|
| `id` | bigint | Primary key |
| `submission_number` | string | Nomor pengajuan unik (format: TRX-YYYYMMDD-XXXX) |
| `submission_date` | date | Tanggal pengajuan |
| `user_id` | bigint FK | Staff yang membuat pengajuan |
| `category_id` | bigint FK | Kategori pengajuan |
| `amount` | decimal(15,2) | Nominal pengajuan |
| `description` | text | Deskripsi / keterangan |
| `attachment` | json | Path file lampiran (JSON array) |
| `status` | string | Status: draft, submitted, waiting_spv, waiting_manager, waiting_direktur, waiting_finance, paid, rejected |
| `timestamps` | - | `created_at`, `updated_at` |

#### Tabel `approvals`
| Kolom | Tipe | Deskripsi |
|--------|------|-----------|
| `id` | bigint | Primary key |
| `submission_id` | bigint FK | Referensi ke pengajuan |
| `user_id` | bigint FK (nullable) | User yang melakukan approval (null jika pending) |
| `role` | string | Role approver (e.g., "spv", "manager", "direktur", "finance") |
| `status` | string | Status: pending, approved, rejected |
| `notes` | text | Catatan approval / reject (opsional) |
| `approved_at` | timestamp | Waktu approval |
| `timestamps` | - | `created_at`, `updated_at` |

#### Tabel `payments`
| Kolom | Tipe | Deskripsi |
|--------|------|-----------|
| `id` | bigint | Primary key |
| `submission_id` | bigint FK (unique) | Referensi ke pengajuan (one-to-one) |
| `user_id` | bigint FK | Finance user yang memproses pembayaran |
| `amount` | decimal(15,2) | Nominal pembayaran |
| `payment_date` | date | Tanggal pembayaran |
| `payment_method` | string | Metode: transfer, cash |
| `reference_number` | string | Nomor referensi / bukti pembayaran |
| `notes` | text | Catatan pembayaran (opsional) |
| `timestamps` | - | `created_at`, `updated_at` |

#### Tabel `categories`
| Kolom | Tipe | Deskripsi |
|--------|------|-----------|
| `id` | bigint | Primary key |
| `name` | string | Nama kategori |
| `is_po_produk` | boolean | Flag untuk kategori "PO Produk" (memiliki workflow khusus) |
| `timestamps` | - | `created_at`, `updated_at` |

#### Tabel `budgets`
| Kolom | Tipe | Deskripsi |
|--------|------|-----------|
| `id` | bigint | Primary key |
| `category_id` | bigint FK | Referensi ke kategori |
| `year` | year | Tahun budget |
| `total_budget` | decimal(15,2) | Total budget tahun tersebut |
| `used_budget` | decimal(15,2) | Budget yang sudah digunakan |
| `timestamps` | - | `created_at`, `updated_at` |
| **Unique** | `(category_id, year)` | Kombinasi unik per kategori per tahun |

---
# ⚙️ Cara Instalasi

Clone repository

```bash
git clone https://github.com/ariyahandikam/sistem-pengajuan-transaksi-pengeluaran.git
```

Masuk ke folder project

```bash
cd sistem-pengajuan-transaksi-pengeluaran
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

Jalankan aplikasi

```bash
php artisan serve
```

Untuk development, jalankan Vite pada terminal terpisah

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

# 📄 License

Project ini dibuat sebagai pemenuhan **Tes Karyawan IT – Web Application Developer** dan digunakan sebagai portofolio pengembangan aplikasi berbasis Laravel.

---

# 👨‍💻 Author

**Ariya Handika Mulyana**

S1 Sistem Informasi - Universitas Galuh

- **Email** : ariyahandika04@gmail.com
- **Alamat** : Ciamis, Jawa Barat
- **No. HP** : 085624440728
- **GitHub** : https://github.com/ariyahandikam
- **LinkedIn** : https://www.linkedin.com/in/ariya-handika-mulyana-5339792a5/