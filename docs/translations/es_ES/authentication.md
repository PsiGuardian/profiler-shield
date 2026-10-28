# Sign-ins y contraseñas

[Índice de documentación](README.md)


## Cuentas y primer registro

Bienvenido al Guardian. su contraseña inicial es temporal. escoge un nuevo secreto: el panel y los diagnósticos permanecen cerrados hasta que lo hagan. El archivo de credencia original se quita después de la validación.

Guardian Las cuentas están separadas de las cuentas del sitio.La cuenta inicial y las nuevas cuentas deben cambiar sus contraseñas antes de continuar.Las vistas de rol de seguridad permiten información sin acceso a perfiles.El cambio de un papel o contraseña invalida sesiones anteriores.

Prepara la ruta privada, clave, almacén, archivos Symfony y un administrador si falta. Conserva cuentas existentes. Rechaza producción. Tras una ejecución directa, limpiar la caché y ejecutar las comprobaciones.


```bash
php bin/console guardian:access --env=dev
```


| Requisitos de contraseña salvados | Duración mínima (Unicode Los personajes) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Política de contraseña

Estas reglas se aplican a la creación y modificación de contraseñas, en la web y en el CLINo revelan ni inspectan las contraseñas existentes.

El valor inicial: 15 Caracteres.Utilizar una contraseña única o un gestor de contraseñas.Categorias de caracteres requeridas no garantizan un contraseño impredecible.

En este servidor, Guardian Utilizar automáticamente Argon2id para nuevas contraseñas. mantenga su PHP La solicitud es limitada, el límite de aplicación es 512 UTF-8 y los bytes.

Este PHP El servidor no proporciona Argon2id. Guardian Por lo tanto, utiliza bcrypt y rechaza las contraseñas superando 72 UTF-8 Pregúntale a su administrador de sistema que permita Argon2id Apoyo en PHP.

Esta opción revoca inmediatamente las sesiones existentes, incluidas sus. Cuentas deben cambiar sus contraseñas antes de configurar Guardian o abrir los diagnósticos.

Cambia la contraseña de la cuenta username existente con entrada oculta conforme a la política, revoca todas sus sesiones y registra la acción. No pasar la contraseña como argumento.

## LOCOUTES PROGRESSIVOS

Cada etapa define un límite de fracaso, una ventana contando, la duración del bloqueo y la ventana repetiendo. 10 Los fracasos provocan a 10Los errores repetidos pueden desencadenar la siguiente etapa. los cheques cubren tanto el cuenta como el origen de la red, incluidas las confirmaciones del administrador.

Los contadores cifrados son independientes por cuenta normalizada y origen; no guardan IP sin procesar. Una reserva atómica precede al hash y caduca en 120 segundos. Reservas abandonadas cuentan como fallos; resultados tardíos no crean sesiones. Un CSRF inválido no reserva nada. El acceso correcto borra fallos de cuenta, conservando reincidencia e historial de origen. Con 10000 entradas activas se rechazan identidades nuevas en vez de expulsar bloqueos.

Las etapas se ejecutan en orden.Después de un bloqueo, los nuevos fallos dentro de la ventana de repetición utilizan la siguiente etapa.Bajo esa ventana, la primera etapa se aplica nuevamente.La última etapa se repite.Las solicitudes bloqueadas no prolongan el retraso. 8 Fase de recuperación local: guardian:login:unlock --all.

| Etapas | Requisitos de contraseña salvados |
|---|---|
| Los fracasos antes del lockout | 1–100 |
| Cortesía (minutos y minutos) | 1–10080 |
| Cálculo de ventanas (minutos) | 1–1440 |
| Repita la ventana después de desbloquear (minutos) | 1–10080 |
| Etapas | 1–8; block[n+1] ≥ block[n] |
| Valor inicial | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Laboratorio de búsqueda exhaustivo

Compare uniformemente los secretos aleatorios de una longitud fija sin introducir una contraseña real. Las tasas de fichaje y el precio del hardware son presunciones ajustables. No se envían intentos de registro.

Usa categorías ASCII disjuntas y BigInt. Inclusión-exclusión calcula el espacio exacto; categorías ausentes o longitud insuficiente dan cero. Rango medio sin reemplazo: (N+1)/2. La espera esperada considera umbrales y último nivel repetido. Se desactiva la estimación si la velocidad no respeta las ventanas. Coste fuera de línea: horas por precio supuesto por hora.

Con un alfabeto fijo, requerir una categoría reduce el número de secretos que cumplen. Expandir el alfabeta es una operación diferente. Los tiempos de lockout asumen hipótesis continuas lo suficientemente rápido para permanecer dentro de ventanas configuradas.

Este modelo no mide la fuerza de una contraseña elegida por el hombre. excluye diccionarios, reutilización, huecos y estrategias dirigidas. bloqueadores web no protegen los hashes robados. La tasa inicial offline es ilustrativa, no un referencia local. CPU La medición: guardian:password:benchmark.

Los grandes números se escriben en completo, con un grupo de dígitos y sin notación E. El tiempo y los costos son redondos. Compare la misma línea utilizando las mismas suposiciones.

| Simulación alfabética disponible | Posibles secretos de cumplimiento |
|---|---|
| ASCII Bajo Caso | 26 |
| ASCII Cortesía | 26 |
| ASCII Los Digitos | 10 |
| ASCII Los símbolos que excluyen el espacio | 32 |
| Tasa online asumida (guesses/s) | 1 / s |
| Tasa asumida offline (hashes/s) | 10000 / s |
| Costo de hardware asumido (€/hora) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## Recuperación y verificación

Respaldar almacén y clave juntos y probar restauración. No se regeneran claves perdidas automáticamente. Tras retirar, comprobar el profiler solo en privado. Producción: APP_DEBUG=0, WebProfilerBundle y recopilación desactivados. Una cadena local de auditoría no prueba integridad en un servidor comprometido.

Reinicia los contadores de username. --ip DIRECCIÓN borra también el contador de ese origen IPv4/IPv6; sin él puede persistir el bloqueo de red. --all borra todos los contadores. No cambia cuentas, contraseñas ni sesiones; la acción queda registrada.

[Referencia de comandos](commands.md)
