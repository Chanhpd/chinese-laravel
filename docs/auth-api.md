# Authentication API Documentation

## Overview

Authentication API sử dụng Laravel Sanctum để quản lý authentication với Bearer Token. API cung cấp các chức năng đăng ký, đăng nhập, đăng xuất và lấy thông tin user.

**Base URL**: `/api/auth`

**Authentication Method**: Bearer Token (Laravel Sanctum)

---

## Endpoints

### 1. Register New User

**Endpoint**: `POST /api/auth/register`

**Description**: Đăng ký tài khoản mới và tự động tạo access token.

**Authentication**: Not Required

**Headers**:
```
Content-Type: application/json
Accept: application/json
```

**Request Body**:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | Yes | User's full name (max: 255 characters) |
| `email` | string | Yes | Valid email address (must be unique) |
| `password` | string | Yes | Password (minimum 8 characters) |
| `password_confirmation` | string | Yes | Password confirmation (must match password) |

**Request Example**:
```bash
curl -X POST "http://localhost:8000/api/auth/register" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Response Example** (201 Created):
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "email_verified_at": null,
      "created_at": "2025-11-16T10:30:00.000000Z",
      "updated_at": "2025-11-16T10:30:00.000000Z"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz123456789"
  }
}
```

**Validation Error Response** (422 Unprocessable Entity):
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": [
      "The email has already been taken."
    ],
    "password": [
      "The password must be at least 8 characters.",
      "The password confirmation does not match."
    ]
  }
}
```

---

### 2. Login

**Endpoint**: `POST /api/auth/login`

**Description**: Đăng nhập và nhận access token. Token cũ sẽ bị xóa khi đăng nhập mới.

**Authentication**: Not Required

**Headers**:
```
Content-Type: application/json
Accept: application/json
```

**Request Body**:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `email` | string | Yes | Registered email address |
| `password` | string | Yes | User's password |

**Request Example**:
```bash
curl -X POST "http://localhost:8000/api/auth/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Response Example** (200 OK):
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "email_verified_at": null,
      "created_at": "2025-11-16T10:30:00.000000Z",
      "updated_at": "2025-11-16T10:30:00.000000Z"
    },
    "token": "2|xyz987654321abcdefghijklmnopqrstuvw"
  }
}
```

**Invalid Credentials Response** (401 Unauthorized):
```json
{
  "success": false,
  "message": "Invalid email or password"
}
```

**Validation Error Response** (422 Unprocessable Entity):
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": [
      "The email must be a valid email address."
    ],
    "password": [
      "The password field is required."
    ]
  }
}
```

---

### 3. Logout

**Endpoint**: `POST /api/auth/logout`

**Description**: Đăng xuất và xóa access token hiện tại.

**Authentication**: Required (Bearer Token)

**Headers**:
```
Authorization: Bearer {your_access_token}
Accept: application/json
```

**Request Example**:
```bash
curl -X POST "http://localhost:8000/api/auth/logout" \
  -H "Authorization: Bearer 2|xyz987654321abcdefghijklmnopqrstuvw" \
  -H "Accept: application/json"
```

**Response Example** (200 OK):
```json
{
  "success": true,
  "message": "Logout successful"
}
```

**Unauthenticated Response** (401 Unauthorized):
```json
{
  "message": "Unauthenticated."
}
```

---

### 4. Get Current User

**Endpoint**: `GET /api/auth/me`

**Description**: Lấy thông tin user hiện tại dựa trên token.

**Authentication**: Required (Bearer Token)

**Headers**:
```
Authorization: Bearer {your_access_token}
Accept: application/json
```

**Request Example**:
```bash
curl -X GET "http://localhost:8000/api/auth/me" \
  -H "Authorization: Bearer 2|xyz987654321abcdefghijklmnopqrstuvw" \
  -H "Accept: application/json"
```

**Response Example** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": null,
    "created_at": "2025-11-16T10:30:00.000000Z",
    "updated_at": "2025-11-16T10:30:00.000000Z"
  }
}
```

**Unauthenticated Response** (401 Unauthorized):
```json
{
  "message": "Unauthenticated."
}
```

---

### 5. Get User Info (Alternative)

**Endpoint**: `GET /api/user`

**Description**: Alternative endpoint để lấy thông tin user hiện tại (Laravel default).

**Authentication**: Required (Bearer Token)

**Headers**:
```
Authorization: Bearer {your_access_token}
Accept: application/json
```

**Request Example**:
```bash
curl -X GET "http://localhost:8000/api/user" \
  -H "Authorization: Bearer 2|xyz987654321abcdefghijklmnopqrstuvw" \
  -H "Accept: application/json"
```

**Response Example** (200 OK):
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "email_verified_at": null,
  "created_at": "2025-11-16T10:30:00.000000Z",
  "updated_at": "2025-11-16T10:30:00.000000Z"
}
```

