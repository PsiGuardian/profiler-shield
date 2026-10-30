# Política de seguridad

[Índice de documentación](README.md)

Comunicar vulnerabilidades en privado a bundle-sec@omegaguardian.fr para acordar un canal cifrado. Sin secretos, tokens activos o URL privadas en el primer mensaje. Incluir versiones Guardian, Symfony y PHP, impacto, requisitos y reproducción mínima. Los responsables confirman, investigan, corrigen en privado, prueban y coordinan aviso y parche.

Nunca envíe por correo contraseñas, secretos TOTP, claves de cifrado ni URL de diagnóstico.

Respaldar almacén y clave juntos y probar restauración. No se regeneran claves perdidas automáticamente. Tras retirar, comprobar el profiler solo en privado. Producción: APP_DEBUG=0, WebProfilerBundle y recopilación desactivados. Una cadena local de auditoría no prueba integridad en un servidor comprometido.
