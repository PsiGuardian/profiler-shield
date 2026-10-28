# Configuración

[Índice de documentación](README.md)


## Protecciones adicionales

Cada protección aparece junto con sus ajustes. Las opciones desactivadas se atenúan y conservan sus valores.

Guardian controla el perfil y la barra de herramientas de descuento. se requiere una cuenta autorizada de administrador o desarrollador. MFAy los horarios, JITLas restricciones de la red y el bloqueo todavía pueden negar acceso. modo de robo esconde los puntos de entrada; no sustituye estos cheques.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Perfil | Configuración |
|---|---|
| Solo · Flexible | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| Equipo · Mejorado | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| Sensible y restringido | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Parámetro | Configuración | Valor inicial |
|---|---|---|
| `stealth` | Devolver 404 en vez de 403 en las rutas de diagnóstico no autorizadas. | `true` |
| `mfa` | Todos los usuarios deben configurar TOTP antes de activar esta obligación. | `false` |
| `idle_minutes` | Minutos sin actividad hasta caducar la sesión. | `60` |
| `absolute_hours` | Horas, independientemente de la actividad. | `8` |
| `remember_days` | Días. No sustituye el inicio de sesión ni el horario. | `14` |
| `schedule_enabled` | Decida cuándo pueden utilizarse los diagnósticos. Las páginas de negocio siguen disponibles. | `false` |
| `weekdays` | Seleccione los días en que se permiten los diagnósticos. | `1,2,3,4,5` |
| `start` | Apertura | `09:00` |
| `end` | Cierre | `18:00` |
| `timezone` | Detectado desde el proceso PHP que atiende esta página. PHP CLI, PHP-FPM y el sistema pueden tener zonas horarias diferentes. Compruebe la configuración del servidor antes de guardar. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | La URL privada devuelve 404 fuera de horario. Recuperación solo mediante CLI local. | `false` |
| `jit_enabled` | Abrir el profiler solo durante una ventana de diagnóstico solicitada. | `false` |
| `jit_minutes` | Minutos, solo cuando está activado. | `30` |
| `ip_allowlist` | Atención: una configuración incorrecta puede bloquearle. Recuperación local: guardian:policy:reset. | `∅` |
| `audit_level` | Elija nivel de detalle y retención del registro local. | `standard` |
| `audit_retention_days` | Días antes de limpiar el registro. | `30` |
| `alert_email` | Recibe alertas de emergencia. El acceso no debe depender del correo. | `∅` |
| `emergency_enabled` | Conceder una excepción de 30 minutos con código de un solo uso y registro de auditoría. | `false` |
| `login_rules` | El defecto: 10 Los fracasos, a continuación 10Los contos se aplican tanto a la cuenta como al origen de la red, incluidas las confirmaciones de contraseña. | `10 / 10 / 30 / 30` |
| `password_policy` | Estas reglas se aplican a la creación y modificación de contraseñas, en la web y en el CLINo revelan ni inspectan las contraseñas existentes. | `min_length=15` |
| `error_mode` | Páginas de aplicación: conserva el sitio existente 404 Respuestas; las excepciones utilizan su 404 template cuando esté disponible, de lo contrario una página neutral. Neutral: mensaje estándar. | `native` |
| `error_reference` | La referencia se aplica a Guardian's páginas neutras: al azar genera un nuevo código, no un mensaje aleatorio. | `none` |
| `error_reference_text` | Disponible sólo con una referencia estática personalizada. | `∅` |
| `error_custom_text` | Disponible solo con un mensaje neutro personalizado. El texto se conserva al cambiar de modo. | `∅ → custom` |

Permite la planificación, los días seleccionados, una hora de inicio inclusiva y un tiempo exclusivo de fin. La zona horaria salvada determina las horas locales. No se aceptan intervalos por la noche. El escondimiento del portal fuera de las horas impide el acceso remoto de emergencia; mantiene disponibles las órdenes de recuperación local.

Idle timeout, vida absoluta y la memoria del dispositivo sirven para diferentes fines.Recuerda de un dispositivo no pasa los horarios o JITLa revocación termina las sesiones relevantes en el servidor. Utilice cuentas individuales para atribuir acciones en el diario.

JIT requerirá la activación de una ventana temporal desde una sesión autorizada. Acceso de emergencia, cuando se habilite, requiere una contraseña, un código de uso único y una razón. No pasa por encima del bloqueo global. Comandos locales permanecen necesarios si el portal es inaccesible.

La lista de permiso IP acepta IPv4, IPv6 y CIDR Redes - Verificar Symfony Configuración de proxy confiable antes de permitirlo. HTTPS Para las cookies seguras. Guardian no repara los servidores web o bases de datos expuestos.

nivel y retención determinan los eventos disponibles, hasta 5000 entradas. filtrar por tipo y cuenta antes de cambiar páginas. los alertas se cuevan; configurar su transporte y entrega. una cadena de hash local no garantiza la integridad en un host comprometido.

Establecer la longitud mínima y las categorías requeridas en conexiones y contraseñas.Las etiquetas de creación de cuenta describen la política salvada.Los cambios se aplican a nuevas contraseños; seleccione renovación obligatoria para las cuentas existentes. Guardian Elegir Argon2id Cuando PHP Se le proporciona, de lo contrario bcrypt.

Filtre todos los eventos retenidos por tipo y cuenta.Los últimos eventos aparecen primero, en las páginas de 50Scrolling no carga la siguiente página.

## Entorno


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


El proxy gestiona TLS, normalización y protección volumétrica. La pasarela comprueba diagnósticos antes del enrutador; las rutas de negocio no leen cuentas o sesiones. Guardian separa sus identidades de Symfony. Respaldar juntos almacén privado y clave fuera del directorio público. Un PHP o servidor comprometido puede revelar ambos. El almacenamiento es de un solo servidor. Fallos de arranque, caché o proxy pueden afectar toda la aplicación.

Respaldar almacén y clave juntos y probar restauración. No se regeneran claves perdidas automáticamente. Tras retirar, comprobar el profiler solo en privado. Producción: APP_DEBUG=0, WebProfilerBundle y recopilación desactivados. Una cadena local de auditoría no prueba integridad en un servidor comprometido.

## Recuperación y verificación

Vacía la lista IP y desactiva horario, JIT y acceso remoto de emergencia. Mantiene autenticación, MFA, bloqueo global y bloqueos de inicio de sesión. Volver a configurar las restricciones necesarias en el panel.

on cierra los diagnósticos y elimina todas las sesiones; las páginas del negocio siguen disponibles. off solo levanta el bloqueo global: hay que volver a entrar y se mantienen las demás políticas.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
