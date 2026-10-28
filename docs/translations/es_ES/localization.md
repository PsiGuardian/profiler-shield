# Traducciones

[Índice de documentación](README.md)

El portal ofrece 28 variantes regionales y 25 catálogos; las cuatro variantes inglesas comparten catálogo. El texto estático se traduce y escapa. No se traducen identificadores, mensajes personalizados ni datos de usuarios. Ningún servicio remoto recibe textos. La auditoría comprueba claves, parámetros, UTF-8, comandos y enlaces; las pruebas HTTP visitan las vistas de cada variante.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
