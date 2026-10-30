# Referência dos comandos

[Índice da documentação](README.md)

Executar na raiz Symfony num terminal de confiança. Os exemplos usam dev. Nunca colocar palavras-passe, chaves ou códigos de recuperação nos argumentos, registos ou Git.

## Descobrir comandos e consultar ajuda


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev e --no-interaction são opções Symfony. --no-interaction não fornece segredos e não serve para comandos interativos de palavra-passe. --help mostra a sintaxe; -v acrescenta detalhes. Código 0 indica o resultado anunciado; --no-admin pode terminar sem portal utilizável.

## `vendor/bin/guardian`

Ativa o bundle em dev/test, limpa a cache, executa guardian:setup --automatic, limpa novamente e executa guardian:doctor e guardian:access. Para ao primeiro erro; repetir preserva contas e chaves. Recusa APP_ENV=prod e necessita de proc_open.

Sem argumentos, --help, -h ou help apresenta ajuda sem alterações. setup é a única ação e não aceita opções adicionais.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Referência dos comandos |
|---|---|
| [`guardian:setup`](#guardiansetup) | Prepara o caminho privado, chave, cofre, ficheiros Symfony e um administrador se necessário. |
| [`guardian:install`](#guardianinstall) | Inicializa o cofre e cria um administrador com --username, admin por predefinição. |
| [`guardian:access`](#guardianaccess) | Mostra o caminho do painel e o local das credenciais iniciais, nunca a palavra-passe. |
| [`guardian:doctor`](#guardiandoctor) | Verifica cofre, permissões, chave, rota, administrador ativo e configuração. |
| [`guardian:password:reset`](#guardianpasswordreset) | Altera a palavra-passe da conta username existente por entrada oculta conforme a política, revoga as sessões e regista a ação. |
| [`guardian:mfa:reset`](#guardianmfareset) | Elimina o segredo e contador TOTP de username, revoga sessões e regista a ação. |
| [`guardian:login:unlock`](#guardianloginunlock) | Reinicia os contadores de username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Limpa a lista IP e desativa horários, JIT e emergência remota. |
| [`guardian:lockdown`](#guardianlockdown) | on fecha os diagnósticos e elimina todas as sessões; o site mantém-se disponível. |
| [`guardian:emergency`](#guardianemergency) | Gera 5 códigos de utilização única para username existente e substitui os anteriores. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Envia alertas pendentes para alert_email com PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Mede verificações de hash com um segredo sintético, nunca uma palavra-passe real. |
| [`guardian:uninstall`](#guardianuninstall) | Prepara a remoção dos ficheiros gerados atribuíveis a Guardian. |

## `guardian:setup`

Prepara o caminho privado, chave, cofre, ficheiros Symfony e um administrador se necessário. Preserva contas existentes. Recusa produção. Após execução direta, limpar a cache e executar as verificações.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic gera admin e um segredo num ficheiro privado 0600. --path escolhe um caminho de 25–181 caracteres sem substituir um existente. --no-admin omite a criação do administrador; sem conta ativa o portal fica inutilizável. --username propõe o nome interativo, admin por predefinição; não muda o automático.

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

Inicializa o cofre e cria um administrador com --username, admin por predefinição. Pede uma palavra-passe oculta segundo a política ativa. Recusa contas existentes. Não cria rotas nem configuração Symfony; usar normalmente o assistente completo.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Nome de usuário: 3 Para 64 Os personagens alfanuméricos,

## `guardian:access`

Mostra o caminho do painel e o local das credenciais iniciais, nunca a palavra-passe. --base-url aceita a origem HTTPS real com porta opcional, sem credenciais, consulta ou fragmento. Substituir o anfitrião de exemplo.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Verifica cofre, permissões, chave, rota, administrador ativo e configuração. Devolve 1 se detetar um problema. PHP CLI e PHP-FPM podem ter fusos diferentes. Não testa exposição à Internet nem regras do proxy.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Altera a palavra-passe da conta username existente por entrada oculta conforme a política, revoga as sessões e regista a ação. Não passar palavras-passe como argumento.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Elimina o segredo e contador TOTP de username, revoga sessões e regista a ação. Registar outro autenticador; a exigência MFA global mantém-se e pode bloquear o profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Reinicia os contadores de username. --ip ENDEREÇO elimina também o contador dessa origem IPv4/IPv6; caso contrário pode persistir um bloqueio de rede. --all elimina todos. Contas, palavras-passe e sessões não mudam; a ação é registada.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Limpa a lista IP e desativa horários, JIT e emergência remota. Autenticação, MFA, bloqueio global e bloqueios de entrada mantêm-se. Reconfigurar as restrições desejadas no painel.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on fecha os diagnósticos e elimina todas as sessões; o site mantém-se disponível. off levanta apenas o bloqueio global: é necessário novo início de sessão e as restantes políticas mantêm-se.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Gera 5 códigos de utilização única para username existente e substitui os anteriores. Exibidos uma vez: guardar fora do servidor num gestor de segredos. O uso remoto exige emergência ativa, sessão autenticada, palavra-passe e motivo. Não contorna o bloqueio global.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Envia alertas pendentes para alert_email com PHP mail(). Configurar transporte e tarefa agendada, depois verificar entrega real. As falhas ficam na fila. Código 1 se o destinatário for inválido ou um envio falhar.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Mede verificações de hash com um segredo sintético, nunca uma palavra-passe real. --iterations aceita 1–10, predefinição 3. JSON com algoritmo, tempo e verificações por segundo. Mede um processo CPU local, não GPU ou atacante.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Prepara a remoção dos ficheiros gerados atribuíveis a Guardian. Preserva contas, definições, registos e chave. Parar workers, executar antes de composer remove, limpar cache dev e reiniciar. Rever ficheiros personalizados ou sem prova de origem.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge pede eliminação com confirmação negativa por predefinição. --force requer --purge e confirma a destruição; obrigatório com --no-interaction. Cofres externos, ligações e ficheiros desconhecidos podem impedir a eliminação completa; conservar as chaves necessárias e rever o resultado.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Desinstalação e dados


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Guardar cofre e chave juntos e testar restauro. Chaves perdidas não são regeneradas automaticamente. Após remoção verificar o profiler apenas em privado. Produção: APP_DEBUG=0, WebProfilerBundle e recolha desativados. Uma cadeia local não prova integridade num servidor comprometido.

[Desinstalação e dados](desinstallation.md)
