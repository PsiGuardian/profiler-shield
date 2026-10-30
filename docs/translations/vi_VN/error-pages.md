# Trang lỗi công cộng

[Mục lục tài liệu](README.md)


## Trang lỗi công cộng

Giữ nguyên trang của ứng dụng hoặc hiển thị HTML trung tính. Mã trạng thái HTTP thực tế không thay đổi.

Thời trang LETTES Symfony Tạo sai lầm. chế độ tổng quát bỏ qua văn bản tùy chỉnh. chỉ chế độ tùy chỉnh sử dụng thông điệp của người truy cập. tham chiếu công cộng có thể không, ngẫu nhiên hoặc cố định; nó là độc lập với thông điệu. HTTP Mã trạng thái vẫn không thay đổi.

Trang ứng dụng: duy trì trang web hiện có 404 Câu trả lời; ngoại lệ sử dụng 404 template khi có sẵn, nếu không một trang trung lập. trung lập: thông điệp tiêu chuẩn. tùy chỉnh: sử dụng văn bản của bạn.

Ngoại lệ 500 ẩn danh nhận trang trung tính. Chi tiết cần danh tính Guardian được phép và GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Mẫu Twig thiếu hoặc lỗi chuyển sang trang trung tính; bộ điều khiển lỗi riêng cần tích hợp riêng. JSON và trang nghiệp vụ thành công không đổi.

| error_mode | Cài đặt |
|---|---|
| native | Trang ứng dụng: duy trì trang web hiện có 404 Câu trả lời; ngoại lệ sử dụng 404 template khi có sẵn, nếu không một trang trung lập. trung lập: thông điệp tiêu chuẩn. tùy chỉnh: sử dụng văn bản của bạn. |
| generic | HTML 400–599 → Giữ nguyên trang của ứng dụng hoặc hiển thị HTML trung tính. Mã trạng thái HTTP thực tế không thay đổi. |
| custom | Chỉ dùng cho thông báo trung tính tùy chỉnh. Văn bản được giữ lại khi đổi chế độ. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Chỉ có sẵn với một tham chiếu tĩnh tùy chỉnh. |
| error_custom_text | Chỉ dùng cho thông báo trung tính tùy chỉnh. Văn bản được giữ lại khi đổi chế độ. |

## Mã tham chiếu hiển thị

Thông báo khác nhau hoặc tham chiếu cố định có thể làm lộ lỗi thực. Không được giả mạo mã HTTP.

Mục tiêu này áp dụng cho GuardianCác trang trung lập: ngẫu nhiên tạo ra một mã mới, không phải là một thông điệp ngẫu thuật.

Các lựa chọn được kiểm tra trên máy chủ trước khi lưu.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Mạng lưới và triển khai

IP allowlist chấp nhận IPv4, IPv6 và CIDR Mạng: Kiểm tra Symfony cấu hình proxy đáng tin cậy trước khi kích hoạt nó. HTTPS Đối với các cookie an toàn Guardian không sửa chữa các máy chủ web hoặc cơ sở dữ liệu.

Từ chối chẩn đoán khi không kiểm tra được danh tính, vai trò, phiên, mạng, lịch, JIT hoặc khóa. Trình duyệt không quyết định hết hạn. Thu thập và xem là riêng biệt. Nhớ thiết bị không phải xác thực. Khẩn cấp cần danh tính, mã đã tiêu thụ và nhật ký bền vững; lỗi kiểm toán nghiêm trọng ngăn mở. Không áp dụng một phần cấu hình sai. Kiểm soát tùy chọn không bắt buộc dịch vụ từ xa.
