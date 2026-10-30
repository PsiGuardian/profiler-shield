# Architektur und Vertrauensgrenzen

[Dokumentationsübersicht](README.md)


## Architektur und Vertrauensgrenzen

Der Proxy übernimmt TLS, Normalisierung und Schutz vor hohem Verkehrsvolumen. Die Zugangsschicht prüft Diagnosen vor dem Routing; Geschäftsseiten lesen keine Konten oder Sitzungen. Guardian-Identitäten sind von Symfony getrennt. Privaten Tresor und Schlüssel gemeinsam außerhalb des öffentlichen Verzeichnisses sichern. Ein kompromittierter PHP-Prozess oder Host kann beide offenlegen. Der Speicher ist für einen Host bestimmt. Start-, Cache- oder Proxyfehler können die gesamte Anwendung betreffen.

Die IP-Leistung akzeptiert IPv4, IPv6 und CIDR Netzwerk - Check Symfony vertrauenswürdige Proxy-Konfiguration, bevor Sie es ermöglichen. HTTPS für sichere Cookies. Guardian Repariert nicht exponierte Web-Server oder Datenbanken.

Guardian kontrolliert die Profil- und Debug Toolbar. Ein autorisierter Administrator oder Entwicklerkonto ist erforderlich. MFADie Zeitpläne, JIT, Netzwerkbeschränkungen und Lockdown können immer noch den Zugriff verweigern. Stealth-Modus versteckt Eingangspunkte; es ersetzt nicht diese Überprüfungen.

| PHP / HTTP | Architektur und Vertrauensgrenzen |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| Sitzung | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Sicherheitsinvarianten

Diagnosen ablehnen, wenn Identität, Rolle, Sitzung, Netzwerk, Zeitplan, JIT oder Sperre nicht geprüft werden können. Der Browser bestimmt keine Ablaufzeiten. Sammlung und Ansicht sind getrennt. Gerätemerkung authentifiziert nicht. Notfallzugriff braucht Identität, verbrauchten Code und dauerhaftes Audit; kritischer Auditfehler verhindert Freigabe. Ungültige Konfiguration nicht teilweise anwenden. Optionale Kontrollen brauchen keinen entfernten Dienst.

Tresor und Schlüssel gemeinsam sichern und Wiederherstellung testen. Verlorene Schlüssel werden nie automatisch neu erzeugt. Nach Entfernung den Profiler nur privat prüfen. Produktion: APP_DEBUG=0, WebProfilerBundle und Sammlung deaktiviert. Eine lokale Protokollkette beweist keine Integrität eines kompromittierten Hosts.

## Zeitungen und Warnungen

Ebene und Aufbewahrung bestimmen verfügbare Ereignisse, bis 5000 Einträge. Filter nach Typ und Konto, bevor Sie Seiten ändern. Warnungen werden gekürzt; Konfigurieren Sie deren Transport und Lieferung. Eine lokale Hashkette garantiert nicht die Integrität eines kompromittierten Hosts.
