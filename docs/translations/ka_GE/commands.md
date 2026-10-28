# ბრძანებების ცნობარი

[დოკუმენტაციის სარჩევი](README.md)

გაუშვით Symfony-ის ძირიდან სანდო ტერმინალში. მაგალითები იყენებს dev-ს. პაროლები, გასაღებები და აღდგენის კოდები არ ჩაწეროთ არგუმენტებში, ჟურნალებში ან Git-ში.

## ბრძანებების მოძიება და დახმარება


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev და --no-interaction Symfony-ის პარამეტრებია. --no-interaction საიდუმლოებს არ აწვდის და ინტერაქტიული პაროლის ბრძანებებისთვის არ გამოდგება. --help აჩვენებს სინტაქსს, -v — დეტალებს. კოდი 0 აღნიშნავს გამოცხადებულ შედეგს; --no-admin შეიძლება წარმატებული იყოს გამოუსადეგარი პორტალით.

## `vendor/bin/guardian`

ააქტიურებს bundle-ს dev/test-ში, ასუფთავებს ქეშს, უშვებს guardian:setup --automatic-ს, ისევ ასუფთავებს ქეშს, შემდეგ უშვებს guardian:doctor-სა და guardian:access-ს. პირველივე შეცდომაზე ჩერდება. განმეორება ინარჩუნებს ანგარიშებსა და გასაღებებს. უარყოფს APP_ENV=prod-ს და საჭიროებს proc_open-ს.

