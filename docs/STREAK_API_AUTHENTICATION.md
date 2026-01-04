# 🔐 Streak API Authentication Guide

## So sánh Basic Auth vs Bearer Token cho Check-in API

### 📊 Bảng So Sánh

| Tiêu chí | Basic Auth (username:password) | Bearer Token (JWT/OAuth) |
|----------|-------------------------------|--------------------------|
| **Bảo mật** | ⚠️ Thấp - credentials gửi mỗi request | ✅ Cao - token có expire time |
| **Hiệu suất** | ⚠️ Chậm hơn (hash/verify mỗi lần) | ✅ Nhanh - verify signature only |
| **Scalability** | ❌ Khó scale (cần DB lookup) | ✅ Stateless, dễ scale |
| **Expire/Revoke** | ❌ Phải đổi password | ✅ Token tự động expire |
| **Best Practice** | ❌ Không khuyến nghị | ✅ **Recommended** ⭐ |

---

## ✅ **RECOMMENDED: Bearer Token (JWT)**

### 🎯 Tại sao nên dùng Bearer Token?

1. **Bảo mật cao hơn**
   - Không gửi password trực tiếp
   - Token có thời gian sống (exp claim)
   - Có thể revoke token mà không cần đổi password
   - Hỗ trợ refresh token

2. **Hiệu suất tốt hơn**
   - Stateless - không cần query DB mỗi request
   - Server chỉ verify signature
   - Giảm tải cho database

3. **Dễ scale**
   - Microservices có thể verify token độc lập
   - Không cần shared session store

4. **Linh hoạt**
   - Chứa thêm thông tin trong payload (userId, roles, etc.)
   - Có thể set expire time khác nhau cho từng use case

---

## 🔥 Implementation: Bearer Token cho Streak API

### 1. **Login Flow - Lấy Token**

```dart
// Request
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}

// Response
{
  "success": true,
  "data": {
    "accessToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "refreshToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "expiresIn": 3600, // 1 hour
    "user": {
      "id": "user123",
      "name": "John Doe",
      "email": "user@example.com"
    }
  }
}
```

### 2. **Check-in API với Bearer Token**

```dart
// Request
POST /api/user/streak/check-in
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
Content-Type: application/json

{
  "checkInDate": "2024-12-17",
  "timestamp": "2024-12-17T14:30:00Z"
}

// Response
{
  "success": true,
  "message": "Check-in recorded successfully",
  "data": {
    "streakCount": 15,
    "lastCheckInDate": "2024-12-17",
    "weeklyCheckIns": [...],
    "totalCheckInDays": 120
  }
}
```

### 3. **Get Streak Data với Bearer Token**

```dart
// Request
GET /api/user/streak
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...

// Response
{
  "success": true,
  "data": {
    "streakCount": 15,
    "lastCheckInDate": "2024-12-17",
    "weeklyCheckIns": [...],
    "totalCheckInDays": 120
  }
}
```

---

## 💻 Flutter Implementation

### Service Class

