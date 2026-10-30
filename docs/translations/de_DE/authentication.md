# Sign-ins und Passwörter

[Dokumentationsübersicht](README.md)


## Konten und erste Sign-in

Willkommen bei Guardian. Ihr ursprüngliches Passwort ist vorübergehend. Wählen Sie ein neues Geheimnis: das Panel und die Diagnosen bleiben gesperrt, bis Sie es tun.

Guardian Konten sind von Seitenkonten getrennt. Das ursprüngliche Konto und neue Konten müssen ihre Passwörter ändern, bevor sie fortfahren. Die Sicherheitsrolle sieht Informationen ohne Profilzugang erlaubt. Änderung einer Rolle oder Passwort verhindert frühere Sitzungen.

Richtet privaten Pfad, Schlüssel, Tresor, Symfony-Dateien und bei Bedarf einen Administrator ein. Bestehende Konten bleiben erhalten. Produktion wird abgewiesen. Nach direktem Aufruf Cache leeren und Prüfungen ausführen.


```bash
php bin/console guardian:access --env=dev
```


| Speicherte Passwortanforderungen | Minimale Länge (Unicode Die Charaktere) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Passwortpolitik

Diese Regeln gelten für Passwort-Erstellung und Änderungen, im Web und in der CLISie enthüllen oder überprüfen keine vorhandenen Passwörter.

Der ursprüngliche Wert: 15 Zeichen. Verwenden Sie ein einzigartiges Passwort oder einen Kennwörterverwalter. erforderliche Charakterkategorien garantieren kein unvorhersehbares Kennwort.

Auf diesem Server, Guardian Automatisch verwendet Argon2id für neue Passwörter. bewahren Sie PHP Unterstützung erlaubt. der Anwendungsbereich ist 512 UTF-8 und byte.

Diese PHP Der Server bietet nicht Argon2id. Guardian Daher benutzt bcrypt und verweigert Passwörter überschreiten 72 UTF-8 Bitten. bitten Sie Ihren Systemadministrator, um Argon2id Unterstützung in PHP.

Diese Option widerruft unverzüglich bestehende Sitzungen, einschließlich Ihrer. Konten müssen ihre Passwörter ändern, bevor Sie Guardian konfigurieren oder Diagnosen öffnen.

Ändert das Passwort des vorhandenen Kontos username per verdeckter Eingabe gemäß aktiver Richtlinie, widerruft alle Sitzungen und protokolliert die Aktion. Passwort nie als Argument übergeben.

## Progressive Lockheed

Jede Phase definiert eine Fehlergrenze, zählende Fenster, Lockout-Dauer und wiederholte Fenster. 10 Schwierigkeiten führen zu einem 10-minute lockout. Wiederholte Fehler können die nächste Phase auslösen. Checks decken sowohl das Konto als auch die Netzwerkquelle, einschließlich Administrator Bestätigungen.

Verschlüsselte Zähler gelten unabhängig für normalisierte Konten und Quellen; rohe IP-Adressen werden nicht gespeichert. Vor dem Hashen erfolgt eine atomare Reservierung für 120 Sekunden. Aufgegebene Reservierungen zählen als Fehler, verspätete Ergebnisse erzeugen keine Sitzung. Ungültiges CSRF reserviert nichts. Erfolgreiche Anmeldung löscht Kontofehler, erhält Wiederholungs- und Quellenhistorie. Bei 10000 aktiven Einträgen werden neue Identitäten abgewiesen, statt Sperren zu verdrängen.

Etappen laufen in Ordnung. Nach einem Lock-out, neue Fehler innerhalb des Wiederholungsfensters verwenden Sie die nächste Phase. Außerhalb dieses Fensters gilt die erste Phase erneut. Die letzte Phase wiederholt. Blockierte Anfragen verlängern nicht die Verzögerung. Maximum 8 Phasen der lokalen Erholung: guardian:login:unlock --all.

| Stufe | Speicherte Passwortanforderungen |
|---|---|
| Versagen vor dem Lock | 1–100 |
| Laufzeiten ( Minuten ) | 1–10080 |
| Anzahl der Fenster (Minuten) | 1–1440 |
| Wiederholen Sie das Fenster nach dem Unlock (Minuten) | 1–10080 |
| Stufe | 1–8; block[n+1] ≥ block[n] |
| Anfangswert | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Exhaustive-Search Kostenlabor

Vergleichen Sie gleichmäßig zufällige Geheimnisse einer festen Länge, ohne ein echtes Passwort eingeben zu können. Erwähnungsraten und Hardwarepreise sind angepasbare Annahmen. Keine Sign-in Versuche werden gesendet.

Die Berechnung verwendet getrennte ASCII-Kategorien und BigInt. Inklusion-Exklusion bestimmt den exakten Suchraum; fehlende Kategorien oder zu kurze Länge ergeben null. Mittlerer Rang ohne Zurücklegen: (N+1)/2. Die erwartete Sperrzeit berücksichtigt Schwellen und die wiederholte letzte Stufe. Passt die Rate nicht zu den Zeitfenstern, wird nicht geschätzt. Offlinekosten entsprechen Stunden mal angenommenem Stundenpreis.

Mit einem festen Alphabet reduziert eine Kategorie die Anzahl der vereinbarten Geheimnisse. Die Erweiterung des Alphabets ist eine andere Operation. Lockout-Times veranlasst kontinuierliche Überlegungen schnell genug, um innerhalb konfigurierten Fenster zu bleiben.

Dieses Modell beurteilt nicht die Stärke eines menschlich gewählten Passworts. Es ausschließt Wörterbuch, Wiederverwendung, Löcher und zielgerichtete Strategien. Web-Locks schützen keine gestohlenen Hashs. Die ursprüngliche Offline-Rate ist illustrativ, kein Referenzzeichen. CPU Die Messung : guardian:password:benchmark.

Große Zahlen sind voll geschrieben, mit Ziffergruppung und ohne E-Noteierung. Zeiten und Kosten sind runden. Vergleichen Sie die gleiche Reihenfolge mit den gleichen Annahmen.

| Verfügbare Simulation Alphabet | mögliche vereinbarte Geheimnisse |
|---|---|
| ASCII Niedriger Fall | 26 |
| ASCII Aufstieg | 26 |
| ASCII Digitalisierung | 10 |
| ASCII Symbole, die den Raum ausschließen | 32 |
| Annahme der Online-Rate (Guesses/s) | 1 / s |
| Offline Prämien (Hashes/s) | 10000 / s |
| Kosten für Hardware (€ / Stunde) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## Wiederherstellung und Verifizierung

Tresor und Schlüssel gemeinsam sichern und Wiederherstellung testen. Verlorene Schlüssel werden nie automatisch neu erzeugt. Nach Entfernung den Profiler nur privat prüfen. Produktion: APP_DEBUG=0, WebProfilerBundle und Sammlung deaktiviert. Eine lokale Protokollkette beweist keine Integrität eines kompromittierten Hosts.

Setzt Kontozähler für username zurück. --ip ADRESSE entfernt zusätzlich den Zähler dieser IPv4/IPv6-Quelle; sonst kann deren Sperre bestehen bleiben. --all löscht sämtliche Zähler. Konten, Passwörter und Sitzungen bleiben unverändert; die Aktion wird protokolliert.

[Befehlsreferenz](commands.md)
