# API Documentation - Chinese Learning App

## Base URL
```
http://localhost:8000/api
```

## Authentication
API sử dụng Laravel Sanctum để xác thực. Sau khi đăng nhập hoặc đăng ký, bạn sẽ nhận được token. Token này cần được gửi kèm trong header của các request cần authentication:

```
Authorization: Bearer {your-token}
```

## Multi-Language Support

API hỗ trợ 10 ngôn ngữ:
- `de` - German (Tiếng Đức)
- `en` - English (Tiếng Anh) - **Default**
- `es` - Spanish (Tiếng Tây Ban Nha)
- `fr` - French (Tiếng Pháp)
- `it` - Italian (Tiếng Ý)
- `ja` - Japanese (Tiếng Nhật)
- `ko` - Korean (Tiếng Hàn)
- `ru` - Russian (Tiếng Nga)
- `vi` - Vietnamese (Tiếng Việt)
- `zh` - Chinese (Tiếng Trung)

### Cách sử dụng Language Code

Có 3 cách để chỉ định ngôn ngữ:

1. **Query Parameter** (Khuyến nghị)
```
GET /api/topics?lang=vi
GET /api/vocabularies?lang=ja
```

2. **Header: X-Language**
```
X-Language: ko
```

3. **Header: Accept-Language**
```
Accept-Language: es
```

### Cơ chế Fallback

- Nếu không có language code hoặc code không hợp lệ → trả về **English** (mặc định)
- Nếu không có bản dịch cho ngôn ngữ được chọn → trả về **English** (mặc định)
- Tên topic tiếng Trung (`name_zh`) luôn được trả về cùng với tên đã được dịch

---

## Endpoints

### 1. Ping - Kiểm tra API

**GET** `/api/ping`

Kiểm tra API có hoạt động hay không.

**Requires Authentication**: No

#### Response Success (200)
```json
{
  "success": true,
  "message": "API đang hoạt động",
  "timestamp": "2025-11-09 10:30:45",
  "version": "1.0.0"
}
```

---

### 2. Đăng ký (Register)

**POST** `/api/auth/register`

Đăng ký tài khoản user mới.

#### Request Body
```json
{
  "name": "Nguyễn Văn A",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Response Success (201)
```json
{
  "success": true,
  "message": "Đăng ký thành công",
  "data": {
    "user": {
      "id": 1,
      "name": "Nguyễn Văn A",
      "email": "user@example.com",
      "email_verified_at": null,
      "created_at": "2025-11-08T10:00:00.000000Z",
      "updated_at": "2025-11-08T10:00:00.000000Z"
    },
    "token": "1|abc123def456..."
  }
}
```

#### Response Error (422)
```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password confirmation does not match."]
  }
}
```

---

### 3. Đăng nhập (Login)

**POST** `/api/auth/login`

Đăng nhập vào hệ thống.

#### Request Body
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

#### Response Success (200)
```json
{
  "success": true,
  "message": "Đăng nhập thành công",
  "data": {
    "user": {
      "id": 1,
      "name": "Nguyễn Văn A",
      "email": "user@example.com",
      "email_verified_at": null,
      "created_at": "2025-11-08T10:00:00.000000Z",
      "updated_at": "2025-11-08T10:00:00.000000Z"
    },
    "token": "2|xyz789abc456..."
  }
}
```

#### Response Error (401)
```json
{
  "success": false,
  "message": "Email hoặc mật khẩu không đúng"
}
```

---

### 4. Đăng xuất (Logout)

**POST** `/api/auth/logout`

Đăng xuất và xóa token hiện tại.

**Requires Authentication**: Yes

#### Headers
```
Authorization: Bearer {your-token}
```

#### Response Success (200)
```json
{
  "success": true,
  "message": "Đăng xuất thành công"
}
```

---

### 5. Lấy thông tin user hiện tại

**GET** `/api/auth/me`

Lấy thông tin user đang đăng nhập.

**Requires Authentication**: Yes

#### Headers
```
Authorization: Bearer {your-token}
```

#### Response Success (200)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Nguyễn Văn A",
    "email": "user@example.com",
    "email_verified_at": null,
    "created_at": "2025-11-08T10:00:00.000000Z",
    "updated_at": "2025-11-08T10:00:00.000000Z"
  }
}
```

