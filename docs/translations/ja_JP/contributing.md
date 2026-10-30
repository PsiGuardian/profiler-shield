# 貢献

[ドキュメント目次](README.md)

分離した Symfony テストアプリで作業します。業務の可用性と不正診断の拒否を維持してください。対象版で匿名要求、取り消したセッション、予定、保存障害をテストします。安定したキー、エスケープ、全翻訳を使います。テスト、画像、Git に秘密を入れず、必須ネットワーク依存を作りません。影響と検証を説明します。
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[セキュリティ方針](security.md) · [画面の例](examples.md)
