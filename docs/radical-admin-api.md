# Radical Management API - Admin

API để quản lý radicals (bộ thủ/ký tự Hán) trong admin dashboard. Hỗ trợ CRUD đầy đủ, tìm kiếm, lọc, thống kê và import hàng loạt.

## Authentication

Tất cả endpoints yêu cầu authentication với role **admin**, **super_admin**, hoặc **staff**.

```bash
Authorization: Bearer {access_token}
```

## Quyền truy cập theo Role

- ✅ **Admin & Super Admin**: Đầy đủ quyền (xem, tạo, sửa, xóa)
- ✅ **Staff**: Đầy đủ quyền (xem, tạo, sửa, xóa)
- ❌ **User**: Không có quyền truy cập admin endpoints

---

## API Endpoints

### 1. Lấy danh sách radicals

**Endpoint:** `GET /admin/radicals`

**Mô tả:** Lấy danh sách radicals với pagination, filter và search

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `level_id` (optional): Lọc theo level ID
- `level_number` (optional): Lọc theo HSK level number (1-9)
- `search` (optional): Tìm kiếm theo hanzi, pinyin, hoặc meaning
- `stroke_count` (optional): Lọc theo số nét
- `is_favorite` (optional): Lọc radical yêu thích (true/false)
- `sort_by` (optional): Sắp xếp theo field (default: frequency_rank)
  - Giá trị: `frequency_rank`, `hanzi`, `pinyin`, `stroke_count`, `level`
- `sort_order` (optional): Thứ tự sắp xếp (default: asc)
  - Giá trị: `asc`, `desc`
- `per_page` (optional): Số items mỗi trang (default: 50)

**Example Request:**
```bash
GET /admin/radicals?level_number=1&search=的&sort_by=frequency_rank&per_page=20
Authorization: Bearer {token}
```

**Response:** (Status 200)
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "hanzi": "的",
      "traditional": "的",
      "pinyin": "de",
      "radical": "白",
      "stroke_count": 8,
      "frequency_rank": 1,
      "general_standard": "一级字表",
      "level_id": 1,
      "level": {
        "id": 1,
        "level_name": "HSK 1",
        "level_number": 1
      },
      "meaning": "của, đích",
      "meaning_vi": "của, đích",
      "meaning_cn": "的",
      "meaning_en": "of, possessive particle",
      "meaning_jp": "の",
      "meaning_kr": "의",
      "meaning_th": "ของ",
      "meaning_de": "von",
      "meaning_fr": "de",
      "meaning_es": "de",
      "meaning_it": "di",
      "meaning_br": "de",
      "meaning_tr": "in",
      "is_favorite": false
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 20,
    "total": 200
  }
}
```

---

### 2. Lấy chi tiết radical

**Endpoint:** `GET /admin/radicals/{id}`

**Mô tả:** Lấy thông tin chi tiết của một radical

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** (Status 200)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "hanzi": "的",
    "traditional": "的",
    "pinyin": "de",
    "radical": "白",
    "stroke_count": 8,
    "frequency_rank": 1,
    "general_standard": "一级字表",
    "level_id": 1,
    "level": {
      "id": 1,
      "level_name": "HSK 1",
      "level_number": 1,
      "test_type": "HSK"
    },
    "meaning": "của, đích",
    "meaning_vi": "của, đích",
    "meaning_cn": "的",
    "meaning_en": "of, possessive particle",
    "meaning_jp": "の",
    "meaning_kr": "의",
    "meaning_th": "ของ",
    "meaning_de": "von",
    "meaning_fr": "de",
    "meaning_es": "de",
    "meaning_it": "di",
    "meaning_br": "de",
    "meaning_tr": "in",
    "is_favorite": false
  }
}
```

---

### 3. Tạo radical mới

**Endpoint:** `POST /admin/radicals`

