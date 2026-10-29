# Xác thực hai yếu tố

[Mục lục tài liệu](README.md)


## Xác thực hai yếu tố

Mỗi người đăng ký bí mật TOTP riêng và xác nhận mã.

Mỗi người đăng ký bí mật TOTP riêng và xác nhận mã. Bắt buộc MFA sẽ chặn tài khoản chưa đăng ký truy cập trình phân tích. Chuẩn bị khôi phục quản trị trước khi áp dụng cho cả nhóm. Chọn thiết lập thủ công và nhập khóa đang hiển thị. Dùng SHA-1, sáu chữ số và chu kỳ 30 giây.

## 1. Xác nhận danh tính

Nhập mật khẩu bạn dùng đăng nhập Guardian. Chỉ cần nhập một lần cho thiết lập này.

## 2. Nhập khóa vào ứng dụng xác thực

Chọn thiết lập thủ công và nhập khóa đang hiển thị. Dùng SHA-1, sáu chữ số và chu kỳ 30 giây.

## 3. Xác nhận mã gồm sáu chữ số

Nhập mã hiển thị trong ứng dụng. Không cần mật khẩu lần hai.

## Quyền riêng tư: không nhập URL website

Nếu ứng dụng hỏi tên hoặc website, chỉ nhập “ΩGuardian” hoặc tên trung tính. Không nhập URL phiên bản Symfony của bạn.

Giữ bí mật: khóa có thể tạo mã TOTP. Không chia sẻ hoặc chụp ảnh màn hình.

Bí mật tạm hết hạn sau 10 phút hoặc 5 mã sai; khi đó bắt đầu lại. Mã sai giữ biểu mẫu mà không hỏi lại mật khẩu. Bộ đếm xác nhận bị tiêu thụ để chống phát lại.

## Khôi phục và xác minh

Xóa bí mật và bộ đếm TOTP của username, thu hồi phiên và ghi nhật ký. Đăng ký trình xác thực mới; yêu cầu MFA toàn cục vẫn còn và có thể chặn trình phân tích.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
