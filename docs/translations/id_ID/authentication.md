# Sign-ins dan kata sandi

[Daftar isi dokumentasi](README.md)


## Akun dan pendaftaran pertama

Selamat datang ke Guardian. kata sandi awal Anda adalah sementara. pilih rahasia baru: panel dan diagnostik tetap tertutup sampai Anda melakukannya. file pengesahan awal dihapus setelah validasi.

Guardian Akun terpisah dari akun situs.Akaun awal dan akun baru harus mengubah kata sandi mereka sebelum melanjutkan.Pandangan peran keamanan memungkinkan informasi tanpa akses profil.Mengubah peran atau password membatalkan sesi sebelumnya.

Menyiapkan jalur privat, kunci, brankas, berkas Symfony dan administrator bila belum ada. Mempertahankan akun. Menolak produksi. Setelah pemanggilan langsung, bersihkan cache dan jalankan pemeriksaan.


```bash
php bin/console guardian:access --env=dev
```


| Permintaan kata sandi yang disimpan | Perbedaan Minimum (Unicode karakter yang |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Kebijakan kata sandi

Peraturan ini berlaku untuk penciptaan kata sandi dan perubahan, di web dan dalam CLIMereka tidak mendedahkan atau memeriksa kata sandi yang ada.

Nilai awal : 15 Gunakan kata sandi unik atau manajer password. kategori karakter yang dibutuhkan tidak menjamin kata laluan yang tak terduga.

Pada server ini, Guardian Menggunakan secara otomatis Argon2id untuk kata sandi baru. tetaplah PHP Permohonan terbatas.Syarat permohonan adalah 512 UTF-8 dan bytes.

ini PHP Server tidak menyediakan Argon2id. Guardian Oleh karena itu digunakan bcrypt Menolak kata sandi yang melebihi 72 UTF-8 Tanya administrator sistem Anda untuk memungkinkan Argon2id Dukungan dalam PHP.

Opsi ini segera membatalkan sesi yang ada, termasuk sesi Anda.Akaun harus mengubah kata sandi mereka sebelum mengkonfigurasi Guardian atau membuka diagnosis.

Mengubah kata sandi akun username yang ada melalui masukan tersembunyi sesuai kebijakan, mencabut semua sesinya dan mencatat tindakan. Jangan berikan kata sandi sebagai argumen.

## Lokasi Progresif

Setiap tahap menentukan batas kegagalan, menghitung jendela, durasi lockout dan ulangi jendela. 10 Kegagalan yang menyebabkan a 10-minute lockout. kegagalan berulang dapat memicu tahap berikutnya. cek mencakup kedua akun dan sumber jaringan, termasuk konfirmasi administrator.

Penghitung terenkripsi terpisah per akun ternormalisasi dan asal; IP mentah tidak disimpan. Reservasi atomik mendahului hash dan kedaluwarsa setelah 120 detik. Reservasi terbengkalai dihitung gagal; hasil terlambat tidak membuat sesi. CSRF tidak valid tidak mereservasi. Login berhasil menghapus kegagalan akun, mempertahankan riwayat pengulangan dan asal. Pada 10000 entri aktif, identitas baru ditolak alih-alih menggusur blokir.

Langkah-langkah berjalan dalam urutan.Selepas lockout, kegagalan baru di dalam jendela pengulangan menggunakan langkah berikutnya.Di luar jendela itu, langkah pertama diterapkan lagi.Langkah terakhir berulang.Permintaan yang terhalang tidak memperpanjang keterlambatan. 8 Langkah-langkah pemulihan lokal : guardian:login:unlock --all.

| Tahap | Permintaan kata sandi yang disimpan |
|---|---|
| Kegagalan sebelum lockout | 1–100 |
| Langkah-langkah yang harus dilakukan (minit) | 1–10080 |
| Menghitung jendela (minit) | 1–1440 |
| Mengulangi jendela setelah membuka (minit) | 1–10080 |
| Tahap | 1–8; block[n+1] ≥ block[n] |
| Nilai awal | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Laboratorium Keuangan Penelitian

Bandingkan secara seragam rahasia dari panjang tetap tanpa memasukkan kata sandi yang sebenarnya. tingkat pengertian dan harga perangkat keras adalah asumsi yang dapat disesuaikan.

Kategori ASCII terpisah dan BigInt. Inklusi-eksklusi menghitung ruang tepat; kategori hilang atau panjang kurang menghasilkan nol. Peringkat rata-rata tanpa pengembalian: (N+1)/2. Waktu tunggu mencakup ambang dan tahap akhir berulang. Perkiraan dimatikan jika laju tidak sesuai jendela. Biaya offline adalah jam dikali perkiraan harga per jam.

Dengan alfabet tetap, menuntut kategori mengurangi jumlah rahasia yang mematuhi. memperluas alfabet adalah operasi yang berbeda. waktu lockout menganggap teka-teki berterusan cukup cepat untuk tinggal di dalam jendela terkonfigurasi.

Model ini tidak mengukur kekuatan kata sandi yang dipilih oleh manusia. ia mengecualikan kamus, penggunaan ulang, kebocoran dan strategi sasaran. kunci web tidak melindungi hash dicuri. kadar offline awal adalah ilustratif, bukan referensi. CPU Pengukuran : guardian:password:benchmark.

Nombor-nombor besar ditulis penuh, dengan kelompok angka dan tanpa notasi E. Waktu dan biaya dikelilingi. Bandingkan baris yang sama menggunakan asumsi yang sama.

| Simulasi Alphabet yang tersedia | Rahasia yang mungkin mematuhi |
|---|---|
| ASCII Bawah Kasus | 26 |
| ASCII Tertinggi | 26 |
| ASCII digit yang | 10 |
| ASCII Simbol yang mengecualikan ruang | 32 |
| Kadar online yang diperhitungkan (guesses/s) | 1 / s |
| Kadar offline yang diperhitungkan (hashes/s) | 10000 / s |
| Biaya perkakasan yang diperhitungkan (€/jam) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## pemulihan dan verifikasi

Cadangkan brankas dan kunci bersama lalu uji pemulihan. Kunci hilang tidak dibuat ulang otomatis. Setelah penghapusan, periksa profiler hanya secara privat. Produksi: APP_DEBUG=0, WebProfilerBundle dan pengumpulan dimatikan. Rantai audit lokal tidak membuktikan integritas host yang disusupi.

Mereset penghitung username. --ip ALAMAT juga menghapus penghitung asal IPv4/IPv6 itu; jika tidak, blokir jaringan bisa tersisa. --all menghapus seluruh penghitung. Akun, kata sandi dan sesi tidak berubah; tindakan dicatat.

[Referensi perintah](commands.md)
