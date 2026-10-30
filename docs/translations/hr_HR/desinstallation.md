# Odlaganje i podaci

[Sadržaj dokumentacije](README.md)

Priprema uklanjanje generiranih datoteka pripisivih Guardianu. Čuva račune, postavke, zapisnike i ključ. Zaustavite procese, pokrenite prije composer remove, očistite dev predmemoriju i ponovno pokrenite. Pregledajte prilagođene datoteke i nepoznato podrijetlo.

--purge traži brisanje podataka sa zadanom odbijenicom. --force zahtijeva --purge i potvrđuje uništenje; obvezno uz --no-interaction. Vanjski trezori, poveznice i nepoznate datoteke mogu spriječiti potpuno brisanje. Sačuvajte potrebne ključeve i pregledajte rezultat.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Zajedno kopirajte trezor i ključ te testirajte obnovu. Izgubljeni ključevi ne stvaraju se automatski. Nakon uklanjanja provjerite profiler samo privatno. Produkcija: APP_DEBUG=0, WebProfilerBundle i prikupljanje isključeni. Lokalni auditni lanac ne dokazuje integritet kompromitiranog hosta.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Pregled naredbi

--purge traži brisanje podataka sa zadanom odbijenicom. --force zahtijeva --purge i potvrđuje uništenje; obvezno uz --no-interaction. Vanjski trezori, poveznice i nepoznate datoteke mogu spriječiti potpuno brisanje. Sačuvajte potrebne ključeve i pregledajte rezultat.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Pregled naredbi](commands.md)


Ako je Composer već uklonio paket, privremeno instalirajte istu verziju bez setup. Pokrenite guardian:uninstall, ponovno uklonite i očistite predmemoriju. Ručno pregledajte prilagođene postavke.
