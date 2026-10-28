# Architecture et frontières de confiance

## Objectifs

- Garder le site principal indépendant des erreurs du moteur Guardian.
- Contrôler les routes et les données de diagnostic **avant** leur consultation.
- Ne jamais traiter un chemin secret comme une identité.
- Réduire les contraintes : aucun service central obligatoire, aucune API distante.
- Interdire une escalade d'options (TimeLock, cookies, urgence) en cas de panne.

## Requête HTTP

1. Reverse proxy / serveur web : TLS, filtrage du trafic volumétrique,
   normalisation des chemins et éventuel VPN.
2. `GuardianSubscriber` sur `kernel.request` priorité 4096 : si route métier,
   **aucune lecture des comptes/sessions** ; si diagnostic, vérifie identité,
   réseau, horaires, lockdown et JIT. En échec, renvoie 404/403 sans erreur 500.
3. Router Symfony : contrôleur métier normal ou route privée Guardian.
4. `CollectionSubscriber` optionnel sur `kernel.request` priorité 3072 : ferme
   le collecteur, puis l'ouvre si requête autorisée ; uniquement lorsque
   `GUARDIAN_COLLECTION_BRIDGE=1`. Son intégration est encore à certifier.
5. `GuardianSubscriber::sanitize` sur `kernel.response` priorité -4096 :
   retire des en-têtes de diagnostic susceptibles de divulguer des tokens.
6. Les réponses du portail ont `no-store`, CSP restrictive, no-referrer,
   frame deny, et n'appellent aucun CDN.

## Frontières et invariants

- **Données métier** : ne dépendent pas du FileStore Guardian.
- **Compte Guardian** : distinct de `UserInterface` et des firewalls métier.
- **Portail** : indépendant de Twig, sans assets publics.
- **Session** : jeton opaque aléatoire 256 bits, empreinte SHA-256 dans une collection chiffrée AES-256-GCM au repos.
- **Cookie** : `__Host-`, Secure, HttpOnly, SameSite=Lax, Path=/, sans Domain.
- **Appareil reconnu** : état non authentifiant ; ne débloque aucun profil.
- **MFA** : TOTP à secret chiffré ; le serveur connaît le temps et rejette
  la réutilisation d'un compteur déjà consommé.
- **TimeLock** : interprète les horaires dans un fuseau IANA ; bornes [start,end).
- **Urgence** : authentification requise ; code à usage unique consommé sur le
  disque ; audit durable avant ouverture de l'autorisation.
- **Lockdown** : refus immédiat des accès au profiler, y compris pour les sessions existantes ; révocation individuelle disponible séparément.
- **Audit** : champs limités, pas de mot de passe, cookie ou URL privée ;
  chaînage local dont les garanties sont limitées si le serveur est compromis.

## Coffre persistant v2

Le chemin par défaut est `<projet>/.guardian-shield/`, en dehors de `var` et du document root normal `public/`. Une variable d'environnement `GUARDIAN_STORAGE_DIR` permet de choisir un volume privé. Chaque collection JSON est une enveloppe AES-256-GCM (nonce aléatoire de 12 octets, tag 16 octets, données authentifiées par nom de collection) avec une sous-clé HMAC dérivée de `GUARDIAN_ENCRYPTION_KEY`. La clé n'est pas dans le coffre, mais la compromission du processus PHP ou de l'hôte peut donner accès aux deux.

Le coffre est mono-hôte ; Redis/SQL distribué nécessiterait une implémentation et des tests distincts. Aucun mécanisme automatique ne régénère la clé.

## Disponibilité et limites

Les erreurs du stockage Guardian sur des URLs sensibles produisent une réponse
de refus. Le service HTTP métier ordinaire évite ces accès. En revanche, un
bug PHP pendant le chargement d'une classe, une extension incompatible,
une règle reverse proxy incorrecte ou un déploiement de cache invalide peuvent
affecter toute l'application. Ce sont des risques d'installation à tester et
à gérer par le pipeline de déploiement.

## Menaces considérées

| Menace | Réponse retenue | Limite |
|---|---|---|
| Scan sur /_profiler | Interception pré-routage | Proxy/CDN peut exposer autre chose |
| Deviner URL privée | Chemin aléatoire, pas d'URL publique | Security by obscurity insuffisante seule |
| Cookie volé | HTTPS, HttpOnly, expiration/révocation | Poste compromis reste un risque |
| Mot de passe compromis | MFA TOTP facultative/obligatoire | Phishing OTP possible : passkeys futures |
| Réutilisation TOTP | Dernier compteur consommé en transaction | Horloge cohérente nécessaire |
| Brute-force | 10 échecs / 10 min par défaut, paliers configurables par compte et origine | Limites réseau complémentaires recommandées ; récupération CLI locale |
| Code d'urgence volé | Auth normale + one-shot + expiration | Protéger le gestionnaire de secrets |
| Stockage indisponible | Profil refusé, site métier indépendant | Crash au boot non neutralisable |
| Fuite par toolbar | Désactivation officielle dans l'hôte | Non garanti si intégrateur ignore la procédure |
| Collecte anonyme | `framework.profiler.collect: false` | Pont de collecte encore non certifié |
| Mauvaise restriction IP | `guardian:policy:reset` | Accès shell requis |
| Compromission de l'hôte | Journaux chaînés localement | Attaquant root peut les remplacer |

## Performances

- Pas de stockage consulté sur une route métier par le `GuardianSubscriber`.
- Avec **bridge désactivé**, le coût résiduel est la reconnaissance de chemin
  et la suppression éventuelle de trois en-têtes.
- Avec **bridge activé**, les requêtes de navigateur muni du cookie Guardian
  consultent le stockage pour décider de collecter. Ce coût est explicitement
  optionnel et doit être mesuré sous charge.
- Stockage fichiers recommandé uniquement en mono-hôte à faible volume.

### Périmètre des chemins

La passerelle pré-routage protège les chemins standards `/_profiler` et `/_wdt` ainsi que des variantes à front controller. Elle
n’intercepte pas une route métier de type `/docs/_profiler/guide`. Les projets
qui montent le profiler sur un préfixe personnalisé doivent ajouter une règle
d’intégration équivalente et la tester avant exposition.
