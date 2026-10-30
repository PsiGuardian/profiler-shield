# Oninstallatie en gegevens

[Documentatie-inhoud](README.md)

Bereidt verwijdering van aantoonbaar door Guardian gemaakte bestanden voor. Behoudt accounts, instellingen, logboeken en sleutel. Stop workers, voer uit vóór composer remove, wis de dev-cache en herstart. Controleer aangepaste bestanden en bestanden zonder herkomstbewijs.

--purge vraagt gegevensverwijdering met standaard Nee. --force vereist --purge en bevestigt vernietiging; verplicht bij --no-interaction. Externe kluizen, koppelingen en onbekende bestanden kunnen volledige verwijdering verhinderen. Bewaar benodigde sleutels en controleer het resultaat.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Maak samen een back-up van kluis en sleutel en test herstel. Verloren sleutels worden nooit automatisch hermaakt. Controleer na verwijdering de profiler alleen privé. Productie: APP_DEBUG=0, WebProfilerBundle en verzameling uit. Een lokale auditketen bewijst geen integriteit op een gecompromitteerde host.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Commando-overzicht

--purge vraagt gegevensverwijdering met standaard Nee. --force vereist --purge en bevestigt vernietiging; verplicht bij --no-interaction. Externe kluizen, koppelingen en onbekende bestanden kunnen volledige verwijdering verhinderen. Bewaar benodigde sleutels en controleer het resultaat.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Commando-overzicht](commands.md)


Is het pakket al verwijderd, installeer tijdelijk dezelfde versie zonder setup. Voer guardian:uninstall uit, verwijder opnieuw en wis de cache. Controleer aangepaste configuraties handmatig.
