# Désinstaller ΩGuardian

Arrêter les workers de développement, puis préparer le retrait avant de retirer le code :

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Chaque commande doit réussir avant la suivante. Redémarrer les workers et vérifier le retour du profiler natif uniquement dans un environnement privé.

## Conserver les données

C’est le choix par défaut. Les comptes, paramètres, secrets MFA, journaux et clé de déchiffrement restent présents pour une réinstallation. Les fichiers générés sont retirés uniquement si le reçu d’installation et leurs empreintes prouvent leur provenance. Les fichiers personnalisés ou sans preuve de provenance sont conservés pour revue manuelle. Les variables et fichiers de l’application sont préservés.

## Demander l’effacement

Remplacer la première commande par :

```bash
php bin/console guardian:uninstall --purge --env=dev
```

La commande demande une confirmation, non par défaut. Un refus annule cette commande sans modification. L’effacement doit être demandé avant Composer.

Les coffres externes, liens symboliques, fichiers inconnus ou permissions insuffisantes empêchent un effacement complet automatique : la clé reste conservée si nécessaire. Ne pas supprimer une clé tant qu’un coffre doit rester exploitable. Une automatisation intentionnelle doit préciser `--purge --force --no-interaction`.

## Si le paquet a déjà été retiré

Réinstaller temporairement la même version sans lancer le setup, puis préparer explicitement son retrait et retirer à nouveau le paquet. Examiner les configurations personnalisées manuellement. Le tutoriel complet se trouve dans [installation](installation.md).
