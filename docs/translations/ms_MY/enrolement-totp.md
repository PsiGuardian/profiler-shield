# Pengesahan dua faktor

[Kandungan dokumentasi](README.md)


## Pengesahan dua faktor

Setiap orang mendaftarkan diri sendiri TOTP rahsia dan mengesahkan kod.

Setiap orang mendaftarkan diri sendiri TOTP rahsia dan mengesahkan kod. permintaan MFA blok akses profil untuk akaun yang tidak didaftarkan. Pilih persediaan manual dan masukkan kunci yang dipaparkan. Gunakan SHA-1, enam digit dan tempoh 30 saat.

## 1. Sahkan identiti anda

Masukkan kata laluan yang sama seperti ketika log masuk ke Guardian. Ia diminta sekali sahaja.

## 2. Masukkan kunci dalam aplikasi pengesah

Pilih persediaan manual dan masukkan kunci yang dipaparkan. Gunakan SHA-1, enam digit dan tempoh 30 saat.

## 3. Sahkan kod enam digit

Masukkan kod yang dipaparkan dalam aplikasi. Kata laluan kedua tidak diperlukan.

## Privasi: jangan masukkan URL laman web

Jika aplikasi meminta nama atau laman web, masukkan hanya “ΩGuardian” atau nama neutral. Jangan masukkan URL pemasangan Symfony.

Rahsiakan kunci: ia boleh menghasilkan kod TOTP. Jangan kongsi atau ambil tangkap layar.

Rahsia sementara luput selepas 10 minit atau 5 kod salah; mulakan semula ketika itu. Kod salah mengekalkan borang tanpa meminta kata laluan lagi. Pembilang pengesahan digunakan untuk menghalang ulangan.

## pemulihan dan pengesahan

Memadam rahsia dan pembilang TOTP username, membatalkan sesi dan merekodkan tindakan. Daftarkan pengesah baharu; keperluan MFA global kekal dan boleh menghalang profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
