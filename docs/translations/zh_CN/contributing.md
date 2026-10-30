# 参与贡献

[文档目录](README.md)

在隔离的 Symfony 测试应用中工作。保持业务可用并拒绝未授权诊断。在相关版本测试匿名请求、已撤销会话、计划和存储故障。使用稳定键、安全转义和完整翻译。测试、截图或 Git 中不得含秘密，不引入强制网络依赖。说明影响和验证。
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[安全政策](security.md) · [界面示例](examples.md)
