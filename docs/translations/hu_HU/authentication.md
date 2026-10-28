# Sign-ins és jelszavak

[Dokumentáció tartalma](README.md)


## Számlák és első bejelentkezés

Üdvözöljük a Guardian. Az eredeti jelszó ideiglenes. Válasszon egy új titkot: a panel és a diagnosztika marad zárva, amíg nem csinálod.

Guardian A fiókok elválasztják a webhely-fiókokat. Az eredeti számla és az új fiókoknak meg kell változtatniuk jelszavukat, mielőtt folytatódnak. A biztonsági funkció megjelenítése lehetővé teszi az információkat profil hozzáférése nélkül. A szerep vagy jelszó módosítása megszünteti a korábbi üléseket.

Előkészíti a privát útvonalat, kulcsot, tárolót, Symfony-fájlokat és szükség esetén adminisztrátort. Megőrzi a fiókokat. Éles környezetben elutasítja a beállítást. Közvetlen futtatás után törölje a gyorsítótárat és ellenőrizzen.


```bash
php bin/console guardian:access --env=dev
```


| Megtakarított jelszó követelmények | Minimális időtartam (Unicode A karakterek) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## jelszó politika

Ezek a szabályok vonatkoznak a jelszó létrehozására és módosítására, az interneten és a CLINem mutatnak fel vagy ellenőriznek meglévő jelszavakat.

A kezdeti érték: 15 karakterek. használjon egyedi jelszót vagy jelszókezelőt. A szükséges karakterkategóriák nem garantálják a kiszámíthatatlan jelszavat.

Ebben a szerverben, Guardian automatikusan használja Argon2id új jelszavakhoz. tartsa meg a PHP támogatás engedélyezett. a kérelem korlátozása 512 UTF-8 A bajtok

Ez a PHP A szerver nem nyújt Argon2id. Guardian Ezért használja bcrypt Elutasítja a jelszavakat túllépve 72 UTF-8 Kérjük a rendszergazdát, hogy engedélyezze Argon2id Támogatás a PHP.

Ez az opció haladéktalanul visszavonja a meglévő üléseket, beleértve a tiédet is.A fiókoknak módosítaniuk kell jelszóikat, mielőtt konfigurálnák a Guardian-t vagy megnyitják a diagnózist.

Megváltoztatja a létező username fiók jelszavát rejtett bevitellel az aktív szabályzat szerint, visszavon minden munkamenetet és naplózza a műveletet. Jelszót ne adjon argumentumként.

## Progresszív zárak

Minden szakasz meghatározza a hibák korlátozását, számolási ablakot, záróidőket és ismétlődő ablakokat. 10 A kudarcok a 10-minute lockout. ismételt hibák indíthatják a következő szakaszban. ellenőrzések fedezik mind a fiók és a hálózati eredet, beleértve az adminisztrátor megerősítéseit.

Titkosított számlálók külön a normalizált fiókra és forrásra; nyers IP-cím nincs tárolva. A hashelést atomi foglalás előzi meg, amely 120 másodperc után lejár. Elhagyott foglalás hibának számít, késői eredmény nem hoz létre munkamenetet. Hibás CSRF nem foglal. Sikeres belépés törli a fiókhibákat, de megtartja az ismétlés és forrás előzményeit. 10000 aktív bejegyzésnél új azonosítókat utasít el, zárolást nem dob ki.

A fázisok rendben futnak. egy zárolás után az ismétlődő ablakon belüli új hibák a következő lépést használják. azon kívül az első lépés újra érvényesül. Az utolsó lépés megismétlődik. A blokkolt kérések nem hosszabbítják a késést. Maximum 8 Helyi helyreállítási szakaszok: guardian:login:unlock --all.

| Fázis | Megtakarított jelszó követelmények |
|---|---|
| Kudarcok a lockout előtt | 1–100 |
| Székelyföld ( percek és percek ) | 1–10080 |
| Az ablak számlálása (minuták) | 1–1440 |
| Ismételje meg az ablakot a zárás után (minuták) | 1–10080 |
| Fázis | 1–8; block[n+1] ≥ block[n] |
| Kezdeti érték | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Exhaustive-search költség laboratórium

Összehasonlítsa egyenletesen véletlen titkokat egy rögzített hossza anélkül, hogy valós jelszavat adna be. A becslések aránya és a hardverárak árai igazíthatók. Nincs bejelentkezési kísérlet küldve.

Különálló ASCII-kategóriák és BigInt. A szitaformula adja a pontos keresési teret; hiányzó kategória vagy rövid hossz nullát ad. Átlagos rang visszatevés nélkül: (N+1)/2. A várható várakozás figyelembe veszi a küszöböket és az ismétlődő utolsó szintet. Ha a sebesség nem fér az időablakokba, nincs becslés. Offline költség: órák szorozva feltételezett óradíjjal.

A rögzített ábécével a kategóriát igényelve csökkenti az összhangban lévő titkok számát. Az ábécé kiterjesztése más művelet. Lockout idők állandó becsléseket tartanak elég gyorsan, hogy konfigurált ablakokon belül maradjanak.

Ez a modell nem mér egy ember által kiválasztott jelszó erejét. kizárja a szótárakat, az újrafelhasználást, a kimeneteleket és a célzott stratégiákat. A webes zárak nem védik a lopott hasheket. Az eredeti offline arány illusztratív, nem referenciaérték. CPU A mérés: guardian:password:benchmark.

A nagy számok teljes formában vannak írva, digitális csoportosítással és E-jegyzés nélkül. Idők és költségek körkörösek. Összehasonlítsa ugyanazt a sorot azonos feltételezéssel.

| Elérhető szimulációs ábécé | Lehetséges összeegyeztethető titkok |
|---|---|
| ASCII alacsonyabb | 26 |
| ASCII Felső | 26 |
| ASCII Digitális | 10 |
| ASCII Szimbólumok, amelyek kizárják a tér | 32 |
| Elfogadott online árfolyam (guesses/s) | 1 / s |
| Az offline árfolyam (hashes/s) | 10000 / s |
| Hardware költsége (€ / óra) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## helyreállítás és ellenőrzés

Tárolót és kulcsot együtt mentse, próbálja a visszaállítást. Elveszett kulcs nem generálódik újra automatikusan. Eltávolítás után a profilert csak privát környezetben ellenőrizze. Éles környezet: APP_DEBUG=0, WebProfilerBundle és gyűjtés kikapcsolva. Helyi auditlánc nem bizonyít sértetlenséget feltört gépen.

Nullázza username számlálóit. --ip CÍM az adott IPv4/IPv6-forrásét is törli, különben a hálózati tiltás megmaradhat. --all minden számlálót töröl. Fiók, jelszó és munkamenet változatlan; a művelet naplózott.

[Parancsreferencia](commands.md)
