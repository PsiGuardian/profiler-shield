# Safe Native — erreurs publiques sécurisées (branche dev-1.0.1)

**Statut :** fonctionnalité expérimentale de la branche `dev-1.0.1`. Cette page ne constitue ni un avis de sécurité ni la documentation d'une version Packagist publiée.

## Objectif

Conserver le **statut HTTP** et les **contrats d'erreurs métier approuvés** sans divulguer les messages d'exception, les traces, les chemins internes ou les secrets aux visiteurs non autorisés.

La protection est directement intégrée dans le listener `PrivacySubscriber` existant, placé avant les pages Symfony de debug. **Aucun nouveau listener applicatif à priorité supérieure n'est nécessaire.**

### Comportement par défaut

- Réponse métier normale ou réponse d'erreur construite par un contrôleur : non modifiée en mode `native`, hormis les en-têtes de diagnostic interdits.
- Exception inconnue : **réponse neutre**, vrai statut HTTP, sans `Throwable::getMessage()`, trace ou previous exception.
- Exception reconnue **par un service mapper explicitement marqué** : code et texte publics validés, sans exposition automatique du message de l'exception.
- `error_mode=native|generic|custom` : comportement HTML déjà existant maintenu, indépendant de la décision de confidentialité.
- Échec d'un mapper ou du renderer : représentation neutre, jamais diffusion de l'exception secondaire.
- `/_profiler` et `/_wdt` : les décisions d'accès restent celles de Guardian ; les mappers métier ne rendent pas ces routes publiques.

> Un mapper est **du code de l'application hôte considéré comme fiable**. Guardian empêche la publication automatique des détails techniques mais ne peut pas détecter tous les secrets si un développeur les écrit intentionnellement dans un texte public approuvé. Ne jamais utiliser `$exception->getMessage()` pour créer un message public.

## Ajouter un mapper approuvé dans l'application Symfony

Exemple minimal à adapter au domaine métier. Pour limiter les informations publiées, **ne mapper que des exceptions métier explicites** et **n'exposer que des chaînes statiques publiques**.

```php
<?php
// src/Security/PublicOrderErrorMapper.php
namespace App\Security;

use App\Exception\OutOfStockException;
use OmegaGuardian\ProfilerShield\Security\PublicErrorMapperInterface;
use OmegaGuardian\ProfilerShield\Security\PublicErrorDescriptor;
use Symfony\Component\HttpFoundation\Request;

final class PublicOrderErrorMapper implements PublicErrorMapperInterface
{
    public function supports(\Throwable $error, Request $request): bool
    {
        return $error instanceof OutOfStockException
            && str_starts_with($request->getPathInfo(), '/api/orders/');
    }

    public function map(\Throwable $error, Request $request): ?PublicErrorDescriptor
    {
        return new PublicErrorDescriptor(
            409,
            'PRODUCT_OUT_OF_STOCK',
            'This product is currently unavailable.',
            ['X-Error-Code' => 'PRODUCT_OUT_OF_STOCK']
        );
    }
}
```

Ajoutez explicitement le tag dans `config/services.yaml` de **l'application** :

```yaml
services:
    App\Security\PublicOrderErrorMapper:
        tags:
            - { name: omega_guardian.public_error_mapper, priority: 100 }
```

L'activation du mapper est volontaire. Aucun scan des exceptions de l'application ne recherche automatiquement des messages à rendre publics. Les `PublicErrorDescriptor` de statut incompatible avec le code HTTP de l'exception sont rejetés : pour retourner un 409, l'exception hôte doit déjà être une `HttpExceptionInterface` de code 409 (ou avoir été transformée en exception HTTP 409 par le code métier).

### Exemple de réponse JSON approuvée

```json
{
  "error": {
    "code": "PRODUCT_OUT_OF_STOCK",
    "status": 409,
    "safe_message": "This product is currently unavailable."
  }
}
```

Si aucun mapper n'approuve l'exception, la réponse JSON neutre conserve la clé `message` historique et fournit un code de repli :

```json
{
  "message": "Request failed",
  "error": {
    "code": "REQUEST_FAILED",
    "status": 500
  }
}
```

Le schéma du fallback est enrichi par rapport à la version 1.0.0. Une API qui exige un schéma d'erreur **strict** devrait utiliser son propre mapper public et inclure ce schéma dans les tests du client consommateur.

## Formats de sortie

| Accept ou format explicite | Sortie |
|---|---|
| `application/json` | JSON sécurisé |
| `application/xml`, `text/xml` | XML sécurisé |
| `application/problem+json` | JSON Problem Details (RFC 9457) |
| `application/problem+xml` | XML Problem Details (RFC 9457) |
| `text/html` | Rendu HTML Guardian existant |
| `text/plain` | Texte neutre |
| `*/*` sur route `/api/` | JSON |
| `*/*` sur autre route | HTML |

La négociation gère les pondérations `q` et les formes `application/*+json` et `application/*+xml`. Le paramètre Symfony `_format`, lorsqu'il est explicitement défini, prime sur les préférences du client. Une réponse neutre varie selon `Accept` et interdit sa mise en cache partagée.

Les en-têtes de protocole `Allow`, `Retry-After`, `WWW-Authenticate` restent conservés lorsque les exceptions HTTP les fournissent. Les en-têtes déclarables par le mapper sont **limités** ; `Set-Cookie`, `X-Debug-Token`, `Location` et les en-têtes arbitraires ne sont pas autorisés. Les en-têtes CORS explicitement déclarés sont contrôlés.

## Précautions de migration

1. Tester sur une instance locale de développement, **jamais en premier sur la production**.
2. Établir la baseline des contrats d'erreur avant/après, statuts, schémas, en-têtes, clients et effets métier.
3. Déclarer les mappers **uniquement** pour les erreurs métier destinées à être exposées.
4. Ne jamais contourner le contrôle Guardian en diminuant sa priorité ni activer le profiler en production.
5. Rejouer la CI sur les versions Symfony supportées.

Le système de compte, le coffre chiffré, la MFA, les cookies de session et les règles d'accès ne sont pas modifiés par cette fonctionnalité.

## Tests

`tests/safe_native_integration.php` utilise le noyau Symfony réel et un mapper d'erreur applicatif explicitement tagué. Vérifie le statut, la neutralisation des exceptions non approuvées, les erreurs des mappers, la protection des headers, les formats et les trois modes HTML.

La CI habituelle `Bundle validation` vérifie PHP 8.3/8.4 et Symfony 5.4, 6.4, 7.4, 8.1. La suite indépendante `PsiGuardian/profiler-shield-real-test` sert de référence et conserve des rapports distincts pour le bundle publié 1.0.0 et les prototypes.

**Limite :** aucune garantie globale n'est possible concernant les erreurs générées par le serveur web, PHP-FPM avant Symfony, les proxys, ou un code métier qui divulgue directement un secret dans une réponse déjà construite.
