# Arhitektura i granice povjerenja

[Sadržaj dokumentacije](README.md)


## Arhitektura i granice povjerenja

Proxy osigurava TLS, normalizaciju i zaštitu od velikog prometa. Pristupnik provjerava dijagnostiku prije routera; poslovne rute ne čitaju račune ili sesije. Guardian identitet odvojen je od Symfonyja. Privatni trezor i ključ zajedno sigurnosno kopirajte izvan javnog direktorija. Kompromitirani PHP ili host mogu otkriti oboje. Pohrana je za jedan host. Pogreške pokretanja, predmemorije ili proxyja mogu utjecati na cijelu aplikaciju.

IP dopuštenje prihvata IPv4, IPv6 i CIDR Mreža – provjerite Symfony povjereno proxy konfiguracija prije omogućavanja. HTTPS Za sigurne kolačiće. Guardian ne popravlja izložene web servere ili baze podataka.

Guardian kontrolira profil i debug toolbar. Potrebna je ovlaštena administratora ili račun razvijatelja. MFAna rasporedu, JIT, mrežne ograničenja i zatvaranje još uvijek mogu odbiti pristup. način štednje krije ulazne točke; ne zamjenjuje ove provjere.

| PHP / HTTP | Arhitektura i granice povjerenja |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| Sjednica | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Sigurnosne invarijante

Odbijte dijagnostiku ako se ne mogu provjeriti identitet, uloga, sesija, mreža, raspored, JIT ili blokada. Preglednik ne određuje istek. Pregled i prikupljanje su odvojeni. Prepoznavanje uređaja ne autentificira. Hitnost traži identitet, potrošen kod i trajni audit; kritična pogreška audita sprječava otvaranje. Ne primjenjujte nevaljane postavke djelomično. Neobvezne kontrole ne zahtijevaju udaljenu uslugu.

Zajedno kopirajte trezor i ključ te testirajte obnovu. Izgubljeni ključevi ne stvaraju se automatski. Nakon uklanjanja provjerite profiler samo privatno. Produkcija: APP_DEBUG=0, WebProfilerBundle i prikupljanje isključeni. Lokalni auditni lanac ne dokazuje integritet kompromitiranog hosta.

## Novine i upozorenja

Razina i zadržavanje određuju dostupne događaje, do 5000 Ulaznice. filtrirajte po vrsti i računu prije promjene stranica. upozorenja se uklanjaju; konfiguracija njihovog prijevoza i isporuke. lokalni hash lanac ne jamči integritet na kompromitiranom domaćinu.
