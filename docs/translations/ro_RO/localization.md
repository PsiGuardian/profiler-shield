# Traduceri

[Cuprinsul documentației](README.md)

Portalul oferă 28 de variante regionale și 25 de cataloage; cele patru variante engleze împart un catalog. Textele statice sunt traduse și escapate. Numele, mesajele personalizate și datele utilizatorilor nu se traduc. Niciun serviciu extern nu primește texte. Auditul verifică chei, parametri, UTF-8, comenzi și linkuri; testele HTTP parcurg vederile fiecărei variante.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
