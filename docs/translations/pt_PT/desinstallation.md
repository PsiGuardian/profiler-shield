# Desinstalação e dados

[Índice da documentação](README.md)

Prepara a remoção dos ficheiros gerados atribuíveis a Guardian. Preserva contas, definições, registos e chave. Parar workers, executar antes de composer remove, limpar cache dev e reiniciar. Rever ficheiros personalizados ou sem prova de origem.

--purge pede eliminação com confirmação negativa por predefinição. --force requer --purge e confirma a destruição; obrigatório com --no-interaction. Cofres externos, ligações e ficheiros desconhecidos podem impedir a eliminação completa; conservar as chaves necessárias e rever o resultado.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Guardar cofre e chave juntos e testar restauro. Chaves perdidas não são regeneradas automaticamente. Após remoção verificar o profiler apenas em privado. Produção: APP_DEBUG=0, WebProfilerBundle e recolha desativados. Uma cadeia local não prova integridade num servidor comprometido.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Referência dos comandos

--purge pede eliminação com confirmação negativa por predefinição. --force requer --purge e confirma a destruição; obrigatório com --no-interaction. Cofres externos, ligações e ficheiros desconhecidos podem impedir a eliminação completa; conservar as chaves necessárias e rever o resultado.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Referência dos comandos](commands.md)


Se Composer já retirou o pacote, reinstalar temporariamente a mesma versão sem setup. Executar guardian:uninstall, retirar novamente e limpar a cache. Rever manualmente configurações personalizadas.
