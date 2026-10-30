# Prehľad príkazov

[Obsah dokumentácie](README.md)

Spúšťajte z koreňa Symfony v dôveryhodnom termináli. Príklady používajú dev. Heslá, kľúče a obnovovacie kódy nepatria do argumentov, protokolov ani Gitu.

## Vyhľadanie príkazov a pomocník


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev a --no-interaction sú voľby Symfony. --no-interaction neposkytuje tajné údaje a nehodí sa na interaktívne heslá. --help opisuje syntax; -v pridá podrobnosti. Kód 0 potvrdzuje hlásený výsledok; --no-admin môže uspieť bez funkčného portálu.

## `vendor/bin/guardian`

Aktivuje bundle v dev/test, vymaže cache, spustí guardian:setup --automatic, znova vymaže cache a spustí guardian:doctor a guardian:access. Zastaví pri prvej chybe. Opakovanie zachová účty a kľúče. Odmieta APP_ENV=prod a vyžaduje proc_open.

Bez argumentu, --help, -h alebo help zobrazí iba pomocníka. setup je jediná akcia a neprijíma ďalšie voľby.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Prehľad príkazov |
|---|---|
| [`guardian:setup`](#guardiansetup) | Pripraví súkromnú cestu, kľúč, úložisko, súbory Symfony a správcu, ak chýba. |
| [`guardian:install`](#guardianinstall) | Inicializuje úložisko a vytvorí správcu s --username, predvolene admin. |
| [`guardian:access`](#guardianaccess) | Zobrazí cestu panela a umiestnenie úvodných údajov, nikdy heslo. |
| [`guardian:doctor`](#guardiandoctor) | Kontroluje úložisko, oprávnenia, kľúč, trasu, aktívneho správcu a nastavenia. |
| [`guardian:password:reset`](#guardianpasswordreset) | Zmení heslo existujúceho username skrytým zadaním podľa politiky, zruší všetky jeho relácie a zaznamená akciu. |
| [`guardian:mfa:reset`](#guardianmfareset) | Odstráni tajný kľúč a počítadlo TOTP účtu username, zruší relácie a zaznamená akciu. |
| [`guardian:login:unlock`](#guardianloginunlock) | Vynuluje počítadlá username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Vyprázdni zoznam IP a vypne plán, JIT a vzdialenú núdzu. |
| [`guardian:lockdown`](#guardianlockdown) | on zavrie diagnostiku a odstráni všetky relácie; bežné stránky fungujú ďalej. |
| [`guardian:emergency`](#guardianemergency) | Vytvorí 5 jednorazových kódov pre existujúci username a nahradí staré. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Odošle čakajúce upozornenia na alert_email cez PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Meria overovanie hashov na syntetickom tajomstve, nie hesle používateľa. |
| [`guardian:uninstall`](#guardianuninstall) | Pripraví odstránenie súborov preukázateľne vytvorených Guardianom. |

## `guardian:setup`

Pripraví súkromnú cestu, kľúč, úložisko, súbory Symfony a správcu, ak chýba. Zachová účty. Odmieta produkciu. Po priamom spustení vymažte cache a vykonajte kontroly.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic vytvorí admin a tajný údaj v súkromnom súbore 0600. --path volí cestu s 25–181 znakmi bez nahradenia existujúcej. --no-admin preskočí vytvorenie správcu; bez aktívneho účtu portál nefunguje. --username navrhuje interaktívne meno, predvolene admin; automatický účet nemení.

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

Inicializuje úložisko a vytvorí správcu s --username, predvolene admin. Skryté zadanie hesla podľa aktívnej politiky. Odmieta existujúci účet. Nevytvára trasy ani konfiguráciu Symfony; bežne použite úplného sprievodcu.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Užívateľské meno : 3 na 64 Alfanumerické znaky -

## `guardian:access`

Zobrazí cestu panela a umiestnenie úvodných údajov, nikdy heslo. --base-url prijíma skutočný HTTPS pôvod s voliteľným portom, bez prihlasovacích údajov, dotazu a fragmentu. Nahraďte vzorového hostiteľa.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Kontroluje úložisko, oprávnenia, kľúč, trasu, aktívneho správcu a nastavenia. Kód 1 znamená problém. PHP CLI a PHP-FPM môžu mať iné časové pásma. Netestuje prístup z Internetu ani pravidlá proxy.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Zmení heslo existujúceho username skrytým zadaním podľa politiky, zruší všetky jeho relácie a zaznamená akciu. Heslo neuvádzajte ako argument.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Odstráni tajný kľúč a počítadlo TOTP účtu username, zruší relácie a zaznamená akciu. Zaregistrujte nový autentifikátor; globálna požiadavka MFA zostáva a môže blokovať profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Vynuluje počítadlá username. --ip ADRESA odstráni aj počítadlo daného IPv4/IPv6 zdroja; inak môže sieťové blokovanie zostať. --all vymaže všetky počítadlá. Účty, heslá a relácie sa nemenia; akcia sa zaznamená.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Vyprázdni zoznam IP a vypne plán, JIT a vzdialenú núdzu. Prihlásenie, MFA, globálne uzamknutie a blokovanie prihlásenia zostávajú. Potrebné obmedzenia znova nastavte v paneli.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on zavrie diagnostiku a odstráni všetky relácie; bežné stránky fungujú ďalej. off ruší iba globálne uzamknutie: treba nové prihlásenie a ostatné pravidlá zostávajú.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Vytvorí 5 jednorazových kódov pre existujúci username a nahradí staré. Zobrazí ich raz; uložte mimo servera v správcovi tajomstiev. Vzdialené použitie vyžaduje zapnutú núdzu, prihlásenú reláciu, heslo a dôvod. Neobchádza globálne uzamknutie.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Odošle čakajúce upozornenia na alert_email cez PHP mail(). Nastavte poštový prenos a plánovanú úlohu, overte doručenie. Neúspešné zostávajú vo fronte. Kód 1 znamená neplatného príjemcu alebo chybu odoslania.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Meria overovanie hashov na syntetickom tajomstve, nie hesle používateľa. --iterations: 1–10, predvolene 3. JSON s algoritmom, časom a overeniami za sekundu. Jeden miestny CPU proces, nie GPU ani útočník.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Pripraví odstránenie súborov preukázateľne vytvorených Guardianom. Zachová účty, nastavenia, protokoly a kľúč. Zastavte procesy, spustite pred composer remove, vymažte dev cache a reštartujte. Upravené súbory a nejasný pôvod skontrolujte.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge žiada zmazanie dát s predvoleným odmietnutím. --force vyžaduje --purge a potvrdzuje zničenie; nutné pri --no-interaction. Externé úložiská, odkazy a neznáme súbory môžu zabrániť úplnému zmazaniu. Potrebné kľúče zachovajte a overte výsledok.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Odinštalovanie a dáta


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Zálohujte úložisko a kľúč spolu a vyskúšajte obnovu. Stratené kľúče sa automaticky neobnovujú. Po odstránení kontrolujte profiler len neverejne. Produkcia: APP_DEBUG=0, WebProfilerBundle a zber vypnuté. Miestny reťazec auditu nedokazuje integritu napadnutého hostiteľa.

[Odinštalácia a údaje](desinstallation.md)
