# Sistem Pengurusan Kenderaan & Pemandu - Unit Pengurusan Fasiliti (UPF)
### Akademi Sains Malaysia (ASM)

Sistem aplikasi web komprehensif bagi pengurusan tempahan kenderaan rasmi, penugasan pemandu, penyerahan & pemulangan kenderaan, semakan pemeriksaan fizikal kenderaan, log bahan api, dan laporan kerosakan/insiden bagi Akademi Sains Malaysia.

---

## 🚀 Ciri-Ciri Utama Sistem

1. **Dashboard & Statistik Pintar**:
   - Status masa nyata kenderaan (Available, Assigned, In Use, Maintenance).
   - Jadual harian pemandu dan permohonan terkini.
   - Peringatan cukai jalan & insurans tamat tempoh.
2. **Pengurusan Permohonan Fleksibel**:
   - Sokongan permohonan dengan khidmat pemandu rasmi ATAU pilihan **pandu sendiri oleh pemohon**.
   - Penugasan kenderaan dan pemandu oleh Pegawai UPF dengan sokongan override konflik jadual.
3. **Aliran Kerja Penyerahan & Pemulangan (Handover & Return)**:
   - Rekod bacaan meter (odometer), paras bahan api, dan senarai semak kelengkapan (Smart Tag, Kad Inden, Kunci kenderaan).
   - **Pengesahan Pemeriksaan Fizikal oleh UPF**: Bahagian khas bagi pegawai UPF menyemak dan mengesahkan keadaan kenderaan dipulangkan dalam keadaan baik dan sempurna.
4. **Peranan Pengguna Terperinci (RBAC)**:
   - **Pentadbir Sistem (Admin)**: Tetapan agensi, audit log, pengurusan pengguna.
   - **Pegawai UPF**: Kelulusan tempahan, penugasan, semakan fizikal kenderaan.
   - **Pemohon**: Menghantar tempahan kenderaan, semak status dan rekod pemulangan pandu sendiri.
   - **Pemandu**: Menerima tugasan, log perjalanan, kemas kini minyak, rekod insiden dan akses **Master Data Kenderaan**.
5. **Antaramuka Mesra Pengguna & Responsif**:
   - Menu atas jenis dropdown navigasi yang stabil dan mesra peranti mudah alih (mobile).
   - Penggunaan Bahasa Malaysia sepenuhnya mengikut format rasmi ASM.

---

## 🛠️ Panduan Deployment (GitHub + Neon + Render)

Sistem ini sedia untuk dideploy ke cloud dengan konfigurasi:
- **Repository**: [GitHub](https://github.com)
- **Pangkalan Data**: [Neon Serverless PostgreSQL](https://neon.tech)
- **Web Service**: [Render.com](https://render.com) (menggunakan Docker atau Blueprint)

Sila rujuk panduan terperinci di: 👉 **[DEPLOYMENT.md](DEPLOYMENT.md)**

---

## 💻 Pemasangan Tempatan (Local Development)

```bash
# 1. Pasang dependencies PHP
composer install

# 2. Salin fail konfigurasi environment
cp .env.example .env

# 3. Jana kunci aplikasi
php artisan key:generate

# 4. Jalankan migrasi dan seeder
php artisan migrate --seed

# 5. Jalankan server pembangunan
php artisan serve
```

Akses sistem di pelayar web: `http://127.0.0.1:8000`

### Log Masuk Lalai:
- **Admin**: `admin@akademisains.gov.my` / `password`
- **Pegawai UPF**: `upf@akademisains.gov.my` / `password`
- **Pemohon**: `azim@akademisains.gov.my` / `password`
- **Pemandu**: `fahizal@akademisains.gov.my` / `password`
