# Penghapusan dan data

[Kandungan dokumentasi](README.md)

Menyediakan pembuangan fail yang terbukti dijana Guardian. Mengekalkan akaun, tetapan, log dan kunci. Hentikan worker, jalankan sebelum composer remove, kosongkan cache dev dan mulakan semula. Semak fail tersuai atau tanpa bukti asal.

--purge meminta pemadaman data dengan pengesahan lalai menolak. --force memerlukan --purge dan mengesahkan pemusnahan; wajib dengan --no-interaction. Peti luaran, pautan dan fail tidak dikenali boleh menghalang pemadaman lengkap. Simpan kunci diperlukan dan semak hasil.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Sandarkan peti simpanan dan kunci bersama serta uji pemulihan. Kunci hilang tidak dijana semula automatik. Selepas penyingkiran, semak profiler hanya secara peribadi. Produksi: APP_DEBUG=0, WebProfilerBundle dan pengumpulan dimatikan. Rantaian audit setempat tidak membuktikan integriti hos terjejas.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Rujukan arahan

--purge meminta pemadaman data dengan pengesahan lalai menolak. --force memerlukan --purge dan mengesahkan pemusnahan; wajib dengan --no-interaction. Peti luaran, pautan dan fail tidak dikenali boleh menghalang pemadaman lengkap. Simpan kunci diperlukan dan semak hasil.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Rujukan arahan](commands.md)


Jika Composer sudah membuang pakej, pasang sementara versi sama tanpa setup. Jalankan guardian:uninstall, buang semula dan kosongkan cache. Semak konfigurasi tersuai secara manual.
