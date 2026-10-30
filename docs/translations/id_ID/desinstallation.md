# Penghapusan dan Data

[Daftar isi dokumentasi](README.md)

Menyiapkan penghapusan berkas yang terbukti dibuat Guardian. Mempertahankan akun, pengaturan, log dan kunci. Hentikan worker, jalankan sebelum composer remove, bersihkan cache dev dan mulai ulang. Tinjau berkas khusus atau tanpa bukti asal.

--purge meminta penghapusan data dengan konfirmasi bawaan menolak. --force memerlukan --purge dan menyetujui penghancuran; wajib dengan --no-interaction. Brankas eksternal, tautan dan berkas asing dapat mencegah penghapusan penuh. Simpan kunci yang diperlukan dan tinjau hasil.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Cadangkan brankas dan kunci bersama lalu uji pemulihan. Kunci hilang tidak dibuat ulang otomatis. Setelah penghapusan, periksa profiler hanya secara privat. Produksi: APP_DEBUG=0, WebProfilerBundle dan pengumpulan dimatikan. Rantai audit lokal tidak membuktikan integritas host yang disusupi.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Referensi perintah

--purge meminta penghapusan data dengan konfirmasi bawaan menolak. --force memerlukan --purge dan menyetujui penghancuran; wajib dengan --no-interaction. Brankas eksternal, tautan dan berkas asing dapat mencegah penghapusan penuh. Simpan kunci yang diperlukan dan tinjau hasil.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Referensi perintah](commands.md)


Jika Composer sudah menghapus paket, pasang sementara versi yang sama tanpa setup. Jalankan guardian:uninstall, hapus lagi dan bersihkan cache. Tinjau konfigurasi khusus secara manual.
