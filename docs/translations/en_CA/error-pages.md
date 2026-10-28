# Public error pages

[Documentation contents](README.md)


## Public error pages

Keep the application’s own pages, or show neutral HTML to visitors. Actual HTTP status codes remain unchanged.

Native mode lets Symfony render errors. Generic mode ignores custom text. Only custom mode uses the visitor message. The public reference can be absent, random or fixed; it is independent of the message. HTTP status codes remain unchanged.

Application pages: preserves the site’s existing 404 responses; exceptions use its 404 template when available, otherwise a neutral page. Neutral: standard message. Custom: uses your text.

Anonymous 500 exceptions receive a neutral page. Details require an authorized Guardian identity and GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Missing or failing Twig templates fall back to neutral rendering; custom host error controllers need their own integration. JSON responses and successful business pages remain unchanged.

| error_mode | Settings |
|---|---|
| native | Application pages: preserves the site’s existing 404 responses; exceptions use its 404 template when available, otherwise a neutral page. Neutral: standard message. Custom: uses your text. |
| generic | HTML 400–599 → Keep the application’s own pages, or show neutral HTML to visitors. Actual HTTP status codes remain unchanged. |
| custom | Available only with “Custom neutral message”. Your text is retained when switching modes. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Available only with a custom static reference. |
| error_custom_text | Available only with “Custom neutral message”. Your text is retained when switching modes. |

## Displayed reference

Different messages per status or static references can reveal the actual error. HTTP status codes must not be falsified.

The reference applies to Guardian’s neutral pages: random generates a new code, not a random message. It is not a log correlation identifier.

Choices are validated on the server before saving.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Network and deployment

The IP allowlist accepts IPv4, IPv6 and CIDR networks. Check Symfony trusted proxy configuration before enabling it. Use HTTPS for secure cookies. Guardian does not repair exposed web servers or databases.

Deny diagnostics if identity, role, session, network, schedule, JIT or lockdown cannot be checked. The browser does not decide expiry. Viewing profiles and collecting them are distinct. Remembering devices does not authenticate. Emergency access requires identity, a consumed code and durable audit; critical audit failure prevents opening. Invalid configuration must not be partially applied. Optional controls require no remote service.