არგუმენტის გარეშე, --help, -h ან help აჩვენებს დახმარებას ცვლილების გარეშე. ერთადერთი მოქმედებაა setup, დამატებითი პარამეტრების გარეშე.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | ბრძანებების ცნობარი |
|---|---|
| [`guardian:setup`](#guardiansetup) | ამზადებს პირად გზას, გასაღებს, საცავს, Symfony-ის ფაილებსა და ადმინისტრატორს, თუ აქტიური არ არსებობს. |
| [`guardian:install`](#guardianinstall) | ამზადებს საცავს და ქმნის ადმინისტრატორს --username სახელით, ნაგულისხმევად admin. |
| [`guardian:access`](#guardianaccess) | აჩვენებს პანელის გზასა და საწყისი მონაცემების მდებარეობას, არასოდეს პაროლს. |
| [`guardian:doctor`](#guardiandoctor) | ამოწმებს საცავს, უფლებებს, გასაღებს, მარშრუტს, აქტიურ ადმინისტრატორსა და პარამეტრებს. |
| [`guardian:password:reset`](#guardianpasswordreset) | ცვლის არსებული username ანგარიშის პაროლს დაფარული შეყვანით მოქმედი პოლიტიკის მიხედვით, აუქმებს ყველა სესიას და აღრიცხავს მოქმედებას. |
| [`guardian:mfa:reset`](#guardianmfareset) | შლის username-ის TOTP საიდუმლოსა და მრიცხველს, აუქმებს სესიებს და აღრიცხავს მოქმედებას. |
| [`guardian:login:unlock`](#guardianloginunlock) | ანულებს username-ის მრიცხველებს. |
| [`guardian:policy:reset`](#guardianpolicyreset) | აცარიელებს IP სიას და თიშავს განრიგს, JIT-სა და დისტანციურ საგანგებო წვდომას. |
| [`guardian:lockdown`](#guardianlockdown) | on კეტავს დიაგნოსტიკას და შლის ყველა სესიას; ბიზნესგვერდები მუშაობს. |
| [`guardian:emergency`](#guardianemergency) | არსებული username-ისთვის ქმნის 5 ერთჯერად კოდს და ცვლის ძველებს. |
| [`guardian:alerts:flush`](#guardianalertsflush) | აგზავნის რიგში მყოფ შეტყობინებებს alert_email-ზე PHP mail()-ით. |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | ზომავს ჰეშის შემოწმებას სინთეზური საიდუმლოთი და არა მომხმარებლის პაროლით. |
| [`guardian:uninstall`](#guardianuninstall) | ამზადებს Guardian-ის მიერ შექმნილი დადასტურებული ფაილების წაშლას. |

## `guardian:setup`

ამზადებს პირად გზას, გასაღებს, საცავს, Symfony-ის ფაილებსა და ადმინისტრატორს, თუ აქტიური არ არსებობს. ინარჩუნებს ანგარიშებს. საწარმოო გარემოში უარყოფილია. პირდაპირ გაშვების შემდეგ გაასუფთავეთ ქეში და შეამოწმეთ.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic ქმნის admin-ს და საიდუმლოს პირად 0600 ფაილში. --path ირჩევს 25–181 სიმბოლოს გზას, არსებულს არ ცვლის. --no-admin ტოვებს ადმინისტრატორის შექმნას; აქტიური ანგარიშის გარეშე პორტალი გამოუსადეგარია. --username სთავაზობს ინტერაქტიულ სახელს, ნაგულისხმევად admin-ს; ავტომატურ ანგარიშს არ არქმევს სხვა სახელს.

```text
--path: ^/[a-zA-Z0-9/_-]{24,180}$
/_profiler; /_wdt → ∅
```

```bash
php bin/console guardian:setup --username=operator --env=dev
php bin/console cache:clear --env=dev
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## `guardian:install`

ამზადებს საცავს და ქმნის ადმინისტრატორს --username სახელით, ნაგულისხმევად admin. ითხოვს დაფარულ პაროლს მოქმედი პოლიტიკით. არსებულ ანგარიშს უარყოფს. არ ქმნის მარშრუტებს ან Symfony-ის კონფიგურაციას; ჩვეულებრივ გამოიყენეთ სრული ასისტენტი.


```bash
php bin/console guardian:install --username=admin --env=dev
```


გამოყენების სახელი : 3 Touching რომ 64 Alphanumeric ფუნდამენტები. _ _ _

## `guardian:access`

აჩვენებს პანელის გზასა და საწყისი მონაცემების მდებარეობას, არასოდეს პაროლს. --base-url იღებს რეალურ HTTPS მისამართს არჩევითი პორტით, მონაცემების, მოთხოვნის პარამეტრებისა და ფრაგმენტის გარეშე. შეცვალეთ მაგალითის ჰოსტი.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

ამოწმებს საცავს, უფლებებს, გასაღებს, მარშრუტს, აქტიურ ადმინისტრატორსა და პარამეტრებს. პრობლემისას დაბრუნების კოდია 1. PHP CLI-ისა და PHP-FPM-ის სარტყლები შეიძლება განსხვავდებოდეს. არ ამოწმებს ინტერნეტში ხილვადობასა და პროქსის წესებს.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

ცვლის არსებული username ანგარიშის პაროლს დაფარული შეყვანით მოქმედი პოლიტიკის მიხედვით, აუქმებს ყველა სესიას და აღრიცხავს მოქმედებას. პაროლი არგუმენტად არ გადასცეთ.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

შლის username-ის TOTP საიდუმლოსა და მრიცხველს, აუქმებს სესიებს და აღრიცხავს მოქმედებას. დაარეგისტრირეთ ახალი ავთენტიფიკატორი; საერთო MFA მოთხოვნა რჩება და შეიძლება პროფაილერი დაბლოკოს.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

ანულებს username-ის მრიცხველებს. --ip მისამართი შლის მოცემული IPv4/IPv6 წყაროს მრიცხველსაც; სხვაგვარად ქსელური ბლოკი შეიძლება დარჩეს. --all შლის ყველა მრიცხველს. ანგარიშები, პაროლები და სესიები უცვლელია; მოქმედება აღირიცხება.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

აცარიელებს IP სიას და თიშავს განრიგს, JIT-სა და დისტანციურ საგანგებო წვდომას. ავთენტიფიკაცია, MFA, საერთო ბლოკი და შესვლის ბლოკები რჩება. აღდგენის შემდეგ პანელში თავიდან დააყენეთ საჭირო შეზღუდვები.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on კეტავს დიაგნოსტიკას და შლის ყველა სესიას; ბიზნესგვერდები მუშაობს. off ხსნის მხოლოდ საერთო ბლოკს: საჭიროა ხელახალი შესვლა და სხვა პოლიტიკები ძალაში რჩება.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

არსებული username-ისთვის ქმნის 5 ერთჯერად კოდს და ცვლის ძველებს. ჩანს მხოლოდ ერთხელ; შეინახეთ სერვერის გარეთ საიდუმლოების მენეჯერში. დისტანციურ გამოყენებას სჭირდება ჩართული საგანგებო წვდომა, ავტორიზებული სესია, პაროლი და მიზეზი. საერთო ბლოკს არ უვლის გვერდს.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

აგზავნის რიგში მყოფ შეტყობინებებს alert_email-ზე PHP mail()-ით. გამართეთ ტრანსპორტი და დაგეგმილი ამოცანა, შეამოწმეთ რეალური მიწოდება. წარუმატებლები რიგში რჩება. კოდი 1 ნიშნავს არასწორ მიმღებს ან გაგზავნის შეცდომას.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

ზომავს ჰეშის შემოწმებას სინთეზური საიდუმლოთი და არა მომხმარებლის პაროლით. --iterations იღებს 1–10-ს, ნაგულისხმევად 3-ს. აბრუნებს JSON-ს ალგორითმით, დროითა და შემოწმებებით წამში. ზომავს ერთ ადგილობრივ CPU პროცესს და არა GPU-ს ან თავდამსხმელს.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

ამზადებს Guardian-ის მიერ შექმნილი დადასტურებული ფაილების წაშლას. ინარჩუნებს ანგარიშებს, პარამეტრებს, ჟურნალებსა და გასაღებს. გააჩერეთ მუშა პროცესები, გაუშვით composer remove-მდე, გაასუფთავეთ dev ქეში და გადატვირთეთ. შეამოწმეთ მორგებული ან უცნობი წარმოშობის ფაილები.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge ითხოვს მონაცემების წაშლას, ნაგულისხმევად უარით. --force საჭიროებს --purge-ს და ადასტურებს განადგურებას; --no-interaction-თან აუცილებელია. გარე საცავებმა, ბმულებმა და უცნობმა ფაილებმა შეიძლება სრული წაშლა შეაფერხოს. შეინარჩუნეთ საჭირო გასაღებები და შეამოწმეთ შედეგი.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## წაშლა და მონაცემები


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


საცავი და გასაღები ერთად შეინახეთ და აღდგენა გამოსცადეთ. დაკარგული გასაღები ავტომატურად არ იქმნება. წაშლის შემდეგ პროფაილერი მხოლოდ დახურულ გარემოში შეამოწმეთ. საწარმოო გარემო: APP_DEBUG=0, WebProfilerBundle და შეგროვება გამორთულია. აუდიტის ადგილობრივი ჯაჭვი გატეხილი ჰოსტის მთლიანობას ვერ ამტკიცებს.

[წაშლა და მონაცემები](desinstallation.md)
