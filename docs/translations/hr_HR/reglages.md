# Postavke

[Sadržaj dokumentacije](README.md)


## Dodatne zaštite

Kontrole su grupirane sa svojim postavkama. Isključene opcije su zasivljene, a vrijednosti sačuvane.

Guardian kontrolira profil i debug toolbar. Potrebna je ovlaštena administratora ili račun razvijatelja. MFAna rasporedu, JIT, mrežne ograničenja i zatvaranje još uvijek mogu odbiti pristup. način štednje krije ulazne točke; ne zamjenjuje ove provjere.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Profil | Postavke |
|---|---|
| Jednostavno · fleksibilan | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| Ekipa · poboljšani | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| Osjetljivi · ograničeni | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Parametar | Postavke | Početna vrijednost |
|---|---|---|
| `stealth` | Vrati 404 umjesto 403 na neovlaštenim dijagnostičkim rutama. | `true` |
| `mfa` | Svi korisnici moraju postaviti TOTP prije obvezne aktivacije. | `false` |
| `idle_minutes` | Minute bez aktivnosti do isteka sesije. | `60` |
| `absolute_hours` | Sati, bez obzira na aktivnost. | `8` |
| `remember_days` | Dani. Ne zamjenjuje prijavu ni pravila radnog vremena. | `14` |
| `schedule_enabled` | Odaberite kada su dijagnostički alati dostupni. Stranice aplikacije ostaju dostupne. | `false` |
| `weekdays` | Odaberite dane kada je dijagnostika dopuštena. | `1,2,3,4,5` |
| `start` | Otvaranje | `09:00` |
| `end` | Zatvaranje | `18:00` |
| `timezone` | Otkriveno iz PHP procesa koji poslužuje ovu stranicu. PHP CLI, PHP-FPM i operacijski sustav mogu rabiti različite vremenske zone. Provjerite konfiguraciju prije spremanja. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | Privatni URL vraća 404 izvan radnog vremena. Oporavak samo lokalnim CLI-jem. | `false` |
| `jit_enabled` | Otvaraj profiler samo tijekom zatraženog dijagnostičkog prozora. | `false` |
| `jit_minutes` | Minute, koriste se samo kada je uključeno. | `30` |
| `ip_allowlist` | Pozor: pogreška može blokirati pristup. Lokalni oporavak: guardian:policy:reset. | `∅` |
| `audit_level` | Odaberite razinu detalja i lokalno zadržavanje zapisnika. | `standard` |
| `audit_retention_days` | Dani do čišćenja zapisnika. | `30` |
| `alert_email` | Prima hitne obavijesti. Pristup ne smije ovisiti o dostavi e-pošte. | `∅` |
| `emergency_enabled` | Dopusti iznimku od 30 minuta s jednokratnim kodom i zapisom revizije. | `false` |
| `login_rules` | Podrazumevano : 10 Neuspjeh, a zatim i 10-minute lockout. računi se primjenjuju na račun i izvor mreže, uključujući potvrde lozinke. | `10 / 10 / 30 / 30` |
| `password_policy` | Ova se pravila primjenjuju na stvaranje i promjene lozinke, na web-u i u CLINe otkrivaju ili provjeravaju postojeće lozinke. | `min_length=15` |
| `error_mode` | Stranice za aplikacije: sačuvaju postojeće stranice 404 Odgovori; iznimke koriste 404 Šablon kada je na raspolaganju, inače neutralna stranica. Neutral: standardni poruka. Prilagođeni: koristi svoj tekst. | `native` |
| `error_reference` | Referencija se primjenjuje na Guardian's neutralne stranice: slučajno generira novi kod, a ne slučajnu poruku. | `none` |
| `error_reference_text` | Dostupna je samo sa standardnim statskim referencijama. | `∅` |
| `error_custom_text` | Dostupno samo za prilagođenu neutralnu poruku. Tekst ostaje spremljen pri promjeni načina. | `∅ → custom` |

Omogućite raspored, odabrane dane, uključivo početak i ekskluzivno vrijeme završetka. sačuvana vremenska zona određuje lokalna vremena. noćni intervalovi nisu prihvaćeni. sakrivanje portala izvan sati sprječava daljinski hitni pristup; zadržite lokalne zapovijedi za oporavak dostupne.

Idle timeout, apsolutni život i sjećanje na uređaj služe različitim svrhama. JITOdbijanje završava relevantne sjednice na serveru. koristite pojedinačne račune kako biste dodijelili akcije u dnevniku.

JIT zahtijeva aktiviranje privremenog prozora od ovlaštenog sjednice. pristup za hitne slučajeve, kada je uključen, zahtijevaju lozinku, kod za jednokratnu upotrebu i razlog. To ne prelazi globalno zatvaranje. Lokalne zapovijedi ostaju potrebni ako portal nije dostupan.

IP dopuštenje prihvata IPv4, IPv6 i CIDR Mreža – provjerite Symfony povjereno proxy konfiguracija prije omogućavanja. HTTPS Za sigurne kolačiće. Guardian ne popravlja izložene web servere ili baze podataka.

Razina i zadržavanje određuju dostupne događaje, do 5000 Ulaznice. filtrirajte po vrsti i računu prije promjene stranica. upozorenja se uklanjaju; konfiguracija njihovog prijevoza i isporuke. lokalni hash lanac ne jamči integritet na kompromitiranom domaćinu.

Postavite minimalnu duljinu i potrebne kategorije u poveznicama i lozinkama. oznake za stvaranje računa opisuju sačuvanu politiku. promjene se primjenjuju na nove loze; odaberite obavezno obnavljanje postojećih računa. Guardian Izbori Argon2id Kada PHP Neka se, inače bcrypt.

Filtrirajte sve sačuvane događaje po vrsti i računu. najnovije događanja prvo se pojavljuju na stranicama 50Scrolling ne preuzima sljedeću stranicu.

## Okruženje


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


Proxy osigurava TLS, normalizaciju i zaštitu od velikog prometa. Pristupnik provjerava dijagnostiku prije routera; poslovne rute ne čitaju račune ili sesije. Guardian identitet odvojen je od Symfonyja. Privatni trezor i ključ zajedno sigurnosno kopirajte izvan javnog direktorija. Kompromitirani PHP ili host mogu otkriti oboje. Pohrana je za jedan host. Pogreške pokretanja, predmemorije ili proxyja mogu utjecati na cijelu aplikaciju.

Zajedno kopirajte trezor i ključ te testirajte obnovu. Izgubljeni ključevi ne stvaraju se automatski. Nakon uklanjanja provjerite profiler samo privatno. Produkcija: APP_DEBUG=0, WebProfilerBundle i prikupljanje isključeni. Lokalni auditni lanac ne dokazuje integritet kompromitiranog hosta.

## Oporavak i provjera

Prazni IP popis i isključuje raspored, JIT i udaljeni hitni pristup. Autentifikacija, MFA, globalna blokada i blokade prijave ostaju. Ponovno postavite željena ograničenja na ploči.

on zatvara dijagnostiku i briše sve sesije; poslovne stranice rade dalje. off uklanja samo globalnu blokadu: prijavite se ponovno, ostala pravila i dalje vrijede.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
