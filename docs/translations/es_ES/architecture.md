# Arquitectura y límites de confianza

[Índice de documentación](README.md)


## Arquitectura y límites de confianza

El proxy gestiona TLS, normalización y protección volumétrica. La pasarela comprueba diagnósticos antes del enrutador; las rutas de negocio no leen cuentas o sesiones. Guardian separa sus identidades de Symfony. Respaldar juntos almacén privado y clave fuera del directorio público. Un PHP o servidor comprometido puede revelar ambos. El almacenamiento es de un solo servidor. Fallos de arranque, caché o proxy pueden afectar toda la aplicación.

La lista de permiso IP acepta IPv4, IPv6 y CIDR Redes - Verificar Symfony Configuración de proxy confiable antes de permitirlo. HTTPS Para las cookies seguras. Guardian no repara los servidores web o bases de datos expuestos.

Guardian controla el perfil y la barra de herramientas de descuento. se requiere una cuenta autorizada de administrador o desarrollador. MFAy los horarios, JITLas restricciones de la red y el bloqueo todavía pueden negar acceso. modo de robo esconde los puntos de entrada; no sustituye estos cheques.

| PHP / HTTP | Arquitectura y límites de confianza |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| Sesión | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Invariantes de seguridad

Denegar diagnósticos si no se pueden verificar identidad, rol, sesión, red, horario, JIT o bloqueo. El navegador no decide vencimientos. Consultar y recopilar perfiles son acciones distintas. Recordar dispositivos no autentica. Emergencia requiere identidad, código consumido y auditoría duradera; un fallo crítico impide abrir. No aplicar parcialmente una configuración inválida. Los controles opcionales no requieren servicios remotos.

Respaldar almacén y clave juntos y probar restauración. No se regeneran claves perdidas automáticamente. Tras retirar, comprobar el profiler solo en privado. Producción: APP_DEBUG=0, WebProfilerBundle y recopilación desactivados. Una cadena local de auditoría no prueba integridad en un servidor comprometido.

## Periódico y alertas

nivel y retención determinan los eventos disponibles, hasta 5000 entradas. filtrar por tipo y cuenta antes de cambiar páginas. los alertas se cuevan; configurar su transporte y entrega. una cadena de hash local no garantiza la integridad en un host comprometido.
