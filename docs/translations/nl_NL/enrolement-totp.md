# Tweefactorauthenticatie

[Documentatie-inhoud](README.md)


## Tweefactorauthenticatie

Bescherm uw Guardian-account met tijdelijke codes zonder externe QR-diensten.

Elk persoon registreert zijn eigen TOTP geheim en bevestigt een code. aanvraag MFA blokken profielen toegang voor ongeregistreerde accounts. de QR code blijft lokaal aan de portal. plan administratieve herstel voordat deze bescherming vereist voor het hele team.

## 1. Bevestig uw identiteit

Voer hetzelfde wachtwoord in als bij het aanmelden bij Guardian. U hoeft het maar één keer op te geven.

## 2. Scan deze QR-code met uw authenticator

Guardian genereert de QR-code lokaal. Er worden geen verzoeken aan externe diensten verzonden.

## 3. Bevestig een zescijferige code

Voer de code uit uw app in. Een tweede wachtwoord is niet nodig.

## Privacy: voer de website-URL niet in

Als de app naar een naam of website vraagt, voer dan alleen 'ΩGuardian' of een neutrale naam in. Voer nooit de URL van uw Symfony-instantie in. Online QR-generatoren zouden uw geheim ontvangen.

Houd deze geheim: hiermee kunnen TOTP-codes worden gemaakt. Deel de sleutel niet en maak geen screenshots.

Het tijdelijke geheim verloopt na 10 minuten of 5 verkeerde codes; begin dan opnieuw. Een fout behoudt het formulier zonder het wachtwoord opnieuw te vragen. De bevestigingsteller wordt verbruikt tegen herhaling. SVG-QR gebruikt bacon/bacon-qr-code en ext-iconv, zonder GD, XMLWriter of netwerk. Handmatige invoer blijft mogelijk. De otpauth-URI bevat een neutrale aanbieder en account, geen sitedomein. Deel geheim en privé-URL niet.

## Herstel en verificatie

Verwijdert TOTP-geheim en teller van username, trekt sessies in en registreert de actie. Registreer een nieuwe authenticator; verplichte MFA blijft actief en kan de profiler blokkeren.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
