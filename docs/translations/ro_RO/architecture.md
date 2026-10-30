# Arhitectură și limite de încredere

[Cuprinsul documentației](README.md)


## Arhitectură și limite de încredere

Proxy-ul asigură TLS, normalizare și protecție volumetrică. Pasarela verifică diagnosticele înaintea routerului; rutele de afaceri nu citesc conturi sau sesiuni. Identitatea Guardian este separată de Symfony. Salvați împreună seiful și cheia în afara directorului public. Compromiterea PHP sau a gazdei poate expune ambele. Stocarea este pentru o singură gazdă. Erori de pornire, cache sau proxy pot afecta întreaga aplicație.

Lista de permisiuni IP acceptă IPv4, IPv6 şi CIDR Rețeaua - Verificați Symfony Configurarea proxy încredere înainte de a permite. HTTPS Pentru cookie-uri sigure. Guardian nu repară serverele web expuse sau bazele de date.

Guardian controlă profilul și barra de instrumente de debug. este necesar un cont autorizat administrator sau dezvoltator. MFAşi programele, JIT, restricțiile de rețea și blocarea pot încă să negeze accesul. modul de furt ascunde punctele de intrare; nu înlocuiește aceste controale.

| PHP / HTTP | Arhitectură și limite de încredere |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| Sesiune | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Invariante de securitate

Refuzați diagnosticele dacă identitatea, rolul, sesiunea, rețeaua, programul, JIT sau blocarea nu se pot verifica. Browserul nu decide expirarea. Consultarea și colectarea sunt distincte. Recunoașterea dispozitivului nu autentifică. Urgența necesită identitate, cod consumat și audit durabil; eșecul critic de audit împiedică deschiderea. Nu aplicați parțial configurații invalide. Controalele opționale nu impun servicii externe.

Salvați seiful și cheia împreună și testați restaurarea. Cheile pierdute nu se regenerează automat. După eliminare verificați profilerul numai privat. Producție: APP_DEBUG=0, WebProfilerBundle și colectarea dezactivate. Un lanț local nu dovedește integritatea unei gazde compromise.

## Jurnal și alerte

Nivelul și retenția determină evenimentele disponibile, până la 5000 intrări. filtrează prin tip și cont înainte de a schimba pagini. avertismentele sunt curvate; configurează transportul și livrarea acestora. o lanț locală de hash nu garantează integritatea unui gazdă compromis.
