# Architecture and trust boundaries

[Documentation contents](README.md)


## Architecture and trust boundaries

The proxy handles TLS, normalization and volumetric protection. The gateway checks diagnostics before routing; business routes do not read accounts or sessions. Guardian identity is separate from Symfony accounts. Back up the private vault and its key together, outside the public directory. Compromised PHP or host access can expose both. Storage is single-host. PHP boot, cache or proxy failures can affect the whole application.

The IP allowlist accepts IPv4, IPv6 and CIDR networks. Check Symfony trusted proxy configuration before enabling it. Use HTTPS for secure cookies. Guardian does not repair exposed web servers or databases.

Guardian controls the profiler and debug toolbar. An authorized administrator or developer account is required. MFA, schedules, JIT, network restrictions and lockdown may still deny access. Stealth mode hides entry points; it does not replace these checks.

| PHP / HTTP | Architecture and trust boundaries |
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

## Security invariants

Deny diagnostics if identity, role, session, network, schedule, JIT or lockdown cannot be checked. The browser does not decide expiry. Viewing profiles and collecting them are distinct. Remembering devices does not authenticate. Emergency access requires identity, a consumed code and durable audit; critical audit failure prevents opening. Invalid configuration must not be partially applied. Optional controls require no remote service.

Back up vault and key together and test restoration. Lost keys are never regenerated automatically. After removal, check the profiler only in a private environment. Production requires APP_DEBUG=0 with WebProfilerBundle and collection disabled. A local audit chain does not prove integrity on a compromised host.

## Journal and alerts

Level and retention determine available events, up to 5000 entries. Filter by type and account before changing pages. Alerts are queued; configure their transport and delivery. A local hash chain does not guarantee integrity on a compromised host.
