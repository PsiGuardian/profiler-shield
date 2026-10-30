# Rujukan arahan

[Kandungan dokumentasi](README.md)

Jalankan dari akar Symfony dalam terminal dipercayai. Contoh menggunakan dev. Jangan letakkan kata laluan, kunci atau kod pemulihan dalam argumen, log atau Git.

## Menemui arahan dan bantuan


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev dan --no-interaction ialah pilihan Symfony. --no-interaction tidak memberikan rahsia dan tidak sesuai untuk kata laluan interaktif. --help menunjukkan sintaks; -v menambah butiran. Kod 0 mengesahkan hasil yang dilaporkan; --no-admin boleh berjaya tanpa portal yang boleh digunakan.

## `vendor/bin/guardian`

Mengaktifkan bundle dalam dev/test, mengosongkan cache, menjalankan guardian:setup --automatic, mengosongkan semula, kemudian menjalankan guardian:doctor dan guardian:access. Berhenti pada ralat pertama. Ulangan mengekalkan akaun dan kunci. Menolak APP_ENV=prod dan memerlukan proc_open.

Tanpa argumen, --help, -h atau help hanya memaparkan bantuan. setup satu-satunya tindakan dan tidak menerima pilihan tambahan.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Rujukan arahan |
|---|---|
| [`guardian:setup`](#guardiansetup) | Menyediakan laluan peribadi, kunci, peti simpanan, fail Symfony dan pentadbir jika tiada. |
| [`guardian:install`](#guardianinstall) | Memulakan peti simpanan dan mencipta pentadbir dengan --username, lalai admin. |
| [`guardian:access`](#guardianaccess) | Memaparkan laluan panel dan lokasi kelayakan awal, bukan kata laluan. |
| [`guardian:doctor`](#guardiandoctor) | Menyemak peti simpanan, izin, kunci, laluan, pentadbir aktif dan konfigurasi. |
| [`guardian:password:reset`](#guardianpasswordreset) | Mengubah kata laluan akaun username sedia ada melalui input tersembunyi mengikut dasar, membatalkan semua sesinya dan merekodkan tindakan. |
| [`guardian:mfa:reset`](#guardianmfareset) | Memadam rahsia dan pembilang TOTP username, membatalkan sesi dan merekodkan tindakan. |
| [`guardian:login:unlock`](#guardianloginunlock) | Menetapkan semula pembilang username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Mengosongkan senarai IP dan mematikan jadual, JIT serta kecemasan jauh. |
| [`guardian:lockdown`](#guardianlockdown) | on menutup diagnostik dan memadam semua sesi; halaman perniagaan kekal tersedia. |
| [`guardian:emergency`](#guardianemergency) | Menjana 5 kod sekali guna untuk username sedia ada, menggantikan kod lama. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Menghantar amaran beratur ke alert_email melalui PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Mengukur pengesahan hash dengan rahsia sintetik, bukan kata laluan pengguna. |
| [`guardian:uninstall`](#guardianuninstall) | Menyediakan pembuangan fail yang terbukti dijana Guardian. |

## `guardian:setup`

Menyediakan laluan peribadi, kunci, peti simpanan, fail Symfony dan pentadbir jika tiada. Mengekalkan akaun sedia ada. Menolak produksi. Selepas panggilan terus, kosongkan cache dan jalankan semakan.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic mencipta admin dan rahsia dalam fail peribadi 0600. --path memilih laluan 25–181 aksara tanpa menggantikan yang sedia ada. --no-admin melangkau penciptaan pentadbir; tanpa akaun aktif portal tidak boleh digunakan. --username mencadangkan nama interaktif, lalai admin; tidak menamakan semula akaun automatik.

```text
--path: ^/[a-zA-Z0-9/_-]{24,180}$
/_profiler; /_wdt → ∅
```

```bash
php bin/console guardian:setup --username=operator --env=dev
php bin/console cache:clear --env=dev
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## `guardian:install`

Memulakan peti simpanan dan mencipta pentadbir dengan --username, lalai admin. Meminta kata laluan tersembunyi mengikut dasar aktif. Menolak akaun sedia ada. Tidak mencipta laluan atau konfigurasi Symfony; biasanya gunakan pembantu lengkap.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Nama Pengguna : 3 kepada 64 Perkataan-perkataan alfanumerik

## `guardian:access`

Memaparkan laluan panel dan lokasi kelayakan awal, bukan kata laluan. --base-url menerima asal HTTPS sebenar dengan port pilihan, tanpa kelayakan, pertanyaan atau fragmen. Gantikan hos contoh.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Menyemak peti simpanan, izin, kunci, laluan, pentadbir aktif dan konfigurasi. Kod keluar 1 jika ada masalah. Zon masa PHP CLI dan PHP-FPM boleh berbeza. Tidak menguji pendedahan Internet atau aturan proksi.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Mengubah kata laluan akaun username sedia ada melalui input tersembunyi mengikut dasar, membatalkan semua sesinya dan merekodkan tindakan. Jangan beri kata laluan sebagai argumen.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Memadam rahsia dan pembilang TOTP username, membatalkan sesi dan merekodkan tindakan. Daftarkan pengesah baharu; keperluan MFA global kekal dan boleh menghalang profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Menetapkan semula pembilang username. --ip ALAMAT juga memadam pembilang asal IPv4/IPv6 itu; jika tidak, sekatan rangkaian mungkin kekal. --all memadam semua pembilang. Akaun, kata laluan dan sesi tidak berubah; tindakan direkodkan.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Mengosongkan senarai IP dan mematikan jadual, JIT serta kecemasan jauh. Pengesahan, MFA, kunci global dan sekatan log masuk kekal. Tetapkan semula sekatan yang diperlukan dalam panel.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on menutup diagnostik dan memadam semua sesi; halaman perniagaan kekal tersedia. off hanya membuka kunci global: perlu log masuk semula dan dasar lain masih terpakai.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Menjana 5 kod sekali guna untuk username sedia ada, menggantikan kod lama. Dipaparkan sekali; simpan di luar pelayan dalam pengurus rahsia. Penggunaan jauh memerlukan kecemasan aktif, sesi disahkan, kata laluan dan sebab. Tidak memintas kunci global.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Menghantar amaran beratur ke alert_email melalui PHP mail(). Konfigurasikan pengangkutan dan tugas berjadual, kemudian semak penghantaran sebenar. Kegagalan kekal dalam baris gilir. Kod 1 untuk penerima tidak sah atau kegagalan penghantaran.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Mengukur pengesahan hash dengan rahsia sintetik, bukan kata laluan pengguna. --iterations menerima 1–10, lalai 3. JSON dengan algoritma, masa dan pengesahan sesaat. Mengukur satu proses CPU setempat, bukan GPU atau penyerang.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Menyediakan pembuangan fail yang terbukti dijana Guardian. Mengekalkan akaun, tetapan, log dan kunci. Hentikan worker, jalankan sebelum composer remove, kosongkan cache dev dan mulakan semula. Semak fail tersuai atau tanpa bukti asal.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge meminta pemadaman data dengan pengesahan lalai menolak. --force memerlukan --purge dan mengesahkan pemusnahan; wajib dengan --no-interaction. Peti luaran, pautan dan fail tidak dikenali boleh menghalang pemadaman lengkap. Simpan kunci diperlukan dan semak hasil.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Nyahpasang dan data


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Sandarkan peti simpanan dan kunci bersama serta uji pemulihan. Kunci hilang tidak dijana semula automatik. Selepas penyingkiran, semak profiler hanya secara peribadi. Produksi: APP_DEBUG=0, WebProfilerBundle dan pengumpulan dimatikan. Rantaian audit setempat tidak membuktikan integriti hos terjejas.

[Penghapusan dan data](desinstallation.md)
