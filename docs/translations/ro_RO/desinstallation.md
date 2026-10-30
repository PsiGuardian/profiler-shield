# Dezinstalare și date

[Cuprinsul documentației](README.md)

Pregătește eliminarea fișierelor generate atribuibile Guardian. Păstrează conturi, setări, jurnale și cheie. Opriți workerii, executați înainte de composer remove, goliți cache-ul dev și reporniți. Verificați fișierele personalizate sau fără dovada originii.

--purge cere ștergerea cu confirmare negativă implicită. --force necesită --purge și confirmă distrugerea; obligatoriu cu --no-interaction. Seifurile externe, legăturile și fișierele necunoscute pot împiedica ștergerea completă. Păstrați cheile necesare și verificați rezultatul.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Salvați seiful și cheia împreună și testați restaurarea. Cheile pierdute nu se regenerează automat. După eliminare verificați profilerul numai privat. Producție: APP_DEBUG=0, WebProfilerBundle și colectarea dezactivate. Un lanț local nu dovedește integritatea unei gazde compromise.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Referința comenzilor

--purge cere ștergerea cu confirmare negativă implicită. --force necesită --purge și confirmă distrugerea; obligatoriu cu --no-interaction. Seifurile externe, legăturile și fișierele necunoscute pot împiedica ștergerea completă. Păstrați cheile necesare și verificați rezultatul.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Referința comenzilor](commands.md)


Dacă Composer a eliminat deja pachetul, reinstalați temporar aceeași versiune fără setup. Rulați guardian:uninstall, eliminați din nou și goliți cache-ul. Verificați manual configurațiile personalizate.
