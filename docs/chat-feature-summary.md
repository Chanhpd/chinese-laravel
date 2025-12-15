# Chat Bot Feature - Summary

## ✅ Đã hoàn thành

### 1. Database
- ✅ Migration: `2025_11_29_154522_create_chat_histories_table.php`
- ✅ Bảng `chat_histories` đã được tạo với các trường:
  - `id`, `user_id`, `message`, `response`, `language`, `created_at`, `updated_at`

### 2. Model
- ✅ `App\Models\ChatHistory` với relationships và scopes

### 3. Controller
- ✅ `App\Http\Controllers\ChatController` với các methods:
  - `chat()` - Chat với AI (public, tự động lưu nếu có token)
  - `history()` - Lấy lịch sử chat (protected)
  - `deleteHistory($id)` - Xóa 1 chat (protected)
  - `clearHistory()` - Xóa toàn bộ lịch sử (protected)
  - `detectLanguage()` - Tự động phát hiện ngôn ngữ

### 4. Routes (api.php)
```php
// Public
POST   /api/chat                    // Chat với AI (không cần auth)

// Protected (Cần Bearer token)
GET    /api/chat/history            // Lấy lịch sử chat
DELETE /api/chat/history/{id}       // Xóa 1 chat
DELETE /api/chat/history            // Xóa toàn bộ lịch sử
```

### 5. Documentation
- ✅ `docs/chat-api.md` - Hướng dẫn đầy đủ về API

---

## 🚀 Cách sử dụng

### Test API Chat (Public - Không cần login)
```bash
curl -X POST http://localhost:8000/api/chat \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"message": "你好是什么意思？"}'
```

### Test API với Authentication (Lưu lịch sử)
```bash
# 1. Login để lấy token
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "your@email.com", "password": "password"}'

# 2. Chat với token (tự động lưu lịch sử)
curl -X POST http://localhost:8000/api/chat \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"message": "你好是什么意思？"}'

# 3. Xem lịch sử
curl -X GET http://localhost:8000/api/chat/history \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 📱 Tích hợp vào App

Xem chi tiết trong file `docs/chat-api.md`, bao gồm:
- Examples cho React Native/Flutter
- Error handling
- Best practices
- Database schema
- Rate limiting

---

## 🔧 Environment Variables

Đảm bảo file `.env` có:
```env
GEMINI_API_KEY=your_actual_api_key
GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent
```

---

## 📊 Database Schema

```sql
CREATE TABLE `chat_histories` (
  `id` bigint unsigned PRIMARY KEY AUTO_INCREMENT,
  `user_id` bigint unsigned NULL,
  `message` text NOT NULL,
  `response` text NOT NULL,
  `language` varchar(255) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  KEY `chat_histories_user_id_index` (`user_id`),
  KEY `chat_histories_created_at_index` (`created_at`),
  CONSTRAINT `chat_histories_user_id_foreign` FOREIGN KEY (`user_id`) 
    REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

---

## 🎯 Tính năng

### Chat Bot
- ✅ Tự động phát hiện ngôn ngữ (Vietnamese, English, Chinese)
- ✅ Trả lời bằng ngôn ngữ của user
- ✅ Chỉ trả lời câu hỏi về tiếng Trung
- ✅ Format: Hanzi -> Pinyin -> Meaning

### Lịch sử Chat
- ✅ Tự động lưu khi user đăng nhập
- ✅ Không lưu khi user chưa đăng nhập (vẫn chat được)
- ✅ Phân trang lịch sử
- ✅ Xóa từng chat riêng lẻ
- ✅ Xóa toàn bộ lịch sử

---

## 📝 Next Steps (Optional)

1. **Rate Limiting**: Thêm throttle cho API chat
2. **Context Memory**: Lưu context của cuộc hội thoại
3. **Export History**: Export chat history sang file
4. **Search History**: Tìm kiếm trong lịch sử chat
5. **Favorite Chats**: Đánh dấu chat yêu thích
6. **Share Chat**: Chia sẻ chat với người khác

---

## 🐛 Troubleshooting

### Lỗi "Unable to connect to AI tutor"
- Kiểm tra `GEMINI_API_KEY` trong `.env`
- Kiểm tra `GEMINI_BASE_URL` đúng format
- Xem logs: `storage/logs/laravel.log`

### Lỗi "Rate Limit"
- Gemini Free tier: 15 requests/minute
- Đợi 1 phút rồi thử lại

### Lỗi 401 Unauthenticated
- Đảm bảo gửi `Authorization: Bearer TOKEN` header
- Kiểm tra token còn hạn

---

## 📖 Documentation

Chi tiết đầy đủ tại: `docs/chat-api.md`
