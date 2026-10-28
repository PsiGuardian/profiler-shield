# Tłumaczenia

[Spis dokumentacji](README.md)

Portal oferuje 28 wariantów regionalnych i 25 katalogów; cztery angielskie warianty dzielą katalog. Teksty statyczne są tłumaczone i escapowane. Nazwy użytkowników, własne komunikaty i dane użytkownika nie są tłumaczone. Zdalne usługi nie otrzymują tekstu. Audyt sprawdza klucze, parametry, UTF-8, polecenia i linki; testy HTTP odwiedzają widoki każdego wariantu.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
