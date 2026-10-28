# Einstellungen

[Dokumentationsübersicht](README.md)


## Zusätzliche Schutzmaßnahmen

Schutzmaßnahmen sind mit ihren Einstellungen gruppiert. Deaktivierte Optionen sind ausgegraut; ihre Werte bleiben erhalten.

Guardian kontrolliert die Profil- und Debug Toolbar. Ein autorisierter Administrator oder Entwicklerkonto ist erforderlich. MFADie Zeitpläne, JIT, Netzwerkbeschränkungen und Lockdown können immer noch den Zugriff verweigern. Stealth-Modus versteckt Eingangspunkte; es ersetzt nicht diese Überprüfungen.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Profil | Einstellungen |
|---|---|
| Solo · Flexibel | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| Team · verstärkt | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| Empfindlich · begrenzt | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Parameter | Einstellungen | Anfangswert |
|---|---|---|
| `stealth` | Auf nicht autorisierten Diagnoserouten 404 statt 403 zurückgeben. | `true` |
| `mfa` | Alle Nutzer müssen TOTP einrichten, bevor diese Pflicht aktiviert wird. | `false` |
| `idle_minutes` | Minuten ohne Aktivität bis zum Sitzungsablauf. | `60` |
| `absolute_hours` | Stunden, unabhängig von Aktivität. | `8` |
| `remember_days` | Tage. Ersetzt weder Anmeldung noch Arbeitszeitregeln. | `14` |
| `schedule_enabled` | Legen Sie fest, wann die Diagnosewerkzeuge zugänglich sind. Die Geschäftsseiten bleiben verfügbar. | `false` |
| `weekdays` | Wählen Sie die Tage aus, an denen Diagnosen erlaubt sind. | `1,2,3,4,5` |
| `start` | Beginn | `09:00` |
| `end` | Ende | `18:00` |
| `timezone` | Die Zone wird aus der PHP-Laufzeit dieser Anfrage ermittelt. PHP-CLI, PHP-FPM und das Betriebssystem können verschiedene Zeitzonen verwenden. Überprüfen Sie die Serverkonfiguration vor dem Speichern. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | Die private URL liefert außerhalb der Zeiten 404. Wiederherstellung nur über lokale CLI. | `false` |
| `jit_enabled` | Profiler nur während einer angeforderten Diagnosephase öffnen. | `false` |
| `jit_minutes` | Minuten, nur bei aktivierter Funktion. | `30` |
| `ip_allowlist` | Achtung: Fehler können Sie aussperren. Lokale Wiederherstellung: guardian:policy:reset. | `∅` |
| `audit_level` | Detailgrad und Aufbewahrungsdauer des lokalen Protokolls wählen. | `standard` |
| `audit_retention_days` | Tage bis zur Protokollbereinigung. | `30` |
| `alert_email` | Empfänger von Notfallmeldungen. Der Zugriff darf nicht von der E-Mail-Zustellung abhängen. | `∅` |
| `emergency_enabled` | 30-minütige Ausnahme mit Einmalcode und Auditprotokoll erlauben. | `false` |
| `login_rules` | Das Default: 10 Versagen und dann eine 10-minute lockout.Counters gelten sowohl für das Konto und die Netzwerkquelle, einschließlich Passwort bestätigungen. | `10 / 10 / 30 / 30` |
| `password_policy` | Diese Regeln gelten für Passwort-Erstellung und Änderungen, im Web und in der CLISie enthüllen oder überprüfen keine vorhandenen Passwörter. | `min_length=15` |
| `error_mode` | Anwendungsseiten: bewahrt die bestehende Website 404 Reaktionen; Ausnahmen nutzen 404 Template, wenn verfügbar, sonst eine neutrale Seite. Neutral: Standardnachricht. Anpassung: verwenden Sie Ihren Text. | `native` |
| `error_reference` | Die Referenz gilt für Guardian's neutrale Seiten: Random generiert einen neuen Code, nicht eine Random-Nachricht. Es ist kein Log Correlation Identifier. | `none` |
| `error_reference_text` | Erhältlich nur mit einer benutzerdefinierten statischen Referenz. | `∅` |
| `error_custom_text` | Nur im Modus „Eigene neutrale Nachricht“ verfügbar. Ihr Text bleibt beim Moduswechsel erhalten. | `∅ → custom` |

