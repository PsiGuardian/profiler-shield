# Güvenlik politikası

[Belge içindekiler](README.md)

Güvenlik açıklarını şifreli kanal kararlaştırmak için özel olarak bundle-sec@omegaguardian.fr adresine bildirin. İlk mesajda sır, etkin token veya özel URL olmasın. Guardian, Symfony ve PHP sürümleri, etki, önkoşullar ve en küçük yeniden üretimi belirtin. Bakımcılar onaylar, inceler, özel düzeltir, test eder ve duyuruyla yamayı koordine eder.

Parolaları, TOTP sırlarını, şifreleme anahtarlarını veya tanılama URL'lerini asla e-postayla göndermeyin.

Kasa ve anahtarı birlikte yedekleyip geri yüklemeyi deneyin. Kayıp anahtar otomatik yeniden oluşturulmaz. Kaldırma sonrası profiler’ı yalnızca özel ortamda denetleyin. Üretim: APP_DEBUG=0, WebProfilerBundle ve toplama kapalı. Yerel denetim zinciri ele geçirilmiş sunucunun bütünlüğünü kanıtlamaz.
