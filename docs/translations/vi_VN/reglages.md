# Cài đặt

[Mục lục tài liệu](README.md)


## Bảo vệ bổ sung

Các cơ chế bảo vệ được nhóm với thiết lập liên quan. Tùy chọn tắt sẽ mờ đi nhưng giữ nguyên giá trị.

Guardian kiểm soát hồ sơ và thanh công cụ debug. Một tài khoản quản trị viên hoặc nhà phát triển được ủy quyền là cần thiết. MFACác chương trình, JIT, giới hạn mạng và khóadown vẫn có thể từ chối truy cập. chế độ ẩn giấu điểm nhập; nó không thay thế các kiểm tra này.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Hồ sơ cấu hình | Cài đặt |
|---|---|
| Độc lập · linh hoạt | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| Nhóm · Tăng cường | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| nhạy cảm · hạn chế | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Tham số | Cài đặt | Giá trị ban đầu |
|---|---|---|
| `stealth` | Trả về 404 thay vì 403 đối với tuyến chẩn đoán không được phép. | `true` |
| `mfa` | Mọi người dùng phải đăng ký TOTP trước khi bật yêu cầu bắt buộc. | `false` |
| `idle_minutes` | Số phút không hoạt động trước khi phiên hết hạn. | `60` |
| `absolute_hours` | Tính bằng giờ, bất kể hoạt động. | `8` |
| `remember_days` | Ngày. Không thay thế đăng nhập hoặc chính sách giờ làm việc. | `14` |
| `schedule_enabled` | Chọn thời điểm các công cụ chẩn đoán được truy cập. Trang ứng dụng vẫn hoạt động. | `false` |
| `weekdays` | Chọn những ngày cho phép chẩn đoán. | `1,2,3,4,5` |
| `start` | Bắt đầu | `09:00` |
| `end` | Kết thúc | `18:00` |
| `timezone` | Phát hiện từ tiến trình PHP phục vụ trang này. PHP CLI, PHP-FPM và hệ điều hành có thể dùng các múi giờ khác nhau. Kiểm tra cấu hình trước khi lưu. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | URL riêng trả về 404 ngoài giờ làm việc. Chỉ có thể khôi phục qua CLI cục bộ. | `false` |
| `jit_enabled` | Chỉ mở profiler trong thời gian chẩn đoán đã yêu cầu. | `false` |
| `jit_minutes` | Tính bằng phút, chỉ sử dụng khi bật. | `30` |
| `ip_allowlist` | Cảnh báo: cấu hình sai có thể khóa truy cập. Khôi phục cục bộ: guardian:policy:reset. | `∅` |
| `audit_level` | Chọn mức chi tiết kiểm toán và thời gian lưu trữ tại máy. | `standard` |
| `audit_retention_days` | Số ngày trước khi dọn nhật ký. | `30` |
| `alert_email` | Nhận cảnh báo khẩn cấp. Quyền truy cập không được phụ thuộc vào việc gửi email. | `∅` |
| `emergency_enabled` | Cho phép ngoại lệ 30 phút với mã dùng một lần và ghi nhật ký kiểm toán. | `false` |
| `login_rules` | Mặc định : 10 thất bại, sau đó A 10-minute lockout. counters áp dụng cho cả tài khoản và nguồn gốc mạng, bao gồm xác nhận mật khẩu. | `10 / 10 / 30 / 30` |
| `password_policy` | Các quy tắc này áp dụng cho việc tạo và thay đổi mật khẩu, trên web và trong CLIHọ không tiết lộ hoặc kiểm tra mật khẩu hiện có. | `min_length=15` |
| `error_mode` | Trang ứng dụng: duy trì trang web hiện có 404 Câu trả lời; ngoại lệ sử dụng 404 template khi có sẵn, nếu không một trang trung lập. trung lập: thông điệp tiêu chuẩn. tùy chỉnh: sử dụng văn bản của bạn. | `native` |
| `error_reference` | Mục tiêu này áp dụng cho GuardianCác trang trung lập: ngẫu nhiên tạo ra một mã mới, không phải là một thông điệp ngẫu thuật. | `none` |
| `error_reference_text` | Chỉ có sẵn với một tham chiếu tĩnh tùy chỉnh. | `∅` |
| `error_custom_text` | Chỉ dùng cho thông báo trung tính tùy chỉnh. Văn bản được giữ lại khi đổi chế độ. | `∅ → custom` |

