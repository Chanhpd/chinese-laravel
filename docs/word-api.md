# Word API Documentation

API để load danh sách từ vựng theo level từ database, tương thích với cấu trúc JSON files (hsk_1.json, tocfl_1.json, etc.)

## Cấu trúc Database

### Bảng `level`
- `id`: INT (Primary Key)
- `test_type`: VARCHAR(20) - HSK hoặc TOCFL
- `level_number`: INT - Số cấp độ
- `level_name`: VARCHAR(50) - Tên cấp độ (ví dụ: "HSK 1", "TOCFL 2")

### Bảng `word`
- `id`: INT (Primary Key)
- `word`: VARCHAR(50) - Từ tiếng Trung
- `pinyin`: VARCHAR(100) - Phiên âm pinyin
- `meaning_vi`: TEXT - Nghĩa tiếng Việt
- `meaning_en`: TEXT - Nghĩa tiếng Anh
- `meaning_ru`: TEXT - Nghĩa tiếng Nga
- `meaning_th`: TEXT - Nghĩa tiếng Thái
- `meaning_ms`: TEXT - Nghĩa tiếng Mã Lai
- `meaning_ko`: TEXT - Nghĩa tiếng Hàn
- `meaning_ja`: TEXT - Nghĩa tiếng Nhật
- `meaning_id`: TEXT - Nghĩa tiếng Indonesia
- `level_id`: INT (Foreign Key -> level.id)

## API Endpoints

### 1. Lấy từ vựng theo level cụ thể

**Endpoint:** `GET /api/words/{testType}/{levelNumber}`

**Mô tả:** Lấy tất cả từ vựng của một level cụ thể, trả về đúng format như hsk_1.json

**Parameters:**
- `testType`: hsk hoặc tocfl (không phân biệt hoa thường)
- `levelNumber`: Số level (1-9 cho HSK, 1-6 cho TOCFL)

**Ví dụ:**
```bash
# Lấy từ vựng HSK level 1
GET /api/words/hsk/1

# Lấy từ vựng TOCFL level 2
GET /api/words/tocfl/2
```

**Response:** (Status 200)
```json
[
  {
    "w": "个",
    "p": "gè",
    "m": "Cái",
    "m_en": "The most commonly used measure word",
    "m_ru": "штука",
    "m_th": "อัน",
    "m_ms": "Unit",
    "m_ko": "개",
    "m_ja": "個",
    "m_id": "buah"
  },
  {
    "w": "书",
    "p": "shū",
    "m": "Sách",
    "m_en": "Book",
    "m_ru": "книга",
    "m_th": "หนังสือ",
    "m_ms": "Buku",
    "m_ko": "책",
    "m_ja": "本",
    "m_id": "Buku"
  }
]
```

**Error Responses:**
```json
// Level không tồn tại (Status 404)
{
  "error": "Level not found for HSK level 1"
}

// Test type không hợp lệ (Status 400)
{
  "error": "Invalid test type. Use HSK or TOCFL."
}
```

---

### 2. Lấy tất cả từ vựng theo test type

**Endpoint:** `GET /api/words/{testType}`

**Mô tả:** Lấy tất cả từ vựng của một test type, nhóm theo level

**Parameters:**
- `testType`: hsk hoặc tocfl

**Ví dụ:**
```bash
# Lấy tất cả từ vựng HSK
GET /api/words/hsk

# Lấy tất cả từ vựng TOCFL
GET /api/words/tocfl
```

**Response:** (Status 200)
```json
[
  {
    "level": "HSK 1",
    "level_number": 1,
    "words": [
      {
        "w": "个",
        "p": "gè",
        "m": "Cái",
        "m_en": "The most commonly used measure word",
        "m_ru": "штука",
        "m_th": "อัน",
        "m_ms": "Unit",
        "m_ko": "개",
        "m_ja": "個",
        "m_id": "buah"
      }
    ]
  },
  {
    "level": "HSK 2",
    "level_number": 2,
    "words": [...]
  }
]
```

---

### 3. Lấy danh sách tất cả levels

**Endpoint:** `GET /api/words/levels`

**Mô tả:** Lấy danh sách tất cả các level có trong hệ thống kèm số lượng từ vựng

**Ví dụ:**
```bash
GET /api/words/levels
```

**Response:** (Status 200)
```json
[
  {
    "id": 1,
    "test_type": "HSK",
    "level_number": 1,
    "level_name": "HSK 1",
    "word_count": 150
  },
  {
    "id": 2,
    "test_type": "HSK",
    "level_number": 2,
    "level_name": "HSK 2",
    "word_count": 150
  },
  {
    "id": 8,
    "test_type": "TOCFL",
    "level_number": 1,
    "level_name": "TOCFL 1",
    "word_count": 500
  }
]
```

---

### 4. Tìm kiếm từ vựng

**Endpoint:** `GET /api/words/search`

**Mô tả:** Tìm kiếm từ vựng theo từ khóa, có thể lọc theo test type và level

