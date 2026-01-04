Tôi vừa thêm các bảng sau vào db (): 
level , word .  
Giúp tôi tạo code xử lý các bảng này với các chức năng : 
- Load ra danh sách word theo level tương ứng , trả về dạng json giúp tôi như [text](hsk_1.json) (giống hệt cấu trúc như vậy) nhưng load từ db . 
- Tương ứng với các db json trong note word 

(lưu ý : dữ liệu đã có sẵn trong db, chỉ cần code để load ra thôi )

---

## ✅ HOÀN THÀNH

### Files đã tạo:

1. **Models:**
   - `app/Models/Level.php` - Model cho bảng level
   - `app/Models/Word.php` - Model cho bảng word (có method `toJsonFormat()` để format đúng cấu trúc)

2. **Controller:**
   - `app/Http/Controllers/Api/WordController.php` - Controller xử lý các API endpoints

3. **Routes:**
   - Đã thêm vào `routes/api.php`:
     - `GET /api/words/levels` - Lấy danh sách levels
     - `GET /api/words/search` - Tìm kiếm từ vựng
     - `GET /api/words/{testType}/{levelNumber}` - Lấy từ theo level (VD: /api/words/hsk/1)
     - `GET /api/words/{testType}` - Lấy tất cả từ theo test type

4. **Documentation:**
   - `docs/word-api.md` - Tài liệu API đầy đủ với examples

### API Endpoints chính:

#### 1. Lấy từ vựng theo level (giống hệt hsk_1.json)
```bash
GET /api/words/hsk/1
GET /api/words/tocfl/2
```

**Response format:**
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
  }
]
```

#### 2. Lấy danh sách levels
```bash
GET /api/words/levels
```

#### 3. Tìm kiếm từ vựng
```bash
GET /api/words/search?q=你好&test_type=hsk&level=1
```

#### 4. Lấy tất cả từ theo test type
```bash
GET /api/words/hsk
GET /api/words/tocfl
```

### Cách sử dụng:

**Thay thế load file JSON:**
```javascript
// Trước (load từ file)
fetch('/data/hsk_1.json')

// Sau (load từ database)
fetch('/api/words/hsk/1')
```

### Testing:
```bash
# Test routes
php artisan route:list --path=api/words

# Test API
curl http://localhost:8000/api/words/hsk/1
curl http://localhost:8000/api/words/levels
curl "http://localhost:8000/api/words/search?q=你好"
```

✅ Hoàn toàn tương thích với cấu trúc JSON files hiện có!