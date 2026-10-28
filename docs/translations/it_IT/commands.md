# Riferimento dei comandi

[Indice della documentazione](README.md)

Eseguire dalla radice Symfony in un terminale affidabile. Gli esempi usano dev. Non inserire password, chiavi o codici di recupero negli argomenti, nei registri o in Git.

## Scoprire i comandi e consultare la guida


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev e --no-interaction sono opzioni Symfony. --no-interaction non fornisce segreti e non va usato per comandi password interattivi. --help mostra la sintassi; -v aggiunge dettagli. Il codice 0 indica il risultato annunciato; --no-admin può riuscire senza portale utilizzabile.

## `vendor/bin/guardian`

Attiva il bundle in dev/test, svuota la cache, esegue guardian:setup --automatic, svuota ancora la cache, poi esegue guardian:doctor e guardian:access. Si ferma al primo errore; ripetere conserva account e chiavi. Rifiuta APP_ENV=prod e richiede proc_open.

Senza argomenti, --help, -h o help mostra solo la guida. setup è l’unica azione e non accetta altre opzioni.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Riferimento dei comandi |
|---|---|
| [`guardian:setup`](#guardiansetup) | Prepara percorso privato, chiave, archivio, file Symfony e un amministratore se manca. |
| [`guardian:install`](#guardianinstall) | Inizializza l’archivio e crea un amministratore con --username, predefinito admin. |
| [`guardian:access`](#guardianaccess) | Mostra il percorso del pannello e la posizione delle credenziali iniziali, mai la password. |
| [`guardian:doctor`](#guardiandoctor) | Verifica archivio, permessi, chiave, percorso, amministratore attivo e configurazione. |
| [`guardian:password:reset`](#guardianpasswordreset) | Cambia la password dell’account username esistente mediante input nascosto conforme alla politica, revoca tutte le sessioni e registra l’azione. |
| [`guardian:mfa:reset`](#guardianmfareset) | Elimina segreto e contatore TOTP di username, revoca le sessioni e registra l’azione. |
| [`guardian:login:unlock`](#guardianloginunlock) | Azzera i contatori di username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Svuota la lista IP e disattiva orari, JIT e accesso remoto d’emergenza. |
| [`guardian:lockdown`](#guardianlockdown) | on chiude i diagnostici ed elimina tutte le sessioni; le pagine aziendali restano disponibili. |
| [`guardian:emergency`](#guardianemergency) | Genera 5 codici monouso per username esistente e sostituisce i precedenti. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Invia gli avvisi in coda a alert_email tramite PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Misura verifiche di hash con un segreto sintetico, mai password utente. |
| [`guardian:uninstall`](#guardianuninstall) | Prepara la rimozione dei file generati attribuibili a Guardian. |

## `guardian:setup`

Prepara percorso privato, chiave, archivio, file Symfony e un amministratore se manca. Conserva gli account esistenti. Rifiuta la produzione. Dopo l’esecuzione diretta, svuotare la cache ed eseguire i controlli.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic genera admin e un segreto in un file privato 0600. --path sceglie un percorso di 25–181 caratteri, senza sostituirne uno esistente. --no-admin salta la creazione dell’amministratore; senza account attivo il portale non è utilizzabile. --username propone il nome interattivo, admin per impostazione predefinita; non rinomina l’account automatico.

```text
--path: ^/[a-zA-Z0-9/_-]{24,180}$
/_profiler; /_wdt → ∅
```

```bash
php bin/console guardian:setup --username=operator --env=dev
php bin/console cache:clear --env=dev
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## `guardian:install`

Inizializza l’archivio e crea un amministratore con --username, predefinito admin. Richiede una password nascosta secondo la politica attiva. Rifiuta account esistenti. Non crea percorsi né configurazione Symfony; usare normalmente l’assistente completo.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Nome utente : 3 per 64 I personaggi alfanumerici,

## `guardian:access`

Mostra il percorso del pannello e la posizione delle credenziali iniziali, mai la password. --base-url accetta l’origine HTTPS reale con porta facoltativa, senza credenziali, query o frammento. Sostituire l’host di esempio.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Verifica archivio, permessi, chiave, percorso, amministratore attivo e configurazione. Restituisce 1 se trova problemi. PHP CLI e PHP-FPM possono usare fusi diversi. Non controlla esposizione Internet o regole proxy.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Cambia la password dell’account username esistente mediante input nascosto conforme alla politica, revoca tutte le sessioni e registra l’azione. Non passare password come argomento.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Elimina segreto e contatore TOTP di username, revoca le sessioni e registra l’azione. Registrare un nuovo autenticatore; il requisito MFA globale rimane e può ancora bloccare il profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Azzera i contatori di username. --ip INDIRIZZO elimina anche quello dell’origine IPv4/IPv6; altrimenti il blocco di rete può persistere. --all azzera tutti i contatori. Account, password e sessioni restano invariati; l’azione viene registrata.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Svuota la lista IP e disattiva orari, JIT e accesso remoto d’emergenza. Autenticazione, MFA, blocco globale e blocchi di login rimangono. Ripristinare le restrizioni desiderate nel pannello.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on chiude i diagnostici ed elimina tutte le sessioni; le pagine aziendali restano disponibili. off rimuove solo il blocco globale: occorre accedere di nuovo e le altre politiche restano applicate.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Genera 5 codici monouso per username esistente e sostituisce i precedenti. Visualizzati una volta: conservarli fuori dal server in un gestore di segreti. L’uso remoto richiede emergenza attiva, sessione autenticata, password e motivo. Non aggira il blocco globale.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Invia gli avvisi in coda a alert_email tramite PHP mail(). Configurare trasporto e attività pianificata, poi verificare la consegna. Gli invii falliti restano in coda. Codice 1 per destinatario non valido o invio fallito.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Misura verifiche di hash con un segreto sintetico, mai password utente. --iterations accetta 1–10, predefinito 3. JSON con algoritmo, tempo e verifiche al secondo. Misura un processo CPU locale, non GPU o attaccanti.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Prepara la rimozione dei file generati attribuibili a Guardian. Conserva account, impostazioni, registri e chiave. Arrestare i worker, eseguire prima di composer remove, svuotare cache dev e riavviare. Controllare i file personalizzati o senza prova di origine.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge richiede la cancellazione con conferma negativa predefinita. --force richiede --purge e conferma la distruzione; necessario con --no-interaction. Archivi esterni, collegamenti e file sconosciuti possono impedire la cancellazione completa; conservare le chiavi necessarie e controllare il risultato.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Disinstallazione e dati


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Salvare archivio e chiave insieme e provare il ripristino. Le chiavi perse non vengono rigenerate automaticamente. Dopo la rimozione verificare il profiler solo in privato. Produzione: APP_DEBUG=0, WebProfilerBundle e raccolta disattivati. La catena locale non prova integrità su host compromessi.

[Disinstallazione e dati](desinstallation.md)
