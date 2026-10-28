# Réglages ΩGuardian : comment décider sans perdre de temps ?

Pour chaque option : **effet exact**, **quand l'activer**, **exemple**,
**conséquence de désactivation** et **impact quotidien**. Les réglages à risque
sont confirmés par mot de passe administrateur. Les valeurs ci-dessous
correspondent au cœur livré, sauf lorsqu'une fonction est explicitement
signalée comme future ou expérimentale.

## Principes non négociables

- Le contrôle d'accès au profiler est obligatoire lorsque Guardian est en place.
- Une session inconnue, expirée ou révoquée n'autorise jamais un accès.
- La panne du stockage Guardian refuse l'accès aux diagnostics.
- Le site principal n'est pas soumis aux horaires de Guardian.
- Le WebProfiler et la toolbar doivent rester désactivés en production.
- Les clés, mots de passe et codes d'urgence ne vont jamais dans Git.

## 1. Modes de préconfiguration

| Profil | Utilisateur idéal | Changements | Impact |
|---|---|---|---|
| Indépendant | Travail seul, recette privée | MFA facultative ; horaires et JIT désactivés ; session 8 h | Faible |
| Équipe | Agence / recette partagée | MFA obligatoire une fois les comptes enrôlés ; cookie appareil 7 j | Modéré |
| Sensible | Environnement soumis à contrôle | MFA, horaires et JIT ; session 4 h ; inactivité 15 min | Élevé |

Les profils sont des points de départ. Ils ne constituent pas une garantie
contre les failles du framework Symfony ou de l'application.

## 2. `stealth`

**Fonction :** refuser les routes de diagnostic avec 404 générique plutôt
qu'un 403 explicite. Le chemin de connexion Guardian est défini séparément.

**Activer :** installation de recette accessible à Internet.
**Exemple :** requête `GET /_profiler/` anonyme → 404 générique.
**Désactiver :** obtenir un refus 403, utile dans un réseau interne où l'on
privilégie la clarté des statuts HTTP.
**Impact développeur :** nul lorsque la session est autorisée.
**Attention :** cela ne rend pas une installation invisible ou invulnérable.

## 3. `GUARDIAN_LOGIN_PATH`

**Fonction :** route d'entrée privée, aléatoire et personnalisable. Aucune
route publique par défaut n'est créée.

**Activer :** variable nécessaire à l'exposition du portail Guardian.
**Exemple :** `/internal/` suivi d'au moins 48 caractères hexadécimaux générés
avec `random_bytes()`, différente pour chaque installation.
**Désactiver :** portail inaccessible, même pour un administrateur ; les routes
protégées restent fermées.
**Impact développeur :** ajouter la route à son gestionnaire de secrets.
**Attention :** rotation implique rebuild du cache route et nouveau favori.

## 4. MFA `mfa`

**Fonction :** imposer à tous les comptes une application TOTP lors de la
connexion. Chaque code consommé est invalidé pour un nouveau login.

**Activer :** équipe, recette exposée ou comptes administrateurs sensibles.
**Exemple :** un prestataire doit saisir mot de passe puis un code à 6 chiffres.
**Désactiver :** connexion par mot de passe seul, à réserver aux environnements
isolés lorsque le risque le permet.
**Impact développeur :** une étape supplémentaire lors des connexions.
**Attention :** chaque compte doit être enrôlé avant la bascule. Le système
ne propose pas encore de passkeys/WebAuthn. La récupération MFA se fait en CLI.

## 5. `idle_minutes` et `absolute_hours`

**Fonction :** révoquer une session après inactivité ou une durée absolue.
**Activer :** ces deux seuils restent obligatoires et configurables.
**Exemple :** 60 min d'inactivité et 8 h absolues. Après pause déjeuner de plus
d'une heure, le développeur se reconnecte ; les droits ne restent pas ouverts.
**Désactiver :** pas proposé ; seuils augmentables selon l'environnement.
**Impact développeur :** faibles seuils = reconnexions plus fréquentes.
**Attention :** une durée de cookie ne suffit pas à sécuriser une session.

## 6. `remember_days`

**Fonction :** conserver un marqueur de reconnaissance non authentifiant.
**Activer :** postes personnels autorisés, jusqu'à la durée de reconnaissance
configurée (14 jours par défaut).
**Exemple :** un navigateur déjà utilisé est identifiable comme appareil
reconnu, mais son utilisateur doit encore s'authentifier si sa session a expiré.
**Désactiver :** ne pas cocher « reconnaître cet appareil » sur le formulaire.
**Impact développeur :** actuellement aucun avantage de reconnexion automatique.
**Attention :** le marqueur ne prolonge pas la session. Réauthentification
passkey simplifiée prévue pour une version ultérieure.

