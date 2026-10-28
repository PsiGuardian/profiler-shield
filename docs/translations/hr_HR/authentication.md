# Sign-ins i lozinke

[Sadržaj dokumentacije](README.md)


## Sljedeći Članak I prvi sign-in

Vaša početna lozinka je privremena. Izaberite novu tajnu: panel i dijagnostike ostaju zatvoreni sve dok to ne učinite.

Guardian računi su odvojeni od web-mjesta. početni račun i novi računi moraju promijeniti svoje lozinke prije nastavka. sigurnosna uloga prikazuje informacije bez pristupa profilu. Promjena uloge ili lozinka otkazuje prethodne sjednice.

Priprema privatnu putanju, ključ, trezor, Symfony datoteke i administratora ako nedostaje. Čuva račune. Odbija produkciju. Nakon izravnog pokretanja očistite predmemoriju i provedite provjere.


```bash
php bin/console guardian:access --env=dev
```


| Smanjene zahtjeve za lozinku | Minimalna duljina (Unicode likovi ) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Politika lozinke

Ova se pravila primjenjuju na stvaranje i promjene lozinke, na web-u i u CLINe otkrivaju ili provjeravaju postojeće lozinke.

Početna vrijednost : 15 Koristite jedinstvenu lozinku ili upravitelja lozinkom. Potrebne kategorije znakova ne jamče nepredvidljivo lozinko.

Na ovom serveru, Guardian Automatski se koristi Argon2id za nove lozinke. zadržite svoje PHP dopušteno podnošenje zahtjeva; ograničenje zahtjeva je 512 UTF-8 u bajtu.

ovo PHP Službenik ne pruža Argon2id. Guardian Stoga se koristi bcrypt Odbijanje lozinke prekomjerno 72 UTF-8 Zatražite od administratora sustava da omogućuje Argon2id Podrška u PHP.

Ova opcija odmah otkaže postojeće sjednice, uključujući i vaše. računi moraju promijeniti svoje lozinke prije nego što konfiguriraju Guardian ili otvaraju dijagnoze.

Mijenja lozinku postojećeg username skrivenim unosom prema pravilima, opoziva sve sesije i bilježi radnju. Lozinku ne prosljeđujte kao argument.

## Progresivni lukovi

Svaka faza definira ograničenje neuspjeha, računanje prozora, trajanje zatvaranja i ponavljanje prozora. 10 Neuspjeh izaziva A 10Ponavljene neuspjehe mogu pokrenuti sljedeću fazu. provjere obuhvaćaju i račun i izvor mreže, uključujući potvrde administratora.

Šifrirani brojači neovisni su po normaliziranom računu i izvoru; sirove IP adrese ne spremaju se. Atomska rezervacija prethodi hashu i istječe nakon 120 sekundi. Napuštene rezervacije broje se kao pogreške; zakašnjeli rezultati ne stvaraju sesije. Nevaljan CSRF ništa ne rezervira. Uspješna prijava briše pogreške računa, čuva povijest ponavljanja i izvora. Pri 10000 aktivnih zapisa odbijaju se novi identiteti umjesto brisanja blokada.

Faza je u redoslijedu. nakon zatvaranja, nove neuspjehe unutar prozora ponavljanja koriste sljedeću fazu. izvan tog prozora, prva faza se ponovno primjenjuje. posljednja faza ponavlja. blokirane zahtjeve ne produljuju kašnjenje. 8 Mjesta lokalnog oporavka: guardian:login:unlock --all.

| stupanj | Smanjene zahtjeve za lozinku |
|---|---|
| Neuspjeh prije zatvaranja | 1–100 |
| Sljedeći članakSvijet (minute) | 1–10080 |
| Izračun prozora (minute) | 1–1440 |
| Ponovite prozor nakon otvaranja (minute) | 1–10080 |
| stupanj | 1–8; block[n+1] ≥ block[n] |
| Početna vrijednost | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Laboratorij troškova ispitnog istraživanja

Jednostavno usporedite slučajne tajne fiksne duljine bez uvođenja pravog lozinke. ocjene i cijene hardvera su prilagodljive pretpostavke.

Odvojene ASCII kategorije i BigInt. Uključivanje-isključivanje daje točan prostor; nedostajuća kategorija ili premala duljina daju nulu. Prosječan rang bez vraćanja: (N+1)/2. Očekivano čekanje uključuje pragove i ponavljanu zadnju razinu. Ako brzina ne stane u prozore, procjena se isključuje. Offline trošak je broj sati puta pretpostavljena satnica.

Uz fiksnu abecedu, zahtijevajući kategoriju smanjuje broj odgovarajućih tajni. proširenje abeceda je drugačiji postupak. Lokacijski vremenski uvjeti poduzimaju kontinuirane pretpostavke dovoljno brzo da ostanu unutar konfiguriranih prozora.

Ovaj model ne mjeri snagu ljudski odabranog lozinka. isključuje rječnike, ponovnu upotrebu, iscjedke i ciljane strategije. web blokade ne štite ukradene hashes. početna offline stopa je ilustrativna, a ne referentna. CPU Mjera je: guardian:password:benchmark.

Veliki brojevi su napisani u punom obliku, s grupiranjem brojeva i bez E-notation.Times i troškovi su okrugli.Usporedite istu redovicu koristeći iste pretpostavke.

| Dostupna simulacija alfabeta | Moguće komplementarne tajne |
|---|---|
| ASCII Smanjenje | 26 |
| ASCII Uskrsni | 26 |
| ASCII Dijagnostika | 10 |
| ASCII Simboli koji isključuju prostor | 32 |
| Prihvaćena internetska stopa (guesses/s) | 1 / s |
| Prihvaćena offline stopa (hashes/s) | 10000 / s |
| Prihvaćeni troškovi hardvera (€ / sat) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## Oporavak i provjera

Zajedno kopirajte trezor i ključ te testirajte obnovu. Izgubljeni ključevi ne stvaraju se automatski. Nakon uklanjanja provjerite profiler samo privatno. Produkcija: APP_DEBUG=0, WebProfilerBundle i prikupljanje isključeni. Lokalni auditni lanac ne dokazuje integritet kompromitiranog hosta.

Poništava brojače username. --ip ADRESA briše i brojač tog IPv4/IPv6 izvora; inače mrežna blokada može ostati. --all briše sve brojače. Računi, lozinke i sesije ne mijenjaju se; radnja se bilježi.

[Pregled naredbi](commands.md)
