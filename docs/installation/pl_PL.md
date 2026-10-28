# ΩGuardian Profiler Shield — Instalacja i użytkowanie

PHP 8.3 lub nowszy; Symfony 5.4, 6.4 lub 7.4. Symfony 8.1 wymaga PHP 8.4 lub nowszego. Wymagane są Composer i rozszerzenie OpenSSL + Tokenizer. Do profilera zainstaluj WebProfilerBundle.

## 1. Instalacja

Uruchom w katalogu głównym aplikacji Symfony zawierającym composer.json i bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Konfiguracja

```bash
php vendor/bin/guardian setup
```

Asystent aktywuje bundle w dev/test, przygotowuje magazyn i konto administratora, czyści pamięć podręczną i sprawdza konfigurację. Każdy krok musi się powieść przed następnym. Zachowaj prywatny dostęp do aplikacji podczas konfiguracji. Ponowne uruchomienie zachowuje konta i klucze.

## 3. Otwarcie panelu

```bash
php bin/console guardian:access --env=dev
```

Polecenie wyświetla prywatną ścieżkę. Zastąp https://VOTRE-HOTE rzeczywistym adresem HTTPS wraz z portem. Odczytaj dane logowania z .guardian-shield/initial-admin-credentials.txt lub wskazanego magazynu. Pierwsze logowanie wymaga zmiany hasła. Nie udostępniaj tego pliku.

## 4. Odinstalowanie

Zatrzymaj procesy robocze i wykonuj polecenia po kolei, sprawdzając wynik każdego kroku. Magazyn i klucz pozostają. Uruchom procesy ponownie i sprawdź profiler w prywatnym środowisku.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Aby usunąć także konta i ustawienia, zastąp pierwsze polecenie poniższym i potwierdź. Odmowa niczego nie zmienia. Sprawdź ręcznie magazyny zewnętrzne i zmienione pliki; zachowaj klucze potrzebnych magazynów.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. CLI

Pomoc opisuje polecenia. Polecenie dostępu odnajduje panel, a diagnostyka sprawdza konfigurację. Wymagany jest zaufany dostęp lokalny.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## Private Packagist

Jeśli organizacja używa Private Packagist, skonfiguruj repozytorium przed instalacją i zapisz dane Composer zgodnie z jej instrukcjami. Nigdy nie zapisuj tokenów w Git.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
