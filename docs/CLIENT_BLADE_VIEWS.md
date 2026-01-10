# Client Views - Laravel Blade Templates

## Cấu trúc mới

Các file HTML tĩnh đã được chuyển đổi sang Blade templates theo chuẩn Laravel:

### Views Location
```
resources/views/
├── layouts/
│   └── app.blade.php          # Layout chung cho toàn bộ client pages
└── client/
    ├── index.blade.php         # Trang chủ (chưa đăng nhập)
    ├── login.blade.php         # Trang đăng nhập
    ├── register.blade.php      # Trang đăng ký
    └── home.blade.php          # Trang home (sau khi đăng nhập)
```

### Routes

```php
// Client routes - tất cả có prefix 'client'
client/                 -> ClientController@index        (Trang chủ)
client/login           -> ClientController@showLoginForm  (Form đăng nhập)
client/register        -> ClientController@showRegisterForm (Form đăng ký)
client/home            -> ClientController@home           (Trang home - yêu cầu auth)
client/logout (POST)   -> ClientController@logout         (Đăng xuất)
```

### Controller

**App\Http\Controllers\Client\ClientController**
- `index()` - Hiển thị trang chủ (guest only)
- `showLoginForm()` - Hiển thị form đăng nhập (guest only)
- `showRegisterForm()` - Hiển thị form đăng ký (guest only)
- `home()` - Hiển thị trang home (authenticated only)
- `logout()` - Xử lý đăng xuất

### Middleware

- **Guest routes**: Chỉ cho phép người chưa đăng nhập truy cập (index, login, register)
- **Auth routes**: Chỉ cho phép người đã đăng nhập truy cập (home, logout)
- Tự động redirect nếu trạng thái authentication không phù hợp

### Blade Features được sử dụng

1. **Layout System**
   - `@extends('layouts.app')` - Kế thừa layout chung
   - `@section('content')` - Định nghĩa nội dung chính
   - `@section('title')` - Định nghĩa title cho từng trang

2. **Asset Management**
   - `{{ asset('client/css/style.css') }}` - Load CSS
   - `{{ asset('client/js/auth.js') }}` - Load JavaScript

3. **CSRF Protection**
   - `@csrf` - Token bảo mật cho forms
   - `<meta name="csrf-token">` - Token cho AJAX requests

4. **Session Flash Messages**
   - `@if(session('success'))` - Hiển thị thông báo thành công
   - `@if($errors->any())` - Hiển thị validation errors

5. **Old Input**
   - `{{ old('email') }}` - Giữ lại input khi validation fail

6. **Route Helpers**
   - `{{ route('client.login') }}` - Generate URL từ route name
   - `{{ route('client.register') }}` - URL-safe và maintainable

7. **Auth Helpers**
   - `{{ auth()->user()->name }}` - Lấy thông tin user đã đăng nhập
   - `@auth` / `@guest` - Điều kiện hiển thị theo trạng thái

8. **Scripts/Styles Stack**
   - `@stack('scripts')` - Cho phép thêm scripts từ child views
   - `@push('scripts')` - Push scripts vào stack

## Migration từ HTML tĩnh

### Before (HTML tĩnh)
```html
<link rel="stylesheet" href="css/style.css">
<a href="login.html">Đăng nhập</a>
```

### After (Blade template)
```blade
<link rel="stylesheet" href="{{ asset('client/css/style.css') }}">
<a href="{{ route('client.login') }}">Đăng nhập</a>
```

## Lợi ích

✅ **Bảo mật**: CSRF protection, validation tích hợp
✅ **Maintainability**: Code reuse với layouts
✅ **SEO Friendly**: Server-side rendering
✅ **Laravel Integration**: Session, Auth, Validation
✅ **Clean URLs**: Route names thay vì hardcoded paths
✅ **Error Handling**: Blade directives cho error messages
✅ **Type Safety**: IDE autocomplete với Blade

## Truy cập

- Trang chủ client: `http://localhost:8000/client`
- Đăng nhập: `http://localhost:8000/client/login`
- Đăng ký: `http://localhost:8000/client/register`
- Home (sau login): `http://localhost:8000/client/home`

## Notes

- JavaScript authentication logic vẫn được giữ nguyên trong `public/client/js/auth.js`
- CSS styling vẫn sử dụng file `public/client/css/style.css`
- Server-side validation có thể được thêm vào Controller
- API authentication vẫn hoạt động như cũ (API routes riêng biệt)
