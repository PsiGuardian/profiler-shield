# ΩGuardian Profiler Shield — स्थापना और उपयोग

PHP 8.3 या नया; Symfony 5.4, 6.4 या 7.4। Symfony 8.1 के लिए PHP 8.4 या नया चाहिए। Composer और OpenSSL + Tokenizer एक्सटेंशन आवश्यक हैं। प्रोफाइलर के लिए WebProfilerBundle स्थापित करें।

## 1. स्थापित करें

Symfony अनुप्रयोग की उस मूल निर्देशिका से चलाएँ जिसमें composer.json और bin/console हैं।

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. कॉन्फ़िगर करें

```bash
php vendor/bin/guardian setup
```

सहायक dev/test में बंडल सक्रिय करता है, वॉल्ट और व्यवस्थापक खाता तैयार करता है, कैश साफ़ करता है और कॉन्फ़िगरेशन जाँचता है। हर चरण सफल होने पर ही आगे बढ़ें। इस दौरान अनुप्रयोग निजी रखें। दोबारा चलाने पर मौजूदा खाते और कुंजियाँ सुरक्षित रहती हैं।

## 3. पैनल खोलें

```bash
php bin/console guardian:access --env=dev
```

कमांड पैनल का निजी पथ दिखाता है। https://VOTRE-HOTE को पोर्ट सहित अनुप्रयोग के वास्तविक HTTPS पते से बदलें। प्रवेश विवरण .guardian-shield/initial-admin-credentials.txt या बताए गए वॉल्ट में पढ़ें। पहले प्रवेश पर पासवर्ड बदलना अनिवार्य है। यह फ़ाइल साझा न करें।

## 4. स्थापना हटाएँ

वर्कर रोकें और हर चरण की सफलता जाँचते हुए कमांड क्रम से चलाएँ। वॉल्ट और कुंजी सुरक्षित रहते हैं। वर्कर पुनः चालू करें और निजी वातावरण में प्रोफाइलर जाँचें।

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

खाते और सेटिंग भी मिटाने के लिए पहली कमांड को नीचे दी गई कमांड से बदलकर पुष्टि करें। मना करने पर कोई बदलाव नहीं होता। बाहरी वॉल्ट और अनुकूलित फ़ाइलें स्वयं जाँचें; रखे जाने वाले वॉल्ट की कुंजियाँ बचाएँ।

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. कमांड संदर्भ

सहायता कमांड समझाती है। प्रवेश कमांड पैनल का पता बताता है; निदान कॉन्फ़िगरेशन जाँचता है। विश्वसनीय स्थानीय पहुँच आवश्यक है।

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[कमांड संदर्भ](commands.md) · [दस्तावेज़ सूची](README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

यदि संगठन Private Packagist उपयोग करता है, तो स्थापना से पहले यह रिपॉज़िटरी कॉन्फ़िगर करें और उसके निर्देशों के अनुसार Composer प्रवेश विवरण सहेजें। टोकन कभी Git में न जोड़ें।

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
