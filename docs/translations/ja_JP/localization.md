# 翻訳

[ドキュメント目次](README.md)

ポータルは 28 の地域設定と 25 のカタログを提供し、4 つの英語設定は共有します。静的テキストを翻訳してエスケープします。利用者名、カスタムメッセージ、利用者データは翻訳せず、外部サービスへテキストを送信しません。監査はキー、パラメーター、UTF-8、命令、リンクを検査し、HTTP テストは全地域の画面を確認します。
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