---

## Test với cURL

### Ping
```bash
curl -X GET http://localhost:8000/api/ping
```

### Đăng ký
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Đăng nhập
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### Lấy thông tin user (với token)
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Cấu hình để test từ app mobile

### 1. Cho phép kết nối từ mạng local
Trong file `.env`, cập nhật:
```
APP_URL=http://YOUR_LOCAL_IP:8000
```

Ví dụ: `APP_URL=http://192.168.1.100:8000`

### 2. Chạy Laravel server trên tất cả network interface
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### 3. Lấy địa chỉ IP local của máy
**Windows:**
```bash
ipconfig
```
Tìm "IPv4 Address" trong phần Wireless LAN adapter hoặc Ethernet adapter.

### 4. Truy cập từ app mobile
Sử dụng địa chỉ:
```
http://192.168.1.100:8000/api
```
(Thay `192.168.1.100` bằng IP thực của bạn)

---

## Error Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request thành công |
| 201 | Created - Tạo resource thành công |
| 401 | Unauthorized - Không có quyền truy cập |
| 422 | Unprocessable Entity - Validation error |
| 500 | Internal Server Error - Lỗi server |

---

## Notes

- Tất cả request và response đều sử dụng JSON format
- Header `Accept: application/json` nên được gửi kèm trong mọi request
- Token không có thời gian hết hạn (có thể cấu hình trong `config/sanctum.php`)
- Khi đăng nhập, tất cả token cũ sẽ bị xóa

---

## Vocabulary & Topics APIs

### 6. Lấy danh sách Topics

**GET** `/api/topics`

Lấy tất cả các chủ đề học từ vựng.

**Requires Authentication**: No

#### Query Parameters
- `lang` (string, optional): Language code (de, en, es, fr, it, ja, ko, ru, vi, zh)
- `active` (boolean, optional): Lọc topics đang active
- `with_count` (boolean, optional): Bao gồm số lượng vocabulary trong mỗi topic
- `with_vocabularies` (boolean, optional): Bao gồm tất cả vocabularies trong topic

#### Response Success (200)
```json
{
  "success": true,
  "language": "vi",
  "data": [
    {
      "id": 1,
      "name": "Chào hỏi cơ bản",
      "name_zh": "问候语",
      "description": "Các cách chào hỏi và giới thiệu bản thân trong tiếng Trung",
      "image_url": "https://example.com/images/greetings.jpg",
      "is_active": true,
      "sort_order": 1,
      "created_at": "2025-11-09T02:45:00.000000Z",
      "updated_at": "2025-11-09T02:45:00.000000Z"
    }
  ]
}
```

**Note**: Khi `lang=en` (hoặc không có lang), `name` sẽ là tiếng Anh. Khi `lang=vi`, `name` sẽ là tiếng Việt.

---

### 7. Lấy chi tiết Topic

**GET** `/api/topics/{id}`

Lấy thông tin chi tiết của một topic.

**Requires Authentication**: No

#### Query Parameters
- `with_vocabularies` (boolean, optional): Bao gồm tất cả vocabularies và translations

