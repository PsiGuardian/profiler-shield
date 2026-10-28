# Contribuire

[Indice della documentazione](README.md)

Lavorare in un’applicazione Symfony di test isolata. Preservare disponibilità e rifiuto dei diagnostici non autorizzati. Provare richieste anonime, sessioni revocate, orari e guasti di archiviazione sulle versioni coinvolte. Usare chiavi stabili, escape e traduzioni complete. Nessun segreto in test, schermate o Git; nessuna rete obbligatoria. Descrivere conseguenze e verifiche.
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[Politica di sicurezza](security.md) · [Esempi di interfaccia](examples.md)
