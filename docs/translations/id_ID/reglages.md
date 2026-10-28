# Pengaturan

[Daftar isi dokumentasi](README.md)


## Perlindungan tambahan

Kontrol dikelompokkan dengan pengaturannya. Opsi nonaktif berwarna abu-abu dan nilainya disimpan.

Guardian mengendalikan profil dan toolbar debug.Ada akun administrator atau pengembang yang diberi izin diperlukan. MFAdengan jadwal, JIT, pembatasan jaringan dan penguncian masih dapat menolak akses. mode pencurian menyembunyikan titik masuk; itu tidak menggantikan kontrol ini.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Profil | Pengaturan |
|---|---|
| Fleksibilitas • Flexibilitas | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| • Tim yang diperkuat | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| Sensitif · Terbatas | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Parameter | Pengaturan | Nilai awal |
|---|---|---|
| `stealth` | Kembalikan 404 alih-alih 403 pada rute diagnostik yang tidak diizinkan. | `true` |
| `mfa` | Semua pengguna harus mendaftarkan TOTP sebelum mewajibkannya. | `false` |
| `idle_minutes` | Menit tanpa aktivitas sebelum sesi kedaluwarsa. | `60` |
| `absolute_hours` | Jam, tanpa memperhatikan aktivitas. | `8` |
| `remember_days` | Hari. Tidak menggantikan login atau aturan jam kerja. | `14` |
| `schedule_enabled` | Pilih kapan alat diagnostik dapat diakses. Halaman aplikasi tetap tersedia. | `false` |
| `weekdays` | Pilih hari ketika diagnostik diizinkan. | `1,2,3,4,5` |
| `start` | Mulai | `09:00` |
| `end` | Selesai | `18:00` |
| `timezone` | Dideteksi dari proses PHP yang melayani halaman ini. PHP CLI, PHP-FPM, dan sistem operasi dapat memakai zona waktu berbeda. Periksa pengaturan server sebelum menyimpan. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | URL privat mengembalikan 404 di luar jam kerja. Pemulihan hanya melalui CLI lokal. | `false` |
| `jit_enabled` | Buka profiler hanya selama jendela diagnostik yang diminta. | `false` |
| `jit_minutes` | Menit, hanya digunakan jika diaktifkan. | `30` |
| `ip_allowlist` | Peringatan: kesalahan dapat mengunci akses. Pemulihan lokal: guardian:policy:reset. | `∅` |
| `audit_level` | Pilih tingkat detail audit dan masa penyimpanan lokal. | `standard` |
| `audit_retention_days` | Hari sebelum log dibersihkan. | `30` |
| `alert_email` | Menerima peringatan darurat. Akses tidak boleh bergantung pada pengiriman email. | `∅` |
| `emergency_enabled` | Izinkan pengecualian 30 menit dengan kode sekali pakai dan catatan audit. | `false` |
| `login_rules` | Secara default : 10 Kegagalan dan kemudian a 10-minute lockout. perhitungan berlaku untuk kedua akun dan sumber jaringan, termasuk konfirmasi kata sandi. | `10 / 10 / 30 / 30` |
| `password_policy` | Peraturan ini berlaku untuk penciptaan kata sandi dan perubahan, di web dan dalam CLIMereka tidak mendedahkan atau memeriksa kata sandi yang ada. | `min_length=15` |
| `error_mode` | Halaman aplikasi mempertahankan respons 404 situs; pengecualian memakai templat 404 bila tersedia, atau halaman netral. Mode netral memakai pesan standar; mode khusus memakai teks Anda. | `native` |
| `error_reference` | Referensi ini berlaku untuk GuardianHalaman 's neutral: secara rawak menghasilkan kode baru, bukan pesan rawak. | `none` |
| `error_reference_text` | Hanya tersedia dengan referensi statik yang disesuaikan. | `∅` |
| `error_custom_text` | Hanya tersedia untuk pesan netral khusus. Teks disimpan saat berganti mode. | `∅ → custom` |

Memungkinkan perencanaan, hari-hari terpilih, waktu permulaan inklusif dan waktu akhir eksklusif. zona waktu yang disimpan menentukan waktu lokal. interval malam tidak diterima. menyembunyikan portal di luar jam mencegah akses kecemasan jarak jauh; menjaga perintah pemulihan lokal tersedia.

Idle timeout, jangka hayat mutlak dan kenangan perangkat melayani tujuan yang berbeda. mengingat sebuah perangkat tidak melewati jadwal atau JIT. penarikan mengakhiri sesi yang relevan di server. Gunakan akun individu untuk memperuntukkan tindakan dalam jurnal.

JIT memerlukan pengaktifan jendela sementara dari sesi yang diberi izin. akses kecemasan, ketika diaktifkan, membutuhkan kata sandi, kode penggunaan tunggal dan alasan. itu tidak melampaui kunci global. perintah lokal tetap diperlukan jika portal tidak dapat diakses.

IP Memberi izin yang diterima IPv4, IPv6 dan CIDR Rangkaian - Periksa Symfony Konfigurasi proxy yang dipercaya sebelum memungkinkan. HTTPS Untuk cookies yang aman. Guardian Tidak memperbaiki server web atau database yang terpapar.

Tingkat dan retensi menentukan peristiwa yang tersedia, hingga 5000 entri. Saring menurut jenis dan akun sebelum berpindah halaman. Peringatan masuk antrean; atur transportasi dan pengirimannya. Rantai hash lokal tidak menjamin integritas pada host yang disusupi.

Tetapkan panjang minimum dan kategori yang diperlukan dalam Koneksi dan kata sandi. tanda penciptaan akun menggambarkan kebijakan disimpan. Perubahan berlaku untuk kata laluan baru; pilih pembaharuan wajib untuk akun sedia ada. Guardian Pilihan Argon2id Ketika PHP memberikan, jika tidak bcrypt.

Filter semua peristiwa yang disimpan berdasarkan jenis dan akun.Peristiwa terbaru pertama kali muncul, di halaman 50Scrolling tidak memuat halaman berikutnya.

## Lingkungan


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


Proksi menangani TLS, normalisasi dan perlindungan volume lalu lintas. Gerbang memeriksa diagnostik sebelum perutean; rute bisnis tidak membaca akun atau sesi. Identitas Guardian terpisah dari Symfony. Cadangkan brankas dan kunci bersama di luar direktori publik. PHP atau host yang disusupi bisa mengungkap keduanya. Penyimpanan untuk satu host. Kesalahan startup, cache atau proksi bisa memengaruhi seluruh aplikasi.

Cadangkan brankas dan kunci bersama lalu uji pemulihan. Kunci hilang tidak dibuat ulang otomatis. Setelah penghapusan, periksa profiler hanya secara privat. Produksi: APP_DEBUG=0, WebProfilerBundle dan pengumpulan dimatikan. Rantai audit lokal tidak membuktikan integritas host yang disusupi.

## pemulihan dan verifikasi

Mengosongkan daftar IP dan mematikan jadwal, JIT serta akses darurat jarak jauh. Autentikasi, MFA, penguncian global dan blokir login tetap berlaku. Atur ulang pembatasan yang diinginkan pada panel.

on menutup diagnostik dan menghapus semua sesi; halaman bisnis tetap berjalan. off hanya membuka penguncian global: pengguna harus masuk lagi dan kebijakan lain tetap berlaku.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