#### Response Success (200)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Chào hỏi cơ bản",
    "name_zh": "问候语",
    "description": "Các cách chào hỏi và giới thiệu bản thân trong tiếng Trung",
    "image_url": "https://example.com/images/greetings.jpg",
    "is_active": true,
    "sort_order": 1,
    "created_at": "2025-11-09T02:45:00.000000Z",
    "updated_at": "2025-11-09T02:45:00.000000Z"
  }
}
```

---

### 8. Lấy vocabularies theo Topic

**GET** `/api/topics/{id}/vocabularies`

Lấy tất cả từ vựng trong một topic.

**Requires Authentication**: No

#### Query Parameters
- `with_translations` (boolean, optional): Bao gồm translations
- `level` (string, optional): Lọc theo level (HSK1, HSK2, etc.)
- `per_page` (integer, optional, default: 20): Số items per page

#### Response Success (200)
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "topic_id": 1,
        "word": "你好",
        "phonetic": "nǐ hǎo",
        "pinyin": "nǐ hǎo",
        "simplified": "你好",
        "traditional": "你好",
        "part_of_speech": "phrase",
        "meaning": "Hello, Hi",
        "meaning_vi": "Xin chào",
        "meaning_zh": "你好",
        "example_sentence": "你好，我叫李明。",
        "example_translation": "Xin chào, tôi tên là Lý Minh.",
        "example_highlight": "你好",
        "definition": "Common greeting used at any time of day",
        "radical_info": "亻(người)",
        "stroke_count": 9,
        "tone_pattern": "3-3",
        "related_words": ["您好", "大家好", "嗨"],
        "similar_chars": ["妳好"],
        "pronunciation_audio": "https://example.com/audio/nihao.mp3",
        "image_url": "https://example.com/images/nihao.jpg",
        "level": "HSK1",
        "created_at": "2025-11-09T02:45:00.000000Z",
        "updated_at": "2025-11-09T02:45:00.000000Z"
      }
    ],
    "per_page": 20,
    "total": 5
  }
}
```

---

### 9. Lấy danh sách Vocabularies

**GET** `/api/vocabularies`

Lấy danh sách từ vựng với các bộ lọc.

**Requires Authentication**: No

#### Query Parameters
- `lang` (string, optional): Language code (de, en, es, fr, it, ja, ko, ru, vi, zh)
- `search` (string, optional): Tìm kiếm theo word, pinyin, meaning
- `topic_id` (integer, optional): Lọc theo topic
- `level` (string, optional): Lọc theo level (HSK1, HSK2, etc.)
- `per_page` (integer, optional, default: 20): Số items per page

#### Response Success (200)
```json
{
  "success": true,
  "language": "vi",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 2,
        "topic_id": 1,
        "word": "你",
        "phonetic": "nǐ",
        "pinyin": "nǐ",
        "simplified": "你",
        "traditional": "你",
        "part_of_speech": "pronoun",
        "meaning": "Bạn",
        "meaning_vi": "Bạn",
        "meaning_zh": "你",
        "example_sentence": "你是学生吗？",
        "example_translation": "Bạn là học sinh phải không?",
        "example_highlight": "你",
        "definition": "Second person singular pronoun",
        "radical_info": "亻(person)",
        "stroke_count": 7,
        "tone_pattern": "3",
        "related_words": ["您", "你们", "你的"],
        "similar_chars": ["妳", "尔"],
        "pronunciation_audio": "https://example.com/audio/ni.mp3",
        "image_url": "https://example.com/images/ni.jpg",
        "level": "HSK1",
        "topic": {
          "id": 1,
          "name": "Chào hỏi cơ bản",
          "name_zh": "问候语"
        }
      }
    ],
    "per_page": 20,
    "total": 50
  }
}
```

**Note**: 
- Khi `lang=vi`, `meaning` và `example_translation` sẽ là tiếng Việt
- Khi `lang=en` hoặc không có lang, sẽ trả về tiếng Anh (mặc định)
- Các trường `meaning_vi`, `meaning_zh` vẫn được trả về để app có thể sử dụng linh hoạt

---

### 10. Lấy chi tiết Vocabulary

**GET** `/api/vocabularies/{id}`

Lấy thông tin chi tiết của một từ vựng.

**Requires Authentication**: No

#### Query Parameters
- `with_translations` (boolean, optional): Bao gồm tất cả translations

