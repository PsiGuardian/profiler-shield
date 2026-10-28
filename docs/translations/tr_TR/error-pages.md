# Halkla İlişkiler Hatası sayfaları

[Belge içindekiler](README.md)


## Halkla İlişkiler Hatası sayfaları

Uygulamanın sayfalarını koruyun veya ziyaretçilere tarafsız HTML gösterin. Gerçek HTTP durum kodları değişmez.

Anasayfa GÜVENLİK LEZ Symfony Görüntüleme hataları. genetik mod özelleştirilmiş metni görmezden gelir. yalnızca özelleşmiş mod ziyaretçi mesajını kullanır. kamu referansı eksik, rastgele veya sabit olabilir; mesajdan bağımsızdır. HTTP Status kodları değişmez kalır.

Uygulama sayfaları: mevcut sitenin korunması 404 Cevaplar; istisnalar kullanılır 404 Şablon mevcut olduğunda, aksi takdirde tarafsız bir sayfa. Neutral: standart mesaj. Özelleştirilmiş: metninizi kullanın.

Anonim 500 istisnaları nötr sayfa alır. Ayrıntılar yetkili Guardian kimliği ve GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1 gerektirir. Eksik veya hatalı Twig şablonları nötr gösterime döner; özel hata denetleyicileri ayrı entegrasyon ister. JSON ve başarılı iş sayfaları değişmez.

| error_mode | Ayarlar |
|---|---|
| native | Uygulama sayfaları: mevcut sitenin korunması 404 Cevaplar; istisnalar kullanılır 404 Şablon mevcut olduğunda, aksi takdirde tarafsız bir sayfa. Neutral: standart mesaj. Özelleştirilmiş: metninizi kullanın. |
| generic | HTML 400–599 → Uygulamanın sayfalarını koruyun veya ziyaretçilere tarafsız HTML gösterin. Gerçek HTTP durum kodları değişmez. |
| custom | Yalnızca özel nötr mesaj modunda kullanılabilir. Mod değiştirilirken metin korunur. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Sadece özel bir statik referans ile mevcuttur. |
| error_custom_text | Yalnızca özel nötr mesaj modunda kullanılabilir. Mod değiştirilirken metin korunur. |

## Gösterilen referans

Farklı iletiler veya sabit referanslar gerçek hatayı açığa çıkarabilir. HTTP kodları sahte olmamalıdır.

Referanslar için geçerlidir Guardian’s tarafsız sayfalar: rastgele yeni bir kod oluşturur, rastgeli bir mesaj değil.

Seçimler kaydedilmeden önce sunucuda doğrulanır.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## ağ ve dağıtım

IP izin listesi kabul eder IPv4, IPv6 ve CIDR İnternet Kontrolü Symfony güvenilir bir proxy yapılandırması, bunu etkinleştirmeden önce. HTTPS Güvenli çerezler için. Guardian Web sunucularını veya veritabanlarını tamir etmez.

Kimlik, rol, oturum, ağ, zamanlama, JIT veya kilit denetlenemiyorsa tanılamayı reddedin. Süre sonuna tarayıcı karar vermez. Toplama ve görüntüleme ayrıdır. Cihaz hatırlama doğrulama değildir. Acil erişim kimlik, tüketilmiş kod ve kalıcı denetim gerektirir; kritik denetim hatası açılmayı önler. Geçersiz yapılandırmayı kısmen uygulamayın. İsteğe bağlı kontroller uzak hizmet gerektirmez.
