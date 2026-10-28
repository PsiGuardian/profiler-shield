# Sign-ins a hesla

[Obsah dokumentace](README.md)


## Účty a první sign-in

Vítejte v Guardian. Vaše původní heslo je dočasné. Vyberte si novou tajemství: panel a diagnostiky zůstanou zavřené, dokud to neuděláte.

Guardian Účty jsou odděleny od účtů na místě. počáteční účet a nové účty musí změnit své hesla před pokračováním. bezpečnostní role zobrazení umožňují informace bez profilu přístupu. Změna úlohy nebo hesla zruší předchozí zasedání.

Připraví soukromou cestu, klíč, úložiště, soubory Symfony a správce, pokud chybí. Zachová účty. Odmítá produkci. Po přímém spuštění vymažte cache a spusťte kontroly.


```bash
php bin/console guardian:access --env=dev
```


| Požadavky na ušetřené heslo | Minimální délka (Unicode Charaktery ) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Politika hesla

Tyto pravidla se vztahují na vytvoření a změny hesla, na webu a v CLINeodhalují ani neprozkoumají stávající hesla.

Původní hodnota : 15 Použijte unikátní heslo nebo správce hesla. Vyžadované kategorie znaků nezaručují nepředvídatelné heslo.

na tomto serveru, Guardian automaticky používá Argon2id pro nové hesla. zachovat své PHP Podpora je povolená. limit aplikace 512 UTF-8 a byty.

Tohle PHP Server neposkytuje Argon2id. Guardian Proto se používá bcrypt Odmítá nadměrné hesla 72 UTF-8 Požádejte svého správce systému, aby povolil Argon2id Podpora v PHP.

Tato možnost okamžitě zruší stávající sezení, včetně vašeho. účty musí změnit své hesla před nastavením Guardian nebo otevření diagnostiky.

Změní heslo existujícího username skrytým zadáním podle politiky, zruší všechny jeho relace a zaznamená akci. Heslo nezadávejte jako argument.

## Progresivní zámky

Každá fáze definuje omezení selhání, počítání okna, trvání blokování a opakování okna. 10 Neúspěchy vyvolávají A 10-minute lockout. opakované selhání může vyvolat další fázi. kontroly pokrývají jak účet a původ sítě, včetně potvrzení správce.

Šifrované čítače jsou nezávislé podle normalizovaného účtu a zdroje; surové IP se neukládají. Atomická rezervace předchází hashování a vyprší po 120 sekundách. Opuštěná rezervace je chyba, pozdní výsledek nevytvoří relaci. Neplatné CSRF nic nerezervuje. Úspěšné přihlášení maže chyby účtu, zachová historii opakování a zdroje. Při 10000 aktivních záznamech se nové identity odmítají místo vytlačení blokací.

Po uzavření, nové selhání v opakovaném okně použijte další fázi. Zvenku tohoto okna se první fáze znovu aplikuje. Poslední fáze se opakuje. Blokované požadavky neprodlouží zpoždění. 8 Místní rehabilitace - místní rehabilitace: guardian:login:unlock --all.

| Fáze | Požadavky na ušetřené heslo |
|---|---|
| Neúspěchy před blokováním | 1–100 |
| Závěrečná doba (minuty) | 1–10080 |
| Výpočet oken (minuty) | 1–1440 |
| Opakujte okno po vypnutí (minuty) | 1–10080 |
| Fáze | 1–8; block[n+1] ≥ block[n] |
| Výchozí hodnota | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Exhaustive-search náklady laboratoř

Porovnejte rovnoměrně náhodné tajemství pevné délky bez zadání reálného hesla. Úrokové sazby a ceny hardwaru jsou přizpůsobitelné předpoklady.

Disjunktní kategorie ASCII a BigInt. Inkluze-exkluze počítá přesný prostor; chybějící kategorie či malá délka dávají nulu. Průměrné pořadí bez vracení: (N+1)/2. Očekávané čekání zahrnuje prahy a opakovanou poslední úroveň. Pokud rychlost nesplňuje okna, odhad se vypne. Offline cena je čas v hodinách krát předpokládaná hodinová cena.

S pevnou abecedou, vyžadování kategorie snižuje počet dodržujících tajemství. Rozšiřovat abecedu je jiná operace. Lokační časy předpokládají nepřetržité odhady dostatečně rychle zůstat v konfiguračních oknech.

Tento model nezměří sílu člověka zvoleného hesla. Vylučuje slovníky, opětovné použití, úniky a cílené strategie. Webové zámky neochraňují ukradené hashy. První offline sazba je ilustrativní, nikoliv referenční. CPU měření : guardian:password:benchmark.

Velké čísla jsou psány v plném, s číslicemi a bez E notace. časy a náklady jsou zaokrouhleny. Porovnejte stejnou řádek pomocí stejných předpokladů.

| K dispozici simulace abecedy | Možné důvěrné tajemství |
|---|---|
| ASCII nižší | 26 |
| ASCII Vrchní | 26 |
| ASCII Digitalizace | 10 |
| ASCII Symboly, které vylučují prostor | 32 |
| Předpokládaná online sazba (guesses/s) | 1 / s |
| Předpokládaná offline sazba (hashes/s) | 10000 / s |
| Náklady na hardware (€ / hodina) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## Recyklace a ověření

Zálohujte úložiště a klíč společně a vyzkoušejte obnovu. Ztracené klíče se automaticky neobnovují. Po odstranění kontrolujte profiler jen neveřejně. Produkce: APP_DEBUG=0, WebProfilerBundle a sběr vypnuté. Místní řetězec auditu nedokazuje integritu napadeného hostitele.

Vynuluje čítače username. --ip ADRESA odstraní i čítač daného IPv4/IPv6 zdroje; jinak může síťové blokování zůstat. --all vymaže všechny čítače. Účty, hesla a relace se nemění; akce se zaznamená.

[Přehled příkazů](commands.md)
