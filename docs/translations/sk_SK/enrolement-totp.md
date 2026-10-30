# Dvojfaktorové overenie

[Obsah dokumentácie](README.md)


## Dvojfaktorové overenie

Každá osoba zaregistruje svoju vlastnú TOTP tajné a potvrdzuje kód.

Každá osoba zaregistruje svoju vlastnú TOTP tajné a potvrdzuje kód. žiadosť MFA bloky profilov prístup pre nezaregistrované účty. plán administratívne zotavenie pred vyžadovaním tejto ochrany pre celý tím. Zvoľte ručné nastavenie a zadajte zobrazený kľúč. Použite SHA-1, šesť číslic a interval 30 sekúnd.

## 1. Potvrďte totožnosť

Zadajte rovnaké heslo ako pri prihlásení do Guardian. Stačí ho zadať raz.

## 2. Zadajte kľúč do autentifikačnej aplikácie

Zvoľte ručné nastavenie a zadajte zobrazený kľúč. Použite SHA-1, šesť číslic a interval 30 sekúnd.

## 3. Potvrďte šesťmiestny kód

Zadajte kód z aplikácie. Druhé heslo nie je potrebné.

## Súkromie: nezadávajte URL webu

Ak aplikácia žiada názov alebo web, uveďte len „ΩGuardian“ alebo neutrálny názov. Nezadávajte URL inštancie Symfony.

Uchovajte ho v tajnosti: umožňuje generovať kódy TOTP. Nezdieľajte ho ani nerobte snímky obrazovky.

Dočasný kľúč vyprší za 10 minút alebo po 5 chybných kódoch; potom začnite znova. Chybný kód zachová formulár bez ďalšieho hesla. Potvrdzovacie počítadlo sa spotrebuje proti opakovaniu.

## Recyklácia a overovanie

Odstráni tajný kľúč a počítadlo TOTP účtu username, zruší relácie a zaznamená akciu. Zaregistrujte nový autentifikátor; globálna požiadavka MFA zostáva a môže blokovať profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
