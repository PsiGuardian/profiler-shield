# Commando-overzicht

[Documentatie-inhoud](README.md)

Voer uit vanuit de Symfony-hoofdmap in een vertrouwde terminal. Voorbeelden gebruiken dev. Zet nooit wachtwoorden, sleutels of herstelcodes in argumenten, logboeken of Git.

## Commando’s ontdekken en hulp opvragen


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev en --no-interaction zijn Symfony-opties. --no-interaction levert geen geheimen en is ongeschikt voor interactieve wachtwoordcommando’s. --help toont syntaxis; -v meer details. Code 0 bevestigt het gemelde resultaat; --no-admin kan slagen zonder bruikbaar portaal.

## `vendor/bin/guardian`

Activeert de bundle in dev/test, wist de cache, voert guardian:setup --automatic uit, wist opnieuw en voert guardian:doctor en guardian:access uit. Stopt bij de eerste fout; herhalen behoudt accounts en sleutels. Weigert APP_ENV=prod en vereist proc_open.

Zonder argument, met --help, -h of help verschijnt alleen hulp. setup is de enige actie en accepteert geen extra opties.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Commando-overzicht |
|---|---|
| [`guardian:setup`](#guardiansetup) | Bereidt privépad, sleutel, kluis, Symfony-bestanden en zo nodig een beheerder voor. |
| [`guardian:install`](#guardianinstall) | Initialiseert de kluis en maakt een beheerder met --username, standaard admin. |
| [`guardian:access`](#guardianaccess) | Toont het paneelpad en de locatie van initiële inloggegevens, nooit het wachtwoord. |
| [`guardian:doctor`](#guardiandoctor) | Controleert kluis, rechten, sleutel, route, actieve beheerder en configuratie. |
| [`guardian:password:reset`](#guardianpasswordreset) | Wijzigt het wachtwoord van bestaand account username met verborgen invoer volgens het beleid, trekt alle sessies in en registreert de actie. |
| [`guardian:mfa:reset`](#guardianmfareset) | Verwijdert TOTP-geheim en teller van username, trekt sessies in en registreert de actie. |
| [`guardian:login:unlock`](#guardianloginunlock) | Zet de tellers van username terug. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Leegt de IP-lijst en schakelt tijdschema, JIT en noodtoegang op afstand uit. |
| [`guardian:lockdown`](#guardianlockdown) | on sluit diagnostiek en verwijdert alle sessies; bedrijfsfuncties blijven beschikbaar. |
| [`guardian:emergency`](#guardianemergency) | Maakt 5 eenmalige codes voor bestaand username en vervangt eerdere codes. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Verstuurt wachtende meldingen naar alert_email via PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Meet hashcontroles met een synthetisch geheim, geen gebruikerswachtwoord. |
| [`guardian:uninstall`](#guardianuninstall) | Bereidt verwijdering van aantoonbaar door Guardian gemaakte bestanden voor. |

## `guardian:setup`

Bereidt privépad, sleutel, kluis, Symfony-bestanden en zo nodig een beheerder voor. Behoudt accounts. Weigert productie. Wis na een directe aanroep de cache en voer de controles uit.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic maakt admin en een geheim in een privébestand 0600. --path kiest een pad van 25–181 tekens en vervangt geen bestaand pad. --no-admin slaat beheerderaanmaak over; zonder actief account is het portaal onbruikbaar. --username stelt de interactieve naam voor, standaard admin; het automatische account verandert niet.

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

Initialiseert de kluis en maakt een beheerder met --username, standaard admin. Vraagt verborgen wachtwoordinvoer volgens het actieve beleid. Weigert bestaande accounts. Maakt geen routes of Symfony-configuratie; gebruik gewoonlijk de volledige assistent.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Gebruikersnaam : 3 om te 64 Alfanumerische karakters,

## `guardian:access`

Toont het paneelpad en de locatie van initiële inloggegevens, nooit het wachtwoord. --base-url accepteert de echte HTTPS-oorsprong met optionele poort, zonder inloggegevens, query of fragment. Vervang de voorbeeldhost.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Controleert kluis, rechten, sleutel, route, actieve beheerder en configuratie. Geeft code 1 bij problemen. PHP CLI en PHP-FPM kunnen verschillende tijdzones gebruiken. Controleert geen internetblootstelling of proxyregels.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Wijzigt het wachtwoord van bestaand account username met verborgen invoer volgens het beleid, trekt alle sessies in en registreert de actie. Geef het wachtwoord nooit als argument.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Verwijdert TOTP-geheim en teller van username, trekt sessies in en registreert de actie. Registreer een nieuwe authenticator; verplichte MFA blijft actief en kan de profiler blokkeren.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Zet de tellers van username terug. --ip ADRES wist ook de teller van die IPv4/IPv6-oorsprong; anders kan de netwerkblokkade blijven. --all wist alle tellers. Accounts, wachtwoorden en sessies blijven gelijk; de actie wordt geregistreerd.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Leegt de IP-lijst en schakelt tijdschema, JIT en noodtoegang op afstand uit. Authenticatie, MFA, algemene blokkade en aanmeldblokkades blijven bestaan. Stel gewenste beperkingen daarna opnieuw in.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on sluit diagnostiek en verwijdert alle sessies; bedrijfsfuncties blijven beschikbaar. off heft alleen de algemene blokkade op: opnieuw aanmelden en overige regels blijven nodig.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Maakt 5 eenmalige codes voor bestaand username en vervangt eerdere codes. Eenmalig zichtbaar; bewaar buiten de server in een geheimenbeheerder. Gebruik op afstand vereist ingeschakelde noodtoegang, aangemelde sessie, wachtwoord en reden. Omzeilt geen algemene blokkade.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Verstuurt wachtende meldingen naar alert_email via PHP mail(). Configureer mailtransport en geplande taak en controleer daadwerkelijke bezorging. Mislukte meldingen blijven wachten. Code 1 bij ongeldige ontvanger of verzendfout.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Meet hashcontroles met een synthetisch geheim, geen gebruikerswachtwoord. --iterations accepteert 1–10, standaard 3. JSON met algoritme, tijd en controles per seconde. Meet één lokaal CPU-proces, geen GPU of aanvaller.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Bereidt verwijdering van aantoonbaar door Guardian gemaakte bestanden voor. Behoudt accounts, instellingen, logboeken en sleutel. Stop workers, voer uit vóór composer remove, wis de dev-cache en herstart. Controleer aangepaste bestanden en bestanden zonder herkomstbewijs.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge vraagt gegevensverwijdering met standaard Nee. --force vereist --purge en bevestigt vernietiging; verplicht bij --no-interaction. Externe kluizen, koppelingen en onbekende bestanden kunnen volledige verwijdering verhinderen. Bewaar benodigde sleutels en controleer het resultaat.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Verwijdering en gegevens


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Maak samen een back-up van kluis en sleutel en test herstel. Verloren sleutels worden nooit automatisch hermaakt. Controleer na verwijdering de profiler alleen privé. Productie: APP_DEBUG=0, WebProfilerBundle en verzameling uit. Een lokale auditketen bewijst geen integriteit op een gecompromitteerde host.

[Oninstallatie en gegevens](desinstallation.md)
