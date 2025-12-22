# Admin Exam Management API

Base URL: `/api/admin/exams`

Middleware: `auth`, `admin` (requires admin role)

---

## 1. List All Exams

**GET** `/api/admin/exams`

Get paginated list of exams with statistics.

### Query Parameters
- `level` (optional): Filter by HSK level (HSK1, HSK2, HSK3, HSK4, HSK5, HSK6)
- `is_active` (optional): Filter by active status (0 or 1)
- `page` (optional): Page number
- `per_page` (optional): Items per page (default: 20)

### Response
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Test 1",
        "time": 35,
        "level": "HSK1",
        "is_active": true,
        "parts_count": 2,
        "attempts_count": 15,
        "created_at": "2024-12-22T10:00:00.000000Z",
        "updated_at": "2024-12-22T10:00:00.000000Z"
      }
    ],
    "total": 1
  }
}
```

---

## 2. Get Exam Details

**GET** `/api/admin/exams/{id}`

Get complete exam with all parts, questions, and statistics.

### Response
```json
{
  "success": true,
  "data": {
    "exam": {
      "id": 1,
      "title": "Test 1",
      "time": 35,
      "level": "HSK1",
      "is_active": true,
      "parts": [
        {
          "id": 1,
          "name": "Listening",
          "time": 18,
          "order": 1,
          "questions": [
            {
              "id": 1,
              "order": 0,
              "total_score": 25,
              "question_type": {
                "id": 1,
                "code": "110001",
                "name": "Listening - True/False"
              },
              "contents": [
                {
                  "id": 1,
                  "q_audio": "https://...",
                  "q_image": "https://...",
                  "a_text": ["对", "错"],
                  "a_correct": ["1"],
                  "score": 5
                }
              ]
            }
          ]
        }
      ]
    },
    "stats": {
      "total_parts": 2,
      "total_questions": 40,
      "total_score": 200,
      "total_attempts": 15,
      "completed_attempts": 12
    }
  }
}
```

---

## 3. Create New Exam

**POST** `/api/admin/exams`

Create a new exam (without parts/questions).

### Request Body
```json
{
  "title": "HSK1 Practice Test 2",
  "time": 40,
  "level": "HSK1",
  "is_active": true
}
```

### Validation Rules
- `title`: required, string, max 255 characters
- `time`: required, integer, minimum 1 (in minutes)
- `level`: required, one of [HSK1, HSK2, HSK3, HSK4, HSK5, HSK6]
- `is_active`: optional, boolean

### Response
```json
{
  "success": true,
  "message": "Exam created successfully",
  "data": {
    "id": 2,
    "title": "HSK1 Practice Test 2",
    "time": 40,
    "level": "HSK1",
    "is_active": true
  }
}
```

---

## 4. Update Exam

**PUT** `/api/admin/exams/{id}`

Update exam basic information.

### Request Body
```json
{
  "title": "HSK1 Mock Test (Updated)",
  "time": 45,
  "is_active": false
}
```

### Response
```json
{
  "success": true,
  "message": "Exam updated successfully",
  "data": {
    "id": 1,
    "title": "HSK1 Mock Test (Updated)",
    "time": 45,
    "level": "HSK1",
    "is_active": false
  }
}
```

---

## 5. Delete Exam

**DELETE** `/api/admin/exams/{id}`

Delete an exam. Cannot delete if exam has user attempts.

### Response (Success)
```json
{
  "success": true,
  "message": "Exam deleted successfully"
}
```

### Response (Has Attempts)
```json
{
  "success": false,
  "message": "Cannot delete exam with existing attempts"
}
```

---

## 6. Get Exam Statistics

**GET** `/api/admin/exams/{id}/statistics`

Get detailed statistics for an exam.

### Response
```json
{
  "success": true,
  "data": {
    "total_attempts": 15,
    "completed_attempts": 12,
    "in_progress_attempts": 3,
    "average_score": 75.5,
    "highest_score": 95.0,
    "lowest_score": 55.0,
    "average_time_spent": 1850,
    "score_distribution": {
      "0-20": 0,
      "21-40": 1,
      "41-60": 2,
      "61-80": 5,
      "81-100": 4
    }
  }
}
```

---

## 7. Get Exam Attempts

**GET** `/api/admin/exams/{id}/attempts`

Get all user attempts for an exam with pagination.

### Query Parameters
- `status` (optional): Filter by status (in_progress, completed, abandoned)
- `page` (optional): Page number

### Response
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "user": {
          "id": 5,
          "name": "John Doe",
          "email": "john@example.com"
        },
        "started_at": "2024-12-22T10:00:00.000000Z",
        "completed_at": "2024-12-22T10:35:00.000000Z",
        "total_score": 150,
        "max_score": 200,
        "percentage": 75.00,
        "status": "completed",
        "time_spent": 2100
      }
    ],
    "total": 15
  }
}
```

---

## 8. Toggle Exam Active Status

**POST** `/api/admin/exams/{id}/toggle-active`

Toggle exam between active and inactive.

### Response
```json
{
  "success": true,
  "message": "Exam status updated",
  "data": {
    "id": 1,
    "title": "Test 1",
    "is_active": false
  }
}
```

---

## 9. Duplicate Exam

**POST** `/api/admin/exams/{id}/duplicate`

Create a complete copy of an exam including all parts, questions, and content.

### Response
```json
{
  "success": true,
  "message": "Exam duplicated successfully",
  "data": {
    "id": 2,
    "title": "Test 1 (Copy)",
    "time": 35,
    "level": "HSK1",
    "is_active": false,
    "parts": [...]
  }
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
  "message": "This action is unauthorized."
}
```

### 404 Not Found
```json
{
  "message": "No query results for model [App\\Models\\Exam] {id}"
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "level": ["The selected level is invalid."]
  }
}
```

---

## Notes

1. **Authentication**: All endpoints require valid Bearer token with admin role
2. **Soft Delete**: Exams are hard-deleted but protected if they have attempts
3. **Cascade Delete**: Deleting an exam will cascade delete all parts, questions, and contents
4. **Duplicate**: Creates inactive copy by default, admin must activate manually
5. **Pagination**: Default 20 items per page, customizable via `per_page` parameter