Khả năng lập kế hoạch, chọn ngày, thời gian bắt đầu tích hợp và thời gian kết thúc độc quyền. Khu vực thời gian được tiết kiệm xác định thời gian địa phương. Khoảng cách ban đêm không được chấp nhận. ẩn cổng ngoài giờ ngăn chặn truy cập khẩn cấp từ xa; giữ các lệnh phục hồi địa phương có sẵn.

Idle timeout, tuổi thọ tuyệt đối và bộ nhớ thiết bị phục vụ các mục đích khác nhau. ghi nhớ một thiết bị không vượt quá lịch hoặc JITPhản hồi kết thúc các phiên liên quan trên máy chủ. Sử dụng tài khoản cá nhân để phân bổ hành động trong tạp chí.

JIT yêu cầu kích hoạt một cửa sổ tạm thời từ phiên được ủy quyền. truy cập khẩn cấp, khi bật, yêu cầu mật khẩu, mã dùng duy nhất và lý do. Nó không vượt qua việc khóa toàn cầu. lệnh địa phương vẫn cần thiết nếu cổng không thể tiếp cận.

IP allowlist chấp nhận IPv4, IPv6 và CIDR Mạng: Kiểm tra Symfony cấu hình proxy đáng tin cậy trước khi kích hoạt nó. HTTPS Đối với các cookie an toàn Guardian không sửa chữa các máy chủ web hoặc cơ sở dữ liệu.

Mức độ và thời hạn lưu quyết định các sự kiện hiện có, tối đa 5000 mục. Lọc theo loại và tài khoản trước khi đổi trang. Cảnh báo được xếp hàng; cấu hình phương thức vận chuyển và gửi. Chuỗi băm cục bộ không bảo đảm tính toàn vẹn trên máy chủ bị xâm nhập.

Thiết lập chiều dài tối thiểu và các thể loại cần thiết trong Kết nối và mật khẩu. dấu hiệu tạo tài khoản mô tả chính sách được lưu trữ. Thay đổi áp dụng cho mật khẩu mới; chọn việc gia hạn bắt buộc cho các tài khoản hiện có. Guardian Lựa chọn Argon2id Khi PHP cung cấp nó, nếu không bcrypt.

Xác định tất cả các sự kiện được lưu trữ theo kiểu và tài khoản. Các sự kiện mới nhất xuất hiện đầu tiên, trong các trang của 50Scrolling không tải trang tiếp theo.

## Môi trường


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


Proxy phụ trách TLS, chuẩn hóa và chống lưu lượng lớn. Cổng kiểm tra chẩn đoán trước bộ định tuyến; tuyến nghiệp vụ không đọc tài khoản hay phiên. Danh tính Guardian tách khỏi Symfony. Sao lưu kho và khóa cùng nhau ngoài thư mục công khai. PHP hoặc máy chủ bị xâm nhập có thể lộ cả hai. Lưu trữ dùng cho một máy chủ. Lỗi khởi động, cache hoặc proxy có thể ảnh hưởng toàn ứng dụng.

Sao lưu kho cùng khóa và thử phục hồi. Không tự tạo lại khóa bị mất. Sau khi gỡ, chỉ kiểm tra trình phân tích trong môi trường riêng. Sản xuất: APP_DEBUG=0, tắt WebProfilerBundle và thu thập. Chuỗi kiểm toán cục bộ không chứng minh toàn vẹn trên máy chủ bị xâm nhập.

## Khôi phục và xác minh

Xóa danh sách IP, tắt lịch, JIT và truy cập khẩn cấp từ xa. Xác thực, MFA, khóa toàn cục và chặn đăng nhập vẫn giữ. Cấu hình lại hạn chế cần thiết trong bảng điều khiển.

on đóng chẩn đoán và xóa toàn bộ phiên; trang nghiệp vụ vẫn chạy. off chỉ bỏ khóa toàn cục: phải đăng nhập lại và các chính sách khác vẫn áp dụng.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