---

## Authentication Flow

### Complete Authentication Flow

```
┌─────────────┐
│   Register  │
│  /register  │
└──────┬──────┘
       │
       ▼
┌─────────────────┐
│ Receive Token   │
│ Store in Client │
└──────┬──────────┘
       │
       ▼
┌─────────────────────┐
│ Use Token for API   │
│ Authorization:      │
│ Bearer {token}      │
└──────┬──────────────┘
       │
       ▼
┌─────────────┐
│   Logout    │
│  /logout    │
└─────────────┘
```

### Login Flow Alternative

```
┌─────────────┐
│    Login    │
│   /login    │
└──────┬──────┘
       │
       ▼
┌─────────────────┐
│ Receive Token   │
│ (Old tokens     │
│  are deleted)   │
└──────┬──────────┘
       │
       ▼
┌─────────────────────┐
│ Use Token for API   │
│ Authorization:      │
│ Bearer {token}      │
└─────────────────────┘
```

---

## Use Cases & Examples

### Use Case 1: User Registration and Auto-Login

```javascript
// Frontend - Register and store token
async function registerUser(name, email, password) {
  try {
    const response = await fetch('http://localhost:8000/api/auth/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        name: name,
        email: email,
        password: password,
        password_confirmation: password
      })
    });

    const data = await response.json();

    if (data.success) {
      // Store token in localStorage or secure storage
      localStorage.setItem('access_token', data.data.token);
      localStorage.setItem('user', JSON.stringify(data.data.user));
      
      console.log('Registration successful!');
      // Redirect to dashboard
      window.location.href = '/dashboard';
    } else {
      console.error('Registration failed:', data.errors);
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

// Usage
registerUser('John Doe', 'john@example.com', 'password123');
```

---

### Use Case 2: User Login

```javascript
// Frontend - Login
async function loginUser(email, password) {
  try {
    const response = await fetch('http://localhost:8000/api/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: email,
        password: password
      })
    });

    const data = await response.json();

    if (response.ok && data.success) {
      // Store token
      localStorage.setItem('access_token', data.data.token);
      localStorage.setItem('user', JSON.stringify(data.data.user));
      
      console.log('Login successful!');
      return data.data;
    } else {
      alert(data.message || 'Login failed');
      return null;
    }
  } catch (error) {
    console.error('Error:', error);
    alert('An error occurred during login');
    return null;
  }
}

// Usage
loginUser('john@example.com', 'password123');
```

---

### Use Case 3: Making Authenticated Requests

```javascript
// Frontend - Make authenticated API call
async function getProtectedData() {
  const token = localStorage.getItem('access_token');
  
  if (!token) {
    console.error('No token found. Please login.');
    window.location.href = '/login';
    return;
  }

  try {
    const response = await fetch('http://localhost:8000/api/stories', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    if (response.status === 401) {
      // Token expired or invalid
      localStorage.removeItem('access_token');
      localStorage.removeItem('user');
      window.location.href = '/login';
      return;
    }

    const data = await response.json();
    console.log('Stories:', data);
    return data;
  } catch (error) {
    console.error('Error:', error);
  }
}
```

---

### Use Case 4: User Logout

```javascript
// Frontend - Logout
async function logoutUser() {
  const token = localStorage.getItem('access_token');
  
  if (!token) {
    console.error('No token found');
    return;
  }

  try {
    const response = await fetch('http://localhost:8000/api/auth/logout', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    const data = await response.json();

    if (data.success) {
      // Clear local storage
      localStorage.removeItem('access_token');
      localStorage.removeItem('user');
      
      console.log('Logout successful!');
      // Redirect to login page
      window.location.href = '/login';
    }
  } catch (error) {
    console.error('Error:', error);
    // Still clear local data even if API call fails
    localStorage.removeItem('access_token');
    localStorage.removeItem('user');
    window.location.href = '/login';
  }
}

// Usage
logoutUser();
```

---

### Use Case 5: Get Current User Info

```javascript
// Frontend - Get current user
async function getCurrentUser() {
  const token = localStorage.getItem('access_token');
  
  if (!token) {
    return null;
  }

  try {
    const response = await fetch('http://localhost:8000/api/auth/me', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    if (response.ok) {
      const data = await response.json();
      return data.data;
    } else {
      // Token invalid
      localStorage.removeItem('access_token');
      localStorage.removeItem('user');
      return null;
    }
  } catch (error) {
    console.error('Error:', error);
    return null;
  }
}

// Usage - Check if user is logged in
async function checkAuth() {
  const user = await getCurrentUser();
  
  if (user) {
    console.log('Logged in as:', user.name);
    // Update UI with user info
    document.getElementById('userName').textContent = user.name;
  } else {
    console.log('Not logged in');
    window.location.href = '/login';
  }
}

checkAuth();
```

