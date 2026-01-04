# Staff Role - Hướng dẫn sử dụng

## Tổng quan

Role **Staff** là một vai trò quản trị với quyền hạn hạn chế hơn so với Admin và Super Admin. Staff có thể truy cập admin dashboard nhưng chỉ có quyền xem dữ liệu, không thể thực hiện các thao tác thay đổi.

## Phân quyền

### Các role trong hệ thống:
- **user**: Người dùng thông thường
- **staff**: Nhân viên quản trị (quyền hạn chế)
- **admin**: Quản trị viên
- **super_admin**: Quản trị viên cấp cao nhất

## Quyền hạn của Staff

### ✅ Được phép:

#### 1. Dashboard & Statistics
- Xem trang dashboard
- Xem logs hệ thống
- Xem thống kê user growth
- Xem thống kê learning activity
- Xem danh sách top learners

#### 2. User Management
- **Chỉ xem**: Xem danh sách users
- **Chỉ xem**: Xem chi tiết thông tin user
- **Chỉ xem**: Xem tiến độ học tập của user
- **Chỉ xem**: Xem từ vựng đã lưu của user

#### 3. Topics Management
- Xem danh sách topics
- Tạo topic mới
- Cập nhật topic
- Xóa topic
- Cập nhật bản dịch topic

#### 4. Vocabularies Management
- Xem danh sách vocabularies
- Tạo vocabulary mới
- Cập nhật vocabulary
- Xóa vocabulary
- Cập nhật bản dịch vocabulary

#### 5. Stories Management
- Xem danh sách stories
- Tạo story mới
- Xem chi tiết story
- Cập nhật story
- Xóa story
- Xem thống kê stories

### ❌ Không được phép:

#### User Management
- ❌ Tạo user mới
- ❌ Cập nhật thông tin user
- ❌ Xóa user
- ❌ Thay đổi role của user
- ❌ Block/Unblock user

## API Endpoints & Quyền truy cập

### Admin Dashboard
```
GET  /admin                             ✅ Staff có quyền
GET  /admin/logs                        ✅ Staff có quyền
GET  /admin/statistics/user-growth     ✅ Staff có quyền
GET  /admin/statistics/learning-activity  ✅ Staff có quyền
GET  /admin/statistics/top-learners    ✅ Staff có quyền
```

### User Management
```
GET    /admin/users                    ✅ Staff có quyền (chỉ xem)
GET    /admin/users/{id}               ✅ Staff có quyền (chỉ xem)
POST   /admin/users                    ❌ Staff KHÔNG có quyền
PUT    /admin/users/{id}               ❌ Staff KHÔNG có quyền
DELETE /admin/users/{id}               ❌ Staff KHÔNG có quyền
PUT    /admin/users/{id}/role          ❌ Staff KHÔNG có quyền
POST   /admin/users/{id}/block         ❌ Staff KHÔNG có quyền
POST   /admin/users/{id}/unblock       ❌ Staff KHÔNG có quyền
GET    /admin/users/{id}/progress      ✅ Staff có quyền (chỉ xem)
GET    /admin/users/{id}/saved-vocabularies  ✅ Staff có quyền (chỉ xem)
```

### Topics, Vocabularies, Stories
```
Tất cả endpoints cho Topics, Vocabularies, Stories: ✅ Staff có đầy đủ quyền
```

## Các methods trong User Model

```php
// Kiểm tra user có phải staff không
$user->isStaff()  // true nếu role = 'staff'

// Kiểm tra user có phải admin hoặc staff không
$user->isAdminOrStaff()  // true nếu role = 'admin', 'super_admin', hoặc 'staff'

// Kiểm tra user có phải admin không (không bao gồm staff)
$user->isAdmin()  // true nếu role = 'admin' hoặc 'super_admin'

// Kiểm tra user có phải super admin không
$user->isSuperAdmin()  // true nếu role = 'super_admin'
```

## Tạo user với role Staff

### Bước 1: Chạy migration
```bash
php artisan migrate
```

Migration sẽ thêm role 'staff' vào enum của cột `role` trong bảng `users`.

### Bước 2: Tạo user staff thông qua Admin API (bởi Admin hoặc Super Admin)
```bash
POST /admin/users
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "name": "Staff User",
  "email": "staff@example.com",
  "password": "password123",
  "role": "staff",
  "status": "active"
}
```

### Bước 3: Hoặc update user hiện tại thành staff
```bash
PUT /admin/users/{id}/role
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "role": "staff"
}
```

## Response khi Staff thực hiện hành động không được phép

Khi staff cố gắng thực hiện các hành động bị hạn chế (sửa/xóa user, block/unblock, etc.), API sẽ trả về:

```json
{
  "success": false,
  "message": "Staff không có quyền [hành động cụ thể]"
}
```

**Status Code:** 403 Forbidden

### Ví dụ:

#### Cố gắng cập nhật user:
```json
{
  "success": false,
  "message": "Staff không có quyền sửa thông tin user"
}
```

#### Cố gắng xóa user:
```json
{
  "success": false,
  "message": "Staff không có quyền xóa user"
}
```

#### Cố gắng block user:
```json
{
  "success": false,
  "message": "Staff không có quyền block user"
}
```

## Middleware

### AdminMiddleware
Middleware này cho phép các role sau truy cập admin dashboard:
- admin
- super_admin  
- staff

```php
// File: app/Http/Middleware/AdminMiddleware.php
if (!in_array($user->role, ['admin', 'super_admin', 'staff'])) {
    // Từ chối truy cập
}
```

## Kiểm tra quyền trong Controller

Các controller sẽ kiểm tra quyền staff trước khi thực hiện hành động:

```php
// Ví dụ trong UserController
public function update(Request $request, $id)
{
    // Kiểm tra nếu user là staff thì từ chối
    if (auth()->user()->isStaff()) {
        return response()->json([
            'success' => false,
            'message' => 'Staff không có quyền sửa thông tin user',
        ], 403);
    }
    
    // Tiếp tục xử lý cho admin/super_admin
    // ...
}
```

## Testing

### Test truy cập với role Staff:

```bash
# 1. Login với tài khoản staff
POST /api/auth/login
{
  "email": "staff@example.com",
  "password": "password123"
}

# 2. Lấy token từ response

# 3. Test các endpoints
GET /admin/users
Authorization: Bearer {staff_token}
# Kỳ vọng: 200 OK - Xem được danh sách

PUT /admin/users/1
Authorization: Bearer {staff_token}
{
  "name": "Updated Name"
}
# Kỳ vọng: 403 Forbidden - Không có quyền sửa
```

## Ghi chú

1. **Staff không được tạo user staff khác**: Chỉ Admin và Super Admin mới có thể tạo hoặc thay đổi role của user.

2. **Staff có đầy đủ quyền với Topics, Vocabularies, Stories**: Đây là thiết kế cố ý để staff có thể quản lý nội dung học tập.

3. **Logging**: Tất cả hành động của staff đều được ghi log tương tự như admin.

4. **Database**: Role 'staff' được lưu dưới dạng enum trong cột `role` của bảng `users`.

## Mở rộng

Nếu cần thêm quyền hạn hoặc hạn chế cho staff trong tương lai:

1. Thêm middleware riêng cho staff nếu cần
2. Cập nhật các controller methods với logic kiểm tra quyền
3. Cập nhật documentation này
