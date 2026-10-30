# ΩGuardian Profiler Shield — Installazione e utilizzo

PHP 8.3 o successivo; Symfony 5.4, 6.4 o 7.4. Symfony 8.1 richiede PHP 8.4 o successivo. Sono necessari Composer e l’estensione OpenSSL + Tokenizer. Installare WebProfilerBundle per usare il profiler.

## 1. Installare

Eseguire dalla radice dell’applicazione Symfony contenente composer.json e bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Configurare

```bash
php vendor/bin/guardian setup
```

L’assistente attiva il bundle in dev/test, prepara l’archivio e l’account amministratore, svuota la cache e verifica la configurazione. Ogni passaggio deve riuscire prima del successivo. Mantenere privata l’applicazione durante la procedura. Ripeterla conserva account e chiavi esistenti.

## 3. Aprire il pannello

```bash
php bin/console guardian:access --env=dev
```

Il comando mostra il percorso privato. Sostituire https://VOTRE-HOTE con l’origine HTTPS effettiva, inclusa la porta. Leggere le credenziali in .guardian-shield/initial-admin-credentials.txt o nell’archivio indicato. Il primo accesso richiede di cambiare la password. Non condividere questo file.

## 4. Disinstallare

Arrestare i worker ed eseguire i comandi nell’ordine indicato, verificandone il successo. L’archivio e la chiave vengono conservati. Riavviare i worker e verificare il profiler in un ambiente privato.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Per eliminare anche account e impostazioni, sostituire il primo comando con il seguente e confermare. Il rifiuto non modifica nulla. Controllare manualmente archivi esterni e file personalizzati; conservare le chiavi degli archivi mantenuti.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Riferimento dei comandi

La guida descrive i comandi. Il comando di accesso individua il pannello; la diagnostica verifica la configurazione. È necessario un accesso locale affidabile.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Riferimento dei comandi](../translations/it_IT/commands.md) · [Indice della documentazione](../translations/it_IT/README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

Se l’organizzazione utilizza Private Packagist, configurare questo repository prima dell’installazione e salvare le credenziali Composer secondo le sue istruzioni. Non inserire token in Git.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
