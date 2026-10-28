# ΩGuardian Profiler Shield — Installation and usage

PHP 8.3 or later; Symfony 5.4, 6.4 or 7.4. Symfony 8.1 requires PHP 8.4 or later. Composer and the OpenSSL + Tokenizer extension are required. Install WebProfilerBundle to use the profiler.

## 1. Install

Run from the Symfony application root containing composer.json and bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Configure

```bash
php vendor/bin/guardian setup
```

The assistant enables the bundle in dev/test, prepares the vault and administrator account, clears the cache and checks configuration. Each step must succeed before the next. Keep the application private during setup. Running it again preserves existing accounts and keys.

## 3. Open the panel

```bash
php bin/console guardian:access --env=dev
```

The command prints the private panel path. Replace https://VOTRE-HOTE with the application’s actual HTTPS origin, including its port. Read the credentials in .guardian-shield/initial-admin-credentials.txt or the indicated vault. The first login requires a password change. Do not share this file.

## 4. Uninstall

Stop workers, then run the commands in order, continuing only after each succeeds. The vault and its key are retained. Restart workers and check the profiler in a private environment.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

To delete accounts and settings as well, replace the first command with the following and confirm deletion. Declining makes no changes. Review external vaults and customized files manually; retain the key for any vault you keep.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Command reference

Help describes the commands. The access command locates the panel; diagnostics check configuration. These operations require trusted local access.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Command reference](../translations/en_US/commands.md) · [Documentation contents](../translations/en_US/README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

For an organization using Private Packagist, configure this repository before installation and save Composer credentials following your organization’s instructions. Never commit a token to Git.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
