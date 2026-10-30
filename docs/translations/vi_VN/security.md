# Chính sách bảo mật

[Mục lục tài liệu](README.md)

Báo lỗ hổng riêng đến bundle-sec@omegaguardian.fr để thống nhất kênh mã hóa. Không gửi bí mật, mã truy cập đang hoạt động hoặc URL riêng trong thư đầu. Nêu phiên bản Guardian, Symfony và PHP, ảnh hưởng, điều kiện và tái hiện tối thiểu. Người bảo trì xác nhận, điều tra, sửa riêng, thử nghiệm và phối hợp công bố bản vá.

Không bao giờ gửi mật khẩu, bí mật TOTP, khóa mã hóa hoặc URL chẩn đoán qua email.

Sao lưu kho cùng khóa và thử phục hồi. Không tự tạo lại khóa bị mất. Sau khi gỡ, chỉ kiểm tra trình phân tích trong môi trường riêng. Sản xuất: APP_DEBUG=0, tắt WebProfilerBundle và thu thập. Chuỗi kiểm toán cục bộ không chứng minh toàn vẹn trên máy chủ bị xâm nhập.
