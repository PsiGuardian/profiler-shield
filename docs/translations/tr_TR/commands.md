# Komut başvurusu

[Belge içindekiler](README.md)

Symfony kök dizininde güvenilir bir terminalden çalıştırın. Örnekler dev kullanır. Parolaları, anahtarları veya kurtarma kodlarını argümanlara, günlüklere veya Git’e koymayın.

## Komutları keşfetme ve yardım


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev ve --no-interaction Symfony seçenekleridir. --no-interaction sır sağlamaz; etkileşimli parola komutlarında kullanmayın. --help sözdizimi, -v ayrıntı gösterir. Kod 0 bildirilen sonucu onaylar; --no-admin kullanılabilir portal olmadan başarılı olabilir.

## `vendor/bin/guardian`

Bundle’ı dev/test içinde etkinleştirir, önbelleği temizler, guardian:setup --automatic çalıştırır, yeniden temizleyip guardian:doctor ve guardian:access çalıştırır. İlk hatada durur. Tekrar hesap ve anahtarları korur. APP_ENV=prod reddedilir, proc_open gerekir.

Argümansız, --help, -h veya help yalnızca yardım gösterir. Tek işlem setup olup ek seçenek kabul etmez.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Komut başvurusu |
|---|---|
| [`guardian:setup`](#guardiansetup) | Özel yolu, anahtarı, kasayı, Symfony dosyalarını ve gerekiyorsa yöneticiyi hazırlar. |
| [`guardian:install`](#guardianinstall) | Kasayı başlatır, --username ile yönetici oluşturur; varsayılan admin. |
| [`guardian:access`](#guardianaccess) | Panel yolunu ve ilk kimlik bilgilerinin yerini gösterir, parolayı göstermez. |
| [`guardian:doctor`](#guardiandoctor) | Kasayı, izinleri, anahtarı, rotayı, etkin yöneticiyi ve ayarları denetler. |
| [`guardian:password:reset`](#guardianpasswordreset) | Mevcut username hesabının parolasını etkin ilkeye uygun gizli girişle değiştirir, tüm oturumlarını iptal eder ve kaydeder. |
| [`guardian:mfa:reset`](#guardianmfareset) | username TOTP sırrını ve sayacını siler, oturumları iptal eder ve kaydeder. |
| [`guardian:login:unlock`](#guardianloginunlock) | username sayaçlarını sıfırlar. |
| [`guardian:policy:reset`](#guardianpolicyreset) | IP listesini boşaltır; zamanlamayı, JIT’yi ve uzak acil erişimi kapatır. |
| [`guardian:lockdown`](#guardianlockdown) | on tanılamayı kapatır ve tüm oturumları siler; iş sayfaları çalışır. |
| [`guardian:emergency`](#guardianemergency) | Mevcut username için 5 tek kullanımlık kod üretip eskileri değiştirir. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Bekleyen uyarıları PHP mail() ile alert_email adresine gönderir. |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Kullanıcı parolası yerine sentetik sırla hash doğrulamasını ölçer. |
| [`guardian:uninstall`](#guardianuninstall) | Guardian tarafından üretildiği kanıtlanan dosyaların kaldırılmasını hazırlar. |

## `guardian:setup`

Özel yolu, anahtarı, kasayı, Symfony dosyalarını ve gerekiyorsa yöneticiyi hazırlar. Hesapları korur. Üretim ortamını reddeder. Doğrudan çalıştırdıktan sonra önbelleği temizleyip denetimleri çalıştırın.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic admin ve özel 0600 dosyada bir sır üretir. --path 25–181 karakterlik yol seçer, mevcut yolu değiştirmez. --no-admin yönetici oluşturmayı atlar; etkin hesap yoksa portal kullanılamaz. --username etkileşimli adı önerir, varsayılan admin; otomatik hesabı yeniden adlandırmaz.

```text
--path: ^/[a-zA-Z0-9/_-]{24,180}$
/_profiler; /_wdt → ∅
```

```bash
php bin/console guardian:setup --username=operator --env=dev
php bin/console cache:clear --env=dev
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## `guardian:install`

Kasayı başlatır, --username ile yönetici oluşturur; varsayılan admin. Etkin ilkeye uygun gizli parola girişi ister. Mevcut hesabı reddeder. Rota veya Symfony yapılandırması oluşturmaz; normalde tam yardımcıyı kullanın.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Kullanıcı Adı : 3 için 64 Alfanumerik karakterler -

## `guardian:access`

Panel yolunu ve ilk kimlik bilgilerinin yerini gösterir, parolayı göstermez. --base-url gerçek HTTPS kökenini, isteğe bağlı portla, kimlik bilgisi, sorgu veya parça olmadan kabul eder. Örnek sunucuyu değiştirin.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Kasayı, izinleri, anahtarı, rotayı, etkin yöneticiyi ve ayarları denetler. Sorunda çıkış kodu 1. PHP CLI ve PHP-FPM saat dilimleri farklı olabilir. İnternet açıklığını veya proxy kurallarını test etmez.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Mevcut username hesabının parolasını etkin ilkeye uygun gizli girişle değiştirir, tüm oturumlarını iptal eder ve kaydeder. Parolayı argüman olarak vermeyin.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

username TOTP sırrını ve sayacını siler, oturumları iptal eder ve kaydeder. Yeni doğrulayıcı kaydedin; genel MFA gereksinimi sürer ve profiler’ı engelleyebilir.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

username sayaçlarını sıfırlar. --ip ADRES ilgili IPv4/IPv6 kaynağının sayacını da siler; aksi halde ağ engeli kalabilir. --all tüm sayaçları siler. Hesap, parola ve oturumlar değişmez; işlem kaydedilir.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

IP listesini boşaltır; zamanlamayı, JIT’yi ve uzak acil erişimi kapatır. Kimlik doğrulama, MFA, genel kilit ve giriş engelleri kalır. İstenen kısıtlamaları panelde yeniden yapılandırın.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on tanılamayı kapatır ve tüm oturumları siler; iş sayfaları çalışır. off yalnızca genel kilidi kaldırır: yeniden giriş gerekir, diğer ilkeler sürer.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Mevcut username için 5 tek kullanımlık kod üretip eskileri değiştirir. Bir kez gösterilir; sunucu dışında sır yöneticisinde saklayın. Uzak kullanım için acil erişim açık olmalı; doğrulanmış oturum, parola ve gerekçe gerekir. Genel kilidi aşmaz.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Bekleyen uyarıları PHP mail() ile alert_email adresine gönderir. Taşımayı ve zamanlanmış görevi yapılandırıp teslimatı denetleyin. Başarısızlar kuyrukta kalır. Geçersiz alıcı veya gönderim hatasında kod 1.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Kullanıcı parolası yerine sentetik sırla hash doğrulamasını ölçer. --iterations: 1–10, varsayılan 3. Algoritma, süre ve saniyedeki doğrulamaları JSON verir. Tek yerel CPU sürecini ölçer, GPU veya saldırganı değil.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Guardian tarafından üretildiği kanıtlanan dosyaların kaldırılmasını hazırlar. Hesap, ayar, günlük ve anahtarı korur. Worker’ları durdurun, composer remove öncesinde çalıştırın, dev önbelleğini temizleyip yeniden başlatın. Özelleştirilmiş veya kökeni belirsiz dosyaları inceleyin.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge varsayılan ret onayıyla veri silme ister. --force, --purge gerektirir ve yıkımı onaylar; --no-interaction ile zorunludur. Haricî kasalar, bağlantılar ve bilinmeyen dosyalar tam silmeyi önleyebilir. Gerekli anahtarları saklayıp sonucu inceleyin.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Kaldırma ve veriler


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Kasa ve anahtarı birlikte yedekleyip geri yüklemeyi deneyin. Kayıp anahtar otomatik yeniden oluşturulmaz. Kaldırma sonrası profiler’ı yalnızca özel ortamda denetleyin. Üretim: APP_DEBUG=0, WebProfilerBundle ve toplama kapalı. Yerel denetim zinciri ele geçirilmiş sunucunun bütünlüğünü kanıtlamaz.

[Uninstall ve Data](desinstallation.md)
