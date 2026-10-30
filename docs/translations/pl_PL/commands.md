# Spis poleceń

[Spis dokumentacji](README.md)

Uruchamiaj w katalogu głównym Symfony w zaufanym terminalu. Przykłady używają dev. Nie umieszczaj haseł, kluczy ani kodów odzyskiwania w argumentach, logach ani Git.

## Odkrywanie poleceń i pomoc


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev i --no-interaction należą do Symfony. --no-interaction nie dostarcza sekretów i nie nadaje się do interaktywnych poleceń haseł. --help pokazuje składnię; -v szczegóły. Kod 0 potwierdza zgłoszony wynik; --no-admin może zakończyć się bez działającego portalu.

## `vendor/bin/guardian`

Aktywuje bundle w dev/test, czyści cache, uruchamia guardian:setup --automatic, ponownie czyści cache, następnie uruchamia guardian:doctor i guardian:access. Zatrzymuje się przy pierwszym błędzie. Powtórzenie zachowuje konta i klucze. Odrzuca APP_ENV=prod i wymaga proc_open.

Bez argumentu, --help, -h lub help wyświetla pomoc bez zmian. setup jest jedyną czynnością i nie przyjmuje dodatkowych opcji.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Spis poleceń |
|---|---|
| [`guardian:setup`](#guardiansetup) | Przygotowuje prywatną ścieżkę, klucz, magazyn, pliki Symfony i administratora, jeśli go brakuje. |
| [`guardian:install`](#guardianinstall) | Inicjalizuje magazyn i tworzy administratora z --username, domyślnie admin. |
| [`guardian:access`](#guardianaccess) | Wyświetla ścieżkę panelu i położenie początkowych danych logowania, nigdy hasło. |
| [`guardian:doctor`](#guardiandoctor) | Sprawdza magazyn, uprawnienia, klucz, trasę, aktywnego administratora i konfigurację. |
| [`guardian:password:reset`](#guardianpasswordreset) | Zmienia hasło istniejącego konta username przez ukryte wprowadzanie zgodne z polityką, unieważnia wszystkie sesje i zapisuje zdarzenie. |
| [`guardian:mfa:reset`](#guardianmfareset) | Usuwa sekret i licznik TOTP konta username, unieważnia sesje i zapisuje zdarzenie. |
| [`guardian:login:unlock`](#guardianloginunlock) | Zeruje liczniki konta username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Czyści listę IP i wyłącza harmonogram, JIT i zdalny dostęp awaryjny. |
| [`guardian:lockdown`](#guardianlockdown) | on zamyka diagnostykę i usuwa wszystkie sesje; strony biznesowe działają dalej. |
| [`guardian:emergency`](#guardianemergency) | Generuje 5 jednorazowych kodów dla istniejącego username, zastępując wcześniejsze. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Wysyła oczekujące alerty na alert_email przez PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Mierzy weryfikację skrótów na syntetycznym sekrecie, bez hasła użytkownika. |
| [`guardian:uninstall`](#guardianuninstall) | Przygotowuje usunięcie plików przypisanych do Guardian. |

## `guardian:setup`

Przygotowuje prywatną ścieżkę, klucz, magazyn, pliki Symfony i administratora, jeśli go brakuje. Zachowuje istniejące konta. Odrzuca produkcję. Po bezpośrednim uruchomieniu wyczyść cache i wykonaj kontrole.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic tworzy admin i sekret w prywatnym pliku 0600. --path wybiera ścieżkę długości 25–181 znaków, bez zastępowania istniejącej. --no-admin pomija tworzenie administratora; bez aktywnego konta portal jest niedostępny. --username proponuje nazwę interaktywną, domyślnie admin; nie zmienia konta automatycznego.

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

Inicjalizuje magazyn i tworzy administratora z --username, domyślnie admin. Prosi o ukryte hasło zgodne z polityką. Odrzuca istniejące konto. Nie tworzy tras ani konfiguracji Symfony; zwykle używaj pełnego asystenta.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Nazwa użytkownika : 3 do 64 Charaktery alfanumeryczne -

## `guardian:access`

Wyświetla ścieżkę panelu i położenie początkowych danych logowania, nigdy hasło. --base-url przyjmuje rzeczywiste źródło HTTPS z opcjonalnym portem, bez danych logowania, zapytania i fragmentu. Zastąp przykładowy host.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Sprawdza magazyn, uprawnienia, klucz, trasę, aktywnego administratora i konfigurację. Kod 1 oznacza wykryty problem. PHP CLI i PHP-FPM mogą mieć inne strefy czasowe. Nie sprawdza dostępu z Internetu ani reguł proxy.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Zmienia hasło istniejącego konta username przez ukryte wprowadzanie zgodne z polityką, unieważnia wszystkie sesje i zapisuje zdarzenie. Nie podawaj hasła jako argumentu.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Usuwa sekret i licznik TOTP konta username, unieważnia sesje i zapisuje zdarzenie. Zarejestruj nowy uwierzytelniacz; globalny wymóg MFA pozostaje i może blokować profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Zeruje liczniki konta username. --ip ADRES usuwa też licznik źródła IPv4/IPv6; inaczej blokada sieci może pozostać. --all usuwa wszystkie liczniki. Konta, hasła i sesje się nie zmieniają; operacja jest rejestrowana.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Czyści listę IP i wyłącza harmonogram, JIT i zdalny dostęp awaryjny. Uwierzytelnianie, MFA, blokada globalna i blokady logowania pozostają. Po odzyskaniu dostępu ustaw ponownie potrzebne ograniczenia.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on zamyka diagnostykę i usuwa wszystkie sesje; strony biznesowe działają dalej. off usuwa tylko blokadę globalną: potrzebne jest ponowne logowanie, a inne polityki pozostają.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Generuje 5 jednorazowych kodów dla istniejącego username, zastępując wcześniejsze. Widoczne raz; przechowuj poza serwerem w menedżerze sekretów. Użycie zdalne wymaga włączonego trybu awaryjnego, zalogowanej sesji, hasła i powodu. Nie omija blokady globalnej.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Wysyła oczekujące alerty na alert_email przez PHP mail(). Skonfiguruj transport i zadanie cykliczne, potem sprawdź doręczenie. Nieudane alerty pozostają w kolejce. Kod 1 oznacza błędnego odbiorcę lub błąd wysyłki.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Mierzy weryfikację skrótów na syntetycznym sekrecie, bez hasła użytkownika. --iterations: 1–10, domyślnie 3. JSON z algorytmem, czasem i weryfikacjami na sekundę. Mierzy jeden lokalny proces CPU, nie GPU ani napastnika.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Przygotowuje usunięcie plików przypisanych do Guardian. Domyślnie zachowuje konta, ustawienia, logi i klucz. Zatrzymaj procesy robocze, wykonaj przed composer remove, wyczyść cache dev i uruchom je ponownie. Sprawdź zmodyfikowane pliki i te bez dowodu pochodzenia.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge żąda usunięcia danych z domyślną odmową potwierdzenia. --force wymaga --purge i potwierdza zniszczenie; konieczne z --no-interaction. Zewnętrzne magazyny, dowiązania i nieznane pliki mogą uniemożliwić pełne usunięcie. Zachowaj potrzebne klucze i sprawdź wynik.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Odinstalowanie i dane


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Kopiuj magazyn i klucz razem i testuj odtworzenie. Utracone klucze nie są automatycznie regenerowane. Po usunięciu sprawdzaj profiler tylko prywatnie. Produkcja: APP_DEBUG=0, WebProfilerBundle i zbieranie wyłączone. Lokalny łańcuch audytu nie dowodzi integralności przejętego hosta.

[Odinstalowanie i dane](desinstallation.md)