---

### Use Case 6: React/Vue Integration Example

```javascript
// React - Auth Context
import { createContext, useState, useEffect } from 'react';

export const AuthContext = createContext();

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(localStorage.getItem('access_token'));
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (token) {
      // Verify token and get user
      fetchCurrentUser();
    } else {
      setLoading(false);
    }
  }, [token]);

  const fetchCurrentUser = async () => {
    try {
      const response = await fetch('http://localhost:8000/api/auth/me', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });

      if (response.ok) {
        const data = await response.json();
        setUser(data.data);
      } else {
        logout();
      }
    } catch (error) {
      console.error('Error fetching user:', error);
      logout();
    } finally {
      setLoading(false);
    }
  };

  const login = async (email, password) => {
    const response = await fetch('http://localhost:8000/api/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ email, password })
    });

    const data = await response.json();

    if (data.success) {
      setToken(data.data.token);
      setUser(data.data.user);
      localStorage.setItem('access_token', data.data.token);
      return { success: true };
    } else {
      return { success: false, message: data.message };
    }
  };

  const register = async (name, email, password) => {
    const response = await fetch('http://localhost:8000/api/auth/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        name,
        email,
        password,
        password_confirmation: password
      })
    });

    const data = await response.json();

    if (data.success) {
      setToken(data.data.token);
      setUser(data.data.user);
      localStorage.setItem('access_token', data.data.token);
      return { success: true };
    } else {
      return { success: false, errors: data.errors };
    }
  };

  const logout = async () => {
    if (token) {
      await fetch('http://localhost:8000/api/auth/logout', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });
    }

    setToken(null);
    setUser(null);
    localStorage.removeItem('access_token');
  };

  return (
    <AuthContext.Provider value={{ user, token, login, register, logout, loading }}>
      {children}
    </AuthContext.Provider>
  );
}
```

---

## Error Responses

### 401 Unauthorized
**Scenario**: Token missing, invalid, or expired
```json
{
  "message": "Unauthenticated."
}
```

### 422 Validation Error
**Scenario**: Invalid input data
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password must be at least 8 characters."
    ]
  }
}
```

### 500 Internal Server Error
**Scenario**: Server error
```json
{
  "success": false,
  "message": "An error occurred",
  "error": "Detailed error message"
}
```

---

## Security Best Practices

### 1. Token Storage
- **Web**: Store token in `httpOnly` cookie (most secure) or `localStorage`
- **Mobile**: Use secure storage (Keychain for iOS, KeyStore for Android)
- **Never** store token in regular cookies without `httpOnly` flag

### 2. Token Transmission
- Always use HTTPS in production
- Send token in `Authorization: Bearer {token}` header
- Never send token in URL query parameters

### 3. Token Lifecycle
- Tokens are deleted on logout
- Old tokens are automatically deleted on new login
- Implement token refresh if needed for longer sessions

### 4. Password Requirements
- Minimum 8 characters
- Consider adding complexity requirements (uppercase, numbers, symbols)
- Use password confirmation on registration

### 5. CORS Configuration
Ensure your backend allows requests from your frontend domain:

```php
// config/cors.php
'paths' => ['api/*'],
'allowed_origins' => ['http://localhost:3000', 'https://yourdomain.com'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

### 6. Rate Limiting
Laravel Sanctum includes rate limiting by default:
- 60 requests per minute for authenticated users
- Can be configured in `app/Http/Kernel.php`

---

## Testing Examples

### Using cURL

```bash
# 1. Register
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'

# 2. Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'

# 3. Get user info (replace TOKEN)
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer TOKEN"

# 4. Logout
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer TOKEN"
```

### Using Postman

1. **Register/Login**:
   - Method: POST
   - URL: `http://localhost:8000/api/auth/login`
   - Headers: `Content-Type: application/json`
   - Body (raw JSON):
     ```json
     {
       "email": "test@example.com",
       "password": "password123"
     }
     ```

2. **Copy the token from response**

3. **Make authenticated requests**:
   - Headers: `Authorization: Bearer {paste_token_here}`

---

## Notes

1. **Token Format**: Sanctum tokens are in format `{token_id}|{plain_text_token}`
2. **Token Expiration**: By default, tokens don't expire. Configure in `config/sanctum.php` if needed
3. **Multiple Devices**: Each login creates a new token. Old tokens are deleted by default in this implementation
4. **User Model**: Uses default Laravel User model with Sanctum's `HasApiTokens` trait

---

## Changelog

### Version 1.0.0 (2025-11-16)
- Initial release
- Register endpoint with automatic token generation
- Login endpoint with old token deletion
- Logout endpoint
- Get current user endpoint
- English error messages
- Bearer token authentication
