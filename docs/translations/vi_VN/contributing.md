# Đóng góp

[Mục lục tài liệu](README.md)

Làm việc trong ứng dụng thử Symfony cách ly. Giữ nghiệp vụ hoạt động và từ chối chẩn đoán trái phép. Kiểm tra yêu cầu ẩn danh, phiên thu hồi, lịch và lỗi lưu trữ trên các phiên bản liên quan. Dùng khóa ổn định, thoát ký tự và dịch đầy đủ. Không có bí mật trong kiểm thử, ảnh hay Git; không bắt buộc mạng. Mô tả ảnh hưởng và xác minh.
```bash
composer install
composer test
php tests/translation_audit.php --strict
php tests/documentation_audit.php
node tests/password_model.js
vendor/bin/phpunit -c phpunit.xml.dist
```

[Chính sách bảo mật](security.md) · [Ví dụ giao diện](examples.md)
