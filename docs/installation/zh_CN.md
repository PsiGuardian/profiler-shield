# ΩGuardian Profiler Shield — 安装与使用

需要 PHP 8.3 或更高版本，以及 Symfony 5.4、6.4 或 7.4。Symfony 8.1 需要 PHP 8.4 或更高版本。需要 Composer 和 OpenSSL + Tokenizer 扩展。使用分析器时请安装 WebProfilerBundle。

## 1. 安装

在包含 composer.json 和 bin/console 的 Symfony 应用根目录中运行。

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. 配置

```bash
php vendor/bin/guardian setup
```

助手会在 dev/test 环境启用此包，准备保管库和管理员账户，清除缓存并检查配置。每一步成功后再继续。配置期间请保持应用私有。重新运行会保留现有账户和密钥。

## 3. 打开面板

```bash
php bin/console guardian:access --env=dev
```

命令会显示面板的私有路径。将 https://VOTRE-HOTE 替换为应用实际的 HTTPS 来源地址，包括端口。在 .guardian-shield/initial-admin-credentials.txt 或指定保管库中读取凭据。首次登录必须更改密码。请勿共享此文件。

## 4. 卸载

停止工作进程，依次执行命令，并确认每一步成功。保管库及其密钥会保留。重新启动工作进程，并在私有环境中检查分析器。

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

如需同时删除账户和设置，请将第一条命令替换为以下命令并确认删除。拒绝不会造成任何更改。请手动检查外部保管库和自定义文件；保留仍需使用的保管库密钥。

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. CLI

帮助提供命令说明。访问命令用于查找面板，诊断命令用于检查配置。这些操作需要可信的本地访问权限。

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## Private Packagist

如果组织使用 Private Packagist，请在安装前配置此仓库，并按照组织说明保存 Composer 凭据。切勿将令牌提交到 Git。

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
