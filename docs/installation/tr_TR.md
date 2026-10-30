# ΩGuardian Profiler Shield — Kurulum ve kullanım

PHP 8.3 veya üzeri; Symfony 5.4, 6.4 veya 7.4. Symfony 8.1, PHP 8.4 veya üzerini gerektirir. Composer ve OpenSSL + Tokenizer uzantısı gereklidir. Profiler için WebProfilerBundle kurun.

## 1. Kurulum

composer.json ve bin/console içeren Symfony uygulamasının kök dizininde çalıştırın.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Yapılandırma

```bash
php vendor/bin/guardian setup
```

Yardımcı, bundle’ı dev/test ortamında etkinleştirir, kasayı ve yönetici hesabını hazırlar, önbelleği temizler ve yapılandırmayı denetler. Her adım başarılı olmadan sonrakine geçmeyin. İşlem sırasında uygulamayı özel tutun. Tekrar çalıştırmak mevcut hesapları ve anahtarları korur.

## 3. Paneli açma

```bash
php bin/console guardian:access --env=dev
```

Komut özel panel yolunu gösterir. https://VOTRE-HOTE yerine uygulamanın port dâhil gerçek HTTPS adresini yazın. Kimlik bilgilerini .guardian-shield/initial-admin-credentials.txt dosyasından veya belirtilen kasadan okuyun. İlk girişte parola değiştirilmelidir. Bu dosyayı paylaşmayın.

## 4. Kaldırma

Çalışan süreçleri durdurun ve her adımın başarısını denetleyerek komutları sırayla çalıştırın. Kasa ve anahtarı korunur. Süreçleri yeniden başlatın ve profiler’ı özel bir ortamda denetleyin.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Hesapları ve ayarları da silmek için ilk komutu aşağıdakiyle değiştirip onaylayın. Reddetmek hiçbir şeyi değiştirmez. Haricî kasaları ve özelleştirilmiş dosyaları elle denetleyin; saklanan kasaların anahtarlarını koruyun.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Komut başvurusu

Yardım komutları açıklar. Erişim komutu paneli bulur; tanılama yapılandırmayı denetler. Güvenilir yerel erişim gereklidir.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Komut başvurusu](../translations/tr_TR/commands.md) · [Belge içindekiler](../translations/tr_TR/README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

Kuruluş Private Packagist kullanıyorsa kurulumdan önce bu depoyu yapılandırın ve Composer kimlik bilgilerini kuruluşun talimatlarına göre kaydedin. Token’ları asla Git’e eklemeyin.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
