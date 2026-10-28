# Sign-ins e password

[Indice della documentazione](README.md)


## Conti e prima iscrizione

Il tuo password iniziale è temporaneo. Scegli un nuovo segreto: il pannello e i diagnostici rimangono bloccati fino a quando non lo fai.

Guardian Gli account sono separati dagli account del sito.L'account iniziale e i nuovi conti devono cambiare le loro password prima di continuare.Il ruolo di sicurezza visualizza informazioni consentite senza accesso al profilo.Cambiare un ruolo o una password invalida le sessioni precedenti.

Prepara percorso privato, chiave, archivio, file Symfony e un amministratore se manca. Conserva gli account esistenti. Rifiuta la produzione. Dopo l’esecuzione diretta, svuotare la cache ed eseguire i controlli.


```bash
php bin/console guardian:access --env=dev
```


| Requisiti di password salvate | Lunghezza minima (Unicode dei personaggi) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Politica di password

Tali regole si applicano alla creazione e alle modifiche della password, sul web e nel CLINon rivelano né verificano password esistenti.

Il valore iniziale: 15 personaggi. usare una password unica o un manager di password. Le categorie di caratteri richieste non garantiscono una password imprevedibile.

In questo server, Guardian Utilizzare automaticamente Argon2id per nuove password. mantenere la sua PHP L’applicazione è limitata: il limite di applicazione 512 UTF-8 di byte.

Questo PHP Il server non offre Argon2id. Guardian Pertanto, utilizza bcrypt e rifiuta le password superando 72 UTF-8 Richiedi al tuo amministratore di sistema di consentire Argon2id Il sostegno in PHP.

Questa opzione revoca immediatamente le sessioni esistenti, comprese le vostre. Gli account devono cambiare le loro password prima di configurare Guardian o aprire la diagnosi.

Cambia la password dell’account username esistente mediante input nascosto conforme alla politica, revoca tutte le sessioni e registra l’azione. Non passare password come argomento.

## Il blocco progressivo

Ogni fase definisce un limite di fallimento, la finestra di conteggio, la durata del blocco e la rivista della finestra. 10 L’insufficienza provoca un 10-minute lockout. gli errori ripetuti possono scatenare la fase successiva. i controlli coprono sia l'account che l'origine della rete, comprese le conferme dell'amministratore.

Contatori cifrati indipendenti per account normalizzato e origine; IP grezzi non conservati. Una prenotazione atomica precede l’hash e scade dopo 120 secondi. Prenotazioni abbandonate contano come errori; risultati tardivi non creano sessioni. CSRF non valido non prenota nulla. Un accesso riuscito azzera errori account ma conserva recidiva e origine. A 10000 voci attive si rifiutano nuove identità invece di eliminare blocchi.

Le fasi vengono eseguite in ordine.Dopo un blocco, nuovi fallimenti all'interno della finestra di ripetizione utilizzano la fase successiva.Al di fuori di quella finestra, la prima fase si applica nuovamente.L'ultima fase si ripete.Le richieste bloccate non prolungano il ritardo. 8 Stagioni di recupero locale: guardian:login:unlock --all.

| Stagione | Requisiti di password salvate |
|---|---|
| Il fallimento prima del lockout | 1–100 |
| L’intervento (minuti di lavoro) | 1–10080 |
| Calcolo della finestra (minuti) | 1–1440 |
| Ripetere la finestra dopo la disattivazione (minuti) | 1–10080 |
| Stagione | 1–8; block[n+1] ≥ block[n] |
| Valore iniziale | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Laboratorio di costi di ricerca

Confronta in modo uniforme i segreti casuali di una lunghezza fissa senza inserire un vero password. Le tasse di scoperta e il prezzo del hardware sono ipotesi adeguabili. Non vengono inviati tentativi di iscrizione.

Categorie ASCII disgiunte e BigInt. Inclusione-esclusione calcola lo spazio esatto; categorie mancanti o lunghezza insufficiente danno zero. Rango medio senza reinserimento: (N+1)/2. L’attesa considera soglie e ultimo livello ripetuto. La stima si disattiva se la velocità non rispetta le finestre. Costo offline: ore per costo orario ipotizzato.

Con un alfabeto fisso, richiedendo una categoria riduce il numero di segreti conformi. espandere l'alfabeto è un'operazione diversa. tempi di lockout assume ipotesi costanti abbastanza veloci per rimanere all'interno delle finestre configurate.

Questo modello non misura la forza di una password scelto dall'uomo. Esclude dizionari, riutilizzo, escursioni e strategie mirate. I blocchi web non proteggono i hash rubati. Il tasso iniziale offline è illustrativo, non un riferimento. CPU La misurazione: guardian:password:benchmark.

I numeri di grandi dimensioni sono scritti in pieno, con gruppi digitali e senza notazione E. Il tempo e i costi sono rotondi.

| Simulazione alphabet disponibile | Possibili segreti conformi |
|---|---|
| ASCII Il caso inferiore | 26 |
| ASCII di Uppercase | 26 |
| ASCII digitali | 10 |
| ASCII I simboli che escludono lo spazio | 32 |
| Tasso assegnato online (guess/s) | 1 / s |
| Tasso assegnato offline (hashes/s) | 10000 / s |
| Costo di hardware assegnato (€/ora) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## Ricostruzione e verifica

Salvare archivio e chiave insieme e provare il ripristino. Le chiavi perse non vengono rigenerate automaticamente. Dopo la rimozione verificare il profiler solo in privato. Produzione: APP_DEBUG=0, WebProfilerBundle e raccolta disattivati. La catena locale non prova integrità su host compromessi.

Azzera i contatori di username. --ip INDIRIZZO elimina anche quello dell’origine IPv4/IPv6; altrimenti il blocco di rete può persistere. --all azzera tutti i contatori. Account, password e sessioni restano invariati; l’azione viene registrata.

[Riferimento dei comandi](commands.md)
