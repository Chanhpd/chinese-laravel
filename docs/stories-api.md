# Stories API Documentation

## Tổng quan

API Stories cung cấp các endpoint để truy cập và quản lý các câu chuyện tiếng Trung theo cấp độ HSK (HSK1-HSK6). Mỗi story bao gồm nội dung tiếng Trung, phiên âm pinyin, bản dịch tiếng Anh và audio.

**Base URL**: `/api`

---

## Public API Endpoints

### 1. Lấy danh sách Stories

**Endpoint**: `GET /api/stories`

**Mô tả**: Lấy danh sách tất cả stories với hỗ trợ phân trang, tìm kiếm và lọc theo HSK level.

**Parameters** (Query String):

| Tham số | Type | Bắt buộc | Mô tả |
|---------|------|----------|-------|
| `hsk_level` | string | Không | Lọc theo HSK level (HSK1, HSK2, HSK3, HSK4, HSK5, HSK6) |
| `search` | string | Không | Tìm kiếm trong title_english, title_chinese, chinese_text |
| `per_page` | integer | Không | Số lượng kết quả mỗi trang (mặc định: 15) |
| `page` | integer | Không | Số trang (mặc định: 1) |

**Request Example**:
```bash
# Lấy tất cả stories
curl -X GET "http://localhost:8000/api/stories"

# Lọc theo HSK1
curl -X GET "http://localhost:8000/api/stories?hsk_level=HSK1"

# Tìm kiếm
curl -X GET "http://localhost:8000/api/stories?search=family"

# Kết hợp filter và pagination
curl -X GET "http://localhost:8000/api/stories?hsk_level=HSK2&per_page=20&page=1"
```

**Response Example** (200 OK):
```json
{
  "data": [
    {
      "id": 1,
      "slug": "who-i-am",
      "title_english": "Who I am",
      "title_chinese": "我是谁",
      "audio_url": "https://traffic.libsyn.com/secure/learnchinese/1064.mp3",
      "image_url": "https://mandarinbean.com/wp-content/uploads/...",
      "tags": "",
      "hsk_level": "HSK1",
      "story_url": "https://mandarinbean.com/who-i-am/",
      "chinese_text": "我的名字叫李大卫我是美国人...",
      "pinyin_text": "wǒ de míng zì jiào Lǐ Dàwèi...",
      "content_html": "<div class='elementor-element'>...</div>",
      "created_at": "2025-11-15T10:30:00.000000Z",
      "updated_at": "2025-11-15T10:30:00.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/stories?page=1",
    "last": "http://localhost:8000/api/stories?page=12",
    "prev": null,
    "next": "http://localhost:8000/api/stories?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 12,
    "per_page": 15,
    "to": 15,
    "total": 170
  }
}
```

---

### 2. Lấy chi tiết Story

**Endpoint**: `GET /api/stories/{slug}`

**Mô tả**: Lấy thông tin chi tiết của một story theo slug.

**Parameters** (URL):

| Tham số | Type | Bắt buộc | Mô tả |
|---------|------|----------|-------|
| `slug` | string | Có | Slug của story (ví dụ: "who-i-am") |

**Request Example**:
```bash
curl -X GET "http://localhost:8000/api/stories/who-i-am"
```

**Response Example** (200 OK):
```json
{
  "data": {
    "id": 1,
    "slug": "who-i-am",
    "title_english": "Who I am",
    "title_chinese": "我是谁",
    "audio_url": "https://traffic.libsyn.com/secure/learnchinese/1064.mp3",
    "image_url": "https://mandarinbean.com/wp-content/uploads/...",
    "tags": "",
    "hsk_level": "HSK1",
    "story_url": "https://mandarinbean.com/who-i-am/",
    "chinese_text": "我的名字叫李大卫我是美国人但是在日本长大...",
    "pinyin_text": "wǒ de míng zì jiào Lǐ Dàwèi wǒ shì Měiguó rén...",
    "content_html": "<div class='elementor-element'>...</div>",
    "created_at": "2025-11-15T10:30:00.000000Z",
    "updated_at": "2025-11-15T10:30:00.000000Z"
  }
}
```

