# 公共错误页面

[文档目录](README.md)


## 公共错误页面

保留应用程序自己的页面，或向访问者显示中性 HTML。实际 HTTP 状态码保持不变。

原生风格允许 Symfony 一般模式忽略了自定义文本,仅使用自定義模式的访问者消息,公共参考可以是缺席、随机或固定;它独立于信息。 HTTP 状态代码保持不变。

应用页面:保留现有网站 404 答案:例外使用 404 模板当可用,否则中立页面. 中立:标准消息. 自定义:使用您的文本。

匿名 500 异常返回中性页面。详细错误要求获得授权的 Guardian 身份及 GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1。Twig 模板缺失或渲染失败时回退到中性页面；主机自定义错误控制器需要单独集成。JSON 和成功的业务页面保持不变。

| error_mode | 设置 |
|---|---|
| native | 应用页面:保留现有网站 404 答案:例外使用 404 模板当可用,否则中立页面. 中立:标准消息. 自定义:使用您的文本。 |
| generic | HTML 400–599 → 保留应用程序自己的页面，或向访问者显示中性 HTML。实际 HTTP 状态码保持不变。 |
| custom | 仅适用于自定义中性消息。切换模式时会保留文本。 (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | 只可用自定义静态参考。 |
| error_custom_text | 仅适用于自定义中性消息。切换模式时会保留文本。 |

## 显示的参考标识

不同的消息或固定标识可能泄露真实错误。不得伪造 HTTP 状态码。

引用适用于 Guardian’s 中立的页面:随机产生一个新的代码,而不是随机消息。

保存前由服务器验证所有选择。

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## 网络与部署

IP允许列表接受 IPv4, IPv6 和 CIDR 网络,检查 Symfony 可靠的 proxy 配置,在启用它之前。 HTTPS 安全的Cookies。 Guardian 不修复曝光的网页服务器或数据库。

无法验证身份、角色、会话、网络、计划、JIT 或锁定时拒绝诊断。浏览器不决定过期时间。查看和收集分析资料相互独立。记住设备不等于认证。紧急访问需要身份、已消费代码和持久审计；关键审计失败禁止开放。不能部分应用无效配置。可选控制不强制使用远程服务。
