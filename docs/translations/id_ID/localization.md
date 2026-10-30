# Terjemahan

[Daftar isi dokumentasi](README.md)

Portal menyediakan 28 varian wilayah dan 25 katalog; empat varian Inggris berbagi katalog. Teks statis diterjemahkan lalu di-escape. Nama pengguna, pesan khusus dan data pengguna tidak diterjemahkan. Layanan jauh tidak menerima teks. Audit memeriksa kunci, parameter, UTF-8, perintah dan tautan; pengujian HTTP mengunjungi tampilan setiap varian.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
