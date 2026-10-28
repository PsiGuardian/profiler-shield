# Nastavenia

[Obsah dokumentácie](README.md)


## Doplnková ochrana

Ochrany sú zoskupené s nastaveniami. Vypnuté možnosti sú sivé a ich hodnoty zostávajú zachované.

Guardian ovláda profil a panel nástrojov na odstraňovanie. je potrebný autorizovaný správca alebo účet vývojára. MFAa harmonogramy , JIT, sieťové obmedzenia a zablokovanie môžu stále odmietnuť prístup. režim únosu skrýva vstupné body; nenahradí tieto kontroly.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Profil | Nastavenia |
|---|---|
| • Flexibilný | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| • Posilnený tím | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| citlivý · obmedzený | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Parameter | Nastavenia | Predvolená hodnota |
|---|---|---|
| `stealth` | Na nepovolených diagnostických trasách vráťte 404 namiesto 403. | `true` |
| `mfa` | Všetci používatelia musia pred vynútením nastaviť TOTP. | `false` |
| `idle_minutes` | Minúty bez aktivity do vypršania relácie. | `60` |
| `absolute_hours` | Hodiny, nezávisle od aktivity. | `8` |
| `remember_days` | Dni. Nenahrádza prihlásenie ani pracovný rozvrh. | `14` |
| `schedule_enabled` | Určte, kedy sú diagnostické nástroje prístupné. Stránky aplikácie zostávajú dostupné. | `false` |
| `weekdays` | Vyberte dni, keď je diagnostika povolená. | `1,2,3,4,5` |
| `start` | Otvorenie | `09:00` |
| `end` | Zatvorenie | `18:00` |
| `timezone` | Zistené z procesu PHP obsluhujúceho túto stránku. PHP CLI, PHP-FPM a operačný systém môžu mať rôzne časové pásma. Pred uložením skontrolujte konfiguráciu. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | Súkromná URL mimo hodín vracia 404. Obnova len miestnym CLI. | `false` |
| `jit_enabled` | Profiler sprístupniť iba počas požadovaného diagnostického okna. | `false` |
| `jit_minutes` | Minúty, používajú sa len po zapnutí. | `30` |
| `ip_allowlist` | Pozor: chyba môže zablokovať prístup. Miestna obnova: guardian:policy:reset. | `∅` |
| `audit_level` | Vyberte detail auditu a dobu miestneho uchovania. | `standard` |
| `audit_retention_days` | Dni pred vyčistením denníka. | `30` |
| `alert_email` | Prijíma núdzové upozornenia. Prístup nesmie závisieť od doručenia e-mailu. | `∅` |
| `emergency_enabled` | Povoliť 30-minútovú výnimku s jednorazovým kódom a auditom. | `false` |
| `login_rules` | V predvolenom: 10 zlyhania, potom a 10-minute lockout. počítania sa vzťahujú na účet a sieťový pôvod, vrátane potvrdenia hesla. | `10 / 10 / 30 / 30` |
| `password_policy` | Tieto pravidlá sa vzťahujú na vytvorenie a zmeny hesla, na webe a v CLINeodhaľujú ani neinspektujú existujúce heslá. | `min_length=15` |
| `error_mode` | Stránky aplikácie: zachováva existujúce stránky 404 Odpovede; výnimky sa používajú 404 šablóna, keď je k dispozícii, inak neutrálna stránka. Neutrálne: štandardné správy. prispôsobené: používa text. | `native` |
| `error_reference` | Odkaz sa vzťahuje na Guardian’s neutrálne stránky: náhodné generuje nový kód, nie náhodnú správu. | `none` |
| `error_reference_text` | K dispozícii iba so špecifickým statickým odkazom. | `∅` |
| `error_custom_text` | Dostupné iba pre vlastnú neutrálnu správu. Text zostane uložený pri zmene režimu. | `∅ → custom` |

Umožňuje naplánovanie, vybrané dni, inkluzívny čas začiatku a exkluzívne čas končiaceho času. uložená časová zóna určuje miestne hodiny. nočné intervaly nie sú akceptované. Skrytie portálu mimo hodín zabraňuje vzdialenému núdzovému prístupu; udržať lokálne príkazy na obnovu k dispozícii.

Idle timeout, absolútna životnosť a pamäť zariadenia slúžia rôznym účelom. Pamätať si zariadenie neprekračuje harmonogramy alebo JITOdvolanie ukončuje príslušné relácie na serveri. Použite jednotlivé účty, aby ste pridali akcie v časopise.

JIT vyžaduje aktiváciu dočasného okna z oprávneného zasadnutia. núdzový prístup, keď je povolený, si vyžiada heslo, kód jednorazového použitia a dôvod. Nezabúda na globálne zablokovanie. Miestne príkazy zostávajú potrebné, ak je portál nedostupný.

IP povolený zoznam prijíma IPv4, IPv6 a CIDR sieť - kontroly Symfony spoľahlivá konfigurácia proxy predtým, než ju umožní. HTTPS Pre bezpečné cookies. Guardian Nepreparuje vystavené webové servery alebo databázy.

Úroveň a údržba určuje dostupné udalosti, až do 5000 vstupy. filtrovať podľa typu a účtu pred zmenou stránok. upozornenia sú zrušené; nastaviť ich prepravu a doručenie. Lokálny hash reťazec nezaručuje integritu na kompromitovanom hostiteľa.

Nastaviť minimálnu dĺžku a požadované kategórie v pripojeniach a heslá. Zoznamy vytvorenia účtu popisujú uloženú politiku. Zmeny sa vzťahujú na nové heslá; vyberte povinné obnovenie pre existujúce účty. Guardian Výbery Argon2id Kedy PHP poskytuje ju, inak bcrypt.

Filtrovať všetky zachované udalosti podľa typu a účtu.Najnovšie udalosti sa najprv zobrazia na stránkach 50Scrolling nezahŕňa ďalšiu stránku.

## Prostredie


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


Proxy zabezpečuje TLS, normalizáciu a ochranu pred objemovou prevádzkou. Brána kontroluje diagnostiku pred routerom; bežné trasy nečítajú účty ani relácie. Identita Guardian je oddelená od Symfony. Súkromné úložisko a kľúč zálohujte spolu mimo verejného adresára. Napadnuté PHP alebo hostiteľ môže odhaliť obe. Úložisko je pre jedného hostiteľa. Chyby štartu, cache alebo proxy môžu ovplyvniť celú aplikáciu.

Zálohujte úložisko a kľúč spolu a vyskúšajte obnovu. Stratené kľúče sa automaticky neobnovujú. Po odstránení kontrolujte profiler len neverejne. Produkcia: APP_DEBUG=0, WebProfilerBundle a zber vypnuté. Miestny reťazec auditu nedokazuje integritu napadnutého hostiteľa.

## Recyklácia a overovanie

Vyprázdni zoznam IP a vypne plán, JIT a vzdialenú núdzu. Prihlásenie, MFA, globálne uzamknutie a blokovanie prihlásenia zostávajú. Potrebné obmedzenia znova nastavte v paneli.

on zavrie diagnostiku a odstráni všetky relácie; bežné stránky fungujú ďalej. off ruší iba globálne uzamknutie: treba nové prihlásenie a ostatné pravidlá zostávajú.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
