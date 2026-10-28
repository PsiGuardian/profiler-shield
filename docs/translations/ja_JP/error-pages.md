# 公共エラーページ

[ドキュメント目次](README.md)


## 公共エラーページ

アプリのページを維持するか、中立的な HTML を表示します。実際の HTTP ステータスコードは変更しません。

オリジナルタイトル:Native Mode Lets Symfony ゲネリックモードは、カスタマイズされたテキストを無視します. カスタマイスされたモードのみが訪問者メッセージを使用します. 公開参照は欠席、ランダムまたは固定であり得る。 HTTP ステータスコードは変わらない。

アプリケーションページ:既存のサイトを保存する 404 答え:例外利用 404 テンプレートが利用可能であれば、中立なページです。中立:標準メッセージ.カスタマイズ:あなたのテキストを使用します。

匿名の 500 例外は中立ページになります。詳細には権限のある Guardian ID と GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1 が必要です。Twig テンプレートがない場合や失敗時は中立表示へ戻ります。独自エラーコントローラーは別途統合が必要です。JSON と成功した業務ページは変えません。

| error_mode | 設定 |
|---|---|
| native | アプリケーションページ:既存のサイトを保存する 404 答え:例外利用 404 テンプレートが利用可能であれば、中立なページです。中立:標準メッセージ.カスタマイズ:あなたのテキストを使用します。 |
| generic | HTML 400–599 → アプリのページを維持するか、中立的な HTML を表示します。実際の HTTP ステータスコードは変更しません。 |
| custom | カスタムの中立メッセージでのみ使用できます。モードを変更してもテキストは保持されます。 (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | 単にカスタマイズされた静的参照で利用できます。 |
| error_custom_text | カスタムの中立メッセージでのみ使用できます。モードを変更してもテキストは保持されます。 |

## 表示する参照番号

エラーごとの異なるメッセージや固定番号は本来のエラーを推測させます。HTTP コードを偽装してはいけません。

この参照は、 Guardianニュートラルページ:ランダムで新しいコードを生成し、偶然のメッセージではない。

保存前にサーバー側で内容を検証します。

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## ネットワークと開発

IP許可リストが承認 IPv4, IPv6 そして、 CIDR ネットワークチェック Symfony 信頼できるプロキシの設定を有効にする前に。 HTTPS 安全なクッキーのために Guardian 暴露されたウェブサーバーやデータベースを修理しない。

ID、役割、セッション、ネットワーク、予定、JIT、ロックが確認不能なら診断を拒否します。期限はブラウザーで決定しません。表示と収集は別です。端末の記憶は認証ではありません。緊急アクセスには ID、使用済みコード、永続的監査が必要で、監査の重大な失敗時は開きません。不正設定を部分適用しません。任意の保護は外部サービスを必須にしません。
