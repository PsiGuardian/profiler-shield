# Přispívání

[Obsah dokumentace](README.md)

Pracujte v izolované testovací aplikaci Symfony. Zachovejte dostupnost a odmítnutí neoprávněné diagnostiky. Testujte anonymní požadavky, zrušené relace, rozvrhy a nedostupné úložiště na daných verzích. Používejte stabilní klíče, escapování a úplné překlady. Bez tajemství v testech, snímcích a Gitu; bez povinné sítě. Popište důsledky a ověření.
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[Bezpečnostní politika](security.md) · [Příklady rozhraní](examples.md)
