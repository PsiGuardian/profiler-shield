# Parancsreferencia

[Dokumentáció tartalma](README.md)

A Symfony gyökérkönyvtárában, megbízható terminálban futtassa. A példák dev környezetet használnak. Jelszót, kulcsot és helyreállító kódot ne írjon argumentumba, naplóba vagy Gitbe.

## Parancsok és súgó


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev és --no-interaction Symfony-kapcsolók. --no-interaction nem ad meg titkokat; interaktív jelszóparancsnál ne használja. --help a szintaxist, -v részleteket mutat. A 0 kód a jelzett eredményt igazolja; --no-admin működő portál nélkül is sikerülhet.

## `vendor/bin/guardian`

Bekapcsolja a bundle-t dev/test alatt, törli a gyorsítótárat, futtatja a guardian:setup --automatic parancsot, újra töröl, majd futtatja a guardian:doctor és guardian:access parancsokat. Az első hibánál megáll. Az ismétlés megőrzi a fiókokat és kulcsokat. APP_ENV=prod esetén elutasít; proc_open szükséges.

Argumentum nélkül, --help, -h vagy help esetén csak súgót mutat. Az egyetlen művelet a setup, további kapcsolók nélkül.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Parancsreferencia |
|---|---|
| [`guardian:setup`](#guardiansetup) | Előkészíti a privát útvonalat, kulcsot, tárolót, Symfony-fájlokat és szükség esetén adminisztrátort. |
| [`guardian:install`](#guardianinstall) | Inicializálja a tárolót és adminisztrátort hoz létre --username névvel, alapértéke admin. |
| [`guardian:access`](#guardianaccess) | Kiírja a panel útvonalát és a kezdeti belépési adatok helyét, a jelszót soha. |
| [`guardian:doctor`](#guardiandoctor) | Ellenőrzi a tárolót, jogosultságokat, kulcsot, útvonalat, aktív adminisztrátort és beállításokat. |
| [`guardian:password:reset`](#guardianpasswordreset) | Megváltoztatja a létező username fiók jelszavát rejtett bevitellel az aktív szabályzat szerint, visszavon minden munkamenetet és naplózza a műveletet. |
| [`guardian:mfa:reset`](#guardianmfareset) | Törli username TOTP-titkát és számlálóját, visszavonja munkameneteit és naplóz. |
| [`guardian:login:unlock`](#guardianloginunlock) | Nullázza username számlálóit. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Üríti az IP-listát és kikapcsolja az ütemezést, JIT-t és távoli vészhozzáférést. |
| [`guardian:lockdown`](#guardianlockdown) | on lezárja a diagnosztikát és törli az összes munkamenetet; az üzleti oldalak működnek. |
| [`guardian:emergency`](#guardianemergency) | 5 egyszer használatos kódot generál létező username számára, lecserélve a korábbiakat. |
| [`guardian:alerts:flush`](#guardianalertsflush) | PHP mail() útján elküldi a várakozó riasztásokat az alert_email címre. |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Szintetikus titokkal méri a hashellenőrzést, valódi jelszó nélkül. |
| [`guardian:uninstall`](#guardianuninstall) | Előkészíti a Guardianhoz rendelhető generált fájlok eltávolítását. |

## `guardian:setup`

Előkészíti a privát útvonalat, kulcsot, tárolót, Symfony-fájlokat és szükség esetén adminisztrátort. Megőrzi a fiókokat. Éles környezetben elutasítja a beállítást. Közvetlen futtatás után törölje a gyorsítótárat és ellenőrizzen.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic admin fiókot és titkot hoz létre privát 0600 fájlban. --path 25–181 karakteres útvonalat választ, meglévőt nem cserél. --no-admin kihagyja az adminisztrátor létrehozását; aktív fiók nélkül a portál használhatatlan. --username az interaktív nevet javasolja, alapértéke admin; az automatikus fiókot nem nevezi át.

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

Inicializálja a tárolót és adminisztrátort hoz létre --username névvel, alapértéke admin. Rejtett jelszóbevitel az aktív szabályzat szerint. Meglévő fiókot elutasít. Útvonalat és Symfony-konfigurációt nem hoz létre; általában a teljes segédet használja.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Felhasználói név : 3 hogy 64 Az alphanumerikus karakterek.

## `guardian:access`

Kiírja a panel útvonalát és a kezdeti belépési adatok helyét, a jelszót soha. --base-url a valódi HTTPS-eredetet fogadja opcionális porttal, hitelesítő adatok, lekérdezés és töredék nélkül. Cserélje ki a példa gazdagépet.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Ellenőrzi a tárolót, jogosultságokat, kulcsot, útvonalat, aktív adminisztrátort és beállításokat. Hiba esetén 1 a kilépési kód. PHP CLI és PHP-FPM időzónája eltérhet. Nem teszteli az internetes elérhetőséget és proxy-szabályokat.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Megváltoztatja a létező username fiók jelszavát rejtett bevitellel az aktív szabályzat szerint, visszavon minden munkamenetet és naplózza a műveletet. Jelszót ne adjon argumentumként.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Törli username TOTP-titkát és számlálóját, visszavonja munkameneteit és naplóz. Regisztráljon új hitelesítőt; a globális MFA-követelmény továbbra is blokkolhatja a profilert.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Nullázza username számlálóit. --ip CÍM az adott IPv4/IPv6-forrásét is törli, különben a hálózati tiltás megmaradhat. --all minden számlálót töröl. Fiók, jelszó és munkamenet változatlan; a művelet naplózott.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Üríti az IP-listát és kikapcsolja az ütemezést, JIT-t és távoli vészhozzáférést. Hitelesítés, MFA, globális zárolás és belépési tiltások megmaradnak. A szükséges korlátozásokat újra állítsa be a panelen.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on lezárja a diagnosztikát és törli az összes munkamenetet; az üzleti oldalak működnek. off csak a globális zárolást oldja fel: új bejelentkezés kell, a többi szabály továbbra is él.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

5 egyszer használatos kódot generál létező username számára, lecserélve a korábbiakat. Egyszer láthatók; szerveren kívüli titokkezelőben tárolja. Távoli használathoz bekapcsolt vészhozzáférés, hitelesített munkamenet, jelszó és indok szükséges. Globális zárolást nem kerül meg.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

PHP mail() útján elküldi a várakozó riasztásokat az alert_email címre. Állítsa be a levélküldést és ütemezett feladatot, ellenőrizze a kézbesítést. Hibás küldések sorban maradnak. Hibás címzettnél vagy küldési hibánál kód 1.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Szintetikus titokkal méri a hashellenőrzést, valódi jelszó nélkül. --iterations: 1–10, alapérték 3. JSON algoritmussal, idővel és másodpercenkénti ellenőrzésekkel. Egy helyi CPU-folyamatot mér, nem GPU-t vagy támadót.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Előkészíti a Guardianhoz rendelhető generált fájlok eltávolítását. Megőrzi a fiókokat, beállításokat, naplókat és kulcsot. Állítsa le a munkafolyamatokat, futtassa composer remove előtt, törölje a dev gyorsítótárát, majd indítsa újra. Az egyéni és bizonytalan eredetű fájlokat vizsgálja meg.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge adattörlést kér, alapértelmezett elutasítással. --force csak --purge mellett érvényes, és megerősíti a törlést; --no-interaction esetén kötelező. Külső tárolók, hivatkozások és ismeretlen fájlok megakadályozhatják a teljes törlést. Őrizze meg a szükséges kulcsokat és ellenőrizze az eredményt.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Eltávolítás és adatok


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Tárolót és kulcsot együtt mentse, próbálja a visszaállítást. Elveszett kulcs nem generálódik újra automatikusan. Eltávolítás után a profilert csak privát környezetben ellenőrizze. Éles környezet: APP_DEBUG=0, WebProfilerBundle és gyűjtés kikapcsolva. Helyi auditlánc nem bizonyít sértetlenséget feltört gépen.

[Eltávolítás és adatok](desinstallation.md)
