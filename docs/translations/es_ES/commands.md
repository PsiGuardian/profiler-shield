# Referencia de comandos

[Índice de documentación](README.md)

Ejecutar desde la raíz de Symfony en un terminal de confianza. Los ejemplos usan dev. No incluir contraseñas, claves ni códigos de recuperación en argumentos, registros o Git.

## Descubrir comandos y obtener ayuda


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev y --no-interaction pertenecen a Symfony. --no-interaction no proporciona secretos y no sirve para comandos interactivos de contraseña. --help muestra sintaxis; -v añade detalles. El código 0 indica el resultado anunciado; --no-admin puede terminar sin portal utilizable.

## `vendor/bin/guardian`

Activa el bundle en dev/test, limpia la caché, ejecuta guardian:setup --automatic, vuelve a limpiar y ejecuta guardian:doctor y guardian:access. Se detiene al primer error; repetir conserva cuentas y claves. Rechaza APP_ENV=prod y necesita proc_open.

Sin argumentos, --help, -h o help muestra ayuda sin cambios. setup es la única acción y no admite opciones adicionales.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Referencia de comandos |
|---|---|
| [`guardian:setup`](#guardiansetup) | Prepara la ruta privada, clave, almacén, archivos Symfony y un administrador si falta. |
| [`guardian:install`](#guardianinstall) | Inicializa el almacén y crea un administrador mediante --username, admin por defecto. |
| [`guardian:access`](#guardianaccess) | Muestra la ruta del panel y dónde están las credenciales iniciales, nunca la contraseña. |
| [`guardian:doctor`](#guardiandoctor) | Comprueba almacén, permisos, clave, ruta, administrador activo y configuración. |
| [`guardian:password:reset`](#guardianpasswordreset) | Cambia la contraseña de la cuenta username existente con entrada oculta conforme a la política, revoca todas sus sesiones y registra la acción. |
| [`guardian:mfa:reset`](#guardianmfareset) | Elimina el secreto y contador TOTP de username, revoca sus sesiones y registra la acción. |
| [`guardian:login:unlock`](#guardianloginunlock) | Reinicia los contadores de username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Vacía la lista IP y desactiva horario, JIT y acceso remoto de emergencia. |
| [`guardian:lockdown`](#guardianlockdown) | on cierra los diagnósticos y elimina todas las sesiones; las páginas del negocio siguen disponibles. |
| [`guardian:emergency`](#guardianemergency) | Genera 5 códigos de un solo uso para username existente, sustituyendo los anteriores. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Envía alertas pendientes a alert_email mediante PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Mide verificaciones de hash con un secreto sintético, no con contraseñas de usuarios. |
| [`guardian:uninstall`](#guardianuninstall) | Prepara la retirada de archivos generados atribuibles a Guardian. |

## `guardian:setup`

Prepara la ruta privada, clave, almacén, archivos Symfony y un administrador si falta. Conserva cuentas existentes. Rechaza producción. Tras una ejecución directa, limpiar la caché y ejecutar las comprobaciones.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic genera admin y un secreto en un archivo privado 0600. --path elige una ruta de 25–181 caracteres sin sustituir una existente. --no-admin omite crear el administrador; sin una cuenta activa el portal no se puede usar. --username propone el nombre interactivo, admin por defecto; no cambia el automático.

```text
--path: ^/[a-zA-Z0-9/_-]{24,180}$
/_profiler; /_wdt → ∅
```

```bash
php bin/console guardian:setup --username=operator --env=dev
php bin/console cache:clear --env=dev
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## `guardian:install`

Inicializa el almacén y crea un administrador mediante --username, admin por defecto. Solicita una contraseña oculta conforme a la política activa. Rechaza cuentas existentes. No crea rutas ni configuración Symfony; usar normalmente el asistente completo.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Nombre de usuario: 3 para 64 Los personajes alfanuméricos,

## `guardian:access`

Muestra la ruta del panel y dónde están las credenciales iniciales, nunca la contraseña. --base-url acepta el origen HTTPS real, con puerto opcional, sin credenciales, consulta ni fragmento. Sustituir el host de ejemplo.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Comprueba almacén, permisos, clave, ruta, administrador activo y configuración. Devuelve 1 si detecta un problema. PHP CLI y PHP-FPM pueden tener zonas horarias diferentes. No comprueba exposición a Internet ni reglas del proxy.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Cambia la contraseña de la cuenta username existente con entrada oculta conforme a la política, revoca todas sus sesiones y registra la acción. No pasar la contraseña como argumento.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Elimina el secreto y contador TOTP de username, revoca sus sesiones y registra la acción. Registrar otro autenticador; la exigencia global de MFA continúa y puede bloquear el profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Reinicia los contadores de username. --ip DIRECCIÓN borra también el contador de ese origen IPv4/IPv6; sin él puede persistir el bloqueo de red. --all borra todos los contadores. No cambia cuentas, contraseñas ni sesiones; la acción queda registrada.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Vacía la lista IP y desactiva horario, JIT y acceso remoto de emergencia. Mantiene autenticación, MFA, bloqueo global y bloqueos de inicio de sesión. Volver a configurar las restricciones necesarias en el panel.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on cierra los diagnósticos y elimina todas las sesiones; las páginas del negocio siguen disponibles. off solo levanta el bloqueo global: hay que volver a entrar y se mantienen las demás políticas.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Genera 5 códigos de un solo uso para username existente, sustituyendo los anteriores. Se muestran una vez; guardarlos fuera del servidor en un gestor de secretos. El uso remoto exige emergencia activada, sesión autenticada, contraseña y motivo. No evita el bloqueo global.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Envía alertas pendientes a alert_email mediante PHP mail(). Configurar transporte y tarea programada; comprobar la entrega real. Los fallos permanecen en cola. Devuelve 1 por destinatario inválido o envío fallido.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Mide verificaciones de hash con un secreto sintético, no con contraseñas de usuarios. --iterations admite 1–10, por defecto 3. JSON con algoritmo, tiempo y verificaciones por segundo. Mide un proceso CPU local, no una GPU ni un atacante.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Prepara la retirada de archivos generados atribuibles a Guardian. Conserva cuentas, ajustes, registros y clave. Detener los workers, ejecutar antes de composer remove, limpiar caché dev y reiniciar. Revisar archivos personalizados o sin prueba de origen.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge solicita borrar datos con confirmación negativa por defecto. --force requiere --purge y confirma la destrucción; obligatorio con --no-interaction. Almacenes externos, enlaces y archivos desconocidos pueden impedir el borrado completo; conservar claves necesarias y revisar el resultado.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Desinstalación y datos


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Respaldar almacén y clave juntos y probar restauración. No se regeneran claves perdidas automáticamente. Tras retirar, comprobar el profiler solo en privado. Producción: APP_DEBUG=0, WebProfilerBundle y recopilación desactivados. Una cadena local de auditoría no prueba integridad en un servidor comprometido.

[Desinstalación y datos](desinstallation.md)
