# Disinstallazione e dati

[Indice della documentazione](README.md)

Prepara la rimozione dei file generati attribuibili a Guardian. Conserva account, impostazioni, registri e chiave. Arrestare i worker, eseguire prima di composer remove, svuotare cache dev e riavviare. Controllare i file personalizzati o senza prova di origine.

--purge richiede la cancellazione con conferma negativa predefinita. --force richiede --purge e conferma la distruzione; necessario con --no-interaction. Archivi esterni, collegamenti e file sconosciuti possono impedire la cancellazione completa; conservare le chiavi necessarie e controllare il risultato.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Salvare archivio e chiave insieme e provare il ripristino. Le chiavi perse non vengono rigenerate automaticamente. Dopo la rimozione verificare il profiler solo in privato. Produzione: APP_DEBUG=0, WebProfilerBundle e raccolta disattivati. La catena locale non prova integrità su host compromessi.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Riferimento dei comandi

--purge richiede la cancellazione con conferma negativa predefinita. --force richiede --purge e conferma la distruzione; necessario con --no-interaction. Archivi esterni, collegamenti e file sconosciuti possono impedire la cancellazione completa; conservare le chiavi necessarie e controllare il risultato.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Riferimento dei comandi](commands.md)


Se Composer ha già rimosso il pacchetto, reinstallare temporaneamente la stessa versione senza setup. Eseguire guardian:uninstall, rimuovere di nuovo e svuotare la cache. Controllare manualmente le configurazioni personalizzate.
