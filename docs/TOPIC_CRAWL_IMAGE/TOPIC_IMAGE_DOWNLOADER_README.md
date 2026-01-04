# Topic Image Downloader - Hướng dẫn sử dụng

Script này tự động tải ảnh cho các topics từ Bing và cập nhật vào database.

## Yêu cầu

1. Python 3.7+
2. Cài đặt thư viện:
```bash
pip install mysql-connector-python requests
```

## Cách sử dụng

### 1. Cách đơn giản (sử dụng thông tin từ .env)

Tạo file `.env` trong thư mục này hoặc sử dụng file `.env` của Laravel:

```bash
# Chạy từ thư mục docs/TOPIC_CRAWL_IMAGE/
python topic_image_downloader.py --password YOUR_DB_PASSWORD --database YOUR_DB_NAME
```

### 2. Cách đầy đủ với tất cả tùy chọn

```bash
python topic_image_downloader.py \
  --host localhost \
  --user root \
  --password your_password \
  --database chinese_laravel \
  --port 3306 \
  --output img_topic \
  --threads 4 \
  --progress topic_download_progress.json \
  --save-interval 10
```

### 3. Sử dụng file batch (Windows)

Chỉnh sửa `run_topic_downloader.bat` với thông tin database của bạn, sau đó:

```bash
run_topic_downloader.bat
```

## Tham số

- `--host`: MySQL host (mặc định: localhost)
- `--user`: MySQL user (mặc định: root)
- `--password`: MySQL password (bắt buộc)
- `--database`: Tên database (bắt buộc)
- `--port`: MySQL port (mặc định: 3306)
- `--output`: Thư mục lưu ảnh (mặc định: img_topic)
- `--threads`: Số luồng download (mặc định: 4)
- `--progress`: File lưu tiến trình (mặc định: topic_download_progress.json)
- `--save-interval`: Lưu tiến trình sau mỗi N lượt download (mặc định: 10)

## Cách hoạt động

1. Script kết nối database và lấy danh sách topics
2. Với mỗi topic:
   - Tạo MD5 hash từ tên topic
   - Sử dụng `name_zh` (tên tiếng Trung) để tìm ảnh trên Bing
   - Download ảnh về thư mục `img_topic/`
   - Cập nhật `image_url` trong database
3. Lưu tiến trình vào file JSON để có thể resume nếu bị ngắt

## Công thức tìm ảnh

Script sử dụng Bing Image Search API với pattern:
```
https://th.bing.com/th?q={topic_name_zh}+学习+教学&w=720&h=406&c=7&rs=1&p=0&o=5&dpr=2&pid=1.7&mkt=zh-WW&cc=VN&setlang=zh-CN&adlt=moderate&t=1
```

Trong đó:
- `q`: Query (tên topic tiếng Trung + "学习 教学" = learning teaching)
- `w`, `h`: Kích thước ảnh (720x406)
- Các tham số khác: Format và filter của Bing

## Sau khi download

1. Di chuyển ảnh vào thư mục Laravel public storage:
```bash
# Tạo thư mục nếu chưa có
mkdir -p public/storage/topics

# Copy ảnh
cp docs/TOPIC_CRAWL_IMAGE/img_topic/* public/storage/topics/

# Link storage nếu chưa link
php artisan storage:link
```

2. Kiểm tra image URLs trong database:
```sql
SELECT id, name, image_url FROM topics;
```

3. Test API để đảm bảo ảnh hiển thị đúng

## Xử lý lỗi

- Script tự động retry khi download thất bại
- Tiến trình được lưu sau mỗi 10 topics
- Có thể dừng (Ctrl+C) và chạy lại - script sẽ bỏ qua những topics đã download

## Ví dụ output

```
Fetching topics from database...
Found 30 topics to process
Using 4 threads
Images will be saved to: D:\Code\Laravel\chinese-laravel\docs\TOPIC_CRAWL_IMAGE\img_topic
Progress will be saved to: topic_download_progress.json
--------------------------------------------------------------------------------

SUCCESS: Hello and Goodbye -> a1b2c3d4e5f6g7h8i9j0_topic.jpg

SUCCESS: People -> 1a2b3c4d5e6f7g8h9i0j_topic.jpg

Progress: 30/30 (Downloaded: 28, Skipped: 0, Failed: 2) Rate: 2.5/s ETA: 0.0m
--------------------------------------------------------------------------------
Topic image download completed!
Total time: 12.3 minutes
Total topics: 30
Successfully downloaded: 28
Skipped (already exists): 0
Failed: 2
Success rate: 93.3%
Average rate: 2.4 topics/second
```

## Troubleshooting

### Lỗi kết nối database
- Kiểm tra MySQL đang chạy
- Kiểm tra thông tin đăng nhập (user, password, database)
- Kiểm tra port MySQL

### Download thất bại
- Kiểm tra kết nối internet
- Thử giảm số threads (--threads 2)
- Bing có thể block nếu request quá nhiều, hãy đợi vài phút

### Ảnh quá nhỏ hoặc không đúng
- Script tự động bỏ qua ảnh < 1KB
- Có thể chỉnh sửa query trong hàm `generate_bing_image_url()` để có kết quả tốt hơn
