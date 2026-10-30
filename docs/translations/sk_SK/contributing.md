# Prispievanie

[Obsah dokumentácie](README.md)

Pracujte v izolovanej testovacej aplikácii Symfony. Zachovajte dostupnosť a odmietnutie neoprávnenej diagnostiky. Testujte anonymné požiadavky, zrušené relácie, rozvrhy a nedostupné úložisko na daných verziách. Používajte stabilné kľúče, escapovanie a úplné preklady. Bez tajomstiev v testoch, snímkach a Gite; bez povinnej siete. Opíšte dôsledky a overenie.
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[Bezpečnostná politika](security.md) · [Príklady rozhrania](examples.md)
