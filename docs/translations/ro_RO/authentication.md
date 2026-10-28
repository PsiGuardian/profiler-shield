# Sign-ins și parole

[Cuprinsul documentației](README.md)


## Conturi și prima înregistrare

Bine ați venit la Guardian. parola dvs. inițială este temporară. alege un nou secret: panourile și diagnosticele rămân închise până când faceți acest lucru. fișierul de credență inițial este șters după validare.

Guardian Conturile sunt separate de conturile site-ului. contul inițial și noile conturi trebuie să își schimbe parolele înainte de a continua. rolul de securitate vizualizare permite informații fără acces la profiluri. Schimbarea unui rol sau parola invalidă sesiunile anterioare.

Pregătește calea privată, cheia, seiful, fișierele Symfony și un administrator dacă lipsește. Păstrează conturile existente. Refuză producția. După apel direct, goliți cache-ul și executați verificările.


```bash
php bin/console guardian:access --env=dev
```


| Cerințe de parolă salvate | Lungimea minimă (Unicode personajele) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Politica de parole

Aceste reguli se aplică creării și modificărilor de parolă, pe web și în CLINu dezvăluie sau inspectează parolele existente.

Valoarea inițială: 15 Caracterele. folosiți o frază unică sau un manager de parole. categoriile de caractere necesare nu garantează o parolă imprevizibilă.

pe acest server, Guardian Utilizarea automată Argon2id pentru noi parole. păstrați-le PHP suport activat. limitele de aplicare sunt 512 UTF-8 în byte.

Această PHP Serverul nu oferă Argon2id. Guardian Prin urmare, se utilizează bcrypt și respinge parolele care depășesc 72 UTF-8 bits. întrebați administratorul dvs. de sistem să permită Argon2id sprijin în PHP.

Această opțiune revocă imediat sesiunile existente, inclusiv cele ale dvs. Conturile trebuie să-și schimbe parolele înainte de a configura Guardian sau deschiderea diagnosticelor.

Schimbă parola contului username existent prin introducere ascunsă conform politicii, revocă toate sesiunile și înregistrează acțiunea. Nu transmiteți parola ca argument.

## Locuri progresive

Fiecare etapă definește o limită de eșec, numărarea ferestrei, durata de blocare și fereastra repetată. 10 Eșecul cauzează o 10-minute lockout. eșecuri repetate pot declanșa următoarea etapă. checurile acoperă atât contul cât și originea rețelei, inclusiv confirmările administratorului.

Contoare cifrate independente pentru cont normalizat și origine; IP brute nu sunt păstrate. O rezervare atomică precede hash-ul și expiră după 120 secunde. Rezervările abandonate sunt eșecuri; rezultatele întârziate nu creează sesiuni. CSRF invalid nu rezervă nimic. Conectarea reușită șterge eșecurile contului, păstrând istoricul recidivei și originii. La 10000 intrări active se refuză identități noi, fără eliminarea blocărilor.

Etapele se desfășoară în ordine.După un blocaj, noi eșecuri din fereastra de repetare utilizează următoarea etapă. În afara acelei ferestre, prima etapă este aplicată din nou. Ultima etapă se repetă. Cererile blocate nu extind întârzierea. 8 Răspunsuri la: Recuperarea locală guardian:login:unlock --all.

| Etapa | Cerințe de parolă salvate |
|---|---|
| Eșecuri înainte de lockout | 1–100 |
| Cuvânt cheie (minute) | 1–10080 |
| Cumpărarea ferestrelor (minute) | 1–1440 |
| Repetă fereastra după dezactivare (minute) | 1–10080 |
| Etapa | 1–8; block[n+1] ≥ block[n] |
| Valoare inițială | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Laboratorul de costuri exhaustive

Comparați în mod uniform secretele aleatorii de lungime fixă fără a introduce o parolă reală. ratele de calcul și prețurile hardware sunt presupuneri ajustabile. Nu se trimit încercări de înregistrare.

Categorii ASCII disjuncte și BigInt. Incluziunea-excluziunea calculează spațiul exact; categorii absente sau lungime insuficientă dau zero. Rang mediu fără înlocuire: (N+1)/2. Așteptarea include praguri și ultima etapă repetată. Estimarea se dezactivează dacă debitul nu respectă ferestrele. Costul offline este numărul de ore înmulțit cu prețul orar presupus.

Cu un alfabet fix, necesitarea unei categorii reduce numărul de secrete corespunzătoare. extinderea alfabetului este o operațiune diferită. timpurile de blocare presupun guesses continuu suficient de repede pentru a rămâne în fereastra configurată.

Acest model nu măsoară puterea unei parole aleasă de om. exclude dicționare, reutilizare, scurgeri și strategii țintă. blocajele web nu protejează hash-urile furate. rata inițială offline este ilustrativă, nu un indicator de referință. CPU măsurătorie : guardian:password:benchmark.

Numerele mari sunt scrise în întregime, cu gruparea de cifre și fără notare E. Timpurile și costurile sunt rotundă.

| Simularea alfabetului disponibil | Posibile secrete compatibile |
|---|---|
| ASCII Cazuri inferioare | 26 |
| ASCII înălţime | 26 |
| ASCII numere | 10 |
| ASCII Simboluri care exclud spațiul | 32 |
| Rata acceptată online (guisses/s) | 1 / s |
| Rata acceptată offline (hashes/s) | 10000 / s |
| Costul de hardware (€ / oră) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## Recuperarea și verificarea

Salvați seiful și cheia împreună și testați restaurarea. Cheile pierdute nu se regenerează automat. După eliminare verificați profilerul numai privat. Producție: APP_DEBUG=0, WebProfilerBundle și colectarea dezactivate. Un lanț local nu dovedește integritatea unei gazde compromise.

Resetează contoarele username. --ip ADRESĂ șterge și contorul acelei origini IPv4/IPv6; altfel blocarea de rețea poate rămâne. --all șterge toate contoarele. Conturile, parolele și sesiunile nu se schimbă; acțiunea se înregistrează.

[Referința comenzilor](commands.md)
