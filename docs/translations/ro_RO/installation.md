# ΩGuardian Profiler Shield — Instalare și utilizare

PHP 8.3 sau mai nou; Symfony 5.4, 6.4 sau 7.4. Symfony 8.1 necesită PHP 8.4 sau mai nou. Sunt necesare Composer și extensia OpenSSL + Tokenizer. Instalați WebProfilerBundle pentru profiler.

## 1. Instalare

Rulați din directorul rădăcină al aplicației Symfony care conține composer.json și bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Configurare

```bash
php vendor/bin/guardian setup
```

Asistentul activează bundle-ul în dev/test, pregătește seiful și contul de administrator, golește memoria cache și verifică configurația. Continuați numai după reușita fiecărui pas. Păstrați aplicația privată în timpul operației. Repetarea păstrează conturile și cheile existente.

## 3. Deschiderea panoului

```bash
php bin/console guardian:access --env=dev
```

Comanda afișează calea privată. Înlocuiți https://VOTRE-HOTE cu originea HTTPS reală, inclusiv portul. Citiți datele de acces din .guardian-shield/initial-admin-credentials.txt sau din seiful indicat. Prima autentificare impune schimbarea parolei. Nu distribuiți acest fișier.

## 4. Dezinstalare

Opriți procesele worker și executați comenzile în ordine, verificând fiecare rezultat. Seiful și cheia se păstrează. Reporniți procesele și verificați profilerul într-un mediu privat.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Pentru a șterge și conturile și setările, înlocuiți prima comandă cu următoarea și confirmați. Refuzul nu modifică nimic. Verificați manual seifurile externe și fișierele personalizate; păstrați cheile seifurilor menținute.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Referința comenzilor

Ajutorul descrie comenzile. Comanda de acces găsește panoul; diagnosticul verifică configurația. Este necesar acces local de încredere.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Referința comenzilor](commands.md) · [Cuprinsul documentației](README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

Dacă organizația folosește Private Packagist, configurați acest depozit înainte de instalare și salvați datele Composer conform instrucțiunilor sale. Nu salvați niciodată tokenuri în Git.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
