# Pregled naredbi

[Sadržaj dokumentacije](README.md)

Pokrenite iz korijena Symfonyja u pouzdanom terminalu. Primjeri koriste dev. Lozinke, ključeve i kodove oporavka ne stavljajte u argumente, zapisnike ili Git.

## Otkrivanje naredbi i pomoć


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev i --no-interaction su Symfony mogućnosti. --no-interaction ne daje tajne i nije za interaktivne lozinke. --help prikazuje sintaksu; -v detalje. Kod 0 potvrđuje prijavljeni rezultat; --no-admin može uspjeti bez uporabljivog portala.

## `vendor/bin/guardian`

Aktivira bundle u dev/test, čisti predmemoriju, pokreće guardian:setup --automatic, ponovno čisti te pokreće guardian:doctor i guardian:access. Staje na prvoj pogrešci. Ponavljanje čuva račune i ključeve. Odbija APP_ENV=prod i zahtijeva proc_open.

Bez argumenta, --help, -h ili help prikazuje samo pomoć. setup je jedina radnja i ne prima dodatne mogućnosti.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Pregled naredbi |
|---|---|
| [`guardian:setup`](#guardiansetup) | Priprema privatnu putanju, ključ, trezor, Symfony datoteke i administratora ako nedostaje. |
| [`guardian:install`](#guardianinstall) | Inicijalizira trezor i stvara administratora s --username, zadano admin. |
| [`guardian:access`](#guardianaccess) | Prikazuje putanju ploče i mjesto početnih vjerodajnica, nikada lozinku. |
| [`guardian:doctor`](#guardiandoctor) | Provjerava trezor, dozvole, ključ, rutu, aktivnog administratora i postavke. |
| [`guardian:password:reset`](#guardianpasswordreset) | Mijenja lozinku postojećeg username skrivenim unosom prema pravilima, opoziva sve sesije i bilježi radnju. |
| [`guardian:mfa:reset`](#guardianmfareset) | Briše TOTP tajnu i brojač za username, opoziva sesije i bilježi radnju. |
| [`guardian:login:unlock`](#guardianloginunlock) | Poništava brojače username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Prazni IP popis i isključuje raspored, JIT i udaljeni hitni pristup. |
| [`guardian:lockdown`](#guardianlockdown) | on zatvara dijagnostiku i briše sve sesije; poslovne stranice rade dalje. |
| [`guardian:emergency`](#guardianemergency) | Stvara 5 jednokratnih kodova za postojeći username i zamjenjuje stare. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Šalje upozorenja iz reda na alert_email pomoću PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Mjeri provjere hasha sa sintetičkom tajnom, bez korisničke lozinke. |
| [`guardian:uninstall`](#guardianuninstall) | Priprema uklanjanje generiranih datoteka pripisivih Guardianu. |

## `guardian:setup`

Priprema privatnu putanju, ključ, trezor, Symfony datoteke i administratora ako nedostaje. Čuva račune. Odbija produkciju. Nakon izravnog pokretanja očistite predmemoriju i provedite provjere.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic stvara admin i tajnu u privatnoj datoteci 0600. --path bira putanju od 25–181 znakova bez zamjene postojeće. --no-admin preskače stvaranje administratora; bez aktivnog računa portal ne radi. --username predlaže interaktivno ime, zadano admin; ne mijenja automatski račun.

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

Inicijalizira trezor i stvara administratora s --username, zadano admin. Traži skriveni unos lozinke prema pravilima. Odbija postojeći račun. Ne stvara rute niti Symfony konfiguraciju; obično koristite potpuni čarobnjak.


```bash
php bin/console guardian:install --username=admin --env=dev
```


korisničko ime : 3 na 64 Alfanumerički znakovi,

## `guardian:access`

Prikazuje putanju ploče i mjesto početnih vjerodajnica, nikada lozinku. --base-url prima stvarno HTTPS ishodište s neobveznim portom, bez vjerodajnica, upita ili fragmenta. Zamijenite oglednog domaćina.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Provjerava trezor, dozvole, ključ, rutu, aktivnog administratora i postavke. Kod 1 označava problem. PHP CLI i PHP-FPM mogu imati različite vremenske zone. Ne ispituje izloženost Internetu niti proxy pravila.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Mijenja lozinku postojećeg username skrivenim unosom prema pravilima, opoziva sve sesije i bilježi radnju. Lozinku ne prosljeđujte kao argument.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Briše TOTP tajnu i brojač za username, opoziva sesije i bilježi radnju. Registrirajte novi autentifikator; globalni MFA ostaje obvezan i može blokirati profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Poništava brojače username. --ip ADRESA briše i brojač tog IPv4/IPv6 izvora; inače mrežna blokada može ostati. --all briše sve brojače. Računi, lozinke i sesije ne mijenjaju se; radnja se bilježi.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Prazni IP popis i isključuje raspored, JIT i udaljeni hitni pristup. Autentifikacija, MFA, globalna blokada i blokade prijave ostaju. Ponovno postavite željena ograničenja na ploči.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on zatvara dijagnostiku i briše sve sesije; poslovne stranice rade dalje. off uklanja samo globalnu blokadu: prijavite se ponovno, ostala pravila i dalje vrijede.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Stvara 5 jednokratnih kodova za postojeći username i zamjenjuje stare. Prikazani jednom; čuvajte izvan poslužitelja u upravitelju tajni. Udaljena uporaba zahtijeva omogućenu hitnost, prijavljenu sesiju, lozinku i razlog. Ne zaobilazi globalnu blokadu.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Šalje upozorenja iz reda na alert_email pomoću PHP mail(). Postavite transport i zakazani zadatak, provjerite dostavu. Neuspjesi ostaju u redu. Kod 1 za nevaljanog primatelja ili pogrešku slanja.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Mjeri provjere hasha sa sintetičkom tajnom, bez korisničke lozinke. --iterations: 1–10, zadano 3. JSON s algoritmom, vremenom i provjerama u sekundi. Mjeri jedan lokalni CPU proces, ne GPU ili napadača.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Priprema uklanjanje generiranih datoteka pripisivih Guardianu. Čuva račune, postavke, zapisnike i ključ. Zaustavite procese, pokrenite prije composer remove, očistite dev predmemoriju i ponovno pokrenite. Pregledajte prilagođene datoteke i nepoznato podrijetlo.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge traži brisanje podataka sa zadanom odbijenicom. --force zahtijeva --purge i potvrđuje uništenje; obvezno uz --no-interaction. Vanjski trezori, poveznice i nepoznate datoteke mogu spriječiti potpuno brisanje. Sačuvajte potrebne ključeve i pregledajte rezultat.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Deinstalacija i podaci


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Zajedno kopirajte trezor i ključ te testirajte obnovu. Izgubljeni ključevi ne stvaraju se automatski. Nakon uklanjanja provjerite profiler samo privatno. Produkcija: APP_DEBUG=0, WebProfilerBundle i prikupljanje isključeni. Lokalni auditni lanac ne dokazuje integritet kompromitiranog hosta.

[Odlaganje i podaci](desinstallation.md)
