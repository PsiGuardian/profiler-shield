# Referința comenzilor

[Cuprinsul documentației](README.md)

Rulați din rădăcina Symfony într-un terminal de încredere. Exemplele folosesc dev. Nu puneți parole, chei sau coduri de recuperare în argumente, jurnale sau Git.

## Descoperire și ajutor


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev și --no-interaction sunt opțiuni Symfony. --no-interaction nu furnizează secrete și nu trebuie folosit pentru parole interactive. --help arată sintaxa; -v adaugă detalii. Codul 0 indică rezultatul anunțat; --no-admin poate reuși fără portal utilizabil.

## `vendor/bin/guardian`

Activează bundle-ul în dev/test, golește cache-ul, rulează guardian:setup --automatic, golește din nou, apoi rulează guardian:doctor și guardian:access. Se oprește la prima eroare. Repetarea păstrează conturi și chei. Refuză APP_ENV=prod și necesită proc_open.

Fără argument, --help, -h sau help afișează ajutor fără modificări. setup este singura acțiune și nu acceptă opțiuni suplimentare.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Referința comenzilor |
|---|---|
| [`guardian:setup`](#guardiansetup) | Pregătește calea privată, cheia, seiful, fișierele Symfony și un administrator dacă lipsește. |
| [`guardian:install`](#guardianinstall) | Inițializează seiful și creează un administrator cu --username, implicit admin. |
| [`guardian:access`](#guardianaccess) | Afișează calea panoului și locația datelor inițiale, niciodată parola. |
| [`guardian:doctor`](#guardiandoctor) | Verifică seiful, permisiunile, cheia, ruta, administratorul activ și configurația. |
| [`guardian:password:reset`](#guardianpasswordreset) | Schimbă parola contului username existent prin introducere ascunsă conform politicii, revocă toate sesiunile și înregistrează acțiunea. |
| [`guardian:mfa:reset`](#guardianmfareset) | Șterge secretul și contorul TOTP pentru username, revocă sesiunile și înregistrează acțiunea. |
| [`guardian:login:unlock`](#guardianloginunlock) | Resetează contoarele username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Golește lista IP și dezactivează programul, JIT și urgența la distanță. |
| [`guardian:lockdown`](#guardianlockdown) | on închide diagnosticele și șterge toate sesiunile; paginile aplicației rămân disponibile. |
| [`guardian:emergency`](#guardianemergency) | Generează 5 coduri unice pentru username existent și le înlocuiește pe cele vechi. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Trimite alertele în așteptare la alert_email prin PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Măsoară verificări de hash pe un secret sintetic, fără parolă reală. |
| [`guardian:uninstall`](#guardianuninstall) | Pregătește eliminarea fișierelor generate atribuibile Guardian. |

## `guardian:setup`

Pregătește calea privată, cheia, seiful, fișierele Symfony și un administrator dacă lipsește. Păstrează conturile existente. Refuză producția. După apel direct, goliți cache-ul și executați verificările.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic generează admin și un secret într-un fișier privat 0600. --path alege o cale de 25–181 de caractere fără a înlocui una existentă. --no-admin omite crearea administratorului; fără cont activ portalul este inutilizabil. --username propune numele interactiv, implicit admin; nu schimbă contul automat.

```text
--path: ^/[a-zA-Z0-9/_-]{24,180}$
/_profiler; /_wdt → ∅
```

```bash
php bin/console guardian:setup --username=operator --env=dev
php bin/console cache:clear --env=dev
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## `guardian:install`

Inițializează seiful și creează un administrator cu --username, implicit admin. Cere o parolă ascunsă conform politicii active. Refuză conturile existente. Nu creează rute sau configurația Symfony; folosiți de obicei asistentul complet.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Numele de utilizator: 3 pentru 64 personajele alphanumerice,

## `guardian:access`

Afișează calea panoului și locația datelor inițiale, niciodată parola. --base-url acceptă originea HTTPS reală cu port opțional, fără acreditări, interogare sau fragment. Înlocuiți gazda de exemplu.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Verifică seiful, permisiunile, cheia, ruta, administratorul activ și configurația. Cod 1 dacă găsește probleme. PHP CLI și PHP-FPM pot avea fusuri diferite. Nu testează expunerea la Internet sau regulile proxy.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Schimbă parola contului username existent prin introducere ascunsă conform politicii, revocă toate sesiunile și înregistrează acțiunea. Nu transmiteți parola ca argument.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Șterge secretul și contorul TOTP pentru username, revocă sesiunile și înregistrează acțiunea. Înrolați alt autentificator; cerința MFA globală rămâne și poate bloca profilerul.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Resetează contoarele username. --ip ADRESĂ șterge și contorul acelei origini IPv4/IPv6; altfel blocarea de rețea poate rămâne. --all șterge toate contoarele. Conturile, parolele și sesiunile nu se schimbă; acțiunea se înregistrează.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Golește lista IP și dezactivează programul, JIT și urgența la distanță. Autentificarea, MFA, blocarea globală și blocările de conectare rămân. Reconfigurați restricțiile dorite în panou.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on închide diagnosticele și șterge toate sesiunile; paginile aplicației rămân disponibile. off ridică doar blocarea globală: trebuie o nouă autentificare și se aplică celelalte politici.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Generează 5 coduri unice pentru username existent și le înlocuiește pe cele vechi. Afișate o dată; păstrați-le în afara serverului într-un manager de secrete. Utilizarea la distanță necesită urgență activă, sesiune autentificată, parolă și motiv. Nu ocolește blocarea globală.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Trimite alertele în așteptare la alert_email prin PHP mail(). Configurați transportul și o sarcină programată, apoi verificați livrarea. Eșecurile rămân în coadă. Cod 1 pentru destinatar invalid sau trimitere eșuată.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Măsoară verificări de hash pe un secret sintetic, fără parolă reală. --iterations acceptă 1–10, implicit 3. JSON cu algoritm, timp și verificări pe secundă. Măsoară un proces CPU local, nu GPU sau atacator.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Pregătește eliminarea fișierelor generate atribuibile Guardian. Păstrează conturi, setări, jurnale și cheie. Opriți workerii, executați înainte de composer remove, goliți cache-ul dev și reporniți. Verificați fișierele personalizate sau fără dovada originii.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge cere ștergerea cu confirmare negativă implicită. --force necesită --purge și confirmă distrugerea; obligatoriu cu --no-interaction. Seifurile externe, legăturile și fișierele necunoscute pot împiedica ștergerea completă. Păstrați cheile necesare și verificați rezultatul.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Dezinstalare și date


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Salvați seiful și cheia împreună și testați restaurarea. Cheile pierdute nu se regenerează automat. După eliminare verificați profilerul numai privat. Producție: APP_DEBUG=0, WebProfilerBundle și colectarea dezactivate. Un lanț local nu dovedește integritatea unei gazde compromise.

[Dezinstalare și date](desinstallation.md)
