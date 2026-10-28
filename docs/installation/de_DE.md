# ΩGuardian Profiler Shield — Installation und Verwendung

PHP ab 8.3; Symfony 5.4, 6.4 oder 7.4. Symfony 8.1 benötigt PHP ab 8.4. Composer und die OpenSSL + Tokenizer-Erweiterung sind erforderlich. Für den Profiler WebProfilerBundle installieren.

## 1. Installieren

Im Stammverzeichnis der Symfony-Anwendung mit composer.json und bin/console ausführen.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Konfigurieren

```bash
php vendor/bin/guardian setup
```

Der Assistent aktiviert das Bundle in dev/test, richtet Tresor und Administratorkonto ein, leert den Cache und prüft die Konfiguration. Erst nach erfolgreichem Abschluss mit dem nächsten Schritt fortfahren. Die Anwendung währenddessen nicht öffentlich zugänglich machen. Erneutes Ausführen erhält Konten und Schlüssel.

## 3. Verwaltung öffnen

```bash
php bin/console guardian:access --env=dev
```

Der Befehl zeigt den privaten Pfad. https://VOTRE-HOTE durch die tatsächliche HTTPS-Adresse einschließlich Port ersetzen. Zugangsdaten aus .guardian-shield/initial-admin-credentials.txt oder dem angegebenen Tresor lesen. Bei der ersten Anmeldung muss das Passwort geändert werden. Diese Datei nicht weitergeben.

## 4. Deinstallieren

Worker stoppen und die Befehle der Reihe nach erfolgreich ausführen. Tresor und Schlüssel bleiben erhalten. Worker neu starten und den Profiler in einer privaten Umgebung prüfen.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Zum Löschen von Konten und Einstellungen den ersten Befehl durch den folgenden ersetzen und bestätigen. Bei Ablehnung bleibt alles unverändert. Externe Tresore und angepasste Dateien manuell prüfen; benötigte Tresorschlüssel behalten.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. CLI

Die Hilfe beschreibt die Befehle. Der Zugriffsbefehl findet die Verwaltung; die Diagnose prüft die Konfiguration. Vertrauenswürdiger lokaler Zugriff ist erforderlich.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## Private Packagist

Bei Verwendung von Private Packagist dieses Repository vor der Installation konfigurieren und Composer-Zugangsdaten nach den Vorgaben der Organisation speichern. Keine Tokens in Git ablegen.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
