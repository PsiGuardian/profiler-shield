# öffentliche Fehler Seiten

[Dokumentationsübersicht](README.md)


## öffentliche Fehler Seiten

Seiten der Anwendung beibehalten oder Besuchern neutrales HTML anzeigen. Die tatsächlichen HTTP-Statuscodes bleiben unverändert.

Native Mode ermöglicht Symfony Erstellen von Fehlern. Generisches Modus ignoriert benutzerdefinierte Text. Nur benutzerdaten Modus verwendet die Besuchernachricht. Die öffentliche Referenz kann fehlen, zufällig oder fester sein; es ist unabhängig von der Nachricht. HTTP Die Status-Codes bleiben unverändert.

Anwendungsseiten: bewahrt die bestehende Website 404 Reaktionen; Ausnahmen nutzen 404 Template, wenn verfügbar, sonst eine neutrale Seite. Neutral: Standardnachricht. Anpassung: verwenden Sie Ihren Text.

Anonyme 500-Ausnahmen erhalten eine neutrale Seite. Details benötigen eine berechtigte Guardian-Identität und GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Fehlende oder fehlerhafte Twig-Vorlagen führen zur neutralen Darstellung; eigene Fehlercontroller benötigen eigene Integration. JSON und erfolgreiche Geschäftsseiten bleiben unverändert.

| error_mode | Einstellungen |
|---|---|
| native | Anwendungsseiten: bewahrt die bestehende Website 404 Reaktionen; Ausnahmen nutzen 404 Template, wenn verfügbar, sonst eine neutrale Seite. Neutral: Standardnachricht. Anpassung: verwenden Sie Ihren Text. |
| generic | HTML 400–599 → Seiten der Anwendung beibehalten oder Besuchern neutrales HTML anzeigen. Die tatsächlichen HTTP-Statuscodes bleiben unverändert. |
| custom | Nur im Modus „Eigene neutrale Nachricht“ verfügbar. Ihr Text bleibt beim Moduswechsel erhalten. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Erhältlich nur mit einer benutzerdefinierten statischen Referenz. |
| error_custom_text | Nur im Modus „Eigene neutrale Nachricht“ verfügbar. Ihr Text bleibt beim Moduswechsel erhalten. |

## Angezeigte Referenz

Unterschiedliche Meldungen oder feste Referenzen können den tatsächlichen Fehler verraten. HTTP-Statuscodes dürfen nicht gefälscht werden.

Die Referenz gilt für Guardian's neutrale Seiten: Random generiert einen neuen Code, nicht eine Random-Nachricht. Es ist kein Log Correlation Identifier.

Die Eingaben werden vor dem Speichern serverseitig geprüft.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Netzwerk und Entfernung

Die IP-Leistung akzeptiert IPv4, IPv6 und CIDR Netzwerk - Check Symfony vertrauenswürdige Proxy-Konfiguration, bevor Sie es ermöglichen. HTTPS für sichere Cookies. Guardian Repariert nicht exponierte Web-Server oder Datenbanken.

Diagnosen ablehnen, wenn Identität, Rolle, Sitzung, Netzwerk, Zeitplan, JIT oder Sperre nicht geprüft werden können. Der Browser bestimmt keine Ablaufzeiten. Sammlung und Ansicht sind getrennt. Gerätemerkung authentifiziert nicht. Notfallzugriff braucht Identität, verbrauchten Code und dauerhaftes Audit; kritischer Auditfehler verhindert Freigabe. Ungültige Konfiguration nicht teilweise anwenden. Optionale Kontrollen brauchen keinen entfernten Dienst.
