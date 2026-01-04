# Streak API Documentation

API để quản lý streak (chuỗi check-in liên tiếp) của người dùng. Hỗ trợ check-in hàng ngày, đồng bộ offline, và theo dõi thống kê.

## Cấu trúc Database

### Bảng `user_streaks`
- `id`: BIGINT (Primary Key)
- `user_id`: BIGINT (Foreign Key -> users.id, unique)
- `streak_count`: INT - Số ngày check-in liên tiếp hiện tại
- `last_check_in_date`: DATE - Ngày check-in cuối cùng
- `weekly_check_ins`: JSON - Mảng các ngày check-in trong tuần hiện tại
- `total_check_in_days`: INT - Tổng số ngày đã check-in
- `longest_streak`: INT - Streak dài nhất từng đạt được
- `created_at`: TIMESTAMP
- `updated_at`: TIMESTAMP

**Indexes:**
- Unique index trên `user_id`
- Index trên `last_check_in_date`
- Index trên `streak_count`

---

## Authentication

**Tất cả endpoints yêu cầu authentication** (Bearer Token):

```bash
Authorization: Bearer {access_token}
```

Lấy token qua API `/api/auth/login`. Xem [STREAK_API_AUTHENTICATION.md](STREAK_API_AUTHENTICATION.md) để hiểu tại sao dùng Bearer Token thay vì Basic Auth.

---

## API Endpoints

### 1. Lấy dữ liệu streak hiện tại

**Endpoint:** `GET /api/user/streak`

**Mô tả:** Lấy thông tin streak của user hiện tại

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** (Status 200)
```json
{
  "success": true,
  "data": {
    "streak_count": 15,
    "last_check_in_date": "2024-12-17",
    "weekly_check_ins": [
      "2024-12-11",
      "2024-12-12",
      "2024-12-13",
      "2024-12-14",
      "2024-12-15",
      "2024-12-16",
      "2024-12-17"
    ],
    "total_check_in_days": 120,
    "longest_streak": 30,
    "can_check_in_today": false
  }
}
```

**Error Response:**
```json
// 401 Unauthorized
{
  "message": "Unauthenticated."
}

// 500 Server Error
{
  "success": false,
  "message": "Failed to get streak data",
  "error": "Error details..."
}
```

---

### 2. Check-in hàng ngày

**Endpoint:** `POST /api/user/streak/check-in`

**Mô tả:** Thực hiện check-in hàng ngày. Tự động cập nhật streak count, weekly check-ins, và total days.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:** (Optional - dùng cho tracking client-side)
```json
{
  "checkInDate": "2024-12-17",
  "timestamp": "2024-12-17T14:30:00Z"
}
```

**Response (Check-in thành công):** (Status 201)
```json
{
  "success": true,
  "message": "Check-in recorded successfully",
  "already_checked_in": false,
  "data": {
    "streak_count": 16,
    "last_check_in_date": "2024-12-17",
    "weekly_check_ins": [
      "2024-12-11",
      "2024-12-12",
      "2024-12-13",
      "2024-12-14",
      "2024-12-15",
      "2024-12-16",
      "2024-12-17"
    ],
    "total_check_in_days": 121,
    "longest_streak": 30,
    "can_check_in_today": false
  }
}
```

**Response (Đã check-in hôm nay):** (Status 200)
```json
{
  "success": true,
  "message": "You have already checked in today",
  "already_checked_in": true,
  "data": {
    "streak_count": 16,
    "last_check_in_date": "2024-12-17",
    "weekly_check_ins": [...],
    "total_check_in_days": 121,
    "longest_streak": 30,
    "can_check_in_today": false
  }
}
```

**Logic:**
- Nếu check-in liên tiếp (yesterday -> today): `streak_count++`
- Nếu bỏ lỡ 1+ ngày: `streak_count = 1` (reset streak)
- Nếu đạt longest_streak mới: cập nhật `longest_streak`
- Luôn tăng `total_check_in_days`
- Tự động cập nhật `weekly_check_ins` (chỉ giữ tuần hiện tại)

