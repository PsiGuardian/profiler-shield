# Dvofaktorska autentifikacija

[Sadržaj dokumentacije](README.md)


## Dvofaktorska autentifikacija

Svaka osoba upisuje svoje TOTP tajna i potvrđuje kod.

Svaka osoba upisuje svoje TOTP tajna i potvrđuje kod. zahtjev MFA blokovi profila za pristup neispisanim računima. planirati administrativnu oporavak prije zahtijevanja ove zaštite za cijeli tim. Odaberite ručnu postavku i unesite prikazani ključ. Koristite SHA-1, šest znamenki i razdoblje od 30 sekundi.

## 1. Potvrdite identitet

Unesite istu lozinku kojom se prijavljujete u Guardian. Za ovu postavku traži se samo jednom.

## 2. Unesite ključ u aplikaciju za autentifikaciju

Odaberite ručnu postavku i unesite prikazani ključ. Koristite SHA-1, šest znamenki i razdoblje od 30 sekundi.

## 3. Potvrdite šesteroznamenkasti kod

Unesite kod prikazan u aplikaciji. Druga lozinka nije potrebna.

## Privatnost: ne unosite URL web-mjesta

Ako aplikacija traži naziv ili web-mjesto, unesite samo „ΩGuardian” ili neutralni naziv. Ne unosite URL Symfony instance.

Čuvajte ga tajnim: omogućuje izradu TOTP kodova. Ne dijelite ga ni ne snimajte zaslon.

Privremena tajna istječe nakon 10 minuta ili 5 pogrešnih kodova; tada počnite ponovno. Pogrešan kod zadržava obrazac bez novog unosa lozinke. Brojač potvrde troši se protiv ponavljanja.

## Oporavak i provjera

Briše TOTP tajnu i brojač za username, opoziva sesije i bilježi radnju. Registrirajte novi autentifikator; globalni MFA ostaje obvezan i može blokirati profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