**Error Response** (404 Not Found):
```json
{
  "message": "No query results for model [App\\Models\\Story]."
}
```

---

### 3. Lấy danh sách HSK Levels

**Endpoint**: `GET /api/stories/hsk-levels`

**Mô tả**: Lấy danh sách các HSK level có trong database.

**Request Example**:
```bash
curl -X GET "http://localhost:8000/api/stories/hsk-levels"
```

**Response Example** (200 OK):
```json
{
  "data": [
    "HSK1",
    "HSK2",
    "HSK3",
    "HSK4",
    "HSK5",
    "HSK6"
  ]
}
```

---

## Admin API Endpoints

**Yêu cầu Authentication**: Tất cả các endpoint dưới đây yêu cầu:
- Bearer Token trong header `Authorization: Bearer {token}`
- User phải có role `admin`

### 4. Lấy danh sách Stories (Admin)

**Endpoint**: `GET /api/admin/stories`

**Headers**:
```
Authorization: Bearer {your_admin_token}
Accept: application/json
```

**Parameters**: Giống như endpoint public `/api/stories`

**Request Example**:
```bash
curl -X GET "http://localhost:8000/api/admin/stories" \
  -H "Authorization: Bearer your_admin_token" \
  -H "Accept: application/json"
```

**Response**: Giống như endpoint public nhưng không dùng StoryResource

---

### 5. Tạo Story mới

**Endpoint**: `POST /api/admin/stories`

**Headers**:
```
Authorization: Bearer {your_admin_token}
Content-Type: application/json
Accept: application/json
```

**Request Body**:

| Field | Type | Bắt buộc | Mô tả |
|-------|------|----------|-------|
| `title_english` | string | Có | Tiêu đề tiếng Anh (max: 255) |
| `slug` | string | Không | Slug tùy chỉnh (auto-generate nếu không có) |
| `title_chinese` | string | Không | Tiêu đề tiếng Trung (max: 255) |
| `audio_url` | string | Không | Link file audio |
| `image_url` | string | Không | Link ảnh đại diện |
| `tags` | string | Không | Tags phân cách bằng dấu phẩy (max: 500) |
| `hsk_level` | string | Không | HSK level (HSK1-HSK6) |
| `story_url` | string | Không | Link nguồn gốc story |
| `chinese_text` | text | Không | Nội dung tiếng Trung |
| `pinyin_text` | text | Không | Phiên âm pinyin |
| `content_html` | text | Không | Nội dung HTML với chú thích |

**Request Example**:
```bash
curl -X POST "http://localhost:8000/api/admin/stories" \
  -H "Authorization: Bearer your_admin_token" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title_english": "My Daily Routine",
    "title_chinese": "我的日常生活",
    "hsk_level": "HSK2",
    "chinese_text": "我每天早上七点起床...",
    "pinyin_text": "wǒ měi tiān zǎo shàng qī diǎn qǐ chuáng...",
    "audio_url": "https://example.com/audio.mp3",
    "tags": "daily life, routine"
  }'
```

**Response Example** (201 Created):
```json
{
  "message": "Story created successfully",
  "data": {
    "id": 171,
    "slug": "my-daily-routine",
    "title_english": "My Daily Routine",
    "title_chinese": "我的日常生活",
    "hsk_level": "HSK2",
    "chinese_text": "我每天早上七点起床...",
    "pinyin_text": "wǒ měi tiān zǎo shàng qī diǎn qǐ chuáng...",
    "audio_url": "https://example.com/audio.mp3",
    "tags": "daily life, routine",
    "created_at": "2025-11-15T10:30:00.000000Z",
    "updated_at": "2025-11-15T10:30:00.000000Z"
  }
}
```

**Validation Errors** (422 Unprocessable Entity):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title_english": [
      "English title is required"
    ],
    "hsk_level": [
      "HSK level must be one of: HSK1, HSK2, HSK3, HSK4, HSK5, HSK6"
    ],
    "slug": [
      "This slug is already in use"
    ]
  }
}
```

---

### 6. Xem chi tiết Story (Admin)

**Endpoint**: `GET /api/admin/stories/{id}`

**Headers**:
```
Authorization: Bearer {your_admin_token}
Accept: application/json
```

**Request Example**:
```bash
curl -X GET "http://localhost:8000/api/admin/stories/1" \
  -H "Authorization: Bearer your_admin_token" \
  -H "Accept: application/json"
