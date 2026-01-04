# Password Management API

API endpoints để quản lý mật khẩu người dùng - bao gồm quên mật khẩu và đổi mật khẩu.

## Base URL
```
http://your-domain.com/api
```

## Authentication
- **Forgot Password** và **Reset Password**: Không cần authentication
- **Change Password**: Cần Bearer Token (auth:sanctum)

---

## 1. Forgot Password (Quên mật khẩu)

Gửi yêu cầu reset mật khẩu. Hệ thống sẽ tạo token và gửi qua email (trong production).

### Endpoint
```
POST /auth/forgot-password
```

### Request Body
```json
{
  "email": "user@example.com"
}
```

### Response - Success (200)
```json
{
  "success": true,
  "message": "Password reset link sent to your email",
  "data": {
    "token": "abc123xyz..." // Chỉ trả về khi testing, production sẽ gửi qua email
  }
}
```

### Response - Email không tồn tại (422)
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "email": [
      "The selected email is invalid."
    ]
  }
}
```

### cURL Example
```bash
curl -X POST http://your-domain.com/api/auth/forgot-password \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com"
  }'
```

### Flutter Example
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

Future<Map<String, dynamic>> forgotPassword(String email) async {
  final url = Uri.parse('http://your-domain.com/api/auth/forgot-password');
  
  try {
    final response = await http.post(
      url,
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'email': email}),
    );

    final data = jsonDecode(response.body);
    
    if (response.statusCode == 200) {
      // Thành công - Hiển thị thông báo cho user check email
      print('Reset link sent: ${data['message']}');
      return data;
    } else {
      // Lỗi validation
      print('Error: ${data['message']}');
      return data;
    }
  } catch (e) {
    print('Network error: $e');
    return {'success': false, 'message': 'Network error'};
  }
}

// Sử dụng
void main() async {
  await forgotPassword('user@example.com');
}
```

---

## 2. Reset Password (Đặt lại mật khẩu với token)

Đặt lại mật khẩu mới sử dụng token nhận được từ email.

### Endpoint
```
POST /auth/reset-password
```

### Request Body
```json
{
  "email": "user@example.com",
  "token": "abc123xyz...",
  "password": "newPassword123",
  "password_confirmation": "newPassword123"
}
```

### Response - Success (200)
```json
{
  "success": true,
  "message": "Password has been reset successfully"
}
```

### Response - Token không hợp lệ (400)
```json
{
  "success": false,
  "message": "Invalid reset token"
}
```

### Response - Token đã hết hạn (400)
```json
{
  "success": false,
  "message": "Reset token has expired"
}
```

### Response - Validation error (422)
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "password": [
      "The password confirmation does not match."
    ]
  }
}
```

### cURL Example
```bash
curl -X POST http://your-domain.com/api/auth/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "token": "abc123xyz...",
    "password": "newPassword123",
    "password_confirmation": "newPassword123"
  }'
```

### Flutter Example
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

Future<Map<String, dynamic>> resetPassword({
  required String email,
  required String token,
  required String newPassword,
}) async {
  final url = Uri.parse('http://your-domain.com/api/auth/reset-password');
  
  try {
    final response = await http.post(
      url,
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'email': email,
        'token': token,
        'password': newPassword,
        'password_confirmation': newPassword,
      }),
    );

    final data = jsonDecode(response.body);
    
    if (response.statusCode == 200) {
      // Thành công - Chuyển về màn hình login
      print('Password reset successfully');
      return data;
    } else {
      // Lỗi - Hiển thị thông báo
      print('Error: ${data['message']}');
      return data;
    }
  } catch (e) {
    print('Network error: $e');
    return {'success': false, 'message': 'Network error'};
  }
}

// Sử dụng
void main() async {
  await resetPassword(
    email: 'user@example.com',
    token: 'abc123xyz...',
    newPassword: 'newPassword123',
  );
}
```

---

## 3. Change Password (Đổi mật khẩu) 🔒

Đổi mật khẩu cho user đã đăng nhập. Yêu cầu mật khẩu hiện tại để xác thực.

### Endpoint
```
POST /auth/change-password
```

### Headers Required
```
Authorization: Bearer <access_token>
Content-Type: application/json
```

### Request Body
```json
{
  "current_password": "oldPassword123",
  "new_password": "newPassword456",
  "new_password_confirmation": "newPassword456"
}
```

### Response - Success (200)
```json
{
  "success": true,
  "message": "Password changed successfully"
}
```

### Response - Mật khẩu hiện tại sai (400)
```json
{
  "success": false,
  "message": "Current password is incorrect"
}
```

### Response - Validation error (422)
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "new_password": [
      "The new password must be at least 6 characters.",
      "The new password confirmation does not match."
    ]
  }
}
```

### Response - Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

### cURL Example
```bash
curl -X POST http://your-domain.com/api/auth/change-password \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "oldPassword123",
    "new_password": "newPassword456",
    "new_password_confirmation": "newPassword456"
  }'
