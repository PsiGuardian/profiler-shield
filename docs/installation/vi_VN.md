# ΩGuardian Profiler Shield — Cài đặt và sử dụng

PHP 8.3 trở lên; Symfony 5.4, 6.4 hoặc 7.4. Symfony 8.1 yêu cầu PHP 8.4 trở lên. Cần Composer và phần mở rộng OpenSSL + Tokenizer. Cài WebProfilerBundle để dùng trình phân tích.

## 1. Cài đặt

Chạy từ thư mục gốc ứng dụng Symfony có chứa composer.json và bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Cấu hình

```bash
php vendor/bin/guardian setup
```

Trợ lý kích hoạt bundle trong dev/test, chuẩn bị kho bảo mật và tài khoản quản trị, xóa bộ nhớ đệm và kiểm tra cấu hình. Chỉ tiếp tục khi từng bước thành công. Giữ ứng dụng ở chế độ riêng tư trong quá trình này. Chạy lại sẽ giữ nguyên tài khoản và khóa hiện có.

## 3. Mở bảng điều khiển

```bash
php bin/console guardian:access --env=dev
```

Lệnh hiển thị đường dẫn riêng tư. Thay https://VOTRE-HOTE bằng địa chỉ gốc HTTPS thực tế, gồm cả cổng. Đọc thông tin đăng nhập trong .guardian-shield/initial-admin-credentials.txt hoặc kho được chỉ định. Lần đăng nhập đầu yêu cầu đổi mật khẩu. Không chia sẻ tệp này.

## 4. Gỡ cài đặt

Dừng các tiến trình worker, sau đó chạy các lệnh theo thứ tự và kiểm tra kết quả từng bước. Kho và khóa được giữ lại. Khởi động lại worker và kiểm tra trình phân tích trong môi trường riêng tư.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Để xóa cả tài khoản và thiết lập, thay lệnh đầu bằng lệnh sau và xác nhận. Từ chối sẽ không thay đổi gì. Kiểm tra thủ công kho bên ngoài và tệp tùy chỉnh; giữ khóa của các kho cần giữ lại.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Tham chiếu lệnh

Trợ giúp mô tả các lệnh. Lệnh truy cập tìm bảng điều khiển; chẩn đoán kiểm tra cấu hình. Cần quyền truy cập cục bộ đáng tin cậy.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Tham chiếu lệnh](../translations/vi_VN/commands.md) · [Mục lục tài liệu](../translations/vi_VN/README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

Nếu tổ chức dùng Private Packagist, cấu hình kho này trước khi cài đặt và lưu thông tin Composer theo hướng dẫn của tổ chức. Không đưa mã truy cập vào Git.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
