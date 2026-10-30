# Architektura a hranice důvěry

[Obsah dokumentace](README.md)


## Architektura a hranice důvěry

Proxy zajišťuje TLS, normalizaci a ochranu před objemovým provozem. Brána kontroluje diagnostiku před routerem; běžné trasy nečtou účty ani relace. Identita Guardian je oddělena od Symfony. Soukromé úložiště a klíč zálohujte společně mimo veřejný adresář. Napadené PHP nebo host může odhalit obojí. Úložiště je pro jednoho hostitele. Chyby startu, cache nebo proxy mohou ovlivnit celou aplikaci.

IP dovolovací seznam přijímá IPv4, IPv6 a CIDR Síťové sítě - kontroly Symfony důvěryhodná konfigurace proxy předtím, než ji umožní. HTTPS Pro bezpečné cookies. Guardian neupravuje vystavené webové servery nebo databáze.

Guardian ovládá profil a dešifrovací řádek nástrojů. je zapotřebí autorizovaného správce nebo účet vývojáře. MFAa harmonogramy , JIT, omezení sítě a blokování může stále odmítnout přístup. režim únosu skrývá vstupní body; nenahradí tyto kontroly.

| PHP / HTTP | Architektura a hranice důvěry |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| Setkání | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Bezpečnostní invarianty

Odmítněte diagnostiku, pokud nelze ověřit identitu, roli, relaci, síť, rozvrh, JIT nebo uzamčení. Prohlížeč neurčuje expiraci. Sběr a prohlížení jsou oddělené. Pamatování zařízení neověřuje identitu. Nouze vyžaduje identitu, spotřebovaný kód a trvalý audit; kritická chyba auditu brání otevření. Neplatné nastavení neaplikujte částečně. Volitelné kontroly nevyžadují vzdálenou službu.

Zálohujte úložiště a klíč společně a vyzkoušejte obnovu. Ztracené klíče se automaticky neobnovují. Po odstranění kontrolujte profiler jen neveřejně. Produkce: APP_DEBUG=0, WebProfilerBundle a sběr vypnuté. Místní řetězec auditu nedokazuje integritu napadeného hostitele.

## Noviny a varování

Úroveň a údržba určují dostupné události, až do 5000 Vstupy. filtrovat podle typu a účtu před změnou stránek. upozornění jsou zkreslené; nastavit jejich přepravu a dodání. Lokální hash řetězec nezaručuje integritu na kompromitovaném hostingu.
