# Référence des commandes

[Sommaire de la documentation](README.md)

Exécuter depuis la racine Symfony, dans un terminal de confiance. Les exemples utilisent dev. Ne jamais placer un mot de passe, une clé ou un code de récupération dans les arguments, les journaux ou Git.

## Découvrir et demander de l’aide


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


Les options --env=dev et --no-interaction viennent de Symfony. --no-interaction ne fournit pas les secrets demandés ; ne pas l’utiliser pour une commande interactive de mot de passe. --help décrit la syntaxe ; -v augmente les détails. Un code 0 indique le succès annoncé par la commande ; --no-admin peut réussir sans portail utilisable.

## `vendor/bin/guardian`

Active le bundle en dev/test, vide le cache, lance guardian:setup --automatic, vide à nouveau le cache, puis exécute guardian:doctor et guardian:access. Arrêt à la première erreur ; une relance conserve comptes et clés. Refuse APP_ENV=prod et nécessite proc_open.

Sans argument, --help, -h ou help : aide sans modification. setup est la seule action ; elle n’accepte aucune option supplémentaire.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Référence des commandes |
|---|---|
| [`guardian:setup`](#guardiansetup) | Prépare la route privée, la clé, le coffre, les fichiers Symfony et un administrateur s’il en manque. |
| [`guardian:install`](#guardianinstall) | Initialise le coffre et crée un administrateur avec --username, admin par défaut. |
| [`guardian:access`](#guardianaccess) | Affiche le chemin du panneau et l’emplacement des identifiants initiaux, jamais le mot de passe. |
| [`guardian:doctor`](#guardiandoctor) | Vérifie le coffre, les permissions, la clé, la route, un administrateur actif et la cohérence des paramètres. |
| [`guardian:password:reset`](#guardianpasswordreset) | Change le mot de passe du compte username, demandé en saisie masquée selon la politique active, puis révoque toutes ses sessions et journalise l’action. |
| [`guardian:mfa:reset`](#guardianmfareset) | Efface le secret TOTP et le compteur du compte username, révoque ses sessions et journalise l’action. |
| [`guardian:login:unlock`](#guardianloginunlock) | Réinitialise les compteurs du compte username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Vide la liste IP et désactive le planning, le JIT et l’urgence distante. |
| [`guardian:lockdown`](#guardianlockdown) | on ferme les diagnostics et supprime toutes les sessions ; le site métier reste disponible. |
| [`guardian:emergency`](#guardianemergency) | Génère 5 codes à usage unique pour le compte username existant et remplace ses anciens codes. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Expédie les alertes en attente au destinataire alert_email avec la fonction PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Mesure des vérifications de hachage sur un secret synthétique, sans mot de passe utilisateur. |
| [`guardian:uninstall`](#guardianuninstall) | Prépare le retrait des fichiers générés attribuables à Guardian ; conserve par défaut comptes, paramètres, journaux et clé. |

## `guardian:setup`

Prépare la route privée, la clé, le coffre, les fichiers Symfony et un administrateur s’il en manque. Les comptes existants sont conservés. Refuse la configuration en production. Après une exécution directe, vider le cache et lancer les vérifications.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic : génère le compte admin et un secret dans un fichier privé 0600. --path : chemin explicite de 25 à 181 caractères, sans rotation d’un chemin existant. --no-admin : prépare sans créer d’administrateur ; le portail reste inutilisable s’il n’existe aucun compte actif. --username : proposition de nom pour la création interactive, admin par défaut ; ne renomme pas le compte automatique.

```text
--path: ^/[a-zA-Z0-9/_-]{24,180}$
/_profiler; /_wdt → ∅
```

```bash
php bin/console guardian:setup --username=operator --env=dev
php bin/console cache:clear --env=dev
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## `guardian:install`

Initialise le coffre et crée un administrateur avec --username, admin par défaut. Demande le mot de passe en saisie masquée et applique la politique active. Refuse un compte existant. Ne crée pas la route ni la configuration Symfony ; utiliser normalement l’assistant complet.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Identifiant : 3 à 64 caractères alphanumériques, . _ -

## `guardian:access`

Affiche le chemin du panneau et l’emplacement des identifiants initiaux, jamais le mot de passe. --base-url accepte une origine HTTPS réelle, avec port éventuel, sans identifiants, paramètres ni fragment. Remplacer l’hôte d’exemple par celui de l’application.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Vérifie le coffre, les permissions, la clé, la route, un administrateur actif et la cohérence des paramètres. Code de sortie 1 si un contrôle signale un problème. Les zones PHP CLI et PHP-FPM peuvent différer ; le diagnostic local ne teste pas l’exposition Internet ni les règles du proxy.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Change le mot de passe du compte username, demandé en saisie masquée selon la politique active, puis révoque toutes ses sessions et journalise l’action. Le compte doit exister. Aucun mot de passe ne se passe en argument.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Efface le secret TOTP et le compteur du compte username, révoque ses sessions et journalise l’action. Enrôler un nouvel authentificateur ; l’exigence MFA globale n’est pas désactivée et peut continuer à interdire le profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Réinitialise les compteurs du compte username. --ip ADRESSE efface aussi le compteur de cette origine IPv4/IPv6 ; sans cela, un blocage réseau peut subsister. --all efface tous les compteurs. Ne change ni comptes, ni mots de passe, ni sessions ; action journalisée.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Vide la liste IP et désactive le planning, le JIT et l’urgence distante. Ne retire pas l’authentification, la MFA, le verrouillage global ni les blocages de connexion. Reconfigurer les restrictions voulues dans le panneau après récupération.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on ferme les diagnostics et supprime toutes les sessions ; le site métier reste disponible. off lève uniquement le verrouillage global : les comptes doivent se reconnecter et les autres politiques restent applicables.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Génère 5 codes à usage unique pour le compte username existant et remplace ses anciens codes. Affichage une seule fois : conserver hors serveur dans un gestionnaire de secrets. L’usage distant nécessite l’option urgence, une session authentifiée, le mot de passe et un motif ; ne contourne pas le verrouillage global.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Expédie les alertes en attente au destinataire alert_email avec la fonction PHP mail(). Configurer le transport et une tâche planifiée, puis vérifier la livraison réelle. Les échecs restent en file ; code de sortie 1 si le destinataire est invalide ou si un envoi échoue.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Mesure des vérifications de hachage sur un secret synthétique, sans mot de passe utilisateur. --iterations accepte 1 à 10, 3 par défaut. Produit du JSON avec l’algorithme, le temps et les vérifications par seconde. Mesure un processus CPU local, pas un GPU ni un attaquant.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Prépare le retrait des fichiers générés attribuables à Guardian ; conserve par défaut comptes, paramètres, journaux et clé. Arrêter les workers et exécuter avant composer remove, puis vider le cache dev et redémarrer. Les fichiers personnalisés ou sans preuve de provenance restent à examiner.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge demande l’effacement des données avec confirmation, refus par défaut. --force nécessite --purge et confirme la destruction ; obligatoire avec --no-interaction pour une purge automatisée. Les coffres externes, liens et fichiers inconnus peuvent empêcher la purge complète : conserver la clé nécessaire et examiner le résultat.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Désinstallation et données


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Sauvegarder le coffre avec sa clé et tester la restauration. Aucun mécanisme ne régénère automatiquement une clé perdue. Après retrait, vérifier le profiler uniquement en environnement privé. En production : APP_DEBUG=0, WebProfilerBundle et collecte désactivés. Une chaîne d’audit locale ne prouve pas l’intégrité d’un hôte compromis.

[Désinstallation et données](desinstallation.md)