## 7. `schedule_enabled`, `weekdays`, `start`, `end`, `timezone`

**Fonction :** restreindre les diagnostics à des heures définies dans le
fuseau IANA (ex. `Europe/Paris`).
**Activer :** équipes avec horaires réguliers.
**Exemple :** lundi-vendredi, 09h00–18h00. Un vendredi à 18h01, tout profil
reste fermé même si le cookie n'a pas expiré.
**Désactiver :** indépendants, astreintes, équipes internationales.
**Impact développeur :** aucun pendant les horaires ; blocage hors horaires.
**Attention :** une seule plage par jour, commune à l'installation, est
implémentée. Les plages traversant minuit et calendriers multi-équipes sont à
implémenter. La validation rejette début >= fin.

## 8. `portal_closed_outside_hours`

**Fonction :** retourner 404 également sur l'URL de connexion Guardian en dehors
de la plage horaire (si TimeLock activé).
**Activer :** entreprise désirant une fermeture distante stricte.
**Exemple :** aucun formulaire Guardian le dimanche ; site métier inchangé.
**Désactiver :** développeur souhaitant consulter son dashboard ou demander une
dérogation en dehors des horaires.
**Impact développeur :** significatif pour les astreintes.
**Attention :** l'urgence distante est INCOMPATIBLE ; seule la récupération
locale CLI est disponible. La validation interdit la combinaison.

## 9. `jit_enabled`, `jit_minutes`

**Fonction :** l'identité peut rester connectée, mais le droit de profiler est
accordé pendant un créneau court activé par l'utilisateur.
**Activer :** accès ponctuel en environnement sensible.
**Exemple :** 30 minutes de diagnostic pour analyser une erreur de paiement.
**Désactiver :** développer avec un profiler accessible en continu durant sa
session normale autorisée.
**Impact développeur :** un clic pour ouvrir le créneau.
**Attention :** la collecte effective dépend de la configuration du profiler Symfony. Le JIT contrôle la consultation des routes.

## 10. `ip_allowlist`

**Fonction :** limiter l'interface et le profiler à des adresses IPv4/IPv6 ou
sous-réseaux CIDR déclarés.
**Activer :** agence connectée via VPN, poste de bastion connu.
**Exemple :** `192.0.2.0/24, 2001:db8::/32` (préfixes réservés à la documentation).
**Désactiver :** déplacements fréquents, réseau mobile sans VPN.
**Impact développeur :** aucun depuis un réseau autorisé ; blocage ailleurs.
**Attention :** indiquer les `trusted_proxies` correctement dans Symfony.
Récupération depuis le serveur : `guardian:policy:reset`.

## 11. Anti-brute-force

**Fonction :** maintenir des compteurs indépendants par compte et par origine réseau, avec paliers configurables
haché, limitant les demandes de connexion après huit échecs rapprochés.
**Activer :** intégré au socle de connexion.
**Exemple :** des essais de mots de passe incorrects sont temporairement
bloqués, puis le compteur expire.
**Désactiver :** aucune option de désactivation totale dans le bundle livré.
**Impact développeur :** nul en cas d'identifiants corrects.
**Attention :** la protection DDoS/distribuée doit aussi être mise en place
au serveur web ou reverse proxy.

## 12. `audit_level` et `audit_retention_days`

**Fonction :** journaliser les actions Guardian sans URL secrète ni cookie.
**Activer :** recommandé même en développement indépendant.
**Exemple :** une dérogation d'urgence garde identifiant, heure UTC et motif.
**Niveaux :** `minimal` exclut les connexions et ouvertures JIT ; `standard`
conserve les principaux événements ; `advanced` prépare une politique élargie.
**Désactiver :** l'audit critique reste exigé (pas d'interrupteur « off »).
**Impact développeur :** nul sur les pages métier ; écritures lors d'actions Guardian.
**Attention :** stockage local mono-hôte, chaînage détectant certaines
modifications mais non résistant à un administrateur système malveillant.
Le niveau avancé ne propose pas encore un export SIEM automatique.

## 13. `alert_email`

**Fonction :** destinataire des notifications de sécurité ; l'envoi est
traité par `guardian:alerts:flush` depuis cron.
**Activer :** organisation avec responsable sécurité.
**Exemple :** un accès d'urgence est ajouté à la file, puis notifié par mail.
**Désactiver :** laisser l'adresse vide ; les alertes resteront sur disque.
**Impact développeur :** aucune latence mail sur les requêtes métier.
**Attention :** `mail()` est minimal : configurer l'envoi et un cron, tester
livraison ; transport SMTP dédié prévu plus tard.

