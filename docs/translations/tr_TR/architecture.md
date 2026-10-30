# Mimari ve güven sınırları

[Belge içindekiler](README.md)


## Mimari ve güven sınırları

Proxy TLS, normalleştirme ve hacimsel korumayı sağlar. Ağ geçidi tanılamayı yönlendiriciden önce denetler; iş rotaları hesap veya oturum okumaz. Guardian kimliği Symfony’den ayrıdır. Özel kasa ve anahtarı birlikte, ortak dizin dışında yedekleyin. Ele geçirilmiş PHP veya sunucu ikisini açığa çıkarabilir. Depolama tek sunuculudur. Başlatma, önbellek veya proxy hataları tüm uygulamayı etkileyebilir.

IP izin listesi kabul eder IPv4, IPv6 ve CIDR İnternet Kontrolü Symfony güvenilir bir proxy yapılandırması, bunu etkinleştirmeden önce. HTTPS Güvenli çerezler için. Guardian Web sunucularını veya veritabanlarını tamir etmez.

Guardian Profil kontrolü ve çökme araç çubuğu. yetkili bir yönetici veya geliştiricinin hesabı gerektirir. MFAve programlar , JIT, ağ kısıtlamaları ve kilitleme hala erişimi reddedebilir. çalınma modu giriş noktalarını gizler; bu kontrolleri değiştirmez.

| PHP / HTTP | Mimari ve güven sınırları |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| oturum | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Güvenlik değişmezleri

Kimlik, rol, oturum, ağ, zamanlama, JIT veya kilit denetlenemiyorsa tanılamayı reddedin. Süre sonuna tarayıcı karar vermez. Toplama ve görüntüleme ayrıdır. Cihaz hatırlama doğrulama değildir. Acil erişim kimlik, tüketilmiş kod ve kalıcı denetim gerektirir; kritik denetim hatası açılmayı önler. Geçersiz yapılandırmayı kısmen uygulamayın. İsteğe bağlı kontroller uzak hizmet gerektirmez.

Kasa ve anahtarı birlikte yedekleyip geri yüklemeyi deneyin. Kayıp anahtar otomatik yeniden oluşturulmaz. Kaldırma sonrası profiler’ı yalnızca özel ortamda denetleyin. Üretim: APP_DEBUG=0, WebProfilerBundle ve toplama kapalı. Yerel denetim zinciri ele geçirilmiş sunucunun bütünlüğünü kanıtlamaz.

## Gazeteler ve Uyarılar

Seviye ve tutma mevcut etkinlikleri belirler, 5000 Girişler. sayfa değiştirmeden önce tür ve hesabı göre filtreleme. uyarılar kaydedilir; nakliye ve teslimatını ayarlayın. yerel bir hash zinciri, tehlikeli bir ev sahibi üzerinde bütünlüğünü garanti etmez.
