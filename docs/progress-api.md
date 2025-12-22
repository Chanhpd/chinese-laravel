# User Progress Tracking API Documentation

## Overview
API endpoints for tracking user learning progress across HSK levels, topics, and providing comprehensive dashboard statistics.

Base URL: `/api`

All endpoints require authentication using Bearer token.

---

## Authentication
```http
Authorization: Bearer {access_token}
```

---

## Progress Dashboard

### 1. Get Learning Dashboard
Get comprehensive learning dashboard with all progress data.

**Endpoint:** `GET /api/dashboard`

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "streak": {
      "current_streak": 7,
      "longest_streak": 15,
      "total_check_in_days": 45,
      "last_check_in": "2024-12-22",
      "weekly_check_ins": ["2024-12-16", "2024-12-17", "2024-12-18", "2024-12-19", "2024-12-20", "2024-12-21", "2024-12-22"]
    },
    "topic_progress": {
      "total_topics": 10,
      "mastered": 2,
      "advanced": 3,
      "intermediate": 3,
      "beginner": 2,
      "total_words_completed": 250,
      "total_words": 500,
      "overall_percentage": 50.0
    },
    "level_progress": {
      "total_levels": 3,
      "mastered": 1,
      "advanced": 1,
      "intermediate": 1,
      "beginner": 0,
      "words_completed": 180,
      "total_words": 300,
      "radicals_completed": 50,
      "total_radicals": 100,
      "overall_percentage": 57.5
    },
    "saved_vocabularies": {
      "total_saved": 120,
      "reviewed": 80,
      "need_review": 30,
      "total_reviews": 450,
      "recent_saved": 15
    },
    "exam_progress": {
      "total_attempts": 12,
      "completed_exams": 10,
      "in_progress": 2,
      "average_score": 75.5,
      "highest_score": 95.0,
      "total_time_spent": 360
    },
    "recent_activities": [
      {
        "type": "topic_study",
        "title": "Family Members",
        "timestamp": "2024-12-22T10:30:00Z",
        "details": "Studied Family Members - 85% complete"
      },
      {
        "type": "level_study",
        "title": "HSK 1",
        "timestamp": "2024-12-22T09:15:00Z",
        "details": "Practiced HSK 1 - 75% complete"
      },
      {
        "type": "exam_completed",
        "title": "HSK 1 Test 1",
        "timestamp": "2024-12-21T16:45:00Z",
        "details": "Completed HSK 1 Test 1 - Score: 85%"
      }
    ]
  }
}
```

### 2. Get Statistics
Get detailed learning statistics.

**Endpoint:** `GET /api/dashboard/statistics`

**Response:**
```json
{
  "success": true,
  "data": {
    "overview": {
      "topics_started": 10,
      "levels_started": 3,
      "words_saved": 120,
      "exams_taken": 10,
      "current_streak": 7
    },
    "by_mastery_level": {
      "topics": {
        "mastered": 2,
        "advanced": 3,
        "intermediate": 3,
        "beginner": 2
      },
      "levels": {
        "mastered": 1,
        "advanced": 1,
        "intermediate": 1,
        "beginner": 0
      }
    },
    "time_spent": {
      "total_exam_time": 360
    }
  }
}
```

---

## User Level Progress

### 3. Get All Level Progress
Get progress for all HSK levels.

**Endpoint:** `GET /api/level-progress`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "level_id": 1,
      "level": {
        "id": 1,
        "test_type": "HSK",
        "level_number": 1,
        "level_name": "HSK 1"
      },
      "words": {
        "completed": 120,
        "total": 150,
        "percentage": 80.0
      },
      "radicals": {
        "completed": 40,
        "total": 50,
        "percentage": 80.0
      },
      "overall_percentage": 80.0,
      "mastery_level": "advanced",
      "last_studied_at": "2024-12-22T10:30:00Z",
      "created_at": "2024-12-01T00:00:00Z",
      "updated_at": "2024-12-22T10:30:00Z"
    }
  ]
}
```

### 4. Get Level Progress by ID
Get progress for a specific HSK level.

**Endpoint:** `GET /api/level-progress/{levelId}`

**Path Parameters:**
- `levelId` (integer, required): Level ID

**Response:** Same as single item in array above.

### 5. Initialize Level Progress
Create initial progress record for a level.

**Endpoint:** `POST /api/level-progress/{levelId}/initialize`

