# Eltávolítás és adatok

[Dokumentáció tartalma](README.md)

Előkészíti a Guardianhoz rendelhető generált fájlok eltávolítását. Megőrzi a fiókokat, beállításokat, naplókat és kulcsot. Állítsa le a munkafolyamatokat, futtassa composer remove előtt, törölje a dev gyorsítótárát, majd indítsa újra. Az egyéni és bizonytalan eredetű fájlokat vizsgálja meg.

--purge adattörlést kér, alapértelmezett elutasítással. --force csak --purge mellett érvényes, és megerősíti a törlést; --no-interaction esetén kötelező. Külső tárolók, hivatkozások és ismeretlen fájlok megakadályozhatják a teljes törlést. Őrizze meg a szükséges kulcsokat és ellenőrizze az eredményt.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Tárolót és kulcsot együtt mentse, próbálja a visszaállítást. Elveszett kulcs nem generálódik újra automatikusan. Eltávolítás után a profilert csak privát környezetben ellenőrizze. Éles környezet: APP_DEBUG=0, WebProfilerBundle és gyűjtés kikapcsolva. Helyi auditlánc nem bizonyít sértetlenséget feltört gépen.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Parancsreferencia

--purge adattörlést kér, alapértelmezett elutasítással. --force csak --purge mellett érvényes, és megerősíti a törlést; --no-interaction esetén kötelező. Külső tárolók, hivatkozások és ismeretlen fájlok megakadályozhatják a teljes törlést. Őrizze meg a szükséges kulcsokat és ellenőrizze az eredményt.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Parancsreferencia](commands.md)


Ha Composer már eltávolította a csomagot, ideiglenesen ugyanazt a verziót telepítse setup nélkül. Futtassa guardian:uninstall, majd távolítsa el újra és törölje a gyorsítótárat. Egyéni konfigurációkat kézzel ellenőrizzen.
