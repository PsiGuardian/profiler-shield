# مرجع الأوامر

[فهرس الوثائق](README.md)

نفّذ من جذر Symfony في طرفية موثوقة. تستخدم الأمثلة dev. لا تضع كلمات المرور أو المفاتيح أو رموز الاسترداد في الوسائط أو السجلات أو Git.

## اكتشاف الأوامر والمساعدة


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev و--no-interaction خياران من Symfony. لا يوفر --no-interaction الأسرار ولا يناسب أوامر كلمات المرور التفاعلية. يعرض --help الصيغة ويزيد -v التفاصيل. يدل الرمز 0 على النتيجة المعلنة؛ قد ينجح --no-admin دون بوابة قابلة للاستخدام.

## `vendor/bin/guardian`

يفعّل الحزمة في dev/test، ويمسح الذاكرة المؤقتة، ويشغّل guardian:setup --automatic ثم يمسحها مجددًا ويشغّل guardian:doctor وguardian:access. يتوقف عند أول خطأ. التكرار يحفظ الحسابات والمفاتيح. يرفض APP_ENV=prod ويتطلب proc_open.

دون وسيط، أو باستخدام --help أو -h أو help، تظهر المساعدة دون تغيير. setup هو الإجراء الوحيد ولا يقبل خيارات إضافية.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | مرجع الأوامر |
|---|---|
| [`guardian:setup`](#guardiansetup) | يجهّز المسار الخاص والمفتاح والخزنة وملفات Symfony ومسؤولًا عند الحاجة. |
| [`guardian:install`](#guardianinstall) | يهيئ الخزنة وينشئ مسؤولًا باستخدام --username، وافتراضيه admin. |
| [`guardian:access`](#guardianaccess) | يعرض مسار اللوحة ومكان بيانات الدخول الأولية، ولا يعرض كلمة المرور. |
| [`guardian:doctor`](#guardiandoctor) | يفحص الخزنة والصلاحيات والمفتاح والمسار والمسؤول النشط وتناسق الإعدادات. |
| [`guardian:password:reset`](#guardianpasswordreset) | يغيّر كلمة مرور حساب username الموجود بإدخال مخفي وفق السياسة، ويلغي كل جلساته ويسجل الإجراء. |
| [`guardian:mfa:reset`](#guardianmfareset) | يحذف سر TOTP وعدّاده للحساب username ويلغي الجلسات ويسجل الإجراء. |
| [`guardian:login:unlock`](#guardianloginunlock) | يصفّر عدادات username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | يفرغ قائمة IP ويعطل الجدول وJIT والطوارئ عن بعد. |
| [`guardian:lockdown`](#guardianlockdown) | يغلق on التشخيص ويحذف كل الجلسات؛ تبقى صفحات العمل متاحة. |
| [`guardian:emergency`](#guardianemergency) | ينشئ 5 رموز أحادية الاستخدام لحساب username موجود ويستبدل السابقة. |
| [`guardian:alerts:flush`](#guardianalertsflush) | يرسل التنبيهات المنتظرة إلى alert_email عبر PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | يقيس التحقق من التجزئة بسر اصطناعي وليس كلمة مرور مستخدم. |
| [`guardian:uninstall`](#guardianuninstall) | يجهز إزالة الملفات المولدة المثبت انتماؤها إلى Guardian. |

## `guardian:setup`

يجهّز المسار الخاص والمفتاح والخزنة وملفات Symfony ومسؤولًا عند الحاجة. يحفظ الحسابات الموجودة. يرفض بيئة الإنتاج. بعد التشغيل المباشر امسح الذاكرة المؤقتة ونفّذ الفحوص.


```bash
php bin/console guardian:setup --automatic --env=dev
```

ينشئ --automatic حساب admin وسرًا في ملف خاص 0600. يختار --path مسارًا من 25 إلى 181 محرفًا دون استبدال مسار موجود. يتجاوز --no-admin إنشاء المسؤول؛ دون حساب نشط لا تعمل البوابة. يقترح --username الاسم التفاعلي، وافتراضيه admin؛ لا يغيّر الحساب التلقائي.

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

يهيئ الخزنة وينشئ مسؤولًا باستخدام --username، وافتراضيه admin. يطلب كلمة مرور مخفية وفق السياسة النشطة. يرفض الحساب الموجود. لا ينشئ المسارات أو إعدادات Symfony؛ استخدم عادةً المساعد الكامل.


```bash
php bin/console guardian:install --username=admin --env=dev
```


اسم المستخدم : 3 إلى 64 الخصائص الألبانيّة،

## `guardian:access`

يعرض مسار اللوحة ومكان بيانات الدخول الأولية، ولا يعرض كلمة المرور. يقبل --base-url أصل HTTPS الحقيقي مع منفذ اختياري، دون بيانات اعتماد أو استعلام أو جزء مرجعي. استبدل المضيف النموذجي.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

يفحص الخزنة والصلاحيات والمفتاح والمسار والمسؤول النشط وتناسق الإعدادات. رمز الخروج 1 عند وجود مشكلة. قد تختلف المنطقة الزمنية بين PHP CLI وPHP-FPM. لا يختبر الانكشاف للإنترنت أو قواعد الوكيل.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

يغيّر كلمة مرور حساب username الموجود بإدخال مخفي وفق السياسة، ويلغي كل جلساته ويسجل الإجراء. لا تمرر كلمة المرور كوسيط.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

يحذف سر TOTP وعدّاده للحساب username ويلغي الجلسات ويسجل الإجراء. سجّل تطبيق مصادقة جديدًا؛ يبقى شرط MFA العام وقد يمنع أداة التحليل.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

يصفّر عدادات username. يمسح --ip العنوان عداد مصدر IPv4/IPv6 أيضًا؛ وإلا قد يبقى حظر الشبكة. يمسح --all جميع العدادات. لا تتغير الحسابات أو كلمات المرور أو الجلسات؛ ويُسجّل الإجراء.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

يفرغ قائمة IP ويعطل الجدول وJIT والطوارئ عن بعد. تبقى المصادقة وMFA والقفل العام وحظر الدخول. أعد ضبط القيود المطلوبة في اللوحة بعد الاسترداد.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

يغلق on التشخيص ويحذف كل الجلسات؛ تبقى صفحات العمل متاحة. يرفع off القفل العام فقط: يلزم الدخول مجددًا وتظل السياسات الأخرى سارية.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

ينشئ 5 رموز أحادية الاستخدام لحساب username موجود ويستبدل السابقة. تظهر مرة واحدة؛ احفظها خارج الخادم في مدير أسرار. يتطلب الاستخدام البعيد تفعيل الطوارئ وجلسة مصادقًا عليها وكلمة مرور وسببًا. لا يتجاوز القفل العام.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

يرسل التنبيهات المنتظرة إلى alert_email عبر PHP mail(). اضبط النقل ومهمة مجدولة وتحقق من التسليم الفعلي. تبقى التنبيهات الفاشلة في الطابور. رمز 1 للمستلم غير الصحيح أو فشل الإرسال.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

يقيس التحقق من التجزئة بسر اصطناعي وليس كلمة مرور مستخدم. يقبل --iterations من 1 إلى 10، وافتراضيه 3. ينتج JSON بالخوارزمية والزمن وعمليات التحقق في الثانية. يقيس عملية CPU محلية واحدة، لا GPU أو مهاجمًا.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

يجهز إزالة الملفات المولدة المثبت انتماؤها إلى Guardian. يحفظ الحسابات والإعدادات والسجلات والمفتاح افتراضيًا. أوقف العمال ونفّذ قبل composer remove ثم امسح ذاكرة dev وأعد التشغيل. راجع الملفات المخصصة أو مجهولة المصدر.


```bash
php bin/console guardian:uninstall --env=dev
```

يطلب --purge حذف البيانات مع رفض افتراضي للتأكيد. يتطلب --force الخيار --purge ويؤكد الإتلاف؛ ويلزم مع --no-interaction. قد تمنع الخزن الخارجية والروابط والملفات المجهولة الحذف الكامل. احتفظ بالمفاتيح اللازمة وافحص النتيجة.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## إلغاء التثبيت والبيانات


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


انسخ الخزنة والمفتاح معًا واختبر الاستعادة. لا تُعاد توليد المفاتيح المفقودة تلقائيًا. بعد الإزالة افحص أداة التحليل في بيئة خاصة فقط. الإنتاج: APP_DEBUG=0 مع تعطيل WebProfilerBundle وجمع البيانات. سلسلة التدقيق المحلية لا تثبت سلامة مضيف مخترق.

[إلغاء التثبيت والبيانات](desinstallation.md)
