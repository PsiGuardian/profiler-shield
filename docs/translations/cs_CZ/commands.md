# Přehled příkazů

[Obsah dokumentace](README.md)

Spouštějte z kořene Symfony v důvěryhodném terminálu. Příklady používají dev. Hesla, klíče a obnovovací kódy nepatří do argumentů, protokolů ani Gitu.

## Vyhledání příkazů a nápověda


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev a --no-interaction jsou volby Symfony. --no-interaction neposkytuje tajné údaje a není vhodné pro interaktivní hesla. --help popisuje syntaxi; -v přidá podrobnosti. Kód 0 potvrzuje hlášený výsledek; --no-admin může uspět bez funkčního portálu.

## `vendor/bin/guardian`

Aktivuje bundle v dev/test, vymaže cache, spustí guardian:setup --automatic, znovu vymaže cache a spustí guardian:doctor a guardian:access. Zastaví při první chybě. Opakování zachová účty a klíče. Odmítá APP_ENV=prod a vyžaduje proc_open.

Bez argumentu, --help, -h nebo help zobrazí pouze nápovědu. setup je jediná akce a nepřijímá další volby.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Přehled příkazů |
|---|---|
| [`guardian:setup`](#guardiansetup) | Připraví soukromou cestu, klíč, úložiště, soubory Symfony a správce, pokud chybí. |
| [`guardian:install`](#guardianinstall) | Inicializuje úložiště a vytvoří správce s --username, výchozí admin. |
| [`guardian:access`](#guardianaccess) | Zobrazí cestu panelu a umístění počátečních údajů, nikdy heslo. |
| [`guardian:doctor`](#guardiandoctor) | Kontroluje úložiště, oprávnění, klíč, trasu, aktivního správce a nastavení. |
| [`guardian:password:reset`](#guardianpasswordreset) | Změní heslo existujícího username skrytým zadáním podle politiky, zruší všechny jeho relace a zaznamená akci. |
| [`guardian:mfa:reset`](#guardianmfareset) | Odstraní tajný klíč a čítač TOTP účtu username, zruší relace a zaznamená akci. |
| [`guardian:login:unlock`](#guardianloginunlock) | Vynuluje čítače username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Vyprázdní IP seznam a vypne plán, JIT a vzdálený nouzový přístup. |
| [`guardian:lockdown`](#guardianlockdown) | on zavře diagnostiku a odstraní všechny relace; běžné stránky fungují dál. |
| [`guardian:emergency`](#guardianemergency) | Vytvoří 5 jednorázových kódů pro existující username a nahradí staré. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Odešle čekající výstrahy na alert_email pomocí PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Měří ověřování hashů na syntetickém tajemství, nikoli hesle uživatele. |
| [`guardian:uninstall`](#guardianuninstall) | Připraví odstranění souborů prokazatelně vytvořených Guardianem. |

## `guardian:setup`

Připraví soukromou cestu, klíč, úložiště, soubory Symfony a správce, pokud chybí. Zachová účty. Odmítá produkci. Po přímém spuštění vymažte cache a spusťte kontroly.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic vytvoří admin a tajný údaj v soukromém souboru 0600. --path volí cestu dlouhou 25–181 znaků bez nahrazení existující. --no-admin přeskočí vytvoření správce; bez aktivního účtu portál nefunguje. --username navrhuje interaktivní jméno, výchozí admin; automatický účet nemění.

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

Inicializuje úložiště a vytvoří správce s --username, výchozí admin. Skryté zadání hesla podle aktivní politiky. Odmítá existující účet. Nevytváří trasy ani konfiguraci Symfony; běžně použijte úplného průvodce.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Uživatelské jméno : 3 na 64 Alfanumerické znaky -

## `guardian:access`

Zobrazí cestu panelu a umístění počátečních údajů, nikdy heslo. --base-url přijímá skutečný HTTPS původ s volitelným portem, bez přihlašovacích údajů, dotazu a fragmentu. Nahraďte ukázkového hostitele.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Kontroluje úložiště, oprávnění, klíč, trasu, aktivního správce a nastavení. Kód 1 značí problém. PHP CLI a PHP-FPM mohou mít jiné časové zóny. Netestuje dostupnost z Internetu ani proxy pravidla.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Změní heslo existujícího username skrytým zadáním podle politiky, zruší všechny jeho relace a zaznamená akci. Heslo nezadávejte jako argument.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Odstraní tajný klíč a čítač TOTP účtu username, zruší relace a zaznamená akci. Zaregistrujte nový autentifikátor; globální požadavek MFA zůstává a může blokovat profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Vynuluje čítače username. --ip ADRESA odstraní i čítač daného IPv4/IPv6 zdroje; jinak může síťové blokování zůstat. --all vymaže všechny čítače. Účty, hesla a relace se nemění; akce se zaznamená.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Vyprázdní IP seznam a vypne plán, JIT a vzdálený nouzový přístup. Přihlášení, MFA, globální uzamčení a blokování přihlášení zůstávají. Potřebná omezení znovu nastavte v panelu.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on zavře diagnostiku a odstraní všechny relace; běžné stránky fungují dál. off ruší pouze globální uzamčení: je nutné nové přihlášení a ostatní pravidla platí dál.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Vytvoří 5 jednorázových kódů pro existující username a nahradí staré. Zobrazí je jednou; uchovejte mimo server ve správci tajemství. Vzdálené použití vyžaduje zapnutou nouzi, přihlášenou relaci, heslo a důvod. Neobchází globální uzamčení.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Odešle čekající výstrahy na alert_email pomocí PHP mail(). Nastavte poštovní přenos a plánovanou úlohu, ověřte doručení. Neúspěšné zůstávají ve frontě. Kód 1 znamená neplatného příjemce nebo chybu odeslání.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Měří ověřování hashů na syntetickém tajemství, nikoli hesle uživatele. --iterations: 1–10, výchozí 3. JSON s algoritmem, časem a počtem ověření za sekundu. Jeden místní CPU proces, nikoli GPU či útočník.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Připraví odstranění souborů prokazatelně vytvořených Guardianem. Zachová účty, nastavení, protokoly a klíč. Zastavte procesy, spusťte před composer remove, vymažte dev cache a restartujte. Upravené soubory a nejasný původ zkontrolujte.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge žádá smazání dat s výchozím zamítnutím. --force vyžaduje --purge a potvrzuje zničení; nutné při --no-interaction. Externí úložiště, odkazy a neznámé soubory mohou zabránit úplnému smazání. Potřebné klíče zachovejte a ověřte výsledek.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Odinstalace a data


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Zálohujte úložiště a klíč společně a vyzkoušejte obnovu. Ztracené klíče se automaticky neobnovují. Po odstranění kontrolujte profiler jen neveřejně. Produkce: APP_DEBUG=0, WebProfilerBundle a sběr vypnuté. Místní řetězec auditu nedokazuje integritu napadeného hostitele.

[Odinstalace a data](desinstallation.md)
