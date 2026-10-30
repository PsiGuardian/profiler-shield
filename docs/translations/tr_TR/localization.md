# Çeviriler

[Belge içindekiler](README.md)

Portal 28 bölgesel seçenek ve 25 katalog sunar; dört İngilizce seçenek bir kataloğu paylaşır. Statik metin çevrilir ve güvenli biçimde kaçışlanır. Kullanıcı adları, özel mesajlar ve kullanıcı verileri çevrilmez. Uzak hizmete metin gönderilmez. Denetim anahtarları, parametreleri, UTF-8’i, komutları ve bağlantıları kontrol eder; HTTP testleri her dilin görünümlerini gezer.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