```

**Response Example** (200 OK):
```json
{
  "data": {
    "id": 1,
    "slug": "who-i-am",
    "title_english": "Who I am",
    "title_chinese": "我是谁",
    "audio_url": "https://traffic.libsyn.com/...",
    "image_url": "https://mandarinbean.com/...",
    "tags": "",
    "hsk_level": "HSK1",
    "story_url": "https://mandarinbean.com/who-i-am/",
    "chinese_text": "我的名字叫李大卫...",
    "pinyin_text": "wǒ de míng zì jiào Lǐ Dàwèi...",
    "content_html": "<div>...</div>",
    "created_at": "2025-11-15T10:30:00.000000Z",
    "updated_at": "2025-11-15T10:30:00.000000Z"
  }
}
```

---

### 7. Cập nhật Story

**Endpoint**: `PUT /api/admin/stories/{id}`

**Headers**:
```
Authorization: Bearer {your_admin_token}
Content-Type: application/json
Accept: application/json
```

**Request Body**: Giống như `POST /api/admin/stories` nhưng tất cả các field đều không bắt buộc

**Request Example**:
```bash
curl -X PUT "http://localhost:8000/api/admin/stories/1" \
  -H "Authorization: Bearer your_admin_token" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title_english": "Who Am I - Updated",
    "audio_url": "https://new-audio-url.com/audio.mp3"
  }'
```

**Response Example** (200 OK):
```json
{
  "message": "Story updated successfully",
  "data": {
    "id": 1,
    "slug": "who-am-i-updated",
    "title_english": "Who Am I - Updated",
    "title_chinese": "我是谁",
    "audio_url": "https://new-audio-url.com/audio.mp3",
    "updated_at": "2025-11-15T11:00:00.000000Z"
  }
}
```

---

### 8. Xóa Story

**Endpoint**: `DELETE /api/admin/stories/{id}`

**Headers**:
```
Authorization: Bearer {your_admin_token}
Accept: application/json
```

**Request Example**:
```bash
curl -X DELETE "http://localhost:8000/api/admin/stories/171" \
  -H "Authorization: Bearer your_admin_token" \
  -H "Accept: application/json"
```

**Response Example** (200 OK):
```json
{
  "message": "Story deleted successfully"
}
```

---

### 9. Thống kê Stories

**Endpoint**: `GET /api/admin/stories/statistics`

**Headers**:
```
Authorization: Bearer {your_admin_token}
Accept: application/json
```

**Request Example**:
```bash
curl -X GET "http://localhost:8000/api/admin/stories/statistics" \
  -H "Authorization: Bearer your_admin_token" \
  -H "Accept: application/json"
```

**Response Example** (200 OK):
```json
{
  "total": 170,
  "by_hsk_level": {
    "HSK1": 20,
    "HSK2": 30,
    "HSK3": 30,
    "HSK4": 30,
    "HSK5": 30,
    "HSK6": 30
  }
}
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "This action is unauthorized."
}
```

### 404 Not Found
```json
{
  "message": "No query results for model [App\\Models\\Story]."
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "Error message here"
    ]
  }
}
```

### 500 Internal Server Error
```json
{
  "message": "Server Error"
}
```

---

## Use Cases & Examples

### Use Case 1: Hiển thị danh sách Stories theo HSK Level

```javascript
// Frontend - Lấy tất cả stories HSK1
fetch('http://localhost:8000/api/stories?hsk_level=HSK1&per_page=10')
  .then(response => response.json())
  .then(data => {
    console.log('Total HSK1 stories:', data.meta.total);
    data.data.forEach(story => {
      console.log(story.title_english, '-', story.title_chinese);
    });
  });
