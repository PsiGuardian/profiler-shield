# Kiến trúc và ranh giới tin cậy

[Mục lục tài liệu](README.md)


## Kiến trúc và ranh giới tin cậy

Proxy phụ trách TLS, chuẩn hóa và chống lưu lượng lớn. Cổng kiểm tra chẩn đoán trước bộ định tuyến; tuyến nghiệp vụ không đọc tài khoản hay phiên. Danh tính Guardian tách khỏi Symfony. Sao lưu kho và khóa cùng nhau ngoài thư mục công khai. PHP hoặc máy chủ bị xâm nhập có thể lộ cả hai. Lưu trữ dùng cho một máy chủ. Lỗi khởi động, cache hoặc proxy có thể ảnh hưởng toàn ứng dụng.

IP allowlist chấp nhận IPv4, IPv6 và CIDR Mạng: Kiểm tra Symfony cấu hình proxy đáng tin cậy trước khi kích hoạt nó. HTTPS Đối với các cookie an toàn Guardian không sửa chữa các máy chủ web hoặc cơ sở dữ liệu.

Guardian kiểm soát hồ sơ và thanh công cụ debug. Một tài khoản quản trị viên hoặc nhà phát triển được ủy quyền là cần thiết. MFACác chương trình, JIT, giới hạn mạng và khóadown vẫn có thể từ chối truy cập. chế độ ẩn giấu điểm nhập; nó không thay thế các kiểm tra này.

| PHP / HTTP | Kiến trúc và ranh giới tin cậy |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| Hội nghị | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Nguyên tắc bảo mật bất biến

Từ chối chẩn đoán khi không kiểm tra được danh tính, vai trò, phiên, mạng, lịch, JIT hoặc khóa. Trình duyệt không quyết định hết hạn. Thu thập và xem là riêng biệt. Nhớ thiết bị không phải xác thực. Khẩn cấp cần danh tính, mã đã tiêu thụ và nhật ký bền vững; lỗi kiểm toán nghiêm trọng ngăn mở. Không áp dụng một phần cấu hình sai. Kiểm soát tùy chọn không bắt buộc dịch vụ từ xa.

Sao lưu kho cùng khóa và thử phục hồi. Không tự tạo lại khóa bị mất. Sau khi gỡ, chỉ kiểm tra trình phân tích trong môi trường riêng. Sản xuất: APP_DEBUG=0, tắt WebProfilerBundle và thu thập. Chuỗi kiểm toán cục bộ không chứng minh toàn vẹn trên máy chủ bị xâm nhập.

## Báo chí và cảnh báo

Mức độ và thời hạn lưu quyết định các sự kiện hiện có, tối đa 5000 mục. Lọc theo loại và tài khoản trước khi đổi trang. Cảnh báo được xếp hàng; cấu hình phương thức vận chuyển và gửi. Chuỗi băm cục bộ không bảo đảm tính toàn vẹn trên máy chủ bị xâm nhập.
