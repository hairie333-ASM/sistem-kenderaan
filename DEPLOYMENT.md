# Panduan Deployment Sistem Pengurusan Kenderaan & Pemandu UPF

Panduan ini menerangkan langkah demi langkah untuk mendeploy aplikasi ini menggunakan gabungan:
1. **GitHub** (Penyimpanan Kod & CI/CD)
2. **Neon.tech** (Pangkalan Data Serverless PostgreSQL)
3. **Render.com** (Web Service Hosting berasaskan Docker / Native)

---

## 🌟 Ringkasan Senibina & Fail Disediakan

| Komponen | Servis | Catatan |
|---|---|---|
| **Kod Sumber** | GitHub | `.gitignore` & struktur fail sedia ada |
| **Pangkalan Data** | Neon (PostgreSQL) | Sokongan SSL `sslmode=require` & `DATABASE_URL` automatik |
| **Web Service** | Render (Docker) | `Dockerfile`, `docker/nginx.conf`, `docker/entrypoint.sh`, `render.yaml` |

Aplikasi ini menggunakan aset UI berasaskan CDN (Tailwind CSS, Font Awesome, Alpine.js), jadi tiada keperluan Node.js / Vite build step yang rumit semasa deployment.

---

## LANGKAH 1: Sediakan Pangkalan Data di Neon (PostgreSQL)

