# Architektúra a hranice dôvery

[Obsah dokumentácie](README.md)


## Architektúra a hranice dôvery

Proxy zabezpečuje TLS, normalizáciu a ochranu pred objemovou prevádzkou. Brána kontroluje diagnostiku pred routerom; bežné trasy nečítajú účty ani relácie. Identita Guardian je oddelená od Symfony. Súkromné úložisko a kľúč zálohujte spolu mimo verejného adresára. Napadnuté PHP alebo hostiteľ môže odhaliť obe. Úložisko je pre jedného hostiteľa. Chyby štartu, cache alebo proxy môžu ovplyvniť celú aplikáciu.

IP povolený zoznam prijíma IPv4, IPv6 a CIDR sieť - kontroly Symfony spoľahlivá konfigurácia proxy predtým, než ju umožní. HTTPS Pre bezpečné cookies. Guardian Nepreparuje vystavené webové servery alebo databázy.

Guardian ovláda profil a panel nástrojov na odstraňovanie. je potrebný autorizovaný správca alebo účet vývojára. MFAa harmonogramy , JIT, sieťové obmedzenia a zablokovanie môžu stále odmietnuť prístup. režim únosu skrýva vstupné body; nenahradí tieto kontroly.

| PHP / HTTP | Architektúra a hranice dôvery |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| Zasadnutie | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Bezpečnostné invarianty

Odmietnite diagnostiku, ak nemožno overiť identitu, rolu, reláciu, sieť, rozvrh, JIT alebo uzamknutie. Prehliadač neurčuje expiráciu. Zber a prehliadanie sú oddelené. Pamätanie zariadenia neoveruje identitu. Núdzový prístup vyžaduje identitu, spotrebovaný kód a trvalý audit; kritická chyba auditu bráni otvoreniu. Neplatné nastavenie neaplikujte čiastočne. Voliteľné kontroly nevyžadujú vzdialenú službu.

Zálohujte úložisko a kľúč spolu a vyskúšajte obnovu. Stratené kľúče sa automaticky neobnovujú. Po odstránení kontrolujte profiler len neverejne. Produkcia: APP_DEBUG=0, WebProfilerBundle a zber vypnuté. Miestny reťazec auditu nedokazuje integritu napadnutého hostiteľa.

## Noviny a upozornenia

Úroveň a údržba určuje dostupné udalosti, až do 5000 vstupy. filtrovať podľa typu a účtu pred zmenou stránok. upozornenia sú zrušené; nastaviť ich prepravu a doručenie. Lokálny hash reťazec nezaručuje integritu na kompromitovanom hostiteľa.
