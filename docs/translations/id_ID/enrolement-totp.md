# Autentikasi dua faktor

[Daftar isi dokumentasi](README.md)


## Autentikasi dua faktor

Setiap orang mendaftarkan diri sendiri TOTP Mengidentifikasi dan mengkonfirmasi kode.

Setiap orang mendaftarkan diri sendiri TOTP Mengidentifikasi dan mengkonfirmasi kode. MFA Memblokir profil akses untuk akun yang tidak terdaftar. Pilih penyiapan manual dan masukkan kunci yang ditampilkan. Gunakan SHA-1, enam digit, periode 30 detik.

## 1. Konfirmasikan identitas Anda

Masukkan kata sandi yang sama untuk masuk ke Guardian. Hanya diminta sekali selama pengaturan ini.

## 2. Masukkan kunci ke aplikasi autentikator

Pilih penyiapan manual dan masukkan kunci yang ditampilkan. Gunakan SHA-1, enam digit, periode 30 detik.

## 3. Konfirmasi kode enam angka

Masukkan kode di aplikasi. Kata sandi kedua tidak diperlukan.

## Privasi: jangan masukkan URL situs

Jika aplikasi meminta nama atau situs, masukkan hanya “ΩGuardian” atau nama netral. Jangan masukkan URL instans Symfony Anda.

Jaga kerahasiaannya: kunci dapat menghasilkan kode TOTP. Jangan bagikan atau ambil tangkapan layar.

Rahasia sementara kedaluwarsa setelah 10 menit atau 5 kode salah; mulai ulang saat itu. Kesalahan mempertahankan formulir tanpa meminta kata sandi lagi. Penghitung konfirmasi dikonsumsi untuk mencegah pemakaian ulang.

## pemulihan dan verifikasi

Menghapus rahasia dan penghitung TOTP username, mencabut sesi dan mencatat tindakan. Daftarkan autentikator baru; kewajiban MFA global tetap berlaku dan dapat memblokir profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
