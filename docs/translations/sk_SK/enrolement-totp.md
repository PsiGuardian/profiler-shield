# Dvojfaktorové overenie

[Obsah dokumentácie](README.md)


## Dvojfaktorové overenie

Chráňte účet Guardian dočasnými kódmi bez externých QR služieb.

Každá osoba zaregistruje svoju vlastnú TOTP tajné a potvrdzuje kód. žiadosť MFA bloky profilov prístup pre nezaregistrované účty. QR kód zostáva lokálne na portál. plán administratívne zotavenie pred vyžadovaním tejto ochrany pre celý tím.

## 1. Potvrďte totožnosť

Zadajte rovnaké heslo ako pri prihlásení do Guardian. Stačí ho zadať raz.

## 2. Naskenujte QR kód v autentifikačnej aplikácii

Guardian generuje QR lokálne bez požiadaviek na externé služby.

## 3. Potvrďte šesťmiestny kód

Zadajte kód z aplikácie. Druhé heslo nie je potrebné.

## Súkromie: nezadávajte URL webu

Ak aplikácia žiada názov alebo web, uveďte len „ΩGuardian“ alebo neutrálny názov. Nezadávajte URL inštancie Symfony. Nepoužívajte online QR generátory, získali by váš tajný kľúč.

Uchovajte ho v tajnosti: umožňuje generovať kódy TOTP. Nezdieľajte ho ani nerobte snímky obrazovky.

Dočasný kľúč vyprší za 10 minút alebo po 5 chybných kódoch; potom začnite znova. Chybný kód zachová formulár bez ďalšieho hesla. Potvrdzovacie počítadlo sa spotrebuje proti opakovaniu. SVG QR používa bacon/bacon-qr-code a ext-iconv bez GD, XMLWriter či siete. Ručné zadanie zostáva. URI otpauth používa neutrálneho poskytovateľa a účet, nie doménu webu. Tajomstvo ani súkromný URL nezdieľajte.

## Recyklácia a overovanie

Odstráni tajný kľúč a počítadlo TOTP účtu username, zruší relácie a zaznamená akciu. Zaregistrujte nový autentifikátor; globálna požiadavka MFA zostáva a môže blokovať profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
