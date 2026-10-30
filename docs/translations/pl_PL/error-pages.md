# Publiczne strony błędu

[Spis dokumentacji](README.md)


## Publiczne strony błędu

Zachowaj strony aplikacji lub pokaż neutralny HTML. Rzeczywiste kody statusu HTTP pozostają bez zmian.

Rodzina mody pozwala Symfony W trybie ogólnym ignoruje skonsolidowany tekst. Tylko w trybu skonsolidu wykorzystuje wiadomość odwiedzającą. Publiczna referencja może być nieobecna, przypadkowa lub stała; jest niezależna od wiadomości. HTTP Kod statusu pozostaje niezmieniony.

Strony aplikacyjne: zachowuje istniejące strony 404 Odpowiedzi; wyjątki wykorzystują 404 szablon, gdy jest dostępny, w przeciwnym razie neutralna strona. Neutralne: standardowe wiadomości. Dostosowane: używa tekstu.

Anonimowe wyjątki 500 otrzymują neutralną stronę. Szczegóły wymagają uprawnionej tożsamości Guardian i GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Brak lub błąd szablonu Twig powoduje neutralny widok; własne kontrolery błędów wymagają integracji. JSON i poprawne strony biznesowe pozostają bez zmian.

| error_mode | Ustawienia |
|---|---|
| native | Strony aplikacyjne: zachowuje istniejące strony 404 Odpowiedzi; wyjątki wykorzystują 404 szablon, gdy jest dostępny, w przeciwnym razie neutralna strona. Neutralne: standardowe wiadomości. Dostosowane: używa tekstu. |
| generic | HTML 400–599 → Zachowaj strony aplikacji lub pokaż neutralny HTML. Rzeczywiste kody statusu HTTP pozostają bez zmian. |
| custom | Dostępne tylko dla własnego komunikatu neutralnego. Tekst pozostaje zapisany po zmianie trybu. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Dostępne tylko z przydzielonym statycznym odniesieniem. |
| error_custom_text | Dostępne tylko dla własnego komunikatu neutralnego. Tekst pozostaje zapisany po zmianie trybu. |

## Wyświetlany identyfikator

Różne komunikaty lub stałe identyfikatory mogą ujawnić rzeczywisty błąd. Nie należy fałszować kodów HTTP.

Odniesienie ma zastosowanie do GuardianStrony neutralne: rzadko generuje nowy kod, a nie rzadkie wiadomość.

Ustawienia są weryfikowane na serwerze przed zapisaniem.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Sieć i wdrażanie

IP dopuszczalnik akceptuje IPv4, IPv6 i CIDR Źródła - sprawdź Symfony zaufana konfiguracja proxy przed umożliwieniem. użyj HTTPS Dla bezpiecznych plików cookie. Guardian nie naprawia wystawionych serwerów internetowych lub baz danych.

Odmów diagnostyki, gdy nie da się sprawdzić tożsamości, roli, sesji, sieci, harmonogramu, JIT lub blokady. Przeglądarka nie ustala wygaśnięcia. Zbieranie i odczyt profili są oddzielne. Zapamiętanie urządzenia nie uwierzytelnia. Awaria wymaga tożsamości, zużytego kodu i trwałego audytu; krytyczny błąd audytu blokuje otwarcie. Nie stosuj częściowo błędnej konfiguracji. Opcjonalne kontrole nie wymagają zdalnej usługi.
