# Pages d’erreur publiques

[Sommaire de la documentation](README.md)


## Pages d’erreur publiques

Préserver les pages du site ou présenter une réponse HTML neutre aux visiteurs. Les statuts HTTP restent exacts.

Le mode natif laisse Symfony afficher ses erreurs. Le mode générique ignore le texte personnalisé. Seul le mode personnalisé utilise le texte du visiteur. La référence publique peut être absente, aléatoire ou fixe ; elle est indépendante du message. Les codes HTTP restent inchangés.

Pages du site : conserve les réponses 404 du site ; pour une exception, utilise son modèle 404 si possible, sinon une page neutre. Neutre : message standard. Personnalisé : utilise votre texte.

Une exception 500 anonyme reçoit une page neutre. Les détails nécessitent une identité Guardian autorisée et GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Un modèle Twig absent ou défaillant déclenche le rendu neutre ; les contrôleurs d’erreur personnalisés de l’hôte nécessitent leur propre intégration. Les réponses JSON et les succès métier restent inchangés.

| error_mode | Configuration |
|---|---|
| native | Pages du site : conserve les réponses 404 du site ; pour une exception, utilise son modèle 404 si possible, sinon une page neutre. Neutre : message standard. Personnalisé : utilise votre texte. |
| generic | HTML 400–599 → Préserver les pages du site ou présenter une réponse HTML neutre aux visiteurs. Les statuts HTTP restent exacts. |
| custom | Disponible uniquement en mode « Message neutre personnalisé ». Le texte est conservé lors d’un changement de mode. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Disponible uniquement avec une référence statique personnalisée. |
| error_custom_text | Disponible uniquement en mode « Message neutre personnalisé ». Le texte est conservé lors d’un changement de mode. |

## Référence affichée

Un message différent par code ou une référence statique peut révéler la véritable erreur. Le statut HTTP ne doit pas être falsifié.

La référence concerne les pages neutres générées par Guardian : aléatoire produit un nouveau code, pas un message aléatoire. Elle ne constitue pas un identifiant de suivi dans les journaux.

Les choix sont contrôlés côté serveur avant enregistrement.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Réseau et déploiement

La liste IP accepte des adresses IPv4, IPv6 et des réseaux CIDR. Vérifiez la configuration des proxys de confiance Symfony avant son activation. Utilisez HTTPS pour les cookies sécurisés. Guardian ne corrige pas une exposition du serveur web ou de la base de données.

Refuser les diagnostics si identité, rôle, session, réseau, horaires, JIT ou verrouillage ne peuvent être vérifiés. Le navigateur ne décide pas des expirations. L’accès aux profils et la collecte sont distincts. Un appareil reconnu n’authentifie pas. L’urgence exige une identité, un code consommé et un audit durable ; une panne critique d’audit interdit l’ouverture. Une configuration invalide ne doit pas être partiellement appliquée. Les options facultatives n’imposent aucun service distant.