```dart
// lib/service/streak_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/streak_data.dart';
import 'storage_service.dart';

class StreakService {
  final String baseUrl = 'https://your-api.com/api';
  final StorageService _storageService;

  StreakService(this._storageService);

  /// Get authorization headers with Bearer token
  Map<String, String> _getAuthHeaders() {
    final token = _storageService.getToken();
    if (token == null) {
      throw Exception('No authentication token found');
    }
    
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    };
  }

  /// Perform daily check-in
  Future<StreakData> performCheckIn() async {
    try {
      final now = DateTime.now();
      final response = await http.post(
        Uri.parse('$baseUrl/user/streak/check-in'),
        headers: _getAuthHeaders(),
        body: jsonEncode({
          'checkInDate': _formatDate(now),
          'timestamp': now.toIso8601String(),
        }),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          final streakData = StreakData.fromJson(data['data']);
          
          // Save to local storage as backup
          await _storageService.saveStreakData(streakData);
          
          return streakData;
        } else {
          throw Exception(data['message'] ?? 'Check-in failed');
        }
      } else if (response.statusCode == 401) {
        // Token expired - need to refresh or re-login
        throw Exception('Authentication expired. Please login again.');
      } else {
        throw Exception('Server error: ${response.statusCode}');
      }
    } catch (e) {
      // Fallback to local check-in if offline
      print('Check-in API failed: $e. Using local storage.');
      final localStreak = _storageService.getStreakData();
      final updatedStreak = localStreak.checkIn();
      await _storageService.saveStreakData(updatedStreak);
      return updatedStreak;
    }
  }

  /// Get current streak data from server
  Future<StreakData> getStreakData() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/user/streak'),
        headers: _getAuthHeaders(),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          final streakData = StreakData.fromJson(data['data']);
          
          // Update local cache
          await _storageService.saveStreakData(streakData);
          
          return streakData;
        } else {
          throw Exception(data['message'] ?? 'Failed to get streak data');
        }
      } else if (response.statusCode == 401) {
        throw Exception('Authentication expired. Please login again.');
      } else {
        throw Exception('Server error: ${response.statusCode}');
      }
    } catch (e) {
      // Fallback to local data if offline
      print('Get streak API failed: $e. Using local storage.');
      return _storageService.getStreakData();
    }
  }

  /// Sync local streak data to server
  Future<StreakData> syncStreakData() async {
    try {
      final localStreak = _storageService.getStreakData();
      
      final response = await http.put(
        Uri.parse('$baseUrl/user/streak/sync'),
        headers: _getAuthHeaders(),
        body: jsonEncode({
          'streakData': localStreak.toJson(),
          'localTimestamp': DateTime.now().toIso8601String(),
        }),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          final serverStreak = StreakData.fromJson(data['data']);
          
          // Use server data as source of truth
          await _storageService.saveStreakData(serverStreak);
          
          return serverStreak;
        } else {
          throw Exception(data['message'] ?? 'Sync failed');
        }
      } else {
        throw Exception('Server error: ${response.statusCode}');
      }
    } catch (e) {
      print('Sync failed: $e. Keeping local data.');
      return _storageService.getStreakData();
    }
  }

  String _formatDate(DateTime date) {
    return '${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}';
  }
}
```

### Update HomeCubit to use API

```dart
// lib/ui/pages/home/bloc/home_cubit.dart
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../../di/service_locator.dart';
import '../../../../service/storage_service.dart';
import '../../../../service/streak_service.dart';
import 'home_state.dart';

class HomeCubit extends Cubit<HomeState> {
  final StorageService _storageService;
  final StreakService _streakService;

  HomeCubit({
    StorageService? storageService,
    StreakService? streakService,
  })  : _storageService = storageService ?? getIt<StorageService>(),
        _streakService = streakService ?? getIt<StreakService>(),
        super(const HomeState()) {
    _initializeData();
  }

  Future<void> _initializeData() async {
    emit(state.copyWith(isLoading: true));
    
    // Perform check-in via API (with local fallback)
    await _performDailyCheckIn();
    
    // Load other data
    _loadMockData();
  }

  /// Perform daily check-in using API
  Future<void> _performDailyCheckIn() async {
    try {
      // Call API - will fallback to local if offline
      final streakData = await _streakService.performCheckIn();
      
      emit(state.copyWith(
        streakData: streakData,
        streakDays: streakData.streakCount,
      ));
    } catch (e) {
      // If API fails, use local data
      final streakData = _storageService.getStreakData();
      emit(state.copyWith(
        streakData: streakData,
        streakDays: streakData.streakCount,
        errorMessage: 'Using offline mode',
      ));
    }
  }

  /// Refresh data from API
  Future<void> refreshData() async {
    emit(state.copyWith(isLoading: true, errorMessage: null));
    
    try {
      // Sync streak data with server
      final streakData = await _streakService.syncStreakData();
      
      emit(state.copyWith(
        streakData: streakData,
        streakDays: streakData.streakCount,
      ));
    } catch (e) {
      emit(state.copyWith(
        errorMessage: 'Failed to sync with server',
      ));
    }
    
    _loadMockData();
  }

  // ... rest of the code
}
```

### Register Service in DI

```dart
// lib/di/service_locator.dart

// Register StreakService
getIt.registerLazySingleton<StreakService>(
  () => StreakService(getIt<StorageService>()),
);
```

---

## 🔄 Token Refresh Flow

### Khi Access Token Hết Hạn

