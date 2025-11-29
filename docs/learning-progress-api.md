# Learning Progress API Documentation

## Overview

Learning Progress API quản lý tiến trình học tập của user qua các topics và từ vựng. API cung cấp:
- Tracking tiến độ học theo topic
- Quản lý saved vocabularies (từ vựng đã lưu)
- Thống kê và analytics học tập
- Mastery level tracking (beginner → intermediate → advanced → mastered)

**Base URL**: `/api`

**Authentication**: Required - Bearer Token (Laravel Sanctum)

**All requests must include**:
```
Authorization: Bearer {your_access_token}
Accept: application/json
```

---

## Table of Contents
1. [User Progress Endpoints](#user-progress-endpoints)
2. [Saved Vocabulary Endpoints](#saved-vocabulary-endpoints)
3. [Data Models](#data-models)
4. [Use Cases & Examples](#use-cases--examples)

---

## User Progress Endpoints

### 1. Get All User Progress

**Endpoint**: `GET /api/progress`

**Description**: Lấy danh sách tất cả các topics mà user đã bắt đầu học, kèm tiến độ.

**Authentication**: Required

**Query Parameters**: None

**Request Example**:
```bash
curl -X GET "http://localhost:8000/api/progress" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Response Example** (200 OK):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "topic": {
        "id": 123,
        "name": "Family and Relationships",
        "name_zh": "家庭与关系",
        "level": "HSK1",
        "description": "Learn vocabulary about family members..."
      },
      "completed_words": 15,
      "total_words": 20,
      "progress_percentage": 75.00,
      "mastery_level": "advanced",
      "last_studied_at": "2025-11-16T14:30:00.000000Z"
    },
    {
      "id": 2,
      "topic": {
        "id": 124,
        "name": "Marriage and Love",
        "name_zh": "婚姻与爱情",
        "level": "HSK2",
        "description": "Vocabulary about marriage..."
      },
      "completed_words": 8,
      "total_words": 20,
      "progress_percentage": 40.00,
      "mastery_level": "intermediate",
      "last_studied_at": "2025-11-15T10:20:00.000000Z"
    }
  ]
}
```

---

### 2. Get Progress for Specific Topic

**Endpoint**: `GET /api/progress/topic/{topicId}`

**Description**: Lấy tiến độ học của user cho một topic cụ thể. Tự động tạo progress record nếu chưa có.

**Authentication**: Required

**Path Parameters**:
- `topicId` (integer, required): ID của topic

**Request Example**:
```bash
curl -X GET "http://localhost:8000/api/progress/topic/123" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Response Example** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "topic": {
      "id": 123,
      "name": "Family and Relationships",
      "name_zh": "家庭与关系",
      "level": "HSK1",
      "description": "Learn vocabulary about family members, relationships..."
    },
    "completed_words": 15,
    "total_words": 20,
    "progress_percentage": 75.00,
    "mastery_level": "advanced",
    "last_studied_at": "2025-11-16T14:30:00.000000Z"
  }
}
```

---

### 3. Update Topic Progress

**Endpoint**: `PUT /api/progress/topic/{topicId}`

**Description**: Cập nhật tiến độ học cho một topic (mark word as completed, decrement, or reset).

**Authentication**: Required

**Path Parameters**:
- `topicId` (integer, required): ID của topic

**Request Body**:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `action` | string | Yes | Action: `increment`, `decrement`, or `reset` |
| `vocabulary_id` | integer | No | ID của vocabulary (optional, for tracking) |

**Actions**:
- `increment`: Tăng số từ đã hoàn thành lên 1
- `decrement`: Giảm số từ đã hoàn thành xuống 1
- `reset`: Reset progress về 0

**Request Example**:
```bash
curl -X PUT "http://localhost:8000/api/progress/topic/123" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "action": "increment",
    "vocabulary_id": 456
  }'
```

**Response Example** (200 OK):
```json
{
  "success": true,
  "message": "Progress updated successfully",
  "data": {
    "completed_words": 16,
    "total_words": 20,
    "progress_percentage": 80.00,
    "mastery_level": "advanced"
  }
}
```

**Mastery Level Calculation**:
- `0-39%`: beginner
- `40-69%`: intermediate
- `70-89%`: advanced
- `90-100%`: mastered

---

### 4. Get User Learning Statistics

**Endpoint**: `GET /api/progress/statistics`

**Description**: Lấy thống kê tổng quan về tiến độ học tập của user.

**Authentication**: Required

**Request Example**:
```bash
curl -X GET "http://localhost:8000/api/progress/statistics" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Response Example** (200 OK):
```json
{
  "success": true,
  "data": {
    "total_topics_started": 5,
    "total_words_learned": 87,
    "mastery_breakdown": {
      "beginner": 1,
      "intermediate": 2,
      "advanced": 1,
      "mastered": 1
    },
    "recent_activity": [
      {
        "topic_name": "Family and Relationships",
        "topic_name_zh": "家庭与关系",
        "last_studied_at": "2025-11-16T14:30:00.000000Z",
        "progress_percentage": 75.00
      },
      {
        "topic_name": "Marriage and Love",
        "topic_name_zh": "婚姻与爱情",
        "last_studied_at": "2025-11-15T10:20:00.000000Z",
        "progress_percentage": 40.00
      }
    ]
  }
}
```

---

### 5. Get Progress by HSK Level

**Endpoint**: `GET /api/progress/level/{level}`

**Description**: Lấy tiến độ của user theo HSK level (HSK1, HSK2, HSK3, HSK4, HSK5, HSK6).

**Authentication**: Required

**Path Parameters**:
- `level` (string, required): HSK level (hsk1, hsk2, hsk3, hsk4, hsk5, hsk6)

**Request Example**:
```bash
curl -X GET "http://localhost:8000/api/progress/level/hsk1" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Response Example** (200 OK):
```json
{
  "success": true,
  "level": "HSK1",
  "data": [
    {
      "topic_id": 123,
      "topic_name": "Family and Relationships",
      "topic_name_zh": "家庭与关系",
      "progress_percentage": 75.00,
      "mastery_level": "advanced"
    },
    {
      "topic_id": 125,
      "topic_name": "Numbers and Counting",
      "topic_name_zh": "数字与计数",
      "progress_percentage": 100.00,
      "mastery_level": "mastered"
    }
  ]
}
```

---

## Saved Vocabulary Endpoints

### 1. Get All Saved Vocabularies

**Endpoint**: `GET /api/saved-vocabularies`

**Description**: Lấy danh sách tất cả các từ vựng mà user đã save để ôn tập.

**Authentication**: Required

**Query Parameters**:

| Parameter | Type | Description |
|-----------|------|-------------|
| `topic_id` | integer | Filter by topic ID |
| `need_review` | boolean | Chỉ lấy từ cần ôn (không review trong 3 ngày) |
| `recent_days` | integer | Lấy từ saved trong X ngày gần đây |
| `per_page` | integer | Số items per page (default: 20) |

**Request Example**:
```bash
curl -X GET "http://localhost:8000/api/saved-vocabularies?topic_id=123&per_page=10" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Response Example** (200 OK):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "vocabulary": {
        "id": 456,
        "word": "家庭",
        "pinyin": "jiātíng",
        "simplified": "家庭",
        "traditional": "家庭",
        "meaning": "family",
        "meaning_zh": "gia đình",
        "part_of_speech": "noun",
        "example_sentence": "我的家庭很幸福",
        "example_translation": "My family is very happy",
        "topic": {
          "id": 123,
          "name": "Family and Relationships",
          "name_zh": "家庭与关系",
          "level": "HSK1"
        }
      },
      "notes": "Need to practice pronunciation",
      "review_count": 3,
      "last_reviewed_at": "2025-11-15T10:00:00.000000Z",
      "created_at": "2025-11-10T08:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 47
  }
}
```

---

### 2. Save a Vocabulary

**Endpoint**: `POST /api/saved-vocabularies`

**Description**: Lưu một từ vựng để ôn tập sau.

**Authentication**: Required

**Request Body**:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `vocabulary_id` | integer | Yes | ID của vocabulary cần save |
| `notes` | string | No | Ghi chú cá nhân (max 500 chars) |

**Request Example**:
```bash
curl -X POST "http://localhost:8000/api/saved-vocabularies" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "vocabulary_id": 456,
    "notes": "Difficult to remember the tone"
  }'
```

**Response Example** (201 Created):
```json
{
  "success": true,
  "message": "Vocabulary saved successfully",
  "data": {
    "id": 1,
    "vocabulary": {
      "id": 456,
      "word": "家庭",
      "simplified": "家庭",
      "meaning": "family"
    },
    "notes": "Difficult to remember the tone",
    "created_at": "2025-11-16T15:20:00.000000Z"
  }
}
```

**Error Response** (409 Conflict):
```json
{
  "success": false,
  "message": "Vocabulary already saved"
}
```

---

### 3. Bulk Save Vocabularies

**Endpoint**: `POST /api/saved-vocabularies/bulk`

**Description**: Lưu nhiều từ vựng cùng lúc.

**Authentication**: Required

**Request Body**:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `vocabulary_ids` | array | Yes | Mảng các vocabulary IDs |

**Request Example**:
```bash
curl -X POST "http://localhost:8000/api/saved-vocabularies/bulk" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "vocabulary_ids": [456, 457, 458, 459, 460]
  }'
```

**Response Example** (201 Created):
```json
{
  "success": true,
  "message": "Vocabularies saved successfully",
  "data": {
    "saved_count": 4,
    "already_saved_count": 1
  }
}
```

---

### 4. Update Saved Vocabulary Notes

**Endpoint**: `PUT /api/saved-vocabularies/{id}`

**Description**: Cập nhật ghi chú cho một saved vocabulary.

**Authentication**: Required

**Path Parameters**:
- `id` (integer, required): ID của saved vocabulary

**Request Body**:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `notes` | string | No | Ghi chú mới (max 500 chars) |

**Request Example**:
```bash
curl -X PUT "http://localhost:8000/api/saved-vocabularies/1" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "notes": "Updated: Remember the second tone"
  }'
```

**Response Example** (200 OK):
```json
{
  "success": true,
  "message": "Notes updated successfully",
  "data": {
    "id": 1,
    "notes": "Updated: Remember the second tone",
    "updated_at": "2025-11-16T16:00:00.000000Z"
  }
}
```

---

### 5. Mark as Reviewed

**Endpoint**: `POST /api/saved-vocabularies/{id}/review`

**Description**: Đánh dấu một từ đã được ôn tập. Tăng review_count và cập nhật last_reviewed_at.

**Authentication**: Required

**Path Parameters**:
- `id` (integer, required): ID của saved vocabulary

**Request Example**:
```bash
curl -X POST "http://localhost:8000/api/saved-vocabularies/1/review" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Response Example** (200 OK):
```json
{
  "success": true,
  "message": "Marked as reviewed",
  "data": {
    "review_count": 4,
    "last_reviewed_at": "2025-11-16T16:30:00.000000Z"
  }
}
```

---

### 6. Remove Saved Vocabulary

**Endpoint**: `DELETE /api/saved-vocabularies/{id}`

**Description**: Xóa một từ khỏi danh sách saved.

**Authentication**: Required

**Path Parameters**:
- `id` (integer, required): ID của saved vocabulary

**Request Example**:
```bash
curl -X DELETE "http://localhost:8000/api/saved-vocabularies/1" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Response Example** (200 OK):
```json
{
  "success": true,
  "message": "Vocabulary removed from saved list"
}
```

---

### 7. Get Saved Vocabulary Statistics

**Endpoint**: `GET /api/saved-vocabularies/statistics`

**Description**: Lấy thống kê về saved vocabularies.

**Authentication**: Required

**Request Example**:
```bash
curl -X GET "http://localhost:8000/api/saved-vocabularies/statistics" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Response Example** (200 OK):
```json
{
  "success": true,
  "data": {
    "total_saved": 47,
    "need_review": 12,
    "reviewed_today": 5,
    "saved_this_week": 8,
    "by_topic": {
      "Family and Relationships": 15,
      "Marriage and Love": 10,
      "Numbers and Counting": 22
    }
  }
}
```

---

## Data Models

### UserTopicProgress Model

```typescript
interface UserTopicProgress {
  id: number;
  topic: {
    id: number;
    name: string;           // English name
    name_zh: string;        // Chinese name
    level: string;          // HSK1-HSK6
    description: string;
  };
  completed_words: number;   // Số từ đã học xong
  total_words: number;       // Tổng số từ trong topic
  progress_percentage: number; // % tiến độ (0-100)
  mastery_level: string;     // beginner|intermediate|advanced|mastered
  last_studied_at: string;   // ISO 8601 datetime
}
```

### SavedVocabulary Model

```typescript
interface SavedVocabulary {
  id: number;
  vocabulary: {
    id: number;
    word: string;              // Chinese characters
    pinyin: string;            // Romanization
    simplified: string;        // Simplified Chinese
    traditional: string;       // Traditional Chinese
    meaning: string;           // English meaning
    meaning_zh: string;        // Vietnamese meaning
    part_of_speech: string;    // noun, verb, adjective, etc.
    example_sentence: string;  // Example in Chinese
    example_translation: string; // Example translation
    topic: {
      id: number;
      name: string;
      name_zh: string;
      level: string;
    };
  };
  notes: string | null;        // User's personal notes
  review_count: number;        // Số lần đã ôn tập
  last_reviewed_at: string | null; // Last review time
  created_at: string;          // When saved
}
```

---

## Use Cases & Examples

### Use Case 1: Start Learning a Topic

Khi user bắt đầu học một topic mới:

```javascript
async function startLearningTopic(topicId) {
  const token = localStorage.getItem('access_token');
  
  try {
    // Get or create progress for this topic
    const response = await fetch(`http://localhost:8000/api/progress/topic/${topicId}`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    const data = await response.json();
    
    if (data.success) {
      console.log('Topic progress:', data.data);
      // Display progress: 15/20 words (75%)
      // Mastery level: advanced
      return data.data;
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

// Usage
startLearningTopic(123);
```

---

### Use Case 2: Mark Word as Completed

Khi user học xong một từ trong topic:

```javascript
async function markWordCompleted(topicId, vocabularyId) {
  const token = localStorage.getItem('access_token');
  
  try {
    const response = await fetch(`http://localhost:8000/api/progress/topic/${topicId}`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        action: 'increment',
        vocabulary_id: vocabularyId
      })
    });

    const data = await response.json();
    
    if (data.success) {
      console.log('Progress updated!');
      console.log(`Completed: ${data.data.completed_words}/${data.data.total_words}`);
      console.log(`Progress: ${data.data.progress_percentage}%`);
      console.log(`Level: ${data.data.mastery_level}`);
      
      // Update UI
      updateProgressBar(data.data.progress_percentage);
      showMasteryBadge(data.data.mastery_level);
      
      return data.data;
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

// Usage
markWordCompleted(123, 456);
```

---

### Use Case 3: Save Vocabulary for Later Review

Khi user gặp từ khó và muốn lưu lại:

```javascript
async function saveVocabulary(vocabularyId, notes = '') {
  const token = localStorage.getItem('access_token');
  
  try {
    const response = await fetch('http://localhost:8000/api/saved-vocabularies', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        vocabulary_id: vocabularyId,
        notes: notes
      })
    });

    const data = await response.json();
    
    if (response.status === 201 && data.success) {
      console.log('Vocabulary saved!');
      showNotification('Added to saved vocabularies');
      return data.data;
    } else if (response.status === 409) {
      console.log('Already saved');
      showNotification('This word is already in your saved list');
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

// Usage
saveVocabulary(456, 'Remember the tone - jiā (1st tone)');
```

---

### Use Case 4: Daily Review Session

Hiển thị từ cần ôn tập hôm nay:

```javascript
async function getDailyReviewWords() {
  const token = localStorage.getItem('access_token');
  
  try {
    // Get words that need review (not reviewed in 3+ days)
    const response = await fetch('http://localhost:8000/api/saved-vocabularies?need_review=true', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    const data = await response.json();
    
    if (data.success) {
      console.log(`You have ${data.data.length} words to review today!`);
      
      // Display review cards
      data.data.forEach(item => {
        displayReviewCard({
          word: item.vocabulary.word,
          pinyin: item.vocabulary.pinyin,
          meaning: item.vocabulary.meaning,
          example: item.vocabulary.example_sentence,
          notes: item.notes,
          savedId: item.id
        });
      });
      
      return data.data;
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

// After reviewing a word
async function markWordReviewed(savedId) {
  const token = localStorage.getItem('access_token');
  
  try {
    const response = await fetch(`http://localhost:8000/api/saved-vocabularies/${savedId}/review`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    const data = await response.json();
    
    if (data.success) {
      console.log(`Review count: ${data.data.review_count}`);
      moveToNextCard();
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

// Usage
getDailyReviewWords();
```

---

### Use Case 5: Learning Dashboard

Hiển thị overview về tiến độ học tập:

```javascript
async function loadLearningDashboard() {
  const token = localStorage.getItem('access_token');
  
  try {
    // Get progress statistics
    const statsResponse = await fetch('http://localhost:8000/api/progress/statistics', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    
    // Get saved vocabulary statistics
    const savedStatsResponse = await fetch('http://localhost:8000/api/saved-vocabularies/statistics', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    const stats = await statsResponse.json();
    const savedStats = await savedStatsResponse.json();
    
    if (stats.success && savedStats.success) {
      // Display dashboard
      const dashboard = {
        totalTopics: stats.data.total_topics_started,
        totalWords: stats.data.total_words_learned,
        masteryBreakdown: stats.data.mastery_breakdown,
        savedWords: savedStats.data.total_saved,
        needReview: savedStats.data.need_review,
        reviewedToday: savedStats.data.reviewed_today,
        recentActivity: stats.data.recent_activity
      };
      
      console.log('Learning Dashboard:', dashboard);
      renderDashboard(dashboard);
      
      return dashboard;
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

function renderDashboard(data) {
  document.getElementById('totalTopics').textContent = data.totalTopics;
  document.getElementById('totalWords').textContent = data.totalWords;
  document.getElementById('savedWords').textContent = data.savedWords;
  document.getElementById('needReview').textContent = data.needReview;
  
  // Render mastery breakdown chart
  renderMasteryChart({
    beginner: data.masteryBreakdown.beginner,
    intermediate: data.masteryBreakdown.intermediate,
    advanced: data.masteryBreakdown.advanced,
    mastered: data.masteryBreakdown.mastered
  });
  
  // Render recent activity
  data.recentActivity.forEach(activity => {
    addActivityItem(activity);
  });
}

// Usage
loadLearningDashboard();
```

---

### Use Case 6: Filter by HSK Level

Hiển thị progress theo HSK level:

```javascript
async function getProgressByLevel(level) {
  const token = localStorage.getItem('access_token');
  
  try {
    const response = await fetch(`http://localhost:8000/api/progress/level/${level}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    const data = await response.json();
    
    if (data.success) {
      console.log(`Progress for ${data.level}:`, data.data);
      
      // Display topics grouped by mastery level
      const grouped = {
        mastered: [],
        advanced: [],
        intermediate: [],
        beginner: []
      };
      
      data.data.forEach(topic => {
        grouped[topic.mastery_level].push(topic);
      });
      
      renderLevelProgress(data.level, grouped);
      
      return data.data;
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

// Usage - Load HSK1 progress
getProgressByLevel('hsk1');
```

---

### Use Case 7: Complete Learning Flow

Flow hoàn chỉnh từ khi user học một topic:

```javascript
class LearningSession {
  constructor(topicId) {
    this.topicId = topicId;
    this.token = localStorage.getItem('access_token');
    this.currentWordIndex = 0;
    this.vocabularies = [];
  }

  async start() {
    // 1. Load topic and progress
    await this.loadTopicProgress();
    
    // 2. Load vocabularies for this topic
    await this.loadVocabularies();
    
    // 3. Start learning
    this.showCurrentWord();
  }

  async loadTopicProgress() {
    const response = await fetch(`http://localhost:8000/api/progress/topic/${this.topicId}`, {
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json'
      }
    });
    
    const data = await response.json();
    this.progress = data.data;
    
    console.log(`Progress: ${this.progress.completed_words}/${this.progress.total_words}`);
  }

  async loadVocabularies() {
    const response = await fetch(`http://localhost:8000/api/topics/${this.topicId}/vocabularies`, {
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json'
      }
    });
    
    const data = await response.json();
    this.vocabularies = data.data;
  }

  showCurrentWord() {
    const word = this.vocabularies[this.currentWordIndex];
    displayWordCard(word);
  }

  async markCurrentWordLearned() {
    const word = this.vocabularies[this.currentWordIndex];
    
    // Update progress
    const response = await fetch(`http://localhost:8000/api/progress/topic/${this.topicId}`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        action: 'increment',
        vocabulary_id: word.id
      })
    });
    
    const data = await response.json();
    this.progress = data.data;
    
    // Show success animation
    showSuccessAnimation();
    updateProgressBar(this.progress.progress_percentage);
    
    // Move to next word
    this.nextWord();
  }

  async saveCurrentWord(notes) {
    const word = this.vocabularies[this.currentWordIndex];
    
    await fetch('http://localhost:8000/api/saved-vocabularies', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        vocabulary_id: word.id,
        notes: notes
      })
    });
    
    showNotification('Word saved for review!');
  }

  nextWord() {
    this.currentWordIndex++;
    if (this.currentWordIndex < this.vocabularies.length) {
      this.showCurrentWord();
    } else {
      this.completeTopic();
    }
  }

  completeTopic() {
    showCompletionScreen({
      topicName: this.progress.topic.name,
      wordsLearned: this.progress.completed_words,
      totalWords: this.progress.total_words,
      masteryLevel: this.progress.mastery_level
    });
  }
}

// Usage
const session = new LearningSession(123);
session.start();

// When user clicks "I learned this word"
document.getElementById('learnedBtn').onclick = () => {
  session.markCurrentWordLearned();
};

// When user clicks "Save for later"
document.getElementById('saveBtn').onclick = () => {
  const notes = prompt('Add a note (optional):');
  session.saveCurrentWord(notes);
  session.nextWord();
};
```

---

## Best Practices

### 1. Progress Tracking
- Gọi `GET /api/progress/topic/{id}` khi user bắt đầu học topic
- Gọi `PUT /api/progress/topic/{id}` với `action=increment` mỗi khi user hoàn thành một từ
- Hiển thị progress bar và mastery level badge real-time

### 2. Saved Vocabularies
- Cho phép user save từ ngay trong learning session
- Hiển thị số từ cần review trên dashboard
- Implement spaced repetition: từ không review trong 3+ ngày sẽ xuất hiện trong daily review

### 3. Performance
- Cache progress data locally
- Batch update progress nếu có thể
- Lazy load vocabularies (pagination)

### 4. User Experience
- Show progress percentage và mastery level
- Celebrate milestones (50%, 100%, mastered)
- Daily review reminders
- Streak tracking

### 5. Offline Support
- Store progress updates trong queue khi offline
- Sync khi có internet trở lại
- Show offline indicator

---

## Error Handling

### Common Errors

**401 Unauthorized**
```json
{
  "message": "Unauthenticated."
}
```
→ Token expired hoặc invalid, redirect to login

**404 Not Found**
```json
{
  "success": false,
  "message": "Topic not found"
}
```
→ Topic hoặc vocabulary không tồn tại

**409 Conflict**
```json
{
  "success": false,
  "message": "Vocabulary already saved"
}
```
→ Từ đã được save rồi

**422 Validation Error**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "action": ["The action field is required."]
  }
}
```
→ Invalid input data

---

## Testing with Postman

1. **Setup Environment Variables**:
   - `base_url`: `http://localhost:8000`
   - `token`: Your access token from login

2. **Test Progress Flow**:
   - GET Progress → PUT Update → GET Statistics

3. **Test Saved Vocabularies**:
   - POST Save → GET List → POST Review → DELETE

---

## Changelog

### Version 1.0.0 (2025-11-16)
- Initial release
- User progress tracking by topic
- Saved vocabularies management
- Learning statistics
- Mastery level system
- Review system with spaced repetition
