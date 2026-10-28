# Odinštalácia a údaje

[Obsah dokumentácie](README.md)

Pripraví odstránenie súborov preukázateľne vytvorených Guardianom. Zachová účty, nastavenia, protokoly a kľúč. Zastavte procesy, spustite pred composer remove, vymažte dev cache a reštartujte. Upravené súbory a nejasný pôvod skontrolujte.

--purge žiada zmazanie dát s predvoleným odmietnutím. --force vyžaduje --purge a potvrdzuje zničenie; nutné pri --no-interaction. Externé úložiská, odkazy a neznáme súbory môžu zabrániť úplnému zmazaniu. Potrebné kľúče zachovajte a overte výsledok.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Zálohujte úložisko a kľúč spolu a vyskúšajte obnovu. Stratené kľúče sa automaticky neobnovujú. Po odstránení kontrolujte profiler len neverejne. Produkcia: APP_DEBUG=0, WebProfilerBundle a zber vypnuté. Miestny reťazec auditu nedokazuje integritu napadnutého hostiteľa.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Prehľad príkazov

--purge žiada zmazanie dát s predvoleným odmietnutím. --force vyžaduje --purge a potvrdzuje zničenie; nutné pri --no-interaction. Externé úložiská, odkazy a neznáme súbory môžu zabrániť úplnému zmazaniu. Potrebné kľúče zachovajte a overte výsledok.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Prehľad príkazov](commands.md)


Ak Composer balík už odobral, dočasne nainštalujte rovnakú verziu bez setup. Spustite guardian:uninstall, znova odoberte a vymažte cache. Vlastné nastavenia skontrolujte ručne.