```dart
class ApiClient {
  Future<http.Response> request(String url, {Map<String, dynamic>? body}) async {
    var token = _storageService.getToken();
    
    var response = await http.post(
      Uri.parse(url),
      headers: {'Authorization': 'Bearer $token'},
      body: jsonEncode(body),
    );

    // Token expired
    if (response.statusCode == 401) {
      // Try to refresh token
      final refreshToken = _storageService.getRefreshToken();
      final newTokenResponse = await http.post(
        Uri.parse('$baseUrl/auth/refresh'),
        body: jsonEncode({'refreshToken': refreshToken}),
      );

      if (newTokenResponse.statusCode == 200) {
        final data = jsonDecode(newTokenResponse.body);
        final newAccessToken = data['data']['accessToken'];
        
        // Save new token
        await _storageService.saveToken(newAccessToken);
        
        // Retry original request
        return await http.post(
          Uri.parse(url),
          headers: {'Authorization': 'Bearer $newAccessToken'},
          body: jsonEncode(body),
        );
      } else {
        // Refresh failed - need to login again
        throw Exception('Session expired. Please login again.');
      }
    }

    return response;
  }
}
```

---

## 🛡️ Security Best Practices

### 1. **Token Storage**
```dart
// ✅ DO: Store token securely
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

final secureStorage = FlutterSecureStorage();

// Save token
await secureStorage.write(key: 'access_token', value: token);

// Read token
final token = await secureStorage.read(key: 'access_token');
```

### 2. **HTTPS Only**
```dart
// ✅ DO: Always use HTTPS
final baseUrl = 'https://your-api.com/api';  // ✅

// ❌ DON'T: Never use HTTP in production
final baseUrl = 'http://your-api.com/api';   // ❌
```

### 3. **Token Expiration**
```dart
// Server should include exp claim in JWT
{
  "userId": "user123",
  "exp": 1735315200,  // Unix timestamp
  "iat": 1735311600,
  "role": "user"
}
```

### 4. **Error Handling**
```dart
// ✅ Handle different error cases
try {
  await streakService.performCheckIn();
} catch (e) {
  if (e.toString().contains('401')) {
    // Token expired - redirect to login
    context.pushReplacement('/login');
  } else {
    // Network error - use offline mode
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text('Using offline mode')),
    );
  }
}
```

---

## ⚠️ Khi NÀO có thể dùng Basic Auth?

Chỉ trong các trường hợp:
- Internal tools (không public)
- Development/testing environment
- Legacy systems chưa support JWT

**❌ KHÔNG BAO GIỜ dùng Basic Auth cho production mobile app!**

---

## 📚 JWT Token Structure

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOiJ1c2VyMTIzIiwiZXhwIjoxNzM1MzE1MjAwfQ.signature
│                                      │                                                │
│          Header (base64)             │         Payload (base64)                       │  Signature
│                                      │                                                │
└─ Algorithm & type                    └─ Data (userId, exp, etc.)                     └─ Verify integrity
```

### Decode Token (for debugging only):
```dart
import 'dart:convert';

void decodeToken(String token) {
  final parts = token.split('.');
  final payload = parts[1];
  
  // Add padding if needed
  var normalized = base64Url.normalize(payload);
  var decoded = utf8.decode(base64Url.decode(normalized));
  
  print('Payload: $decoded');
  // Output: {"userId":"user123","exp":1735315200,"iat":1735311600}
}
```

---

## 🎯 Kết Luận

### ✅ **Sử dụng Bearer Token (JWT)** vì:
1. **Bảo mật cao** - Token có expire, không gửi password
2. **Hiệu suất tốt** - Stateless, không query DB
3. **Dễ scale** - Microservices friendly
4. **Best practice** - Industry standard
5. **Linh hoạt** - Refresh token, revoke dễ dàng

### ❌ **KHÔNG dùng Basic Auth** cho mobile app production

---

## 📖 Tài liệu tham khảo

- [JWT.io](https://jwt.io/)
- [OAuth 2.0 RFC](https://oauth.net/2/)
- [OWASP Mobile Security](https://owasp.org/www-project-mobile-security/)
- [Flutter Secure Storage](https://pub.dev/packages/flutter_secure_storage)
