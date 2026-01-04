# Radical API Documentation

API để load danh sách ký tự Hán (radicals/characters) theo HSK level từ database

## Cấu trúc Database

### Bảng `radical`
- `id`: INT (Primary Key)
- `hanzi`: VARCHAR(10) - Ký tự Hán (giản thể)
- `traditional`: VARCHAR(10) - Chữ phồn thể
- `pinyin`: VARCHAR(50) - Phiên âm pinyin
- `radical`: VARCHAR(50) - Bộ thủ (ví dụ: "白 106.3")
- `stroke_count`: INT - Số nét viết
- `frequency_rank`: INT - Thứ tự tần suất sử dụng
- `general_standard`: VARCHAR(20) - Tiêu chuẩn chung
- `level_id`: INT (Foreign Key -> level.id) - HSK level
- `meaning`: TEXT - Nghĩa chính (tiếng Anh)
- `meaning_vi`: TEXT - Nghĩa tiếng Việt
- `meaning_cn`: TEXT - Nghĩa tiếng Trung
- `meaning_en`: TEXT - Nghĩa tiếng Anh
- `meaning_jp`: TEXT - Nghĩa tiếng Nhật
- `meaning_kr`: TEXT - Nghĩa tiếng Hàn
- `meaning_th`: TEXT - Nghĩa tiếng Thái
- `meaning_de`: TEXT - Nghĩa tiếng Đức
- `meaning_fr`: TEXT - Nghĩa tiếng Pháp
- `meaning_es`: TEXT - Nghĩa tiếng Tây Ban Nha
- `meaning_it`: TEXT - Nghĩa tiếng Ý
- `meaning_br`: TEXT - Nghĩa tiếng Brazil
- `meaning_tr`: TEXT - Nghĩa tiếng Thổ Nhĩ Kỳ
- `is_favorite`: TINYINT(1) - Đánh dấu yêu thích

## API Endpoints

### 1. Lấy radicals theo HSK level

**Endpoint:** `GET /api/radicals/hsk/{levelNumber}`

**Mô tả:** Lấy tất cả ký tự Hán của một HSK level cụ thể, sắp xếp theo thứ tự tần suất

**Parameters:**
- `levelNumber`: Số HSK level (1-9)

**Ví dụ:**
```bash
# Lấy radicals HSK level 1
GET /api/radicals/hsk/1

# Lấy radicals HSK level 6
GET /api/radicals/hsk/6
```

**Response:** (Status 200)
```json
[
  {
    "hanzi": "的",
    "traditional": "的",
    "pinyin": "de",
    "radical": "白 106.3",
    "stroke_count": 8,
    "frequency_rank": 1,
    "general_standard": "1155",
    "meaning": "possessive, adjectival suffix",
    "meaning_vi": "hậu tố sở hữu, tính từ",
    "meaning_cn": "所有格、形容词后缀",
    "meaning_en": "possessive, adjectival suffix",
    "meaning_jp": "所有格、形容詞接尾辞",
    "meaning_kr": "소유격, 형용사 접미사",
    "meaning_th": "คำต่อท้ายคำคุณศัพท์แสดงความเป็นเจ้าของ",
    "meaning_de": "Possessiv, Adjektivsuffix",
    "meaning_fr": "suffixe possessif, adjectival",
    "meaning_es": "posesivo, sufijo adjetivo",
    "meaning_it": "suffisso possessivo e aggettivale",
    "meaning_br": "possessivo, sufixo adjetivo",
    "meaning_tr": "iyelik, sıfat eki",
    "is_favorite": false
  },
  {
    "hanzi": "一",
    "traditional": "一",
    "pinyin": "yī",
    "radical": "一 1.0",
    "stroke_count": 1,
    "frequency_rank": 2,
    "general_standard": "1",
    "meaning": "one; a, an; alone",
    "meaning_vi": "một; một, một; một mình",
    "meaning_cn": "一;一个，一个；独自的",
    "meaning_en": "one; a, an; alone",
    "meaning_jp": "1つ; 、、、一人で",
    "meaning_kr": "하나; 에, 에; 홀로",
    "meaning_th": "หนึ่ง; ก, อัน; ตามลำพัง",
    "meaning_de": "eins; ein, ein; allein",
    "meaning_fr": "un; un, un; seul",
    "meaning_es": "uno; una, una; solo",
    "meaning_it": "uno; un, un; solo",
    "meaning_br": "um; um, um; sozinho",
    "meaning_tr": "bir; a, an; yalnız",
    "is_favorite": false
  }
]
```

