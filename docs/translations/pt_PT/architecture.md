# Arquitetura e fronteiras de confiança

[Índice da documentação](README.md)


## Arquitetura e fronteiras de confiança

O proxy trata TLS, normalização e proteção volumétrica. A passagem verifica diagnósticos antes do router; rotas de negócio não leem contas ou sessões. Identidades Guardian são separadas de Symfony. Guardar cofre privado e chave juntos fora do diretório público. PHP ou servidor comprometido pode expor ambos. O armazenamento é mono-servidor. Erros de arranque, cache ou proxy podem afetar toda a aplicação.

A lista de permissões IP aceita IPv4, IPv6 e CIDR Redes - Verificação Symfony Configuração de proxy confiável antes de ativá-lo. HTTPS Para cookies seguras. Guardian Não repara servidores web expostos ou bases de dados.

Guardian controla o perfil e a barra de ferramentas debug. É necessária uma conta autorizada de administrador ou desenvolvedor. MFAe os horários, JITRestrições de rede e bloqueio podem ainda negar acesso. o modo de roubo esconde pontos de entrada; não substitui esses cheques.

| PHP / HTTP | Arquitetura e fronteiras de confiança |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| Sessão | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Invariantes de segurança

Recusar diagnósticos se identidade, função, sessão, rede, horários, JIT ou bloqueio não puderem ser verificados. O navegador não decide expirações. Consulta e recolha são distintas. Reconhecimento de dispositivo não autentica. Emergência exige identidade, código consumido e auditoria duradoura; falha crítica impede abertura. Não aplicar parcialmente configuração inválida. Controlos opcionais não impõem serviço remoto.

Guardar cofre e chave juntos e testar restauro. Chaves perdidas não são regeneradas automaticamente. Após remoção verificar o profiler apenas em privado. Produção: APP_DEBUG=0, WebProfilerBundle e recolha desativados. Uma cadeia local não prova integridade num servidor comprometido.

## Jornal e alertas

Nível e retenção determinam eventos disponíveis, até 5000 entradas. filtro por tipo e conta antes de mudar páginas. alertas são quebrados; configurar seu transporte e entrega. uma cadeia de hash local não garante a integridade em um host comprometido.
