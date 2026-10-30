# ΩGuardian Profiler Shield — Inštalácia a používanie

PHP 8.3 alebo novší; Symfony 5.4, 6.4 alebo 7.4. Symfony 8.1 vyžaduje PHP 8.4 alebo novší. Potrebné sú Composer a rozšírenie OpenSSL + Tokenizer. Pre profiler nainštalujte WebProfilerBundle.

## 1. Inštalácia

Spúšťajte z koreňového priečinka aplikácie Symfony obsahujúceho composer.json a bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Nastavenie

```bash
php vendor/bin/guardian setup
```

Sprievodca aktivuje bundle v dev/test, pripraví úložisko a účet správcu, vymaže cache a overí nastavenie. Pokračujte až po úspechu každého kroku. Počas nastavenia ponechajte aplikáciu neverejnú. Opakovanie zachová existujúce účty a kľúče.

## 3. Otvorenie panela

```bash
php bin/console guardian:access --env=dev
```

Príkaz vypíše súkromnú cestu. Nahraďte https://VOTRE-HOTE skutočnou HTTPS adresou vrátane portu. Prihlasovacie údaje sú v .guardian-shield/initial-admin-credentials.txt alebo v uvedenom úložisku. Prvé prihlásenie vyžaduje zmenu hesla. Súbor nezdieľajte.

## 4. Odinštalovanie

Zastavte pracovné procesy a spúšťajte príkazy postupne po úspechu predchádzajúceho. Úložisko a kľúč zostanú zachované. Reštartujte procesy a overte profiler v neverejnom prostredí.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Ak chcete vymazať aj účty a nastavenia, nahraďte prvý príkaz nasledujúcim a potvrďte. Odmietnutie nič nezmení. Externé úložiská a upravené súbory skontrolujte ručne; potrebné kľúče ponechajte.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Prehľad príkazov

Pomocník opisuje príkazy. Príkaz prístupu nájde panel; diagnostika overí nastavenie. Vyžaduje sa dôveryhodný miestny prístup.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Prehľad príkazov](../translations/sk_SK/commands.md) · [Obsah dokumentácie](../translations/sk_SK/README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

Ak organizácia používa Private Packagist, nastavte tento repozitár pred inštaláciou a uložte údaje Composer podľa jej pokynov. Tokeny nikdy neukladajte do Gitu.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
