# Traductions

Le portail propose 28 variantes régionales, avec 25 catalogues linguistiques. Les variantes anglaises partagent le même catalogue. Chaque variante dispose de son [guide d’installation](installation/).

Les composants utilisent des clés de traduction et échappent les textes avant leur insertion dans le HTML. Les comptes, messages personnalisés et autres contenus saisis par les utilisateurs ne sont pas traduits. Les catalogues sont embarqués ; le portail ne transmet aucun texte à un service de traduction.

L’audit contrôle la couverture des clés, les paramètres, l’UTF-8 et les commandes techniques. Les tests HTTP parcourent les vues du portail dans les 28 variantes.

```bash
php tests/translation_audit.php --strict
composer test
```
