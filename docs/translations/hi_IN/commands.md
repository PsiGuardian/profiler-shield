# कमांड संदर्भ

[दस्तावेज़ सूची](README.md)

Symfony की मूल निर्देशिका से विश्वसनीय टर्मिनल में चलाएँ। उदाहरण dev उपयोग करते हैं। पासवर्ड, कुंजी या पुनर्प्राप्ति कोड को तर्कों, लॉग या Git में न डालें।

## कमांड और सहायता खोजें


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev और --no-interaction Symfony विकल्प हैं। --no-interaction गुप्त मान नहीं देता; संवादात्मक पासवर्ड कमांड में न लगाएँ। --help सिंटैक्स और -v अधिक विवरण देता है। कोड 0 घोषित परिणाम दर्शाता है; --no-admin उपयोगी पोर्टल बिना भी सफल हो सकता है।

## `vendor/bin/guardian`

dev/test में बंडल सक्रिय करता है, कैश साफ़ करता है, guardian:setup --automatic चलाता है, फिर कैश साफ़ करके guardian:doctor और guardian:access चलाता है। पहली त्रुटि पर रुकता है। दोबारा चलाने पर खाते और कुंजियाँ बचती हैं। APP_ENV=prod अस्वीकार करता है; proc_open चाहिए।

