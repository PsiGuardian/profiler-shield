# Közreműködés

[Dokumentáció tartalma](README.md)

Elkülönített Symfony-tesztalkalmazásban dolgozzon. Tartsa fenn az üzleti elérhetőséget és jogosulatlan diagnosztika tiltását. Teszteljen névtelen kérést, visszavont munkamenetet, ütemezést és tárolóhibát az érintett verziókon. Stabil kulcsokat, escape-elést és teljes fordítást használjon. Titok ne legyen tesztben, képen vagy Gitben; kötelező hálózat ne legyen. Írja le a hatást és ellenőrzést.
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[Biztonsági szabályzat](security.md) · [Felületpéldák](examples.md)
