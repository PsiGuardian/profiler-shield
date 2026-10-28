# ΩGuardian Profiler Shield — Telepítés és használat

PHP 8.3 vagy újabb; Symfony 5.4, 6.4 vagy 7.4. A Symfony 8.1 használatához PHP 8.4 vagy újabb kell. Composer és OpenSSL + Tokenizer-bővítmény szükséges. A profilerhez telepítse a WebProfilerBundle csomagot.

## 1. Telepítés

Futtassa a composer.json és bin/console fájlokat tartalmazó Symfony-alkalmazás gyökérkönyvtárában.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Beállítás

```bash
php vendor/bin/guardian setup
```

A segéd bekapcsolja a bundle-t dev/test környezetben, előkészíti a tárolót és az adminisztrátori fiókot, törli a gyorsítótárat és ellenőrzi a beállításokat. Csak sikeres lépés után folytassa. A művelet alatt az alkalmazás maradjon privát. Az ismételt futtatás megőrzi a fiókokat és kulcsokat.

## 3. A panel megnyitása

```bash
php bin/console guardian:access --env=dev
```

A parancs kiírja a privát útvonalat. A https://VOTRE-HOTE helyére az alkalmazás valódi HTTPS-címe kerüljön, porttal együtt. A belépési adatokat a .guardian-shield/initial-admin-credentials.txt fájlban vagy a jelzett tárolóban találja. Az első belépéskor jelszót kell változtatni. Ne ossza meg a fájlt.

## 4. Eltávolítás

Állítsa le a munkafolyamatokat, majd sorrendben futtassa a parancsokat, ellenőrizve mindegyik sikerét. A tároló és a kulcs megmarad. Indítsa újra a folyamatokat, és privát környezetben ellenőrizze a profilert.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

A fiókok és beállítások törléséhez az első parancsot cserélje az alábbira, és erősítse meg. Elutasításkor semmi nem változik. A külső tárolókat és egyéni fájlokat kézzel ellenőrizze; a megtartott tárolók kulcsait őrizze meg.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. CLI

A súgó ismerteti a parancsokat. A hozzáférési parancs megkeresi a panelt; a diagnosztika ellenőrzi a beállításokat. Megbízható helyi hozzáférés szükséges.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## Private Packagist

Private Packagist használata esetén telepítés előtt állítsa be ezt a tárolót, és a szervezet előírásai szerint mentse a Composer hitelesítő adatait. Tokent soha ne tegyen Gitbe.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