**Error Responses:**
```json
// Level không tồn tại (Status 404)
{
  "error": "HSK level 1 not found"
}
```

---

### 2. Lấy tất cả radicals HSK

**Endpoint:** `GET /api/radicals/hsk`

**Mô tả:** Lấy tất cả ký tự Hán HSK, nhóm theo level

**Ví dụ:**
```bash
GET /api/radicals/hsk
```

**Response:** (Status 200)
```json
[
  {
    "level": "HSK 1",
    "level_number": 1,
    "radicals": [
      {
        "hanzi": "的",
        "traditional": "的",
        "pinyin": "de",
        "radical": "白 106.3",
        "stroke_count": 8,
        "frequency_rank": 1,
        "meaning": "possessive, adjectival suffix",
        "meaning_vi": "hậu tố sở hữu, tính từ",
        "is_favorite": false
      }
    ]
  },
  {
    "level": "HSK 2",
    "level_number": 2,
    "radicals": [...]
  }
]
```

---

### 3. Lấy danh sách levels với số lượng radicals

**Endpoint:** `GET /api/radicals/levels`

**Mô tả:** Lấy danh sách tất cả HSK levels kèm số lượng ký tự Hán

**Ví dụ:**
```bash
GET /api/radicals/levels
```

**Response:** (Status 200)
```json
[
  {
    "id": 1,
    "test_type": "HSK",
    "level_number": 1,
    "level_name": "HSK 1",
    "radical_count": 174
  },
  {
    "id": 2,
    "test_type": "HSK",
    "level_number": 2,
    "level_name": "HSK 2",
    "radical_count": 347
  }
]
```

---

### 4. Tìm kiếm radicals

**Endpoint:** `GET /api/radicals/search`

**Mô tả:** Tìm kiếm ký tự Hán theo từ khóa, có thể lọc theo level, số nét, yêu thích

**Query Parameters:**
- `q`: Từ khóa tìm kiếm (tìm trong hanzi, pinyin, meaning, meaning_vi, meaning_en, meaning_cn)
- `level`: (Optional) HSK level number (1-9)
- `strokes`: (Optional) Số nét viết
- `favorite`: (Optional) Chỉ lấy các ký tự yêu thích (1 hoặc true)
- `order_by`: (Optional) Sắp xếp theo 'frequency_rank' (mặc định) hoặc 'stroke_count'

**Ví dụ:**
```bash
# Tìm kiếm ký tự "你"
GET /api/radicals/search?q=你

# Tìm kiếm trong HSK level 1
GET /api/radicals/search?q=人&level=1

# Tìm các ký tự có 3 nét
GET /api/radicals/search?strokes=3

# Lấy các ký tự yêu thích
GET /api/radicals/search?favorite=1

# Tìm kiếm và sắp xếp theo số nét
GET /api/radicals/search?q=一&order_by=stroke_count
```

**Response:** (Status 200)
```json
[
  {
    "hanzi": "你",
    "traditional": "你",
    "pinyin": "nǐ",
    "radical": "人 9.5",
    "stroke_count": 7,
    "frequency_rank": 32,
    "general_standard": "782",
    "meaning": "you, second person pronoun",
    "meaning_vi": "bạn, đại từ nhân xưng ngôi thứ hai",
    "meaning_cn": "你，第二人称代词",
    "meaning_en": "you, second person pronoun",
    "is_favorite": false,
    "level_name": "HSK 1"
  }
]
```

**Note:** Giới hạn 100 kết quả mỗi lần tìm kiếm

---

### 5. Lấy thống kê radicals

**Endpoint:** `GET /api/radicals/statistics`

**Mô tả:** Lấy thống kê tổng quan về radicals trong hệ thống

