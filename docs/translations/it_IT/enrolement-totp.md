# Autenticazione a due fattori

[Indice della documentazione](README.md)


## Autenticazione a due fattori

Ogni persona registra la propria TOTP segreto e conferma un codice.

Ogni persona registra la propria TOTP segreto e conferma un codice. richiesta MFA blocchi di profili per l'accesso agli account non registrati. pianificare il recupero amministrativo prima di richiedere questa protezione per l'intero team. Seleziona la configurazione manuale e inserisci la chiave mostrata. Usa SHA-1, sei cifre e 30 secondi.

## 1. Conferma la tua identità

Inserisci la stessa password che usi per accedere a Guardian. Sarà richiesta una sola volta.

## 2. Inserisci la chiave nell’autenticatore

Seleziona la configurazione manuale e inserisci la chiave mostrata. Usa SHA-1, sei cifre e 30 secondi.

## 3. Conferma un codice a sei cifre

Inserisci il codice mostrato nell'app. Non è richiesta una seconda password.

## Privacy: non inserire l'URL del sito

Se l'app chiede un nome o sito, inserisci solo «ΩGuardian» oppure un alias neutro. Non inserire l'URL dell'istanza Symfony.

Mantienila privata: consente di generare codici TOTP. Non condividerla e non fare screenshot.

Il segreto provvisorio scade dopo 10 minuti o 5 codici errati; ricominciare allora. Un errore conserva il modulo senza richiedere la password. Il contatore di conferma viene consumato contro il riuso.

## Ricostruzione e verifica

Elimina segreto e contatore TOTP di username, revoca le sessioni e registra l’azione. Registrare un nuovo autenticatore; il requisito MFA globale rimane e può ancora bloccare il profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
