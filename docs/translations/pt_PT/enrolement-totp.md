# Autenticação de dois fatores

[Índice da documentação](README.md)


## Autenticação de dois fatores

Proteja a sua conta Guardian com códigos temporários, sem serviços QR externos.

Cada indivíduo registra seu próprio TOTP secreto e confirma um código. requerimento MFA blocos de acesso aos perfis para contas não registradas. QR Planos de recuperação administrativa antes de exigir essa proteção para toda a equipe.

## 1. Confirme a sua identidade

Introduza a palavra-passe que utiliza para iniciar sessão no Guardian. Só será pedida uma vez.

## 2. Leia este QR na aplicação autenticadora

Guardian gera o QR localmente, sem contactar serviços externos.

## 3. Confirme um código de seis dígitos

Introduza o código apresentado na aplicação. Não é necessária outra palavra-passe.

## Privacidade: não indique o URL do site

Se a aplicação pedir nome ou site, indique apenas «ΩGuardian» ou um nome neutro. Nunca introduza o URL da sua instância Symfony. Não use geradores QR online, que receberiam o seu segredo.

Mantenha-a privada: permite gerar códigos TOTP. Não a partilhe nem faça capturas de ecrã.

O segredo provisório expira após 10 minutos ou 5 códigos incorretos; reiniciar então. Um erro conserva o formulário sem pedir outra vez a palavra-passe. O contador de confirmação é consumido contra repetição. QR SVG usa bacon/bacon-qr-code e ext-iconv sem GD, XMLWriter ou rede. A entrada manual permanece. O URI otpauth usa fornecedor neutro e conta, sem domínio do site. Não partilhar segredo nem URL privado.

## Recuperação e verificação

Elimina o segredo e contador TOTP de username, revoga sessões e regista a ação. Registar outro autenticador; a exigência MFA global mantém-se e pode bloquear o profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
