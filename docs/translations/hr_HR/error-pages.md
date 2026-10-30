# Javna pogrešna stranica

[Sadržaj dokumentacije](README.md)


## Javna pogrešna stranica

Zadržite stranice aplikacije ili prikažite neutralni HTML. Stvarni HTTP statusni kodovi ostaju isti.

Sljedeći članakNative mode lets Symfony Opći način ignoriše prilagođeni tekst. samo prilagođen način koristi poruku posjetitelja. javna referenca može biti odsutna, slučajna ili fiksna; ona je neovisna o poruci. HTTP Kodovi stanja ostaju nepromijenjeni.

Stranice za aplikacije: sačuvaju postojeće stranice 404 Odgovori; iznimke koriste 404 Šablon kada je na raspolaganju, inače neutralna stranica. Neutral: standardni poruka. Prilagođeni: koristi svoj tekst.

Anonimne iznimke 500 dobivaju neutralnu stranicu. Detalji zahtijevaju ovlašteni Guardian identitet i GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Nedostajući ili neispravni Twig predlošci koriste neutralni prikaz; vlastiti kontroleri pogrešaka trebaju integraciju. JSON i uspješne poslovne stranice ne mijenjaju se.

| error_mode | Postavke |
|---|---|
| native | Stranice za aplikacije: sačuvaju postojeće stranice 404 Odgovori; iznimke koriste 404 Šablon kada je na raspolaganju, inače neutralna stranica. Neutral: standardni poruka. Prilagođeni: koristi svoj tekst. |
| generic | HTML 400–599 → Zadržite stranice aplikacije ili prikažite neutralni HTML. Stvarni HTTP statusni kodovi ostaju isti. |
| custom | Dostupno samo za prilagođenu neutralnu poruku. Tekst ostaje spremljen pri promjeni načina. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Dostupna je samo sa standardnim statskim referencijama. |
| error_custom_text | Dostupno samo za prilagođenu neutralnu poruku. Tekst ostaje spremljen pri promjeni načina. |

## Prikazana referenca

Različite poruke ili statične reference mogu otkriti stvarnu pogrešku. HTTP statusni kodovi ne smiju se krivotvoriti.

Referencija se primjenjuje na Guardian's neutralne stranice: slučajno generira novi kod, a ne slučajnu poruku.

Odabiri se provjeravaju na poslužitelju prije spremanja.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Mreža i implementacija

IP dopuštenje prihvata IPv4, IPv6 i CIDR Mreža – provjerite Symfony povjereno proxy konfiguracija prije omogućavanja. HTTPS Za sigurne kolačiće. Guardian ne popravlja izložene web servere ili baze podataka.

Odbijte dijagnostiku ako se ne mogu provjeriti identitet, uloga, sesija, mreža, raspored, JIT ili blokada. Preglednik ne određuje istek. Pregled i prikupljanje su odvojeni. Prepoznavanje uređaja ne autentificira. Hitnost traži identitet, potrošen kod i trajni audit; kritična pogreška audita sprječava otvaranje. Ne primjenjujte nevaljane postavke djelomično. Neobvezne kontrole ne zahtijevaju udaljenu uslugu.
