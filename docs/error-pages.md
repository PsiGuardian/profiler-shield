# Pages d'erreur et protection des diagnostics

## Comportement par défaut (`native`)

- Une réponse 404 **déjà produite** par un contrôleur du site conserve son corps HTML original. Guardian ne réécrit pas les réponses métier réussies.
- Sur une **exception HTTP 404**, Guardian cherche le modèle Twig personnalisé de l'hôte : `templates/bundles/TwigBundle/Exception/error404.html.twig`, puis `error.html.twig`. Si Twig n'est pas installé, que le modèle manque ou que son rendu échoue, Guardian renvoie une page HTML neutre, sans trace Symfony ni nom Guardian.
- Le rendu via le TwigBundle de Symfony exige `twig` opérationnel dans le conteneur. Les rendus d'erreur via un contrôleur personnalisé de l'hôte ne sont pas utilisés automatiquement ; ils doivent être testés séparément.
- Une **exception 500** anonyme est neutralisée : un stack trace ou une exception d'application ne doit jamais être exposé aux visiteurs. Un compte Guardian expressément autorisé retrouve les erreurs détaillées si `GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1`.
- Les routes `/_profiler` et `/_wdt` restent contrôlées par Guardian et renvoient un refus avant le routeur si la session n'est pas autorisée.

## Mode `generic` ou `custom`

Dans la configuration Guardian, section « 04 · Pages d'erreur et discrétion » :

1. `native` (par défaut) : conserver la page personnalisée de l'hôte si possible.
2. `generic` : sur les réponses publiques **HTML** d'erreur (400–599), montrer un même message neutre, sans code d'erreur visible.
3. `custom` : montrer un texte personnalisé de 360 caractères maximum, échappé en HTML. Il n'est pas permis d'injecter du HTML/JavaScript. La page reste strictement locale.
4. Référence : aucune (recommandé), aléatoire indépendante du statut, ou statique personnalisée (risque de corrélation).

La sauvegarde exige l'authentification Guardian admin, le mot de passe et le jeton CSRF. Les changements sont journalisés. Le mécanisme ne touche pas aux réponses JSON, aux succès métier ou aux pages détaillées pour les développeurs autorisés.

### Sécurité et limites

**Ne pas changer les codes HTTP réels pour les tromper.** Une erreur 404 doit rester HTTP 404 ; afficher « 200 » dans le HTML ne trompera pas un scanner, et renvoyer un vrai 200 fausserait les caches, les robots et les moniteurs de santé.

Il n'existe aucune méthode pour rendre une réponse 404 *indiscernable* par tous les scanners : les codes HTTP, en-têtes du frontal, taille, timing, négociation de contenu et chemins accessibles peuvent les différencier. Les pages neutres ne sont qu'une réduction de divulgation. Le déploiement doit aussi couper les signatures du serveur et PHP, interdire les diagnostics anonymes au niveau proxy, et effectuer des tests actifs sur l'hôte.

**Site en premier :** en cas d'erreur du coffre Guardian, les routes métier ne doivent pas échouer à cause du moteur de présentation. Les accès au profiler restent refusés en cas de panne.

### Tests à faire avant publication

- Une route inexistante avec modèle Twig personnalisé, sans modèle, et avec un modèle défaillant.
- Une route qui renvoie directement un `Response(..., 404)` avec une page personnalisée.
- Les erreurs HTTP 400, 403, 404, 429, 500, 503 (le statut reste exact).
- Les réponses `application/json` et `application/problem+json`, les pages de succès et les requêtes CORS.
- Avec et sans connexion Guardian ; session expirée, compte désactivé, TimeLock fermé, mode furtif.
- Symfony 7.4 en mode dev, reverse proxy et PHP-FPM.
