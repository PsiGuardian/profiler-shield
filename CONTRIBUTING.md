# Contribuer à ΩGuardian Profiler Shield

Merci de votre intérêt. Les contributions doivent préserver deux invariants : **site métier opérationnel** et **profiler inaccessible sans autorisation fiable**.

## Démarrer

1. Cloner le dépôt, installer PHP/Composer et travailler dans une application Symfony de test avec un dépôt Composer `path` local.
2. Exécuter les tests locaux : `php tests/run.php`, `php tests/flow_smoke.php`, `php tests/setup_smoke.php`, `php tests/setup_interactive_smoke.php`, `php tests/doctor_smoke.php`.
3. Exécuter les tests PHPUnit et les tests HTTP sur les versions Symfony effectivement concernées.
4. Décrire les scénarios de panne, les effets sur les routes métier et les conséquences pour les développeurs.

## Qualité des contributions

- PHPDoc et commentaires français pour chaque décision de sécurité non triviale.
- Pas de dépendance réseau obligatoire, d’endpoint secret codé en dur, ni de secrets dans les tests ou les captures.
- Ajouter des tests négatifs (requêtes publiques, sessions révoquées, TimeLock, stockage indisponible).
- Toute nouvelle chaîne visible doit employer une **clé de traduction stable** ; ne plus ajouter de remplacement textuel HTML.
- Éviter toute fonctionnalité déclarée compatible sans validation sur un noyau Symfony réel.

**Vulnérabilités :** suivre `SECURITY.md`, jamais d’issue publique avec preuve d’exploitation.
