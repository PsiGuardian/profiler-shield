# Terjemahan

[Kandungan dokumentasi](README.md)

Portal menyediakan 28 varian wilayah dan 25 katalog; empat varian Inggeris berkongsi katalog. Teks statik diterjemahkan kemudian di-escape. Nama pengguna, mesej tersuai dan data pengguna tidak diterjemahkan. Perkhidmatan jauh tidak menerima teks. Audit menyemak kunci, parameter, UTF-8, arahan dan pautan; ujian HTTP melawat paparan setiap varian.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
