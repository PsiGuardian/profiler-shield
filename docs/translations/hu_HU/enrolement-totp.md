# Kétfaktoros hitelesítés

[Dokumentáció tartalma](README.md)


## Kétfaktoros hitelesítés

Védje Guardian-fiókját ideiglenes kódokkal, külső QR-szolgáltatások nélkül.

Minden egyes személy saját TOTP titkos és megerősíti a kódot. kérés MFA blokkolja a profilokhoz való hozzáférést regisztrálatlan fiókok számára. QR Az adminisztratív helyreállítás megtervezése, mielőtt ezt a védelmet az egész csapat számára igényli.

## 1. Személyazonosság megerősítése

Adja meg a Guardian-bejelentkezéshez használt jelszavát. A beállítás során csak egyszer kérjük.

## 2. Olvassa be a QR-kódot hitelesítő alkalmazásával

A Guardian helyben generálja a QR-kódot, külső szolgáltatás nélkül.

## 3. Hatjegyű kód megerősítése

Írja be az alkalmazás kódját. Második jelszó nem szükséges.

## Adatvédelem: ne adja meg a webhely URL-jét

Ha az alkalmazás nevet vagy webhelyet kér, csak „ΩGuardian”-t vagy semleges nevet adjon meg. Ne adja meg Symfony-példányának URL-jét. Online QR-generátor használata tilos: megkapná a titkát.

Tartsa titokban: ezzel TOTP-kódok generálhatók. Ne ossza meg és ne készítsen képernyőképet.

Az ideiglenes titok 10 perc vagy 5 hibás kód után lejár; ekkor kezdje újra. Hibás kódnál megmarad az űrlap új jelszókérés nélkül. A megerősítési számláló felhasználódik az ismétlés ellen. SVG QR: bacon/bacon-qr-code és ext-iconv, GD, XMLWriter és hálózat nélkül. Kézi bevitel is elérhető. Az otpauth URI semleges szolgáltatót és fiókot tartalmaz, nem a webhely tartományát. Ne osszon meg titkot vagy privát URL-t.

## helyreállítás és ellenőrzés

Törli username TOTP-titkát és számlálóját, visszavonja munkameneteit és naplóz. Regisztráljon új hitelesítőt; a globális MFA-követelmény továbbra is blokkolhatja a profilert.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