बिना तर्क, --help, -h या help केवल सहायता दिखाते हैं। setup एकमात्र कार्रवाई है और अतिरिक्त विकल्प नहीं लेती।


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | कमांड संदर्भ |
|---|---|
| [`guardian:setup`](#guardiansetup) | निजी पथ, कुंजी, वॉल्ट, Symfony फ़ाइलें और आवश्यकता होने पर व्यवस्थापक तैयार करता है। मौजूदा खाते रखता है। उत्पादन में अस्वीकार करता है। सीधे चलाने के बाद कैश साफ़ करें और जाँच चलाएँ। |
| [`guardian:install`](#guardianinstall) | वॉल्ट प्रारंभ करके --username से व्यवस्थापक बनाता है, डिफ़ॉल्ट admin। सक्रिय नीति के अनुसार छिपा पासवर्ड इनपुट माँगता है। मौजूदा खाता अस्वीकार करता है। रूट या Symfony कॉन्फ़िगरेशन नहीं बनाता; सामान्यतः पूरा सहायक उपयोग करें। |
| [`guardian:access`](#guardianaccess) | पैनल पथ और शुरुआती प्रवेश विवरण का स्थान दिखाता है, पासवर्ड नहीं। --base-url वास्तविक HTTPS मूल और वैकल्पिक पोर्ट स्वीकार करता है, बिना प्रवेश विवरण, क्वेरी या फ़्रैगमेंट। उदाहरण होस्ट बदलें। |
| [`guardian:doctor`](#guardiandoctor) | वॉल्ट, अनुमतियाँ, कुंजी, रूट, सक्रिय व्यवस्थापक और कॉन्फ़िगरेशन जाँचता है। समस्या पर निकास कोड 1। PHP CLI और PHP-FPM के समय क्षेत्र अलग हो सकते हैं। इंटरनेट पर खुलापन या प्रॉक्सी नियम नहीं जाँचता। |
| [`guardian:password:reset`](#guardianpasswordreset) | मौजूदा username का पासवर्ड सक्रिय नीति अनुसार छिपे इनपुट से बदलता है, सभी सत्र रद्द करता है और घटना लिखता है। पासवर्ड तर्क में न दें। |
| [`guardian:mfa:reset`](#guardianmfareset) | username का TOTP गुप्त मान और काउंटर मिटाता है, सत्र रद्द करता है और घटना लिखता है। नया प्रमाणक पंजीकृत करें; वैश्विक MFA आवश्यकता रहती है और प्रोफाइलर रोक सकती है। |
| [`guardian:login:unlock`](#guardianloginunlock) | username के काउंटर रीसेट करता है। --ip पता उस IPv4/IPv6 स्रोत का काउंटर भी मिटाता है; अन्यथा नेटवर्क रोक रह सकती है। --all सभी काउंटर मिटाता है। खाते, पासवर्ड और सत्र नहीं बदलते; कार्रवाई दर्ज होती है। |
| [`guardian:policy:reset`](#guardianpolicyreset) | IP सूची खाली करता है और समय-सारणी, JIT तथा दूरस्थ आपात पहुँच बंद करता है। प्रमाणीकरण, MFA, वैश्विक लॉक और प्रवेश रोक रहते हैं। पुनर्प्राप्ति के बाद पैनल में आवश्यक प्रतिबंध फिर सेट करें। |
| [`guardian:lockdown`](#guardianlockdown) | on निदान बंद करके सभी सत्र मिटाता है; व्यावसायिक पृष्ठ चालू रहते हैं। off केवल वैश्विक लॉक हटाता है: दोबारा प्रवेश चाहिए और अन्य नीतियाँ लागू रहती हैं। |
| [`guardian:emergency`](#guardianemergency) | मौजूदा username के लिए 5 एकबारगी कोड बनाकर पुराने बदलता है। केवल एक बार दिखते हैं; सर्वर से बाहर गुप्त जानकारी प्रबंधक में रखें। दूरस्थ उपयोग हेतु आपात पहुँच चालू, प्रमाणित सत्र, पासवर्ड और कारण चाहिए। वैश्विक लॉक नहीं लाँघता। |
| [`guardian:alerts:flush`](#guardianalertsflush) | PHP mail() से लंबित चेतावनियाँ alert_email को भेजता है। परिवहन और निर्धारित कार्य सेट करके वास्तविक डिलीवरी जाँचें। असफल संदेश कतार में रहते हैं। अमान्य प्राप्तकर्ता या भेजने में विफलता पर कोड 1। |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | उपयोगकर्ता पासवर्ड के बजाय कृत्रिम गुप्त मान से हैश जाँच मापता है। --iterations: 1–10, डिफ़ॉल्ट 3। एल्गोरिदम, समय और प्रति सेकंड जाँच का JSON देता है। एक स्थानीय CPU प्रक्रिया मापता है, GPU या हमलावर नहीं। |
| [`guardian:uninstall`](#guardianuninstall) | Guardian द्वारा बनाई गई प्रमाणित फ़ाइलें हटाने की तैयारी करता है। खाते, सेटिंग, लॉग और कुंजी सुरक्षित रखता है। वर्कर रोकें, composer remove से पहले चलाएँ, dev कैश साफ़ करके पुनः चालू करें। अनुकूलित या अज्ञात स्रोत की फ़ाइलें जाँचें। |

## `guardian:setup`

निजी पथ, कुंजी, वॉल्ट, Symfony फ़ाइलें और आवश्यकता होने पर व्यवस्थापक तैयार करता है। मौजूदा खाते रखता है। उत्पादन में अस्वीकार करता है। सीधे चलाने के बाद कैश साफ़ करें और जाँच चलाएँ।


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic admin और निजी 0600 फ़ाइल में गुप्त मान बनाता है। --path 25–181 अक्षरों का पथ चुनता है, मौजूदा पथ नहीं बदलता। --no-admin व्यवस्थापक बनाना छोड़ता है; सक्रिय खाते बिना पोर्टल काम नहीं करता। --username संवादात्मक नाम सुझाता है, डिफ़ॉल्ट admin; स्वचालित खाते का नाम नहीं बदलता।

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

वॉल्ट प्रारंभ करके --username से व्यवस्थापक बनाता है, डिफ़ॉल्ट admin। सक्रिय नीति के अनुसार छिपा पासवर्ड इनपुट माँगता है। मौजूदा खाता अस्वीकार करता है। रूट या Symfony कॉन्फ़िगरेशन नहीं बनाता; सामान्यतः पूरा सहायक उपयोग करें।


```bash
php bin/console guardian:install --username=admin --env=dev
```


उपयोगकर्ता नाम : 3 के लिए 64 अल्फान्यूमरिक वर्णों के बारे में -

## `guardian:access`

पैनल पथ और शुरुआती प्रवेश विवरण का स्थान दिखाता है, पासवर्ड नहीं। --base-url वास्तविक HTTPS मूल और वैकल्पिक पोर्ट स्वीकार करता है, बिना प्रवेश विवरण, क्वेरी या फ़्रैगमेंट। उदाहरण होस्ट बदलें।


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

वॉल्ट, अनुमतियाँ, कुंजी, रूट, सक्रिय व्यवस्थापक और कॉन्फ़िगरेशन जाँचता है। समस्या पर निकास कोड 1। PHP CLI और PHP-FPM के समय क्षेत्र अलग हो सकते हैं। इंटरनेट पर खुलापन या प्रॉक्सी नियम नहीं जाँचता।


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

मौजूदा username का पासवर्ड सक्रिय नीति अनुसार छिपे इनपुट से बदलता है, सभी सत्र रद्द करता है और घटना लिखता है। पासवर्ड तर्क में न दें।


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

username का TOTP गुप्त मान और काउंटर मिटाता है, सत्र रद्द करता है और घटना लिखता है। नया प्रमाणक पंजीकृत करें; वैश्विक MFA आवश्यकता रहती है और प्रोफाइलर रोक सकती है।


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

username के काउंटर रीसेट करता है। --ip पता उस IPv4/IPv6 स्रोत का काउंटर भी मिटाता है; अन्यथा नेटवर्क रोक रह सकती है। --all सभी काउंटर मिटाता है। खाते, पासवर्ड और सत्र नहीं बदलते; कार्रवाई दर्ज होती है।


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

IP सूची खाली करता है और समय-सारणी, JIT तथा दूरस्थ आपात पहुँच बंद करता है। प्रमाणीकरण, MFA, वैश्विक लॉक और प्रवेश रोक रहते हैं। पुनर्प्राप्ति के बाद पैनल में आवश्यक प्रतिबंध फिर सेट करें।


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on निदान बंद करके सभी सत्र मिटाता है; व्यावसायिक पृष्ठ चालू रहते हैं। off केवल वैश्विक लॉक हटाता है: दोबारा प्रवेश चाहिए और अन्य नीतियाँ लागू रहती हैं।


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

मौजूदा username के लिए 5 एकबारगी कोड बनाकर पुराने बदलता है। केवल एक बार दिखते हैं; सर्वर से बाहर गुप्त जानकारी प्रबंधक में रखें। दूरस्थ उपयोग हेतु आपात पहुँच चालू, प्रमाणित सत्र, पासवर्ड और कारण चाहिए। वैश्विक लॉक नहीं लाँघता।


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

PHP mail() से लंबित चेतावनियाँ alert_email को भेजता है। परिवहन और निर्धारित कार्य सेट करके वास्तविक डिलीवरी जाँचें। असफल संदेश कतार में रहते हैं। अमान्य प्राप्तकर्ता या भेजने में विफलता पर कोड 1।


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

उपयोगकर्ता पासवर्ड के बजाय कृत्रिम गुप्त मान से हैश जाँच मापता है। --iterations: 1–10, डिफ़ॉल्ट 3। एल्गोरिदम, समय और प्रति सेकंड जाँच का JSON देता है। एक स्थानीय CPU प्रक्रिया मापता है, GPU या हमलावर नहीं।


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Guardian द्वारा बनाई गई प्रमाणित फ़ाइलें हटाने की तैयारी करता है। खाते, सेटिंग, लॉग और कुंजी सुरक्षित रखता है। वर्कर रोकें, composer remove से पहले चलाएँ, dev कैश साफ़ करके पुनः चालू करें। अनुकूलित या अज्ञात स्रोत की फ़ाइलें जाँचें।


```bash
php bin/console guardian:uninstall --env=dev
```

--purge डिफ़ॉल्ट अस्वीकृति वाली पुष्टि के साथ डेटा मिटाने को कहता है। --force के लिए --purge चाहिए और यह विनाश की पुष्टि करता है; --no-interaction के साथ अनिवार्य। बाहरी वॉल्ट, लिंक और अज्ञात फ़ाइलें पूर्ण सफ़ाई रोक सकती हैं। ज़रूरी कुंजियाँ बचाकर परिणाम जाँचें।

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## स्थापना हटाना और डेटा


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


वॉल्ट और कुंजी साथ बैकअप करें और पुनर्स्थापना जाँचें। खोई कुंजियाँ अपने आप फिर नहीं बनतीं। हटाने के बाद प्रोफाइलर केवल निजी वातावरण में जाँचें। उत्पादन: APP_DEBUG=0, WebProfilerBundle और संग्रह बंद। स्थानीय ऑडिट शृंखला प्रभावित होस्ट की अखंडता सिद्ध नहीं करती।

[विघटन और डेटा](desinstallation.md)
