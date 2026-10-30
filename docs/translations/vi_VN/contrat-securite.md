# Nguyên tắc bảo mật bất biến

[Mục lục tài liệu](README.md)

Từ chối chẩn đoán khi không kiểm tra được danh tính, vai trò, phiên, mạng, lịch, JIT hoặc khóa. Trình duyệt không quyết định hết hạn. Thu thập và xem là riêng biệt. Nhớ thiết bị không phải xác thực. Khẩn cấp cần danh tính, mã đã tiêu thụ và nhật ký bền vững; lỗi kiểm toán nghiêm trọng ngăn mở. Không áp dụng một phần cấu hình sai. Kiểm soát tùy chọn không bắt buộc dịch vụ từ xa.

Sao lưu kho cùng khóa và thử phục hồi. Không tự tạo lại khóa bị mất. Sau khi gỡ, chỉ kiểm tra trình phân tích trong môi trường riêng. Sản xuất: APP_DEBUG=0, tắt WebProfilerBundle và thu thập. Chuỗi kiểm toán cục bộ không chứng minh toàn vẹn trên máy chủ bị xâm nhập.

Guardian kiểm soát hồ sơ và thanh công cụ debug. Một tài khoản quản trị viên hoặc nhà phát triển được ủy quyền là cần thiết. MFACác chương trình, JIT, giới hạn mạng và khóadown vẫn có thể từ chối truy cập. chế độ ẩn giấu điểm nhập; nó không thay thế các kiểm tra này.

Idle timeout, tuổi thọ tuyệt đối và bộ nhớ thiết bị phục vụ các mục đích khác nhau. ghi nhớ một thiết bị không vượt quá lịch hoặc JITPhản hồi kết thúc các phiên liên quan trên máy chủ. Sử dụng tài khoản cá nhân để phân bổ hành động trong tạp chí.

IP allowlist chấp nhận IPv4, IPv6 và CIDR Mạng: Kiểm tra Symfony cấu hình proxy đáng tin cậy trước khi kích hoạt nó. HTTPS Đối với các cookie an toàn Guardian không sửa chữa các máy chủ web hoặc cơ sở dữ liệu.
