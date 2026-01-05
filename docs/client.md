# Client Web Application - Chinese Learning App

## Tổng quan

Client Web là ứng dụng web đơn giản dành cho người dùng (user) để học tiếng Trung. Ứng dụng được xây dựng với HTML, CSS, và JavaScript thuần, tích hợp với Laravel API backend.

## Cấu trúc thư mục

```
public/client/
├── index.html              # Trang landing page
├── login.html              # Trang đăng nhập
├── register.html           # Trang đăng ký
├── home.html               # Trang chủ sau khi đăng nhập
├── css/
│   └── style.css          # File CSS chung
└── js/
    └── auth.js            # Logic authentication
```

## Tính năng đã triển khai

### 1. Authentication System

#### Đăng ký (Register)
- **URL**: `/client/register.html`
- **Chức năng**: 
  - Form đăng ký với validation
  - Tích hợp API `/api/auth/register`
  - Tự động lưu token và chuyển hướng sau khi đăng ký thành công
- **Fields**: name, email, password, password_confirmation

#### Đăng nhập (Login)
- **URL**: `/client/login.html`
- **Chức năng**:
  - Form đăng nhập với validation
  - Tích hợp API `/api/auth/login`
  - Tự động lưu token và chuyển hướng sau khi đăng nhập thành công
- **Fields**: email, password

#### Trang chủ (Home)
- **URL**: `/client/home.html`
- **Chức năng**:
  - Hiển thị thông tin user
  - Avatar với chữ cái đầu của tên
  - Chức năng đăng xuất
  - Preview các tính năng sẽ phát triển
- **Bảo vệ**: Yêu cầu đăng nhập (redirect nếu chưa login)

## API Integration

### Base URL
```javascript
const API_BASE_URL = 'http://localhost:8000/api';
```

### Authentication Flow

1. **Register/Login**:
   ```javascript
   POST /api/auth/register
   POST /api/auth/login
   
   Response: {
     success: true,
     data: {
       user: {...},
       token: "..."
     }
   }
   ```

2. **Token Storage**:
   - Token được lưu trong `localStorage` với key `auth_token`
   - User data được lưu trong `localStorage` với key `user_data`

3. **Authenticated Requests**:
   ```javascript
   headers: {
     'Authorization': 'Bearer {token}',
     'Content-Type': 'application/json',
     'Accept': 'application/json'
   }
   ```

4. **Logout**:
   ```javascript
   POST /api/auth/logout
   // Clear localStorage và redirect về login
   ```

## Sử dụng

### 1. Khởi chạy Laravel Backend

```bash
# Start Laravel development server
php artisan serve
```

Server sẽ chạy tại: `http://localhost:8000`

### 2. Truy cập Client Web

Mở trình duyệt và truy cập:

```
http://localhost:8000/client/index.html
http://localhost:8000/client/login.html
http://localhost:8000/client/register.html
http://localhost:8000/client/home.html
```

### 3. Flow sử dụng

1. Truy cập `/client/index.html`
2. Click "Đăng ký tài khoản" để tạo account mới
3. Sau khi đăng ký thành công, tự động chuyển đến `/client/home.html`
4. Hoặc click "Đăng nhập" nếu đã có tài khoản

## JavaScript API Helper

File `js/auth.js` cung cấp các helper functions:

### API Methods
```javascript
// Login
await auth.api.login(email, password);

// Register
await auth.api.register(name, email, password, password_confirmation);

// Logout
await auth.api.logout();

// Get current user
await auth.api.getCurrentUser();

// Check authentication
auth.api.isAuthenticated();

// Get stored user data
auth.api.getUser();
```

### UI Methods
```javascript
// Show loading on button
auth.ui.showButtonLoading(button);

// Hide loading on button
auth.ui.hideButtonLoading(button);

// Show alert message
auth.ui.showAlert(message, 'success' | 'danger');

// Show field error
auth.ui.showFieldError(fieldId, message);

// Clear errors
auth.ui.clearAllErrors();

// Handle validation errors from API
auth.ui.handleValidationErrors(errors);
```

### Route Guards
```javascript
// Require authentication (redirect to login if not authenticated)
auth.requireAuth();

// Require guest (redirect to home if already authenticated)
auth.requireGuest();
```

## Styling

### CSS Variables
```css
--primary-color: #4F46E5
--primary-dark: #4338CA
--secondary-color: #10B981
--danger-color: #EF4444
--text-color: #1F2937
--bg-light: #F9FAFB
```

### Responsive Design
- Mobile-first approach
- Breakpoint: 768px
- Flexbox và Grid layout

## Error Handling

### Validation Errors (422)
```javascript
{
  "success": false,
  "errors": {
    "email": ["Email đã được sử dụng"],
    "password": ["Mật khẩu phải ít nhất 8 ký tự"]
  }
}
```

### Authentication Errors (401)
```javascript
{
  "success": false,
  "message": "Email hoặc mật khẩu không đúng"
}
```

## Security Features

1. **Bearer Token Authentication**: Sử dụng Laravel Sanctum
2. **CORS Configuration**: Configured trong Laravel backend
3. **XSS Protection**: Input sanitization
4. **Password Requirements**: Minimum 8 characters
5. **Token Rotation**: Old tokens revoked on new login

## Tính năng sẽ phát triển

- [ ] Học từ vựng theo chủ đề
- [ ] Luyện viết chữ Hán
- [ ] Bài kiểm tra trình độ
- [ ] Chatbot AI hỗ trợ học
- [ ] Theo dõi tiến độ học tập
- [ ] Hệ thống streak và rewards
- [ ] Profile management
- [ ] Password reset
- [ ] Email verification

## Troubleshooting

### CORS Issues
Nếu gặp lỗi CORS, kiểm tra file `config/cors.php`:
```php
'paths' => ['api/*'],
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
```

### Token Not Working
1. Kiểm tra token trong localStorage
2. Verify token format: `Bearer {token}`
3. Check token expiration

### API Connection Failed
1. Verify Laravel server đang chạy: `php artisan serve`
2. Check API_BASE_URL trong `js/auth.js`
3. Test API endpoint với Postman

## Development Notes

### Local Storage
```javascript
// Stored data
localStorage.setItem('auth_token', token);
localStorage.setItem('user_data', JSON.stringify(user));

// Clear on logout
localStorage.removeItem('auth_token');
localStorage.removeItem('user_data');
```

### API Response Format
```javascript
{
  "success": true/false,
  "message": "...",
  "data": {...},
  "errors": {...} // Only on validation errors
}
```

## Contact & Support

Để biết thêm thông tin về API endpoints, xem:
- [API Documentation](api.md)
- [Auth API Documentation](auth-api.md) 
