# Uninstallation and data

[Documentation contents](README.md)

Prepares removal of generated files attributable to Guardian; retains accounts, settings, logs and key by default. Stop workers and run before composer remove, then clear the dev cache and restart. Customized files or files without ownership evidence require review.

--purge requests data deletion with a default-no confirmation. --force requires --purge and confirms destruction; required with --no-interaction for automated purging. External vaults, links and unknown files may prevent complete deletion: retain necessary keys and inspect the result.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Back up vault and key together and test restoration. Lost keys are never regenerated automatically. After removal, check the profiler only in a private environment. Production requires APP_DEBUG=0 with WebProfilerBundle and collection disabled. A local audit chain does not prove integrity on a compromised host.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Command reference

--purge requests data deletion with a default-no confirmation. --force requires --purge and confirms destruction; required with --no-interaction for automated purging. External vaults, links and unknown files may prevent complete deletion: retain necessary keys and inspect the result.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Command reference](commands.md)


If Composer has already removed the package, temporarily reinstall the same version without running setup. Run guardian:uninstall, remove the package again and clear the cache. Review customized configurations manually.
