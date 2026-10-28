# ΩGuardian Profiler Shield — Instalace a použití

PHP 8.3 nebo novější; Symfony 5.4, 6.4 nebo 7.4. Symfony 8.1 vyžaduje PHP 8.4 nebo novější. Je nutný Composer a rozšíření OpenSSL + Tokenizer. Pro profiler nainstalujte WebProfilerBundle.

## 1. Instalace

Spouštějte z kořenového adresáře aplikace Symfony se soubory composer.json a bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Nastavení

```bash
php vendor/bin/guardian setup
```

Průvodce aktivuje bundle v dev/test, připraví úložiště a účet správce, vymaže cache a ověří nastavení. Pokračujte až po úspěchu každého kroku. Během nastavení ponechte aplikaci neveřejnou. Opakované spuštění zachová účty a klíče.

## 3. Otevření panelu

```bash
php bin/console guardian:access --env=dev
```

Příkaz vypíše soukromou cestu. Nahraďte https://VOTRE-HOTE skutečnou HTTPS adresou včetně portu. Přihlašovací údaje najdete v .guardian-shield/initial-admin-credentials.txt nebo v uvedeném úložišti. První přihlášení vyžaduje změnu hesla. Soubor nesdílejte.

## 4. Odinstalace

Zastavte pracovní procesy a spouštějte příkazy postupně, vždy po úspěchu předchozího. Úložiště a klíč zůstanou zachovány. Procesy restartujte a profiler ověřte v neveřejném prostředí.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Chcete-li odstranit i účty a nastavení, nahraďte první příkaz následujícím a potvrďte smazání. Odmítnutí nic nezmění. Externí úložiště a upravené soubory zkontrolujte ručně; potřebné klíče ponechte.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. CLI

Nápověda popisuje příkazy. Příkaz přístupu najde panel; diagnostika ověří nastavení. Je nutný důvěryhodný místní přístup.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## Private Packagist

Pokud organizace používá Private Packagist, nastavte tento repozitář před instalací a uložte údaje Composer podle jejích pokynů. Tokeny nikdy neukládejte do Gitu.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
