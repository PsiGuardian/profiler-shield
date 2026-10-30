# ΩGuardian Profiler Shield — Суулгах ба ашиглах

PHP 8.3 буюу түүнээс шинэ; Symfony 5.4, 6.4 эсвэл 7.4 шаардлагатай. Symfony 8.1 нь PHP 8.4 буюу түүнээс шинийг шаарддаг. Composer болон OpenSSL + Tokenizer өргөтгөл хэрэгтэй. Профайлер ашиглахын тулд WebProfilerBundle суулгана.

## 1. Суулгах

composer.json болон bin/console бүхий Symfony програмын үндсэн хавтсаас ажиллуулна.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Тохируулах

```bash
php vendor/bin/guardian setup
```

Туслах нь dev/test орчинд bundle-ийг идэвхжүүлж, хадгалалт болон администраторын бүртгэлийг бэлтгэж, кэшийг цэвэрлэн тохиргоог шалгана. Алхам бүр амжилттай болсны дараа үргэлжлүүлнэ. Энэ үед програмыг нийтэд нээлтгүй байлгана. Дахин ажиллуулахад бүртгэл, түлхүүрүүд хадгалагдана.

## 3. Самбарыг нээх

```bash
php bin/console guardian:access --env=dev
```

Тушаал нь самбарын хувийн замыг харуулна. https://VOTRE-HOTE-г програмын бодит HTTPS хаяг болон портоор солино. Нэвтрэх мэдээллийг .guardian-shield/initial-admin-credentials.txt файл эсвэл заасан хадгалалтаас уншина. Анх нэвтрэхдээ нууц үг солих шаардлагатай. Файлыг бүү хуваалц.

## 4. Устгах

Ажлын процессуудыг зогсоож, тушаалуудыг дарааллаар нь амжилттай ажиллуулна. Хадгалалт болон түлхүүр үлдэнэ. Процессуудыг дахин эхлүүлж, профайлерыг хаалттай орчинд шалгана.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Бүртгэл, тохиргоог хамт устгах бол эхний тушаалыг доорх тушаалаар сольж баталгаажуулна. Татгалзвал өөрчлөлт хийхгүй. Гадаад хадгалалт, өөрчилсөн файлуудыг гараар шалгаж, үлдээх хадгалалтын түлхүүрийг хадгална.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Тушаалуудын лавлах

Тусламж нь тушаалуудыг тайлбарлана. Хандалтын тушаал самбарыг олно; оношилгоо тохиргоог шалгана. Итгэмжлэгдсэн дотоод хандалт шаардлагатай.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Тушаалуудын лавлах](../translations/mn_MN/commands.md) · [Баримт бичгийн агуулга](../translations/mn_MN/README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

Байгууллага Private Packagist ашигладаг бол суулгахаас өмнө энэ санг тохируулж, Composer-ийн нэвтрэх мэдээллийг байгууллагын заавраар хадгална. Токеныг Git-д бүү оруул.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
