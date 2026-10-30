# Halaman kesilapan awam

[Kandungan dokumentasi](README.md)


## Halaman kesilapan awam

Kekalkan halaman aplikasi atau paparkan HTML neutral. Kod status HTTP sebenar tidak berubah.

Perbezaan fesyen boleh Symfony membuat kesilapan. mod generik mengabaikan teks yang disesuaikan. hanya mod tersesuaik menggunakan mesej pelawat. rujukan awam boleh tidak hadir, rawak atau tetap; ia bebas daripada mesej. HTTP Kod status tetap tidak berubah.

Halaman aplikasi mengekalkan respons 404 laman; pengecualian menggunakan templat 404 jika tersedia, atau halaman neutral. Mod neutral menggunakan mesej standard; mod tersuai menggunakan teks anda.

Pengecualian 500 tanpa identiti mendapat halaman neutral. Butiran memerlukan identiti Guardian dibenarkan dan GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Templat Twig tiada atau gagal beralih ke paparan neutral; pengawal ralat tersuai memerlukan integrasi sendiri. JSON dan halaman perniagaan berjaya tidak berubah.

| error_mode | Tetapan |
|---|---|
| native | Halaman aplikasi mengekalkan respons 404 laman; pengecualian menggunakan templat 404 jika tersedia, atau halaman neutral. Mod neutral menggunakan mesej standard; mod tersuai menggunakan teks anda. |
| generic | HTML 400–599 → Kekalkan halaman aplikasi atau paparkan HTML neutral. Kod status HTTP sebenar tidak berubah. |
| custom | Hanya tersedia untuk mesej neutral tersuai. Teks dikekalkan apabila bertukar mod. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Hanya boleh didapati dengan rujukan statik yang disesuaikan. |
| error_custom_text | Hanya tersedia untuk mesej neutral tersuai. Teks dikekalkan apabila bertukar mod. |

## Rujukan dipaparkan

Mesej berlainan atau rujukan tetap boleh mendedahkan ralat sebenar. Kod HTTP tidak boleh dipalsukan.

Rujukan ini terpakai kepada Guardian's halaman neutral: secara rawak menghasilkan kod baru, bukan mesej rawak.

Pilihan disemak pada pelayan sebelum disimpan.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Rangkaian dan penyebaran

Pihak IP boleh menerima IPv4, IPv6 dan CIDR Rangkaian : Periksa Symfony konfigurasi proxy yang dipercayai sebelum membolehkannya. HTTPS untuk cookies yang selamat. Guardian tidak memperbaiki pelayan web atau pangkalan data yang terdedah.

Tolak diagnostik jika identiti, peranan, sesi, rangkaian, jadual, JIT atau kunci tidak boleh disemak. Pelayar tidak menentukan tamat tempoh. Pengumpulan dan paparan berasingan. Mengingati peranti bukan pengesahan. Kecemasan memerlukan identiti, kod digunakan dan audit berkekalan; kegagalan audit kritikal menghalang pembukaan. Jangan gunakan sebahagian konfigurasi tidak sah. Kawalan pilihan tidak memerlukan perkhidmatan jauh.
