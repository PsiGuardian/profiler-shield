# ΩGuardian Profiler Shield — Instalasi dan penggunaan

PHP 8.3 atau lebih baru; Symfony 5.4, 6.4 atau 7.4. Symfony 8.1 memerlukan PHP 8.4 atau lebih baru. Composer dan ekstensi OpenSSL + Tokenizer diperlukan. Pasang WebProfilerBundle untuk menggunakan profiler.

## 1. Instalasi

Jalankan dari direktori utama aplikasi Symfony yang berisi composer.json dan bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Konfigurasi

```bash
php vendor/bin/guardian setup
```

Asisten mengaktifkan bundle di dev/test, menyiapkan brankas dan akun administrator, membersihkan cache dan memeriksa konfigurasi. Lanjutkan hanya setelah setiap langkah berhasil. Jaga aplikasi tetap privat selama proses. Menjalankannya kembali mempertahankan akun dan kunci yang ada.

## 3. Buka panel

```bash
php bin/console guardian:access --env=dev
```

Perintah menampilkan jalur privat panel. Ganti https://VOTRE-HOTE dengan asal HTTPS aplikasi yang sebenarnya, termasuk port. Baca kredensial di .guardian-shield/initial-admin-credentials.txt atau brankas yang ditunjukkan. Login pertama mewajibkan perubahan kata sandi. Jangan bagikan berkas ini.

## 4. Hapus instalasi

Hentikan worker lalu jalankan perintah secara berurutan setelah setiap langkah berhasil. Brankas dan kuncinya dipertahankan. Mulai ulang worker dan periksa profiler di lingkungan privat.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Untuk menghapus akun dan pengaturan juga, ganti perintah pertama dengan berikut ini dan konfirmasikan penghapusan. Menolak tidak mengubah apa pun. Tinjau brankas eksternal dan berkas khusus secara manual; simpan kunci brankas yang dipertahankan.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Referensi perintah

Bantuan menjelaskan perintah. Perintah akses menemukan panel; diagnostik memeriksa konfigurasi. Diperlukan akses lokal yang tepercaya.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Referensi perintah](commands.md) · [Daftar isi dokumentasi](README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

Jika organisasi menggunakan Private Packagist, konfigurasikan repositori ini sebelum instalasi dan simpan kredensial Composer sesuai petunjuk organisasi. Jangan pernah memasukkan token ke Git.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
