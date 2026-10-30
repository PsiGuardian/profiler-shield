# Halaman Kesalahan Publik

[Daftar isi dokumentasi](README.md)


## Halaman Kesalahan Publik

Pertahankan halaman aplikasi atau tampilkan HTML netral. Kode status HTTP yang sebenarnya tidak berubah.

Modus yang membiarkan Symfony Mode generik mengabaikan teks yang disesuaikan. hanya mode yang menyesuaikannya menggunakan pesan pengunjung. referensi publik dapat tidak ada, rawak atau tetap; itu bebas dari pesan. HTTP Kod status tetap tidak berubah.

Halaman aplikasi mempertahankan respons 404 situs; pengecualian memakai templat 404 bila tersedia, atau halaman netral. Mode netral memakai pesan standar; mode khusus memakai teks Anda.

Pengecualian 500 anonim mendapat halaman netral. Detail memerlukan identitas Guardian berizin dan GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Templat Twig hilang atau gagal beralih ke tampilan netral; pengontrol galat khusus perlu integrasi tersendiri. JSON dan halaman bisnis berhasil tidak berubah.

| error_mode | Pengaturan |
|---|---|
| native | Halaman aplikasi mempertahankan respons 404 situs; pengecualian memakai templat 404 bila tersedia, atau halaman netral. Mode netral memakai pesan standar; mode khusus memakai teks Anda. |
| generic | HTML 400–599 → Pertahankan halaman aplikasi atau tampilkan HTML netral. Kode status HTTP yang sebenarnya tidak berubah. |
| custom | Hanya tersedia untuk pesan netral khusus. Teks disimpan saat berganti mode. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Hanya tersedia dengan referensi statik yang disesuaikan. |
| error_custom_text | Hanya tersedia untuk pesan netral khusus. Teks disimpan saat berganti mode. |

## Referensi yang ditampilkan

Pesan berbeda atau referensi tetap dapat mengungkap kesalahan yang sebenarnya. Kode HTTP tidak boleh dipalsukan.

Referensi ini berlaku untuk GuardianHalaman 's neutral: secara rawak menghasilkan kode baru, bukan pesan rawak.

Pilihan divalidasi oleh server sebelum disimpan.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Jaringan dan penyebaran

IP Memberi izin yang diterima IPv4, IPv6 dan CIDR Rangkaian - Periksa Symfony Konfigurasi proxy yang dipercaya sebelum memungkinkan. HTTPS Untuk cookies yang aman. Guardian Tidak memperbaiki server web atau database yang terpapar.

Tolak diagnostik bila identitas, peran, sesi, jaringan, jadwal, JIT atau penguncian tidak dapat diperiksa. Browser tidak menentukan kedaluwarsa. Pengumpulan dan penayangan terpisah. Mengingat perangkat bukan autentikasi. Darurat memerlukan identitas, kode terpakai dan audit tahan lama; kegagalan audit kritis mencegah pembukaan. Jangan terapkan sebagian konfigurasi tidak valid. Kontrol opsional tidak memerlukan layanan jauh.
