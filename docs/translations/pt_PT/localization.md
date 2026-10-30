# Traduções

[Índice da documentação](README.md)

O portal oferece 28 variantes regionais e 25 catálogos; as quatro variantes inglesas partilham um catálogo. Os textos estáticos são traduzidos e escapados. Identificadores, mensagens personalizadas e dados dos utilizadores não são traduzidos. Nenhum serviço remoto recebe textos. A auditoria verifica chaves, parâmetros, UTF-8, comandos e ligações; os testes HTTP percorrem as vistas de cada variante.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
