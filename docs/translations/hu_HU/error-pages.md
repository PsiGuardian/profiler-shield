# Public Error oldalak

[Dokumentáció tartalma](README.md)


## Public Error oldalak

Tartsa meg az alkalmazás oldalait, vagy jelenítsen meg semleges HTML-t. A tényleges HTTP-állapotkódok változatlanok.

Szülői módja Symfony hibaelhárítás. generikus mód figyelmen kívül hagyja a testreszabott szöveget. csak a testre szabott mód használja a látogató üzenetét. A nyilvános hivatkozás hiányzik, véletlenszerű vagy rögzített; független az üzenetről. HTTP Az állapot kódjai változatlanok maradnak.

Felhasználási oldalak: megőrzi a webhely létező 404 válaszok; kivételek használják a 404 Template, ha rendelkezésre áll, egyébként egy semleges oldal. semleges: szabványos üzenet. személyre szabott: használja a szöveget.

A névtelen 500-as kivételek semleges oldalt kapnak. Részletekhez jogosult Guardian-azonosság és GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1 kell. Hiányzó vagy hibás Twig-sablonnál semleges megjelenítés van; saját hibavezérlők külön integrációt igényelnek. JSON és sikeres üzleti oldalak változatlanok.

| error_mode | Beállítások |
|---|---|
| native | Felhasználási oldalak: megőrzi a webhely létező 404 válaszok; kivételek használják a 404 Template, ha rendelkezésre áll, egyébként egy semleges oldal. semleges: szabványos üzenet. személyre szabott: használja a szöveget. |
| generic | HTML 400–599 → Tartsa meg az alkalmazás oldalait, vagy jelenítsen meg semleges HTML-t. A tényleges HTTP-állapotkódok változatlanok. |
| custom | Csak egyéni semleges üzenetnél érhető el. A szöveg módváltáskor megmarad. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Csak egy személyre szabott statikus referenciával érhető el. |
| error_custom_text | Csak egyéni semleges üzenetnél érhető el. A szöveg módváltáskor megmarad. |

## Megjelenített hivatkozás

Az eltérő üzenetek vagy állandó hivatkozások elárulhatják a valódi hibát. A HTTP-kódokat nem szabad meghamisítani.

A hivatkozás a Guardian’s semleges oldalak: véletlenszerűen generálja egy új kódot, nem véletlen üzenet.

A beállításokat mentés előtt a kiszolgáló ellenőrzi.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Hálózat és telepítés

Az IP engedélyezési listája elfogadja IPv4, IPv6 és CIDR Hálózatok - Ellenőrizze Symfony megbízható proxy konfiguráció, mielőtt lehetővé teszi. HTTPS Biztonságos cookie-k használata. Guardian nem javítja a kiszolgáltatott webszervereket vagy adatbázisokat.

Tagadja meg a diagnosztikát, ha az azonosság, szerep, munkamenet, hálózat, ütemezés, JIT vagy zár nem ellenőrizhető. A böngésző nem dönt a lejáratról. Gyűjtés és megtekintés különbözik. Eszközfelismerés nem hitelesítés. Vészhozzáféréshez azonosság, felhasznált kód és tartós audit kell; kritikus audithiba tiltja a nyitást. Hibás konfigurációt ne alkalmazzon részlegesen. Opcionális védelem nem igényel távoli szolgáltatást.
