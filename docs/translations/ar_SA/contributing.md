# المساهمة

[فهرس الوثائق](README.md)

اعمل في تطبيق Symfony تجريبي معزول. حافظ على توفر العمل ورفض التشخيص غير المخول. اختبر الطلبات المجهولة والجلسات الملغاة والجداول وأعطال التخزين في الإصدارات المعنية. استخدم مفاتيح ثابتة وهروبًا آمنًا وترجمات كاملة. لا أسرار في الاختبارات أو الصور أو Git ولا اعتماد شبكي إلزامي. صف الآثار والتحقق.
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[سياسة الأمان](security.md) · [أمثلة الواجهة](examples.md)
