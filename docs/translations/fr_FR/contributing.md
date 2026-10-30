# Contribuer

[Sommaire de la documentation](README.md)

Travailler dans une application Symfony de test isolée. Préserver la disponibilité métier et le refus des diagnostics non autorisés. Tester anonymes, sessions révoquées, horaires et stockage défaillant sur les versions concernées. Utiliser des clés stables, échapper les textes et compléter les traductions. Aucun secret dans tests, captures ou Git ; aucune dépendance réseau obligatoire. Décrire effets et validations.
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[Politique de sécurité](security.md) · [Exemples d’interface](examples.md)
