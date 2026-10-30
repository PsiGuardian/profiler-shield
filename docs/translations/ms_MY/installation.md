# ΩGuardian Profiler Shield — Pemasangan dan penggunaan

PHP 8.3 atau lebih baharu; Symfony 5.4, 6.4 atau 7.4. Symfony 8.1 memerlukan PHP 8.4 atau lebih baharu. Composer dan sambungan OpenSSL + Tokenizer diperlukan. Pasang WebProfilerBundle untuk menggunakan profiler.

## 1. Pasang

Jalankan dari direktori akar aplikasi Symfony yang mengandungi composer.json dan bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Konfigurasi

```bash
php vendor/bin/guardian setup
```

Pembantu mengaktifkan bundle dalam dev/test, menyediakan peti simpanan dan akaun pentadbir, mengosongkan cache dan menyemak konfigurasi. Teruskan hanya selepas setiap langkah berjaya. Pastikan aplikasi kekal peribadi sepanjang proses. Menjalankannya semula mengekalkan akaun dan kunci sedia ada.

## 3. Buka panel

```bash
php bin/console guardian:access --env=dev
```

Arahan memaparkan laluan peribadi panel. Gantikan https://VOTRE-HOTE dengan asal HTTPS sebenar aplikasi, termasuk port. Baca kelayakan dalam .guardian-shield/initial-admin-credentials.txt atau peti simpanan yang dinyatakan. Log masuk pertama memerlukan perubahan kata laluan. Jangan kongsi fail ini.

## 4. Nyahpasang

Hentikan worker dan jalankan arahan mengikut urutan selepas setiap langkah berjaya. Peti simpanan dan kuncinya dikekalkan. Mulakan semula worker dan semak profiler dalam persekitaran peribadi.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Untuk memadam akaun dan tetapan juga, gantikan arahan pertama dengan yang berikut dan sahkan. Menolak tidak mengubah apa-apa. Semak peti simpanan luaran dan fail tersuai secara manual; simpan kunci peti yang dikekalkan.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Rujukan arahan

Bantuan menerangkan arahan. Arahan akses mencari panel; diagnostik menyemak konfigurasi. Akses setempat yang dipercayai diperlukan.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Rujukan arahan](commands.md) · [Kandungan dokumentasi](README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

Jika organisasi menggunakan Private Packagist, konfigurasikan repositori ini sebelum pemasangan dan simpan kelayakan Composer mengikut arahannya. Jangan sekali-kali menyimpan token dalam Git.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
