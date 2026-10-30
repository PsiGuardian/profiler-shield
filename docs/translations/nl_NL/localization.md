# Vertalingen

[Documentatie-inhoud](README.md)

Het portaal biedt 28 regionale varianten en 25 catalogi; de vier Engelse varianten delen een catalogus. Statische teksten worden vertaald en veilig weergegeven. Gebruikersnamen, eigen berichten en gebruikersgegevens worden niet vertaald. Geen externe dienst ontvangt teksten. Audits controleren sleutels, parameters, UTF-8, commando’s en links; HTTP-tests bezoeken de weergaven in elke taalvariant.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