```

### Flutter Example
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

Future<Map<String, dynamic>> changePassword({
  required String currentPassword,
  required String newPassword,
}) async {
  final prefs = await SharedPreferences.getInstance();
  final token = prefs.getString('access_token') ?? '';
  
  if (token.isEmpty) {
    return {'success': false, 'message': 'User not authenticated'};
  }

  final url = Uri.parse('http://your-domain.com/api/auth/change-password');
  
  try {
    final response = await http.post(
      url,
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode({
        'current_password': currentPassword,
        'new_password': newPassword,
        'new_password_confirmation': newPassword,
      }),
    );

    final data = jsonDecode(response.body);
    
    if (response.statusCode == 200) {
      // Thành công
      print('Password changed successfully');
      return data;
    } else if (response.statusCode == 400) {
      // Mật khẩu hiện tại sai
      print('Wrong current password');
      return data;
    } else if (response.statusCode == 401) {
      // Token hết hạn - Yêu cầu login lại
      print('Session expired, please login again');
      return {'success': false, 'message': 'Session expired'};
    } else {
      // Lỗi khác
      print('Error: ${data['message']}');
      return data;
    }
  } catch (e) {
    print('Network error: $e');
    return {'success': false, 'message': 'Network error'};
  }
}

// Sử dụng
void main() async {
  await changePassword(
    currentPassword: 'oldPassword123',
    newPassword: 'newPassword456',
  );
}
```

---

## Complete Flutter Implementation

### Password Service Class

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

class PasswordService {
  static const String baseUrl = 'http://your-domain.com/api';

  // 1. Forgot Password
  static Future<Map<String, dynamic>> forgotPassword(String email) async {
    final url = Uri.parse('$baseUrl/auth/forgot-password');
    
    try {
      final response = await http.post(
        url,
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'email': email}),
      );

      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }

  // 2. Reset Password
  static Future<Map<String, dynamic>> resetPassword({
    required String email,
    required String token,
    required String newPassword,
  }) async {
    final url = Uri.parse('$baseUrl/auth/reset-password');
    
    try {
      final response = await http.post(
        url,
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'email': email,
          'token': token,
          'password': newPassword,
          'password_confirmation': newPassword,
        }),
      );

      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }

  // 3. Change Password
  static Future<Map<String, dynamic>> changePassword({
    required String currentPassword,
    required String newPassword,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('access_token') ?? '';
    
    if (token.isEmpty) {
      return {'success': false, 'message': 'User not authenticated'};
    }

    final url = Uri.parse('$baseUrl/auth/change-password');
    
    try {
      final response = await http.post(
        url,
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode({
          'current_password': currentPassword,
          'new_password': newPassword,
          'new_password_confirmation': newPassword,
        }),
      );

      return jsonDecode(response.body);
    } catch (e) {
      return {'success': false, 'message': 'Network error: $e'};
    }
  }
}
```

### UI Example - Forgot Password Screen

```dart
import 'package:flutter/material.dart';

