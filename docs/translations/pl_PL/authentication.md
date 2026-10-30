# Sign-ins i hasła

[Spis dokumentacji](README.md)


## Konta i pierwsze podpisy

Twój pierwotny hasło jest tymczasowy. Wybierz nową tajemnicę: panel i diagnostyki pozostają zamknięte, aż to zrobisz.

Guardian Konta są oddzielone od kont witryny. Konto początkowe i nowe konta muszą zmienić swoje hasła przed kontynuowaniem. Rola bezpieczeństwa widok pozwala na informacje bez dostępu do profili. Zmiana roli lub hasła zakazuje poprzednich sesji.

Przygotowuje prywatną ścieżkę, klucz, magazyn, pliki Symfony i administratora, jeśli go brakuje. Zachowuje istniejące konta. Odrzuca produkcję. Po bezpośrednim uruchomieniu wyczyść cache i wykonaj kontrole.


```bash
php bin/console guardian:access --env=dev
```


| zastrzeżone wymagania hasła | Minimalna długość (Unicode Charaktery ) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Polityka hasła

Niniejsze zasady mają zastosowanie do tworzenia i zmiany hasła, w Internecie oraz w CLINie ujawniają ani nie sprawdzają istniejących hasła.

Wartość początkowa : 15 Użyj unikalnej hasła lub menedżera hasła. wymagane kategorie znaków nie gwarantują nieprzewidywalnego hasła

Na tym serwerze, Guardian automatycznie wykorzystuje Argon2id dla nowych hasła. trzymaj się PHP Wniosek o udzielenie zgody: limit aplikacji 512 UTF-8 i byty.

Tego PHP Serwer nie dostarcza Argon2id. Guardian Dlatego używa bcrypt i odrzuca hasła przekraczające 72 UTF-8 Pytanie administratora systemu, aby umożliwić Argon2id Wsparcie w PHP.

Opcja ta natychmiast odwołuje istniejące sesje, w tym Twoje. Konta muszą zmienić swoje hasła przed konfiguracją Guardian lub otwieraniem diagnostyki.

Zmienia hasło istniejącego konta username przez ukryte wprowadzanie zgodne z polityką, unieważnia wszystkie sesje i zapisuje zdarzenie. Nie podawaj hasła jako argumentu.

## Progresywne blokady

Każda faza określa limit niepowodzenia, liczenie okna, czas trwania zamknięcia i powtarzanie okna. 10 Niepowodzenia powodują A 10-minute lockout. powtarzane błędy mogą wywołać następny etap. czeki obejmują zarówno konto i pochodzenie sieci, w tym potwierdzenia administratora.

Szyfrowane liczniki są niezależne dla znormalizowanego konta i źródła; surowe IP nie są zapisywane. Rezerwacja atomowa poprzedza haszowanie i wygasa po 120 sekundach. Porzucone rezerwacje są błędami; późne wyniki nie tworzą sesji. Błędny CSRF nic nie rezerwuje. Udane logowanie zeruje błędy konta, zachowując historię ponowień i źródła. Przy 10000 aktywnych wpisach odrzucane są nowe tożsamości zamiast usuwania blokad.

Etapy są wykonywane w porządku. po zamknięciu, nowe niepowodzenia w oknie powtarzania używają następnego etapu. poza tym oknem pierwszy etap jest ponownie stosowany. ostatni etap powtarza się. blokowane żądania nie rozszerzają opóźnienia. maksymalny 8 Stacje rehabilitacji lokalnej: guardian:login:unlock --all.

| Etap | zastrzeżone wymagania hasła |
|---|---|
| Niepowodzenia przed zamknięciem | 1–100 |
| Liczba minut (minuty) | 1–10080 |
| Liczba okien (minuty) | 1–1440 |
| Powtórz okno po odblokowaniu (minuty) | 1–10080 |
| Etap | 1–8; block[n+1] ≥ block[n] |
| Wartość początkowa | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Wyczerpujące wyszukiwanie kosztów laboratorium

Porównaj równomiernie przypadkowe tajemnice stałej długości bez wprowadzania prawdziwego hasła. stopy oceny i ceny sprzętu są przypuszczenia dostosowywane.

Rozłączne kategorie ASCII i BigInt. Zasada włączeń-wyłączeń oblicza dokładną przestrzeń; brak kategorii lub zbyt mała długość daje zero. Średnia pozycja bez zwracania: (N+1)/2. Oczekiwane opóźnienie uwzględnia progi i powtarzany ostatni etap. Gdy tempo nie mieści się w oknach, szacunek jest wyłączony. Koszt offline to godziny razy założona cena godzinowa.

Z ustalonym alfabetem, wymaganie kategorii zmniejsza liczbę tajemnic zgodnych. Rozszerzenie alfabetu jest inną operacją. Czasy blokowania przejmują ciągłe przypuszczenia wystarczająco szybko, aby pozostać w konfigurowanych oknach.

Ten model nie mierzy siły wybranego przez człowieka hasła. wyklucza słownictwo, ponowne wykorzystanie, ucieczki i ukierunkowane strategie. blokady internetowe nie chronią skradzionych hashów. początkowa stawka offline jest ilustracją, a nie referencją. CPU Środki pomiarowe : guardian:password:benchmark.

Wielkie liczby są napisane w całości, z grupowaniem cyfrowym i bez notowania E. Czasy i koszty są okrągłe. Porównaj tę samą kolejkę za pomocą tych samych przypuszczeń.

| Dostępne symulacje alfabetu | Potencjalne tajemnice |
|---|---|
| ASCII Niższe przypadki | 26 |
| ASCII Wysokość | 26 |
| ASCII cyfry | 10 |
| ASCII Symbole wykluczające przestrzeń | 32 |
| Podejmowane stawki w Internecie (guesses/s) | 1 / s |
| Podejmowana stawka offline (hashes/s) | 10000 / s |
| Koszty sprzętu (€/godzinny) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## Odzyskiwanie i weryfikacja

Kopiuj magazyn i klucz razem i testuj odtworzenie. Utracone klucze nie są automatycznie regenerowane. Po usunięciu sprawdzaj profiler tylko prywatnie. Produkcja: APP_DEBUG=0, WebProfilerBundle i zbieranie wyłączone. Lokalny łańcuch audytu nie dowodzi integralności przejętego hosta.

Zeruje liczniki konta username. --ip ADRES usuwa też licznik źródła IPv4/IPv6; inaczej blokada sieci może pozostać. --all usuwa wszystkie liczniki. Konta, hasła i sesje się nie zmieniają; operacja jest rejestrowana.

[Spis poleceń](commands.md)
