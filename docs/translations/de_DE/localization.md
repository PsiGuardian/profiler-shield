# Übersetzungen

[Dokumentationsübersicht](README.md)

Das Portal bietet 28 regionale Varianten und 25 Kataloge; die vier englischen Varianten teilen einen Katalog. Statische Texte werden übersetzt und maskiert. Benutzernamen, eigene Nachrichten und Benutzerdaten werden nicht übersetzt. Kein externer Dienst erhält Texte. Prüfungen kontrollieren Schlüssel, Parameter, UTF-8, technische Befehle und Dokumentationslinks; HTTP-Tests besuchen die Ansichten jeder Sprache.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
