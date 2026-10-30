# Tweefactorauthenticatie

[Documentatie-inhoud](README.md)


## Tweefactorauthenticatie

Elk persoon registreert zijn eigen TOTP geheim en bevestigt een code.

Elk persoon registreert zijn eigen TOTP geheim en bevestigt een code. aanvraag MFA blokken profielen toegang voor ongeregistreerde accounts. plan administratieve herstel voordat deze bescherming vereist voor het hele team. Kies handmatige configuratie en voer de getoonde sleutel in. Gebruik SHA-1, zes cijfers en een periode van 30 seconden.

## 1. Bevestig uw identiteit

Voer hetzelfde wachtwoord in als bij het aanmelden bij Guardian. U hoeft het maar één keer op te geven.

## 2. Voer de sleutel in de authenticator in

Kies handmatige configuratie en voer de getoonde sleutel in. Gebruik SHA-1, zes cijfers en een periode van 30 seconden.

## 3. Bevestig een zescijferige code

Voer de code uit uw app in. Een tweede wachtwoord is niet nodig.

## Privacy: voer de website-URL niet in

Als de app naar een naam of website vraagt, voer dan alleen 'ΩGuardian' of een neutrale naam in. Voer nooit de URL van uw Symfony-instantie in.

Houd deze geheim: hiermee kunnen TOTP-codes worden gemaakt. Deel de sleutel niet en maak geen screenshots.

Het tijdelijke geheim verloopt na 10 minuten of 5 verkeerde codes; begin dan opnieuw. Een fout behoudt het formulier zonder het wachtwoord opnieuw te vragen. De bevestigingsteller wordt verbruikt tegen herhaling.

## Herstel en verificatie

Verwijdert TOTP-geheim en teller van username, trekt sessies in en registreert de actie. Registreer een nieuwe authenticator; verplichte MFA blijft actief en kan de profiler blokkeren.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
