# Autentikasi dua faktor

[Daftar isi dokumentasi](README.md)


## Autentikasi dua faktor

Lindungi akun Guardian dengan kode sementara, tanpa layanan QR eksternal.

Setiap orang mendaftarkan diri sendiri TOTP Mengidentifikasi dan mengkonfirmasi kode. MFA Memblokir profil akses untuk akun yang tidak terdaftar. QR Perencanaan pemulihan administratif sebelum memerlukan perlindungan ini untuk seluruh tim.

## 1. Konfirmasikan identitas Anda

Masukkan kata sandi yang sama untuk masuk ke Guardian. Hanya diminta sekali selama pengaturan ini.

## 2. Pindai QR ini dengan aplikasi autentikator

Guardian membuat QR secara lokal tanpa mengirim permintaan ke layanan eksternal.

## 3. Konfirmasi kode enam angka

Masukkan kode di aplikasi. Kata sandi kedua tidak diperlukan.

## Privasi: jangan masukkan URL situs

Jika aplikasi meminta nama atau situs, masukkan hanya “ΩGuardian” atau nama netral. Jangan masukkan URL instans Symfony Anda. Jangan gunakan pembuat QR online karena akan menerima rahasia Anda.

Jaga kerahasiaannya: kunci dapat menghasilkan kode TOTP. Jangan bagikan atau ambil tangkapan layar.

Rahasia sementara kedaluwarsa setelah 10 menit atau 5 kode salah; mulai ulang saat itu. Kesalahan mempertahankan formulir tanpa meminta kata sandi lagi. Penghitung konfirmasi dikonsumsi untuk mencegah pemakaian ulang. SVG QR memakai bacon/bacon-qr-code dan ext-iconv, tanpa GD, XMLWriter atau jaringan. Masukan manual tetap tersedia. URI otpauth memakai penyedia netral dan akun, tanpa domain situs. Jangan bagikan rahasia atau URL privat.

## pemulihan dan verifikasi

Menghapus rahasia dan penghitung TOTP username, mencabut sesi dan mencatat tindakan. Daftarkan autentikator baru; kewajiban MFA global tetap berlaku dan dapat memblokir profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
