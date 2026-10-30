# Fordítások

[Dokumentáció tartalma](README.md)

A portál 28 regionális változatot és 25 katalógust kínál; a négy angol változat közös katalógust használ. A statikus szöveg fordítás és biztonságos kódolás után jelenik meg. A felhasználóneveket, egyéni üzeneteket és adatokat nem fordítja. Külső szolgáltatás nem kap szöveget. Az audit kulcsokat, paramétereket, UTF-8-at, parancsokat és linkeket ellenőriz; HTTP-tesztek minden nyelv nézeteit bejárják.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
