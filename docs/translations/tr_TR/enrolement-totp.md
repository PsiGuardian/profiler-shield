# İki faktörlü kimlik doğrulama

[Belge içindekiler](README.md)


## İki faktörlü kimlik doğrulama

Her kişi kendi kayıtlarını yapar TOTP gizli ve bir kod doğrulamaktadır.

Her kişi kendi kayıtlarını yapar TOTP gizli ve bir kod doğrulamaktadır. talep MFA bloklar profil erişimi kayıtlı olmayan hesaplar için. tüm ekip için bu koruma gerektirmeden önce idari iyileşmeyi planlayın. Manuel kurulumu seçip gösterilen anahtarı girin. SHA-1, altı rakam ve 30 saniyelik süre kullanın.

## 1. Kimliğinizi doğrulayın

Guardian'a girişte kullandığınız parolayı girin. Bu kurulum için yalnızca bir kez istenir.

## 2. Anahtarı doğrulama uygulamasına girin

Manuel kurulumu seçip gösterilen anahtarı girin. SHA-1, altı rakam ve 30 saniyelik süre kullanın.

## 3. Altı haneli kodu doğrulayın

Uygulamadaki kodu girin. İkinci parola gerekmez.

## Gizlilik: web sitesi URL'sini girmeyin

Uygulama ad veya site isterse yalnızca “ΩGuardian” ya da tarafsız bir ad girin. Symfony örneğinizin URL'sini yazmayın.

Gizli tutun: TOTP kodlarınız bununla oluşturulabilir. Paylaşmayın, ekran görüntüsü almayın.

Geçici sır 10 dakika veya 5 yanlış koddan sonra biter; yeniden başlayın. Yanlış kodda form korunur, parola tekrar istenmez. Onay sayacı tekrar kullanımını önlemek için tüketilir.

## İyileşme ve doğrulama

username TOTP sırrını ve sayacını siler, oturumları iptal eder ve kaydeder. Yeni doğrulayıcı kaydedin; genel MFA gereksinimi sürer ve profiler’ı engelleyebilir.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
