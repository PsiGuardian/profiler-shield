# Pengesahan dua faktor

[Kandungan dokumentasi](README.md)


## Pengesahan dua faktor

Lindungi akaun Guardian dengan kod sementara tanpa perkhidmatan QR luar.

Setiap orang mendaftarkan diri sendiri TOTP rahsia dan mengesahkan kod. permintaan MFA blok akses profil untuk akaun yang tidak didaftarkan. QR Rancangan pemulihan pentadbiran sebelum memerlukan perlindungan ini untuk keseluruhan pasukan.

## 1. Sahkan identiti anda

Masukkan kata laluan yang sama seperti ketika log masuk ke Guardian. Ia diminta sekali sahaja.

## 2. Imbas QR ini menggunakan aplikasi pengesah

Guardian menjana QR secara setempat tanpa menghantar permintaan kepada perkhidmatan luar.

## 3. Sahkan kod enam digit

Masukkan kod yang dipaparkan dalam aplikasi. Kata laluan kedua tidak diperlukan.

## Privasi: jangan masukkan URL laman web

Jika aplikasi meminta nama atau laman web, masukkan hanya “ΩGuardian” atau nama neutral. Jangan masukkan URL pemasangan Symfony. Jangan gunakan penjana QR dalam talian kerana ia akan menerima rahsia anda.

Rahsiakan kunci: ia boleh menghasilkan kod TOTP. Jangan kongsi atau ambil tangkap layar.

Rahsia sementara luput selepas 10 minit atau 5 kod salah; mulakan semula ketika itu. Kod salah mengekalkan borang tanpa meminta kata laluan lagi. Pembilang pengesahan digunakan untuk menghalang ulangan. QR SVG menggunakan bacon/bacon-qr-code dan ext-iconv tanpa GD, XMLWriter atau rangkaian. Input manual kekal. URI otpauth menggunakan pembekal neutral dan akaun tanpa domain laman. Jangan kongsi rahsia atau URL peribadi.

## pemulihan dan pengesahan

Memadam rahsia dan pembilang TOTP username, membatalkan sesi dan merekodkan tindakan. Daftarkan pengesah baharu; keperluan MFA global kekal dan boleh menghalang profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
