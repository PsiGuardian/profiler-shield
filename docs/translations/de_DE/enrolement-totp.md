# Zwei-Faktor-Authentifizierung

[Dokumentationsübersicht](README.md)


## Zwei-Faktor-Authentifizierung

Jede Person registriert ihre eigene TOTP Geheimnis und bestätigt einen Code.

Jede Person registriert ihre eigene TOTP Geheimnis und bestätigt einen Code. MFA Blocks Profile Zugang für unregisterte Konten. Plan administrativer Wiederherstellung, bevor dieser Schutz für das gesamte Team erforderlich ist. Wählen Sie die manuelle Einrichtung und geben Sie den angezeigten Schlüssel ein. Verwenden Sie SHA-1, sechs Ziffern und 30 Sekunden.

## 1. Identität bestätigen

Geben Sie dasselbe Passwort wie bei der Guardian-Anmeldung ein. Es wird für diese Einrichtung nur einmal benötigt.

## 2. Schlüssel in der Authenticator-App eingeben

Wählen Sie die manuelle Einrichtung und geben Sie den angezeigten Schlüssel ein. Verwenden Sie SHA-1, sechs Ziffern und 30 Sekunden.

## 3. Sechsstelligen Code bestätigen

Geben Sie den in Ihrer App angezeigten Code ein. Ein zweites Passwort ist nicht erforderlich.

## Datenschutz: Website-URL nicht eingeben

Wenn Ihre App nach einem Namen oder einer Website fragt, geben Sie nur „ΩGuardian“ oder einen neutralen Alias an. Geben Sie niemals die URL Ihrer Symfony-Instanz ein.

Geheim halten: Mit diesem Schlüssel lassen sich TOTP-Codes erzeugen. Nicht weitergeben oder Screenshots erstellen.

Vorläufige Geheimnisse verfallen nach 10 Minuten oder 5 falschen Codes; Einrichtung dann neu beginnen. Bei falschem Code bleibt das Formular ohne neue Passwortabfrage. Der Bestätigungszähler wird gegen Wiederholung verbraucht.

## Wiederherstellung und Verifizierung

Löscht TOTP-Geheimnis und Zähler für username, widerruft dessen Sitzungen und protokolliert die Aktion. Neuen Authentifikator registrieren. Die globale MFA-Anforderung bleibt aktiv und kann den Profiler weiterhin sperren.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
