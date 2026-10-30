# Sign-ins en wachtwoorden

[Documentatie-inhoud](README.md)


## Accounts en eerste sign-in

Welkom bij Guardian. uw oorspronkelijke wachtwoord is tijdelijk. kies een nieuw geheim: het panel en de diagnose blijven gesloten totdat u dat doet.

Guardian accounts zijn gescheiden van site-accounts. het initiële account en nieuwe accounts moeten hun wachtwoorden wijzigen voordat ze verder gaan. de beveiligingsrollen zien informatie zonder profielen toegang toestaan. het veranderen van een rol of wachtwoord invalideert eerdere sessies.

Bereidt privépad, sleutel, kluis, Symfony-bestanden en zo nodig een beheerder voor. Behoudt accounts. Weigert productie. Wis na een directe aanroep de cache en voer de controles uit.


```bash
php bin/console guardian:access --env=dev
```


| Bespaarde wachtwoordvereisten | Minimale lengte (Unicode De personages) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Passwordbeleid

Deze regels zijn van toepassing op het creëren en wijzigen van wachtwoorden, op het web en in de CLIZe onthullen of inspecteren niet bestaande wachtwoorden.

Eerste waarde : 15 personages. gebruik een unieke wachtwoord of een wachtwoordenbeheerder. vereiste karaktercategorieën garanderen geen onvoorspelbare wachtwortel.

Op deze server, Guardian automatisch gebruikt Argon2id voor nieuwe wachtwoorden. houd het PHP ondersteuning geaccepteerd. de aanvraaggrens is 512 UTF-8 en bytes.

Dit is PHP De server biedt geen Argon2id. Guardian Daarom gebruikt bcrypt en weigert wachtwoorden overschrijden 72 UTF-8 bits. vraagt uw systeemadministrator om Argon2id Ondersteuning in PHP.

Deze optie annuleert onmiddellijk bestaande sessies, met inbegrip van uw. Accounts moeten hun wachtwoorden wijzigen voordat ze Guardian configureren of diagnoses openen.

Wijzigt het wachtwoord van bestaand account username met verborgen invoer volgens het beleid, trekt alle sessies in en registreert de actie. Geef het wachtwoord nooit als argument.

## Progressieve lock

Elk stadium definieert een foutgrens, tellen venster, lockout duur en herhalen venster. 10 Fouten veroorzaken een 10-minute lockout. herhaalde mislukkingen kunnen de volgende fase triggeren. checks omvatten zowel account en netwerk oorsprong, met inbegrip van administrator bevestigen.

Versleutelde tellers zijn onafhankelijk per genormaliseerd account en oorsprong; ruwe IP-adressen worden niet bewaard. Een atomaire reservering gaat vooraf aan hashing en verloopt na 120 seconden. Verlaten reserveringen tellen als fout; late resultaten maken geen sessie. Ongeldige CSRF reserveert niets. Succesvol aanmelden wist accountfouten maar behoudt herhalings- en oorspronggeschiedenis. Bij 10000 actieve regels worden nieuwe identiteiten geweigerd in plaats van blokkades te verwijderen.

Stappen lopen in volgorde.Na een lockout, nieuwe mislukkingen binnen het herhalingsvenster gebruiken de volgende fase. Buiten dat venster, wordt de eerste fase opnieuw toegepast. De laatste fase herhaalt.Blokkeerde verzoeken verlengen de vertraging niet.Maximum 8 Stappen. plaatselijke herstel: guardian:login:unlock --all.

| Stap | Bespaarde wachtwoordvereisten |
|---|---|
| Mislukkingen vóór lockout | 1–100 |
| Lockout (minuten van tijd) | 1–10080 |
| Het tellen van het raam (minuten) | 1–1440 |
| Herhaal het venster na het ontgrendelen (minuten) | 1–10080 |
| Stap | 1–8; block[n+1] ≥ block[n] |
| Beginwaarde | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Exhaustive-search kosten laboratorium

Vergelijk uniform willekeurige geheimen van een vaste lengte zonder een echte wachtwoord in te voeren. Geeft tarieven en hardware prijzen zijn aanpassbare aannames. Geen sign-in pogingen worden verzonden.

Disjuncte ASCII-categorieën en BigInt. Inclusie-exclusie berekent de exacte zoekruimte; ontbrekende categorieën of te korte lengte geven nul. Gemiddelde rang zonder teruglegging: (N+1)/2. De verwachte wachttijd omvat drempels en de herhaalde laatste stap. Past het tempo niet binnen de vensters, dan vervalt de schatting. Offlinekosten zijn uren maal veronderstelde uurprijs.

Met een vaste alfabet, het vereisen van een categorie vermindert het aantal compliant geheimen. Het uitbreiden van de alfabet is een andere operatie. Lockout tijden veronderstellen continu getuigenissen snel genoeg om binnen geconfigureerde ramen te blijven.

Dit model meet niet de sterkte van een door mensen gekozen wachtwoord. Het uitsluit woordenboek, hergebruik, leaks en gerichte strategieën. Web lockuts beschermen geen gestolen hashes. De oorspronkelijke offline tarieven zijn illustratief, niet een benchmark. CPU De meting: guardian:password:benchmark.

Grote getallen zijn geschreven in volledige, met numerieke groepering en zonder E notatie. tijden en kosten worden omringd. vergelijk dezelfde volgorde met dezelfde aannames.

| Simulatie Alphabet beschikbaar | Mogelijke compliant geheimen |
|---|---|
| ASCII LowerCase | 26 |
| ASCII Hoofdstuk | 26 |
| ASCII Digitale | 10 |
| ASCII Symbolen die ruimte uitsluiten | 32 |
| Aangebracht online tarief (guesses/s) | 1 / s |
| Aanstaande offline tarieven (hashes/s) | 10000 / s |
| Gebruikte hardware kosten (€ / uur) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## Herstel en verificatie

Maak samen een back-up van kluis en sleutel en test herstel. Verloren sleutels worden nooit automatisch hermaakt. Controleer na verwijdering de profiler alleen privé. Productie: APP_DEBUG=0, WebProfilerBundle en verzameling uit. Een lokale auditketen bewijst geen integriteit op een gecompromitteerde host.

Zet de tellers van username terug. --ip ADRES wist ook de teller van die IPv4/IPv6-oorsprong; anders kan de netwerkblokkade blijven. --all wist alle tellers. Accounts, wachtwoorden en sessies blijven gelijk; de actie wordt geregistreerd.

[Commando-overzicht](commands.md)
