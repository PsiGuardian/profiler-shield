# Sign-ins ve şifreleri

[Belge içindekiler](README.md)


## Hesaplar ve ilk giriş

Gardian'a hoşgeldiniz. başlangıç şifreniz geçici. yeni bir sır seçin: paneli ve teşhisleri yapana kadar kilitli kalın.

Guardian Hesaplar site hesaplarından ayrıdır. başlangıç hesabı ve yeni hesaplar devam etmeden önce şifrelerini değiştirmelidir. güvenlik rolü görüntüleri profil erişimi olmadan bilgilere izin verir. bir rol veya şifreyi değiştirmek önceki oturumları iptal eder.

Özel yolu, anahtarı, kasayı, Symfony dosyalarını ve gerekiyorsa yöneticiyi hazırlar. Hesapları korur. Üretim ortamını reddeder. Doğrudan çalıştırdıktan sonra önbelleği temizleyip denetimleri çalıştırın.


```bash
php bin/console guardian:access --env=dev
```


| Kurtarılan şifre gereksinimleri | Minimum Uzunluk (Unicode Karakterler ) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Şifre Politikası

Bu kurallar şifre oluşturma ve değişiklikler için geçerlidir, web ve CLIMevcut şifreleri tespit etmez veya incelemezler.

Başlangıç değeri : 15 karakterler. benzersiz bir pasaport veya şifre yöneticisi kullanın. gerektiren karakter kategorileri öngörülemeyen bir şifreleri garanti etmez.

Bu sunucu üzerinde, Guardian Otomatik Kullanım Argon2id Yeni şifre için. tutun PHP Destek Sınırı: Uygulama sınırı 512 UTF-8 Bute için.

Bu PHP Servis sağlanmıyor Argon2id. Guardian Bu nedenle kullanılır bcrypt Şifreyi aşarak reddediyor 72 UTF-8 • Sistem yöneticinizden izin vermesini isteyin Argon2id Destek içinde PHP.

Bu seçenek, mevcut oturumları derhal iptal eder. hesaplar Guardian'ı ayarlamadan veya teşhisleri açmadan önce şifresini değiştirmelidir.

Mevcut username hesabının parolasını etkin ilkeye uygun gizli girişle değiştirir, tüm oturumlarını iptal eder ve kaydeder. Parolayı argüman olarak vermeyin.

## Gelişmiş Lock

Her aşama bir başarısızlık sınırı, sayma penceresi, kilitleme süresi ve tekrarlama penceresini tanımlar. 10 Başarısızlıklar A 10-minute lockout. tekrarlanan başarısızlıklar bir sonraki aşamayı tetikleyebilir. kontroller hem hesap ve ağ kaynağı, yönetici onayları da dahil olmak üzere kapsamaktadır.

Şifreli sayaçlar normalleştirilmiş hesap ve kaynak için bağımsızdır; ham IP adresleri saklanmaz. Hash öncesi atomik rezervasyon 120 saniyede biter. Terk edilmiş rezervasyonlar hata sayılır; geç sonuçlar oturum oluşturmaz. Geçersiz CSRF rezervasyon yapmaz. Başarılı giriş hesap hatalarını temizler, tekrar ve kaynak geçmişini korur. 10000 etkin kayıtta engelleri atmak yerine yeni kimlikler reddedilir.

Adımlar sırada çalışır. bir kilitleme sonrasında, tekrarlama penceresi içinde yeni başarısızlıklar bir sonraki aşamayı kullanır. bu pencere dışında, ilk aşama yeniden uygulanır. son aşama tekrarlanır. engellenmiş talepler gecikmeyi uzatmaz. 8 Toplumsal İyileşme - Yerel Kurtarma: guardian:login:unlock --all.

| aşaması | Kurtarılan şifre gereksinimleri |
|---|---|
| Lockout öncesinde başarısızlıklar | 1–100 |
| Yürüyüş ( dakikalar ) | 1–10080 |
| Pencerelerin sayısı (minutlar) | 1–1440 |
| Açıldıktan sonra pencereyi tekrarlayın (dakika) | 1–10080 |
| aşaması | 1–8; block[n+1] ≥ block[n] |
| İlk değer | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Araştırma maliyet laboratuvarı

Gerçek bir şifre girmeden sabit uzunluğun rastgele sırlarını eşit olarak karşılaştırın. bulma oranları ve donanım fiyatı ayarlanabilir varsayımlardır.

Ayrık ASCII kategorileri ve BigInt kullanılır. Dahil etme-çıkarma kesin arama uzayını hesaplar; eksik kategori veya yetersiz uzunluk sıfır verir. Yerine koymadan ortalama sıra: (N+1)/2. Beklenen gecikme eşikleri ve tekrarlanan son aşamayı içerir. Hız pencerelere uymuyorsa tahmin kapanır. Çevrimdışı bedel, saat ile varsayılan saatlik maliyetin çarpımıdır.

Sabit bir alfabe ile, bir kategoriyi gerektiren uyumlu sırların sayısını azaltır. alfabeyi genişletmek farklı bir işlemdir. Lockout zamanları ayarlanmış pencerelerde kalmak için yeterince hızlı sürekli tahminler alır.

Bu model insan tarafından seçilen bir şifrenin gücünü ölçmez. Sözlükler, yeniden kullanım, sızıntılar ve hedef stratejileri hariç tutar. Web kilitleri çalınan hashleri korumaz. Başlangıç çevrimdışı oranı görüntülenir, bir referans değil. Yerel CPU Değerlendirme : guardian:password:benchmark.

Büyük sayılar tam olarak, sayı grubu ile ve E notu olmadan yazılır. zaman ve maliyetler yuvarlanır. aynı satırı aynı varsayımları kullanarak karşılaştırın.

| Mevcut simülasyon alfabesi | Mümkün uyumlu sırlar |
|---|---|
| ASCII Düşük | 26 |
| ASCII Yukarıda | 26 |
| ASCII Dijital | 10 |
| ASCII uzayı dışlayan simgeler | 32 |
| Çevrimiçi Fiyatlar (Guesses/s) | 1 / s |
| Offline Hattı (Hashes / S) | 10000 / s |
| Malzeme maliyeti (saat başına €) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## İyileşme ve doğrulama

Kasa ve anahtarı birlikte yedekleyip geri yüklemeyi deneyin. Kayıp anahtar otomatik yeniden oluşturulmaz. Kaldırma sonrası profiler’ı yalnızca özel ortamda denetleyin. Üretim: APP_DEBUG=0, WebProfilerBundle ve toplama kapalı. Yerel denetim zinciri ele geçirilmiş sunucunun bütünlüğünü kanıtlamaz.

username sayaçlarını sıfırlar. --ip ADRES ilgili IPv4/IPv6 kaynağının sayacını da siler; aksi halde ağ engeli kalabilir. --all tüm sayaçları siler. Hesap, parola ve oturumlar değişmez; işlem kaydedilir.

[Komut başvurusu](commands.md)
