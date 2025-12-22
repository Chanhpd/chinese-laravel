# Exam Database Schema Design

## Database Tables

### 1. **exams** (Bài thi HSK)
```sql
CREATE TABLE exams (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    level VARCHAR(50) NOT NULL, -- 'HSK1', 'HSK2', etc.
    total_time INT NOT NULL, -- Total minutes
    total_score INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 2. **exam_parts** (Phần thi: Listening, Reading, Writing)
```sql
CREATE TABLE exam_parts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    exam_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL, -- 'Listening', 'Reading', 'Writing'
    part_order INT NOT NULL, -- Thứ tự phần
    time INT NOT NULL, -- Minutes for this part
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);
```

### 3. **question_types** (Loại câu hỏi: 110001, 110002, etc.)
```sql
CREATE TABLE question_types (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    exam_part_id BIGINT UNSIGNED NOT NULL,
    kind VARCHAR(50) NOT NULL, -- '110001', '110002', etc.
    type_order INT NOT NULL, -- Thứ tự trong part
    instruction TEXT, -- Hướng dẫn làm bài
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (exam_part_id) REFERENCES exam_parts(id) ON DELETE CASCADE
);
```

### 4. **questions** (Câu hỏi chính)
```sql
CREATE TABLE questions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    question_type_id BIGINT UNSIGNED NOT NULL,
    kind VARCHAR(50) NOT NULL, -- Duplicate for easier query
    question_order INT NOT NULL,
    score INT NOT NULL DEFAULT 5,
    
    -- General section (shared across sub-questions)
    general_text TEXT, -- G_text
    general_text_audio TEXT, -- G_text_audio (Chinese text with pinyin)
    general_audio VARCHAR(500), -- G_audio URL
    general_image VARCHAR(500), -- G_image URL
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (question_type_id) REFERENCES question_types(id) ON DELETE CASCADE
);
```

### 5. **question_translations** (Bản dịch general section)
```sql
CREATE TABLE question_translations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    question_id BIGINT UNSIGNED NOT NULL,
    language_code VARCHAR(10) NOT NULL, -- 'vi', 'en', 'fr', etc.
    general_text_translate TEXT,
    general_text_audio_translate TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_translation (question_id, language_code)
);
```

### 6. **question_contents** (Nội dung câu hỏi chi tiết - content array)
```sql
CREATE TABLE question_contents (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    question_id BIGINT UNSIGNED NOT NULL,
    content_order INT NOT NULL,
    
    -- Question data
    question_text TEXT,
    question_audio VARCHAR(500),
    question_image VARCHAR(500),
    
    -- Answer options (stored as JSON for flexibility)
    answer_texts JSON, -- ["对", "错"] or ["A", "B", "C", "D"]
    answer_audios JSON, -- URLs for audio answers
    answer_images JSON, -- URLs for image answers
    
    -- Correct answers
    correct_answers JSON, -- ["1"] or ["2", "3"]
    more_correct_answers JSON, -- Alternative correct answers
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);
```

### 7. **question_explanations** (Giải thích đáp án)
```sql
CREATE TABLE question_explanations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    question_content_id BIGINT UNSIGNED NOT NULL,
    language_code VARCHAR(10) NOT NULL, -- 'vi', 'en', 'fr', etc.
    explanation TEXT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (question_content_id) REFERENCES question_contents(id) ON DELETE CASCADE,
    UNIQUE KEY unique_explanation (question_content_id, language_code)
);
```

### 8. **user_exam_attempts** (Lượt thi của user)
```sql
CREATE TABLE user_exam_attempts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    exam_id BIGINT UNSIGNED NOT NULL,
    
    started_at TIMESTAMP,
    completed_at TIMESTAMP,
    time_spent INT, -- Minutes
    
    total_score INT NOT NULL DEFAULT 0,
    max_score INT NOT NULL,
    percentage DECIMAL(5,2),
    
    status VARCHAR(50) NOT NULL DEFAULT 'in_progress', -- 'in_progress', 'completed', 'abandoned'
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);
```

### 9. **user_answers** (Câu trả lời của user)
```sql
CREATE TABLE user_answers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    attempt_id BIGINT UNSIGNED NOT NULL,
    question_content_id BIGINT UNSIGNED NOT NULL,
    
    user_answer JSON, -- ["1"] or ["2", "3"]
    is_correct BOOLEAN NOT NULL DEFAULT FALSE,
    score_earned INT NOT NULL DEFAULT 0,
    time_spent INT, -- Seconds for this question
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES user_exam_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_content_id) REFERENCES question_contents(id) ON DELETE CASCADE,
    UNIQUE KEY unique_answer (attempt_id, question_content_id)
);
```

## Relationships Summary

```
exams (1) → (*) exam_parts
exam_parts (1) → (*) question_types
question_types (1) → (*) questions
questions (1) → (*) question_translations
questions (1) → (*) question_contents
question_contents (1) → (*) question_explanations

users (1) → (*) user_exam_attempts
exams (1) → (*) user_exam_attempts
user_exam_attempts (1) → (*) user_answers
question_contents (1) → (*) user_answers
```

## Example Data Flow

### HSK 1 Test Structure:
```
Exam: "HSK 1 Test 1" (35 minutes)
  ├─ Part 1: Listening (18 minutes)
  │   ├─ Type 110001: True/False with image
  │   │   ├─ Question 1 (General: "三个杯子")
  │   │   │   └─ Content 1: Audio + Image → 对/错
  │   │   └─ Question 2 (General: "喝茶")
  │   │       └─ Content 1: Audio + Image → 对/错
  │   ├─ Type 110002: Match dialogue to image
  │   └─ Type 110003: True/False from dialogue
  │
  └─ Part 2: Reading (17 minutes)
      ├─ Type 210001: Match word to image
      ├─ Type 210002: Fill in blank
      └─ Type 210003: Dialogue comprehension
```

## Migration Order

1. `exams`
2. `exam_parts`
3. `question_types`
4. `questions`
5. `question_translations`
6. `question_contents`
7. `question_explanations`
8. `user_exam_attempts`
9. `user_answers`

## Indexes for Performance

```sql
-- Frequently queried relationships
CREATE INDEX idx_exam_parts_exam_id ON exam_parts(exam_id);
CREATE INDEX idx_question_types_part_id ON question_types(exam_part_id);
CREATE INDEX idx_questions_type_id ON questions(question_type_id);
CREATE INDEX idx_question_contents_question_id ON question_contents(question_id);
CREATE INDEX idx_user_attempts_user_id ON user_exam_attempts(user_id);
CREATE INDEX idx_user_attempts_exam_id ON user_exam_attempts(exam_id);
CREATE INDEX idx_user_answers_attempt_id ON user_answers(attempt_id);

-- Filter by status and date
CREATE INDEX idx_user_attempts_status ON user_exam_attempts(status);
CREATE INDEX idx_user_attempts_completed ON user_exam_attempts(completed_at);
CREATE INDEX idx_exams_active ON exams(is_active);
```
