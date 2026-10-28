# Beállítások

[Dokumentáció tartalma](README.md)


## További védelmek

A védelmek a beállításaikkal együtt szerepelnek. A kikapcsolt opciók szürkék, értékeik megmaradnak.

Guardian ellenőrzi a profilokat és a debug eszköztárat. engedélyezett adminisztrátor vagy fejlesztő fiók szükséges. MFAAz ütemterv, JIT, hálózati korlátozások és zárolás még mindig megtagadhatja a hozzáférést. Stealth mód elrejti a belépési pontokat; nem helyettesíti ezeket a ellenőrzéseket.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Profil | Beállítások |
|---|---|
| • Rugalmas | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| Csapat · fokozott | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| érzékeny · korlátozott | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Paraméter | Beállítások | Kezdeti érték |
|---|---|---|
| `stealth` | Jogosulatlan diagnosztikai útvonalakon 403 helyett 404-et adjon vissza. | `true` |
| `mfa` | Minden felhasználónak TOTP-t kell beállítania a kötelezővé tétel előtt. | `false` |
| `idle_minutes` | Aktivitás nélküli percek a lejáratig. | `60` |
| `absolute_hours` | Óra, aktivitástól függetlenül. | `8` |
| `remember_days` | Nap. Nem helyettesíti a belépést vagy a munkaidő-szabályt. | `14` |
| `schedule_enabled` | Válassza ki a diagnosztika elérhetőségi idejét. Az alkalmazás oldalai elérhetők maradnak. | `false` |
| `weekdays` | Válassza ki a diagnosztikára engedélyezett napokat. | `1,2,3,4,5` |
| `start` | Nyitás | `09:00` |
| `end` | Zárás | `18:00` |
| `timezone` | A jelen oldalt kiszolgáló PHP-folyamatból észlelve. A PHP CLI, PHP-FPM és az operációs rendszer időzónája eltérhet. Mentés előtt ellenőrizze a kiszolgáló beállításait. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | A privát URL munkaidőn kívül 404-et ad vissza. Helyreállítás csak helyi CLI-vel. | `false` |
| `jit_enabled` | A profilert csak kért diagnosztikai időablakban nyissa meg. | `false` |
| `jit_minutes` | Perc, csak engedélyezett állapotban érvényes. | `30` |
| `ip_allowlist` | Figyelem: hibás beállítás kizárhatja Önt. Helyi helyreállítás: guardian:policy:reset. | `∅` |
| `audit_level` | Válassza ki a napló részletességét és helyi megőrzését. | `standard` |
| `audit_retention_days` | Napok a napló törléséig. | `30` |
| `alert_email` | Vészhelyzeti riasztásokat fogad. A hozzáférés nem függhet e-mail-kézbesítéstől. | `∅` |
| `emergency_enabled` | 30 perces kivétel engedélyezése egyszer használatos kóddal és naplózással. | `false` |
| `login_rules` | Az alapértelmezett: 10 hibák, majd a 10A számlák mind a fiókra, mind a hálózati eredetre vonatkoznak, beleértve a jelszavak megerősítését is. | `10 / 10 / 30 / 30` |
| `password_policy` | Ezek a szabályok vonatkoznak a jelszó létrehozására és módosítására, az interneten és a CLINem mutatnak fel vagy ellenőriznek meglévő jelszavakat. | `min_length=15` |
| `error_mode` | Felhasználási oldalak: megőrzi a webhely létező 404 válaszok; kivételek használják a 404 Template, ha rendelkezésre áll, egyébként egy semleges oldal. semleges: szabványos üzenet. személyre szabott: használja a szöveget. | `native` |
| `error_reference` | A hivatkozás a Guardian’s semleges oldalak: véletlenszerűen generálja egy új kódot, nem véletlen üzenet. | `none` |
| `error_reference_text` | Csak egy személyre szabott statikus referenciával érhető el. | `∅` |
| `error_custom_text` | Csak egyéni semleges üzenetnél érhető el. A szöveg módváltáskor megmarad. | `∅ → custom` |

A tervezés, a kiválasztott napok, az inkluzív indítási idő és az exkluzív végidő lehetővé tétele. Az mentett időzónák meghatározják a helyi időket. Éjszakai intervallumokat nem fogadnak el. A portál elrejtése órán kívül megakadályozza a távoli vészhelyzeti hozzáférést; tartsa rendelkezésre a helyi helyreállítási parancsokat.

Idle timeout, az abszolút élettartam és a készülék emlékezete különböző célokra szolgál. JITA visszavonás befejezi a megfelelő üléseket a kiszolgálón. Az egyes fiókok használata az üzenetben szereplő tevékenységek odaítéléséhez.

JIT engedélyezett ülésből egy ideiglenes ablak aktiválására van szükség. A sürgősségi hozzáférés, amikor engedélyezve van, jelszót, egyetlen felhasználási kódot és okot igényel. Nem halad át a globális zároláson. Helyi parancsok továbbra is szükségesek, ha a portál elérhetetlen.

Az IP engedélyezési listája elfogadja IPv4, IPv6 és CIDR Hálózatok - Ellenőrizze Symfony megbízható proxy konfiguráció, mielőtt lehetővé teszi. HTTPS Biztonságos cookie-k használata. Guardian nem javítja a kiszolgáltatott webszervereket vagy adatbázisokat.

A szint és a tartás határozza meg a rendelkezésre álló eseményeket, 5000 bejegyzések. típus és számla szerint szűrő, mielőtt oldalakat változtatna. figyelmeztetések megjelennek; konfigurálják szállításukat és szállítást. A helyi hash lánc nem garantálja a kompromisszumot jelentő host integritását.

Állítsa be a minimális hosszúságot és a szükséges kategóriákat a Kapcsolatok és jelszavak. A fiók létrehozásának jelzései leírják a mentett politikát. A változások az új jelszóra vonatkoznak; válassza ki a meglévő fiókok kötelező frissítését. Guardian Választás Argon2id Mikor PHP megadja, egyébként bcrypt.

Az összes megőrzött eseményt típus és számla szerint szűrje.A legújabb események először megjelenik a 50A Scrolling nem tölti fel a következő oldalt.

## Környezet


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


A proxy kezeli a TLS-t, normalizálást és nagy forgalom elleni védelmet. Az átjáró a router előtt ellenőriz; üzleti útvonalak nem olvasnak fiókokat vagy munkameneteket. A Guardian-azonosság független a Symfonyétól. A privát tárolót és kulcsot együtt, nyilvános könyvtáron kívül mentse. Feltört PHP vagy gép mindkettőt felfedheti. A tárolás egy gépre készült. Indítási, gyorsítótár- vagy proxyhiba az egész alkalmazást érintheti.

Tárolót és kulcsot együtt mentse, próbálja a visszaállítást. Elveszett kulcs nem generálódik újra automatikusan. Eltávolítás után a profilert csak privát környezetben ellenőrizze. Éles környezet: APP_DEBUG=0, WebProfilerBundle és gyűjtés kikapcsolva. Helyi auditlánc nem bizonyít sértetlenséget feltört gépen.

## helyreállítás és ellenőrzés

Üríti az IP-listát és kikapcsolja az ütemezést, JIT-t és távoli vészhozzáférést. Hitelesítés, MFA, globális zárolás és belépési tiltások megmaradnak. A szükséges korlátozásokat újra állítsa be a panelen.

on lezárja a diagnosztikát és törli az összes munkamenetet; az üzleti oldalak működnek. off csak a globális zárolást oldja fel: új bejelentkezés kell, a többi szabály továbbra is él.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
