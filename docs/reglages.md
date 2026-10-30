# Configuration

[Sommaire de la documentation](README.md)


## Protections complémentaires

Chaque contrôle est regroupé avec ses réglages. Les options inactives sont grisées et conservées pour une prochaine activation.

Guardian contrôle le profiler et la barre de débogage. Un compte administrateur ou développeur autorisé est nécessaire. MFA, horaires, JIT, restrictions réseau et verrouillage peuvent encore refuser l’accès. Le mode discret masque les points d’entrée ; il ne remplace pas ces contrôles.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Profil | Configuration |
|---|---|
| Indépendant · flexible | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| Équipe · renforcé | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| Sensible · contrôlé | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Paramètre | Configuration | Valeur initiale |
|---|---|---|
| `stealth` | Retourne 404 au lieu de 403 sur les routes de diagnostic non autorisées. | `true` |
| `mfa` | Tous les comptes doivent disposer d’un TOTP enrôlé avant l’activation. | `false` |
| `idle_minutes` | Minutes sans activité autorisée avant expiration. | `60` |
| `absolute_hours` | Heures, quelle que soit l’activité. | `8` |
| `remember_days` | Jours. Ne remplace ni l’authentification ni les horaires. | `14` |
| `schedule_enabled` | Décidez des moments où les diagnostics sont accessibles. Les pages métier restent disponibles. | `false` |
| `weekdays` | Cliquez sur les jours pour autoriser ou exclure les accès. | `1,2,3,4,5` |
| `start` | Ouverture | `09:00` |
| `end` | Fermeture | `18:00` |
| `timezone` | Détecté depuis le processus PHP qui sert cette page. PHP CLI, PHP-FPM et le système peuvent utiliser des fuseaux différents. Vérifiez la configuration réelle avant de valider. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | La route privée renvoie 404 hors horaires. Récupération locale uniquement. | `false` |
| `jit_enabled` | N’ouvre le profiler que pour une période de diagnostic demandée. | `false` |
| `jit_minutes` | En minutes, utilisée uniquement lorsque l’option est activée. | `30` |
| `ip_allowlist` | Attention : une erreur peut bloquer votre accès. Récupération locale : guardian:policy:reset. | `∅` |
| `audit_level` | Niveau de détail et durée de conservation du journal local. | `standard` |
| `audit_retention_days` | Nombre de jours, avant purge. | `30` |
| `alert_email` | Destinataire des alertes d’urgence ; ne doit pas dépendre de la disponibilité du site. | `∅` |
| `emergency_enabled` | Autorise une fenêtre exceptionnelle de 30 minutes, avec code unique et audit. | `false` |
| `login_rules` | Par défaut : 10 échecs, puis 10 minutes de blocage. Les compteurs se cumulent par compte et par origine réseau, y compris lors des confirmations de mot de passe. | `10 / 10 / 30 / 30` |
| `password_policy` | Ces règles s’appliquent aux créations et changements de mot de passe, sur le web comme en CLI. Elles ne révèlent ni ne vérifient les anciens mots de passe. | `min_length=15` |
| `error_mode` | Pages du site : conserve les réponses 404 du site ; pour une exception, utilise son modèle 404 si possible, sinon une page neutre. Neutre : message standard. Personnalisé : utilise votre texte. | `native` |
| `error_reference` | La référence concerne les pages neutres générées par Guardian : aléatoire produit un nouveau code, pas un message aléatoire. Elle ne constitue pas un identifiant de suivi dans les journaux. | `none` |
| `error_reference_text` | Disponible uniquement avec une référence statique personnalisée. | `∅` |
| `error_custom_text` | Disponible uniquement en mode « Message neutre personnalisé ». Le texte est conservé lors d’un changement de mode. | `∅ → custom` |

Activez le planning, sélectionnez les jours, l’heure de début incluse et l’heure de fin exclue. Le fuseau enregistré détermine les heures locales. Aucune plage traversant minuit n’est acceptée. Masquer aussi le portail hors horaires empêche l’urgence à distance ; prévoyez un accès local aux commandes de récupération.

La durée d’inactivité, la durée absolue et la mémorisation de l’appareil ont des fonctions distinctes. Mémoriser un appareil ne contourne ni les horaires ni le JIT. La révocation coupe les sessions concernées côté serveur. Utilisez les comptes individuels pour attribuer les actions dans le journal.

Le JIT exige l’activation d’une fenêtre temporaire depuis une session autorisée. L’urgence, si elle est activée, requiert le mot de passe, un code à usage unique et un motif. Elle ne contourne pas le verrouillage général. Les commandes locales restent nécessaires si le portail est inaccessible.

La liste IP accepte des adresses IPv4, IPv6 et des réseaux CIDR. Vérifiez la configuration des proxys de confiance Symfony avant son activation. Utilisez HTTPS pour les cookies sécurisés. Guardian ne corrige pas une exposition du serveur web ou de la base de données.

Le niveau et la durée de conservation déterminent les événements disponibles, avec une limite de 5000 entrées. Filtrez par type et compte avant de changer de page. Les alertes sont mises en file ; configurez le transport et leur envoi. Une chaîne d’empreintes locale ne garantit pas l’intégrité sur un hôte compromis.

Réglez la longueur minimale et les catégories obligatoires dans Connexions et mots de passe. Les badges de création de compte décrivent la politique enregistrée. Les changements s’appliquent aux nouveaux mots de passe ; cochez le renouvellement obligatoire pour les comptes existants. Guardian choisit Argon2id quand PHP le propose, sinon bcrypt.

Filtrez tous les événements conservés par type et par compte. Les plus récents apparaissent en premier, par pages de 50. Le défilement ne charge pas les pages suivantes.

## Environnement


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


Le proxy assure TLS, normalisation et protection volumétrique. La passerelle contrôle les diagnostics avant le routeur ; les routes métier ne lisent pas les comptes ou sessions. L’identité Guardian est indépendante des comptes Symfony. Le coffre privé et sa clé doivent être sauvegardés ensemble, hors du répertoire public. Une compromission PHP ou de l’hôte peut exposer les deux. Le stockage est mono-hôte. Une erreur de démarrage PHP, de cache ou de proxy peut affecter toute l’application.

Sauvegarder le coffre avec sa clé et tester la restauration. Aucun mécanisme ne régénère automatiquement une clé perdue. Après retrait, vérifier le profiler uniquement en environnement privé. En production : APP_DEBUG=0, WebProfilerBundle et collecte désactivés. Une chaîne d’audit locale ne prouve pas l’intégrité d’un hôte compromis.

## Récupération et vérifications

Vide la liste IP et désactive le planning, le JIT et l’urgence distante. Ne retire pas l’authentification, la MFA, le verrouillage global ni les blocages de connexion. Reconfigurer les restrictions voulues dans le panneau après récupération.

on ferme les diagnostics et supprime toutes les sessions ; le site métier reste disponible. off lève uniquement le verrouillage global : les comptes doivent se reconnecter et les autres politiques restent applicables.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
