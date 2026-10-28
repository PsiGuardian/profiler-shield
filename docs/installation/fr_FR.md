# ΩGuardian Profiler Shield — Installation et utilisation

PHP 8.3 ou supérieur ; Symfony 5.4, 6.4 ou 7.4. Symfony 8.1 nécessite PHP 8.4 ou supérieur. Composer et l’extension OpenSSL + Tokenizer sont requis. Installer WebProfilerBundle pour utiliser le profiler.

## 1. Installer

Exécuter depuis la racine de l’application Symfony, contenant composer.json et bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Configurer

```bash
php vendor/bin/guardian setup
```

L’assistant active le bundle en dev/test, prépare le coffre et le compte administrateur, vide le cache et vérifie la configuration. Chaque étape doit réussir avant la suivante. Garder l’application privée pendant cette opération. Une relance conserve les comptes et les clés existants.

## 3. Ouvrir le panneau

```bash
php bin/console guardian:access --env=dev
```

La commande affiche le chemin privé du panneau. Remplacer https://VOTRE-HOTE par l’origine HTTPS réelle de l’application, port compris. Lire les identifiants dans .guardian-shield/initial-admin-credentials.txt, ou dans le coffre indiqué. Le premier accès impose de changer le mot de passe. Ne pas partager ce fichier.

## 4. Désinstaller

Arrêter les workers, puis exécuter les commandes dans cet ordre, après réussite de chaque étape. Le coffre et sa clé sont conservés. Redémarrer les workers et vérifier le profiler dans un environnement privé.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Pour supprimer également les comptes et paramètres, remplacer la première commande par celle-ci et confirmer la suppression. Un refus ne modifie rien. Vérifier manuellement les coffres externes et fichiers personnalisés ; conserver la clé de tout coffre à garder.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Référence des commandes

L’aide décrit les commandes. La commande d’accès retrouve le panneau ; le diagnostic vérifie la configuration. Ces opérations nécessitent un accès local de confiance.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Référence des commandes](../translations/fr_FR/commands.md) · [Sommaire de la documentation](../translations/fr_FR/README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

Pour une organisation utilisant Private Packagist, configurer ce dépôt avant l’installation et enregistrer les identifiants Composer selon les instructions de l’organisation. Ne jamais ajouter de jeton au dépôt Git.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
