# Katkıda bulunma

[Belge içindekiler](README.md)

Yalıtılmış Symfony test uygulamasında çalışın. İş erişilebilirliğini ve yetkisiz tanılama reddini koruyun. İlgili sürümlerde anonim istek, iptal edilmiş oturum, zamanlama ve depolama arızalarını test edin. Kararlı anahtarlar, güvenli kaçışlama ve tam çeviri kullanın. Testte, görüntüde veya Git’te sır olmasın; zorunlu ağ bağımlılığı olmasın. Etkileri ve doğrulamayı açıklayın.
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[Güvenlik politikası](security.md) · [Arayüz örnekleri](examples.md)
