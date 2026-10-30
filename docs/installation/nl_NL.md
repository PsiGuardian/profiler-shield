# ΩGuardian Profiler Shield — Installatie en gebruik

PHP 8.3 of hoger; Symfony 5.4, 6.4 of 7.4. Symfony 8.1 vereist PHP 8.4 of hoger. Composer en de OpenSSL + Tokenizer-extensie zijn vereist. Installeer WebProfilerBundle voor de profiler.

## 1. Installeren

Voer uit vanuit de hoofdmap van de Symfony-applicatie met composer.json en bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Configureren

```bash
php vendor/bin/guardian setup
```

De assistent activeert de bundle in dev/test, maakt de kluis en het beheerdersaccount gereed, wist de cache en controleert de configuratie. Ga alleen verder als elke stap slaagt. Houd de applicatie tijdens de installatie privé. Opnieuw uitvoeren behoudt bestaande accounts en sleutels.

## 3. Paneel openen

```bash
php bin/console guardian:access --env=dev
```

Het commando toont het privépad. Vervang https://VOTRE-HOTE door de werkelijke HTTPS-oorsprong, inclusief poort. Lees de inloggegevens in .guardian-shield/initial-admin-credentials.txt of de aangegeven kluis. Bij de eerste aanmelding moet het wachtwoord worden gewijzigd. Deel dit bestand niet.

## 4. Verwijderen

Stop de workers en voer de commando’s op volgorde uit; controleer of elke stap slaagt. De kluis en sleutel blijven bewaard. Start de workers opnieuw en controleer de profiler in een privéomgeving.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Vervang voor het verwijderen van accounts en instellingen het eerste commando door het volgende en bevestig. Weigeren verandert niets. Controleer externe kluizen en aangepaste bestanden handmatig; bewaar sleutels van kluizen die behouden blijven.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Commando-overzicht

De hulp beschrijft de commando’s. Het toegangscommando vindt het paneel; de diagnose controleert de configuratie. Betrouwbare lokale toegang is vereist.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Commando-overzicht](../translations/nl_NL/commands.md) · [Documentatie-inhoud](../translations/nl_NL/README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

Configureer bij gebruik van Private Packagist deze repository vóór installatie en sla Composer-inloggegevens op volgens de organisatie-instructies. Sla nooit tokens op in Git.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
