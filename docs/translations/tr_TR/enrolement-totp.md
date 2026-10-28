# İki faktörlü kimlik doğrulama

[Belge içindekiler](README.md)


## İki faktörlü kimlik doğrulama

Guardian hesabınızı harici QR hizmetleri olmadan geçici kodlarla koruyun.

Her kişi kendi kayıtlarını yapar TOTP gizli ve bir kod doğrulamaktadır. talep MFA bloklar profil erişimi kayıtlı olmayan hesaplar için. QR Kodu portal için yerel kalır. tüm ekip için bu koruma gerektirmeden önce idari iyileşmeyi planlayın.

## 1. Kimliğinizi doğrulayın

Guardian'a girişte kullandığınız parolayı girin. Bu kurulum için yalnızca bir kez istenir.

## 2. Bu QR kodu kimlik doğrulayıcınızla tarayın

Guardian QR kodunu yerel olarak oluşturur; dış hizmetlere istek göndermez.

## 3. Altı haneli kodu doğrulayın

Uygulamadaki kodu girin. İkinci parola gerekmez.

## Gizlilik: web sitesi URL'sini girmeyin

Uygulama ad veya site isterse yalnızca “ΩGuardian” ya da tarafsız bir ad girin. Symfony örneğinizin URL'sini yazmayın. Çevrimiçi QR oluşturucularını kullanmayın: gizli anahtarınızı alırlar.

Gizli tutun: TOTP kodlarınız bununla oluşturulabilir. Paylaşmayın, ekran görüntüsü almayın.

Geçici sır 10 dakika veya 5 yanlış koddan sonra biter; yeniden başlayın. Yanlış kodda form korunur, parola tekrar istenmez. Onay sayacı tekrar kullanımını önlemek için tüketilir. SVG QR, bacon/bacon-qr-code ve ext-iconv kullanır; GD, XMLWriter veya ağ gerekmez. Elle giriş sürer. otpauth URI tarafsız sağlayıcı ve hesabı içerir, site alan adını içermez. Sırrı ve özel URL’yi paylaşmayın.

## İyileşme ve doğrulama

username TOTP sırrını ve sayacını siler, oturumları iptal eder ve kaydeder. Yeni doğrulayıcı kaydedin; genel MFA gereksinimi sürer ve profiler’ı engelleyebilir.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