## 14. `emergency_enabled`

**Fonction :** donner 30 minutes d'accès exceptionnel à un utilisateur déjà
connecté, après vérification du mot de passe et d'un code à usage unique.
**Activer :** agences ayant des astreintes et un responsable sécurité.
**Exemple :** intervention urgente un samedi en dehors du créneau normal.
**Désactiver :** équipe sans besoin d'urgence distante ; la commande shell de
récupération reste disponible.
**Impact développeur :** uniquement pendant un incident.
**Attention :** exécuter `guardian:emergency utilisateur` côté serveur pour
générer cinq codes. Un code n'est affiché qu'une fois, doit être protégé hors
site et ne remplace pas l'authentification.

## 15. Commande `guardian:lockdown on/off`

**Fonction :** fermer immédiatement les diagnostics et invalider les sessions.
**Activer :** suspicion de compromission ou intervention sécurité.
**Exemple :** découverte d'un token révélé, mise en lockdown pendant analyse.
**Désactiver :** `guardian:lockdown off` uniquement depuis shell ; les développeurs
devront de nouveau se connecter.
**Impact développeur :** aucun effet sur les pages métier, blocage du profiler.
**Attention :** ne remplace pas un retrait de WebProfilerBundle en production.

## 16. `GUARDIAN_COLLECTION_BRIDGE`

**Fonction :** contrôler la collecte Symfony au niveau de la requête authentifiée,
avec fermeture par défaut.
**Activer :** seulement après tests end-to-end réussis sur **votre version**
de Symfony, avec `framework.profiler.collect: false` et toolbar désactivée.
**Exemple :** une requête du navigateur d'un développeur autorisé est collectée ;
celle d'un visiteur sans cookie ne l'est pas.
**Désactiver :** valeur 0 (par défaut, sûre et restrictive) ; aucun nouveau profil.
**Impact développeur :** frais de collecte et de vérification de session quand
actif ; mesurer cet impact sur l’infrastructure utilisée.

## 17. Security Doctor

**Fonction :** contrôler l'état du stockage, la syntaxe du chemin et des
paramètres, ainsi que les préconditions d'intégration à vérifier manuellement.
**Activer :** commande `guardian:doctor` au déploiement et après mise à jour.
**Exemple :** l'administrateur est averti si la clé de chiffrement TOTP manque.
**Désactiver :** ne pas le planifier, mais toujours l'exécuter avant publication.
**Impact développeur :** nul hors déploiement.
**Attention :** Doctor local ne confirme ni l'exposition externe ni la
configuration réelle de la toolbar. Ces contrôles nécessitent des tests HTTP.

## Scénarios de référence

**Indépendant sur poste local :** profil solo, TimeLock désactivé, MFA au choix,
bridge de collecte activé uniquement après validation. Les contraintes sont
minimales.

**Agence de dix développeurs en recette :** comptes individuels, MFA enrôlée,
réseau VPN, sessions 8 h, audit standard, créneaux JIT selon les risques. La
fermeture horaire reste facultative si les horaires sont irréguliers.

**Prestataire externe pour deux jours :** créer un compte personnel, activer MFA,
limiter l'accès par IP/VPN et le désactiver après mission. Désactiver le compte à la fin de la mission.

**Site en production :** `APP_DEBUG=0`, pas de WebProfilerBundle, aucune collecte.
Ne pas utiliser Guardian pour justifier l'exposition du profiler de production.



## Connexions et mots de passe

Les paliers de limitation et les exigences du mot de passe sont décrits dans [authentification](authentication.md). La création de compte et le renouvellement présentent les règles enregistrées sous forme de badges. Enregistrer la politique avant de créer un compte pour appliquer ses nouvelles exigences.

## Consultation des listes

Le journal filtre l’ensemble des événements conservés avant de les paginer par 50. Les filtres type et compte se combinent ; les liens précédent/suivant conservent ces filtres. Une zone à hauteur limitée permet de défiler dans chaque page. Les filtres de comptes par nom et rôle agissent localement sur les comptes affichés. Sans JavaScript, les comptes restent tous consultables et les filtres du journal restent opérationnels.

Le sélecteur de langue s’ouvre explicitement, permet une recherche locale parmi les 28 choix et n’applique rien avant le bouton de validation. La liste reste défilable au clavier. Le guide intégré couvre les protections et limites de l’interface courante.
