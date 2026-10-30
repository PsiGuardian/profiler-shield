# Architektúra és bizalmi határok

[Dokumentáció tartalma](README.md)


## Architektúra és bizalmi határok

A proxy kezeli a TLS-t, normalizálást és nagy forgalom elleni védelmet. Az átjáró a router előtt ellenőriz; üzleti útvonalak nem olvasnak fiókokat vagy munkameneteket. A Guardian-azonosság független a Symfonyétól. A privát tárolót és kulcsot együtt, nyilvános könyvtáron kívül mentse. Feltört PHP vagy gép mindkettőt felfedheti. A tárolás egy gépre készült. Indítási, gyorsítótár- vagy proxyhiba az egész alkalmazást érintheti.

Az IP engedélyezési listája elfogadja IPv4, IPv6 és CIDR Hálózatok - Ellenőrizze Symfony megbízható proxy konfiguráció, mielőtt lehetővé teszi. HTTPS Biztonságos cookie-k használata. Guardian nem javítja a kiszolgáltatott webszervereket vagy adatbázisokat.

Guardian ellenőrzi a profilokat és a debug eszköztárat. engedélyezett adminisztrátor vagy fejlesztő fiók szükséges. MFAAz ütemterv, JIT, hálózati korlátozások és zárolás még mindig megtagadhatja a hozzáférést. Stealth mód elrejti a belépési pontokat; nem helyettesíti ezeket a ellenőrzéseket.

| PHP / HTTP | Architektúra és bizalmi határok |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| ülés | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Biztonsági alapfeltételek

Tagadja meg a diagnosztikát, ha az azonosság, szerep, munkamenet, hálózat, ütemezés, JIT vagy zár nem ellenőrizhető. A böngésző nem dönt a lejáratról. Gyűjtés és megtekintés különbözik. Eszközfelismerés nem hitelesítés. Vészhozzáféréshez azonosság, felhasznált kód és tartós audit kell; kritikus audithiba tiltja a nyitást. Hibás konfigurációt ne alkalmazzon részlegesen. Opcionális védelem nem igényel távoli szolgáltatást.

Tárolót és kulcsot együtt mentse, próbálja a visszaállítást. Elveszett kulcs nem generálódik újra automatikusan. Eltávolítás után a profilert csak privát környezetben ellenőrizze. Éles környezet: APP_DEBUG=0, WebProfilerBundle és gyűjtés kikapcsolva. Helyi auditlánc nem bizonyít sértetlenséget feltört gépen.

## Újságok és figyelmeztetések

A szint és a tartás határozza meg a rendelkezésre álló eseményeket, 5000 bejegyzések. típus és számla szerint szűrő, mielőtt oldalakat változtatna. figyelmeztetések megjelennek; konfigurálják szállításukat és szállítást. A helyi hash lánc nem garantálja a kompromisszumot jelentő host integritását.
