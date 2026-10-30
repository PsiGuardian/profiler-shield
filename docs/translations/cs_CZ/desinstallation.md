# Odinstalace a data

[Obsah dokumentace](README.md)

Připraví odstranění souborů prokazatelně vytvořených Guardianem. Zachová účty, nastavení, protokoly a klíč. Zastavte procesy, spusťte před composer remove, vymažte dev cache a restartujte. Upravené soubory a nejasný původ zkontrolujte.

--purge žádá smazání dat s výchozím zamítnutím. --force vyžaduje --purge a potvrzuje zničení; nutné při --no-interaction. Externí úložiště, odkazy a neznámé soubory mohou zabránit úplnému smazání. Potřebné klíče zachovejte a ověřte výsledek.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Zálohujte úložiště a klíč společně a vyzkoušejte obnovu. Ztracené klíče se automaticky neobnovují. Po odstranění kontrolujte profiler jen neveřejně. Produkce: APP_DEBUG=0, WebProfilerBundle a sběr vypnuté. Místní řetězec auditu nedokazuje integritu napadeného hostitele.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Přehled příkazů

--purge žádá smazání dat s výchozím zamítnutím. --force vyžaduje --purge a potvrzuje zničení; nutné při --no-interaction. Externí úložiště, odkazy a neznámé soubory mohou zabránit úplnému smazání. Potřebné klíče zachovejte a ověřte výsledek.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Přehled příkazů](commands.md)


Pokud Composer balíček už odebral, dočasně nainstalujte stejnou verzi bez setup. Spusťte guardian:uninstall, znovu odeberte a vymažte cache. Vlastní nastavení zkontrolujte ručně.
