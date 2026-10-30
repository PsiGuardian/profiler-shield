# ΩGuardian Profiler Shield — インストールと使用方法

PHP 8.3 以降と Symfony 5.4、6.4 または 7.4 が必要です。Symfony 8.1 には PHP 8.4 以降が必要です。Composer と OpenSSL + Tokenizer 拡張が必要です。プロファイラーを使用する場合は WebProfilerBundle をインストールしてください。

## 1. インストール

composer.json と bin/console がある Symfony アプリケーションのルートで実行します。

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. 設定

```bash
php vendor/bin/guardian setup
```

アシスタントは dev/test でバンドルを有効にし、保管庫と管理者アカウントを準備し、キャッシュを消去して設定を確認します。各手順の成功後に次へ進んでください。設定中はアプリケーションを非公開にしてください。再実行しても既存のアカウントと鍵は保持されます。

## 3. パネルを開く

```bash
php bin/console guardian:access --env=dev
```

コマンドで非公開のパスを表示します。https://VOTRE-HOTE をポートを含む実際の HTTPS オリジンに置き換えてください。.guardian-shield/initial-admin-credentials.txt または指定の保管庫で認証情報を確認します。初回ログイン時にパスワード変更が必要です。このファイルを共有しないでください。

## 4. アンインストール

ワーカーを停止し、各手順の成功を確認しながら順番に実行します。保管庫と鍵は保持されます。ワーカーを再起動し、非公開環境でプロファイラーを確認してください。

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

アカウントと設定も削除する場合は、最初のコマンドを次のコマンドに置き換え、削除を確認します。拒否すると変更されません。外部の保管庫とカスタムファイルは手動で確認し、保持する保管庫の鍵を残してください。

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. コマンドリファレンス

ヘルプでコマンドを確認できます。アクセスコマンドはパネルの場所を示し、診断コマンドは設定を確認します。信頼できるローカルアクセスが必要です。

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[コマンドリファレンス](../translations/ja_JP/commands.md) · [ドキュメント目次](../translations/ja_JP/README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

組織で Private Packagist を使用する場合は、インストール前にこのリポジトリを設定し、組織の指示に従って Composer の認証情報を保存してください。トークンを Git に登録しないでください。

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
