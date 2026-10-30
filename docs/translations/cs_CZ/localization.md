# Překlady

[Obsah dokumentace](README.md)

Portál nabízí 28 regionálních variant a 25 katalogů; čtyři anglické varianty sdílejí katalog. Statický text se překládá a escapuje. Jména, vlastní zprávy a uživatelská data se nepřekládají. Vzdálené služby texty nedostávají. Audit ověřuje klíče, parametry, UTF-8, příkazy a odkazy; HTTP testy navštěvují pohledy všech variant.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
