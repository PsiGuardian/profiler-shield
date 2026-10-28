# Veřejné chybové stránky

[Obsah dokumentace](README.md)


## Veřejné chybové stránky

Zachovejte stránky aplikace nebo zobrazte neutrální HTML. Skutečné stavové kódy HTTP se nemění.

Nativní móda dovoluje Symfony Generický režim ignoruje přizpůsobený text. Pouze přizvolený režim používá poselství návštěvníka. Veřejný odkaz může být nepřítomný, náhodný nebo fixní; je nezávislý na zprávě. HTTP Statusové kódy zůstávají nezměněny.

Stránky aplikace: zachovává existující webové stránky 404 Odpovědi; výjimky používají 404 šablona, když je k dispozici, jinak neutrální stránka. Neutral: standardní zpráva.

Anonymní výjimky 500 dostanou neutrální stránku. Podrobnosti vyžadují oprávněnou identitu Guardian a GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Chybějící či vadný Twig použije neutrální zobrazení; vlastní chybové controllery potřebují integraci. JSON a úspěšné obchodní stránky se nemění.

| error_mode | Nastavení |
|---|---|
| native | Stránky aplikace: zachovává existující webové stránky 404 Odpovědi; výjimky používají 404 šablona, když je k dispozici, jinak neutrální stránka. Neutral: standardní zpráva. |
| generic | HTML 400–599 → Zachovejte stránky aplikace nebo zobrazte neutrální HTML. Skutečné stavové kódy HTTP se nemění. |
| custom | Dostupné pouze pro vlastní neutrální zprávu. Text zůstane uložen při změně režimu. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | K dispozici pouze s přizpůsobeným statickým odkazem. |
| error_custom_text | Dostupné pouze pro vlastní neutrální zprávu. Text zůstane uložen při změně režimu. |

## Zobrazený odkaz

Různé zprávy nebo statické odkazy mohou prozradit skutečnou chybu. Kódy HTTP se nesmějí falšovat.

Odkaz se vztahuje na Guardian‚s neutrální stránky: náhodně generuje nový kód, ne náhodné zprávy.

Volby se před uložením kontrolují na serveru.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Síť a implementace

IP dovolovací seznam přijímá IPv4, IPv6 a CIDR Síťové sítě - kontroly Symfony důvěryhodná konfigurace proxy předtím, než ji umožní. HTTPS Pro bezpečné cookies. Guardian neupravuje vystavené webové servery nebo databáze.

Odmítněte diagnostiku, pokud nelze ověřit identitu, roli, relaci, síť, rozvrh, JIT nebo uzamčení. Prohlížeč neurčuje expiraci. Sběr a prohlížení jsou oddělené. Pamatování zařízení neověřuje identitu. Nouze vyžaduje identitu, spotřebovaný kód a trvalý audit; kritická chyba auditu brání otevření. Neplatné nastavení neaplikujte částečně. Volitelné kontroly nevyžadují vzdálenou službu.