#### Response Success (200)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "topic_id": 1,
    "word": "你好",
    "phonetic": "nǐ hǎo",
    "pinyin": "nǐ hǎo",
    "simplified": "你好",
    "traditional": "你好",
    "part_of_speech": "phrase",
    "meaning": "Hello, Hi",
    "meaning_vi": "Xin chào",
    "meaning_zh": "你好",
    "example_sentence": "你好，我叫李明。",
    "example_translation": "Xin chào, tôi tên là Lý Minh.",
    "example_highlight": "你好",
    "definition": "Common greeting used at any time of day",
    "radical_info": "亻(người)",
    "stroke_count": 9,
    "tone_pattern": "3-3",
    "related_words": ["您好", "大家好", "嗨"],
    "similar_chars": ["妳好"],
    "pronunciation_audio": "https://example.com/audio/nihao.mp3",
    "image_url": "https://example.com/images/nihao.jpg",
    "level": "HSK1",
    "topic": {
      "id": 1,
      "name": "Chào hỏi cơ bản",
      "name_zh": "问候语"
    },
    "translations": [
      {
        "id": 1,
        "vocabulary_id": 1,
        "language_code": "ja",
        "meaning": "こんにちは",
        "example_translation": "こんにちは、私は李明です。"
      }
    ]
  }
}
```

---

### 11. Lấy Vocabulary ngẫu nhiên

**GET** `/api/vocabularies/random`

Lấy từ vựng ngẫu nhiên để luyện tập.

**Requires Authentication**: No

#### Query Parameters
- `topic_id` (integer, optional): Lọc theo topic
- `level` (string, optional): Lọc theo level
- `count` (integer, optional, default: 1): Số lượng vocabulary cần lấy

#### Response Success (200)
```json
{
  "success": true,
  "data": [
    {
      "id": 5,
      "word": "对不起",
      "pinyin": "duì bu qǐ",
      "simplified": "对不起",
      "meaning": "Sorry, I apologize",
      "meaning_vi": "Xin lỗi",
      "level": "HSK1"
    }
  ]
}
```

---

### 12. Lấy Translation theo ngôn ngữ

**GET** `/api/vocabularies/{id}/translation/{languageCode}`

Lấy bản dịch của một vocabulary theo mã ngôn ngữ.

**Requires Authentication**: No

#### Path Parameters
- `id`: ID của vocabulary
- `languageCode`: Mã ngôn ngữ (vi, ja, ko, es, fr, etc.)

#### Response Success (200)
```json
{
  "success": true,
  "data": {
    "vocabulary": {
      "id": 3,
      "word": "再见",
      "simplified": "再见",
      "pinyin": "zài jiàn"
    },
    "translation": {
      "id": 1,
      "vocabulary_id": 3,
      "language_code": "ja",
      "meaning": "さようなら",
      "example_translation": "また明日、さようなら！"
    }
  }
}
```

---

## Test API Vocabularies với cURL

### Lấy danh sách topics (tiếng Việt)
```bash
curl "http://localhost:8000/api/topics?lang=vi"
```

### Lấy danh sách topics (tiếng Nhật)
```bash
curl "http://localhost:8000/api/topics?lang=ja"
```

### Lấy topic với số lượng vocabularies
```bash
curl "http://localhost:8000/api/topics?with_count=1&lang=ko"
```

### Lấy vocabularies của topic 1 (tiếng Việt)
```bash
curl "http://localhost:8000/api/topics/1/vocabularies?lang=vi"
```

### Tìm kiếm vocabulary (tiếng Tây Ban Nha)
```bash
curl "http://localhost:8000/api/vocabularies?search=你好&lang=es"
```

### Lấy vocabulary ngẫu nhiên (5 từ, HSK1, tiếng Pháp)
```bash
curl "http://localhost:8000/api/vocabularies/random?count=5&level=HSK1&lang=fr"
```

### Lấy chi tiết vocabulary (tiếng Hàn)
```bash
curl "http://localhost:8000/api/vocabularies/1?lang=ko"
```

### Test với header X-Language
```bash
curl -H "X-Language: vi" http://localhost:8000/api/topics
```