**Response:**
```json
{
  "success": true,
  "message": "Level progress initialized",
  "data": {
    "id": 1,
    "user_id": 1,
    "level_id": 1,
    "words": {
      "completed": 0,
      "total": 150,
      "percentage": 0.0
    },
    "radicals": {
      "completed": 0,
      "total": 50,
      "percentage": 0.0
    },
    "overall_percentage": 0.0,
    "mastery_level": "beginner"
  }
}
```

### 6. Mark Word as Completed
Mark a word as completed in a level.

**Endpoint:** `POST /api/level-progress/{levelId}/word-completed`

**Request Body:**
```json
{
  "word_id": 123
}
```

**Validation:**
- `word_id` (integer, required): Must exist in word table

**Response:**
```json
{
  "success": true,
  "message": "Word marked as completed",
  "data": {
    "id": 1,
    "user_id": 1,
    "level_id": 1,
    "words": {
      "completed": 121,
      "total": 150,
      "percentage": 80.67
    },
    "radicals": {
      "completed": 40,
      "total": 50,
      "percentage": 80.0
    },
    "overall_percentage": 80.5,
    "mastery_level": "advanced",
    "last_studied_at": "2024-12-22T10:30:00Z"
  }
}
```

### 7. Mark Radical as Completed
Mark a radical as completed in a level.

**Endpoint:** `POST /api/level-progress/{levelId}/radical-completed`

**Request Body:**
```json
{
  "radical_id": 45
}
```

**Validation:**
- `radical_id` (integer, required): Must exist in radical table

**Response:** Similar to word completion response.

### 8. Get Level Statistics
Get aggregated statistics for all levels.

**Endpoint:** `GET /api/level-progress/statistics`

**Response:**
```json
{
  "success": true,
  "data": {
    "total_levels": 3,
    "mastered_levels": 1,
    "advanced_levels": 1,
    "intermediate_levels": 1,
    "beginner_levels": 0,
    "total_words_completed": 300,
    "total_words": 450,
    "total_radicals_completed": 120,
    "total_radicals": 150,
    "last_studied_level": "HSK 1",
    "last_studied_at": "2024-12-22T10:30:00Z",
    "overall_percentage": 70.0
  }
}
```

### 9. Reset Level Progress
Reset progress for a specific level (for re-learning).

**Endpoint:** `POST /api/level-progress/{levelId}/reset`

**Response:**
```json
{
  "success": true,
  "message": "Progress reset successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "level_id": 1,
    "words": {
      "completed": 0,
      "total": 150,
      "percentage": 0.0
    },
    "radicals": {
      "completed": 0,
      "total": 50,
      "percentage": 0.0
    },
    "overall_percentage": 0.0,
    "mastery_level": "beginner",
    "last_studied_at": null
  }
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "word_id": ["The word id field is required."]
  }
}
```

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Progress not found"
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "An error occurred while processing your request"
}
```

---

## Mastery Levels

Progress is automatically categorized into mastery levels based on completion percentage:

- **Beginner**: 0% - 39%
- **Intermediate**: 40% - 69%
- **Advanced**: 70% - 89%
- **Mastered**: 90% - 100%

---

## Usage Examples

### Example 1: Track Daily Learning Flow
```javascript
// 1. Check in for the day
POST /api/user/streak/check-in

// 2. Get dashboard overview
GET /api/dashboard

// 3. Start studying HSK 1
GET /api/level-progress/1

// 4. Mark words as completed
POST /api/level-progress/1/word-completed
{ "word_id": 123 }

// 5. Mark radicals as completed
POST /api/level-progress/1/radical-completed
{ "radical_id": 45 }

// 6. Check updated progress
GET /api/level-progress/1
```

### Example 2: Monitor Learning Progress
```javascript
// Get comprehensive dashboard
GET /api/dashboard

// Get level statistics
GET /api/level-progress/statistics

// Get topic statistics
GET /api/progress/statistics
```

---

## Notes

1. **Auto-initialization**: Level progress is automatically created when first accessed or when marking items as completed
2. **Auto-syncing**: Total word and radical counts are automatically synced from the level data
3. **Mastery levels**: Automatically updated based on completion percentage
4. **Last studied timestamp**: Automatically updated when marking items as completed
5. **Progress percentage**: Calculated in real-time based on completed vs total items
