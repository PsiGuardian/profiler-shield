# Contribuir

[Índice da documentação](README.md)

Trabalhar numa aplicação Symfony de teste isolada. Preservar disponibilidade e recusar diagnósticos não autorizados. Testar pedidos anónimos, sessões revogadas, horários e falhas de armazenamento nas versões afetadas. Usar chaves estáveis, escapar textos e completar traduções. Sem segredos em testes, capturas ou Git; sem rede obrigatória. Descrever efeitos e validação.
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[Política de segurança](security.md) · [Exemplos de interface](examples.md)
