<div align="center">

![Manual Book Cover](assets/images/manual_book_cover.png)

</div>

---

# MANUAL BOOK
## Aplikasi Starter Kit CodeIgniter 3 dengan RBAC

---

## 📋 Daftar Isi

1. [Pendahuluan](#pendahuluan)
2. [Persyaratan Sistem](#persyaratan-sistem)
3. [Instalasi](#instalasi)
4. [Fitur Utama](#fitur-utama)
5. [Fitur Dashboard Super Admin](#fitur-dashboard-super-admin-detail)
6. [Panduan Pengguna](#panduan-pengguna)
7. [Manajemen RBAC](#manajemen-rbac)
8. [CRUD Generator](#crud-generator)
9. [Chart Generator](#chart-generator)
10. [Summary Widget](#summary-widget)
11. [Database Management](#database-management)
12. [Pengaturan Aplikasi](#pengaturan-aplikasi)
13. [Management User](#management-user)
14. [Uninstall Aplikasi](#uninstall-aplikasi)
15. [Troubleshooting](#troubleshooting)
16. [Tips & Best Practices](#tips--best-practices)
17. [FAQ](#faq-frequently-asked-questions)
18. [Kontak & Support](#kontak--support)
19. [Changelog](#changelog)
20. [Lisensi](#lisensi)
21. [Penutup](#penutup)

---

## 1. Pendahuluan

### 1.1 Tentang Aplikasi

Aplikasi ini adalah **Starter Kit** berbasis **CodeIgniter 3** yang dilengkapi dengan sistem **Role-Based Access Control (RBAC)** yang canggih. Aplikasi ini dirancang untuk mempercepat pengembangan aplikasi web dengan menyediakan fitur-fitur siap pakai seperti:

- ✅ **RBAC (Role-Based Access Control)** - Manajemen hak akses berbasis role dan permission
- ✅ **CRUD Generator** - Generator otomatis untuk Create, Read, Update, Delete
- ✅ **Chart Generator** - Pembuat grafik dinamis dengan berbagai tipe
- ✅ **Summary Widget** - Widget ringkasan data dengan berbagai fungsi agregasi
- ✅ **Database Management** - Manajemen tabel database
- ✅ **User Management** - Manajemen pengguna dengan multi-role
- ✅ **Web-Based Installer** - Instalasi mudah melalui web browser

### 1.2 Teknologi yang Digunakan

- **Framework**: CodeIgniter 3.x
- **PHP**: 5.6 atau lebih tinggi (disarankan PHP 7.4+)
- **Database**: MySQL/MariaDB
- **Frontend**: AdminLTE 3.x, Bootstrap 4, jQuery
- **Libraries**: 
  - PhpSpreadsheet (untuk export Excel)
  - Dompdf (untuk export PDF)
  - Chart.js (untuk grafik)

---

## 2. Persyaratan Sistem

### 2.1 Server Requirements

| Komponen | Minimum | Rekomendasi |
|----------|---------|-------------|
| **PHP** | 5.6 | 7.4 atau 8.0 |
| **MySQL** | 5.5 | 5.7 atau MariaDB 10.x |
| **Web Server** | Apache 2.4 | Apache 2.4 / Nginx |
| **Memory** | 128 MB | 256 MB atau lebih |
| **Disk Space** | 50 MB | 100 MB atau lebih |

### 2.2 PHP Extensions yang Diperlukan

- `mysqli` atau `pdo_mysql`
- `mbstring`
- `openssl`
- `gd` atau `imagick` (untuk manipulasi gambar)
- `zip` (untuk export Excel)
- `xml` (untuk PhpSpreadsheet)

### 2.3 Apache Modules

- `mod_rewrite` (untuk URL rewriting)

---

## 3. Instalasi

### 3.1 Persiapan

1. **Download atau Clone** aplikasi ini ke direktori web server Anda
2. **Ekstrak** (jika dalam format zip) ke folder yang diinginkan
   ```
   Contoh: C:\laragon\www\my_staterkit
   ```

3. **Pastikan** folder berikut memiliki permission write:
   - `application/cache/`
   - `application/logs/`
   - `assets/images/`
   - `assets/dist/img/profile/`
   - `assets/backupdb/`

### 3.2 Instalasi via Web Browser

Aplikasi ini dilengkapi dengan **Web-Based Installer** yang memudahkan proses instalasi.

#### Step 1: Akses Installer

1. Buka browser dan akses URL aplikasi Anda
   ```
   http://localhost/my_staterkit
   ```

2. Anda akan diarahkan ke halaman installer secara otomatis

#### Step 2: Konfigurasi Database

1. Masukkan informasi database:
   - **Hostname**: `localhost` (atau IP server database)
   - **Username**: Username MySQL Anda
   - **Password**: Password MySQL Anda
   - **Database Name**: Nama database yang akan dibuat (contoh: `my_staterkit_db`)

2. Klik **"Test Connection"** untuk memverifikasi koneksi

3. Jika koneksi berhasil, klik **"Create Database & Continue"**

#### Step 3: Import Database Schema

1. Installer akan otomatis mengimport schema database dari file `installer_rbac_schema.sql`

2. Proses ini akan membuat tabel-tabel berikut:
   - `mst_user` - Tabel pengguna
   - `mst_roles` - Tabel role
   - `mst_modules` - Tabel modul aplikasi
   - `mst_permissions` - Tabel permission
   - `mst_parent_menus` - Tabel parent menu
   - `tbl_role_permissions` - Relasi role-permission
   - `tbl_user_roles` - Relasi user-role
   - `tbl_aplikasi` - Pengaturan aplikasi
   - `tbl_crud_history` - Riwayat CRUD generator
   - `tbl_chart_gen` - Konfigurasi chart
   - `tbl_summary_widgets` - Konfigurasi widget
   - `tbl_rbac_audit_log` - Log audit RBAC

3. Klik **"Continue"** setelah import selesai

#### Step 4: Konfigurasi Aplikasi

Masukkan informasi aplikasi dan admin:

**Informasi Aplikasi:**
- **Nama Aplikasi**: Nama aplikasi Anda
- **Alamat**: Alamat organisasi/perusahaan
- **Telepon**: Nomor telepon
- **Nama Developer**: Nama pengembang
- **Logo**: Upload logo aplikasi (opsional)
- **Title Icon**: Upload favicon (opsional)

**Pengaturan Sistem:**
- **Default Timezone**: Pilih timezone (default: Asia/Jakarta)
- **Session Timeout**: Waktu timeout session dalam detik (default: 300)
- **Uninstall Secret Key**: Kunci rahasia untuk uninstall (minimal 8 karakter)

**Akun Super Admin:**
- **Nama**: Nama lengkap admin
- **NIK**: Nomor Induk Kepegawaian/ID
- **Email**: Email admin (untuk login)
- **Password**: Password admin (minimal 6 karakter)

Klik **"Finalize Installation"**

#### Step 5: Instalasi Selesai

1. Anda akan melihat halaman konfirmasi instalasi berhasil
2. Klik **"Go to Login Page"** untuk masuk ke aplikasi
3. Login menggunakan email dan password admin yang telah dibuat

### 3.3 Instalasi Manual (Alternatif)

Jika Anda ingin instalasi manual:

1. **Import Database**
   ```sql
   CREATE DATABASE my_staterkit_db;
   ```
   
2. **Import Schema**
   ```bash
   mysql -u username -p my_staterkit_db < installer_rbac_schema.sql
   ```

3. **Konfigurasi Database**
   
   Edit file `application/config/database.php`:
   ```php
   $db['default'] = array(
       'hostname' => 'localhost',
       'username' => 'your_username',
       'password' => 'your_password',
       'database' => 'my_staterkit_db',
       // ... konfigurasi lainnya
   );
   ```

4. **Buat Lock File**
   
   Buat file kosong: `application/config/installed.lock`

5. **Insert Data Awal**
   
   Jalankan query SQL untuk insert data aplikasi dan user admin secara manual

---

## 4. Fitur Utama

### 4.1 Dashboard

Dashboard adalah halaman utama yang muncul setelah login. Dashboard Super Admin memiliki berbagai fitur dan informasi penting:

#### Komponen Dashboard:

**1. Welcome Banner**
- Menampilkan nama aplikasi
- Menampilkan nama user yang sedang login
- Logo aplikasi (jika sudah diupload)
- Tombol quick access: "Ubah Profil" dan "Ubah Password"

**2. Summary Widgets**
- Widget ringkasan data yang dapat dikonfigurasi
- Menampilkan statistik penting (jumlah user, total penjualan, dll)
- Klik widget untuk melihat detail data
- Widget ditampilkan berdasarkan role permission

**3. Dynamic Charts**
- Grafik dinamis yang dapat dikonfigurasi
- Berbagai tipe chart (Line, Bar, Pie, Doughnut, Area)
- Filter dinamis untuk analisis data
- Chart ditampilkan berdasarkan role permission

**4. System & Maintenance Panel**
- Kontrol maintenance mode
- Pengaturan environment mode
- Informasi sistem real-time

**5. System Information**
- Timezone dan waktu server
- Versi PHP dan CodeIgniter
- Informasi environment

**6. Internet Speed Test**
- Test kecepatan internet real-time
- Powered by OpenSpeedtest
- Deteksi koneksi offline otomatis

**7. Network Tools**
- Tools diagnostik jaringan
- Ping, Netstat, Traceroute, NSLookup, IPConfig
- Public IP address display
- Terminal output real-time

---

## 5. Fitur Dashboard Super Admin (Detail)

### 5.1 Maintenance Mode

**Apa itu Maintenance Mode?**

Maintenance Mode adalah fitur untuk membatasi akses aplikasi saat melakukan pemeliharaan atau update. Saat mode ini aktif:
- ✅ **Super Admin** tetap dapat mengakses aplikasi
- ❌ **User biasa** akan melihat halaman maintenance
- ⚠️ Cocok digunakan saat update aplikasi atau database

#### Cara Mengaktifkan:

1. Di dashboard, cari panel **"System & Maintenance"**
2. Lihat toggle switch **"Maintenance Mode"**
3. **Klik toggle** untuk mengubah status:
   - **OFF (INACTIVE)**: Aplikasi normal, semua user dapat akses
   - **ON (ACTIVE)**: Mode maintenance aktif, hanya Super Admin yang dapat akses
4. Status akan berubah otomatis dengan notifikasi

#### Status Indicator:

- **INACTIVE** (Abu-abu): Mode maintenance tidak aktif
- **ACTIVE** (Merah): Mode maintenance aktif

#### Kapan Menggunakan:

- ✅ Saat melakukan update database
- ✅ Saat melakukan maintenance server
- ✅ Saat melakukan perubahan besar pada aplikasi
- ✅ Saat troubleshooting masalah
- ❌ Jangan aktifkan saat jam kerja tanpa pemberitahuan

#### Tips:

- Informasikan user sebelum mengaktifkan maintenance mode
- Aktifkan di luar jam kerja jika memungkinkan
- Matikan segera setelah maintenance selesai
- Super Admin tetap dapat bekerja normal saat mode aktif

---

### 5.2 Environment Mode

**Apa itu Environment Mode?**

Environment Mode menentukan bagaimana aplikasi menangani error dan debugging. Ada 3 mode:

#### 1. Development Mode 🔴

**Karakteristik:**
- Menampilkan **error detail** lengkap di browser
- Menampilkan **stack trace** untuk debugging
- Menampilkan **query database** yang dijalankan
- **Tidak ada caching** untuk development yang lebih cepat

**Kapan Digunakan:**
- ✅ Saat development/coding
- ✅ Saat debugging masalah
- ✅ Saat testing fitur baru
- ✅ Di local development environment

**Peringatan:**
- ❌ **JANGAN** gunakan di production server
- ❌ Error detail dapat mengekspos informasi sensitif
- ❌ Dapat menampilkan struktur database dan code

#### 2. Testing Mode 🟡

**Karakteristik:**
- Error ditampilkan dengan **detail sedang**
- Cocok untuk **testing environment**
- Beberapa debugging masih aktif
- Performa lebih baik dari development

**Kapan Digunakan:**
- ✅ Saat testing di staging server
- ✅ Saat QA testing
- ✅ Saat UAT (User Acceptance Testing)

#### 3. Production Mode 🔵

**Karakteristik:**
- Error **disembunyikan** dari user
- Hanya menampilkan **error message umum**
- **Performa optimal** dengan caching
- Error detail hanya di log file

**Kapan Digunakan:**
- ✅ **WAJIB** untuk live/production server
- ✅ Saat aplikasi sudah digunakan user
- ✅ Untuk keamanan maksimal

**Keuntungan:**
- ✅ Menyembunyikan informasi sensitif
- ✅ Performa lebih cepat
- ✅ Lebih aman dari hacker
- ✅ User experience lebih baik

#### Cara Mengubah Environment:

1. Di dashboard, cari panel **"System & Maintenance"**
2. Lihat bagian **"Environment Mode"**
3. Pilih environment dari dropdown:
   - Development
   - Testing
   - Production
4. Konfirmasi perubahan di dialog
5. Halaman akan **reload otomatis**

#### Status Badge:

- **DEVELOPMENT** (Badge Merah): Mode development aktif
- **TESTING** (Badge Kuning): Mode testing aktif
- **PRODUCTION** (Badge Biru): Mode production aktif

#### Best Practice:

| Environment | Server | Error Display | Caching | Security |
|-------------|--------|---------------|---------|----------|
| Development | Local | Full Detail | OFF | Low |
| Testing | Staging | Medium Detail | Partial | Medium |
| Production | Live | Hidden | ON | High |

**Rekomendasi:**
- Local development: **Development**
- Staging server: **Testing**
- Live server: **Production** (WAJIB!)

---

### 5.3 System Information

Panel System Information menampilkan informasi penting tentang server dan aplikasi.

#### Informasi yang Ditampilkan:

**1. Default Timezone**
- Menampilkan timezone yang digunakan aplikasi
- Format: Asia/Jakarta, Asia/Makassar, dll
- Menampilkan offset UTC (contoh: UTC+07:00)
- Badge: "Default Timezone"

**Fungsi:**
- Menentukan waktu yang digunakan untuk timestamp
- Mempengaruhi fungsi date() dan time()
- Penting untuk konsistensi waktu di seluruh aplikasi

**2. Server Time**
- Menampilkan **waktu server real-time**
- Format: HH:MM:SS (contoh: 14:30:45)
- Menampilkan **tanggal lengkap**
- Format: Day, DD Month YYYY
- Badge: "Server Time"
- **Auto-update** setiap detik

**3. PHP & Framework Version**
- Menampilkan **versi PHP** yang digunakan
- Menampilkan **versi CodeIgniter**
- Badge: "Framework"

---

### 5.4 Internet Speed Test

**Apa itu Speed Test?**

Fitur untuk mengukur kecepatan internet server secara real-time menggunakan OpenSpeedtest.

#### Cara Menggunakan:

1. Scroll ke bagian **"Internet Speed Test"**
2. Widget akan load otomatis
3. Klik **"Start"** di widget OpenSpeedtest
4. Tunggu proses testing selesai (30-60 detik)
5. Lihat hasil: Download speed, Upload speed, Ping, Jitter

---

### 5.5 Network Tools

**Apa itu Network Tools?**

Kumpulan tools diagnostik jaringan untuk troubleshooting dan monitoring koneksi.

#### Tools yang Tersedia:

**1. Ping** - Test konektivitas ke host tujuan
**2. Netstat** - Menampilkan koneksi jaringan aktif
**3. Traceroute** - Melacak jalur paket ke tujuan
**4. NSLookup** - Query DNS records
**5. IPConfig** - Menampilkan konfigurasi IP server

---

### 4.2 Role-Based Access Control (RBAC)

Sistem RBAC yang lengkap dengan:

- **Roles**: Definisi peran pengguna (Super Admin, Manager, Staff, dll)
- **Modules**: Modul-modul aplikasi
- **Permissions**: Hak akses spesifik (view, create, edit, delete)
- **Role-Permission Assignment**: Mengatur permission untuk setiap role
- **User-Role Assignment**: Mengatur role untuk setiap user
- **Audit Log**: Pencatatan aktivitas RBAC

### 4.3 CRUD Generator

Generator otomatis untuk membuat:

- **Controller**: File controller dengan fungsi CRUD lengkap
- **Model**: File model untuk akses database
- **Views**: Halaman index, form tambah/edit
- **Konfigurasi Field**: Tipe input, validasi, relasi tabel
- **Export**: Excel dan PDF
- **Notification**: Toast, SweetAlert, atau Alert biasa

### 4.4 Chart Generator

Pembuat grafik dinamis dengan fitur:

- **Tipe Chart**: Line, Bar, Pie, Doughnut, Area
- **Multiple Series**: Mendukung beberapa series data
- **Filter Dinamis**: Filter berdasarkan text, date, month/year, year
- **Role-Based Access**: Chart hanya tampil untuk role tertentu
- **Placement**: Dashboard atau halaman khusus

### 4.5 Summary Widget

Widget ringkasan data dengan:

- **Fungsi Agregasi**: COUNT, SUM, AVG, MIN, MAX
- **Format Tampilan**: Rupiah, Number, atau default
- **Background Color**: Berbagai pilihan warna
- **Role-Based Access**: Widget hanya tampil untuk role tertentu
- **Detail Modal**: Klik widget untuk melihat detail data

### 4.6 Database Management

Manajemen database melalui web:

- **Table Generator**: Membuat tabel baru dengan GUI
- **View Tables**: Melihat struktur tabel
- **Backup Database**: Download backup database dalam format SQL

---

## 6. Panduan Pengguna

### 6.1 Login

1. Akses URL aplikasi
2. Masukkan **Email** dan **Password**
3. Klik **"Login"**
4. Anda akan diarahkan ke dashboard sesuai role Anda

### 6.2 Dashboard

Setelah login, Anda akan melihat:

- **Header**: Nama aplikasi, notifikasi, profil user
- **Sidebar**: Menu navigasi sesuai permission role Anda
- **Content Area**: Konten utama (statistik, widget, chart)

### 6.3 Profil User

#### Edit Profil

1. Klik **foto profil** di header
2. Pilih **"Edit Profile"**
3. Ubah **Nama** atau **Foto Profil**
4. Klik **"Save Changes"**

#### Ubah Password

1. Klik **foto profil** di header
2. Pilih **"Change Password"**
3. Masukkan:
   - **Current Password**: Password lama
   - **New Password**: Password baru (minimal 3 karakter)
   - **Confirm Password**: Konfirmasi password baru
4. Klik **"Change Password"**

### 6.4 Logout

1. Klik **foto profil** di header
2. Pilih **"Logout"**

---

## 7. Manajemen RBAC

> **Catatan**: Fitur ini hanya tersedia untuk **Super Admin**

### 6.1 Manajemen Roles

#### Melihat Daftar Role

1. Buka menu **RBAC Management** → **Roles**
2. Anda akan melihat tabel berisi semua role

#### Menambah Role Baru

1. Klik tombol **"Add New Role"**
2. Isi form:
   - **Role Name**: Nama role (contoh: "Manager", "Staff")
   - **Description**: Deskripsi role
   - **Status**: Active/Inactive
3. Klik **"Save"**
4. Role baru akan otomatis dibuatkan:
   - Controller file
   - Sidebar file
   - View folder

#### Mengedit Role

1. Klik tombol **"Edit"** pada role yang ingin diubah
2. Ubah informasi yang diperlukan
3. Klik **"Update"**

#### Menghapus Role

1. Klik tombol **"Delete"** pada role yang ingin dihapus
2. Konfirmasi penghapusan
3. **Perhatian**: File controller, sidebar, dan view akan ikut terhapus

### 6.2 Manajemen Modules

#### Melihat Daftar Module

1. Buka menu **RBAC Management** → **Modules**
2. Anda akan melihat daftar modul dalam struktur tree

#### Menambah Module

1. Klik tombol **"Add Module"**
2. Isi form:
   - **Module Name**: Nama modul (contoh: "Laporan Penjualan")
   - **Description**: Deskripsi modul
   - **Controller Name**: Nama controller (contoh: "laporan_penjualan")
   - **Icon**: Icon Font Awesome (contoh: "fa fa-chart-line")
   - **Parent Module**: Pilih parent jika submenu (opsional)
   - **Parent Menu**: Pilih parent menu untuk grouping (opsional)
   - **Sort Order**: Urutan tampilan
   - **Status**: Active/Inactive
3. Klik **"Save"**

#### Mengedit Module

1. Klik tombol **"Edit"** pada module yang ingin diubah
2. Ubah informasi yang diperlukan
3. Klik **"Update"**

#### Menghapus Module

1. Klik tombol **"Delete"** pada module yang ingin dihapus
2. Konfirmasi penghapusan
3. **Perhatian**: Permission yang terkait akan ikut terhapus

### 6.3 Manajemen Permissions

#### Melihat Daftar Permission

1. Buka menu **RBAC Management** → **Permissions**
2. Anda akan melihat daftar permission dikelompokkan per module

#### Menambah Permission

1. Klik tombol **"Add Permission"**
2. Isi form:
   - **Permission Name**: Nama permission (contoh: "View Reports")
   - **Permission Key**: Key unik (contoh: "reports.view")
   - **Description**: Deskripsi permission
   - **Module**: Pilih module terkait
   - **Status**: Active/Inactive
3. Klik **"Save"**

#### Mengedit Permission

1. Klik tombol **"Edit"** pada permission yang ingin diubah
2. Ubah informasi yang diperlukan
3. Klik **"Update"**

#### Menghapus Permission

1. Klik tombol **"Delete"** pada permission yang ingin dihapus
2. Konfirmasi penghapusan

### 6.4 Assign Permissions to Role

#### Mengatur Permission untuk Role

1. Buka menu **RBAC Management** → **Assign Permissions**
2. Pilih **Role** dari dropdown
3. Centang **permission** yang ingin diberikan ke role tersebut
4. Permission dikelompokkan berdasarkan module
5. Klik **"Save Permissions"**

**Tips:**
- Gunakan checkbox **"Select All"** untuk memilih semua permission dalam satu module
- Permission yang sudah di-assign akan otomatis tercentang saat membuka halaman

### 6.5 Assign Roles to User

#### Mengatur Role untuk User

1. Buka menu **RBAC Management** → **Assign User Roles**
2. Pilih **User** dari dropdown
3. Centang **role** yang ingin diberikan ke user tersebut
4. Satu user dapat memiliki **multiple roles**
5. Klik **"Save Roles"**

**Catatan:**
- User dengan multiple roles akan memiliki gabungan permission dari semua rolenya
- Sidebar menu akan menampilkan semua menu yang diizinkan dari semua role

### 6.6 Parent Menus

#### Mengelola Parent Menu

Parent menu digunakan untuk mengelompokkan beberapa module dalam satu menu dropdown.

1. Buka menu **RBAC Management** → **Parent Menus**
2. Klik **"Add Parent Menu"** untuk menambah
3. Isi form:
   - **Menu Name**: Nama menu (contoh: "System Tools")
   - **Icon**: Icon Font Awesome
   - **Sort Order**: Urutan tampilan
   - **Status**: Active/Inactive
4. Klik **"Save"**

**Penggunaan:**
- Saat membuat module, pilih parent menu yang sesuai
- Module dengan parent menu yang sama akan dikelompokkan dalam satu dropdown

---

## 8. CRUD Generator

> **Catatan**: Fitur ini hanya tersedia untuk **Super Admin**

### 7.1 Menggunakan CRUD Generator

#### Langkah-langkah:

1. Buka menu **Table Generator** atau **CRUD Generator**
2. Pilih **tabel** dari dropdown (tabel harus sudah ada di database)
3. Klik **"Generate CRUD"**

#### Konfigurasi CRUD:

**Basic Configuration:**
- **Controller Name**: Nama controller yang akan dibuat
- **Model Name**: Nama model yang akan dibuat
- **View Directory**: Direktori view (otomatis dari nama tabel)
- **Notification Type**: Pilih tipe notifikasi (Toast/SweetAlert/Alert)

**Field Configuration:**

Untuk setiap field dalam tabel, Anda dapat mengatur:

1. **Display in List**: Tampilkan di tabel list (Ya/Tidak)
2. **Display in Form**: Tampilkan di form add/edit (Ya/Tidak)
3. **Input Type**: Tipe input
   - Text
   - Textarea
   - Number
   - Email
   - Date
   - Select (Dropdown)
   - File Upload
   - Hidden
4. **Validation Rules**: Aturan validasi
   - Required
   - Numeric
   - Email
   - Min Length
   - Max Length
   - dll
5. **Relation**: Jika tipe Select, tentukan:
   - **Related Table**: Tabel relasi
   - **Display Field**: Field yang ditampilkan
   - **Value Field**: Field yang disimpan

#### Generate:

1. Setelah konfigurasi selesai, klik **"Generate CRUD"**
2. Sistem akan membuat:
   - `application/controllers/NamaController.php`
   - `application/models/NamaModel.php`
   - `application/views/nama_tabel/index.php`
   - `application/views/nama_tabel/form.php`
3. CRUD history akan tersimpan di tabel `tbl_crud_history`

### 7.2 Fitur CRUD yang Dihasilkan

CRUD yang dihasilkan memiliki fitur:

- ✅ **List Data**: Tabel dengan pagination, search, dan sorting
- ✅ **Add Data**: Form tambah data dengan validasi
- ✅ **Edit Data**: Form edit data
- ✅ **Delete Data**: Hapus data dengan konfirmasi
- ✅ **Export Excel**: Export data ke Excel
- ✅ **Export PDF**: Export data ke PDF
- ✅ **File Upload**: Jika ada field file
- ✅ **Relational Data**: Dropdown dari tabel lain
- ✅ **Notification**: Toast/SweetAlert/Alert sesuai pilihan

### 7.3 CRUD History

#### Melihat Riwayat CRUD

1. Buka menu **CRUD Generator** → **History**
2. Anda akan melihat daftar CRUD yang pernah di-generate:
   - Table Name
   - Controller Name
   - Model Name
   - Generated At
   - Generated By
   - Notification Type

#### Regenerate CRUD

1. Klik tombol **"Regenerate"** pada history yang diinginkan
2. Sistem akan membuat ulang CRUD dengan konfigurasi yang sama

#### Delete CRUD

1. Klik tombol **"Delete"** pada history yang diinginkan
2. Konfirmasi penghapusan
3. File controller, model, dan view akan dihapus

---

## 9. Chart Generator

> **Catatan**: Fitur ini tersedia sesuai permission role

### 8.1 Membuat Chart Baru

#### Langkah-langkah:

1. Buka menu **Chart Generator**
2. Klik tombol **"Create New Chart"**
3. Isi form konfigurasi chart:

**Basic Configuration:**
- **Chart Title**: Judul chart (contoh: "Penjualan Bulanan 2024")
- **Chart Type**: Pilih tipe chart
  - Line Chart
  - Bar Chart
  - Pie Chart
  - Doughnut Chart
  - Area Chart
- **Placement**: Lokasi tampilan chart
  - Dashboard
  - Custom page (masukkan identifier)
- **Allowed Roles**: Pilih role yang dapat melihat chart (multiple select)
- **Status**: Active/Inactive

**Data Configuration:**

Untuk setiap series data:

1. **Series Label**: Label series (contoh: "Produk A")
2. **Table Name**: Pilih tabel sumber data
3. **Label Column**: Kolom untuk label sumbu X (contoh: "bulan")
4. **Value Column**: Kolom untuk nilai sumbu Y (contoh: "total_penjualan")
5. **Aggregate Function**: Fungsi agregasi
   - COUNT
   - SUM
   - AVG
   - MIN
   - MAX
6. **Group By**: Kolom untuk grouping data
7. **Color**: Warna series (hex color picker)

**Filter Configuration (Opsional):**

Tambahkan filter dinamis:

1. Klik **"Add Filter"**
2. Pilih **Column** yang akan difilter
3. Pilih **Filter Type**:
   - Text (input text)
   - Date (date picker)
   - Month/Year (month picker)
   - Year (year picker)
4. Anda dapat menambahkan multiple filter

#### Save Chart:

1. Klik **"Save Chart"**
2. Chart akan tersimpan dan dapat dilihat di placement yang dipilih

### 8.2 Melihat Chart

#### Di Dashboard:

- Chart dengan placement "dashboard" akan otomatis tampil di dashboard
- Hanya tampil jika role user termasuk dalam allowed roles

#### Advanced Menu (Filter):

1. Klik icon **"Advanced Menu"** di pojok kanan atas chart
2. Isi filter yang tersedia
3. Klik **"Apply Filter"**
4. Chart akan update sesuai filter

### 8.3 Mengedit Chart

1. Buka menu **Chart Generator**
2. Klik tombol **"Edit"** pada chart yang ingin diubah
3. Ubah konfigurasi yang diperlukan
4. Klik **"Update Chart"**

### 8.4 Menghapus Chart

1. Buka menu **Chart Generator**
2. Klik tombol **"Delete"** pada chart yang ingin dihapus
3. Konfirmasi penghapusan

---

## 10. Summary Widget

> **Catatan**: Fitur ini tersedia sesuai permission role

### 9.1 Membuat Widget Baru

#### Langkah-langkah:

1. Buka menu **Summary Widget**
2. Klik tombol **"Create New Widget"**
3. Isi form konfigurasi widget:

**Widget Configuration:**
- **Title**: Judul widget (contoh: "Total Penjualan")
- **Table Name**: Pilih tabel sumber data
- **Column Name**: Pilih kolom yang akan diagregasi
- **Aggregate Function**: Pilih fungsi agregasi
  - COUNT: Hitung jumlah record
  - SUM: Jumlahkan nilai
  - AVG: Rata-rata nilai
  - MIN: Nilai minimum
  - MAX: Nilai maksimum
- **Background Color**: Pilih warna background widget
  - Navy Blue
  - Green
  - Yellow
  - Red
  - Purple
  - Teal
  - Orange
  - Maroon
- **Formatting**: Format tampilan nilai
  - None (default)
  - Rupiah (Rp 1.000.000)
  - Number (1,000,000)
- **Placement**: Lokasi tampilan widget
  - Dashboard
  - Custom page
- **Allowed Roles**: Pilih role yang dapat melihat widget
- **Status**: Active/Inactive

#### Save Widget:

1. Klik **"Save Widget"**
2. Widget akan tersimpan dan tampil di placement yang dipilih

### 9.2 Melihat Widget

#### Di Dashboard:

- Widget dengan placement "dashboard" akan otomatis tampil di dashboard
- Tampil dalam bentuk kotak dengan:
  - Icon
  - Nilai hasil agregasi
  - Judul widget

#### Detail Data:

1. **Klik** pada widget
2. Modal akan muncul menampilkan **detail data**
3. Detail data menampilkan:
   - Tabel dengan semua record yang dihitung
   - Pagination
   - Sesuai dengan fungsi agregasi yang dipilih

**Contoh:**
- Widget "Total Penjualan" dengan SUM → Modal menampilkan semua transaksi penjualan
- Widget "Jumlah User" dengan COUNT → Modal menampilkan semua user

### 9.3 Mengedit Widget

1. Buka menu **Summary Widget**
2. Klik tombol **"Edit"** pada widget yang ingin diubah
3. Ubah konfigurasi yang diperlukan
4. Klik **"Update Widget"**

### 9.4 Menghapus Widget

1. Buka menu **Summary Widget**
2. Klik tombol **"Delete"** pada widget yang ingin dihapus
3. Konfirmasi penghapusan

---

## 11. Database Management

> **Catatan**: Fitur ini hanya tersedia untuk **Super Admin**

### 10.1 Table Generator

#### Membuat Tabel Baru:

1. Buka menu **Table Generator** → **Database Manager**
2. Klik **"Create New Table"**
3. Isi form:
   - **Table Name**: Nama tabel (contoh: "tbl_produk")
   - **Engine**: InnoDB (recommended)
   - **Charset**: utf8mb4 (recommended)

#### Menambah Field:

1. Klik **"Add Field"**
2. Untuk setiap field, isi:
   - **Field Name**: Nama kolom (contoh: "nama_produk")
   - **Data Type**: Tipe data
     - INT
     - VARCHAR
     - TEXT
     - DATE
     - DATETIME
     - DECIMAL
     - dll
   - **Length**: Panjang/ukuran (contoh: 255 untuk VARCHAR)
   - **Default Value**: Nilai default (opsional)
   - **Null**: Allow NULL? (Yes/No)
   - **Auto Increment**: Ya/Tidak
   - **Primary Key**: Ya/Tidak
   - **Unique**: Ya/Tidak
   - **Index**: Ya/Tidak

3. Tambahkan field sebanyak yang diperlukan
4. Klik **"Create Table"**

### 10.2 View Tables

#### Melihat Struktur Tabel:

1. Buka menu **Table Generator** → **View Tables**
2. Pilih tabel dari dropdown
3. Anda akan melihat:
   - Nama tabel
   - Jumlah kolom
   - Struktur kolom (nama, tipe, length, null, key)
   - Jumlah record

#### Modify Table:

1. Klik **"Modify Table"**
2. Anda dapat:
   - Menambah kolom baru
   - Mengubah kolom existing
   - Menghapus kolom
3. Klik **"Save Changes"**

### 10.3 Backup Database

#### Download Backup:

1. Buka menu **Database Management** → **Backup**
2. Klik tombol **"Backup Database"**
3. File SQL akan otomatis terdownload dengan format:
   ```
   backupdb_gusananta_YYYYMMDD-HHMMSS.sql
   ```
4. Backup juga tersimpan di folder `assets/backupdb/`

#### Restore Backup:

1. Gunakan phpMyAdmin atau MySQL command line
2. Import file SQL backup:
   ```bash
   mysql -u username -p database_name < backup_file.sql
   ```

---

## 12. Pengaturan Aplikasi

> **Catatan**: Fitur ini hanya tersedia untuk **Super Admin**

### 11.1 Identitas Aplikasi

#### Mengubah Identitas:

1. Buka menu **Setup Aplikasi** atau **Application Settings**
2. Anda dapat mengubah:
   - **Nama Aplikasi**
   - **Alamat**
   - **Telepon**
   - **Nama Developer**
   - **Logo**: Upload logo baru
   - **Title Icon**: Upload favicon baru

3. Klik **"Save Changes"**

### 11.2 Skin/Theme

#### Mengubah Tema:

1. Di dashboard, cari **"Change Skin"** atau **"Theme Settings"**
2. Pilih skin yang diinginkan:
   - Skin 1 (Blue)
   - Skin 2 (Green)
   - Skin 3 (Purple)
   - Skin 4 (Red)
   - MD Skin (Material Design)
3. Klik **"Apply"**
4. Halaman akan reload dengan tema baru

### 11.3 Environment Mode

#### Mengubah Environment:

1. Di dashboard, cari **"Environment Settings"**
2. Pilih environment:
   - **Development**: Menampilkan error detail (untuk development)
   - **Testing**: Mode testing
   - **Production**: Menyembunyikan error (untuk live server)
3. Klik **"Change Environment"**
4. **Reload halaman** untuk menerapkan perubahan

**Perhatian:**
- Gunakan **Production** mode saat aplikasi sudah live
- **Development** mode hanya untuk development

### 11.4 Maintenance Mode

#### Mengaktifkan Maintenance Mode:

1. Di dashboard, cari **"Maintenance Mode"**
2. Toggle switch ke **ON**
3. Aplikasi akan menampilkan halaman maintenance untuk user biasa
4. **Super Admin** tetap dapat akses aplikasi

#### Menonaktifkan Maintenance Mode:

1. Toggle switch ke **OFF**
2. Aplikasi kembali normal

### 11.5 Session Timeout

#### Mengatur Session Timeout:

1. Buka **Application Settings**
2. Ubah **Session Timeout** (dalam detik)
   - Default: 300 detik (5 menit)
   - Recommended: 1800 detik (30 menit)
3. Klik **"Save"**

### 11.6 Timezone

#### Mengatur Timezone:

1. Buka **Application Settings**
2. Pilih **Default Timezone** dari dropdown
   - Asia/Jakarta
   - Asia/Makassar
   - Asia/Jayapura
   - dll
3. Klik **"Save"**

---

## 13. Management User

> **Catatan**: Fitur ini hanya tersedia untuk **Super Admin**

### 12.1 Melihat Daftar User

1. Buka menu **Management User**
2. Anda akan melihat tabel berisi:
   - Nama
   - NIK
   - Email
   - Level/Role
   - Status (Aktif/Tidak Aktif)
   - Date Created
   - Action

### 12.2 Menambah User Baru

1. Klik tombol **"Add New User"**
2. Isi form:
   - **Nama Lengkap**
   - **NIK**: Nomor Induk Kepegawaian
   - **Email**: Email untuk login (harus unique)
   - **Password**: Minimal 3 karakter
   - **Confirm Password**: Konfirmasi password
   - **Level**: Pilih level (untuk backward compatibility)
   - **Roles**: Pilih role (dapat multiple)
   - **Status**: Active/Inactive
3. Klik **"Save"**

### 12.3 Mengedit User

1. Klik tombol **"Edit"** pada user yang ingin diubah
2. Ubah informasi yang diperlukan
3. **Catatan**: Email tidak dapat diubah jika sudah ada
4. Klik **"Update"**

### 12.4 Mengubah Status User

#### Toggle Status:

1. Klik tombol **"Toggle Status"** pada user
2. Status akan berubah:
   - Aktif → Tidak Aktif
   - Tidak Aktif → Aktif
3. User yang tidak aktif tidak dapat login

#### Disable User (dengan Secret Key):

1. Klik tombol **"Disable"** pada user
2. Masukkan **Uninstall Secret Key**
3. Klik **"Confirm"**
4. User akan di-disable

### 12.5 Menghapus User

**Perhatian**: Fitur delete user tidak tersedia untuk mencegah kehilangan data. Gunakan **Disable** sebagai gantinya.

---

## 14. Uninstall Aplikasi

> **PERINGATAN**: Proses ini akan menghapus SEMUA data dan file yang di-generate!

### 13.1 Proses Uninstall

1. Login sebagai **Super Admin**
2. Buka **Application Settings** atau **Dashboard**
3. Cari tombol **"Uninstall Application"**
4. Masukkan **Uninstall Secret Key** yang dibuat saat instalasi
5. Klik **"Uninstall"**
6. Konfirmasi sekali lagi

### 13.2 Apa yang Dihapus?

Proses uninstall akan menghapus:

1. **Database**: Seluruh database akan di-drop
2. **Generated Files**:
   - Controller role yang di-generate
   - Sidebar role yang di-generate
   - View folder role yang di-generate
   - CRUD controller yang di-generate
   - CRUD model yang di-generate
   - CRUD view yang di-generate
3. **Uploaded Files**:
   - Logo aplikasi
   - Favicon
   - Foto profil user (kecuali default.png)
4. **Lock File**: `application/config/installed.lock`
5. **Session**: Session user akan di-destroy

### 13.3 Setelah Uninstall

- Aplikasi akan kembali ke halaman installer
- Anda dapat melakukan instalasi ulang dari awal
- File core aplikasi (framework, libraries) tetap ada

---

## 15. Troubleshooting

### 15.1 Masalah Instalasi

#### Error: "Database connection failed"

**Solusi:**
- Pastikan MySQL service berjalan
- Periksa username dan password MySQL
- Periksa hostname (gunakan `localhost` atau `127.0.0.1`)
- Pastikan user MySQL memiliki privilege untuk create database

#### Error: "Permission denied" saat upload

**Solusi:**
- Pastikan folder berikut memiliki write permission:
  ```
  application/cache/
  application/logs/
  assets/images/
  assets/dist/img/profile/
  assets/backupdb/
  ```
- Di Linux/Mac: `chmod -R 777 folder_name`
- Di Windows: Klik kanan folder → Properties → Security → Edit → Allow Full Control

#### Error: "404 Page Not Found" setelah instalasi

**Solusi:**
- Pastikan `mod_rewrite` Apache sudah aktif
- Periksa file `.htaccess` di root folder
- Periksa konfigurasi `base_url` di `application/config/config.php`

### 15.2 Masalah Login

#### Error: "Invalid email or password"

**Solusi:**
- Pastikan email dan password benar
- Cek caps lock keyboard
- Reset password melalui database jika lupa:
  ```sql
  UPDATE mst_user SET password = '$2y$10$...' WHERE email = 'admin@example.com';
  ```
  (Generate password hash menggunakan PHP `password_hash()`)

#### Error: "Session expired" terus menerus

**Solusi:**
- Periksa session timeout di application settings
- Pastikan folder `application/cache/` writable
- Periksa konfigurasi session di `application/config/config.php`
- Clear browser cache dan cookies

### 15.3 Masalah RBAC

#### Menu tidak muncul setelah assign permission

**Solusi:**
- Logout dan login kembali
- Pastikan permission sudah di-assign ke role
- Pastikan role sudah di-assign ke user
- Periksa module status (harus active)
- Periksa permission status (harus active)

#### Error: "Access Denied"

**Solusi:**
- Pastikan user memiliki permission yang sesuai
- Periksa role user di management user
- Periksa permission yang di-assign ke role
- Logout dan login kembali

### 15.4 Masalah CRUD Generator

#### Error saat generate CRUD

**Solusi:**
- Pastikan tabel sudah ada di database
- Pastikan folder `application/controllers/`, `application/models/`, `application/views/` writable
- Periksa nama controller dan model (harus valid PHP class name)
- Periksa log error di `application/logs/`

#### CRUD yang di-generate tidak berfungsi

**Solusi:**
- Periksa apakah file controller, model, view sudah ter-generate
- Periksa permission untuk mengakses CRUD tersebut
- Periksa routing di `application/config/routes.php`
- Clear cache browser

### 15.5 Masalah Chart Generator

#### Chart tidak tampil

**Solusi:**
- Pastikan chart status = Active
- Pastikan role user termasuk dalam allowed roles
- Pastikan placement sesuai (dashboard/custom)
- Periksa console browser untuk error JavaScript
- Pastikan data source (tabel dan kolom) ada dan valid

#### Chart kosong/tidak ada data

**Solusi:**
- Periksa apakah tabel memiliki data
- Periksa konfigurasi kolom label dan value
- Periksa aggregate function yang digunakan
- Periksa filter yang diterapkan

### 15.6 Masalah Summary Widget

#### Widget tidak tampil

**Solusi:**
- Pastikan widget status = Active
- Pastikan role user termasuk dalam allowed roles
- Pastikan placement sesuai
- Logout dan login kembali

#### Widget menampilkan nilai 0

**Solusi:**
- Periksa apakah tabel memiliki data
- Periksa kolom yang dipilih untuk agregasi
- Periksa aggregate function yang digunakan
- Periksa apakah ada filter WHERE yang membatasi data

#### Modal detail tidak muncul saat klik widget

**Solusi:**
- Periksa console browser untuk error JavaScript
- Pastikan jQuery dan Bootstrap JS sudah loaded
- Clear cache browser
- Periksa apakah ada conflict JavaScript

### 15.7 Masalah Database Management

#### Error saat backup database

**Solusi:**
- Pastikan folder `assets/backupdb/` writable
- Pastikan user MySQL memiliki privilege SELECT
- Periksa ukuran database (jika terlalu besar, gunakan phpMyAdmin)

#### Error saat create table

**Solusi:**
- Pastikan user MySQL memiliki privilege CREATE TABLE
- Periksa nama tabel (harus unique, tidak boleh ada spasi)
- Periksa konfigurasi field (tipe data, length, dll)

### 15.8 Masalah Performance

#### Aplikasi lambat

**Solusi:**
- Enable cache di CodeIgniter
- Optimize database (index, query optimization)
- Gunakan pagination untuk data banyak
- Minimize JavaScript dan CSS
- Gunakan CDN untuk library eksternal
- Upgrade server (RAM, CPU)

#### Database query lambat

**Solusi:**
- Tambahkan index pada kolom yang sering di-query
- Optimize query (gunakan EXPLAIN)
- Batasi jumlah JOIN
- Gunakan pagination
- Archive data lama

### 15.9 Masalah Umum

#### Error: "Blank page" atau "White screen"

**Solusi:**
- Set environment ke development untuk melihat error
- Periksa log error di `application/logs/`
- Periksa PHP error log
- Pastikan semua library dan helper sudah loaded
- Periksa syntax error di file PHP

#### Error: "CSRF token mismatch"

**Solusi:**
- Reload halaman
- Clear browser cache dan cookies
- Periksa konfigurasi CSRF di `application/config/config.php`
- Pastikan form memiliki CSRF token

#### Error: "Upload file failed"

**Solusi:**
- Periksa `upload_max_filesize` dan `post_max_size` di `php.ini`
- Pastikan folder upload writable
- Periksa ekstensi file yang diizinkan
- Periksa ukuran file (jangan melebihi max size)

---

## 16. Tips & Best Practices

### 16.1 Keamanan

1. **Gunakan Production Mode** saat aplikasi sudah live
2. **Ubah Default Password** admin setelah instalasi
3. **Backup Database** secara berkala
4. **Simpan Uninstall Secret Key** di tempat aman
5. **Gunakan HTTPS** untuk aplikasi production
6. **Update CodeIgniter** ke versi terbaru secara berkala
7. **Validasi Input** di semua form
8. **Gunakan Prepared Statement** untuk query database
9. **Limit Login Attempts** untuk mencegah brute force
10. **Enable CSRF Protection**

### 16.2 Performance

1. **Enable Cache** untuk query yang sering digunakan
2. **Gunakan Index** pada kolom yang sering di-query
3. **Optimize Images** sebelum upload
4. **Minimize JavaScript dan CSS**
5. **Gunakan Pagination** untuk data banyak
6. **Archive Data Lama** secara berkala
7. **Monitor Server Resources** (CPU, RAM, Disk)

### 16.3 Development

1. **Gunakan Version Control** (Git)
2. **Buat Backup** sebelum update besar
3. **Test di Development** sebelum deploy ke production
4. **Dokumentasikan Perubahan**
5. **Gunakan Naming Convention** yang konsisten
6. **Comment Code** yang kompleks
7. **Follow CodeIgniter Best Practices**

### 16.4 RBAC

1. **Buat Role** sesuai kebutuhan organisasi
2. **Assign Permission** dengan prinsip least privilege
3. **Review Permission** secara berkala
4. **Gunakan Audit Log** untuk tracking perubahan
5. **Test Permission** setelah assign
6. **Dokumentasikan Role** dan permission

### 16.5 CRUD Generator

1. **Periksa Struktur Tabel** sebelum generate
2. **Konfigurasi Field** dengan benar
3. **Test CRUD** setelah generate
4. **Customize** sesuai kebutuhan
5. **Backup** sebelum regenerate
6. **Simpan History** untuk referensi

---

## 17. FAQ (Frequently Asked Questions)

### Q1: Apakah aplikasi ini gratis?

**A:** Ya, aplikasi ini adalah starter kit open source berbasis CodeIgniter 3.

### Q2: Apakah bisa digunakan untuk aplikasi komersial?

**A:** Ya, Anda dapat menggunakan aplikasi ini untuk proyek komersial. Pastikan mematuhi lisensi CodeIgniter.

### Q3: Apakah support multi-language?

**A:** Saat ini aplikasi menggunakan Bahasa Indonesia. Anda dapat menambahkan multi-language dengan menggunakan CodeIgniter Language Library.

### Q4: Apakah bisa mengubah template/theme?

**A:** Ya, aplikasi menggunakan AdminLTE 3. Anda dapat mengubah skin atau mengganti dengan template lain.

### Q5: Bagaimana cara upgrade CodeIgniter?

**A:** 
1. Backup aplikasi dan database
2. Download CodeIgniter versi terbaru
3. Replace folder `system/` dengan yang baru
4. Periksa changelog untuk perubahan breaking
5. Test aplikasi secara menyeluruh

### Q6: Apakah support REST API?

**A:** Aplikasi ini belum include REST API. Anda dapat menambahkan dengan library seperti CodeIgniter REST Server.

### Q7: Bagaimana cara menambahkan fitur baru?

**A:** 
1. Buat module baru di RBAC
2. Buat permission untuk module tersebut
3. Buat controller, model, view secara manual atau gunakan CRUD Generator
4. Assign permission ke role yang sesuai

### Q8: Apakah bisa deploy di shared hosting?

**A:** Ya, pastikan shared hosting support PHP 5.6+ dan MySQL. Upload file via FTP dan import database.

### Q9: Bagaimana cara reset password admin jika lupa?

**A:** 
```sql
UPDATE mst_user 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE email = 'admin@example.com';
```
Password akan menjadi: `password`

### Q10: Apakah ada dokumentasi API?

**A:** Dokumentasi ini adalah dokumentasi utama. Untuk API documentation, Anda dapat menggunakan tools seperti Postman atau Swagger jika menambahkan REST API.

---

## 18. Kontak & Support

### 18.1 Informasi Developer

- **Developer**: [Nama Developer dari tbl_aplikasi]
- **Email**: [Email Support]
- **Website**: [Website Aplikasi]

### 18.2 Pelaporan Bug

Jika menemukan bug atau error:

1. Catat error message yang muncul
2. Catat langkah-langkah untuk reproduce bug
3. Screenshot jika memungkinkan
4. Kirim ke email support atau buat issue di repository

### 18.3 Request Fitur

Untuk request fitur baru:

1. Jelaskan fitur yang diinginkan
2. Jelaskan use case dan manfaatnya
3. Kirim ke email support

---

## 19. Changelog

### Version 1.0.0 (Initial Release)

**Features:**
- ✅ Web-based installer
- ✅ RBAC (Role-Based Access Control)
- ✅ CRUD Generator
- ✅ Chart Generator
- ✅ Summary Widget
- ✅ Database Management
- ✅ User Management
- ✅ Backup Database
- ✅ Multiple skin/theme
- ✅ Environment switcher
- ✅ Maintenance mode
- ✅ Session management
- ✅ Timezone configuration

---

## 20. Lisensi

Aplikasi ini menggunakan framework CodeIgniter 3 yang memiliki lisensi MIT.

**MIT License**

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

---

## 21. Penutup

Terima kasih telah menggunakan aplikasi Starter Kit CodeIgniter 3 dengan RBAC ini. Semoga aplikasi ini dapat membantu mempercepat pengembangan proyek Anda.

Jika ada pertanyaan, saran, atau kritik, jangan ragu untuk menghubungi kami.

**Selamat menggunakan! 🚀**

---

**© 2024 - Starter Kit CodeIgniter 3 with RBAC**
