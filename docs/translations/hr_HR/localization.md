# Prijevodi

[Sadržaj dokumentacije](README.md)

Portal nudi 28 regionalnih inačica i 25 kataloga; četiri engleske dijele katalog. Statički tekst prevodi se i sigurno kodira. Imena, prilagođene poruke i korisnički podaci ne prevode se. Udaljena usluga ne prima tekst. Audit provjerava ključeve, parametre, UTF-8, naredbe i poveznice; HTTP testovi posjećuju prikaze svih inačica.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
