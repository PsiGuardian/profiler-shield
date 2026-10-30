# Traduzioni

[Indice della documentazione](README.md)

Il portale offre 28 varianti regionali e 25 cataloghi; le quattro varianti inglesi condividono un catalogo. I testi statici sono tradotti ed escapati. Nomi utente, messaggi personalizzati e dati utente non vengono tradotti. Nessun servizio remoto riceve testi. L’audit controlla chiavi, parametri, UTF-8, comandi e collegamenti; i test HTTP visitano le viste di ogni lingua.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
