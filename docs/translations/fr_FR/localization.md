# Traductions

[Sommaire de la documentation](README.md)

Le portail propose 28 variantes régionales et 25 catalogues ; les quatre variantes anglaises partagent leur catalogue. Les textes statiques sont traduits puis échappés. Les identifiants, messages personnalisés et données utilisateur ne sont pas traduits. Aucun service distant ne reçoit les textes. L’audit vérifie clés, paramètres, UTF-8, commandes techniques et liens de documentation ; les tests HTTP parcourent les vues dans chaque locale.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