1. Pergi ke [https://neon.tech](https://neon.tech) dan daftar / log masuk akaun (Percuma / Free Tier).
2. Klik **Create Project**:
   - **Name**: `sistem-kenderaan` (atau pilihan anda)
   - **Postgres version**: `16` atau `17` (versi terkini)
   - **Region**: Pilih **Singapore (`ap-southeast-1`)** untuk kelajuan sambungan terbaik dari Malaysia.
3. Selepas projek dicipta, Neon akan memaparkan **Connection String**:
   - Pilih tab **Pooled connection** atau **Direct connection**.
   - Contoh format connection string:
     ```text
     postgresql://neondb_owner:npg_AbCdEf123456@ep-cool-butterfly-123456.ap-southeast-1.aws.neon.tech/neondb?sslmode=require
     ```
4. **Salin Connection String ini** kerana anda akan memasukkannya ke dalam tetapan Render nanti.

---

## LANGKAH 2: Tolak Kod Sumber ke GitHub

### Pilihan A: Sebagai Repository Baharu Khusus (Disyorkan)
Buka terminal pada komputer anda dan jalankan arahan berikut:

```bash
cd "/Users/asm/Documents/Test Antigravity Sep 2026/sistem kenderaan"

# Inisialisasi Git jika belum ada .git di folder ini
git init

# Tambah semua fail
git add .

# Buat commit pertama
git commit -m "Initial commit for Sistem Pengurusan Kenderaan UPF"

# Namakan branch utama sebagai main
git branch -M main

# Sambungkan ke repository GitHub anda (cipta repo baru di GitHub dahulu)
git remote add origin https://github.com/<username-github-anda>/sistem-kenderaan.git

# Tolak kod ke GitHub
git push -u origin main
```

### Pilihan B: Menggunakan Monorepo / Repo Sedia Ada
Sekiranya anda menggunakan repository induk (`asm-candidate-ranking`), pastikan anda menetapkan **Root Directory** kepada `sistem kenderaan` semasa mencipta Web Service di Render.

---

## LANGKAH 3: Deploy di Render (Web Service)

Anda boleh memilih salah satu kaedah di bawah:

### Kaedah 1: Menggunakan Render Blueprint (Paling Pantas & Automatik)
Aplikasi ini disertakan dengan fail `render.yaml`:
1. Log masuk ke akaun [https://render.com](https://render.com).
2. Di dashboard, klik menu **Blueprints** -> **New Blueprint Instance**.
3. Sambungkan akaun GitHub anda dan pilih repository `sistem-kenderaan`.
4. Render akan membaca `render.yaml` secara automatik.
5. Pada bahagian Environment Variables yang memerlukan input:
   - Masukkan **`DATABASE_URL`** dengan connection string Neon yang anda salin pada Langkah 1.
6. Klik **Apply**. Render akan membina Docker container, menyambung ke Neon, menjalankan migrasi database, memasukkan data awal (seeders), dan melancarkan web service.

---

### Kaedah 2: Setup Web Service Manual (Docker)
Sekiranya anda ingin membuat Web Service secara manual di dashboard Render:

1. Di Render Dashboard, klik butang **New +** -> **Web Service**.
2. Pilih repository GitHub anda.
3. Tetapkan maklumat berikut:
   - **Name**: `sistem-kenderaan-asm` (atau apa-apa nama unik)
   - **Region**: `Singapore` (pastikan sama rantau dengan Neon untuk latensi paling rendah)
   - **Branch**: `main`
   - **Root Directory**: Biarkan kosong (jika repo khusus) ATAU masukkan `sistem kenderaan` (jika menggunakan monorepo)
   - **Runtime**: **Docker**
   - **Instance Type**: **Free**
4. Klik **Advanced** dan tetapkan **Health Check Path**: `/up`
5. Pada bahagian **Environment Variables**, klik **Add Environment Variable** dan masukkan:

| Key | Value | Catatan |
|---|---|---|
| `APP_NAME` | `Sistem Pengurusan Kenderaan & Pemandu - UPF` | Nama Aplikasi |
| `APP_ENV` | `production` | Mod produksi |
| `APP_KEY` | *(Klik Generate atau guna `php artisan key:generate --show`)* | Kunci keselamatan Laravel |
| `APP_DEBUG` | `false` | Matikan debug pada live site |
| `APP_URL` | `https://<nama-app-anda>.onrender.com` | URL yang diberikan oleh Render |
| `APP_TIMEZONE` | `Asia/Kuala_Lumpur` | Waktu Malaysia |
| `APP_LOCALE` | `ms` | Bahasa Malaysia |
| `DB_CONNECTION` | `pgsql` | Pemacu PostgreSQL |
| `DB_SSLMODE` | `require` | Diperlukan oleh Neon |
| `DATABASE_URL` | `postgresql://neondb_owner:***@ep-***.neon.tech/neondb?sslmode=require` | Connection string dari Neon |
| `SESSION_DRIVER` | `database` | Simpan sesi pengguna dalam DB |
| `RUN_SEEDER` | `true` | Masukkan data awal pengguna & 9 kenderaan ASM semasa boot pertama |

6. Klik **Create Web Service**.

---

## 🚀 Apa Yang Berlaku Semasa Deployment di Render?

Skrip `docker/entrypoint.sh` akan dijalankan secara automatik setiap kali web service dimulakan:
1. Menyelaraskan port Nginx mengikut port dinamik Render (`$PORT`).
2. Menyemak sambungan ke pangkalan data Neon.
3. Menjalankan `php artisan migrate --force` untuk membina semua jadual.
4. Menjalankan `php artisan db:seed --force` (sekiranya `RUN_SEEDER=true`) untuk mengisi:
   - 4 Peranan Pengguna (`admin`, `upf`, `pemohon`, `pemandu`).
   - Akaun pengguna rasmi (Admin, Aizat & Azwa UPF, Fahizal, Izzul, Shareeza, Azim, Kamal, Fathorossoim).
   - 9 Kenderaan Rasmi ASM lengkap dengan no pendaftaran, spesifikasi dan tarikh cukai jalan / insurans.
   - Rekod permohonan sampel, penyerahan kunci, pemulangan, log minyak dan laporan kerosakan.
5. Mengoptimumkan cache Laravel (`config:cache`, `route:cache`, `view:cache`).
6. Memulakan Nginx dan PHP-FPM 8.4 menggunakan Supervisor.

---

## 🔐 Maklumat Log Masuk Pengguna Ujian

Kata laluan lalai untuk semua akaun di bawah adalah: **`password`**

| Peranan | Nama Pegawai | Emel Log Masuk | Kebenaran / Akses |
|---|---|---|---|
| **Admin** | Pentadbir Sistem ASM | `admin@akademisains.gov.my` | Konfigurasi sistem, Audit Log, Pengurusan User |
| **Pegawai UPF** | Aizat bin Ahmad | `upf@akademisains.gov.my` | Kelulusan, Penugasan Kenderaan & Pemandu, Pemeriksaan Balik |
| **Pegawai UPF 2** | Azwa binti Mansor | `azwa@akademisains.gov.my` | Pengurusan operasi harian UPF |
| **Pemohon** | Mohd Azim bin Zainal | `azim@akademisains.gov.my` | Permohonan kenderaan (dengan pemandu atau pandu sendiri) |
| **Pemohon** | Kamal bin Ariffin | `kamal@akademisains.gov.my` | Permohonan kenderaan rasmi acara ASM |
| **Pemohon** | Fathorossoim bin Sulaiman | `fathorossoim@akademisains.gov.my` | Pemohon Bahagian STI |
| **Pemandu** | Fahizal bin Ramli | `fahizal@akademisains.gov.my` | Tugasan pemandu, log perjalanan, minyak, lapor isu & Master Data Kenderaan |
| **Pemandu** | Izzul bin Hakimi | `izzul@akademisains.gov.my` | Tugasan pemandu & semak kenderaan |
| **Pemandu** | Shareeza bin Ishak | `shareeza@akademisains.gov.my` | Tugasan pemandu & semak kenderaan |

---

## 🛠️ Tips & Penyelenggaraan Selepas Deploy

1. **Jalankan Perintah Artisan melalui Render Shell**:
   - Di dashboard Render, klik tab **Shell** pada Web Service anda.
   - Anda boleh menjalankan sebarang arahan Artisan:
     ```bash
     php artisan route:list
     php artisan migrate:status
     ```
2. **Kemas Kini Kod di Masa Hadapan**:
   - Cuma lakukan `git commit` dan `git push` ke branch `main` di GitHub.
   - Render akan mengesan commit baharu dan melakukan auto-deploy tanpa sebarang tindakan manual tambahan.
3. **Matikan `RUN_SEEDER` Selepas Boot Pertama (Pilihan)**:
   - Selepas deployment pertama berjaya dan data awal telah dimasukkan, anda boleh menukar nilai `RUN_SEEDER` kepada `false` di Render Environment Variables untuk mempercepatkan masa boot jika aplikasi restart.

---

## 🔍 Penyelesaian Ralat: "500 | Server Error"

Sekiranya pelayar web memaparkan skrin **500 | Server Error**, berikut adalah punca lazim dan langkah penyelesaiannya:

### 1. `APP_KEY` Hilang atau Format Tidak Sah
- **Punca**: Laravel memerlukan kunci penyulitan 32-bait yang sah (bermula dengan `base64:`). Jika menggunakan ciri penjanaan automatik Render, Render kadangkala menjana rentetan rawak yang tidak sepadan dengan saiz cipher AES-256 Laravel, menyebabkan kegagalan sesi.
- **Penyelesaian**:
  1. Buka Render Web Service anda -> Tab **Environment**.
  2. Cari pembolehubah `APP_KEY`. Jika tiada atau tidak bermula dengan `base64:`, masukkan kunci ini:
     ```text
     base64:gY8ctdjBdxS/Nwbq/ziNTru4njn88CJxTCFOyW6ZAnw=
     ```
     *(Atau jana kunci baru di komputer anda dengan `php artisan key:generate --show`)*.
  3. Klik **Save Changes**. Render akan memulakan semula web service secara automatik.

### 2. `DATABASE_URL` Belum Dimasukkan
- **Punca**: Render Web Service cuba menyambung ke Neon Postgres, tetapi pembolehubah `DATABASE_URL` kosong atau tertinggal.
- **Penyelesaian**:
  1. Dapatkan connection string daripada dashboard [Neon.tech](https://neon.tech):
     `postgresql://neondb_owner:***@ep-***.ap-southeast-1.aws.neon.tech/neondb?sslmode=require`
  2. Masukkan ke dalam Render Environment Variables sebagai `DATABASE_URL`.
  3. Pastikan `DB_SSLMODE` ditetapkan kepada `require`.

### 3. Semak Status Diagnostik Pantas
Aplikasi ini kini dilengkapi dengan endpoint diagnostik khas:
- Buka di pelayar web: **`https://sistem-kenderaan-asm.onrender.com/status`**
- Halaman ini akan memaparkan JSON yang memberitahu secara terperinci sama ada:
  - `encrypter_ok`: status kunci aplikasi `APP_KEY`
  - `connected`: status sambungan ke pangkalan data
  - `tables_count`: bilangan jadual yang telah berjaya dimigrasi
  - `error`: mesej ralat sebenar jika sambungan gagal.
