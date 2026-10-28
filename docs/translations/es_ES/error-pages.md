# Páginas de error público

[Índice de documentación](README.md)


## Páginas de error público

Conserve las páginas de la aplicación o muestre HTML neutro. Los códigos de estado HTTP reales no cambian.

La moda nativa permite Symfony El modo genérico ignora el texto personalizado.Sólo el modo personalizado utiliza el mensaje del visitante.La referencia pública puede ser ausente, aleatoria o fija; es independiente del mensaje. HTTP Los códigos de estado permanecen inalterados.

Páginas de aplicación: conserva el sitio existente 404 Respuestas; las excepciones utilizan su 404 template cuando esté disponible, de lo contrario una página neutral. Neutral: mensaje estándar.

Las excepciones 500 anónimas reciben una página neutra. Los detalles requieren identidad Guardian autorizada y GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Plantillas Twig ausentes o fallidas usan el renderizado neutro; los controladores de error propios necesitan integración. JSON y páginas de negocio correctas no cambian.

| error_mode | Configuración |
|---|---|
| native | Páginas de aplicación: conserva el sitio existente 404 Respuestas; las excepciones utilizan su 404 template cuando esté disponible, de lo contrario una página neutral. Neutral: mensaje estándar. |
| generic | HTML 400–599 → Conserve las páginas de la aplicación o muestre HTML neutro. Los códigos de estado HTTP reales no cambian. |
| custom | Disponible solo con un mensaje neutro personalizado. El texto se conserva al cambiar de modo. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Disponible sólo con una referencia estática personalizada. |
| error_custom_text | Disponible solo con un mensaje neutro personalizado. El texto se conserva al cambiar de modo. |

## Referencia visible

Los mensajes distintos por estado o las referencias estáticas pueden revelar el error real. No se deben falsificar los códigos HTTP.

La referencia se aplica a Guardian's páginas neutras: al azar genera un nuevo código, no un mensaje aleatorio.

El servidor valida las opciones antes de guardarlas.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Red y implantación

La lista de permiso IP acepta IPv4, IPv6 y CIDR Redes - Verificar Symfony Configuración de proxy confiable antes de permitirlo. HTTPS Para las cookies seguras. Guardian no repara los servidores web o bases de datos expuestos.

Denegar diagnósticos si no se pueden verificar identidad, rol, sesión, red, horario, JIT o bloqueo. El navegador no decide vencimientos. Consultar y recopilar perfiles son acciones distintas. Recordar dispositivos no autentica. Emergencia requiere identidad, código consumido y auditoría duradera; un fallo crítico impide abrir. No aplicar parcialmente una configuración inválida. Los controles opcionales no requieren servicios remotos.