**Mô tả:** Tạo một radical mới

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "hanzi": "好",
  "traditional": "好",
  "pinyin": "hǎo",
  "radical": "女",
  "stroke_count": 6,
  "frequency_rank": 5,
  "general_standard": "一级字表",
  "level_id": 1,
  "meaning": "tốt, hay",
  "meaning_vi": "tốt, hay, đẹp",
  "meaning_cn": "好",
  "meaning_en": "good, well",
  "meaning_jp": "良い",
  "meaning_kr": "좋다",
  "meaning_th": "ดี",
  "meaning_de": "gut",
  "meaning_fr": "bon",
  "meaning_es": "bueno",
  "meaning_it": "buono",
  "meaning_br": "bom",
  "meaning_tr": "iyi",
  "is_favorite": false
}
```

**Validation Rules:**
- `hanzi`: required, string, max 10 chars
- `traditional`: optional, string, max 10 chars
- `pinyin`: required, string, max 50 chars
- `radical`: optional, string, max 10 chars
- `stroke_count`: optional, integer, min 1
- `frequency_rank`: optional, integer, min 1
- `general_standard`: optional, string, max 50 chars
- `level_id`: optional, must exist in levels table
- `meaning*`: optional, string
- `is_favorite`: optional, boolean

**Response:** (Status 201)
```json
{
  "success": true,
  "message": "Radical created successfully",
  "data": {
    "id": 123,
    "hanzi": "好",
    "pinyin": "hǎo",
    "level": {
      "id": 1,
      "level_name": "HSK 1",
      "level_number": 1
    }
  }
}
```

---

### 4. Cập nhật radical

**Endpoint:** `PUT /admin/radicals/{id}`

**Mô tả:** Cập nhật thông tin radical

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:** (Tất cả fields đều optional)
```json
{
  "hanzi": "好",
  "pinyin": "hǎo",
  "meaning_vi": "tốt, hay, đẹp, xinh",
  "stroke_count": 6,
  "level_id": 2
}
```

**Response:** (Status 200)
```json
{
  "success": true,
  "message": "Radical updated successfully",
  "data": {
    "id": 123,
    "hanzi": "好",
    "pinyin": "hǎo"
  }
}
```

---

### 5. Xóa radical

**Endpoint:** `DELETE /admin/radicals/{id}`

**Mô tả:** Xóa một radical

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** (Status 200)
```json
{
  "success": true,
  "message": "Radical deleted successfully"
}
```

---

### 6. Thống kê radicals

**Endpoint:** `GET /admin/radicals/statistics`

**Mô tả:** Lấy thống kê về radicals

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** (Status 200)
```json
{
  "success": true,
  "data": {
    "total_radicals": 3500,
    "by_level": [
      {
        "level_name": "HSK 1",
        "level_number": 1,
        "radical_count": 150
      },
      {
        "level_name": "HSK 2",
        "level_number": 2,
        "radical_count": 300
      }
    ],
    "by_stroke_count": [
      {
        "stroke_count": 1,
        "count": 5
      },
      {
        "stroke_count": 2,
        "count": 15
      },
      {
        "stroke_count": 3,
        "count": 35
      }
    ],
    "favorites_count": 120
  }
}
```

---

### 7. Lấy danh sách levels

**Endpoint:** `GET /admin/radicals/levels`

**Mô tả:** Lấy danh sách các HSK levels để sử dụng khi tạo/cập nhật radical

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** (Status 200)
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "test_type": "HSK",
      "level_number": 1,
      "level_name": "HSK 1"
    },
    {
      "id": 2,
      "test_type": "HSK",
      "level_number": 2,
      "level_name": "HSK 2"
    }
  ]
}
```

---

### 8. Import radicals hàng loạt

**Endpoint:** `POST /admin/radicals/bulk-import`

**Mô tả:** Import nhiều radicals cùng lúc từ JSON

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "level_id": 1,
  "radicals": [
    {
      "hanzi": "我",
      "pinyin": "wǒ",
      "meaning_vi": "tôi",
      "meaning_en": "I, me",
      "stroke_count": 7
    },
    {
      "hanzi": "你",
      "pinyin": "nǐ",
      "meaning_vi": "bạn",
      "meaning_en": "you",
      "stroke_count": 7
    }
  ]
}
```

**Notes:**
- `level_id` ở root level sẽ được áp dụng cho tất cả radicals nếu chúng không có `level_id` riêng
- Nếu có lỗi khi import một radical, nó sẽ được báo cáo trong response nhưng các radical khác vẫn tiếp tục được import

**Response:** (Status 200)
```json
{
  "success": true,
  "message": "Successfully imported 2 radicals",
  "imported": 2,
  "errors": []
}
```

**Response with errors:**
```json
{
  "success": true,
  "message": "Successfully imported 1 radicals",
  "imported": 1,
  "errors": [
    {
      "index": 0,
      "data": {
        "hanzi": "我",
        "pinyin": "wo"
      },
      "error": "Validation failed..."
    }
  ]
}
```

---

### 9. Cập nhật radicals hàng loạt

**Endpoint:** `PUT /admin/radicals/bulk-update`

**Mô tả:** Cập nhật nhiều radicals cùng lúc với cùng một dữ liệu

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "ids": [1, 2, 3, 4, 5],
  "data": {
    "level_id": 2,
    "is_favorite": true
  }
}
```

**Validation:**
- `ids`: required, array of existing radical IDs
- `data`: required, object containing fields to update

**Response:** (Status 200)
```json
{
  "success": true,
  "message": "Successfully updated 5 radicals",
  "updated": 5
}
```

---

### 10. Xóa radicals hàng loạt

**Endpoint:** `DELETE /admin/radicals/bulk-delete`

