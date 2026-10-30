# Bijdragen

[Documentatie-inhoud](README.md)

Werk in een geïsoleerde Symfony-testapplicatie. Behoud beschikbaarheid en weigering van ongeautoriseerde diagnostiek. Test anonieme verzoeken, ingetrokken sessies, schema’s en opslagstoringen op betrokken versies. Gebruik stabiele sleutels, escape teksten en voltooi vertalingen. Geen geheimen in tests, screenshots of Git; geen verplichte netwerkafhankelijkheid. Beschrijf gevolgen en validatie.
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[Beveiligingsbeleid](security.md) · [Interfacevoorbeelden](examples.md)
