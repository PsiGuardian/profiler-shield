# Arsitektur dan batas kepercayaan

[Daftar isi dokumentasi](README.md)


## Arsitektur dan batas kepercayaan

Proksi menangani TLS, normalisasi dan perlindungan volume lalu lintas. Gerbang memeriksa diagnostik sebelum perutean; rute bisnis tidak membaca akun atau sesi. Identitas Guardian terpisah dari Symfony. Cadangkan brankas dan kunci bersama di luar direktori publik. PHP atau host yang disusupi bisa mengungkap keduanya. Penyimpanan untuk satu host. Kesalahan startup, cache atau proksi bisa memengaruhi seluruh aplikasi.

IP Memberi izin yang diterima IPv4, IPv6 dan CIDR Rangkaian - Periksa Symfony Konfigurasi proxy yang dipercaya sebelum memungkinkan. HTTPS Untuk cookies yang aman. Guardian Tidak memperbaiki server web atau database yang terpapar.

Guardian mengendalikan profil dan toolbar debug.Ada akun administrator atau pengembang yang diberi izin diperlukan. MFAdengan jadwal, JIT, pembatasan jaringan dan penguncian masih dapat menolak akses. mode pencurian menyembunyikan titik masuk; itu tidak menggantikan kontrol ini.

| PHP / HTTP | Arsitektur dan batas kepercayaan |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| Sesi | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Invarian keamanan

Tolak diagnostik bila identitas, peran, sesi, jaringan, jadwal, JIT atau penguncian tidak dapat diperiksa. Browser tidak menentukan kedaluwarsa. Pengumpulan dan penayangan terpisah. Mengingat perangkat bukan autentikasi. Darurat memerlukan identitas, kode terpakai dan audit tahan lama; kegagalan audit kritis mencegah pembukaan. Jangan terapkan sebagian konfigurasi tidak valid. Kontrol opsional tidak memerlukan layanan jauh.

Cadangkan brankas dan kunci bersama lalu uji pemulihan. Kunci hilang tidak dibuat ulang otomatis. Setelah penghapusan, periksa profiler hanya secara privat. Produksi: APP_DEBUG=0, WebProfilerBundle dan pengumpulan dimatikan. Rantai audit lokal tidak membuktikan integritas host yang disusupi.

## Akhbar dan peringatan

Tingkat dan retensi menentukan peristiwa yang tersedia, hingga 5000 entri. Saring menurut jenis dan akun sebelum berpindah halaman. Peringatan masuk antrean; atur transportasi dan pengirimannya. Rantai hash lokal tidak menjamin integritas pada host yang disusupi.
