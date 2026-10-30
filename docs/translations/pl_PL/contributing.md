# Współtworzenie

[Spis dokumentacji](README.md)

Pracuj w izolowanej aplikacji testowej Symfony. Zachowaj dostępność i odmowę nieuprawnionej diagnostyki. Testuj anonimowe żądania, cofnięte sesje, harmonogramy i błędy magazynu na właściwych wersjach. Używaj stałych kluczy, escapowania i pełnych tłumaczeń. Bez sekretów w testach, obrazach i Git; bez obowiązkowej sieci. Opisz skutki i walidację.
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[Polityka bezpieczeństwa](security.md) · [Przykłady interfejsu](examples.md)
