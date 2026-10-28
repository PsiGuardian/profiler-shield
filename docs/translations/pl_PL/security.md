# Polityka bezpieczeństwa

[Spis dokumentacji](README.md)

Zgłaszaj podatności prywatnie do bundle-sec@omegaguardian.fr w celu uzgodnienia szyfrowanego kanału. Bez sekretów, aktywnych tokenów i prywatnych URL w pierwszej wiadomości. Podaj wersje Guardian, Symfony i PHP, wpływ, warunki i minimalne odtworzenie. Opiekunowie potwierdzają, badają, poprawiają prywatnie, testują i koordynują komunikat i poprawkę.

Nigdy nie wysyłaj e-mailem haseł, sekretów TOTP, kluczy szyfrowania ani adresów URL diagnostyki.

Kopiuj magazyn i klucz razem i testuj odtworzenie. Utracone klucze nie są automatycznie regenerowane. Po usunięciu sprawdzaj profiler tylko prywatnie. Produkcja: APP_DEBUG=0, WebProfilerBundle i zbieranie wyłączone. Lokalny łańcuch audytu nie dowodzi integralności przejętego hosta.
