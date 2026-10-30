# Impostazioni

[Indice della documentazione](README.md)


## Protezioni aggiuntive

I controlli sono raggruppati con le relative impostazioni. Le opzioni disattivate sono attenuate e mantengono i valori.

Guardian controlla il profilo e la barra degli strumenti di debug. È richiesto un account amministratore o sviluppatore autorizzato. MFAIl calendario, JITLe restrizioni di rete e il blocco possono ancora rifiutare l'accesso. modalità Stealth nasconde i punti d'ingresso; non sostituisce questi controlli.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Profilo | Impostazioni |
|---|---|
| Solo · flessibile | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| Gruppo · migliorato | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| sensibile · limitata | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Parametro | Impostazioni | Valore iniziale |
|---|---|---|
| `stealth` | Restituisci 404 invece di 403 sulle rotte diagnostiche non autorizzate. | `true` |
| `mfa` | Tutti gli utenti devono configurare TOTP prima di imporlo. | `false` |
| `idle_minutes` | Minuti senza attività prima della scadenza. | `60` |
| `absolute_hours` | Ore indipendentemente dall'attività. | `8` |
| `remember_days` | Giorni. Non sostituisce accesso o orari di lavoro. | `14` |
| `schedule_enabled` | Decidi quando sono accessibili gli strumenti di diagnostica. Le pagine applicative restano disponibili. | `false` |
| `weekdays` | Seleziona i giorni in cui consentire la diagnostica. | `1,2,3,4,5` |
| `start` | Apertura | `09:00` |
| `end` | Chiusura | `18:00` |
| `timezone` | Rilevato dal processo PHP che serve questa pagina. PHP CLI, PHP-FPM e sistema operativo possono avere fusi orari diversi. Verifica la configurazione prima di salvare. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | L’URL privato restituisce 404 fuori orario. Recupero solo tramite CLI locale. | `false` |
| `jit_enabled` | Apri il profiler solo durante una finestra diagnostica richiesta. | `false` |
| `jit_minutes` | Minuti, utilizzati solo se attivo. | `30` |
| `ip_allowlist` | Attenzione: errori possono bloccare l'accesso. Ripristino locale: guardian:policy:reset. | `∅` |
| `audit_level` | Seleziona dettaglio del log e durata della conservazione. | `standard` |
| `audit_retention_days` | Giorni prima della pulizia del log. | `30` |
| `alert_email` | Riceve gli avvisi di emergenza. L'accesso non deve dipendere dalla consegna delle email. | `∅` |
| `emergency_enabled` | Consenti un'eccezione di 30 minuti con codice monouso e registrazione audit. | `false` |
| `login_rules` | di default: 10 Il fallimento, poi un 10-minute lockout. i contanti si applicano sia al conto che all'origine della rete, comprese le conferme di password. | `10 / 10 / 30 / 30` |
| `password_policy` | Tali regole si applicano alla creazione e alle modifiche della password, sul web e nel CLINon rivelano né verificano password esistenti. | `min_length=15` |
| `error_mode` | Pagine di applicazione: conserva il sito esistente 404 risposte; le eccezioni utilizzano 404 modello quando disponibile, altrimenti una pagina neutrale. neutro: messaggio standard. personalizzato: utilizza il tuo testo. | `native` |
| `error_reference` | Il riferimento si applica a GuardianPagine neutre: generano casualmente un nuovo codice, non un messaggio casuale. | `none` |
| `error_reference_text` | Disponibile solo con una riferimento statica personalizzata. | `∅` |
| `error_custom_text` | Disponibile solo con un messaggio neutro personalizzato. Il testo viene conservato cambiando modalità. | `∅ → custom` |

Permesso di pianificare, selezionare giorni, un'ora di inizio inclusiva e un'orario esclusivo. La zona oraria salvata determina le ore locali. Intervallo notturno non è accettato. nascondere il portale fuori dalle ore impedisce l'accesso a distanza di emergenza; mantenere disponibili i comandi di recupero locale.

Idle timeout, vita assoluta e memoria del dispositivo servono scopi diversi. Ricordare un dispositivo non trascende gli orari o JITLa revoca terminerà le sessioni pertinenti sul server. Utilizzare account individuali per attribuire azioni nel diario.

JIT richiede l'attivazione di una finestra temporanea da una sessione autorizzata. Accesso di emergenza, quando attivato, richiede una password, un codice per uso singolo e una ragione. Non si esaurisce il blocco globale. I comandi locali rimangono necessari se il portale non è accessibile.

L’IP consente di accettare IPv4, IPv6 e CIDR Rete - Verifica Symfony Configurazione proxy affidabile prima di consentire. HTTPS per i cookie sicuri. Guardian non ripara i server web esposti o le basi di dati.

Il livello e la ritenzione determinano gli eventi disponibili, fino a 5000 Entrate. filtro per tipo e conto prima di cambiare pagine. gli avvisi vengono tagliati; configurare il loro trasporto e consegna. una catena hash locale non garantisce l'integrità su un host compromesso.

Imposta la lunghezza minima e le categorie richieste in Connessioni e password. I tag di creazione dell'account descrivono la politica salvata. Le modifiche si applicano alle nuove password; selezionare il rinnovo obbligatorio per gli account esistenti. Guardian Scegliere Argon2id Quando PHP che lo fornisce, altrimenti bcrypt.

Filtrare tutti gli eventi conservati per tipo e conto. Gli eventi più recenti vengono visualizzati prima, nelle pagine di 50Scrolling non carica la pagina successiva.

## Ambiente


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


Il proxy gestisce TLS, normalizzazione e protezione volumetrica. Il gateway controlla i diagnostici prima del router; le pagine aziendali non leggono account o sessioni. Le identità Guardian sono separate da Symfony. Salvare archivio e chiave insieme fuori dalla directory pubblica. PHP o host compromessi possono esporre entrambi. L’archiviazione è per un solo host. Errori di avvio, cache o proxy possono incidere su tutta l’applicazione.

Salvare archivio e chiave insieme e provare il ripristino. Le chiavi perse non vengono rigenerate automaticamente. Dopo la rimozione verificare il profiler solo in privato. Produzione: APP_DEBUG=0, WebProfilerBundle e raccolta disattivati. La catena locale non prova integrità su host compromessi.

## Ricostruzione e verifica

Svuota la lista IP e disattiva orari, JIT e accesso remoto d’emergenza. Autenticazione, MFA, blocco globale e blocchi di login rimangono. Ripristinare le restrizioni desiderate nel pannello.

on chiude i diagnostici ed elimina tutte le sessioni; le pagine aziendali restano disponibili. off rimuove solo il blocco globale: occorre accedere di nuovo e le altre politiche restano applicate.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
