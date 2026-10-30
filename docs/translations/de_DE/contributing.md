# Mitwirken

[Dokumentationsübersicht](README.md)

In isolierter Symfony-Testanwendung arbeiten. Geschäftsverfügbarkeit und Ablehnung unberechtigter Diagnosen erhalten. Anonyme Anfragen, widerrufene Sitzungen, Zeitpläne und Speicherausfälle auf betroffenen Versionen testen. Stabile Schlüssel, maskierte Texte und vollständige Übersetzungen verwenden. Keine Geheimnisse in Tests, Bildern oder Git; keine zwingende Netzabhängigkeit. Folgen und Prüfungen beschreiben.
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[Sicherheitsrichtlinie](security.md) · [Oberflächenbeispiele](examples.md)
