# Xác thực hai yếu tố

[Mục lục tài liệu](README.md)


## Xác thực hai yếu tố

Bảo vệ tài khoản Guardian bằng mã tạm thời, không cần dịch vụ QR bên ngoài.

Mỗi người đăng ký bí mật TOTP riêng và xác nhận mã. Bắt buộc MFA sẽ chặn tài khoản chưa đăng ký truy cập trình phân tích. Mã QR chỉ ở trong cổng. Chuẩn bị khôi phục quản trị trước khi áp dụng cho cả nhóm.

## 1. Xác nhận danh tính

Nhập mật khẩu bạn dùng đăng nhập Guardian. Chỉ cần nhập một lần cho thiết lập này.

## 2. Quét QR bằng ứng dụng xác thực

Guardian tạo QR ngay trên máy chủ, không gửi yêu cầu đến dịch vụ bên ngoài.

## 3. Xác nhận mã gồm sáu chữ số

Nhập mã hiển thị trong ứng dụng. Không cần mật khẩu lần hai.

## Quyền riêng tư: không nhập URL website

Nếu ứng dụng hỏi tên hoặc website, chỉ nhập “ΩGuardian” hoặc tên trung tính. Không nhập URL phiên bản Symfony của bạn. Không dùng công cụ tạo QR trực tuyến vì dịch vụ đó sẽ nhận được bí mật của bạn.

Giữ bí mật: khóa có thể tạo mã TOTP. Không chia sẻ hoặc chụp ảnh màn hình.

Bí mật tạm hết hạn sau 10 phút hoặc 5 mã sai; khi đó bắt đầu lại. Mã sai giữ biểu mẫu mà không hỏi lại mật khẩu. Bộ đếm xác nhận bị tiêu thụ để chống phát lại. QR SVG dùng bacon/bacon-qr-code và ext-iconv, không cần GD, XMLWriter hay mạng. Vẫn có nhập tay. URI otpauth dùng nhà cung cấp trung tính và tài khoản, không có tên miền trang. Không chia sẻ bí mật hoặc URL riêng.

## Khôi phục và xác minh

Xóa bí mật và bộ đếm TOTP của username, thu hồi phiên và ghi nhật ký. Đăng ký trình xác thực mới; yêu cầu MFA toàn cục vẫn còn và có thể chặn trình phân tích.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
