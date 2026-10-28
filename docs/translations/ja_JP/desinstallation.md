# 解体とデータ

[ドキュメント目次](README.md)

Guardian に由来すると確認できる生成ファイルの削除を準備します。アカウント、設定、ログ、鍵は既定で保持します。ワーカーを停止し composer remove 前に実行し、dev キャッシュを消去して再起動します。カスタムファイルや由来不明のファイルを確認してください。

--purge は既定で拒否する確認付きのデータ削除です。--force は --purge と共に破棄を承認し、--no-interaction 使用時は必須です。外部の保管庫、リンク、不明なファイルにより完全削除できない場合があります。必要な鍵を保持し、結果を確認してください。
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
保管庫と鍵を一緒に保存し、復元をテストしてください。失った鍵は自動再生成しません。削除後のプロファイラー確認は非公開環境のみ。本番は APP_DEBUG=0、WebProfilerBundle と収集を無効化します。侵害されたホストのローカル監査チェーンは完全性を保証しません。
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## コマンドリファレンス

--purge は既定で拒否する確認付きのデータ削除です。--force は --purge と共に破棄を承認し、--no-interaction 使用時は必須です。外部の保管庫、リンク、不明なファイルにより完全削除できない場合があります。必要な鍵を保持し、結果を確認してください。


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[コマンドリファレンス](commands.md)


Composer が既に削除した場合、setup を実行せず同じ版を一時再インストールします。guardian:uninstall を実行し、再び削除してキャッシュを消去します。カスタム設定は手動で確認してください。
