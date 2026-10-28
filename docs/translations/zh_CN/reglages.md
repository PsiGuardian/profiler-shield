# 设置

[文档目录](README.md)


## 附加保护

保护选项与其设置分组显示。禁用的选项变灰，数值保留供以后使用。

Guardian 控制个人资料和漏洞工具栏. 需要授权的管理员或开发人员帐户。 MFA时间表, JIT,网络限制和锁定仍然可能拒绝访问. 盗窃模式隐藏输入点; 不取代这些检查。


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| 配置方案 | 设置 |
|---|---|
| 单身 · 灵活 | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| 团队 · 增强 | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| 敏感 · 有限 | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| 参数 | 设置 | 初始值 |
|---|---|---|
| `stealth` | 对未经授权的诊断路由返回 404 而非 403。 | `true` |
| `mfa` | 启用强制 MFA 前，所有用户都必须完成 TOTP 注册。 | `false` |
| `idle_minutes` | 会话到期前允许无活动的分钟数。 | `60` |
| `absolute_hours` | 小时数，与活动情况无关。 | `8` |
| `remember_days` | 天数。不能代替登录或工作时间策略。 | `14` |
| `schedule_enabled` | 选择诊断工具可访问的时间。业务页面仍可正常使用。 | `false` |
| `weekdays` | 选择允许诊断的日期。 | `1,2,3,4,5` |
| `start` | 开放时间 | `09:00` |
| `end` | 关闭时间 | `18:00` |
| `timezone` | 根据处理此页面的 PHP 进程检测。PHP CLI、PHP-FPM 与操作系统的时区可能不同。保存前请核对服务器配置。 | `OS / PHP CLI` |
| `portal_closed_outside_hours` | 私人 URL 在工作时间外返回 404。仅可通过本地命令行恢复。 | `false` |
| `jit_enabled` | 仅在申请的诊断时间窗口开放 Profiler。 | `false` |
| `jit_minutes` | 单位为分钟，仅启用后生效。 | `30` |
| `ip_allowlist` | 警告：配置错误可能锁定您的访问。可通过本地 guardian:policy:reset 恢复。 | `∅` |
| `audit_level` | 选择审计详细程度和本地保留时间。 | `standard` |
| `audit_retention_days` | 清理日志前的天数。 | `30` |
| `alert_email` | 接收紧急警报。访问权限不能依赖邮件是否送达。 | `∅` |
| `emergency_enabled` | 允许使用一次性代码并记入审计的 30 分钟例外。 | `false` |
| `login_rules` | 默认: 10 失败,然后A 10会计适用于帐户和网络起源,包括密码确认。 | `10 / 10 / 30 / 30` |
| `password_policy` | 这些规则适用于密码创建和更改,在网页上和 CLI他们不透露或检查现有密码。 | `min_length=15` |
| `error_mode` | 应用页面:保留现有网站 404 答案:例外使用 404 模板当可用,否则中立页面. 中立:标准消息. 自定义:使用您的文本。 | `native` |
| `error_reference` | 引用适用于 Guardian’s 中立的页面:随机产生一个新的代码,而不是随机消息。 | `none` |
| `error_reference_text` | 只可用自定义静态参考。 | `∅` |
| `error_custom_text` | 仅适用于自定义中性消息。切换模式时会保留文本。 | `∅ → custom` |

允许安排,选择日期,包括开始时间和独家结束时间. 节省的时间区决定当地时间. 上夜间隔不被接受. 隐藏门户在外时阻止远程紧急访问; 保持当地恢复命令可用。

Idle timeout,绝对寿命和设备记忆服务于不同的目的。 JIT. 撤销终止服务器上的相关会议. 使用个别帐户在日志中分配行动。

JIT 需要从授权的会话中激活一个临时窗口. 紧急访问,当启用时,需要密码、单用代码和理由。

IP允许列表接受 IPv4, IPv6 和 CIDR 网络,检查 Symfony 可靠的 proxy 配置,在启用它之前。 HTTPS 安全的Cookies。 Guardian 不修复曝光的网页服务器或数据库。

水平和保留决定可用的事件,到 5000 输入. 在页面更改之前按类型和帐户过滤. 警告被转换; 设置运输和交付。

在连接和密码中设置最低长度和所需类别. 帐户创建标签描述了保存的政策. 适用于新密码的变更; 选择现有帐户的强制更新。 Guardian 选择 Argon2id 當時 PHP 提供它,否则 bcrypt.

按类型和帐户过滤所有保存的事件. 最新事件首先显示,在页面上 50Scrolling 不加载下一页。

## 环境


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


代理负责 TLS、规范化及大流量防护。网关在路由前检查诊断；业务路由不读取账户或会话。Guardian 身份独立于 Symfony。将私有保管库与密钥一起备份到公共目录之外。PHP 或主机失陷可能同时泄露两者。存储面向单主机。启动、缓存或代理故障可能影响整个应用。

一起备份保管库和密钥，并测试恢复。丢失密钥不会自动重新生成。卸载后仅在私有环境检查分析器。生产环境：APP_DEBUG=0，禁用 WebProfilerBundle 和收集。受侵主机上的本地审计链不能证明完整性。

## 恢复和验证

清空 IP 允许列表，关闭时间计划、JIT 和远程紧急访问。身份验证、MFA、全局锁定和登录锁定仍有效。恢复后在面板重新配置所需限制。

on 关闭诊断并删除所有会话，业务页面仍可用。off 仅解除全局锁定：必须重新登录，其他策略继续生效。


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
