# Sign-ins dan kata laluan

[Kandungan dokumentasi](README.md)


## Akaun dan pendaftaran pertama

Selamat datang ke Guardian. kata laluan awal anda adalah sementara. pilih rahsia baru: panel dan diagnostik kekal tertutup sehingga anda melakukannya. fail pengesahan awal dihapuskan selepas kelulusan.

Guardian Akaun terpisah daripada akaun laman. akaun awal dan akaun baru mesti mengubah kata laluan mereka sebelum berterusan. pandangan peranan keselamatan membenarkan maklumat tanpa akses profil. Mengubah peranan atau kata lalai menghalang sesi terdahulu.

Menyediakan laluan peribadi, kunci, peti simpanan, fail Symfony dan pentadbir jika tiada. Mengekalkan akaun sedia ada. Menolak produksi. Selepas panggilan terus, kosongkan cache dan jalankan semakan.


```bash
php bin/console guardian:access --env=dev
```


| Permintaan kata laluan yang disimpan | Minimum pengeluaran (Unicode kepada watak-watak |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Kebijakan kata laluan

Peraturan ini terpakai kepada penciptaan kata laluan dan perubahan, di web dan dalam CLIMereka tidak mendedahkan atau memeriksa kata laluan yang sedia ada.

Nilai awal : 15 menggunakan kata laluan unik atau pengurus kata lalu. kategori watak yang diperlukan tidak menjamin kata lalai yang tidak dapat diramalkan.

Pada pelayan ini, Guardian Penggunaan secara automatik Argon2id untuk kata laluan baru.Selengkapnya PHP Bantuan boleh didayakan. had permohonan adalah 512 UTF-8 daripada bytes.

ini PHP Pelayan tidak menyediakan Argon2id. Guardian Oleh itu, digunakan bcrypt dan menolak kata laluan melebihi 72 UTF-8 meminta pentadbir sistem anda untuk membolehkan Argon2id Sokongan dalam PHP.

Opsyen ini dengan serta-merta membatalkan sesi yang sedia ada, termasuk anda. akaun perlu mengubah kata laluan mereka sebelum mengkonfigurasi Guardian atau membuka diagnostik.

Mengubah kata laluan akaun username sedia ada melalui input tersembunyi mengikut dasar, membatalkan semua sesinya dan merekodkan tindakan. Jangan beri kata laluan sebagai argumen.

## Pencahayaan Progresif

Setiap peringkat mendefinisikan had kegagalan, mengira tetingkap, tempoh lockout dan ulangi tetingap. 10 kegagalan yang menyebabkan a 10-minute lockout. kegagalan berulang boleh mencetuskan fasa seterusnya. cek meliputi kedua-dua akaun dan asal rangkaian, termasuk pengesahan pentadbir.

Pembilang disulitkan berasingan bagi akaun ternormal dan asal; IP mentah tidak disimpan. Tempahan atomik mendahului hash dan luput selepas 120 saat. Tempahan terbengkalai dikira gagal; hasil lewat tidak mencipta sesi. CSRF tidak sah tidak menempah. Log masuk berjaya memadam kegagalan akaun tetapi mengekalkan sejarah pengulangan dan asal. Pada 10000 entri aktif, identiti baharu ditolak tanpa membuang sekatan.

Langkah-langkah berjalan dalam perintah.Selepas lockout, kegagalan baru di dalam tetingkap pengulangan menggunakan langkah seterusnya.Di luar tetingap itu, langkah pertama digunakan lagi.Langkah terakhir mengulangi.Permintaan yang disekat tidak memperpanjang keterlambatan. 8 Tahap pemulihan tempatan : guardian:login:unlock --all.

| Tahap | Permintaan kata laluan yang disimpan |
|---|---|
| kegagalan sebelum lockout | 1–100 |
| Perbezaan antara minit (minit) | 1–10080 |
| Pengiraan tetingkap (minit) | 1–1440 |
| Mengulangi tetingkap selepas membuka (minit) | 1–10080 |
| Tahap | 1–8; block[n+1] ≥ block[n] |
| Nilai awal | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Makmal Kos Pencarian Penuh

Bandingkan secara seragam rahsia kebetulan panjang tetap tanpa memasukkan kata laluan sebenar. kadar pengesahan dan harga perkakasan adalah asumsi yang boleh disesuaikan. tiada percubaan pendaftaran dihantar.

Kategori ASCII berasingan dan BigInt. Rangkuman-pengecualian mengira ruang tepat; kategori tiada atau panjang tidak cukup menghasilkan sifar. Kedudukan purata tanpa penggantian ialah (N+1)/2. Masa menunggu mengambil kira ambang dan tahap akhir berulang. Anggaran dimatikan jika kadar tidak sesuai dengan tetingkap. Kos luar talian ialah jam didarab harga sejam andaian.

Dengan alfat tetap, memerlukan kategori mengurangkan bilangan rahsia yang mematuhi. memperluaskan alfat adalah operasi yang berbeza. masa lockout menganggap teka-teki berterusan cukup cepat untuk kekal di dalam tetingkap yang dikonfigurasi.

Model ini tidak mengukur kekuatan kata laluan yang dipilih oleh manusia. ia mengecualikan kamus, penggunaan semula, kebocoran dan strategi yang disasarkan. kunci web tidak melindungi hash dicuri. kadar offline awal adalah ilustratif, bukan rujukan. CPU Pengukuran : guardian:password:benchmark.

Nombor besar ditulis dalam penuh, dengan kumpulan digit dan tanpa notasi E. Waktu dan kos dikelilingi. Bandingkan baris yang sama menggunakan asumsi yang sama.

| Simulasi Alphabet yang tersedia | Rahsia yang mungkin sesuai |
|---|---|
| ASCII Kasus yang rendah | 26 |
| ASCII Tertinggi | 26 |
| ASCII digit yang | 10 |
| ASCII Simbol yang mengecualikan ruang | 32 |
| Kadar yang diambil dalam talian (guesses/s) | 1 / s |
| Kadar offline yang dipercayai (hashes/s) | 10000 / s |
| Kos perkakasan yang dianggarkan (€/jam) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## pemulihan dan pengesahan

Sandarkan peti simpanan dan kunci bersama serta uji pemulihan. Kunci hilang tidak dijana semula automatik. Selepas penyingkiran, semak profiler hanya secara peribadi. Produksi: APP_DEBUG=0, WebProfilerBundle dan pengumpulan dimatikan. Rantaian audit setempat tidak membuktikan integriti hos terjejas.

Menetapkan semula pembilang username. --ip ALAMAT juga memadam pembilang asal IPv4/IPv6 itu; jika tidak, sekatan rangkaian mungkin kekal. --all memadam semua pembilang. Akaun, kata laluan dan sesi tidak berubah; tindakan direkodkan.

[Rujukan arahan](commands.md)
