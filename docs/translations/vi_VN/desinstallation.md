# Khóa và dữ liệu

[Mục lục tài liệu](README.md)

Chuẩn bị xóa tệp được chứng minh do Guardian tạo. Giữ tài khoản, thiết lập, nhật ký và khóa. Dừng worker, chạy trước composer remove, xóa cache dev rồi khởi động lại. Kiểm tra tệp tùy chỉnh hoặc chưa rõ nguồn.

--purge yêu cầu xóa dữ liệu với xác nhận mặc định từ chối. --force cần --purge và xác nhận hủy; bắt buộc với --no-interaction. Kho ngoài, liên kết và tệp lạ có thể ngăn xóa toàn bộ. Giữ khóa cần thiết và kiểm tra kết quả.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Sao lưu kho cùng khóa và thử phục hồi. Không tự tạo lại khóa bị mất. Sau khi gỡ, chỉ kiểm tra trình phân tích trong môi trường riêng. Sản xuất: APP_DEBUG=0, tắt WebProfilerBundle và thu thập. Chuỗi kiểm toán cục bộ không chứng minh toàn vẹn trên máy chủ bị xâm nhập.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Tham chiếu lệnh

--purge yêu cầu xóa dữ liệu với xác nhận mặc định từ chối. --force cần --purge và xác nhận hủy; bắt buộc với --no-interaction. Kho ngoài, liên kết và tệp lạ có thể ngăn xóa toàn bộ. Giữ khóa cần thiết và kiểm tra kết quả.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Tham chiếu lệnh](commands.md)


Nếu Composer đã gỡ gói, cài lại tạm cùng phiên bản mà không chạy setup. Chạy guardian:uninstall, gỡ lại và xóa cache. Kiểm tra cấu hình tùy chỉnh thủ công.
