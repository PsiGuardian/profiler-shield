# Sign-ins a heslá

[Obsah dokumentácie](README.md)


## Účty a prvý sign-in

Vaše počiatočné heslo je dočasné.Vyberte si novú tajomstvo: panel a diagnostiky zostanú zatvorené, kým to neurobíte.

Guardian Účty sú oddelené od účtov lokality. Počiatočný účet a nové účty musia zmeniť svoje heslá pred pokračovaním. Bezpečnostné úlohy zobrazenia umožňujú informácie bez prístupu k profilom. Zmena úlohy alebo hesla neplatí predchádzajúce relácie.

Pripraví súkromnú cestu, kľúč, úložisko, súbory Symfony a správcu, ak chýba. Zachová účty. Odmieta produkciu. Po priamom spustení vymažte cache a vykonajte kontroly.


```bash
php bin/console guardian:access --env=dev
```


| Ušetrené požiadavky na heslo | Minimálna dĺžka (Unicode Charaktery ) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Politika hesla

Tieto pravidlá sa vzťahujú na vytvorenie a zmeny hesla, na webe a v CLINeodhaľujú ani neinspektujú existujúce heslá.

Počiatočná hodnota : 15 znaky. Použite jedinečnú heslo alebo správcu hesla. Požadované kategórie znakov nezaručujú nepredvídateľné heslo.

na tomto serveri, Guardian automaticky používa Argon2id pre nové heslá. udržať svoje PHP podpora je povolená. limit aplikácie je 512 UTF-8 v bytoch

Toto PHP Server neposkytuje Argon2id. Guardian Preto používa bcrypt a odmieta heslá presahujúce 72 UTF-8 byty. požiadajte svojho správcu systému, aby umožnil Argon2id Podpora v PHP.

Táto možnosť okamžite zruší existujúce relácie, vrátane vašich. účty musia zmeniť svoje heslá pred nastavením Guardian alebo otvorenie diagnostiky.

Zmení heslo existujúceho username skrytým zadaním podľa politiky, zruší všetky jeho relácie a zaznamená akciu. Heslo neuvádzajte ako argument.

## Progresívne bloky

Každá fáza definuje limit zlyhania, počítanie okien, trvanie zablokovania a opakovanie okna. 10 Zlyhanie vyvoláva A 10-minute lockout. opakované chyby môžu spôsobiť ďalšiu fázu. kontroly pokrývajú ako účet a sieťový pôvod, vrátane potvrdenia správcu.

Šifrované počítadlá sú nezávislé podľa normalizovaného účtu a zdroja; surové IP sa neukladajú. Atomická rezervácia predchádza hashovaniu a vyprší po 120 sekundách. Opustená rezervácia je chyba, neskorý výsledok nevytvorí reláciu. Neplatné CSRF nič nerezervuje. Úspešné prihlásenie maže chyby účtu, zachová históriu opakovania a zdroja. Pri 10000 aktívnych záznamoch sa nové identity odmietnu namiesto vytlačenia blokovaní.

Etapy prebiehajú v poradí. Po blokovaní sa nové zlyhania v opakovanom okne používajú na ďalšiu fázu. Okrem tohto okna sa prvý krok opäť uplatňuje. Posledný stupeň sa opakuje. Zablokované požiadavky nezväčšujú oneskorenie. 8 Východiská a lokálne zotavenie: guardian:login:unlock --all.

| štádium | Ušetrené požiadavky na heslo |
|---|---|
| Neúspechy pred blokovaním | 1–100 |
| Príslušenstvo ( minúty ) | 1–10080 |
| Výpočet okien (minúty) | 1–1440 |
| Opakujte okno po odomknutí (minúty) | 1–10080 |
| štádium | 1–8; block[n+1] ≥ block[n] |
| Predvolená hodnota | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Exhaustive-search náklady laboratórium

Porovnajte rovnomerne náhodné tajomstvá pevnej dĺžky bez toho, aby ste zadali skutočné heslo. Úrokové sadzby a ceny hardvéru sú prispôsobiteľné predpoklady. Žiadne pokusy o prihlásenie nie sú odoslané.

Disjunktné kategórie ASCII a BigInt. Inklúzia-exklúzia počíta presný priestor; chýbajúca kategória či malá dĺžka dávajú nulu. Priemerné poradie bez vrátenia: (N+1)/2. Očakávané čakanie zahŕňa prahy a opakovanú poslednú úroveň. Ak rýchlosť nespĺňa okná, odhad sa vypne. Offline cena je čas v hodinách krát predpokladaná hodinová cena.

S pevnou abecedou, vyžadovanie kategórie znižuje počet spĺňajúcich tajomstiev. Rozšírenie abecedy je iná operácia. Časy zablokovania predpokladajú nepretržité hádanky dostatočne rýchlo zostať v konfigurovaných oknách.

Tento model nezmerá silu ľudsky vybraného hesla. Vylučuje slovníky, opätovné použitie, úniky a cielené stratégie. Webové zámky neochránia ukradnuté hashy. Počiatočná offline sadzba je ilustratívna, nie referenčný znak. CPU meranie : guardian:password:benchmark.

Veľké čísla sú napísané v plnej, s číslicovými skupinami a bez E notácie. časy a náklady sú okrúhle. Porovnajte rovnaký riadok pomocou tých istých predpokladov.

| K dispozícii simulácia abecedy | Možné tajomstvá |
|---|---|
| ASCII nižšie | 26 |
| ASCII Vyššie | 26 |
| ASCII Digitálne | 10 |
| ASCII symboly, ktoré vylučujú priestor | 32 |
| Predpokladaná online sadzba (guesses/s) | 1 / s |
| Predpokladaná offline sadzba (hashes/s) | 10000 / s |
| Predpokladané náklady na hardvér (€/hodina) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## Recyklácia a overovanie

Zálohujte úložisko a kľúč spolu a vyskúšajte obnovu. Stratené kľúče sa automaticky neobnovujú. Po odstránení kontrolujte profiler len neverejne. Produkcia: APP_DEBUG=0, WebProfilerBundle a zber vypnuté. Miestny reťazec auditu nedokazuje integritu napadnutého hostiteľa.

Vynuluje počítadlá username. --ip ADRESA odstráni aj počítadlo daného IPv4/IPv6 zdroja; inak môže sieťové blokovanie zostať. --all vymaže všetky počítadlá. Účty, heslá a relácie sa nemenia; akcia sa zaznamená.

[Prehľad príkazov](commands.md)
