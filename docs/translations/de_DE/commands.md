# Befehlsreferenz

[Dokumentationsübersicht](README.md)

Im Symfony-Stammverzeichnis in einem vertrauenswürdigen Terminal ausführen. Beispiele verwenden dev. Passwörter, Schlüssel und Wiederherstellungscodes niemals in Argumenten, Protokollen oder Git speichern.

## Befehle finden und Hilfe anzeigen


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev und --no-interaction stammen von Symfony. --no-interaction liefert keine Geheimnisse und eignet sich nicht für interaktive Passwortbefehle. --help zeigt die Syntax, -v weitere Details. Code 0 bestätigt das gemeldete Ergebnis; --no-admin kann ohne nutzbares Portal erfolgreich sein.

## `vendor/bin/guardian`

Aktiviert das Bundle in dev/test, leert den Cache, führt guardian:setup --automatic aus, leert den Cache erneut und startet guardian:doctor sowie guardian:access. Stoppt beim ersten Fehler. Erneutes Ausführen erhält Konten und Schlüssel. APP_ENV=prod wird abgewiesen; proc_open ist erforderlich.

Ohne Argument, mit --help, -h oder help wird nur Hilfe angezeigt. setup ist die einzige Aktion und akzeptiert keine weiteren Optionen.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Befehlsreferenz |
|---|---|
| [`guardian:setup`](#guardiansetup) | Richtet privaten Pfad, Schlüssel, Tresor, Symfony-Dateien und bei Bedarf einen Administrator ein. |
| [`guardian:install`](#guardianinstall) | Initialisiert den Tresor und erstellt einen Administrator mit --username, standardmäßig admin. |
| [`guardian:access`](#guardianaccess) | Zeigt Panel-Pfad und Speicherort der ersten Zugangsdaten, niemals das Passwort. |
| [`guardian:doctor`](#guardiandoctor) | Prüft Tresor, Berechtigungen, Schlüssel, Route, aktiven Administrator und Einstellungen. |
| [`guardian:password:reset`](#guardianpasswordreset) | Ändert das Passwort des vorhandenen Kontos username per verdeckter Eingabe gemäß aktiver Richtlinie, widerruft alle Sitzungen und protokolliert die Aktion. |
| [`guardian:mfa:reset`](#guardianmfareset) | Löscht TOTP-Geheimnis und Zähler für username, widerruft dessen Sitzungen und protokolliert die Aktion. |
| [`guardian:login:unlock`](#guardianloginunlock) | Setzt Kontozähler für username zurück. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Leert die IP-Liste und deaktiviert Zeitplan, JIT und entfernten Notfallzugriff. |
| [`guardian:lockdown`](#guardianlockdown) | on sperrt Diagnosen und entfernt alle Sitzungen; Geschäftsseiten bleiben erreichbar. |
| [`guardian:emergency`](#guardianemergency) | Erstellt 5 Einmalcodes für ein vorhandenes username-Konto und ersetzt dessen vorherige Codes. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Versendet wartende Warnungen an alert_email über PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Misst Hashprüfungen mit einem künstlichen Geheimnis, keinem Benutzerpasswort. |
| [`guardian:uninstall`](#guardianuninstall) | Bereitet die Entfernung nachweislich von Guardian erzeugter Dateien vor. |

## `guardian:setup`

Richtet privaten Pfad, Schlüssel, Tresor, Symfony-Dateien und bei Bedarf einen Administrator ein. Bestehende Konten bleiben erhalten. Produktion wird abgewiesen. Nach direktem Aufruf Cache leeren und Prüfungen ausführen.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic erzeugt admin und ein Geheimnis in einer privaten Datei mit Modus 0600. --path wählt einen Pfad mit 25–181 Zeichen; bestehende Pfade werden nicht ersetzt. --no-admin überspringt die Kontoerstellung; ohne aktiven Administrator ist das Portal unbenutzbar. --username schlägt den interaktiven Namen vor, standardmäßig admin; das automatische Konto bleibt unverändert.

```text
--path: ^/[a-zA-Z0-9/_-]{24,180}$
/_profiler; /_wdt → ∅
```

```bash
php bin/console guardian:setup --username=operator --env=dev
php bin/console cache:clear --env=dev
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## `guardian:install`

Initialisiert den Tresor und erstellt einen Administrator mit --username, standardmäßig admin. Verdeckte Passworteingabe gemäß aktiver Richtlinie. Bestehende Konten werden abgewiesen. Erstellt keine Routen oder Symfony-Konfiguration; normalerweise den vollständigen Assistenten verwenden.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Benutzername : 3 zu 64 Alphanumerische Charaktere -

## `guardian:access`

Zeigt Panel-Pfad und Speicherort der ersten Zugangsdaten, niemals das Passwort. --base-url akzeptiert den tatsächlichen HTTPS-Ursprung mit optionalem Port, ohne Zugangsdaten, Abfrage oder Fragment. Beispielhost ersetzen.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Prüft Tresor, Berechtigungen, Schlüssel, Route, aktiven Administrator und Einstellungen. Rückgabecode 1 bei einem gemeldeten Problem. PHP CLI und PHP-FPM können andere Zeitzonen haben. Prüft weder Internetzugriff noch Proxyregeln.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Ändert das Passwort des vorhandenen Kontos username per verdeckter Eingabe gemäß aktiver Richtlinie, widerruft alle Sitzungen und protokolliert die Aktion. Passwort nie als Argument übergeben.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Löscht TOTP-Geheimnis und Zähler für username, widerruft dessen Sitzungen und protokolliert die Aktion. Neuen Authentifikator registrieren. Die globale MFA-Anforderung bleibt aktiv und kann den Profiler weiterhin sperren.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Setzt Kontozähler für username zurück. --ip ADRESSE entfernt zusätzlich den Zähler dieser IPv4/IPv6-Quelle; sonst kann deren Sperre bestehen bleiben. --all löscht sämtliche Zähler. Konten, Passwörter und Sitzungen bleiben unverändert; die Aktion wird protokolliert.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Leert die IP-Liste und deaktiviert Zeitplan, JIT und entfernten Notfallzugriff. Anmeldung, MFA, globale Sperre und Anmeldesperren bleiben bestehen. Gewünschte Regeln anschließend im Panel wieder setzen.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on sperrt Diagnosen und entfernt alle Sitzungen; Geschäftsseiten bleiben erreichbar. off hebt nur die globale Sperre auf. Erneute Anmeldung und alle anderen Richtlinien bleiben erforderlich.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Erstellt 5 Einmalcodes für ein vorhandenes username-Konto und ersetzt dessen vorherige Codes. Nur einmal sichtbar; außerhalb des Servers im Geheimnismanager sichern. Nutzung erfordert aktivierten Notfallzugriff, angemeldete Sitzung, Passwort und Begründung. Umgeht keine globale Sperre.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Versendet wartende Warnungen an alert_email über PHP mail(). Mailtransport und Zeitplan einrichten, echte Zustellung prüfen. Fehlgeschlagene Warnungen bleiben in der Warteschlange. Rückgabecode 1 bei ungültigem Empfänger oder Versandfehler.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Misst Hashprüfungen mit einem künstlichen Geheimnis, keinem Benutzerpasswort. --iterations: 1–10, Standard 3. JSON mit Algorithmus, Zeit und Prüfungen pro Sekunde. Misst einen lokalen CPU-Prozess, keinen GPU-Angreifer.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Bereitet die Entfernung nachweislich von Guardian erzeugter Dateien vor. Konten, Einstellungen, Protokolle und Schlüssel bleiben standardmäßig erhalten. Worker stoppen, vor composer remove ausführen, danach dev-Cache leeren und Worker starten. Angepasste oder nicht zuordenbare Dateien prüfen.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge verlangt bestätigte Datenlöschung, standardmäßig Nein. --force ist nur mit --purge gültig und bestätigt die Löschung; bei --no-interaction zwingend. Externe Tresore, Links und unbekannte Dateien können vollständige Löschung verhindern. Erforderliche Schlüssel behalten und Ergebnis prüfen.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Deinstallation und Daten


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Tresor und Schlüssel gemeinsam sichern und Wiederherstellung testen. Verlorene Schlüssel werden nie automatisch neu erzeugt. Nach Entfernung den Profiler nur privat prüfen. Produktion: APP_DEBUG=0, WebProfilerBundle und Sammlung deaktiviert. Eine lokale Protokollkette beweist keine Integrität eines kompromittierten Hosts.

[Entfernung und Daten](desinstallation.md)
