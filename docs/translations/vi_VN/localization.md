# Bản dịch

[Mục lục tài liệu](README.md)

Cổng cung cấp 28 biến thể khu vực và 25 danh mục; bốn biến thể tiếng Anh dùng chung danh mục. Văn bản tĩnh được dịch rồi thoát ký tự an toàn. Tên người dùng, thông báo tùy chỉnh và dữ liệu người dùng không được dịch. Không gửi văn bản đến dịch vụ từ xa. Kiểm toán kiểm tra khóa, tham số, UTF-8, lệnh và liên kết; kiểm thử HTTP duyệt các trang của từng biến thể.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
