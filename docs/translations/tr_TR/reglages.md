# Ayarlar

[Belge içindekiler](README.md)


## Ek korumalar

Kontroller ilgili ayarlarla gruplandırılır. Devre dışı seçenekler soluk görünür, değerleri korunur.

Guardian Profil kontrolü ve çökme araç çubuğu. yetkili bir yönetici veya geliştiricinin hesabı gerektirir. MFAve programlar , JIT, ağ kısıtlamaları ve kilitleme hala erişimi reddedebilir. çalınma modu giriş noktalarını gizler; bu kontrolleri değiştirmez.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Profil | Ayarlar |
|---|---|
| Yalnız · esnek | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| · Ekip geliştirilmiş | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| Hassas · Sınırlı | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Parametre | Ayarlar | İlk değer |
|---|---|---|
| `stealth` | Yetkisiz tanılama yollarında 403 yerine 404 döndür. | `true` |
| `mfa` | Zorunlu MFA'yı açmadan önce tüm kullanıcılar TOTP kurmalıdır. | `false` |
| `idle_minutes` | Oturumun sona ermesinden önce etkinlik olmayan dakika. | `60` |
| `absolute_hours` | Etkinlikten bağımsız saat. | `8` |
| `remember_days` | Gün. Oturum açma veya çalışma saatleri politikasının yerini tutmaz. | `14` |
| `schedule_enabled` | Tanılama araçlarının ne zaman kullanılabileceğini seçin. Uygulama sayfaları erişilebilir kalır. | `false` |
| `weekdays` | Tanılamaya izin verilen günleri seçin. | `1,2,3,4,5` |
| `start` | Açılış | `09:00` |
| `end` | Kapanış | `18:00` |
| `timezone` | Bu sayfayı sunan PHP işleminden algılandı. PHP CLI, PHP-FPM ve işletim sistemi farklı saat dilimleri kullanabilir. Kaydetmeden önce sunucu ayarlarını kontrol edin. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | Özel URL mesai dışında 404 döndürür. Kurtarma yalnızca yerel CLI ile mümkündür. | `false` |
| `jit_enabled` | Profiler'ı yalnızca istenen tanılama penceresinde aç. | `false` |
| `jit_minutes` | Dakika, yalnızca etkin olduğunda kullanılır. | `30` |
| `ip_allowlist` | Uyarı: hatalar erişimi kilitleyebilir. Yerel kurtarma: guardian:policy:reset. | `∅` |
| `audit_level` | Denetim ayrıntısını ve yerel saklama süresini seçin. | `standard` |
| `audit_retention_days` | Günlük temizliğine kadar gün. | `30` |
| `alert_email` | Acil uyarıları alır. Erişim e-posta teslimatına bağlı olmamalıdır. | `∅` |
| `emergency_enabled` | Tek kullanımlık kod ve denetim kaydıyla 30 dakikalık istisna ver. | `false` |
| `login_rules` | Değişken : 10 Başarısızlıklar, sonra bir 10-minute lockout. sayılar hem hesap ve ağ kaynağı için geçerlidir, şifre doğrulamaları da dahil olmak üzere. | `10 / 10 / 30 / 30` |
| `password_policy` | Bu kurallar şifre oluşturma ve değişiklikler için geçerlidir, web ve CLIMevcut şifreleri tespit etmez veya incelemezler. | `min_length=15` |
| `error_mode` | Uygulama sayfaları: mevcut sitenin korunması 404 Cevaplar; istisnalar kullanılır 404 Şablon mevcut olduğunda, aksi takdirde tarafsız bir sayfa. Neutral: standart mesaj. Özelleştirilmiş: metninizi kullanın. | `native` |
| `error_reference` | Referanslar için geçerlidir Guardian’s tarafsız sayfalar: rastgele yeni bir kod oluşturur, rastgeli bir mesaj değil. | `none` |
| `error_reference_text` | Sadece özel bir statik referans ile mevcuttur. | `∅` |
| `error_custom_text` | Yalnızca özel nötr mesaj modunda kullanılabilir. Mod değiştirilirken metin korunur. | `∅ → custom` |

Planlama, seçilen günler, kapsayıcı bir başlangıç saati ve özel bir son saati sağlar. kaydedilen zaman aralığı yerel saatleri belirler. Gece aralıkları kabul edilmez. portalın saat dışında gizlenmesi uzaktan acil erişim engeller; yerel kurtarma komutlarını mevcut tutar.

Idle timeout, mutlak ömür ve cihaz hatıra farklı amaçlara hizmet eder. bir cihazı hatırlamak zamanlamaları aşmaz veya JIT. iptal sunucu üzerinde ilgili oturumları sona erdirir. günlük eylemleri atamak için bireysel hesaplar kullanın.

JIT yetkili bir oturumdan geçici bir pencere etkinleştirilmesini gerektirir. acil erişim, etkinleştirildiğinde, bir şifre, tek kullanım kodu ve bir neden gerektirür. küresel kilitlemeyi aşmaz. portal ulaşılamazsa yerel komutlar gereklidir.

IP izin listesi kabul eder IPv4, IPv6 ve CIDR İnternet Kontrolü Symfony güvenilir bir proxy yapılandırması, bunu etkinleştirmeden önce. HTTPS Güvenli çerezler için. Guardian Web sunucularını veya veritabanlarını tamir etmez.

Seviye ve tutma mevcut etkinlikleri belirler, 5000 Girişler. sayfa değiştirmeden önce tür ve hesabı göre filtreleme. uyarılar kaydedilir; nakliye ve teslimatını ayarlayın. yerel bir hash zinciri, tehlikeli bir ev sahibi üzerinde bütünlüğünü garanti etmez.

Hesap oluşturma işaretleri kaydedilen politikayı açıklıyor. değişiklikler yeni şifre için geçerlidir; mevcut hesaplar için zorunlu yenileme seçin. Guardian Seçim Argon2id Ne zaman PHP Öyleyse, aksi takdirde bcrypt.

Tüm kaydedilen etkinlikleri tür ve hesap olarak filtreleyin. en yeni etkinlikler öncelikle, sayfalarda görünür 50Scrolling bir sonraki sayfayı yükleyemez.

## Ortam


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


Proxy TLS, normalleştirme ve hacimsel korumayı sağlar. Ağ geçidi tanılamayı yönlendiriciden önce denetler; iş rotaları hesap veya oturum okumaz. Guardian kimliği Symfony’den ayrıdır. Özel kasa ve anahtarı birlikte, ortak dizin dışında yedekleyin. Ele geçirilmiş PHP veya sunucu ikisini açığa çıkarabilir. Depolama tek sunuculudur. Başlatma, önbellek veya proxy hataları tüm uygulamayı etkileyebilir.

Kasa ve anahtarı birlikte yedekleyip geri yüklemeyi deneyin. Kayıp anahtar otomatik yeniden oluşturulmaz. Kaldırma sonrası profiler’ı yalnızca özel ortamda denetleyin. Üretim: APP_DEBUG=0, WebProfilerBundle ve toplama kapalı. Yerel denetim zinciri ele geçirilmiş sunucunun bütünlüğünü kanıtlamaz.

## İyileşme ve doğrulama

IP listesini boşaltır; zamanlamayı, JIT’yi ve uzak acil erişimi kapatır. Kimlik doğrulama, MFA, genel kilit ve giriş engelleri kalır. İstenen kısıtlamaları panelde yeniden yapılandırın.

on tanılamayı kapatır ve tüm oturumları siler; iş sayfaları çalışır. off yalnızca genel kilidi kaldırır: yeniden giriş gerekir, diğer ilkeler sürer.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