**Mô tả:** Xóa nhiều radicals cùng lúc

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "ids": [1, 2, 3, 4, 5]
}
```

**Validation:**
- `ids`: required, array of existing radical IDs

**Response:** (Status 200)
```json
{
  "success": true,
  "message": "Successfully deleted 5 radicals",
  "deleted": 5
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
  "success": false,
  "message": "Unauthorized. Admin access required."
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Radical not found"
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "hanzi": [
      "The hanzi field is required."
    ],
    "pinyin": [
      "The pinyin field is required."
    ]
  }
}
```

### 500 Server Error
```json
{
  "success": false,
  "message": "An error occurred",
  "error": "Error details..."
}
```

---

## Admin Logs

Tất cả các thao tác với radicals đều được ghi log vào bảng `admin_logs`:

- `create_radical`: Tạo radical mới
- `update_radical`: Cập nhật radical
- `delete_radical`: Xóa radical
- `bulk_import_radicals`: Import hàng loạt
- `bulk_update_radicals`: Cập nhật hàng loạt
- `bulk_delete_radicals`: Xóa hàng loạt

**Xem logs:**
```bash
GET /admin/logs
```

---

## Examples

### Ví dụ 1: Tìm tất cả radical HSK 1 có từ "的"

```bash
GET /admin/radicals?level_number=1&search=的
Authorization: Bearer {token}
```

### Ví dụ 2: Lấy tất cả radical có 5 nét

```bash
GET /admin/radicals?stroke_count=5&sort_by=frequency_rank
Authorization: Bearer {token}
```

### Ví dụ 3: Tạo radical mới

```bash
POST /admin/radicals
Authorization: Bearer {token}
Content-Type: application/json

{
  "hanzi": "爱",
  "traditional": "愛",
  "pinyin": "ài",
  "radical": "爫",
  "stroke_count": 10,
  "level_id": 1,
  "meaning_vi": "yêu, thương",
  "meaning_en": "love"
}
```

### Ví dụ 4: Cập nhật level cho nhiều radicals

```bash
PUT /admin/radicals/bulk-update
Authorization: Bearer {token}
Content-Type: application/json

{
  "ids": [1, 2, 3, 4, 5],
  "data": {
    "level_id": 3
  }
}
```

### Ví dụ 5: Import 100 radicals từ file JSON

```bash
POST /admin/radicals/bulk-import
Authorization: Bearer {token}
Content-Type: application/json

{
  "level_id": 1,
  "radicals": [
    // ... array of 100 radical objects
  ]
}
```

---

## Tips & Best Practices

1. **Sử dụng pagination**: Với số lượng lớn radicals, luôn sử dụng pagination và điều chỉnh `per_page` phù hợp.

2. **Search hiệu quả**: Kết hợp search với filter level để tìm kiếm nhanh hơn:
   ```
   GET /admin/radicals?level_number=1&search=好
   ```

3. **Bulk operations**: Sử dụng bulk operations khi cần cập nhật/xóa nhiều radicals cùng lúc.

4. **Validation**: Luôn validate dữ liệu trước khi gửi, đặc biệt với bulk import.

5. **Frequency rank**: Sử dụng frequency_rank để sắp xếp radical theo độ phổ biến trong tiếng Trung.

6. **Multiple meanings**: Hệ thống hỗ trợ meanings cho 12 ngôn ngữ, tận dụng để tạo ứng dụng đa ngôn ngữ.

---

## Database Schema

```sql
CREATE TABLE `radical` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hanzi` varchar(10) NOT NULL,
  `traditional` varchar(10) DEFAULT NULL,
  `pinyin` varchar(50) NOT NULL,
  `radical` varchar(10) DEFAULT NULL,
  `stroke_count` int(11) DEFAULT NULL,
  `frequency_rank` int(11) DEFAULT NULL,
  `general_standard` varchar(50) DEFAULT NULL,
  `level_id` bigint(20) unsigned DEFAULT NULL,
  `meaning` text DEFAULT NULL,
  `meaning_vi` text DEFAULT NULL,
  `meaning_cn` text DEFAULT NULL,
  `meaning_en` text DEFAULT NULL,
  `meaning_jp` text DEFAULT NULL,
  `meaning_kr` text DEFAULT NULL,
  `meaning_th` text DEFAULT NULL,
  `meaning_de` text DEFAULT NULL,
  `meaning_fr` text DEFAULT NULL,
  `meaning_es` text DEFAULT NULL,
  `meaning_it` text DEFAULT NULL,
  `meaning_br` text DEFAULT NULL,
  `meaning_tr` text DEFAULT NULL,
  `is_favorite` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `radical_level_id_foreign` (`level_id`),
  CONSTRAINT `radical_level_id_foreign` FOREIGN KEY (`level_id`) REFERENCES `level` (`id`) ON DELETE SET NULL
);
```
