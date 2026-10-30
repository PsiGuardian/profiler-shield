# Autenticación de dos factores

[Índice de documentación](README.md)


## Autenticación de dos factores

Cada persona registra su propia TOTP secreto y confirma un código.

Cada persona registra su propia TOTP secreto y confirma un código. solicitud MFA bloques de acceso a perfiles para cuentas no registradas. Elija la configuración manual e introduzca la clave mostrada. Use SHA-1, seis dígitos y un periodo de 30 segundos.

## 1. Confirme su identidad

Introduzca la misma contraseña que usa para entrar en Guardian. Solo se pide una vez.

## 2. Introduzca la clave en su autenticador

Elija la configuración manual e introduzca la clave mostrada. Use SHA-1, seis dígitos y un periodo de 30 segundos.

## 3. Confirme un código de seis dígitos

Introduzca el código de su aplicación. No se requiere otra contraseña.

## Privacidad: no introduzca la URL del sitio

Si la aplicación solicita un nombre o sitio, indique únicamente «ΩGuardian» o un alias neutro. No introduzca la URL de su instancia Symfony.

Manténgala privada: permite generar sus códigos TOTP. No la comparta ni haga capturas.

El secreto provisional vence a los 10 minutos o tras 5 códigos erróneos; reiniciar entonces. Un error conserva el formulario sin pedir otra vez la contraseña. Se consume el contador de confirmación contra repetición.

## Recuperación y verificación

Elimina el secreto y contador TOTP de username, revoca sus sesiones y registra la acción. Registrar otro autenticador; la exigencia global de MFA continúa y puede bloquear el profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
