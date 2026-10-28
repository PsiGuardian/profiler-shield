# ΩGuardian Profiler Shield — ინსტალაცია და გამოყენება

საჭიროა PHP 8.3 ან ახალი; Symfony 5.4, 6.4 ან 7.4. Symfony 8.1 მოითხოვს PHP 8.4-ს ან ახალს. საჭიროა Composer და OpenSSL + Tokenizer გაფართოება. პროფაილერისთვის დააყენეთ WebProfilerBundle.

## 1. ინსტალაცია

გაუშვით Symfony აპლიკაციის ძირითადი საქაღალდიდან, სადაც არის composer.json და bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. კონფიგურაცია

```bash
php vendor/bin/guardian setup
```

ასისტენტი ააქტიურებს bundle-ს dev/test გარემოში, ამზადებს საცავსა და ადმინისტრატორის ანგარიშს, ასუფთავებს ქეშს და ამოწმებს პარამეტრებს. შემდეგ ნაბიჯზე გადადით მხოლოდ წარმატების შემდეგ. ამ დროს აპლიკაცია დახურული იყოს. განმეორებითი გაშვება ინარჩუნებს ანგარიშებსა და გასაღებებს.

## 3. პანელის გახსნა

```bash
php bin/console guardian:access --env=dev
```

ბრძანება აჩვენებს პანელის პირად გზას. https://VOTRE-HOTE შეცვალეთ აპლიკაციის რეალური HTTPS მისამართით, პორტის ჩათვლით. შესვლის მონაცემები წაიკითხეთ .guardian-shield/initial-admin-credentials.txt ფაილში ან მითითებულ საცავში. პირველ შესვლაზე პაროლის შეცვლა აუცილებელია. ფაილი არ გააზიაროთ.

## 4. წაშლა

გააჩერეთ მუშა პროცესები და ბრძანებები გაუშვით თანმიმდევრობით, თითოეული შედეგის შემოწმებით. საცავი და გასაღები შენარჩუნდება. გადატვირთეთ პროცესები და პროფაილერი დახურულ გარემოში შეამოწმეთ.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

ანგარიშებისა და პარამეტრების წასაშლელად პირველი ბრძანება ჩაანაცვლეთ შემდეგით და დაადასტურეთ. უარი არაფერს ცვლის. გარე საცავები და შეცვლილი ფაილები ხელით შეამოწმეთ; საჭირო საცავების გასაღებები შეინახეთ.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. ბრძანებების ცნობარი

დახმარება აღწერს ბრძანებებს. წვდომის ბრძანება პოულობს პანელს; დიაგნოსტიკა ამოწმებს პარამეტრებს. საჭიროა სანდო ადგილობრივი წვდომა.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[ბრძანებების ცნობარი](commands.md) · [დოკუმენტაციის სარჩევი](README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

თუ ორგანიზაცია Private Packagist-ს იყენებს, ინსტალაციამდე გამართეთ ეს საცავი და Composer-ის მონაცემები მისი მითითებებით შეინახეთ. ტოკენები Git-ში არასოდეს დაამატოთ.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