---

### 3. Đồng bộ dữ liệu offline

**Endpoint:** `PUT /api/user/streak/sync`

**Mô tả:** Đồng bộ dữ liệu streak từ client (offline mode). Server là source of truth, chỉ merge weekly check-ins.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "streakData": {
    "streakCount": 15,
    "lastCheckInDate": "2024-12-17",
    "weeklyCheckIns": [
      "2024-12-11",
      "2024-12-12",
      "2024-12-13"
    ],
    "totalCheckInDays": 120
  },
  "localTimestamp": "2024-12-17T14:30:00Z"
}
```

**Response:** (Status 200)
```json
{
  "success": true,
  "message": "Data synced successfully",
  "data": {
    "streak_count": 16,
    "last_check_in_date": "2024-12-17",
    "weekly_check_ins": [
      "2024-12-11",
      "2024-12-12",
      "2024-12-13",
      "2024-12-14",
      "2024-12-15",
      "2024-12-16",
      "2024-12-17"
    ],
    "total_check_in_days": 121,
    "longest_streak": 30,
    "can_check_in_today": false
  }
}
```

**Note:** Server data luôn được ưu tiên. Client data chỉ dùng để merge weekly check-ins.

---

### 4. Lấy thống kê chi tiết

**Endpoint:** `GET /api/user/streak/statistics`

**Mô tả:** Lấy thống kê chi tiết và achievements

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** (Status 200)
```json
{
  "success": true,
  "data": {
    "current_streak": 16,
    "longest_streak": 30,
    "total_check_in_days": 121,
    "weekly_check_ins_count": 7,
    "can_check_in_today": false,
    "last_check_in_date": "2024-12-17",
    "streaks": {
      "current": 16,
      "longest": 30,
      "this_week": 7
    },
    "achievements": [
      {
        "name": "Week Warrior",
        "icon": "🔥",
        "description": "7 days streak achieved",
        "unlocked": true
      },
      {
        "name": "Month Master",
        "icon": "⭐",
        "description": "30 days streak achieved",
        "unlocked": true
      },
      {
        "name": "Intermediate",
        "icon": "🌿",
        "description": "100 total check-ins",
        "unlocked": true
      }
    ]
  }
}
```

**Achievements:**

Streak Milestones:
- 🔥 Week Warrior: 7 ngày liên tiếp
- ⭐ Month Master: 30 ngày liên tiếp
- 🏆 Century Champion: 100 ngày liên tiếp
- 👑 Year Legend: 365 ngày liên tiếp

Total Check-in Milestones:
- 🌱 Beginner: 50 ngày tổng
- 🌿 Intermediate: 100 ngày tổng
- 🌳 Advanced: 500 ngày tổng
- 🌲 Expert: 1000 ngày tổng

---

### 5. Reset streak (Testing/Debug)

**Endpoint:** `DELETE /api/user/streak/reset`

**Mô tả:** Reset streak về 0. Giữ lại total_check_in_days và longest_streak.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** (Status 200)
```json
{
  "success": true,
  "message": "Streak reset successfully",
  "data": {
    "streak_count": 0,
    "last_check_in_date": null,
    "weekly_check_ins": [],
    "total_check_in_days": 121,
    "longest_streak": 30,
    "can_check_in_today": true
  }
}
```

**Note:** Endpoint này dùng cho testing. Production có thể remove hoặc thêm admin guard.

---

## Flutter Implementation Example

### 1. Service Class

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

class StreakService {
  final String baseUrl = 'https://your-api.com/api';
  final StorageService _storage;

  StreakService(this._storage);

  Map<String, String> _getHeaders() {
    final token = _storage.getToken();
    return {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer $token',
    };
  }

  /// Get streak data
  Future<StreakData> getStreakData() async {
    final response = await http.get(
      Uri.parse('$baseUrl/user/streak'),
      headers: _getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return StreakData.fromJson(data['data']);
    }
    throw Exception('Failed to load streak data');
  }

  /// Perform check-in
  Future<CheckInResponse> checkIn() async {
    final response = await http.post(
      Uri.parse('$baseUrl/user/streak/check-in'),
      headers: _getHeaders(),
      body: jsonEncode({
        'checkInDate': DateTime.now().toIso8601String().split('T')[0],
        'timestamp': DateTime.now().toIso8601String(),
      }),
    );

    if (response.statusCode == 200 || response.statusCode == 201) {
      final data = jsonDecode(response.body);
      return CheckInResponse(
        success: data['success'],
        message: data['message'],
        alreadyCheckedIn: data['already_checked_in'],
        streakData: StreakData.fromJson(data['data']),
      );
    }
    throw Exception('Check-in failed');
  }

  /// Sync data
  Future<StreakData> syncData(StreakData localData) async {
    final response = await http.put(
      Uri.parse('$baseUrl/user/streak/sync'),
      headers: _getHeaders(),
      body: jsonEncode({
        'streakData': localData.toJson(),
        'localTimestamp': DateTime.now().toIso8601String(),
      }),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return StreakData.fromJson(data['data']);
    }
    throw Exception('Sync failed');
  }

  /// Get statistics
  Future<StreakStatistics> getStatistics() async {
    final response = await http.get(
      Uri.parse('$baseUrl/user/streak/statistics'),
      headers: _getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return StreakStatistics.fromJson(data['data']);
    }
    throw Exception('Failed to load statistics');
  }
}
```

