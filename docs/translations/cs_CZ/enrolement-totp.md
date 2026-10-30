# Dvoufaktorové ověření

[Obsah dokumentace](README.md)


## Dvoufaktorové ověření

Každá osoba si zaregistruje vlastní TOTP Tajné a potvrzuje kód.

Každá osoba si zaregistruje vlastní TOTP Tajné a potvrzuje kód. požadavek MFA bloky přístupu profilů pro nezapsané účty. plánovat administrativní zotavení před požadováním této ochrany pro celý tým. Zvolte ruční nastavení a zadejte zobrazený klíč. Použijte SHA-1, šest číslic a interval 30 sekund.

## 1. Potvrďte totožnost

Zadejte stejné heslo jako při přihlášení ke Guardian. Bude vyžadováno jen jednou.

## 2. Zadejte klíč do autentizační aplikace

Zvolte ruční nastavení a zadejte zobrazený klíč. Použijte SHA-1, šest číslic a interval 30 sekund.

## 3. Potvrďte šestimístný kód

Zadejte kód z aplikace. Další heslo není nutné.

## Soukromí: nezadávejte URL webu

Pokud aplikace žádá název či web, uveďte jen „ΩGuardian“ nebo neutrální název. Nezadávejte URL instance Symfony.

Uchovejte jej v tajnosti: lze z něj vytvářet kódy TOTP. Nesdílejte jej ani nepořizujte snímky.

Dočasný klíč vyprší za 10 minut nebo po 5 chybných kódech; pak začněte znovu. Chybný kód zachová formulář bez dalšího hesla. Potvrzovací čítač se spotřebuje proti opakování.

## Recyklace a ověření

Odstraní tajný klíč a čítač TOTP účtu username, zruší relace a zaznamená akci. Zaregistrujte nový autentifikátor; globální požadavek MFA zůstává a může blokovat profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
