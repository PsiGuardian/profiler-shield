# コマンドリファレンス

[ドキュメント目次](README.md)

Symfony のルートから信頼できる端末で実行します。例は dev を使います。パスワード、鍵、復旧コードを引数、ログ、Git に保存しないでください。

## コマンドの一覧とヘルプ


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev と --no-interaction は Symfony のオプションです。--no-interaction は秘密を入力しないため、対話式パスワード命令には使わないでください。--help は構文、-v は詳細を表示します。終了コード 0 は通知された結果の成功です。--no-admin は利用可能なポータルがなくても成功します。

## `vendor/bin/guardian`

dev/test で有効化し、キャッシュを消去、guardian:setup --automatic を実行、再び消去し、guardian:doctor と guardian:access を実行します。最初の失敗で停止します。再実行はアカウントと鍵を保持します。APP_ENV=prod を拒否し、proc_open が必要です。

引数なし、--help、-h、help は変更せずヘルプを表示します。操作は setup のみで、追加オプションはありません。


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | コマンドリファレンス |
|---|---|
| [`guardian:setup`](#guardiansetup) | 非公開パス、鍵、保管庫、Symfony ファイルを準備し、管理者がいなければ作成します。 |
| [`guardian:install`](#guardianinstall) | 保管庫を初期化し、--username で管理者を作成します。 |
| [`guardian:access`](#guardianaccess) | パネルのパスと初期認証情報の場所を表示し、パスワードは表示しません。 |
| [`guardian:doctor`](#guardiandoctor) | 保管庫、権限、鍵、ルート、有効な管理者、設定を検証します。 |
| [`guardian:password:reset`](#guardianpasswordreset) | 既存の username のパスワードを現在のポリシーに従う非表示入力で変更し、全セッションを取り消して記録します。 |
| [`guardian:mfa:reset`](#guardianmfareset) | username の TOTP 秘密とカウンターを削除し、セッションを取り消して記録します。 |
| [`guardian:login:unlock`](#guardianloginunlock) | username のカウンターをリセットします。 |
| [`guardian:policy:reset`](#guardianpolicyreset) | IP リストを空にし、予定、JIT、遠隔緊急アクセスを無効化します。 |
| [`guardian:lockdown`](#guardianlockdown) | on は診断を閉じて全セッションを削除します。 |
| [`guardian:emergency`](#guardianemergency) | 既存の username に 5 個の使い捨てコードを生成し、古いコードを置換します。 |
| [`guardian:alerts:flush`](#guardianalertsflush) | PHP mail() で待機中の警告を alert_email へ送信します。 |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | 利用者のパスワードではなく合成秘密でハッシュ検証を測定します。 |
| [`guardian:uninstall`](#guardianuninstall) | Guardian に由来すると確認できる生成ファイルの削除を準備します。 |

## `guardian:setup`

非公開パス、鍵、保管庫、Symfony ファイルを準備し、管理者がいなければ作成します。既存アカウントを保持します。本番環境を拒否します。直接実行後はキャッシュを消去して検証します。


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic は admin と秘密を専用 0600 ファイルに生成します。--path は 25–181 文字のパスを選び、既存パスを置換しません。--no-admin は管理者作成を省略し、有効アカウントがなければポータルは利用不能です。--username は対話時の名前候補で既定値 admin。自動作成の名前は変えません。

```text
--path: ^/[a-zA-Z0-9/_-]{24,180}$
/_profiler; /_wdt → ∅
```

```bash
php bin/console guardian:setup --username=operator --env=dev
php bin/console cache:clear --env=dev
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## `guardian:install`

保管庫を初期化し、--username で管理者を作成します。既定値は admin。現在のポリシーに従い非表示でパスワードを入力します。既存アカウントは拒否します。ルートや Symfony 設定は作りません。通常は完全なアシスタントを使います。


```bash
php bin/console guardian:install --username=admin --env=dev
```


ユーザー名: 3 へ 64 アルファノミリック・キャラクター・・・

## `guardian:access`

パネルのパスと初期認証情報の場所を表示し、パスワードは表示しません。--base-url は実際の HTTPS オリジンと任意のポートを受け付けます。認証情報、クエリ、フラグメントは不可です。例のホストを置き換えてください。


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

保管庫、権限、鍵、ルート、有効な管理者、設定を検証します。問題があれば終了コード 1。PHP CLI と PHP-FPM の時区は異なる場合があります。インターネット公開やプロキシの規則は検査しません。


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

既存の username のパスワードを現在のポリシーに従う非表示入力で変更し、全セッションを取り消して記録します。パスワードを引数にしないでください。


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

username の TOTP 秘密とカウンターを削除し、セッションを取り消して記録します。新しい認証器を登録してください。全体の MFA 要件は残り、プロファイラーを引き続き制限する場合があります。


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

username のカウンターをリセットします。--ip アドレス は IPv4/IPv6 送信元のカウンターも消去します。指定しないと送信元の制限が残る場合があります。--all は全カウンターを削除します。アカウント、パスワード、セッションは変更せず記録します。


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

IP リストを空にし、予定、JIT、遠隔緊急アクセスを無効化します。認証、MFA、全体ロック、ログイン制限は残ります。復旧後に必要な制限を設定し直してください。


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on は診断を閉じて全セッションを削除します。業務ページは動作します。off は全体ロックのみ解除します。再ログインが必要で、他のポリシーは有効です。


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

既存の username に 5 個の使い捨てコードを生成し、古いコードを置換します。一度しか表示されないため、サーバー外の秘密管理ツールに保管してください。遠隔利用には緊急アクセスの有効化、認証済みセッション、パスワード、理由が必要です。全体ロックは回避しません。


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

PHP mail() で待機中の警告を alert_email へ送信します。配送設定と定期タスクを構成し、実際の配信を検証してください。失敗した警告はキューに残ります。宛先不正や送信失敗は終了コード 1 です。


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

利用者のパスワードではなく合成秘密でハッシュ検証を測定します。--iterations は 1–10、既定値 3。アルゴリズム、時間、毎秒の検証回数を JSON で出力します。ローカル CPU プロセス一つの測定で、GPU や攻撃者の性能ではありません。


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Guardian に由来すると確認できる生成ファイルの削除を準備します。アカウント、設定、ログ、鍵は既定で保持します。ワーカーを停止し composer remove 前に実行し、dev キャッシュを消去して再起動します。カスタムファイルや由来不明のファイルを確認してください。


```bash
php bin/console guardian:uninstall --env=dev
```

--purge は既定で拒否する確認付きのデータ削除です。--force は --purge と共に破棄を承認し、--no-interaction 使用時は必須です。外部の保管庫、リンク、不明なファイルにより完全削除できない場合があります。必要な鍵を保持し、結果を確認してください。

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## アンインストールとデータ


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


保管庫と鍵を一緒に保存し、復元をテストしてください。失った鍵は自動再生成しません。削除後のプロファイラー確認は非公開環境のみ。本番は APP_DEBUG=0、WebProfilerBundle と収集を無効化します。侵害されたホストのローカル監査チェーンは完全性を保証しません。

[解体とデータ](desinstallation.md)
