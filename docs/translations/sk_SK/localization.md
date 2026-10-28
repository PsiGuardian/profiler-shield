# Preklady

[Obsah dokumentácie](README.md)

Portál ponúka 28 regionálnych variantov a 25 katalógov; štyri anglické varianty zdieľajú katalóg. Statický text sa prekladá a escapuje. Mená, vlastné správy a používateľské dáta sa neprekladajú. Vzdialené služby texty nedostávajú. Audit overuje kľúče, parametre, UTF-8, príkazy a odkazy; HTTP testy navštevujú zobrazenia všetkých variantov.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
