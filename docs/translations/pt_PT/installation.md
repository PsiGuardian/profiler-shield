# ΩGuardian Profiler Shield — Instalação e utilização

PHP 8.3 ou superior; Symfony 5.4, 6.4 ou 7.4. Symfony 8.1 requer PHP 8.4 ou superior. São necessários Composer e a extensão OpenSSL + Tokenizer. Instalar WebProfilerBundle para utilizar o profiler.

## 1. Instalar

Executar na raiz da aplicação Symfony que contém composer.json e bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Configurar

```bash
php vendor/bin/guardian setup
```

O assistente ativa o bundle em dev/test, prepara o cofre e a conta de administrador, limpa a cache e verifica a configuração. Cada passo deve terminar com sucesso antes do seguinte. Manter a aplicação privada durante a operação. Repetir preserva as contas e chaves existentes.

## 3. Abrir o painel

```bash
php bin/console guardian:access --env=dev
```

O comando apresenta o caminho privado. Substituir https://VOTRE-HOTE pela origem HTTPS real, incluindo a porta. Ler as credenciais em .guardian-shield/initial-admin-credentials.txt ou no cofre indicado. O primeiro acesso exige a alteração da palavra-passe. Não partilhar este ficheiro.

## 4. Desinstalar

Parar os workers e executar os comandos por ordem, verificando cada resultado. O cofre e a chave são preservados. Reiniciar os workers e verificar o profiler num ambiente privado.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Para eliminar também contas e definições, substituir o primeiro comando pelo seguinte e confirmar. Recusar não altera nada. Rever manualmente cofres externos e ficheiros personalizados; guardar a chave dos cofres preservados.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Referência dos comandos

A ajuda descreve os comandos. O comando de acesso encontra o painel; o diagnóstico verifica a configuração. É necessário acesso local de confiança.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Referência dos comandos](commands.md) · [Índice da documentação](README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

Se a organização utilizar Private Packagist, configurar este repositório antes da instalação e guardar as credenciais Composer segundo as suas instruções. Nunca guardar tokens no Git.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
