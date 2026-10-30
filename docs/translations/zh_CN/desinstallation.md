# 拆除和数据

[文档目录](README.md)

准备移除可证明由 Guardian 生成的文件，默认保留账户、设置、日志和密钥。停止工作进程，在 composer remove 之前执行，然后清除 dev 缓存并重启。检查自定义或来源不明的文件。

--purge 请求删除数据，确认默认拒绝。--force 必须配合 --purge，表示确认销毁；自动使用 --no-interaction 时必需。外部保管库、链接和未知文件可能阻止完全清除。保留必要密钥并检查结果。
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
一起备份保管库和密钥，并测试恢复。丢失密钥不会自动重新生成。卸载后仅在私有环境检查分析器。生产环境：APP_DEBUG=0，禁用 WebProfilerBundle 和收集。受侵主机上的本地审计链不能证明完整性。
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 命令参考

--purge 请求删除数据，确认默认拒绝。--force 必须配合 --purge，表示确认销毁；自动使用 --no-interaction 时必需。外部保管库、链接和未知文件可能阻止完全清除。保留必要密钥并检查结果。


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[命令参考](commands.md)


如果 Composer 已移除包，请临时重新安装相同版本但不运行 setup。执行 guardian:uninstall，再移除包并清除缓存。手动检查自定义配置。
