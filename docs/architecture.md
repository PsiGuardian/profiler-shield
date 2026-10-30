# Architecture et frontières de confiance

[Sommaire de la documentation](README.md)


## Architecture et frontières de confiance

Le proxy assure TLS, normalisation et protection volumétrique. La passerelle contrôle les diagnostics avant le routeur ; les routes métier ne lisent pas les comptes ou sessions. L’identité Guardian est indépendante des comptes Symfony. Le coffre privé et sa clé doivent être sauvegardés ensemble, hors du répertoire public. Une compromission PHP ou de l’hôte peut exposer les deux. Le stockage est mono-hôte. Une erreur de démarrage PHP, de cache ou de proxy peut affecter toute l’application.

La liste IP accepte des adresses IPv4, IPv6 et des réseaux CIDR. Vérifiez la configuration des proxys de confiance Symfony avant son activation. Utilisez HTTPS pour les cookies sécurisés. Guardian ne corrige pas une exposition du serveur web ou de la base de données.

Guardian contrôle le profiler et la barre de débogage. Un compte administrateur ou développeur autorisé est nécessaire. MFA, horaires, JIT, restrictions réseau et verrouillage peuvent encore refuser l’accès. Le mode discret masque les points d’entrée ; il ne remplace pas ces contrôles.

| PHP / HTTP | Architecture et frontières de confiance |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| Session | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Invariants de sécurité

Refuser les diagnostics si identité, rôle, session, réseau, horaires, JIT ou verrouillage ne peuvent être vérifiés. Le navigateur ne décide pas des expirations. L’accès aux profils et la collecte sont distincts. Un appareil reconnu n’authentifie pas. L’urgence exige une identité, un code consommé et un audit durable ; une panne critique d’audit interdit l’ouverture. Une configuration invalide ne doit pas être partiellement appliquée. Les options facultatives n’imposent aucun service distant.

Sauvegarder le coffre avec sa clé et tester la restauration. Aucun mécanisme ne régénère automatiquement une clé perdue. Après retrait, vérifier le profiler uniquement en environnement privé. En production : APP_DEBUG=0, WebProfilerBundle et collecte désactivés. Une chaîne d’audit locale ne prouve pas l’intégrité d’un hôte compromis.

## Journal et alertes

Le niveau et la durée de conservation déterminent les événements disponibles, avec une limite de 5000 entrées. Filtrez par type et compte avant de changer de page. Les alertes sont mises en file ; configurez le transport et leur envoi. Une chaîne d’empreintes locale ne garantit pas l’intégrité sur un hôte compromis.
