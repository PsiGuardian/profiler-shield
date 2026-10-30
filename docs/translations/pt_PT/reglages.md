# Definições

[Índice da documentação](README.md)


## Proteções adicionais

Os controlos estão agrupados com as definições associadas. As opções inativas ficam esbatidas e mantêm os seus valores.

Guardian controla o perfil e a barra de ferramentas debug. É necessária uma conta autorizada de administrador ou desenvolvedor. MFAe os horários, JITRestrições de rede e bloqueio podem ainda negar acesso. o modo de roubo esconde pontos de entrada; não substitui esses cheques.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Perfil | Definições |
|---|---|
| Solos · Flexível | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| • Equipamento reforçado | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| Sensível e restrito | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Parâmetro | Definições | Valor inicial |
|---|---|---|
| `stealth` | Devolver 404 em vez de 403 nas rotas de diagnóstico não autorizadas. | `true` |
| `mfa` | Todos os utilizadores têm de configurar TOTP antes de tornar obrigatório. | `false` |
| `idle_minutes` | Minutos sem atividade antes de expirar a sessão. | `60` |
| `absolute_hours` | Horas, independentemente da atividade. | `8` |
| `remember_days` | Dias. Não substitui autenticação nem regras de horário. | `14` |
| `schedule_enabled` | Escolha quando os diagnósticos estão acessíveis. As páginas da aplicação continuam disponíveis. | `false` |
| `weekdays` | Selecione os dias em que os diagnósticos são permitidos. | `1,2,3,4,5` |
| `start` | Abertura | `09:00` |
| `end` | Fecho | `18:00` |
| `timezone` | Detetado a partir do processo PHP que serve esta página. PHP CLI, PHP-FPM e o sistema operativo podem utilizar fusos diferentes. Confirme a configuração antes de guardar. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | O URL privado devolve 404 fora do horário. Recuperação apenas por CLI local. | `false` |
| `jit_enabled` | Abrir o profiler apenas numa janela de diagnóstico solicitada. | `false` |
| `jit_minutes` | Minutos, usados só quando ativo. | `30` |
| `ip_allowlist` | Atenção: erros podem bloquear o seu acesso. Recuperação local: guardian:policy:reset. | `∅` |
| `audit_level` | Selecione o nível de detalhe e a retenção do registo local. | `standard` |
| `audit_retention_days` | Dias antes da limpeza do registo. | `30` |
| `alert_email` | Recebe alertas de emergência. O acesso não pode depender da entrega de emails. | `∅` |
| `emergency_enabled` | Conceder exceção de 30 minutos com código único e registo de auditoria. | `false` |
| `login_rules` | Definição : 10 fracassos e depois a 10-minute lockout. contas aplicam-se tanto à conta e origem da rede, incluindo confirmações de senha. | `10 / 10 / 30 / 30` |
| `password_policy` | Estas regras aplicam-se à criação e alterações de senhas, na web e no CLIEles não revelam ou inspeccionam senhas existentes. | `min_length=15` |
| `error_mode` | Páginas de aplicação: preserva o site existente 404 Resposta; Exceções usam 404 modelo quando disponível, caso contrário uma página neutra. neutral: mensagem padrão. personalizado: usa seu texto. | `native` |
| `error_reference` | A referência se aplica a GuardianPáginas neutras: o acaso gera um novo código, não uma mensagem aleatória. | `none` |
| `error_reference_text` | Disponível apenas com uma referência estática personalizada. | `∅` |
| `error_custom_text` | Disponível apenas com uma mensagem neutra personalizada. O texto é preservado ao mudar de modo. | `∅ → custom` |

Permite planejamento, dias selecionados, uma hora de início inclusiva e um tempo exclusivo de fim. A zona horária salvada determina horas locais. intervalos nocturnos não são aceitos. Esconder o portal fora das horas impede acesso remoto de emergência; manter as ordens de recuperação local disponíveis.

Idle timeout, vida absoluta e memória do dispositivo servem a diferentes finalidades. lembrar um dispositivo não ultrapassa os horários ou JITA revogação termina as sessões relevantes no servidor. Use contas individuais para atribuir ações no diário.

JIT requer ativação de uma janela temporária de uma sessão autorizada. acesso de emergência, quando ativado, exige uma senha, um código de uso único e uma razão. não perca o bloqueio global. comandos locais permanecem necessários se o portal é inacessível.

A lista de permissões IP aceita IPv4, IPv6 e CIDR Redes - Verificação Symfony Configuração de proxy confiável antes de ativá-lo. HTTPS Para cookies seguras. Guardian Não repara servidores web expostos ou bases de dados.

Nível e retenção determinam eventos disponíveis, até 5000 entradas. filtro por tipo e conta antes de mudar páginas. alertas são quebrados; configurar seu transporte e entrega. uma cadeia de hash local não garante a integridade em um host comprometido.

Defina a duração mínima e as categorias necessárias em Conectos e senhas. os marcadores de criação de contas descrevem a política salvida. as alterações se aplicam às novas senhas; selecione renovação obrigatória para contas existentes. Guardian Escolha Argon2id Quando PHP fornece-o, de outra forma bcrypt.

Filtre todos os eventos preservados por tipo e conta. Novos eventos aparecerão primeiro, nas páginas de 50O Scrolling não carrega a próxima página.

## Ambiente


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


O proxy trata TLS, normalização e proteção volumétrica. A passagem verifica diagnósticos antes do router; rotas de negócio não leem contas ou sessões. Identidades Guardian são separadas de Symfony. Guardar cofre privado e chave juntos fora do diretório público. PHP ou servidor comprometido pode expor ambos. O armazenamento é mono-servidor. Erros de arranque, cache ou proxy podem afetar toda a aplicação.

Guardar cofre e chave juntos e testar restauro. Chaves perdidas não são regeneradas automaticamente. Após remoção verificar o profiler apenas em privado. Produção: APP_DEBUG=0, WebProfilerBundle e recolha desativados. Uma cadeia local não prova integridade num servidor comprometido.

## Recuperação e verificação

Limpa a lista IP e desativa horários, JIT e emergência remota. Autenticação, MFA, bloqueio global e bloqueios de entrada mantêm-se. Reconfigurar as restrições desejadas no painel.

on fecha os diagnósticos e elimina todas as sessões; o site mantém-se disponível. off levanta apenas o bloqueio global: é necessário novo início de sessão e as restantes políticas mantêm-se.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
