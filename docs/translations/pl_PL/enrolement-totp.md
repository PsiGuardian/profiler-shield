# Uwierzytelnianie dwuskładnikowe

[Spis dokumentacji](README.md)


## Uwierzytelnianie dwuskładnikowe

Chroń konto Guardian kodami tymczasowymi, bez zewnętrznych usług QR.

Każda osoba rejestruje własną TOTP Sekret i potwierdza kod. żądanie MFA Bloki profilów dostępu do niezarejestrowanych kont. QR Planowanie odzyskiwania administracyjnego przed wymaganiem tej ochrony dla całego zespołu.

## 1. Potwierdź tożsamość

Wpisz to samo hasło, którego używasz do logowania w Guardian. Podasz je tylko raz.

## 2. Zeskanuj kod QR w aplikacji uwierzytelniającej

Guardian generuje QR lokalnie, bez żądań do zewnętrznych usług.

## 3. Potwierdź sześciocyfrowy kod

Wpisz kod z aplikacji. Drugie hasło nie jest wymagane.

## Prywatność: nie wpisuj adresu URL witryny

Jeśli aplikacja prosi o nazwę lub stronę, wpisz tylko „ΩGuardian” lub neutralną nazwę. Nie podawaj URL instancji Symfony. Nie używaj generatorów QR online: otrzymałyby Twój sekret.

Zachowaj go w tajemnicy: umożliwia tworzenie kodów TOTP. Nie udostępniaj go ani nie rób zrzutów ekranu.

Sekret tymczasowy wygasa po 10 minutach lub 5 błędnych kodach; wtedy zacznij od nowa. Błąd zachowuje formularz bez ponownego hasła. Licznik potwierdzenia jest zużywany przeciw powtórzeniu. QR SVG używa bacon/bacon-qr-code i ext-iconv bez GD, XMLWriter i sieci. Pozostaje wpis ręczny. URI otpauth używa neutralnego dostawcy i konta, bez domeny strony. Nie udostępniaj sekretu ani prywatnego URL.

## Odzyskiwanie i weryfikacja

Usuwa sekret i licznik TOTP konta username, unieważnia sesje i zapisuje zdarzenie. Zarejestruj nowy uwierzytelniacz; globalny wymóg MFA pozostaje i może blokować profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
