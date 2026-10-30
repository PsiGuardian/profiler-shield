# Uwierzytelnianie dwuskładnikowe

[Spis dokumentacji](README.md)


## Uwierzytelnianie dwuskładnikowe

Każda osoba rejestruje własną TOTP Sekret i potwierdza kod.

Każda osoba rejestruje własną TOTP Sekret i potwierdza kod. żądanie MFA Bloki profilów dostępu do niezarejestrowanych kont. Wybierz ręczną konfigurację i wpisz wyświetlony klucz. Ustaw SHA-1, sześć cyfr i okres 30 sekund.

## 1. Potwierdź tożsamość

Wpisz to samo hasło, którego używasz do logowania w Guardian. Podasz je tylko raz.

## 2. Wpisz klucz w aplikacji uwierzytelniającej

Wybierz ręczną konfigurację i wpisz wyświetlony klucz. Ustaw SHA-1, sześć cyfr i okres 30 sekund.

## 3. Potwierdź sześciocyfrowy kod

Wpisz kod z aplikacji. Drugie hasło nie jest wymagane.

## Prywatność: nie wpisuj adresu URL witryny

Jeśli aplikacja prosi o nazwę lub stronę, wpisz tylko „ΩGuardian” lub neutralną nazwę. Nie podawaj URL instancji Symfony.

Zachowaj go w tajemnicy: umożliwia tworzenie kodów TOTP. Nie udostępniaj go ani nie rób zrzutów ekranu.

Sekret tymczasowy wygasa po 10 minutach lub 5 błędnych kodach; wtedy zacznij od nowa. Błąd zachowuje formularz bez ponownego hasła. Licznik potwierdzenia jest zużywany przeciw powtórzeniu.

## Odzyskiwanie i weryfikacja

Usuwa sekret i licznik TOTP konta username, unieważnia sesje i zapisuje zdarzenie. Zarejestruj nowy uwierzytelniacz; globalny wymóg MFA pozostaje i może blokować profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