**Ví dụ:**
```bash
GET /api/radicals/statistics
```

**Response:** (Status 200)
```json
{
  "total_radicals": 2663,
  "favorite_count": 45,
  "by_level": [
    {
      "level": "HSK 1",
      "count": 174
    },
    {
      "level": "HSK 2",
      "count": 347
    },
    {
      "level": "HSK 3",
      "count": 617
    }
  ],
  "by_stroke_count": [
    {
      "strokes": 1,
      "count": 5
    },
    {
      "strokes": 2,
      "count": 12
    },
    {
      "strokes": 3,
      "count": 28
    }
  ]
}
```

---

### 6. Toggle yêu thích

**Endpoint:** `POST /api/radicals/{id}/favorite`

**Mô tả:** Đánh dấu hoặc bỏ đánh dấu một ký tự Hán là yêu thích

**Parameters:**
- `id`: ID của radical

**Ví dụ:**
```bash
POST /api/radicals/123/favorite
```

**Response:** (Status 200)
```json
{
  "success": true,
  "is_favorite": true,
  "message": "Added to favorites"
}
```

hoặc

```json
{
  "success": true,
  "is_favorite": false,
  "message": "Removed from favorites"
}
```

---

## Cách sử dụng

### 1. Ví dụ với JavaScript/Frontend

```javascript
// Lấy radicals HSK 1
async function getHSK1Radicals() {
  const response = await fetch('/api/radicals/hsk/1');
  const radicals = await response.json();
  return radicals;
}

// Lấy danh sách levels để tạo menu
async function getRadicalLevelMenu() {
  const response = await fetch('/api/radicals/levels');
  const levels = await response.json();
  return levels;
}

// Tìm kiếm ký tự Hán
async function searchRadical(keyword) {
  const response = await fetch(`/api/radicals/search?q=${keyword}`);
  const results = await response.json();
  return results;
}

// Lấy thống kê
async function getRadicalStats() {
  const response = await fetch('/api/radicals/statistics');
  const stats = await response.json();
  return stats;
}

// Toggle yêu thích
async function toggleFavorite(radicalId) {
  const response = await fetch(`/api/radicals/${radicalId}/favorite`, {
    method: 'POST',
  });
  const result = await response.json();
  return result;
}

// Tìm các ký tự theo số nét
async function findByStrokes(strokeCount) {
  const response = await fetch(`/api/radicals/search?strokes=${strokeCount}`);
  const results = await response.json();
  return results;
}
```

### 2. Ví dụ với cURL

```bash
# Lấy radicals HSK 1
curl http://localhost:8000/api/radicals/hsk/1

# Lấy danh sách levels
curl http://localhost:8000/api/radicals/levels

# Tìm kiếm ký tự "你好"
curl "http://localhost:8000/api/radicals/search?q=你好"

# Lấy tất cả radicals HSK
curl http://localhost:8000/api/radicals/hsk

# Lấy thống kê
curl http://localhost:8000/api/radicals/statistics

# Toggle favorite
curl -X POST http://localhost:8000/api/radicals/123/favorite

# Tìm các ký tự có 5 nét trong HSK 1
curl "http://localhost:8000/api/radicals/search?strokes=5&level=1"

# Lấy ký tự yêu thích
curl "http://localhost:8000/api/radicals/search?favorite=1"
```

---

## Models

### Radical Model
**File:** `app/Models/Radical.php`

**Relations:**
- `level()`: belongsTo - Lấy thông tin HSK level của ký tự này

**Methods:**
- `toJsonFormat()`: Chuyển đổi radical sang format JSON chuẩn

### Level Model (Updated)
**File:** `app/Models/Level.php`

**Relations:**
- `words()`: hasMany - Lấy tất cả từ vựng của level này
- `radicals()`: hasMany - Lấy tất cả ký tự Hán của level này

---

## Controller

**File:** `app/Http/Controllers/Api/RadicalController.php`

**Methods:**
- `getRadicalsByLevel($levelNumber)` - Lấy radicals theo HSK level
- `getAllHSKRadicals()` - Lấy tất cả radicals HSK nhóm theo level
- `getLevels()` - Lấy danh sách levels với số lượng radicals
- `searchRadicals(Request $request)` - Tìm kiếm radicals
- `getStatistics()` - Lấy thống kê radicals
- `toggleFavorite($id)` - Đánh dấu yêu thích

