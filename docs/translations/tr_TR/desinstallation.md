# Uninstall ve Data

[Belge içindekiler](README.md)

Guardian tarafından üretildiği kanıtlanan dosyaların kaldırılmasını hazırlar. Hesap, ayar, günlük ve anahtarı korur. Worker’ları durdurun, composer remove öncesinde çalıştırın, dev önbelleğini temizleyip yeniden başlatın. Özelleştirilmiş veya kökeni belirsiz dosyaları inceleyin.

--purge varsayılan ret onayıyla veri silme ister. --force, --purge gerektirir ve yıkımı onaylar; --no-interaction ile zorunludur. Haricî kasalar, bağlantılar ve bilinmeyen dosyalar tam silmeyi önleyebilir. Gerekli anahtarları saklayıp sonucu inceleyin.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Kasa ve anahtarı birlikte yedekleyip geri yüklemeyi deneyin. Kayıp anahtar otomatik yeniden oluşturulmaz. Kaldırma sonrası profiler’ı yalnızca özel ortamda denetleyin. Üretim: APP_DEBUG=0, WebProfilerBundle ve toplama kapalı. Yerel denetim zinciri ele geçirilmiş sunucunun bütünlüğünü kanıtlamaz.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Komut başvurusu

--purge varsayılan ret onayıyla veri silme ister. --force, --purge gerektirir ve yıkımı onaylar; --no-interaction ile zorunludur. Haricî kasalar, bağlantılar ve bilinmeyen dosyalar tam silmeyi önleyebilir. Gerekli anahtarları saklayıp sonucu inceleyin.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Komut başvurusu](commands.md)


Composer paketi zaten kaldırdıysa aynı sürümü setup çalıştırmadan geçici kurun. guardian:uninstall çalıştırıp paketi tekrar kaldırın ve önbelleği temizleyin. Özel yapılandırmaları elle inceleyin.
