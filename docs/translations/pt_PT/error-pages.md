# Páginas de erro público

[Índice da documentação](README.md)


## Páginas de erro público

Mantenha as páginas da aplicação ou apresente HTML neutro. Os códigos de estado HTTP reais mantêm-se.

Modo Nativo permite Symfony O modo genérico ignora texto personalizado.Só o modo personalizado usa a mensagem do visitante.A referência pública pode ser ausente, aleatório ou fixa; é independente da mensagem. HTTP Os códigos de status permanecem inalterados.

Páginas de aplicação: preserva o site existente 404 Resposta; Exceções usam 404 modelo quando disponível, caso contrário uma página neutra. neutral: mensagem padrão. personalizado: usa seu texto.

Exceções 500 anónimas recebem uma página neutra. Os detalhes exigem identidade Guardian autorizada e GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Modelos Twig ausentes ou com falhas usam o modo neutro; controladores de erro próprios precisam de integração. JSON e páginas de negócio bem-sucedidas não mudam.

| error_mode | Definições |
|---|---|
| native | Páginas de aplicação: preserva o site existente 404 Resposta; Exceções usam 404 modelo quando disponível, caso contrário uma página neutra. neutral: mensagem padrão. personalizado: usa seu texto. |
| generic | HTML 400–599 → Mantenha as páginas da aplicação ou apresente HTML neutro. Os códigos de estado HTTP reais mantêm-se. |
| custom | Disponível apenas com uma mensagem neutra personalizada. O texto é preservado ao mudar de modo. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Disponível apenas com uma referência estática personalizada. |
| error_custom_text | Disponível apenas com uma mensagem neutra personalizada. O texto é preservado ao mudar de modo. |

## Referência apresentada

Mensagens diferentes por estado ou referências estáticas podem revelar o erro real. Os códigos HTTP não devem ser falsificados.

A referência se aplica a GuardianPáginas neutras: o acaso gera um novo código, não uma mensagem aleatória.

As escolhas são validadas no servidor antes de guardar.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Rede e implantação

A lista de permissões IP aceita IPv4, IPv6 e CIDR Redes - Verificação Symfony Configuração de proxy confiável antes de ativá-lo. HTTPS Para cookies seguras. Guardian Não repara servidores web expostos ou bases de dados.

Recusar diagnósticos se identidade, função, sessão, rede, horários, JIT ou bloqueio não puderem ser verificados. O navegador não decide expirações. Consulta e recolha são distintas. Reconhecimento de dispositivo não autentica. Emergência exige identidade, código consumido e auditoria duradoura; falha crítica impede abertura. Não aplicar parcialmente configuração inválida. Controlos opcionais não impõem serviço remoto.
