# ΩGuardian Profiler Shield — Instalacija i uporaba

PHP 8.3 ili noviji; Symfony 5.4, 6.4 ili 7.4. Symfony 8.1 zahtijeva PHP 8.4 ili noviji. Potrebni su Composer i proširenje OpenSSL + Tokenizer. Za profiler instalirajte WebProfilerBundle.

## 1. Instalacija

Pokrenite u korijenskom direktoriju aplikacije Symfony koji sadrži composer.json i bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Postavljanje

```bash
php vendor/bin/guardian setup
```

Čarobnjak aktivira bundle u dev/test, priprema trezor i administratorski račun, čisti predmemoriju i provjerava postavke. Nastavite tek nakon uspjeha svakog koraka. Tijekom postupka aplikacija mora ostati privatna. Ponavljanje čuva postojeće račune i ključeve.

## 3. Otvaranje ploče

```bash
php bin/console guardian:access --env=dev
```

Naredba prikazuje privatnu putanju. Zamijenite https://VOTRE-HOTE stvarnom HTTPS adresom i portom. Podatke za prijavu pročitajte u .guardian-shield/initial-admin-credentials.txt ili navedenom trezoru. Prva prijava zahtijeva promjenu lozinke. Ne dijelite ovu datoteku.

## 4. Deinstalacija

Zaustavite radne procese i izvršavajte naredbe redom, provjeravajući svaki rezultat. Trezor i ključ ostaju sačuvani. Ponovno pokrenite procese i provjerite profiler u privatnom okruženju.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Za brisanje računa i postavki zamijenite prvu naredbu sljedećom i potvrdite. Odbijanje ništa ne mijenja. Ručno provjerite vanjske trezore i prilagođene datoteke; sačuvajte ključeve zadržanih trezora.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Pregled naredbi

Pomoć opisuje naredbe. Naredba pristupa pronalazi ploču, a dijagnostika provjerava postavke. Potreban je pouzdan lokalni pristup.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Pregled naredbi](../translations/hr_HR/commands.md) · [Sadržaj dokumentacije](../translations/hr_HR/README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

Ako organizacija koristi Private Packagist, prije instalacije postavite ovaj repozitorij i spremite Composer vjerodajnice prema njezinim uputama. Tokene nikada ne spremajte u Git.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