### 2. Models

```dart
class StreakData {
  final int streakCount;
  final String? lastCheckInDate;
  final List<String> weeklyCheckIns;
  final int totalCheckInDays;
  final int longestStreak;
  final bool canCheckInToday;

  StreakData({
    required this.streakCount,
    this.lastCheckInDate,
    required this.weeklyCheckIns,
    required this.totalCheckInDays,
    required this.longestStreak,
    required this.canCheckInToday,
  });

  factory StreakData.fromJson(Map<String, dynamic> json) {
    return StreakData(
      streakCount: json['streak_count'] ?? 0,
      lastCheckInDate: json['last_check_in_date'],
      weeklyCheckIns: List<String>.from(json['weekly_check_ins'] ?? []),
      totalCheckInDays: json['total_check_in_days'] ?? 0,
      longestStreak: json['longest_streak'] ?? 0,
      canCheckInToday: json['can_check_in_today'] ?? true,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'streakCount': streakCount,
      'lastCheckInDate': lastCheckInDate,
      'weeklyCheckIns': weeklyCheckIns,
      'totalCheckInDays': totalCheckInDays,
    };
  }
}

class CheckInResponse {
  final bool success;
  final String message;
  final bool alreadyCheckedIn;
  final StreakData streakData;

  CheckInResponse({
    required this.success,
    required this.message,
    required this.alreadyCheckedIn,
    required this.streakData,
  });
}
```

### 3. BLoC/Cubit Usage

```dart
class HomeCubit extends Cubit<HomeState> {
  final StreakService _streakService;

  HomeCubit(this._streakService) : super(HomeState.initial());

  Future<void> performCheckIn() async {
    try {
      emit(state.copyWith(isLoading: true));
      
      final response = await _streakService.checkIn();
      
      emit(state.copyWith(
        isLoading: false,
        streakData: response.streakData,
        message: response.message,
      ));

      if (response.alreadyCheckedIn) {
        // Show message: Already checked in
      } else {
        // Show success animation
      }
    } catch (e) {
      emit(state.copyWith(
        isLoading: false,
        error: e.toString(),
      ));
    }
  }

  Future<void> loadStreakData() async {
    try {
      final data = await _streakService.getStreakData();
      emit(state.copyWith(streakData: data));
    } catch (e) {
      // Handle error
    }
  }
}
```

---

## Use Cases

### 1. Daily Check-in Flow

```dart
// User opens app
await loadStreakData();

// User clicks check-in button
if (streakData.canCheckInToday) {
  await performCheckIn();
  // Show success + streak count animation
} else {
  // Show "Already checked in today"
}
```