---

## Routes

**File:** `routes/api.php`

```php
Route::prefix('radicals')->group(function () {
    Route::get('/levels', [RadicalController::class, 'getLevels']);
    Route::get('/statistics', [RadicalController::class, 'getStatistics']);
    Route::get('/search', [RadicalController::class, 'searchRadicals']);
    Route::get('/hsk/{levelNumber}', [RadicalController::class, 'getRadicalsByLevel']);
    Route::get('/hsk', [RadicalController::class, 'getAllHSKRadicals']);
    Route::post('/{id}/favorite', [RadicalController::class, 'toggleFavorite']);
});
```

---

## Use Cases

### 1. Hiển thị danh sách ký tự theo level
```javascript
// Load HSK 1 characters
const radicals = await fetch('/api/radicals/hsk/1').then(r => r.json());

// Display in a grid
radicals.forEach(radical => {
  console.log(`${radical.hanzi} (${radical.pinyin}): ${radical.meaning_vi}`);
  console.log(`Frequency: #${radical.frequency_rank}, Strokes: ${radical.stroke_count}`);
});
```

### 2. Tìm kiếm và lọc
```javascript
// Search by meaning
const results = await fetch('/api/radicals/search?q=one').then(r => r.json());

// Filter by strokes
const threeStrokes = await fetch('/api/radicals/search?strokes=3').then(r => r.json());

// Get favorites
const favorites = await fetch('/api/radicals/search?favorite=1').then(r => r.json());
```

### 3. Thống kê và phân tích
```javascript
// Get statistics
const stats = await fetch('/api/radicals/statistics').then(r => r.json());

console.log(`Total characters: ${stats.total_radicals}`);
console.log(`Favorites: ${stats.favorite_count}`);

// Show distribution by level
stats.by_level.forEach(level => {
  console.log(`${level.level}: ${level.count} characters`);
});

// Show distribution by stroke count
stats.by_stroke_count.forEach(item => {
  console.log(`${item.strokes} strokes: ${item.count} characters`);
});
```

### 4. Quản lý yêu thích
```javascript
// Add to favorites
await fetch('/api/radicals/123/favorite', { method: 'POST' });

// Get all favorites
const favorites = await fetch('/api/radicals/search?favorite=1').then(r => r.json());
```

---

## Testing

### Test với Postman hoặc Browser

1. Lấy radicals HSK 1:
   ```
   http://localhost:8000/api/radicals/hsk/1
   ```

2. Lấy danh sách levels:
   ```
   http://localhost:8000/api/radicals/levels
   ```

3. Tìm kiếm ký tự "你":
   ```
   http://localhost:8000/api/radicals/search?q=你
   ```

4. Lấy thống kê:
   ```
   http://localhost:8000/api/radicals/statistics
   ```

5. Tìm ký tự có 3 nét:
   ```
   http://localhost:8000/api/radicals/search?strokes=3
   ```

---

## Notes

- Dữ liệu đã có sẵn trong database (đã import từ JSON files qua script Python)
- API không cần authentication (public)
- Hỗ trợ tìm kiếm đa ngôn ngữ (12 ngôn ngữ)
- Radicals được sắp xếp theo frequency_rank (tần suất sử dụng) mặc định
- Có thể lọc theo số nét viết (stroke_count)
- Có thể đánh dấu yêu thích để tạo danh sách học tập cá nhân
- Response format: JSON
- Encoding: UTF-8

---

## Khác biệt với Word API

| Feature | Word API | Radical API |
|---------|----------|-------------|
| Test Types | HSK, TOCFL | Chỉ HSK |
| Favorite | ❌ | ✅ |
| Stroke Count | ❌ | ✅ |
| Frequency Rank | ❌ | ✅ |
| Traditional Form | ❌ | ✅ |
| Radical Info | ❌ | ✅ |
| Languages | 9 ngôn ngữ | 12 ngôn ngữ |
| Statistics | ❌ | ✅ |
