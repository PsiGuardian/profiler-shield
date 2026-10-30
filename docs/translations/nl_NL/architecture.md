# Architectuur en vertrouwensgrenzen

[Documentatie-inhoud](README.md)


## Architectuur en vertrouwensgrenzen

De proxy verzorgt TLS, normalisatie en bescherming tegen verkeersvolumes. De toegangspoort controleert diagnostiek vóór routering; bedrijfsroutes lezen geen accounts of sessies. Guardian-identiteiten staan los van Symfony. Bewaar kluis en sleutel samen buiten de publieke map. Een gecompromitteerd PHP-proces of host kan beide blootstellen. Opslag is voor één host. Opstart-, cache- of proxyfouten kunnen de hele applicatie raken.

De IP allowlist accepteert IPv4, IPv6 en CIDR Netwerken - Check Symfony vertrouwde proxy-configuratie voordat u het mogelijk maakt. HTTPS Voor veilige cookies. Guardian Repareert niet blootgestelde web servers of databases.

Guardian controleert het profiel en de toolbar. Een geautoriseerde administrator of ontwikkelaar account is vereist. MFADe schema’s, JIT, netwerkbeperkingen en lockdown kunnen nog steeds toegang weigeren. stealth-modus verbergt inputpunten; het vervangt deze checks niet.

| PHP / HTTP | Architectuur en vertrouwensgrenzen |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| Sessie | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Beveiligingsvoorwaarden

Weiger diagnostiek als identiteit, rol, sessie, netwerk, tijdschema, JIT of blokkade niet controleerbaar is. De browser beslist niet over verlopen. Profielen bekijken en verzamelen zijn verschillend. Apparaatherkenning authenticeert niet. Noodtoegang vereist identiteit, verbruikte code en duurzame audit; kritieke auditfout voorkomt openen. Pas ongeldige configuratie niet gedeeltelijk toe. Optionele controles vereisen geen externe dienst.

Maak samen een back-up van kluis en sleutel en test herstel. Verloren sleutels worden nooit automatisch hermaakt. Controleer na verwijdering de profiler alleen privé. Productie: APP_DEBUG=0, WebProfilerBundle en verzameling uit. Een lokale auditketen bewijst geen integriteit op een gecompromitteerde host.

## Journalisten en waarschuwingen

Niveau en retentie bepalen de beschikbare gebeurtenissen, tot 5000 inschrijvingen. filteren door type en account voordat u pagina's wijzigt. waarschuwingen worden geconfigureerd; configureren hun transport en levering. Een lokale hashketen garandeert niet de integriteit van een gevaarlijke host.