### 2. Offline Support

```dart
// No internet - save locally
final localData = await _localStorage.getStreakData();
final updatedData = localData.checkInLocally();
await _localStorage.saveStreakData(updatedData);

// When back online - sync
try {
  final serverData = await _streakService.syncData(updatedData);
  await _localStorage.saveStreakData(serverData);
} catch (e) {
  // Continue with local data
}
```

### 3. Achievement Display

```dart
final stats = await _streakService.getStatistics();

// Show achievements
for (final achievement in stats.achievements) {
  if (achievement.unlocked) {
    print('${achievement.icon} ${achievement.name}');
  }
}

// Show progress bars
print('Current streak: ${stats.currentStreak} / 365 days');
print('Total check-ins: ${stats.totalCheckInDays}');
```

---

## Testing

### cURL Examples

```bash
# 1. Get streak data
curl -X GET http://localhost:8000/api/user/streak \
  -H "Authorization: Bearer YOUR_TOKEN"

# 2. Check-in
curl -X POST http://localhost:8000/api/user/streak/check-in \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "checkInDate": "2024-12-17",
    "timestamp": "2024-12-17T14:30:00Z"
  }'

# 3. Get statistics
curl -X GET http://localhost:8000/api/user/streak/statistics \
  -H "Authorization: Bearer YOUR_TOKEN"

# 4. Sync data
curl -X PUT http://localhost:8000/api/user/streak/sync \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "streakData": {
      "streakCount": 15,
      "lastCheckInDate": "2024-12-16",
      "weeklyCheckIns": ["2024-12-16"],
      "totalCheckInDays": 120
    }
  }'

# 5. Reset streak (testing)
curl -X DELETE http://localhost:8000/api/user/streak/reset \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Business Logic Details

### Streak Calculation

1. **Consecutive Days:**
   - Yesterday check-in + Today check-in = Streak continues
   - Gap > 1 day = Streak resets to 1

2. **Weekly Check-ins:**
   - Automatically filters to current week (Monday - Sunday)
   - Removes old dates when new week starts

3. **Longest Streak:**
   - Tracks maximum streak ever achieved
   - Never decreases (even if current streak resets)

4. **Total Days:**
   -累积 (cumulative) - never resets
   - Counts unique days checked in

### Edge Cases

- **Same Day Check-in:** Returns success but doesn't increment
- **Time Zone:** Server uses server timezone (UTC recommended)
- **Offline Mode:** Client can track locally, sync later
- **Data Conflict:** Server data is always source of truth

---

## Security & Best Practices

1. **Authentication Required:** All endpoints need Bearer token
2. **Rate Limiting:** Recommend 10 requests/minute per user
3. **Validation:** Check date formats and required fields
4. **Idempotent:** Multiple check-ins same day = same result
5. **Offline Support:** Client should cache and sync when online

---

## Models

**File:** `app/Models/UserStreak.php`

**Key Methods:**
- `performCheckIn()` - Execute daily check-in logic
- `toApiFormat()` - Format for API response
- `canCheckInToday()` - Check if can check-in
- `getOrCreateForUser($userId)` - Get or create streak
- `syncFromClient($data)` - Sync offline data

---

## Controller

**File:** `app/Http/Controllers/Api/StreakController.php`

**Methods:**
- `getStreakData()` - GET /api/user/streak
- `checkIn()` - POST /api/user/streak/check-in
- `syncData()` - PUT /api/user/streak/sync
- `getStatistics()` - GET /api/user/streak/statistics
- `resetStreak()` - DELETE /api/user/streak/reset

---

## Migration

**File:** `database/migrations/2025_12_17_*_create_user_streaks_table.php`

**Table Structure:** One record per user, unique constraint on user_id

---

## Notes

- ✅ Hỗ trợ offline mode
- ✅ Auto reset streak nếu bỏ lỡ
- ✅ Track longest streak
- ✅ Weekly check-ins auto-filter
- ✅ Achievements system
- ✅ Bearer Token authentication
- ✅ JSON response format
- ✅ Error handling
