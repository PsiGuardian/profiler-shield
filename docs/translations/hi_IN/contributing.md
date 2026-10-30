# योगदान

[दस्तावेज़ सूची](README.md)

अलग Symfony परीक्षण अनुप्रयोग में काम करें। व्यावसायिक उपलब्धता और अनधिकृत निदान अस्वीकृति बनाए रखें। संबंधित संस्करणों पर अनाम अनुरोध, रद्द सत्र, समय-सारणी और भंडारण विफलताएँ जाँचें। स्थिर कुंजियाँ, सुरक्षित एस्केप और पूरे अनुवाद रखें। परीक्षण, चित्र या Git में गुप्त मान न हों; अनिवार्य नेटवर्क निर्भरता न हो। प्रभाव और सत्यापन बताएँ।
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[सुरक्षा नीति](security.md) · [इंटरफ़ेस उदाहरण](examples.md)
