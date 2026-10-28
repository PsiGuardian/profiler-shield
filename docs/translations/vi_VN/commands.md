# Tham chiếu lệnh

[Mục lục tài liệu](README.md)

Chạy từ thư mục gốc Symfony trong thiết bị đầu cuối đáng tin cậy. Ví dụ dùng dev. Không đặt mật khẩu, khóa hoặc mã khôi phục trong đối số, nhật ký hay Git.

## Tìm lệnh và trợ giúp


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev và --no-interaction là tùy chọn Symfony. --no-interaction không cung cấp bí mật, không dùng cho lệnh mật khẩu tương tác. --help chỉ cú pháp; -v thêm chi tiết. Mã 0 xác nhận kết quả được báo; --no-admin có thể thành công khi cổng chưa dùng được.

## `vendor/bin/guardian`

Kích hoạt bundle trong dev/test, xóa cache, chạy guardian:setup --automatic, xóa lại rồi chạy guardian:doctor và guardian:access. Dừng ở lỗi đầu tiên. Chạy lại giữ tài khoản và khóa. Từ chối APP_ENV=prod và cần proc_open.

Không có đối số, --help, -h hoặc help chỉ hiển thị trợ giúp. setup là hành động duy nhất, không nhận tùy chọn bổ sung.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Tham chiếu lệnh |
|---|---|
| [`guardian:setup`](#guardiansetup) | Chuẩn bị đường dẫn riêng tư, khóa, kho, tệp Symfony và quản trị viên nếu thiếu. |
| [`guardian:install`](#guardianinstall) | Khởi tạo kho và tạo quản trị viên với --username, mặc định admin. |
| [`guardian:access`](#guardianaccess) | Hiển thị đường dẫn bảng điều khiển và nơi lưu thông tin ban đầu, không hiện mật khẩu. |
| [`guardian:doctor`](#guardiandoctor) | Kiểm tra kho, quyền, khóa, tuyến, quản trị viên hoạt động và cấu hình. |
| [`guardian:password:reset`](#guardianpasswordreset) | Đổi mật khẩu tài khoản username hiện có bằng nhập ẩn theo chính sách, thu hồi toàn bộ phiên và ghi nhật ký. |
| [`guardian:mfa:reset`](#guardianmfareset) | Xóa bí mật và bộ đếm TOTP của username, thu hồi phiên và ghi nhật ký. |
| [`guardian:login:unlock`](#guardianloginunlock) | Đặt lại bộ đếm username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Xóa danh sách IP, tắt lịch, JIT và truy cập khẩn cấp từ xa. |
| [`guardian:lockdown`](#guardianlockdown) | on đóng chẩn đoán và xóa toàn bộ phiên; trang nghiệp vụ vẫn chạy. |
| [`guardian:emergency`](#guardianemergency) | Tạo 5 mã dùng một lần cho username hiện có, thay mã cũ. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Gửi cảnh báo chờ đến alert_email bằng PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Đo xác minh băm với bí mật tổng hợp, không dùng mật khẩu người dùng. |
| [`guardian:uninstall`](#guardianuninstall) | Chuẩn bị xóa tệp được chứng minh do Guardian tạo. |

## `guardian:setup`

Chuẩn bị đường dẫn riêng tư, khóa, kho, tệp Symfony và quản trị viên nếu thiếu. Giữ tài khoản hiện có. Từ chối môi trường sản xuất. Sau khi chạy trực tiếp, xóa cache và chạy kiểm tra.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic tạo admin và bí mật trong tệp riêng 0600. --path chọn đường dẫn 25–181 ký tự, không thay đường dẫn hiện có. --no-admin bỏ tạo quản trị viên; không có tài khoản hoạt động thì cổng không dùng được. --username gợi ý tên tương tác, mặc định admin; không đổi tên tài khoản tự động.

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

Khởi tạo kho và tạo quản trị viên với --username, mặc định admin. Yêu cầu nhập mật khẩu ẩn theo chính sách. Từ chối tài khoản đã có. Không tạo tuyến hay cấu hình Symfony; thường dùng trợ lý đầy đủ.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Tên người dùng: 3 để 64 Các nhân vật alphanumeric,

## `guardian:access`

Hiển thị đường dẫn bảng điều khiển và nơi lưu thông tin ban đầu, không hiện mật khẩu. --base-url nhận nguồn HTTPS thật với cổng tùy chọn, không có thông tin đăng nhập, truy vấn hay đoạn định danh. Thay máy chủ ví dụ.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Kiểm tra kho, quyền, khóa, tuyến, quản trị viên hoạt động và cấu hình. Mã thoát 1 nếu có vấn đề. Múi giờ PHP CLI và PHP-FPM có thể khác nhau. Không kiểm tra khả năng truy cập từ Internet hay quy tắc proxy.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Đổi mật khẩu tài khoản username hiện có bằng nhập ẩn theo chính sách, thu hồi toàn bộ phiên và ghi nhật ký. Không truyền mật khẩu bằng đối số.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Xóa bí mật và bộ đếm TOTP của username, thu hồi phiên và ghi nhật ký. Đăng ký trình xác thực mới; yêu cầu MFA toàn cục vẫn còn và có thể chặn trình phân tích.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Đặt lại bộ đếm username. --ip ĐỊA_CHỈ xóa cả bộ đếm nguồn IPv4/IPv6 đó; nếu không, chặn mạng có thể còn. --all xóa tất cả bộ đếm. Tài khoản, mật khẩu và phiên không đổi; hành động được ghi lại.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Xóa danh sách IP, tắt lịch, JIT và truy cập khẩn cấp từ xa. Xác thực, MFA, khóa toàn cục và chặn đăng nhập vẫn giữ. Cấu hình lại hạn chế cần thiết trong bảng điều khiển.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on đóng chẩn đoán và xóa toàn bộ phiên; trang nghiệp vụ vẫn chạy. off chỉ bỏ khóa toàn cục: phải đăng nhập lại và các chính sách khác vẫn áp dụng.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Tạo 5 mã dùng một lần cho username hiện có, thay mã cũ. Chỉ hiển thị một lần; lưu ngoài máy chủ trong trình quản lý bí mật. Dùng từ xa cần bật khẩn cấp, phiên xác thực, mật khẩu và lý do. Không vượt khóa toàn cục.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Gửi cảnh báo chờ đến alert_email bằng PHP mail(). Cấu hình vận chuyển và tác vụ định kỳ, kiểm tra giao nhận thật. Cảnh báo lỗi vẫn trong hàng đợi. Mã 1 khi người nhận không hợp lệ hoặc gửi thất bại.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Đo xác minh băm với bí mật tổng hợp, không dùng mật khẩu người dùng. --iterations nhận 1–10, mặc định 3. Xuất JSON với thuật toán, thời gian và số xác minh mỗi giây. Chỉ đo một tiến trình CPU cục bộ, không phải GPU hay kẻ tấn công.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Chuẩn bị xóa tệp được chứng minh do Guardian tạo. Giữ tài khoản, thiết lập, nhật ký và khóa. Dừng worker, chạy trước composer remove, xóa cache dev rồi khởi động lại. Kiểm tra tệp tùy chỉnh hoặc chưa rõ nguồn.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge yêu cầu xóa dữ liệu với xác nhận mặc định từ chối. --force cần --purge và xác nhận hủy; bắt buộc với --no-interaction. Kho ngoài, liên kết và tệp lạ có thể ngăn xóa toàn bộ. Giữ khóa cần thiết và kiểm tra kết quả.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Gỡ cài đặt và dữ liệu


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Sao lưu kho cùng khóa và thử phục hồi. Không tự tạo lại khóa bị mất. Sau khi gỡ, chỉ kiểm tra trình phân tích trong môi trường riêng. Sản xuất: APP_DEBUG=0, tắt WebProfilerBundle và thu thập. Chuỗi kiểm toán cục bộ không chứng minh toàn vẹn trên máy chủ bị xâm nhập.

[Khóa và dữ liệu](desinstallation.md)
