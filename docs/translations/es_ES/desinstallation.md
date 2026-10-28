# Desinstalación y datos

[Índice de documentación](README.md)

Prepara la retirada de archivos generados atribuibles a Guardian. Conserva cuentas, ajustes, registros y clave. Detener los workers, ejecutar antes de composer remove, limpiar caché dev y reiniciar. Revisar archivos personalizados o sin prueba de origen.

--purge solicita borrar datos con confirmación negativa por defecto. --force requiere --purge y confirma la destrucción; obligatorio con --no-interaction. Almacenes externos, enlaces y archivos desconocidos pueden impedir el borrado completo; conservar claves necesarias y revisar el resultado.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Respaldar almacén y clave juntos y probar restauración. No se regeneran claves perdidas automáticamente. Tras retirar, comprobar el profiler solo en privado. Producción: APP_DEBUG=0, WebProfilerBundle y recopilación desactivados. Una cadena local de auditoría no prueba integridad en un servidor comprometido.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Referencia de comandos

--purge solicita borrar datos con confirmación negativa por defecto. --force requiere --purge y confirma la destrucción; obligatorio con --no-interaction. Almacenes externos, enlaces y archivos desconocidos pueden impedir el borrado completo; conservar claves necesarias y revisar el resultado.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Referencia de comandos](commands.md)


Si Composer ya retiró el paquete, reinstalar temporalmente la misma versión sin ejecutar setup. Ejecutar guardian:uninstall, retirar de nuevo y limpiar la caché. Revisar manualmente configuraciones personalizadas.
