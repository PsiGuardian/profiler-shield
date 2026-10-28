# Dvoufaktorové ověření

[Obsah dokumentace](README.md)


## Dvoufaktorové ověření

Chraňte účet Guardian dočasnými kódy bez externích QR služeb.

Každá osoba si zaregistruje vlastní TOTP Tajné a potvrzuje kód. požadavek MFA bloky přístupu profilů pro nezapsané účty. QR Kód zůstává lokální na portálu. plánovat administrativní zotavení před požadováním této ochrany pro celý tým.

## 1. Potvrďte totožnost

Zadejte stejné heslo jako při přihlášení ke Guardian. Bude vyžadováno jen jednou.

## 2. Naskenujte QR kód autentizační aplikací

Guardian vytváří QR místně bez požadavků na externí služby.

## 3. Potvrďte šestimístný kód

Zadejte kód z aplikace. Další heslo není nutné.

## Soukromí: nezadávejte URL webu

Pokud aplikace žádá název či web, uveďte jen „ΩGuardian“ nebo neutrální název. Nezadávejte URL instance Symfony. Nepoužívejte online QR generátory: získaly by váš tajný klíč.

Uchovejte jej v tajnosti: lze z něj vytvářet kódy TOTP. Nesdílejte jej ani nepořizujte snímky.

Dočasný klíč vyprší za 10 minut nebo po 5 chybných kódech; pak začněte znovu. Chybný kód zachová formulář bez dalšího hesla. Potvrzovací čítač se spotřebuje proti opakování. SVG QR používá bacon/bacon-qr-code a ext-iconv bez GD, XMLWriter či sítě. Ruční zadání zůstává. URI otpauth používá neutrálního poskytovatele a účet, ne doménu webu. Tajemství ani soukromý URL nesdílejte.

## Recyklace a ověření

Odstraní tajný klíč a čítač TOTP účtu username, zruší relace a zaznamená akci. Zaregistrujte nový autentifikátor; globální požadavek MFA zůstává a může blokovat profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
