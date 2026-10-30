# アーキテクチャと信頼境界

[ドキュメント目次](README.md)


## アーキテクチャと信頼境界

プロキシは TLS、正規化、大量通信への防御を担当します。ゲートウェイはルーティング前に診断を検査し、業務ルートではアカウントやセッションを読みません。Guardian の ID は Symfony と独立しています。保管庫と鍵を一緒に公開ディレクトリ外へバックアップしてください。PHP やホストの侵害で両方が漏れる可能性があります。保存先は単一ホストです。起動、キャッシュ、プロキシの障害はアプリ全体に影響し得ます。

IP許可リストが承認 IPv4, IPv6 そして、 CIDR ネットワークチェック Symfony 信頼できるプロキシの設定を有効にする前に。 HTTPS 安全なクッキーのために Guardian 暴露されたウェブサーバーやデータベースを修理しない。

Guardian プロフィールとデビューツールバーを制御します. 有効な管理者または開発者のアカウントが必要です。 MFAスケジュール、 JIT, ネットワーク制限およびロックダウンはまだアクセスを拒否する可能性があります. Stealth モードは入力ポイントを隠し、これらのチェックを置き換えることはありません。

| PHP / HTTP | アーキテクチャと信頼境界 |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| セッション | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## セキュリティの不変条件

ID、役割、セッション、ネットワーク、予定、JIT、ロックが確認不能なら診断を拒否します。期限はブラウザーで決定しません。表示と収集は別です。端末の記憶は認証ではありません。緊急アクセスには ID、使用済みコード、永続的監査が必要で、監査の重大な失敗時は開きません。不正設定を部分適用しません。任意の保護は外部サービスを必須にしません。

保管庫と鍵を一緒に保存し、復元をテストしてください。失った鍵は自動再生成しません。削除後のプロファイラー確認は非公開環境のみ。本番は APP_DEBUG=0、WebProfilerBundle と収集を無効化します。侵害されたホストのローカル監査チェーンは完全性を保証しません。

## 新聞と警告

レベルと維持は、利用可能なイベントを決定し、 5000 入力. ページを変更する前にタイプとアカウントによってフィルタリング. アラームが表示されます; 輸送および配達を設定します. 地元のハッシュチェーンは、損なわれたホストの完全性を保証しません。
