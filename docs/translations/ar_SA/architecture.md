# البنية وحدود الثقة

[فهرس الوثائق](README.md)


## البنية وحدود الثقة

يتولى الوكيل TLS والتطبيع والحماية من أحجام المرور الكبيرة. تفحص البوابة التشخيص قبل التوجيه؛ لا تقرأ مسارات العمل الحسابات أو الجلسات. هوية Guardian منفصلة عن Symfony. انسخ الخزنة الخاصة ومفتاحها معًا خارج المجلد العام. اختراق PHP أو المضيف قد يكشف كليهما. التخزين لمضيف واحد. أخطاء الإقلاع أو الذاكرة المؤقتة أو الوكيل قد تؤثر على التطبيق كله.

IP يسمح القائمة تقبل IPv4, IPv6 و CIDR الشبكات - التحقق Symfony إعداد بروكسي موثوق به قبل تمكينها.استخدام HTTPS الكوكيز الآمنة Guardian لا تقوم بإصلاح خوادم الويب أو قواعد البيانات المعروضة.

Guardian يسيطر على ملفات تعريف و شريط أدوات التفريغ.يتطلب حساب مدير أو مطور مرخص. MFAوالجدول الزمني، JITقد لا تزال القيود على الشبكة والانهيار قد يرفض الوصول.وضع السرقة يخفي نقاط الدخول؛ فإنه لا يحل محل هذه التحققات.

| PHP / HTTP | البنية وحدود الثقة |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| الجلسة | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## ثوابت الأمان

ارفض التشخيص إذا تعذر فحص الهوية أو الدور أو الجلسة أو الشبكة أو الجدول أو JIT أو القفل. المتصفح لا يقرر انتهاء الصلاحية. جمع الملفات وعرضها منفصلان. تذكر الجهاز لا يصادق عليه. الطوارئ تتطلب هوية ورمزًا مستهلكًا وتدقيقًا دائمًا؛ فشل التدقيق الحرج يمنع الفتح. لا تطبق إعدادًا غير صحيح جزئيًا. الضوابط الاختيارية لا تفرض خدمة بعيدة.

انسخ الخزنة والمفتاح معًا واختبر الاستعادة. لا تُعاد توليد المفاتيح المفقودة تلقائيًا. بعد الإزالة افحص أداة التحليل في بيئة خاصة فقط. الإنتاج: APP_DEBUG=0 مع تعطيل WebProfilerBundle وجمع البيانات. سلسلة التدقيق المحلية لا تثبت سلامة مضيف مخترق.

## الصحف والتحذيرات

المستوى والاحتفاظ يحدد الأحداث المتاحة، حتى 5000 إدخالات. تصفية حسب النوع والحساب قبل تغيير الصفحات. يتم تحديد التحذيرات؛ تثبيت نقلها وتسليمها. لا يضمن سلسلة hash المحلية سلامة المضيف المتضرر.