**Query Parameters:**
- `q`: Từ khóa tìm kiếm (tìm trong word, pinyin, meaning_vi, meaning_en)
- `test_type`: (Optional) HSK hoặc TOCFL
- `level`: (Optional) Số level

**Ví dụ:**
```bash
# Tìm kiếm từ "你好"
GET /api/words/search?q=你好

# Tìm kiếm trong HSK level 1
GET /api/words/search?q=吃&test_type=hsk&level=1

# Tìm kiếm tất cả từ có nghĩa "eat"
GET /api/words/search?q=eat
```

**Response:** (Status 200)
```json
[
  {
    "w": "你好",
    "p": "nǐ hǎo",
    "m": "Xin chào",
    "m_en": "Hello",
    "m_ru": "Привет",
    "m_th": "สวัสดี",
    "m_ms": "Hello",
    "m_ko": "안녕하세요",
    "m_ja": "こんにちは",
    "m_id": "Halo",
    "level_name": "HSK 1"
  }
]
```

**Note:** Giới hạn 100 kết quả mỗi lần tìm kiếm

---

## Cách sử dụng

### 1. Tương thích với JSON files hiện có

API này trả về format JSON giống hệt với các file JSON hiện có (hsk_1.json, tocfl_2.json, etc.). Bạn có thể thay thế việc đọc file JSON bằng việc gọi API:

**Trước:**
```javascript
// Load từ file JSON
fetch('/data/hsk_1.json')
  .then(res => res.json())
  .then(words => console.log(words));
```

**Sau:**
```javascript
// Load từ database qua API
fetch('/api/words/hsk/1')
  .then(res => res.json())
  .then(words => console.log(words));
```

### 2. Ví dụ với JavaScript/Frontend

```javascript
// Lấy từ vựng HSK 1
async function getHSK1Words() {
  const response = await fetch('/api/words/hsk/1');
  const words = await response.json();
  return words;
}

// Lấy danh sách levels để tạo menu
async function getLevelMenu() {
  const response = await fetch('/api/words/levels');
  const levels = await response.json();
  
  // Group by test type
  const hskLevels = levels.filter(l => l.test_type === 'HSK');
  const tocflLevels = levels.filter(l => l.test_type === 'TOCFL');
  
  return { hsk: hskLevels, tocfl: tocflLevels };
}

// Tìm kiếm từ vựng
async function searchWord(keyword) {
  const response = await fetch(`/api/words/search?q=${keyword}`);
  const results = await response.json();
  return results;
}
```

### 3. Ví dụ với cURL

```bash
# Lấy từ vựng HSK 1
curl http://localhost:8000/api/words/hsk/1

# Lấy danh sách levels
curl http://localhost:8000/api/words/levels

# Tìm kiếm từ "你好"
curl "http://localhost:8000/api/words/search?q=你好"

# Lấy tất cả từ HSK
curl http://localhost:8000/api/words/hsk
```

---

## Models

### Level Model
**File:** `app/Models/Level.php`

**Relations:**
- `words()`: hasMany - Lấy tất cả từ vựng của level này

### Word Model
**File:** `app/Models/Word.php`

**Relations:**
- `level()`: belongsTo - Lấy thông tin level của từ này

**Methods:**
- `toJsonFormat()`: Chuyển đổi word sang format JSON chuẩn

---

## Controller

**File:** `app/Http/Controllers/Api/WordController.php`

**Methods:**
- `getWordsByLevel($testType, $levelNumber)` - Lấy từ theo level
- `getWordsByTestType($testType)` - Lấy tất cả từ theo test type
- `getLevels()` - Lấy danh sách levels
- `searchWords(Request $request)` - Tìm kiếm từ vựng

---

## Routes

**File:** `routes/api.php`

```php
Route::prefix('words')->group(function () {
    Route::get('/levels', [WordController::class, 'getLevels']);
    Route::get('/search', [WordController::class, 'searchWords']);
    Route::get('/{testType}/{levelNumber}', [WordController::class, 'getWordsByLevel']);
    Route::get('/{testType}', [WordController::class, 'getWordsByTestType']);
});
```

---

## Testing

### Test với Postman hoặc Browser

1. Lấy từ vựng HSK 1:
   ```
   http://localhost:8000/api/words/hsk/1
   ```

2. Lấy danh sách levels:
   ```
   http://localhost:8000/api/words/levels
   ```

3. Tìm kiếm từ "你好":
   ```
   http://localhost:8000/api/words/search?q=你好
   ```

4. Lấy tất cả từ TOCFL:
   ```
   http://localhost:8000/api/words/tocfl
   ```

---

## Notes

- Dữ liệu đã có sẵn trong database (đã import từ JSON files qua script Python)
- API hoàn toàn tương thích với format JSON files hiện có
- Không cần authentication để truy cập các API này (public)
- Hỗ trợ tìm kiếm đa ngôn ngữ (vi, en, ru, th, ms, ko, ja, id)
- Response format: JSON
- Encoding: UTF-8
