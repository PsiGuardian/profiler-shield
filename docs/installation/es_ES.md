# ΩGuardian Profiler Shield — Instalación y uso

PHP 8.3 o posterior; Symfony 5.4, 6.4 o 7.4. Symfony 8.1 requiere PHP 8.4 o posterior. Se necesitan Composer y la extensión OpenSSL + Tokenizer. Instalar WebProfilerBundle para usar el profiler.

## 1. Instalar

Ejecutar desde la raíz de la aplicación Symfony que contiene composer.json y bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Configurar

```bash
php vendor/bin/guardian setup
```

El asistente activa el bundle en dev/test, prepara el almacén y la cuenta de administrador, limpia la caché y comprueba la configuración. Continuar solo si cada paso termina correctamente. Mantener la aplicación privada durante el proceso. Repetirlo conserva las cuentas y claves existentes.

## 3. Abrir el panel

```bash
php bin/console guardian:access --env=dev
```

El comando muestra la ruta privada. Sustituir https://VOTRE-HOTE por el origen HTTPS real, incluido el puerto. Leer las credenciales en .guardian-shield/initial-admin-credentials.txt o en el almacén indicado. El primer acceso exige cambiar la contraseña. No compartir este archivo.

## 4. Desinstalar

Detener los workers y ejecutar los comandos en orden, comprobando cada resultado. El almacén y su clave se conservan. Reiniciar los workers y comprobar el profiler en un entorno privado.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Para borrar también cuentas y ajustes, sustituir el primer comando por el siguiente y confirmar. Rechazar no modifica nada. Revisar manualmente los almacenes externos y archivos personalizados; conservar la clave de cualquier almacén que se mantenga.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Referencia de comandos

La ayuda describe los comandos. El comando de acceso localiza el panel; el diagnóstico comprueba la configuración. Se requiere acceso local de confianza.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Referencia de comandos](../translations/es_ES/commands.md) · [Índice de documentación](../translations/es_ES/README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

Si la organización utiliza Private Packagist, configurar este repositorio antes de instalar y guardar las credenciales de Composer según sus instrucciones. Nunca incluir tokens en Git.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
