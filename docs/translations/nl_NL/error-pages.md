# Public error pagina's

[Documentatie-inhoud](README.md)


## Public error pagina's

Behoud de pagina's van de applicatie of toon neutrale HTML. De echte HTTP-statuscodes blijven behouden.

Oorspronkelijke mode lets Symfony het maken van fouten. generieke modus ignoreert aangepaste tekst. alleen aangepast modus gebruikt de bezoeker boodschap. De publieke verwijzing kan ontbreken, willekeurig of vast zijn; het is onafhankelijk van het bericht. HTTP De statuscodes blijven onveranderd.

Applicatiepagina's: behoudt de bestaande site 404 Reacties; uitzonderingen gebruiken 404 template wanneer beschikbaar, anders een neutrale pagina. neutraal: standaard bericht. aangepast: gebruik uw tekst.

Anonieme 500-uitzonderingen krijgen een neutrale pagina. Details vereisen toegestane Guardian-identiteit en GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Ontbrekende of falende Twig-sjablonen vallen terug op neutrale weergave; eigen foutcontrollers vereisen eigen integratie. JSON en geslaagde bedrijfspagina’s blijven gelijk.

| error_mode | Instellingen |
|---|---|
| native | Applicatiepagina's: behoudt de bestaande site 404 Reacties; uitzonderingen gebruiken 404 template wanneer beschikbaar, anders een neutrale pagina. neutraal: standaard bericht. aangepast: gebruik uw tekst. |
| generic | HTML 400–599 → Behoud de pagina's van de applicatie of toon neutrale HTML. De echte HTTP-statuscodes blijven behouden. |
| custom | Alleen beschikbaar bij een aangepast neutraal bericht. Uw tekst blijft bewaard bij het wisselen van modus. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Alleen beschikbaar met een standaard statische referentie. |
| error_custom_text | Alleen beschikbaar bij een aangepast neutraal bericht. Uw tekst blijft bewaard bij het wisselen van modus. |

## Getoonde referentie

Verschillende meldingen of vaste referenties kunnen de echte fout onthullen. HTTP-statuscodes mogen niet worden vervalst.

De verwijzing is van toepassing op Guardian's neutraal pagina's: willekeurig genereert een nieuwe code, niet een willeks bericht. het is geen log correlation identifier.

Keuzes worden op de server gecontroleerd voordat ze worden opgeslagen.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Netwerk en implementatie

De IP allowlist accepteert IPv4, IPv6 en CIDR Netwerken - Check Symfony vertrouwde proxy-configuratie voordat u het mogelijk maakt. HTTPS Voor veilige cookies. Guardian Repareert niet blootgestelde web servers of databases.

Weiger diagnostiek als identiteit, rol, sessie, netwerk, tijdschema, JIT of blokkade niet controleerbaar is. De browser beslist niet over verlopen. Profielen bekijken en verzamelen zijn verschillend. Apparaatherkenning authenticeert niet. Noodtoegang vereist identiteit, verbruikte code en duurzame audit; kritieke auditfout voorkomt openen. Pas ongeldige configuratie niet gedeeltelijk toe. Optionele controles vereisen geen externe dienst.
