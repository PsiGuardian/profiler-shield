# Tetapan

[Kandungan dokumentasi](README.md)


## Perlindungan tambahan

Kawalan dikelompokkan bersama tetapannya. Pilihan tidak aktif dikelabukan dan nilainya disimpan.

Guardian mengawal profil dan bar alat debug. akaun pentadbir atau pemaju yang diberi kuasa diperlukan. MFAdalam jadual, JIT, pembatasan rangkaian dan penguncian masih boleh menolak akses. mod pencurian menyembunyikan titik kemasukan; ia tidak menggantikan pemeriksaan ini.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Profil | Tetapan |
|---|---|
| Tag: fleksibel | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| Pasukan · diperkuat | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| sensitif · terhad | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Parameter | Tetapan | Nilai awal |
|---|---|---|
| `stealth` | Pulangkan 404 dan bukannya 403 pada laluan diagnostik tanpa kebenaran. | `true` |
| `mfa` | Semua pengguna mesti mendaftar TOTP sebelum keperluan ini diwajibkan. | `false` |
| `idle_minutes` | Minit tanpa aktiviti sebelum sesi tamat. | `60` |
| `absolute_hours` | Jam tanpa mengira aktiviti. | `8` |
| `remember_days` | Hari. Tidak menggantikan log masuk atau aturan waktu bekerja. | `14` |
| `schedule_enabled` | Pilih masa alat diagnostik boleh diakses. Halaman aplikasi kekal tersedia. | `false` |
| `weekdays` | Pilih hari diagnostik dibenarkan. | `1,2,3,4,5` |
| `start` | Mula | `09:00` |
| `end` | Tamat | `18:00` |
| `timezone` | Dikesan daripada proses PHP yang melayani halaman ini. PHP CLI, PHP-FPM dan sistem operasi mungkin mempunyai zon waktu berbeza. Semak konfigurasi pelayan sebelum menyimpan. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | URL peribadi mengembalikan 404 di luar waktu kerja. Pemulihan hanya melalui CLI setempat. | `false` |
| `jit_enabled` | Buka profiler hanya sepanjang tempoh diagnostik yang diminta. | `false` |
| `jit_minutes` | Minit, hanya digunakan apabila diaktifkan. | `30` |
| `ip_allowlist` | Amaran: kesilapan boleh mengunci akses anda. Pemulihan setempat: guardian:policy:reset. | `∅` |
| `audit_level` | Pilih perincian audit dan tempoh penyimpanan setempat. | `standard` |
| `audit_retention_days` | Hari sebelum pembersihan log. | `30` |
| `alert_email` | Menerima amaran kecemasan. Akses tidak boleh bergantung pada penghantaran e-mel. | `∅` |
| `emergency_enabled` | Benarkan pengecualian 30 minit dengan kod sekali guna dan log audit. | `false` |
| `login_rules` | Kegagalan : 10 kegagalan, kemudian a 10-minute lockout. counters terpakai kepada kedua-dua akaun dan asal rangkaian, termasuk pengesahan kata laluan. | `10 / 10 / 30 / 30` |
| `password_policy` | Peraturan ini terpakai kepada penciptaan kata laluan dan perubahan, di web dan dalam CLIMereka tidak mendedahkan atau memeriksa kata laluan yang sedia ada. | `min_length=15` |
| `error_mode` | Halaman aplikasi mengekalkan respons 404 laman; pengecualian menggunakan templat 404 jika tersedia, atau halaman neutral. Mod neutral menggunakan mesej standard; mod tersuai menggunakan teks anda. | `native` |
| `error_reference` | Rujukan ini terpakai kepada Guardian's halaman neutral: secara rawak menghasilkan kod baru, bukan mesej rawak. | `none` |
| `error_reference_text` | Hanya boleh didapati dengan rujukan statik yang disesuaikan. | `∅` |
| `error_custom_text` | Hanya tersedia untuk mesej neutral tersuai. Teks dikekalkan apabila bertukar mod. | `∅ → custom` |

Membolehkan jadual, hari-hari terpilih, masa permulaan termasuk dan masa akhir eksklusif. zon masa yang disimpan menentukan masa tempatan. interval malam tidak diterima. menyembunyikan portal di luar jam menghalang akses kecemasan jauh; menjaga perintah pemulihan tempatan tersedia.

Idle timeout, hayat mutlak dan kenangan peranti berkhidmat untuk tujuan yang berbeza. mengingati peranti tidak melebihi jadual atau JIT. penarikan menamatkan sesi yang berkaitan pada pelayan. gunakan akaun individu untuk memperuntukkan tindakan dalam jurnal.

JIT memerlukan pengaktifan tetingkap sementara daripada sesi yang diberi kuasa. akses kecemasan, apabila diaktifkan, memerlukan kata laluan, kod penggunaan tunggal dan alasan. ia tidak melampaui penutupan global. perintah tempatan kekal diperlukan jika portal tidak dapat diakses.

Pihak IP boleh menerima IPv4, IPv6 dan CIDR Rangkaian : Periksa Symfony konfigurasi proxy yang dipercayai sebelum membolehkannya. HTTPS untuk cookies yang selamat. Guardian tidak memperbaiki pelayan web atau pangkalan data yang terdedah.

Tahap dan pengekalan menentukan peristiwa yang tersedia, sehingga 5000 entries.Filter mengikut jenis dan akaun sebelum mengubah halaman.Pemberitahuan dikurangkan; mengkonfigurasi pengangkutan dan penghantaran mereka.Sebuah rantaian hash tempatan tidak menjamin integriti pada host yang terjejas.

Tetapkan panjang minimum dan kategori yang diperlukan dalam sambungan dan kata laluan. tanda penciptaan akaun menggambarkan dasar disimpan. perubahan terpakai kepada kata lalu baru; pilih pembaharuan wajib untuk akaun sedia ada. Guardian Pilihan Argon2id Apabila PHP membolehkan, jika tidak bcrypt.

Filter semua peristiwa yang disimpan mengikut jenis dan akaun. peristiwa terkini muncul terlebih dahulu, dalam halaman 50Scrolling tidak memuatkan halaman seterusnya.

## Persekitaran


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


Proksi mengendalikan TLS, normalisasi dan perlindungan volum trafik. Gerbang menyemak diagnostik sebelum penghalaan; laluan perniagaan tidak membaca akaun atau sesi. Identiti Guardian berasingan daripada Symfony. Sandarkan peti simpanan dan kunci bersama di luar direktori awam. PHP atau hos terjejas boleh mendedahkan kedua-duanya. Storan untuk satu hos. Ralat permulaan, cache atau proksi boleh menjejaskan seluruh aplikasi.

Sandarkan peti simpanan dan kunci bersama serta uji pemulihan. Kunci hilang tidak dijana semula automatik. Selepas penyingkiran, semak profiler hanya secara peribadi. Produksi: APP_DEBUG=0, WebProfilerBundle dan pengumpulan dimatikan. Rantaian audit setempat tidak membuktikan integriti hos terjejas.

## pemulihan dan pengesahan

Mengosongkan senarai IP dan mematikan jadual, JIT serta kecemasan jauh. Pengesahan, MFA, kunci global dan sekatan log masuk kekal. Tetapkan semula sekatan yang diperlukan dalam panel.

on menutup diagnostik dan memadam semua sesi; halaman perniagaan kekal tersedia. off hanya membuka kunci global: perlu log masuk semula dan dasar lain masih terpakai.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
