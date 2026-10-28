# Instellingen

[Documentatie-inhoud](README.md)


## Aanvullende bescherming

Beveiligingen staan bij hun instellingen. Uitgeschakelde opties zijn grijs; hun waarden blijven bewaard.

Guardian controleert het profiel en de toolbar. Een geautoriseerde administrator of ontwikkelaar account is vereist. MFADe schema’s, JIT, netwerkbeperkingen en lockdown kunnen nog steeds toegang weigeren. stealth-modus verbergt inputpunten; het vervangt deze checks niet.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Profiel | Instellingen |
|---|---|
| Solo · Flexibel | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| Team · verbeterd | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| Gevoelige · beperkte | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Parameter | Instellingen | Beginwaarde |
|---|---|---|
| `stealth` | Geef 404 in plaats van 403 terug voor niet-toegestane diagnoseroutes. | `true` |
| `mfa` | Alle gebruikers moeten TOTP instellen voordat dit verplicht wordt. | `false` |
| `idle_minutes` | Minuten zonder activiteit voordat de sessie verloopt. | `60` |
| `absolute_hours` | Uren, ongeacht activiteit. | `8` |
| `remember_days` | Dagen. Vervangt nooit aanmelden of werktijdregels. | `14` |
| `schedule_enabled` | Kies wanneer diagnosetools toegankelijk zijn. Applicatiepagina's blijven beschikbaar. | `false` |
| `weekdays` | Selecteer de dagen waarop diagnostiek is toegestaan. | `1,2,3,4,5` |
| `start` | Opening | `09:00` |
| `end` | Sluiting | `18:00` |
| `timezone` | Bepaald door het PHP-proces voor deze pagina. PHP CLI, PHP-FPM en het besturingssysteem kunnen verschillende tijdzones gebruiken. Controleer de serverconfiguratie vóór het opslaan. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | De privé-URL geeft buiten werktijd 404 terug. Herstel alleen via de lokale CLI. | `false` |
| `jit_enabled` | Open de profiler alleen tijdens een aangevraagde diagnoseperiode. | `false` |
| `jit_minutes` | Minuten, alleen gebruikt indien ingeschakeld. | `30` |
| `ip_allowlist` | Waarschuwing: fouten kunnen u buitensluiten. Lokaal herstel: guardian:policy:reset. | `∅` |
| `audit_level` | Selecteer logdetails en de lokale bewaartermijn. | `standard` |
| `audit_retention_days` | Dagen vóór het opruimen van logs. | `30` |
| `alert_email` | Ontvangt noodmeldingen. Toegang mag niet afhangen van e-mailbezorging. | `∅` |
| `emergency_enabled` | Sta een uitzondering van 30 minuten toe met eenmalige code en auditlog. | `false` |
| `login_rules` | De standaard: 10 mislukkingen, vervolgens een 10-minute lockout. counters zijn van toepassing op zowel account als netwerk oorsprong, met inbegrip van wachtwoord bevestiging. | `10 / 10 / 30 / 30` |
| `password_policy` | Deze regels zijn van toepassing op het creëren en wijzigen van wachtwoorden, op het web en in de CLIZe onthullen of inspecteren niet bestaande wachtwoorden. | `min_length=15` |
| `error_mode` | Applicatiepagina's: behoudt de bestaande site 404 Reacties; uitzonderingen gebruiken 404 template wanneer beschikbaar, anders een neutrale pagina. neutraal: standaard bericht. aangepast: gebruik uw tekst. | `native` |
| `error_reference` | De verwijzing is van toepassing op Guardian's neutraal pagina's: willekeurig genereert een nieuwe code, niet een willeks bericht. het is geen log correlation identifier. | `none` |
| `error_reference_text` | Alleen beschikbaar met een standaard statische referentie. | `∅` |
| `error_custom_text` | Alleen beschikbaar bij een aangepast neutraal bericht. Uw tekst blijft bewaard bij het wisselen van modus. | `∅ → custom` |

Schema, selecteer dagen, een inclusieve starttijd en een exclusieve eindtijd. De gereserveerde tijdzone bepaalt lokale tijden. Overnight intervals zijn niet geaccepteerd. Het verbergen van het portaal buiten de uren voorkomt remote noodtoegang; houd lokale herstelbevelingen beschikbaar.

Idle timeout, absolute levensduur en apparaat herinnering dienen verschillende doeleinden. het herinneren van een apparaat overschrijdt geen schema's of JIT. Revocation beëindigt relevante sessies op de server. Gebruik individuele accounts om acties in het tijdschrift toe te wijzen.

JIT vereist het activeren van een tijdelijke venster vanaf een geautoriseerde sessie. noodtoegang, wanneer deze is ingeschakeld, vereist een wachtwoord, een single-use code en een reden. Het gaat niet over de wereldwijde lockdown. Lokale bestellingen blijven nodig als het portaal ontoegankelijk is.

De IP allowlist accepteert IPv4, IPv6 en CIDR Netwerken - Check Symfony vertrouwde proxy-configuratie voordat u het mogelijk maakt. HTTPS Voor veilige cookies. Guardian Repareert niet blootgestelde web servers of databases.

Niveau en retentie bepalen de beschikbare gebeurtenissen, tot 5000 inschrijvingen. filteren door type en account voordat u pagina's wijzigt. waarschuwingen worden geconfigureerd; configureren hun transport en levering. Een lokale hashketen garandeert niet de integriteit van een gevaarlijke host.

Stel minimale lengte en vereiste categorieën in verbindingen en wachtwoorden. account creation badges beschrijven het bewaard beleid. wijzigingen gelden voor nieuwe wachtwoord; selecteer verplichte verlenging voor bestaande accounts. Guardian keuzes Argon2id Wanneer PHP Het geeft het, anders bcrypt.

Filter alle bewaardde gebeurtenissen per type en account. nieuwste gebeurtenis verschijnt eerst, op pagina's van 50Scrolling brengt de volgende pagina niet op.

## Omgeving


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


De proxy verzorgt TLS, normalisatie en bescherming tegen verkeersvolumes. De toegangspoort controleert diagnostiek vóór routering; bedrijfsroutes lezen geen accounts of sessies. Guardian-identiteiten staan los van Symfony. Bewaar kluis en sleutel samen buiten de publieke map. Een gecompromitteerd PHP-proces of host kan beide blootstellen. Opslag is voor één host. Opstart-, cache- of proxyfouten kunnen de hele applicatie raken.

Maak samen een back-up van kluis en sleutel en test herstel. Verloren sleutels worden nooit automatisch hermaakt. Controleer na verwijdering de profiler alleen privé. Productie: APP_DEBUG=0, WebProfilerBundle en verzameling uit. Een lokale auditketen bewijst geen integriteit op een gecompromitteerde host.

## Herstel en verificatie

Leegt de IP-lijst en schakelt tijdschema, JIT en noodtoegang op afstand uit. Authenticatie, MFA, algemene blokkade en aanmeldblokkades blijven bestaan. Stel gewenste beperkingen daarna opnieuw in.

on sluit diagnostiek en verwijdert alle sessies; bedrijfsfuncties blijven beschikbaar. off heft alleen de algemene blokkade op: opnieuw aanmelden en overige regels blijven nodig.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
