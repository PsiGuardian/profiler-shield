# 命令参考

[文档目录](README.md)

在 Symfony 根目录的可信终端运行。示例使用 dev。不要将密码、密钥或恢复码放入参数、日志或 Git。

## 发现命令与获取帮助


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev 和 --no-interaction 属于 Symfony。--no-interaction 不提供秘密，不适用于交互式密码命令。--help 显示语法，-v 增加细节。退出码 0 表示命令报告的结果成功；--no-admin 即使没有可用门户也可能成功。

## `vendor/bin/guardian`

在 dev/test 启用此包，清除缓存，运行 guardian:setup --automatic，再次清除缓存，然后运行 guardian:doctor 和 guardian:access。首次出错即停止。重复运行保留账户和密钥。拒绝 APP_ENV=prod，需要 proc_open。

无参数、--help、-h 或 help 仅显示帮助。setup 是唯一操作，不接受附加选项。


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | 命令参考 |
|---|---|
| [`guardian:setup`](#guardiansetup) | 准备私有路径、密钥、保管库、Symfony 文件，并在没有活动管理员时创建管理员。 |
| [`guardian:install`](#guardianinstall) | 初始化保管库并通过 --username 创建管理员，默认 admin。 |
| [`guardian:access`](#guardianaccess) | 显示面板路径和初始凭据位置，绝不显示密码。 |
| [`guardian:doctor`](#guardiandoctor) | 检查保管库、权限、密钥、路由、活动管理员和配置。 |
| [`guardian:password:reset`](#guardianpasswordreset) | 通过符合当前策略的隐藏输入更改现有 username 账户的密码，撤销所有会话并记录操作。 |
| [`guardian:mfa:reset`](#guardianmfareset) | 移除 username 的 TOTP 秘密和计数器，撤销会话并记录操作。 |
| [`guardian:login:unlock`](#guardianloginunlock) | 重置 username 的账户计数器。 |
| [`guardian:policy:reset`](#guardianpolicyreset) | 清空 IP 允许列表，关闭时间计划、JIT 和远程紧急访问。 |
| [`guardian:lockdown`](#guardianlockdown) | on 关闭诊断并删除所有会话，业务页面仍可用。 |
| [`guardian:emergency`](#guardianemergency) | 为现有 username 生成 5 个一次性代码，替换原有代码。 |
| [`guardian:alerts:flush`](#guardianalertsflush) | 通过 PHP mail() 将排队警报发往 alert_email。 |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | 用合成秘密测试哈希验证，不使用用户密码。 |
| [`guardian:uninstall`](#guardianuninstall) | 准备移除可证明由 Guardian 生成的文件，默认保留账户、设置、日志和密钥。 |

## `guardian:setup`

准备私有路径、密钥、保管库、Symfony 文件，并在没有活动管理员时创建管理员。保留现有账户。拒绝生产环境。直接运行后应清除缓存并执行检查。


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic 生成 admin 和保存在私有 0600 文件中的秘密。--path 选择 25–181 字符的路径，不替换现有路径。--no-admin 跳过创建管理员；没有活动账户时门户不可用。--username 提供交互式账户名，默认 admin；不会重命名自动账户。

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

初始化保管库并通过 --username 创建管理员，默认 admin。按当前策略要求隐藏输入密码。拒绝已有账户。不创建路由或 Symfony 配置；通常应使用完整助手。


```bash
php bin/console guardian:install --username=admin --env=dev
```


用户名: 3 到 64 阿尔巴尼亚字符,

## `guardian:access`

显示面板路径和初始凭据位置，绝不显示密码。--base-url 接受真实 HTTPS 来源地址，可含端口，不能含凭据、查询或片段。替换示例主机。


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

检查保管库、权限、密钥、路由、活动管理员和配置。发现问题时退出码为 1。PHP CLI 与 PHP-FPM 时区可能不同。不检查互联网暴露情况或代理规则。


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

通过符合当前策略的隐藏输入更改现有 username 账户的密码，撤销所有会话并记录操作。不要将密码作为参数。


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

移除 username 的 TOTP 秘密和计数器，撤销会话并记录操作。重新注册验证器；全局 MFA 要求仍有效，可能继续阻止分析器访问。


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

重置 username 的账户计数器。--ip 地址 同时清除该 IPv4/IPv6 来源的计数器，否则网络锁定可能仍存在。--all 清除全部计数器。账户、密码和会话不变；操作会记录。


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

清空 IP 允许列表，关闭时间计划、JIT 和远程紧急访问。身份验证、MFA、全局锁定和登录锁定仍有效。恢复后在面板重新配置所需限制。


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on 关闭诊断并删除所有会话，业务页面仍可用。off 仅解除全局锁定：必须重新登录，其他策略继续生效。


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

为现有 username 生成 5 个一次性代码，替换原有代码。仅显示一次；请保存在服务器外的秘密管理器中。远程使用需要开启紧急访问、已认证会话、密码和原因。不能绕过全局锁定。


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

通过 PHP mail() 将排队警报发往 alert_email。配置邮件传输和计划任务并验证实际送达。失败警报保留在队列中。收件人无效或发送失败时返回 1。


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

用合成秘密测试哈希验证，不使用用户密码。--iterations 接受 1–10，默认 3。输出包含算法、耗时和每秒验证次数的 JSON。只测量一个本地 CPU 进程，不代表 GPU 或攻击者。


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

准备移除可证明由 Guardian 生成的文件，默认保留账户、设置、日志和密钥。停止工作进程，在 composer remove 之前执行，然后清除 dev 缓存并重启。检查自定义或来源不明的文件。


```bash
php bin/console guardian:uninstall --env=dev
```

--purge 请求删除数据，确认默认拒绝。--force 必须配合 --purge，表示确认销毁；自动使用 --no-interaction 时必需。外部保管库、链接和未知文件可能阻止完全清除。保留必要密钥并检查结果。

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## 卸载与数据


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


一起备份保管库和密钥，并测试恢复。丢失密钥不会自动重新生成。卸载后仅在私有环境检查分析器。生产环境：APP_DEBUG=0，禁用 WebProfilerBundle 和收集。受侵主机上的本地审计链不能证明完整性。

[拆除和数据](desinstallation.md)
