# 翻译

[文档目录](README.md)

门户提供 28 个区域选项和 25 个语言目录；四个英语区域共享目录。静态文本先翻译再转义。用户名、自定义消息和用户数据不会翻译，也不会向远程服务发送文本。审计检查键、参数、UTF-8、技术命令和文档链接；HTTP 测试遍历每个区域的页面。
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
