# Autenticación de dos factores

[Índice de documentación](README.md)


## Autenticación de dos factores

Proteja su cuenta Guardian mediante códigos temporales, sin servicios QR externos.

Cada persona registra su propia TOTP secreto y confirma un código. solicitud MFA bloques de acceso a perfiles para cuentas no registradas. QR Plan de recuperación administrativa antes de requerir esta protección para todo el equipo.

## 1. Confirme su identidad

Introduzca la misma contraseña que usa para entrar en Guardian. Solo se pide una vez.

## 2. Escanee este QR con su autenticador

Guardian genera el QR localmente, sin enviar solicitudes a servicios externos.

## 3. Confirme un código de seis dígitos

Introduzca el código de su aplicación. No se requiere otra contraseña.

## Privacidad: no introduzca la URL del sitio

Si la aplicación solicita un nombre o sitio, indique únicamente «ΩGuardian» o un alias neutro. No introduzca la URL de su instancia Symfony. Nunca use un generador QR en línea: recibiría su secreto.

Manténgala privada: permite generar sus códigos TOTP. No la comparta ni haga capturas.

El secreto provisional vence a los 10 minutos o tras 5 códigos erróneos; reiniciar entonces. Un error conserva el formulario sin pedir otra vez la contraseña. Se consume el contador de confirmación contra repetición. El QR SVG usa bacon/bacon-qr-code y ext-iconv sin GD, XMLWriter ni red. La entrada manual sigue disponible. La URI otpauth usa proveedor neutro y cuenta, sin dominio del sitio. No compartir secreto ni URL privada.

## Recuperación y verificación

Elimina el secreto y contador TOTP de username, revoca sus sesiones y registra la acción. Registrar otro autenticador; la exigencia global de MFA continúa y puede bloquear el profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
