# Dvofaktorska autentifikacija

[Sadržaj dokumentacije](README.md)


## Dvofaktorska autentifikacija

Zaštitite Guardian račun privremenim kodovima, bez vanjskih QR usluga.

Svaka osoba upisuje svoje TOTP tajna i potvrđuje kod. zahtjev MFA blokovi profila za pristup neispisanim računima. QR kod ostaje lokalno na portalu. planirati administrativnu oporavak prije zahtijevanja ove zaštite za cijeli tim.

## 1. Potvrdite identitet

Unesite istu lozinku kojom se prijavljujete u Guardian. Za ovu postavku traži se samo jednom.

## 2. Skenirajte QR u aplikaciji za autentikaciju

Guardian generira QR lokalno, bez zahtjeva vanjskim uslugama.

## 3. Potvrdite šesteroznamenkasti kod

Unesite kod prikazan u aplikaciji. Druga lozinka nije potrebna.

## Privatnost: ne unosite URL web-mjesta

Ako aplikacija traži naziv ili web-mjesto, unesite samo „ΩGuardian” ili neutralni naziv. Ne unosite URL Symfony instance. Ne koristite mrežne generatore QR-a: primili bi vašu tajnu.

Čuvajte ga tajnim: omogućuje izradu TOTP kodova. Ne dijelite ga ni ne snimajte zaslon.

Privremena tajna istječe nakon 10 minuta ili 5 pogrešnih kodova; tada počnite ponovno. Pogrešan kod zadržava obrazac bez novog unosa lozinke. Brojač potvrde troši se protiv ponavljanja. SVG QR koristi bacon/bacon-qr-code i ext-iconv bez GD, XMLWriter ili mreže. Ručni unos ostaje dostupan. URI otpauth ima neutralnog pružatelja i račun, bez domene stranice. Ne dijelite tajnu ni privatni URL.

## Oporavak i provjera

Briše TOTP tajnu i brojač za username, opoziva sesije i bilježi radnju. Registrirajte novi autentifikator; globalni MFA ostaje obvezan i može blokirati profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
