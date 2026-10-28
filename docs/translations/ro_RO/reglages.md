# Setări

[Cuprinsul documentației](README.md)


## Protecții suplimentare

Controalele sunt grupate cu setările lor. Opțiunile inactive sunt estompate, iar valorile păstrate.

Guardian controlă profilul și barra de instrumente de debug. este necesar un cont autorizat administrator sau dezvoltator. MFAşi programele, JIT, restricțiile de rețea și blocarea pot încă să negeze accesul. modul de furt ascunde punctele de intrare; nu înlocuiește aceste controale.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Profil | Setări |
|---|---|
| Flexibilă • Flexibilă | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| Echipa · îmbunătățită | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| Sensibilă · restricționată | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Parametru | Setări | Valoare inițială |
|---|---|---|
| `stealth` | Returnează 404 în loc de 403 pe rute de diagnostic neautorizate. | `true` |
| `mfa` | Toți utilizatorii trebuie să configureze TOTP înainte de activarea cerinței. | `false` |
| `idle_minutes` | Minute fără activitate înainte de expirare. | `60` |
| `absolute_hours` | Ore, indiferent de activitate. | `8` |
| `remember_days` | Zile. Nu înlocuiește conectarea sau regulile orare. | `14` |
| `schedule_enabled` | Alegeți când pot fi accesate instrumentele de diagnostic. Paginile aplicației rămân disponibile. | `false` |
| `weekdays` | Selectați zilele în care diagnosticarea este permisă. | `1,2,3,4,5` |
| `start` | Deschidere | `09:00` |
| `end` | Închidere | `18:00` |
| `timezone` | Detectat din procesul PHP care servește pagina. PHP CLI, PHP-FPM și sistemul de operare pot folosi fusuri diferite. Verificați configurația serverului înainte de salvare. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | URL-ul privat returnează 404 în afara programului. Recuperare doar prin CLI local. | `false` |
| `jit_enabled` | Deschide profilerul doar într-o fereastră de diagnostic solicitată. | `false` |
| `jit_minutes` | Minute, folosite doar când este activat. | `30` |
| `ip_allowlist` | Atenție: o greșeală poate bloca accesul. Recuperare locală: guardian:policy:reset. | `∅` |
| `audit_level` | Alegeți nivelul de detaliu și durata stocării locale. | `standard` |
| `audit_retention_days` | Zile înainte de curățarea jurnalului. | `30` |
| `alert_email` | Primește alerte de urgență. Accesul nu trebuie să depindă de livrarea e-mailului. | `∅` |
| `emergency_enabled` | Permite o excepție de 30 minute cu cod unic și jurnal de audit. | `false` |
| `login_rules` | În mod default: 10 eșecurile, apoi a 10Conturile se aplică atât contului, cât și originii rețelei, inclusiv confirmările de parolă. | `10 / 10 / 30 / 30` |
| `password_policy` | Aceste reguli se aplică creării și modificărilor de parolă, pe web și în CLINu dezvăluie sau inspectează parolele existente. | `min_length=15` |
| `error_mode` | Pagini de aplicație: păstrează site-ul existent 404 Răspunsuri; excepţiile folosesc 404 șablon atunci când este disponibil, altfel o pagină neutră. neutru: mesaj standard. personalizat: utilizați textul dvs. | `native` |
| `error_reference` | Referința se aplică la GuardianPagini neutre: aleatoriu generează un nou cod, nu un mesaj aleatoriu. | `none` |
| `error_reference_text` | Disponibil numai cu o referință statisă personalizată. | `∅` |
| `error_custom_text` | Disponibil numai cu un mesaj neutru personalizat. Textul se păstrează la schimbarea modului. | `∅ → custom` |

Permiteți planificarea, selectați zile, un timp de pornire inclus și o perioadă exclusivă. zona orală salvată determină vremurile locale. intervalele de seară nu sunt acceptate. ascunderea portalului în afara orelor împiedică accesul la urgență la distanță; menține comenzi locale de recuperare disponibile.

Idle timeout, durata de viață absolută și memoria dispozitivului servesc scopuri diferite. amintirea unui dispozitiv nu depășește programele sau JITRevocarea termină sesiunile relevante pe server. Utilizați conturi individuale pentru a atribui acțiuni în jurnal.

JIT necesită activarea unei fereastră temporare dintr-o sesiune autorizată. Accesul de urgență, atunci când este activat, necesită o parolă, un cod unic și un motiv. Nu depășește blocarea globală. Comandele locale rămân necesare dacă portalul nu este accesibil.

Lista de permisiuni IP acceptă IPv4, IPv6 şi CIDR Rețeaua - Verificați Symfony Configurarea proxy încredere înainte de a permite. HTTPS Pentru cookie-uri sigure. Guardian nu repară serverele web expuse sau bazele de date.

Nivelul și retenția determină evenimentele disponibile, până la 5000 intrări. filtrează prin tip și cont înainte de a schimba pagini. avertismentele sunt curvate; configurează transportul și livrarea acestora. o lanț locală de hash nu garantează integritatea unui gazdă compromis.

Setarea lungimii minime și a categoriilor necesare în conexiuni și parole. marcajele de creare a contului descriu politica salvată. Modificările se aplică la noi parole; selectați reînnoirea obligatorie pentru conturile existente. Guardian alegerile Argon2id Când PHP Îl oferă, altfel bcrypt.

Filtrează toate evenimentele păstrate în funcție de tip și cont. cele mai noi evenimente apar mai întâi, pe paginile 50Scrolling nu încărcă următoarea pagină.

## Mediu


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


Proxy-ul asigură TLS, normalizare și protecție volumetrică. Pasarela verifică diagnosticele înaintea routerului; rutele de afaceri nu citesc conturi sau sesiuni. Identitatea Guardian este separată de Symfony. Salvați împreună seiful și cheia în afara directorului public. Compromiterea PHP sau a gazdei poate expune ambele. Stocarea este pentru o singură gazdă. Erori de pornire, cache sau proxy pot afecta întreaga aplicație.

Salvați seiful și cheia împreună și testați restaurarea. Cheile pierdute nu se regenerează automat. După eliminare verificați profilerul numai privat. Producție: APP_DEBUG=0, WebProfilerBundle și colectarea dezactivate. Un lanț local nu dovedește integritatea unei gazde compromise.

## Recuperarea și verificarea

Golește lista IP și dezactivează programul, JIT și urgența la distanță. Autentificarea, MFA, blocarea globală și blocările de conectare rămân. Reconfigurați restricțiile dorite în panou.

on închide diagnosticele și șterge toate sesiunile; paginile aplicației rămân disponibile. off ridică doar blocarea globală: trebuie o nouă autentificare și se aplică celelalte politici.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
