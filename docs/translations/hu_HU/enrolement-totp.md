# Kétfaktoros hitelesítés

[Dokumentáció tartalma](README.md)


## Kétfaktoros hitelesítés

Minden egyes személy saját TOTP titkos és megerősíti a kódot.

Minden egyes személy saját TOTP titkos és megerősíti a kódot. kérés MFA blokkolja a profilokhoz való hozzáférést regisztrálatlan fiókok számára. Válassza a kézi beállítást és írja be a megjelenített kulcsot. Használjon SHA-1-et, hat számjegyet és 30 másodperces időszakot.

## 1. Személyazonosság megerősítése

Adja meg a Guardian-bejelentkezéshez használt jelszavát. A beállítás során csak egyszer kérjük.

## 2. Írja be a kulcsot a hitelesítő alkalmazásba

Válassza a kézi beállítást és írja be a megjelenített kulcsot. Használjon SHA-1-et, hat számjegyet és 30 másodperces időszakot.

## 3. Hatjegyű kód megerősítése

Írja be az alkalmazás kódját. Második jelszó nem szükséges.

## Adatvédelem: ne adja meg a webhely URL-jét

Ha az alkalmazás nevet vagy webhelyet kér, csak „ΩGuardian”-t vagy semleges nevet adjon meg. Ne adja meg Symfony-példányának URL-jét.

Tartsa titokban: ezzel TOTP-kódok generálhatók. Ne ossza meg és ne készítsen képernyőképet.

Az ideiglenes titok 10 perc vagy 5 hibás kód után lejár; ekkor kezdje újra. Hibás kódnál megmarad az űrlap új jelszókérés nélkül. A megerősítési számláló felhasználódik az ismétlés ellen.

## helyreállítás és ellenőrzés

Törli username TOTP-titkát és számlálóját, visszavonja munkameneteit és naplóz. Regisztráljon új hitelesítőt; a globális MFA-követelmény továbbra is blokkolhatja a profilert.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
