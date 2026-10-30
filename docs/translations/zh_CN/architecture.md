# 架构与信任边界

[文档目录](README.md)


## 架构与信任边界

代理负责 TLS、规范化及大流量防护。网关在路由前检查诊断；业务路由不读取账户或会话。Guardian 身份独立于 Symfony。将私有保管库与密钥一起备份到公共目录之外。PHP 或主机失陷可能同时泄露两者。存储面向单主机。启动、缓存或代理故障可能影响整个应用。

IP允许列表接受 IPv4, IPv6 和 CIDR 网络,检查 Symfony 可靠的 proxy 配置,在启用它之前。 HTTPS 安全的Cookies。 Guardian 不修复曝光的网页服务器或数据库。

Guardian 控制个人资料和漏洞工具栏. 需要授权的管理员或开发人员帐户。 MFA时间表, JIT,网络限制和锁定仍然可能拒绝访问. 盗窃模式隐藏输入点; 不取代这些检查。

| PHP / HTTP | 架构与信任边界 |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| 會議 | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## 安全不变量

无法验证身份、角色、会话、网络、计划、JIT 或锁定时拒绝诊断。浏览器不决定过期时间。查看和收集分析资料相互独立。记住设备不等于认证。紧急访问需要身份、已消费代码和持久审计；关键审计失败禁止开放。不能部分应用无效配置。可选控制不强制使用远程服务。

一起备份保管库和密钥，并测试恢复。丢失密钥不会自动重新生成。卸载后仅在私有环境检查分析器。生产环境：APP_DEBUG=0，禁用 WebProfilerBundle 和收集。受侵主机上的本地审计链不能证明完整性。

## 报纸和警告

水平和保留决定可用的事件,到 5000 输入. 在页面更改之前按类型和帐户过滤. 警告被转换; 设置运输和交付。
