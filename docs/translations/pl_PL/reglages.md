# Ustawienia

[Spis dokumentacji](README.md)


## Dodatkowe zabezpieczenia

Zabezpieczenia są pogrupowane z ustawieniami. Wyłączone opcje są wyszarzone, a wartości zachowane.

Guardian kontroluje profil i pasek narzędzi debug. Konto autoryzowanego administratora lub deweloperów jest wymagane. MFAi harmonogramów, JIT, ograniczenia sieciowe i zamknięcie mogą nadal odmówić dostępu. tryb ukrywa punkty wejściowe; nie zastępuje tych kontrol.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Profil | Ustawienia |
|---|---|
| Solo · elastyczne | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| Zespół · Wzmocniony | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| wrażliwy · ograniczony | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Parametr | Ustawienia | Wartość początkowa |
|---|---|---|
| `stealth` | Zwracaj 404 zamiast 403 na niedozwolonych trasach diagnostycznych. | `true` |
| `mfa` | Wszyscy użytkownicy muszą skonfigurować TOTP przed włączeniem wymogu. | `false` |
| `idle_minutes` | Minuty bez aktywności do wygaśnięcia sesji. | `60` |
| `absolute_hours` | Godziny, niezależnie od aktywności. | `8` |
| `remember_days` | Dni. Nie zastępuje logowania ani harmonogramu pracy. | `14` |
| `schedule_enabled` | Wybierz czas dostępności diagnostyki. Strony aplikacji pozostają dostępne. | `false` |
| `weekdays` | Wybierz dni, w których diagnostyka jest dozwolona. | `1,2,3,4,5` |
| `start` | Otwarcie | `09:00` |
| `end` | Zamknięcie | `18:00` |
| `timezone` | Strefa jest odczytywana z procesu PHP obsługującego tę stronę. PHP CLI, PHP-FPM i system operacyjny mogą mieć różne strefy czasowe. Sprawdź konfigurację serwera przed zapisaniem. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | Prywatny adres URL zwraca 404 poza godzinami pracy. Odzyskiwanie tylko lokalnie przez CLI. | `false` |
| `jit_enabled` | Otwieraj profiler tylko podczas żądanego okna diagnostycznego. | `false` |
| `jit_minutes` | Minuty, używane tylko po włączeniu. | `30` |
| `ip_allowlist` | Uwaga: błąd może zablokować dostęp. Odzyskiwanie lokalne: guardian:policy:reset. | `∅` |
| `audit_level` | Wybierz szczegółowość audytu i czas przechowywania lokalnego. | `standard` |
| `audit_retention_days` | Dni przed wyczyszczeniem dziennika. | `30` |
| `alert_email` | Odbiera alerty awaryjne. Dostęp nie może zależeć od dostarczania e-maili. | `∅` |
| `emergency_enabled` | Zezwól na wyjątek na 30 minut z kodem jednorazowym i audytem. | `false` |
| `login_rules` | Podstawowe → 10 niepowodzenia, a następnie 10Konty mają zastosowanie zarówno do konta, jak i pochodzenia sieciowego, w tym potwierdzeń hasła. | `10 / 10 / 30 / 30` |
| `password_policy` | Niniejsze zasady mają zastosowanie do tworzenia i zmiany hasła, w Internecie oraz w CLINie ujawniają ani nie sprawdzają istniejących hasła. | `min_length=15` |
| `error_mode` | Strony aplikacyjne: zachowuje istniejące strony 404 Odpowiedzi; wyjątki wykorzystują 404 szablon, gdy jest dostępny, w przeciwnym razie neutralna strona. Neutralne: standardowe wiadomości. Dostosowane: używa tekstu. | `native` |
| `error_reference` | Odniesienie ma zastosowanie do GuardianStrony neutralne: rzadko generuje nowy kod, a nie rzadkie wiadomość. | `none` |
| `error_reference_text` | Dostępne tylko z przydzielonym statycznym odniesieniem. | `∅` |
| `error_custom_text` | Dostępne tylko dla własnego komunikatu neutralnego. Tekst pozostaje zapisany po zmianie trybu. | `∅ → custom` |

Umożliwia zaplanowanie, wybrane dni, włączone godziny rozpoczęcia i ekskluzywny czas końcowy. strefa czasu przechowywana określa lokalne godziny. przerwy wieczorne nie są akceptowane. Ukrywanie portalu poza godzinami uniemożliwia zdalny dostęp do sytuacji awaryjnej; utrzymanie lokalnych poleceń odzyskania.

Idle timeout, absolutna żywotność i pamięć urządzenia służą różnym celom. Pamięć o urządzeniu nie przebija harmonogramów lub JITOdwołanie kończy odpowiednie sesje na serwerze.Użyj indywidualnych kont, aby przypisać działania w dzienniku.

JIT wymaga aktywacji okno tymczasowe z upoważnionej sesji. dostęp awaryjny, gdy jest włączony, wymaga hasła, kodu jednorazowego użytkowania i przyczyny. nie przechodzi globalnego zamknięcia. lokalne polecenia pozostają niezbędne, jeśli portal jest niedostępny.

IP dopuszczalnik akceptuje IPv4, IPv6 i CIDR Źródła - sprawdź Symfony zaufana konfiguracja proxy przed umożliwieniem. użyj HTTPS Dla bezpiecznych plików cookie. Guardian nie naprawia wystawionych serwerów internetowych lub baz danych.

poziom i utrzymanie określa dostępne wydarzenia, aż do 5000 wpisy. filtracja według typu i konta przed zmianą stron. ostrzeżenia są przekazywane; skonfigurować ich transport i dostawę. lokalny łańcuch hash nie gwarantuje integralności zagrożonego hostingu.

Określ minimalną długość i wymagane kategorie w połączeniach i hasłach. znaky tworzenia konta opisują zastrzeżoną politykę. Zmiany mają zastosowanie do nowych hasła; wybierz obowiązkowe odnowienie istniejących kont. Guardian Wybór Argon2id Kiedy PHP Zapewniamy, że inaczej bcrypt.

Filtruj wszystkie zachowane wydarzenia według typu i konta. Najnowsze wydarzenia pojawiają się najpierw, na stronach 50Scrolling nie ładuje następnej strony.

## Środowisko


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


Proxy obsługuje TLS, normalizację i ochronę przed dużym ruchem. Brama sprawdza diagnostykę przed routerem; trasy biznesowe nie czytają kont ani sesji. Tożsamości Guardian są oddzielne od Symfony. Kopiuj prywatny magazyn i klucz razem poza katalogiem publicznym. Przejęcie PHP lub hosta może ujawnić oba. Magazyn jest dla jednego hosta. Błędy startu, cache lub proxy mogą dotyczyć całej aplikacji.

Kopiuj magazyn i klucz razem i testuj odtworzenie. Utracone klucze nie są automatycznie regenerowane. Po usunięciu sprawdzaj profiler tylko prywatnie. Produkcja: APP_DEBUG=0, WebProfilerBundle i zbieranie wyłączone. Lokalny łańcuch audytu nie dowodzi integralności przejętego hosta.

## Odzyskiwanie i weryfikacja

Czyści listę IP i wyłącza harmonogram, JIT i zdalny dostęp awaryjny. Uwierzytelnianie, MFA, blokada globalna i blokady logowania pozostają. Po odzyskaniu dostępu ustaw ponownie potrzebne ograniczenia.

on zamyka diagnostykę i usuwa wszystkie sesje; strony biznesowe działają dalej. off usuwa tylko blokadę globalną: potrzebne jest ponowne logowanie, a inne polityki pozostają.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
