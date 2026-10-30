# Kebijakan keamanan

[Daftar isi dokumentasi](README.md)

Laporkan kerentanan secara privat ke bundle-sec@omegaguardian.fr untuk menyepakati saluran terenkripsi. Tanpa rahasia, token aktif atau URL privat pada pesan pertama. Sertakan versi Guardian, Symfony dan PHP, dampak, prasyarat dan reproduksi minimal. Pengelola mengakui, menyelidiki, memperbaiki privat, menguji serta mengoordinasikan pemberitahuan dan patch.

Jangan pernah mengirim kata sandi, rahasia TOTP, kunci enkripsi, atau URL diagnostik melalui email.

Cadangkan brankas dan kunci bersama lalu uji pemulihan. Kunci hilang tidak dibuat ulang otomatis. Setelah penghapusan, periksa profiler hanya secara privat. Produksi: APP_DEBUG=0, WebProfilerBundle dan pengumpulan dimatikan. Rantai audit lokal tidak membuktikan integritas host yang disusupi.
