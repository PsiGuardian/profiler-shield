# ΩGuardian Profiler Shield — التثبيت والاستخدام

يلزم PHP 8.3 أو أحدث مع Symfony 5.4 أو 6.4 أو 7.4. يتطلب Symfony 8.1 إصدار PHP 8.4 أو أحدث. يلزم Composer وامتداد OpenSSL + Tokenizer. ثبّت WebProfilerBundle لاستخدام أداة التحليل.

## 1. التثبيت

نفّذ الأوامر من المجلد الرئيسي لتطبيق Symfony الذي يحتوي على composer.json وbin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. الإعداد

```bash
php vendor/bin/guardian setup
```

يفعّل المساعد الحزمة في dev/test، ويجهّز الخزنة وحساب المسؤول، ويمسح الذاكرة المؤقتة ويتحقق من الإعدادات. لا تنتقل إلى الخطوة التالية إلا بعد نجاح السابقة. أبقِ التطبيق خاصًا أثناء الإعداد. إعادة التشغيل تحافظ على الحسابات والمفاتيح الموجودة.

## 3. فتح اللوحة

```bash
php bin/console guardian:access --env=dev
```

يعرض الأمر المسار الخاص باللوحة. استبدل https://VOTRE-HOTE بعنوان HTTPS الحقيقي للتطبيق مع المنفذ. اقرأ بيانات الدخول من .guardian-shield/initial-admin-credentials.txt أو الخزنة المحددة. يلزم تغيير كلمة المرور عند أول دخول. لا تشارك هذا الملف.

## 4. إلغاء التثبيت

أوقف العمليات العاملة ثم نفّذ الأوامر بالترتيب مع التحقق من نجاح كل خطوة. تُحفظ الخزنة ومفتاحها. أعد تشغيل العمليات وافحص أداة التحليل في بيئة خاصة.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

لحذف الحسابات والإعدادات أيضًا، استبدل الأمر الأول بالأمر التالي وأكّد الحذف. الرفض لا يغيّر شيئًا. راجع الخزن الخارجية والملفات المخصصة يدويًا واحتفظ بمفاتيح الخزن التي تريد إبقاءها.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. مرجع الأوامر

تشرح المساعدة الأوامر. يعثر أمر الوصول على اللوحة ويتحقق التشخيص من الإعدادات. تتطلب هذه العمليات وصولًا محليًا موثوقًا.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[مرجع الأوامر](commands.md) · [فهرس الوثائق](README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

إذا كانت المؤسسة تستخدم Private Packagist، فاضبط هذا المستودع قبل التثبيت واحفظ بيانات اعتماد Composer وفق تعليماتها. لا تضف رموز الوصول إلى Git مطلقًا.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
