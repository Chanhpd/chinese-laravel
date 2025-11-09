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
