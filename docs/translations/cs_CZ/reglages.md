# Nastavení

[Obsah dokumentace](README.md)


## Doplňková ochrana

Ochrany jsou seskupeny s nastavením. Vypnuté možnosti jsou šedé a jejich hodnoty zůstávají zachovány.

Guardian ovládá profil a dešifrovací řádek nástrojů. je zapotřebí autorizovaného správce nebo účet vývojáře. MFAa harmonogramy , JIT, omezení sítě a blokování může stále odmítnout přístup. režim únosu skrývá vstupní body; nenahradí tyto kontroly.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Profil | Nastavení |
|---|---|
| Flexibilní - flexibilní | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| • Posilování týmu | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| citlivé · omezené | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Parametr | Nastavení | Výchozí hodnota |
|---|---|---|
| `stealth` | Na nepovolených diagnostických trasách vracejte 404 místo 403. | `true` |
| `mfa` | Všichni uživatelé musí před vynucením nastavit TOTP. | `false` |
| `idle_minutes` | Minuty bez aktivity do vypršení relace. | `60` |
| `absolute_hours` | Hodiny, nezávisle na aktivitě. | `8` |
| `remember_days` | Dny. Nenahrazuje přihlášení ani pravidla pracovní doby. | `14` |
| `schedule_enabled` | Určete, kdy jsou diagnostické nástroje přístupné. Stránky aplikace zůstávají dostupné. | `false` |
| `weekdays` | Vyberte dny, kdy je diagnostika povolena. | `1,2,3,4,5` |
| `start` | Otevření | `09:00` |
| `end` | Uzavření | `18:00` |
| `timezone` | Zjištěno z procesu PHP obsluhujícího tuto stránku. PHP CLI, PHP-FPM a operační systém mohou mít různá časová pásma. Před uložením ověřte konfiguraci. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | Soukromá URL vrací mimo pracovní dobu 404. Obnova pouze místní CLI. | `false` |
| `jit_enabled` | Profiler zpřístupnit jen během požadovaného diagnostického okna. | `false` |
| `jit_minutes` | Minuty, používají se jen po aktivaci. | `30` |
| `ip_allowlist` | Pozor: chyba může zablokovat přístup. Místní obnova: guardian:policy:reset. | `∅` |
| `audit_level` | Vyberte detail auditu a délku místního uchování. | `standard` |
| `audit_retention_days` | Dny do vyčištění protokolu. | `30` |
| `alert_email` | Přijímá nouzová upozornění. Přístup nesmí záviset na doručení e-mailu. | `∅` |
| `emergency_enabled` | Povolit 30minutovou výjimku s jednorázovým kódem a auditem. | `false` |
| `login_rules` | Za předpokladu: 10 Neúspěchy, a pak 10-minute lockout. počty se vztahují jak na účet, tak i na původ sítě, včetně potvrzení hesla. | `10 / 10 / 30 / 30` |
| `password_policy` | Tyto pravidla se vztahují na vytvoření a změny hesla, na webu a v CLINeodhalují ani neprozkoumají stávající hesla. | `min_length=15` |
| `error_mode` | Stránky aplikace: zachovává existující webové stránky 404 Odpovědi; výjimky používají 404 šablona, když je k dispozici, jinak neutrální stránka. Neutral: standardní zpráva. | `native` |
| `error_reference` | Odkaz se vztahuje na Guardian‚s neutrální stránky: náhodně generuje nový kód, ne náhodné zprávy. | `none` |
| `error_reference_text` | K dispozici pouze s přizpůsobeným statickým odkazem. | `∅` |
| `error_custom_text` | Dostupné pouze pro vlastní neutrální zprávu. Text zůstane uložen při změně režimu. | `∅ → custom` |

Umožňuje plánování, vybrané dny, inkluzivní čas zahájení a exkluzivně ukončený čas. Úsporná časová zóna určuje místní časy. Večerní intervaly nejsou akceptovány. Skrytí portálu mimo hodiny zabraňuje vzdálenému nouzovému přístupu; udržuje místní příkazy pro obnovu k dispozici.

Idle timeout, absolutní životnost a paměť zařízení slouží různým účelům. Pamět na zařízení nepřekračuje harmonogramy nebo JITOdvolání ukončuje relevantní sezení na serveru. Použijte jednotlivé účty k přidělení akcí v časopise.

JIT vyžaduje aktivovat dočasné okno z povoleného zasedání. nouzový přístup, když je zapnutý, požaduje heslo, jednorázový kód a důvod. To nepřekračuje globální blokování. místní příkazy zůstávají nezbytné, pokud je portál nedostupný.

IP dovolovací seznam přijímá IPv4, IPv6 a CIDR Síťové sítě - kontroly Symfony důvěryhodná konfigurace proxy předtím, než ji umožní. HTTPS Pro bezpečné cookies. Guardian neupravuje vystavené webové servery nebo databáze.

Úroveň a údržba určují dostupné události, až do 5000 Vstupy. filtrovat podle typu a účtu před změnou stránek. upozornění jsou zkreslené; nastavit jejich přepravu a dodání. Lokální hash řetězec nezaručuje integritu na kompromitovaném hostingu.

Nastavit minimální délku a požadované kategorie v souvislosti a hesla. účet vytvoření značky popisuje uloženou politiku. Změny se vztahují na nové hesla; vyberte povinné obnovení pro stávající účty. Guardian Výběr Argon2id Kdy PHP V opačném případě ji poskytne bcrypt.

Filtrovat všechny zachované události podle typu a účtu. Nejnovější událost se objeví nejprve, na stránkách 50Scrolling nezakládá další stránku.

## Prostředí


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


Proxy zajišťuje TLS, normalizaci a ochranu před objemovým provozem. Brána kontroluje diagnostiku před routerem; běžné trasy nečtou účty ani relace. Identita Guardian je oddělena od Symfony. Soukromé úložiště a klíč zálohujte společně mimo veřejný adresář. Napadené PHP nebo host může odhalit obojí. Úložiště je pro jednoho hostitele. Chyby startu, cache nebo proxy mohou ovlivnit celou aplikaci.

Zálohujte úložiště a klíč společně a vyzkoušejte obnovu. Ztracené klíče se automaticky neobnovují. Po odstranění kontrolujte profiler jen neveřejně. Produkce: APP_DEBUG=0, WebProfilerBundle a sběr vypnuté. Místní řetězec auditu nedokazuje integritu napadeného hostitele.

## Recyklace a ověření

Vyprázdní IP seznam a vypne plán, JIT a vzdálený nouzový přístup. Přihlášení, MFA, globální uzamčení a blokování přihlášení zůstávají. Potřebná omezení znovu nastavte v panelu.

on zavře diagnostiku a odstraní všechny relace; běžné stránky fungují dál. off ruší pouze globální uzamčení: je nutné nové přihlášení a ostatní pravidla platí dál.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
