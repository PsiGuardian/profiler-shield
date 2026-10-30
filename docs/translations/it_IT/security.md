# Politica di sicurezza

[Indice della documentazione](README.md)

Segnalare vulnerabilità in privato a bundle-sec@omegaguardian.fr per concordare un canale cifrato. Nessun segreto, token attivo o URL privata nel primo messaggio. Includere versioni Guardian, Symfony e PHP, impatto, prerequisiti e riproduzione minima. I responsabili confermano, indagano, correggono privatamente, testano e coordinano avviso e correzione.

Non inviare mai via email password, segreti TOTP, chiavi di cifratura o URL di diagnostica.

Salvare archivio e chiave insieme e provare il ripristino. Le chiavi perse non vengono rigenerate automaticamente. Dopo la rimozione verificare il profiler solo in privato. Produzione: APP_DEBUG=0, WebProfilerBundle e raccolta disattivati. La catena locale non prova integrità su host compromessi.
