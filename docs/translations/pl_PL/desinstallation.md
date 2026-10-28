# Odinstalowanie i dane

[Spis dokumentacji](README.md)

Przygotowuje usunięcie plików przypisanych do Guardian. Domyślnie zachowuje konta, ustawienia, logi i klucz. Zatrzymaj procesy robocze, wykonaj przed composer remove, wyczyść cache dev i uruchom je ponownie. Sprawdź zmodyfikowane pliki i te bez dowodu pochodzenia.

--purge żąda usunięcia danych z domyślną odmową potwierdzenia. --force wymaga --purge i potwierdza zniszczenie; konieczne z --no-interaction. Zewnętrzne magazyny, dowiązania i nieznane pliki mogą uniemożliwić pełne usunięcie. Zachowaj potrzebne klucze i sprawdź wynik.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Kopiuj magazyn i klucz razem i testuj odtworzenie. Utracone klucze nie są automatycznie regenerowane. Po usunięciu sprawdzaj profiler tylko prywatnie. Produkcja: APP_DEBUG=0, WebProfilerBundle i zbieranie wyłączone. Lokalny łańcuch audytu nie dowodzi integralności przejętego hosta.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Spis poleceń

--purge żąda usunięcia danych z domyślną odmową potwierdzenia. --force wymaga --purge i potwierdza zniszczenie; konieczne z --no-interaction. Zewnętrzne magazyny, dowiązania i nieznane pliki mogą uniemożliwić pełne usunięcie. Zachowaj potrzebne klucze i sprawdź wynik.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Spis poleceń](commands.md)


Jeśli Composer usunął już pakiet, zainstaluj tymczasowo tę samą wersję bez setup. Uruchom guardian:uninstall, usuń pakiet ponownie i wyczyść cache. Sprawdź ręcznie własne konfiguracje.
