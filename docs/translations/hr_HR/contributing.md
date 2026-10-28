# Doprinosi

[Sadržaj dokumentacije](README.md)

Radite u izoliranoj Symfony testnoj aplikaciji. Očuvajte poslovnu dostupnost i odbijanje neovlaštene dijagnostike. Testirajte anonimne zahtjeve, opozvane sesije, raspored i kvarove pohrane na pogođenim verzijama. Koristite stabilne ključeve, sigurno kodiranje i potpune prijevode. Bez tajni u testovima, snimkama ili Gitu; bez obvezne mreže. Opišite učinke i provjere.
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[Sigurnosna politika](security.md) · [Primjeri sučelja](examples.md)