Es ermöglicht die Zeitplanung, ausgewählte Tage, eine einschließende Startzeit und eine exklusive Endzeit. Die gespeicherten Zeitzone bestimmt lokale Uhrzeiten. Übernachtungsintervallen werden nicht akzeptiert. Das Verbergen des Portals außerhalb der Uhrzeit verhindert remote Emergency-Zugang; halten lokalen Wiederherstellungskommandos zur Verfügung.

Idle-Timout, absolute Lebensdauer und Gerätminnung dienen unterschiedlichen Zwecken. Erinnerung an ein Gerät verpasst keine Zeitpläne oder JIT. Revocation beendet relevante Sitzungen auf dem Server. Verwenden Sie individuelle Konten, um Aktionen in der Zeitschrift zu erteilen.

JIT erfordert die Aktivierung eines vorübergehenden Fensters aus einer autorisierten Sitzung. Notfallzugang, wenn aktiviert wird, erfordert ein Passwort, einen einzigen Anwendungscode und eine Gründe. Es übertrifft nicht globale Lockdown. Lokale Befehl bleiben notwendig, wenn der Portal unzureichbar ist.

Die IP-Leistung akzeptiert IPv4, IPv6 und CIDR Netzwerk - Check Symfony vertrauenswürdige Proxy-Konfiguration, bevor Sie es ermöglichen. HTTPS für sichere Cookies. Guardian Repariert nicht exponierte Web-Server oder Datenbanken.

Ebene und Aufbewahrung bestimmen verfügbare Ereignisse, bis 5000 Einträge. Filter nach Typ und Konto, bevor Sie Seiten ändern. Warnungen werden gekürzt; Konfigurieren Sie deren Transport und Lieferung. Eine lokale Hashkette garantiert nicht die Integrität eines kompromittierten Hosts.

Stellen Sie die Mindestlänge und erforderliche Kategorien in Verbindungen und Passwörter fest. Account Creation Bands beschreiben die gespeicherten Richtlinie. Änderungen gelten für neue Passworte; wählen Sie eine obligatorische Erneuerung für bestehende Konten aus. Guardian Wählen Argon2id Wenn PHP Er gibt es, sonst bcrypt.

Filtern Sie alle gespeicherten Ereignisse nach Typ und Konto. Neueste Ereignissen erscheinen zuerst, in Seiten von 50Scrolling laden die nächste Seite nicht auf.

## Umgebung


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


Der Proxy übernimmt TLS, Normalisierung und Schutz vor hohem Verkehrsvolumen. Die Zugangsschicht prüft Diagnosen vor dem Routing; Geschäftsseiten lesen keine Konten oder Sitzungen. Guardian-Identitäten sind von Symfony getrennt. Privaten Tresor und Schlüssel gemeinsam außerhalb des öffentlichen Verzeichnisses sichern. Ein kompromittierter PHP-Prozess oder Host kann beide offenlegen. Der Speicher ist für einen Host bestimmt. Start-, Cache- oder Proxyfehler können die gesamte Anwendung betreffen.

Tresor und Schlüssel gemeinsam sichern und Wiederherstellung testen. Verlorene Schlüssel werden nie automatisch neu erzeugt. Nach Entfernung den Profiler nur privat prüfen. Produktion: APP_DEBUG=0, WebProfilerBundle und Sammlung deaktiviert. Eine lokale Protokollkette beweist keine Integrität eines kompromittierten Hosts.

## Wiederherstellung und Verifizierung

Leert die IP-Liste und deaktiviert Zeitplan, JIT und entfernten Notfallzugriff. Anmeldung, MFA, globale Sperre und Anmeldesperren bleiben bestehen. Gewünschte Regeln anschließend im Panel wieder setzen.

on sperrt Diagnosen und entfernt alle Sitzungen; Geschäftsseiten bleiben erreichbar. off hebt nur die globale Sperre auf. Erneute Anmeldung und alle anderen Richtlinien bleiben erforderlich.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
