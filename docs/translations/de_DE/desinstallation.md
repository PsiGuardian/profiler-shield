# Entfernung und Daten

[Dokumentationsübersicht](README.md)

Bereitet die Entfernung nachweislich von Guardian erzeugter Dateien vor. Konten, Einstellungen, Protokolle und Schlüssel bleiben standardmäßig erhalten. Worker stoppen, vor composer remove ausführen, danach dev-Cache leeren und Worker starten. Angepasste oder nicht zuordenbare Dateien prüfen.

--purge verlangt bestätigte Datenlöschung, standardmäßig Nein. --force ist nur mit --purge gültig und bestätigt die Löschung; bei --no-interaction zwingend. Externe Tresore, Links und unbekannte Dateien können vollständige Löschung verhindern. Erforderliche Schlüssel behalten und Ergebnis prüfen.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Tresor und Schlüssel gemeinsam sichern und Wiederherstellung testen. Verlorene Schlüssel werden nie automatisch neu erzeugt. Nach Entfernung den Profiler nur privat prüfen. Produktion: APP_DEBUG=0, WebProfilerBundle und Sammlung deaktiviert. Eine lokale Protokollkette beweist keine Integrität eines kompromittierten Hosts.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Befehlsreferenz

--purge verlangt bestätigte Datenlöschung, standardmäßig Nein. --force ist nur mit --purge gültig und bestätigt die Löschung; bei --no-interaction zwingend. Externe Tresore, Links und unbekannte Dateien können vollständige Löschung verhindern. Erforderliche Schlüssel behalten und Ergebnis prüfen.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Befehlsreferenz](commands.md)


Wurde das Paket bereits entfernt, dieselbe Version vorübergehend ohne setup installieren. guardian:uninstall ausführen, Paket erneut entfernen und Cache leeren. Angepasste Konfigurationen manuell prüfen.
