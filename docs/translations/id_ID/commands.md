# Referensi perintah

[Daftar isi dokumentasi](README.md)

Jalankan dari akar Symfony di terminal tepercaya. Contoh memakai dev. Jangan menaruh kata sandi, kunci atau kode pemulihan dalam argumen, log atau Git.

## Menemukan perintah dan bantuan


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev dan --no-interaction milik Symfony. --no-interaction tidak menyediakan rahasia dan tidak sesuai untuk perintah kata sandi interaktif. --help menunjukkan sintaks; -v menambah detail. Kode 0 menunjukkan hasil yang dilaporkan; --no-admin bisa berhasil tanpa portal yang dapat digunakan.

## `vendor/bin/guardian`

Mengaktifkan bundle di dev/test, membersihkan cache, menjalankan guardian:setup --automatic, membersihkan lagi, lalu menjalankan guardian:doctor dan guardian:access. Berhenti pada kesalahan pertama. Pengulangan mempertahankan akun dan kunci. Menolak APP_ENV=prod dan memerlukan proc_open.

Tanpa argumen, --help, -h atau help hanya menampilkan bantuan. setup satu-satunya tindakan dan tidak menerima opsi tambahan.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Referensi perintah |
|---|---|
| [`guardian:setup`](#guardiansetup) | Menyiapkan jalur privat, kunci, brankas, berkas Symfony dan administrator bila belum ada. |
| [`guardian:install`](#guardianinstall) | Menginisialisasi brankas dan membuat administrator dengan --username, bawaan admin. |
| [`guardian:access`](#guardianaccess) | Menampilkan jalur panel dan lokasi kredensial awal, bukan kata sandinya. |
| [`guardian:doctor`](#guardiandoctor) | Memeriksa brankas, izin, kunci, rute, administrator aktif dan konfigurasi. |
| [`guardian:password:reset`](#guardianpasswordreset) | Mengubah kata sandi akun username yang ada melalui masukan tersembunyi sesuai kebijakan, mencabut semua sesinya dan mencatat tindakan. |
| [`guardian:mfa:reset`](#guardianmfareset) | Menghapus rahasia dan penghitung TOTP username, mencabut sesi dan mencatat tindakan. |
| [`guardian:login:unlock`](#guardianloginunlock) | Mereset penghitung username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Mengosongkan daftar IP dan mematikan jadwal, JIT serta akses darurat jarak jauh. |
| [`guardian:lockdown`](#guardianlockdown) | on menutup diagnostik dan menghapus semua sesi; halaman bisnis tetap berjalan. |
| [`guardian:emergency`](#guardianemergency) | Membuat 5 kode sekali pakai untuk username yang ada dan mengganti kode lama. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Mengirim peringatan antrean ke alert_email melalui PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Mengukur verifikasi hash dengan rahasia sintetis, bukan kata sandi pengguna. |
| [`guardian:uninstall`](#guardianuninstall) | Menyiapkan penghapusan berkas yang terbukti dibuat Guardian. |

## `guardian:setup`

Menyiapkan jalur privat, kunci, brankas, berkas Symfony dan administrator bila belum ada. Mempertahankan akun. Menolak produksi. Setelah pemanggilan langsung, bersihkan cache dan jalankan pemeriksaan.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic membuat admin dan rahasia dalam berkas privat 0600. --path memilih jalur 25–181 karakter tanpa mengganti jalur yang ada. --no-admin melewati pembuatan administrator; tanpa akun aktif portal tidak dapat digunakan. --username mengusulkan nama interaktif, bawaan admin; tidak mengganti nama akun otomatis.

```text
--path: ^/[a-zA-Z0-9/_-]{24,180}$
/_profiler; /_wdt → ∅
```

```bash
php bin/console guardian:setup --username=operator --env=dev
php bin/console cache:clear --env=dev
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## `guardian:install`

Menginisialisasi brankas dan membuat administrator dengan --username, bawaan admin. Meminta kata sandi tersembunyi sesuai kebijakan aktif. Menolak akun yang sudah ada. Tidak membuat rute atau konfigurasi Symfony; biasanya gunakan asisten lengkap.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Nama Pengguna : 3 Untuk 64 Karakter alphanumerik adalah

## `guardian:access`

Menampilkan jalur panel dan lokasi kredensial awal, bukan kata sandinya. --base-url menerima asal HTTPS nyata dengan port opsional, tanpa kredensial, kueri atau fragmen. Ganti host contoh.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Memeriksa brankas, izin, kunci, rute, administrator aktif dan konfigurasi. Kode keluar 1 jika ada masalah. Zona waktu PHP CLI dan PHP-FPM bisa berbeda. Tidak menguji paparan Internet atau aturan proksi.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Mengubah kata sandi akun username yang ada melalui masukan tersembunyi sesuai kebijakan, mencabut semua sesinya dan mencatat tindakan. Jangan berikan kata sandi sebagai argumen.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Menghapus rahasia dan penghitung TOTP username, mencabut sesi dan mencatat tindakan. Daftarkan autentikator baru; kewajiban MFA global tetap berlaku dan dapat memblokir profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Mereset penghitung username. --ip ALAMAT juga menghapus penghitung asal IPv4/IPv6 itu; jika tidak, blokir jaringan bisa tersisa. --all menghapus seluruh penghitung. Akun, kata sandi dan sesi tidak berubah; tindakan dicatat.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Mengosongkan daftar IP dan mematikan jadwal, JIT serta akses darurat jarak jauh. Autentikasi, MFA, penguncian global dan blokir login tetap berlaku. Atur ulang pembatasan yang diinginkan pada panel.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on menutup diagnostik dan menghapus semua sesi; halaman bisnis tetap berjalan. off hanya membuka penguncian global: pengguna harus masuk lagi dan kebijakan lain tetap berlaku.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Membuat 5 kode sekali pakai untuk username yang ada dan mengganti kode lama. Ditampilkan satu kali; simpan di luar server dalam pengelola rahasia. Pemakaian jarak jauh memerlukan darurat aktif, sesi terautentikasi, kata sandi dan alasan. Tidak melewati penguncian global.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Mengirim peringatan antrean ke alert_email melalui PHP mail(). Atur transportasi dan tugas terjadwal lalu periksa pengiriman nyata. Kegagalan tetap di antrean. Kode 1 untuk penerima tidak valid atau pengiriman gagal.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Mengukur verifikasi hash dengan rahasia sintetis, bukan kata sandi pengguna. --iterations menerima 1–10, bawaan 3. JSON berisi algoritme, waktu dan verifikasi per detik. Mengukur satu proses CPU lokal, bukan GPU atau penyerang.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Menyiapkan penghapusan berkas yang terbukti dibuat Guardian. Mempertahankan akun, pengaturan, log dan kunci. Hentikan worker, jalankan sebelum composer remove, bersihkan cache dev dan mulai ulang. Tinjau berkas khusus atau tanpa bukti asal.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge meminta penghapusan data dengan konfirmasi bawaan menolak. --force memerlukan --purge dan menyetujui penghancuran; wajib dengan --no-interaction. Brankas eksternal, tautan dan berkas asing dapat mencegah penghapusan penuh. Simpan kunci yang diperlukan dan tinjau hasil.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Penghapusan instalasi dan data


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Cadangkan brankas dan kunci bersama lalu uji pemulihan. Kunci hilang tidak dibuat ulang otomatis. Setelah penghapusan, periksa profiler hanya secara privat. Produksi: APP_DEBUG=0, WebProfilerBundle dan pengumpulan dimatikan. Rantai audit lokal tidak membuktikan integritas host yang disusupi.

[Penghapusan dan Data](desinstallation.md)
