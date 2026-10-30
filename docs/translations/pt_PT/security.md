# Política de segurança

[Índice da documentação](README.md)

Comunicar vulnerabilidades em privado a bundle-sec@omegaguardian.fr para combinar um canal cifrado. Sem segredos, tokens ativos ou URLs privados no primeiro contacto. Indicar versões Guardian, Symfony e PHP, impacto, requisitos e reprodução mínima. Os responsáveis confirmam, investigam, corrigem em privado, testam e coordenam aviso e correção.

Nunca envie palavras-passe, segredos TOTP, chaves de encriptação ou URLs de diagnóstico por email.

Guardar cofre e chave juntos e testar restauro. Chaves perdidas não são regeneradas automaticamente. Após remoção verificar o profiler apenas em privado. Produção: APP_DEBUG=0, WebProfilerBundle e recolha desativados. Uma cadeia local não prova integridade num servidor comprometido.
