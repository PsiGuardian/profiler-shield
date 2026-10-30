# Verejné chyby stránky

[Obsah dokumentácie](README.md)


## Verejné chyby stránky

Zachovajte stránky aplikácie alebo zobrazte neutrálne HTML. Skutočné stavové kódy HTTP sa nemenia.

Nádherná móda dovolí Symfony generický režim ignoruje prispôsobený text. iba prispôsobené režime používa návštevnícke posolstvo. verejná referencia môže byť neprítomná, náhodná alebo pevná; je nezávislá od správy. HTTP Štátne kódy zostávajú nezmenené.

Stránky aplikácie: zachováva existujúce stránky 404 Odpovede; výnimky sa používajú 404 šablóna, keď je k dispozícii, inak neutrálna stránka. Neutrálne: štandardné správy. prispôsobené: používa text.

Anonymné výnimky 500 dostanú neutrálnu stránku. Podrobnosti vyžadujú oprávnenú identitu Guardian a GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Chýbajúci či chybný Twig použije neutrálne zobrazenie; vlastné chybové controllery potrebujú integráciu. JSON a úspešné obchodné stránky sa nemenia.

| error_mode | Nastavenia |
|---|---|
| native | Stránky aplikácie: zachováva existujúce stránky 404 Odpovede; výnimky sa používajú 404 šablóna, keď je k dispozícii, inak neutrálna stránka. Neutrálne: štandardné správy. prispôsobené: používa text. |
| generic | HTML 400–599 → Zachovajte stránky aplikácie alebo zobrazte neutrálne HTML. Skutočné stavové kódy HTTP sa nemenia. |
| custom | Dostupné iba pre vlastnú neutrálnu správu. Text zostane uložený pri zmene režimu. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | K dispozícii iba so špecifickým statickým odkazom. |
| error_custom_text | Dostupné iba pre vlastnú neutrálnu správu. Text zostane uložený pri zmene režimu. |

## Zobrazená referencia

Rôzne správy alebo statické referencie môžu odhaliť skutočnú chybu. Kódy HTTP sa nesmú falšovať.

Odkaz sa vzťahuje na Guardian’s neutrálne stránky: náhodné generuje nový kód, nie náhodnú správu.

Voľby sa kontrolujú na serveri pred uložením.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Sieť a implementácia

IP povolený zoznam prijíma IPv4, IPv6 a CIDR sieť - kontroly Symfony spoľahlivá konfigurácia proxy predtým, než ju umožní. HTTPS Pre bezpečné cookies. Guardian Nepreparuje vystavené webové servery alebo databázy.

Odmietnite diagnostiku, ak nemožno overiť identitu, rolu, reláciu, sieť, rozvrh, JIT alebo uzamknutie. Prehliadač neurčuje expiráciu. Zber a prehliadanie sú oddelené. Pamätanie zariadenia neoveruje identitu. Núdzový prístup vyžaduje identitu, spotrebovaný kód a trvalý audit; kritická chyba auditu bráni otvoreniu. Neplatné nastavenie neaplikujte čiastočne. Voliteľné kontroly nevyžadujú vzdialenú službu.
