# Seni bina dan sempadan kepercayaan

[Kandungan dokumentasi](README.md)


## Seni bina dan sempadan kepercayaan

Proksi mengendalikan TLS, normalisasi dan perlindungan volum trafik. Gerbang menyemak diagnostik sebelum penghalaan; laluan perniagaan tidak membaca akaun atau sesi. Identiti Guardian berasingan daripada Symfony. Sandarkan peti simpanan dan kunci bersama di luar direktori awam. PHP atau hos terjejas boleh mendedahkan kedua-duanya. Storan untuk satu hos. Ralat permulaan, cache atau proksi boleh menjejaskan seluruh aplikasi.

Pihak IP boleh menerima IPv4, IPv6 dan CIDR Rangkaian : Periksa Symfony konfigurasi proxy yang dipercayai sebelum membolehkannya. HTTPS untuk cookies yang selamat. Guardian tidak memperbaiki pelayan web atau pangkalan data yang terdedah.

Guardian mengawal profil dan bar alat debug. akaun pentadbir atau pemaju yang diberi kuasa diperlukan. MFAdalam jadual, JIT, pembatasan rangkaian dan penguncian masih boleh menolak akses. mod pencurian menyembunyikan titik kemasukan; ia tidak menggantikan pemeriksaan ini.

| PHP / HTTP | Seni bina dan sempadan kepercayaan |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| Perhimpunan | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Invarian keselamatan

Tolak diagnostik jika identiti, peranan, sesi, rangkaian, jadual, JIT atau kunci tidak boleh disemak. Pelayar tidak menentukan tamat tempoh. Pengumpulan dan paparan berasingan. Mengingati peranti bukan pengesahan. Kecemasan memerlukan identiti, kod digunakan dan audit berkekalan; kegagalan audit kritikal menghalang pembukaan. Jangan gunakan sebahagian konfigurasi tidak sah. Kawalan pilihan tidak memerlukan perkhidmatan jauh.

Sandarkan peti simpanan dan kunci bersama serta uji pemulihan. Kunci hilang tidak dijana semula automatik. Selepas penyingkiran, semak profiler hanya secara peribadi. Produksi: APP_DEBUG=0, WebProfilerBundle dan pengumpulan dimatikan. Rantaian audit setempat tidak membuktikan integriti hos terjejas.

## Akhbar dan peringatan

Tahap dan pengekalan menentukan peristiwa yang tersedia, sehingga 5000 entries.Filter mengikut jenis dan akaun sebelum mengubah halaman.Pemberitahuan dikurangkan; mengkonfigurasi pengangkutan dan penghantaran mereka.Sebuah rantaian hash tempatan tidak menjamin integriti pada host yang terjejas.
