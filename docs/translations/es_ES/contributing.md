# Contribuir

[Índice de documentación](README.md)

Trabajar en una aplicación Symfony de prueba aislada. Preservar el negocio y rechazar diagnósticos no autorizados. Probar solicitudes anónimas, sesiones revocadas, horarios y fallos de almacenamiento en las versiones afectadas. Usar claves estables, escapar textos y completar traducciones. Sin secretos en pruebas, capturas o Git; sin red obligatoria. Describir efectos y validación.
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[Política de seguridad](security.md) · [Ejemplos de interfaz](examples.md)
