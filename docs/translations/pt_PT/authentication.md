# Inscrições e senhas

[Índice da documentação](README.md)


## Contas e Primeira Inscrição

Bem-vindo ao Guardião. sua senha inicial é temporária. Escolha um novo segredo: o painel e os diagnósticos permanecem fechados até que você faça.

Guardian As contas são separadas das contas do site.A conta inicial e as novas contas devem mudar suas senhas antes de continuar.O rolo de segurança permite informações sem acesso a perfis.Mudança de um papel ou palavra-passe invalida sessões anteriores.

Prepara o caminho privado, chave, cofre, ficheiros Symfony e um administrador se necessário. Preserva contas existentes. Recusa produção. Após execução direta, limpar a cache e executar as verificações.


```bash
php bin/console guardian:access --env=dev
```


| Requisitos de senha salvos | Duração mínima (Unicode Os personagens) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Política de Password

Estas regras aplicam-se à criação e alterações de senhas, na web e no CLIEles não revelam ou inspeccionam senhas existentes.

O valor inicial: 15 Caracteres.Utiliza uma palavra-passe única ou um gerenciador de senhas.As categorias de caracteres necessárias não garantem uma senha imprevisível.

Neste servidor, Guardian Utilizando automaticamente Argon2id para novas senhas. mantê-las PHP Suporte permitido. o limite de aplicação é 512 UTF-8 em bytes.

Esta PHP O servidor não fornece Argon2id. Guardian Por isso, usa bcrypt e rejeita as senhas exceder 72 UTF-8 Pergunte ao seu administrador de sistema para permitir Argon2id Apoio em PHP.

Esta opção revoga imediatamente as sessões existentes, incluindo a sua. Contas devem mudar suas senhas antes de configurar o Guardian ou abrir diagnósticos.

Altera a palavra-passe da conta username existente por entrada oculta conforme a política, revoga as sessões e regista a ação. Não passar palavras-passe como argumento.

## Locação Progressiva

Cada etapa define um limite de falha, contando janelas, duração de lockout e repetição da janela. 10 O fracasso provoca a 10-minute lockout. falhas repetidas podem desencadear a próxima etapa. cheques cobrem tanto conta e origem da rede, incluindo confirmações do administrador.

Contadores cifrados independentes por conta normalizada e origem; IP em bruto não é guardado. Uma reserva atómica antecede o hash e expira em 120 segundos. Reservas abandonadas contam como falhas; resultados tardios não criam sessões. CSRF inválido não reserva nada. Uma entrada válida apaga falhas da conta mas conserva reincidência e origem. Com 10000 entradas ativas recusam-se novas identidades em vez de expulsar bloqueios.

As etapas funcionam em ordem. Após um bloqueio, novas falhas dentro da janela de repetição usam a próxima etapa. Fora dessa janela, a primeira fase é aplicada novamente. A última etapa se repete. Pedidos bloqueados não estendem o atraso. 8 Fase: Recuperação local guardian:login:unlock --all.

| Etapa | Requisitos de senha salvos |
|---|---|
| Falhas antes do lockout | 1–100 |
| Locação (minutos e minutos) | 1–10080 |
| Avaliação da janela (minutos) | 1–1440 |
| Repita a janela depois de desbloquear (minutos) | 1–10080 |
| Etapa | 1–8; block[n+1] ≥ block[n] |
| Valor inicial | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Laboratório de Pesquisa Exhaustive

Compare uniformemente segredos aleatórios de um comprimento fixo sem entrar uma palavra-passe real. taxas e preços do hardware são hipóteses ajustáveis. Não se enviam tentativas de assinatura.

Categorias ASCII disjuntas e BigInt. Inclusão-exclusão calcula o espaço exato; categorias ausentes ou comprimento insuficiente dão zero. Posição média sem reposição: (N+1)/2. A espera considera limiares e último patamar repetido. Desativa-se a estimativa se o débito não respeitar as janelas. Custo offline: horas pelo preço horário suposto.

Com um alfabeto fixo, exigindo uma categoria reduz o número de segredos compatíveis. Expandir o alfabete é uma operação diferente. Tempos de lockout assumem suposições contínuas rápido o suficiente para ficar dentro das janelas configuradas.

Este modelo não mede a força de uma senha escolhida pelo homem. exclui dicionários, reutilização, vazamentos e estratégias direcionadas. bloqueio web não protege hashes roubados. A taxa inicial offline é ilustrativa, não um referencial. Local CPU A medição: guardian:password:benchmark.

Grandes números são escritos em pleno, com agrupamento de dígitos e sem notação E. Os tempos e os custos são redondados. Compare a mesma linha usando as mesmas suposições.

| Simulação de alfabeto disponível | possíveis segredos |
|---|---|
| ASCII Baixo Caso | 26 |
| ASCII Caminhão | 26 |
| ASCII Digitais | 10 |
| ASCII Símbolos que excluem espaço | 32 |
| Taxa assumida on-line (guesses/s) | 1 / s |
| Taxa assumida offline (hashes/s) | 10000 / s |
| Custo de hardware (€/hora) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## Recuperação e verificação

Guardar cofre e chave juntos e testar restauro. Chaves perdidas não são regeneradas automaticamente. Após remoção verificar o profiler apenas em privado. Produção: APP_DEBUG=0, WebProfilerBundle e recolha desativados. Uma cadeia local não prova integridade num servidor comprometido.

Reinicia os contadores de username. --ip ENDEREÇO elimina também o contador dessa origem IPv4/IPv6; caso contrário pode persistir um bloqueio de rede. --all elimina todos. Contas, palavras-passe e sessões não mudam; a ação é registada.

[Referência dos comandos](commands.md)