class ForgotPasswordScreen extends StatefulWidget {
  @override
  _ForgotPasswordScreenState createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends State<ForgotPasswordScreen> {
  final _emailController = TextEditingController();
  bool _isLoading = false;

  Future<void> _handleForgotPassword() async {
    final email = _emailController.text.trim();
    
    if (email.isEmpty) {
      _showError('Please enter your email');
      return;
    }

    setState(() => _isLoading = true);

    final result = await PasswordService.forgotPassword(email);

    setState(() => _isLoading = false);

    if (result['success'] == true) {
      _showSuccess('Reset link sent to your email');
      // Chuyển sang màn hình nhập token
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => ResetPasswordScreen(email: email),
        ),
      );
    } else {
      _showError(result['message'] ?? 'Failed to send reset link');
    }
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.red),
    );
  }

  void _showSuccess(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.green),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Forgot Password')),
      body: Padding(
        padding: EdgeInsets.all(16.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            TextField(
              controller: _emailController,
              decoration: InputDecoration(
                labelText: 'Email',
                border: OutlineInputBorder(),
              ),
              keyboardType: TextInputType.emailAddress,
            ),
            SizedBox(height: 20),
            ElevatedButton(
              onPressed: _isLoading ? null : _handleForgotPassword,
              child: _isLoading
                  ? CircularProgressIndicator(color: Colors.white)
                  : Text('Send Reset Link'),
              style: ElevatedButton.styleFrom(
                minimumSize: Size(double.infinity, 50),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
```

### UI Example - Change Password Screen

```dart
import 'package:flutter/material.dart';

class ChangePasswordScreen extends StatefulWidget {
  @override
  _ChangePasswordScreenState createState() => _ChangePasswordScreenState();
}

class _ChangePasswordScreenState extends State<ChangePasswordScreen> {
  final _currentPasswordController = TextEditingController();
  final _newPasswordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  bool _isLoading = false;

  Future<void> _handleChangePassword() async {
    final currentPassword = _currentPasswordController.text;
    final newPassword = _newPasswordController.text;
    final confirmPassword = _confirmPasswordController.text;

    if (currentPassword.isEmpty || newPassword.isEmpty) {
      _showError('Please fill all fields');
      return;
    }

    if (newPassword != confirmPassword) {
      _showError('New passwords do not match');
      return;
    }

    if (newPassword.length < 6) {
      _showError('Password must be at least 6 characters');
      return;
    }

    setState(() => _isLoading = true);

    final result = await PasswordService.changePassword(
      currentPassword: currentPassword,
      newPassword: newPassword,
    );

    setState(() => _isLoading = false);

    if (result['success'] == true) {
      _showSuccess('Password changed successfully');
      Navigator.pop(context); // Quay lại màn hình trước
    } else {
      _showError(result['message'] ?? 'Failed to change password');
    }
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.red),
    );
  }

  void _showSuccess(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.green),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Change Password')),
      body: Padding(
        padding: EdgeInsets.all(16.0),
        child: Column(
          children: [
            TextField(
              controller: _currentPasswordController,
              decoration: InputDecoration(
                labelText: 'Current Password',
                border: OutlineInputBorder(),
              ),
              obscureText: true,
            ),
            SizedBox(height: 16),
            TextField(
              controller: _newPasswordController,
              decoration: InputDecoration(
                labelText: 'New Password',
                border: OutlineInputBorder(),
              ),
              obscureText: true,
            ),
            SizedBox(height: 16),
            TextField(
              controller: _confirmPasswordController,
              decoration: InputDecoration(
                labelText: 'Confirm New Password',
                border: OutlineInputBorder(),
              ),
              obscureText: true,
            ),
            SizedBox(height: 20),
            ElevatedButton(
              onPressed: _isLoading ? null : _handleChangePassword,
              child: _isLoading
                  ? CircularProgressIndicator(color: Colors.white)
                  : Text('Change Password'),
              style: ElevatedButton.styleFrom(
                minimumSize: Size(double.infinity, 50),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
```

---

## Validation Rules

### Forgot Password
- `email`: required, must be valid email format, must exist in database

### Reset Password
- `email`: required, must be valid email format, must exist in database
- `token`: required, must be valid reset token
- `password`: required, minimum 6 characters
- `password_confirmation`: must match `password`
- Token expires after 60 minutes

### Change Password
- `current_password`: required
- `new_password`: required, minimum 6 characters
- `new_password_confirmation`: must match `new_password`
- User must be authenticated (Bearer Token)

---

## Error Codes

| Status Code | Description |
|-------------|-------------|
| 200 | Success |
| 400 | Bad Request (invalid token, wrong password) |
| 401 | Unauthorized (missing or invalid Bearer Token) |
| 422 | Validation Error |
| 500 | Server Error |

---

## Security Notes

1. **Token Expiration**: Reset tokens expire after 60 minutes
2. **Token Storage**: Tokens are hashed in database using Laravel Hash
3. **Old Tokens**: Previous reset tokens are automatically deleted when requesting new one
4. **Email Verification**: Email must exist in database before sending reset link
5. **Production**: Remove `token` from forgot-password response and send via email instead
6. **HTTPS**: Always use HTTPS in production for password-related endpoints
7. **Rate Limiting**: Consider adding rate limiting to prevent brute force attacks

---

## Testing Flow

### 1. Test Forgot Password
```bash
# Step 1: Request reset token
curl -X POST http://your-domain.com/api/auth/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com"}'

# Response will contain token (only in testing)
# Save this token for next step
```

### 2. Test Reset Password
```bash
# Step 2: Reset with token
curl -X POST http://your-domain.com/api/auth/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "token": "TOKEN_FROM_STEP_1",
    "password": "newPassword123",
    "password_confirmation": "newPassword123"
  }'
```

### 3. Test Change Password
```bash
# Step 3: Login to get token
curl -X POST http://your-domain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "newPassword123"
  }'

# Step 4: Change password with Bearer Token
curl -X POST http://your-domain.com/api/auth/change-password \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "newPassword123",
    "new_password": "newPassword456",
    "new_password_confirmation": "newPassword456"
  }'
```

---

## Production Checklist

- [ ] Configure mail settings in `.env`:
  ```
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.gmail.com
  MAIL_PORT=587
  MAIL_USERNAME=your-email@gmail.com
  MAIL_PASSWORD=your-app-password
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS=noreply@yourdomain.com
  MAIL_FROM_NAME="${APP_NAME}"
  ```
- [ ] Create email template for password reset
- [ ] Remove `token` from forgot-password response
- [ ] Implement email sending in `forgotPassword` method
- [ ] Add rate limiting middleware
- [ ] Test email delivery
- [ ] Use HTTPS in production
- [ ] Add CORS configuration if needed

---

## Support

Nếu cần hỗ trợ hoặc có câu hỏi về API, vui lòng liên hệ team phát triển.
