# Sign-ins và mật khẩu

[Mục lục tài liệu](README.md)


## Tài khoản và đăng ký đầu tiên

Chào mừng bạn đến với Guardian. mật khẩu ban đầu của bạn là tạm thời. chọn một bí mật mới: bảng điều khiển và chẩn đoán vẫn bị khóa cho đến khi bạn làm. tập tin xác nhận ban đầu sẽ được xóa sau khi xác minh.

Guardian Tài khoản được tách ra khỏi tài khoản trang web. Tài khoản ban đầu và tài khoản mới phải thay đổi mật khẩu của họ trước khi tiếp tục. Chơi vai trò bảo mật cho phép thông tin mà không cần truy cập hồ sơ. Thay đổi một vai trò hoặc mật khẩu chấm dứt các phiên trước đó.

Chuẩn bị đường dẫn riêng tư, khóa, kho, tệp Symfony và quản trị viên nếu thiếu. Giữ tài khoản hiện có. Từ chối môi trường sản xuất. Sau khi chạy trực tiếp, xóa cache và chạy kiểm tra.


```bash
php bin/console guardian:access --env=dev
```


| yêu cầu mật khẩu được tiết kiệm | Độ dài tối thiểu (Unicode nhân vật) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Chính sách mật khẩu

Các quy tắc này áp dụng cho việc tạo và thay đổi mật khẩu, trên web và trong CLIHọ không tiết lộ hoặc kiểm tra mật khẩu hiện có.

Giá trị ban đầu: 15 Sử dụng một mật khẩu độc đáo hoặc quản lý mật khẩu. Các loại nhân vật cần thiết không đảm bảo mật khẩu không thể đoán trước.

Trên máy chủ này, Guardian Tự động sử dụng Argon2id cho các mật khẩu mới. giữ nó PHP Hỗ trợ được bật. giới hạn ứng dụng là 512 UTF-8 Ưu đãi

cái này PHP Server không cung cấp Argon2id. Guardian Do đó sử dụng bcrypt và từ chối mật khẩu vượt quá 72 UTF-8 Bytes. yêu cầu người quản trị hệ thống của bạn cho phép Argon2id Hỗ trợ trong PHP.

Tùy chọn này sẽ ngay lập tức hủy bỏ các phiên hiện có, bao gồm cả của bạn. Tài khoản phải thay đổi mật khẩu của họ trước khi cấu hình Guardian hoặc mở chẩn đoán.

Đổi mật khẩu tài khoản username hiện có bằng nhập ẩn theo chính sách, thu hồi toàn bộ phiên và ghi nhật ký. Không truyền mật khẩu bằng đối số.

## Các khóa tiến bộ

Mỗi giai đoạn xác định một giới hạn thất bại, tính toán cửa sổ, thời gian khóa và lặp lại cửa sổ. 10 Những thất bại gây ra A 10Các thất bại lặp đi lặp lại có thể kích hoạt giai đoạn tiếp theo. kiểm tra bao gồm cả tài khoản và nguồn gốc mạng, bao gồm xác nhận quản trị viên.

Bộ đếm mã hóa độc lập theo tài khoản chuẩn hóa và nguồn; không giữ IP thô. Đặt chỗ nguyên tử trước khi băm, hết hạn sau 120 giây. Đặt chỗ bỏ dở tính là thất bại; kết quả muộn không tạo phiên. CSRF không hợp lệ không đặt chỗ. Đăng nhập thành công xóa thất bại tài khoản nhưng giữ lịch sử tái phạm và nguồn. Khi đủ 10000 mục hoạt động, từ chối danh tính mới thay vì loại bỏ khóa.

Các giai đoạn chạy theo thứ tự. sau một khóa, các thất bại mới trong cửa sổ lặp lại sử dụng giai đoạn tiếp theo. bên ngoài cửa sổ đó, giai đoạn đầu tiên áp dụng lần nữa. giai đoạn cuối lặp đi lặp. yêu cầu bị chặn không kéo dài sự chậm trễ. tối đa 8 Phân tích phục hồi địa phương: guardian:login:unlock --all.

| giai đoạn | yêu cầu mật khẩu được tiết kiệm |
|---|---|
| thất bại trước khi lockout | 1–100 |
| Lời bài hát: Lockout (Minute) | 1–10080 |
| Tính toán cửa sổ ( phút) | 1–1440 |
| Lặp lại cửa sổ sau khi mở khóa ( phút) | 1–10080 |
| giai đoạn | 1–8; block[n+1] ≥ block[n] |
| Giá trị ban đầu | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Tìm kiếm chi phí phòng thí nghiệm

So sánh một cách đồng nhất bí mật ngẫu nhiên của một chiều dài cố định mà không cần nhập mật khẩu thực sự. Tỷ lệ ghi nhận và giá phần cứng là giả định có thể điều chỉnh. Không có nỗ lực đăng ký được gửi.

Các nhóm ASCII rời nhau và BigInt. Bao hàm-loại trừ tính chính xác không gian; thiếu nhóm hoặc độ dài cho kết quả không. Thứ hạng trung bình không hoàn lại là (N+1)/2. Thời gian chờ tính ngưỡng và cấp cuối lặp lại. Tắt ước tính nếu tốc độ không phù hợp cửa sổ. Chi phí ngoại tuyến bằng giờ nhân giá giờ giả định.

Với một chữ cái cố định, yêu cầu một thể loại làm giảm số lượng bí mật phù hợp. mở rộng chữ cái là một hoạt động khác nhau. thời gian khóa cho rằng các giả thuyết liên tục đủ nhanh để ở lại trong cửa sổ được cấu hình.

Mô hình này không đo sức mạnh của một mật khẩu được lựa chọn bởi con người. Nó loại trừ các từ điển, tái sử dụng, rò rỉ và chiến lược nhắm mục tiêu. Web lockouts không bảo vệ hash bị đánh cắp. Tỷ lệ ngoại tuyến ban đầu là minh họa, không phải là điểm tham chiếu. CPU đo lường : guardian:password:benchmark.

Các con số lớn được viết đầy đủ, với nhóm chữ số và không có điểm E. Thời gian và chi phí là vòng tròn. So sánh cùng một hàng bằng cách sử dụng các giả định tương tự.

| Simulation Alphabet có sẵn | Những bí mật phù hợp có thể |
|---|---|
| ASCII thấp hơn | 26 |
| ASCII Lớn | 26 |
| ASCII số | 10 |
| ASCII biểu tượng loại trừ không gian | 32 |
| Tỷ lệ đăng ký trực tuyến (guesses/s) | 1 / s |
| Tỷ lệ offline (hashes/s) | 10000 / s |
| Chi phí phần cứng (€/giờ) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## Khôi phục và xác minh

Sao lưu kho cùng khóa và thử phục hồi. Không tự tạo lại khóa bị mất. Sau khi gỡ, chỉ kiểm tra trình phân tích trong môi trường riêng. Sản xuất: APP_DEBUG=0, tắt WebProfilerBundle và thu thập. Chuỗi kiểm toán cục bộ không chứng minh toàn vẹn trên máy chủ bị xâm nhập.

Đặt lại bộ đếm username. --ip ĐỊA_CHỈ xóa cả bộ đếm nguồn IPv4/IPv6 đó; nếu không, chặn mạng có thể còn. --all xóa tất cả bộ đếm. Tài khoản, mật khẩu và phiên không đổi; hành động được ghi lại.

[Tham chiếu lệnh](commands.md)
