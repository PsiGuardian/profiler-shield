# Dasar keselamatan

[Kandungan dokumentasi](README.md)

Laporkan kelemahan secara peribadi ke bundle-sec@omegaguardian.fr untuk saluran disulitkan. Tiada rahsia, token aktif atau URL peribadi dalam mesej pertama. Sertakan versi Guardian, Symfony dan PHP, kesan, prasyarat dan penghasilan semula minimum. Penyelenggara mengakui, menyiasat, membaiki secara peribadi, menguji dan menyelaraskan notis serta tampalan.

Jangan sekali-kali menghantar kata laluan, rahsia TOTP, kunci penyulitan atau URL diagnostik melalui e-mel.

Sandarkan peti simpanan dan kunci bersama serta uji pemulihan. Kunci hilang tidak dijana semula automatik. Selepas penyingkiran, semak profiler hanya secara peribadi. Produksi: APP_DEBUG=0, WebProfilerBundle dan pengumpulan dimatikan. Rantaian audit setempat tidak membuktikan integriti hos terjejas.