```

### Use Case 2: Xem chi tiết Story với Audio

```javascript
// Frontend - Hiển thị story với audio player
fetch('http://localhost:8000/api/stories/who-i-am')
  .then(response => response.json())
  .then(result => {
    const story = result.data;
    
    // Display story
    document.getElementById('title').textContent = story.title_chinese;
    document.getElementById('subtitle').textContent = story.title_english;
    document.getElementById('chinese').textContent = story.chinese_text;
    document.getElementById('pinyin').textContent = story.pinyin_text;
    document.getElementById('content').innerHTML = story.content_html;
    
    // Setup audio player
    if (story.audio_url) {
      document.getElementById('audio').src = story.audio_url;
    }
  });
```

### Use Case 3: Tìm kiếm Stories

```javascript
// Frontend - Search stories
function searchStories(keyword) {
  fetch(`http://localhost:8000/api/stories?search=${encodeURIComponent(keyword)}`)
    .then(response => response.json())
    .then(data => {
      displayResults(data.data);
    });
}

// Tìm kiếm "family"
searchStories('family');
```

### Use Case 4: Admin tạo Story mới

```javascript
// Admin Dashboard - Create new story
const newStory = {
  title_english: "At the Restaurant",
  title_chinese: "在餐厅",
  hsk_level: "HSK2",
  chinese_text: "我和朋友去餐厅吃饭...",
  pinyin_text: "wǒ hé péng yǒu qù cān tīng chī fàn...",
  tags: "restaurant, food, daily life"
};

fetch('http://localhost:8000/api/admin/stories', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${adminToken}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify(newStory)
})
.then(response => response.json())
.then(data => {
  if (data.message === 'Story created successfully') {
    console.log('Created story ID:', data.data.id);
    // Redirect to story detail or show success message
  }
})
.catch(error => console.error('Error:', error));
```

### Use Case 5: Lấy thống kê để hiển thị Dashboard

```javascript
// Admin Dashboard - Display statistics
fetch('http://localhost:8000/api/admin/stories/statistics', {
  headers: {
    'Authorization': `Bearer ${adminToken}`,
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(stats => {
  console.log(`Total Stories: ${stats.total}`);
  
  // Display chart of stories by HSK level
  const chartData = Object.entries(stats.by_hsk_level).map(([level, count]) => ({
    level: level,
    count: count
  }));
  
  renderChart(chartData);
});
```

---

## Notes & Best Practices

### 1. Pagination
- Luôn sử dụng pagination khi lấy danh sách để tối ưu performance
- Mặc định `per_page=15`, có thể tùy chỉnh từ 1-100

### 2. Slug Generation
- Slug tự động được tạo từ `title_english` nếu không cung cấp
- Slug phải unique trong toàn bộ database
- Format: chữ thường, phân cách bằng dấu gạch ngang

### 3. HSK Levels
- Chỉ chấp nhận: HSK1, HSK2, HSK3, HSK4, HSK5, HSK6
- Phân biệt chữ hoa/thường

### 4. Content HTML
- `content_html` có thể chứa HTML với ruby tags để hiển thị pinyin
- Nên sanitize HTML trước khi hiển thị trên frontend

### 5. Authentication
- Admin endpoints yêu cầu Bearer token
- Token có thể lấy từ `/api/auth/login`
- Token expire sau 24 giờ (có thể cấu hình trong `config/sanctum.php`)

### 6. Rate Limiting
- Public API: 60 requests/minute
- Admin API: 100 requests/minute

---

## Database Schema

```sql
CREATE TABLE stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) UNIQUE NOT NULL,
    title_english VARCHAR(255) NOT NULL,
    title_chinese VARCHAR(255),
    audio_url TEXT,
    image_url TEXT,
    tags VARCHAR(500),
    hsk_level VARCHAR(10),
    story_url TEXT,
    chinese_text TEXT,
    pinyin_text TEXT,
    content_html MEDIUMTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_hsk_level (hsk_level)
);
```

---

## Changelog

### Version 1.0.0 (2025-11-15)
- Initial release
- Public API endpoints for stories listing and detail
- Admin CRUD operations
- HSK level filtering
- Search functionality
- Statistics endpoint
