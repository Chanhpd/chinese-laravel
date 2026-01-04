# Sequence Diagram - Chinese Learning Platform

## Main Features Sequence Flows

### 1. Vocabulary Learning (Học từ vựng)
### 2. Taking Exam (Làm bài thi)
### 3. Radical Character Writing Assessment (Chấm điểm viết Radical)

---

## PlantUML Diagrams

### 1. Vocabulary Learning Sequence

```plantuml
@startuml Vocabulary Learning Sequence

actor User
participant "Frontend" as FE
participant "API" as API
participant "TopicController" as TC
participant "VocabularyController" as VC
participant "UserTopicProgress" as UTP
database Database as DB

== Browse Topics ==
User -> FE: Open Learning
FE -> API: GET /api/topics
API -> TC: index()
TC -> DB: Query topics
DB --> TC: List
TC --> API: Response
API --> FE: JSON
FE --> User: Display topics

== Select Topic ==
User -> FE: Select Topic (id: 5)
FE -> API: GET /api/topics/5
API -> TC: show(5)
TC -> DB: Query topic
DB --> TC: Data
TC -> DB: Check progress
DB --> TC: Progress
TC --> API: Response
API --> FE: JSON
FE --> User: Display vocabularies

== Study Vocabulary ==
User -> FE: Select Vocabulary (id: 123)
FE -> API: GET /api/vocabularies/123
API -> VC: show(123)
VC -> DB: Query vocabulary
DB --> VC: Data
VC --> API: Response
API --> FE: JSON
FE --> User: Display word + audio

User -> FE: Click "Play Audio"
FE -> FE: Play audio

== Mark Completed ==
User -> FE: Mark as Completed
FE -> API: POST /api/progress/mark
API -> UTP: markCompleted()
UTP -> DB: Get/Create progress
DB --> UTP: Record
UTP -> UTP: Update progress
UTP -> DB: Save
DB --> UTP: Updated
UTP --> API: Response
API --> FE: JSON
FE --> User: Show progress

@enduml
```

### 2. Taking Exam Sequence

```plantuml
@startuml Taking Exam Sequence

actor User
participant "Frontend" as FE
participant "API" as API
participant "ExamController" as EC
participant "UserExamAttempt" as UEA
participant "UserAnswer" as UA
database Database as DB

== Browse and Select Exam ==
User -> FE: Open Exam Section
FE -> API: GET /api/exams?level=HSK1
API -> EC: index()
EC -> DB: Query active exams
DB --> EC: Exams list
EC --> API: Response
API --> FE: JSON
FE --> User: Display exams

User -> FE: Select Exam (id: 1)
FE -> API: GET /api/exams/1
API -> EC: show(1)
EC -> DB: Query exam details
DB --> EC: Exam data
EC --> API: Response
API --> FE: JSON
FE --> User: Display exam overview

== Start Exam ==
User -> FE: Click "Start Exam"
FE -> API: POST /api/exams/1/start
API -> UEA: Create attempt
UEA -> DB: Insert UserExamAttempt
DB --> UEA: Created (id: 999)
UEA --> API: Response
API --> FE: JSON
FE -> FE: Start timer
FE --> User: Show questions

== Answer Questions ==
loop For each question
    User -> FE: Select answer
    FE -> API: POST /api/exam-attempts/999/answers
    API -> UA: store()
    UA -> DB: Insert UserAnswer
    DB --> UA: Saved
    UA --> API: Success
    API --> FE: Response
    FE --> User: Answer recorded
end

== Submit and Score ==
User -> FE: Click "Submit"
FE -> API: POST /api/exam-attempts/999/submit
API -> UEA: submitExam(999)
UEA -> DB: Get attempt with answers
DB --> UEA: Data

loop For each answer
    UEA -> DB: Get correct answer
    DB --> UEA: Correct answer
    UEA -> UEA: Compare and score
    UEA -> DB: Update UserAnswer
    DB --> UEA: Updated
end

UEA -> UEA: Calculate total score
UEA -> DB: Save attempt
DB --> UEA: Saved
UEA --> API: Results
API --> FE: JSON
FE --> User: Display score

User -> FE: Return to dashboard

@enduml
```

### 3. Radical Character Writing Assessment Sequence

```plantuml
@startuml Radical Writing Assessment Sequence

actor User
participant "Frontend" as FE
participant "API" as API
participant "RadicalController" as RC
participant "AssessmentService" as WAS
participant "OpenAI Vision" as AI
participant "UserLevelProgress" as ULP
database Database as DB

== Browse and Select ==
User -> FE: Open Radical Learning
FE -> API: GET /api/radicals?level_id=1
API -> RC: index()
RC -> DB: Query radicals
DB --> RC: List
RC --> API: Response
API --> FE: JSON
FE --> User: Display radicals

User -> FE: Select Radical (id: 50)
FE -> API: GET /api/radicals/50
API -> RC: show(50)
RC -> DB: Query radical
DB --> RC: Data
RC --> API: Response
API --> FE: JSON
FE --> User: Display radical + animation

== Practice Writing ==
User -> FE: Click "Practice"
FE -> FE: Initialize Canvas
User -> FE: Draw character
FE -> FE: Capture strokes
User -> FE: Submit
FE -> FE: Convert to image

== AI Assessment ==
FE -> API: POST /api/radicals/50/assess
API -> WAS: assess(image, radical_id)
WAS -> DB: Get radical
DB --> WAS: Data
WAS -> AI: Analyze image
AI --> WAS: Score + feedback
WAS -> WAS: Calculate final score
WAS -> DB: Save result
DB --> WAS: Saved

alt Score >= 60
    WAS -> ULP: Update progress
    ULP -> DB: Get progress
    DB --> ULP: Data
    ULP -> ULP: Mark completed
    ULP -> DB: Save
    DB --> ULP: Updated
    ULP --> WAS: Done
end

WAS --> API: Results
API --> FE: JSON
FE --> User: Display score

User -> FE: Return

@enduml
```

---

## Mermaid Diagrams

### 1. Vocabulary Learning Sequence

```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend
    participant API as API Controller
    participant TC as TopicController
    participant VC as VocabularyController
    participant UTP as UserTopicProgress
    participant DB as Database

    Note over User,DB: Browse and Select Topic
    User->>FE: Open Learning Section
    FE->>API: GET /api/topics?is_active=1
    API->>TC: index()
    TC->>DB: Query active topics
    DB-->>TC: Return topics list
    TC-->>API: Topics with vocab count
    API-->>FE: JSON response
    FE-->>User: Display topics list

    User->>FE: Select Topic (id: 5)
    FE->>API: GET /api/topics/5
    API->>TC: show(5)
    TC->>DB: Query topic with vocabularies
    DB-->>TC: Topic details
    TC->>DB: Check UserTopicProgress
    DB-->>TC: User progress data
    TC-->>API: Complete topic data
    API-->>FE: JSON response
    FE-->>User: Display vocabulary list

    Note over User,DB: Study Vocabulary
    User->>FE: Select Vocabulary (id: 123)
    FE->>API: GET /api/vocabularies/123
    API->>VC: show(123)
    VC->>DB: Query vocabulary with translations
    DB-->>VC: Vocabulary details
    VC-->>API: Full vocabulary data
    API-->>FE: JSON response
    FE-->>User: Display word, translations, audio

    User->>FE: Click "Play Audio"
    FE->>FE: Play pronunciation_audio

    Note over User,DB: Mark as Completed
    User->>FE: Click "Mark as Completed"
    FE->>API: POST /api/user-topic-progress/mark-completed
    API->>UTP: markWordCompleted()
    UTP->>DB: Get/Create UserTopicProgress
    DB-->>UTP: Progress record
    
    UTP->>UTP: Increment completed_words
    UTP->>UTP: Calculate progress and mastery level
    UTP->>UTP: Update last_studied_at
    UTP->>DB: Save UserTopicProgress
    DB-->>UTP: Updated
    UTP-->>API: Progress updated
    API-->>FE: JSON response
    FE-->>User: Show updated progress
```

### 2. Taking Exam Sequence

```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend
    participant API as API Controller
    participant EC as ExamController
    participant UEA as UserExamAttempt
    participant UA as UserAnswer
    participant DB as Database

    Note over User,DB: Browse and Select Exam
    User->>FE: Open Exam Section
    FE->>API: GET /api/exams?level=HSK1
    API->>EC: index()
    EC->>DB: Query active exams
    DB-->>EC: Exams list
    EC-->>API: Formatted data
    API-->>FE: JSON response
    FE-->>User: Display available exams

    User->>FE: Select Exam (id: 1)
    FE->>API: GET /api/exams/1
    API->>EC: show(1)
    EC->>DB: Query exam with all parts/questions
    DB-->>EC: Complete exam structure
    EC-->>API: Full exam data
    API-->>FE: JSON response
    FE-->>User: Display exam overview

    Note over User,DB: Start Exam
    User->>FE: Click "Start Exam"
    FE->>API: POST /api/exams/1/start
    API->>EC: startExam(1)
    EC->>UEA: Create new attempt
    
    UEA->>UEA: Set exam_id, user_id<br/>status = 'in_progress'<br/>started_at = now()
    UEA->>DB: Insert UserExamAttempt
    DB-->>UEA: Attempt created (id: 999)
    UEA-->>EC: Attempt ID
    EC-->>API: Attempt created
    API-->>FE: JSON {attempt_id: 999, ...}
    FE->>FE: Start timer
    FE-->>User: Show exam questions

    Note over User,DB: Answer Questions
    loop For each question
        User->>FE: View question
        
        opt Question has audio
            FE->>FE: Play Q_audio
            User->>User: Listen
        end
        
        User->>FE: Select answer(s)
        FE->>API: POST /api/exam-attempts/999/answers
        API->>UA: store()
        UA->>UA: Set attempt_id, question_content_id<br/>user_answer, answered_at
        UA->>DB: Insert UserAnswer
        DB-->>UA: Answer saved
        UA-->>API: Success
        API-->>FE: Answer recorded
        FE-->>User: Show answer selected
    end

    Note over User,DB: Submit Exam
    User->>FE: Click "Submit Exam"
    FE->>FE: Confirm submission
    User->>FE: Confirm
    FE->>API: POST /api/exam-attempts/999/submit
    API->>UEA: submitExam(999)
    UEA->>DB: Get attempt with all answers
    DB-->>UEA: Attempt + answers
    UEA->>UEA: Stop timer, calculate time_spent

    Note over User,DB: Scoring Process
    UEA->>UEA: Initialize total_score, max_score
    
    loop For each UserAnswer
        UEA->>DB: Get QuestionContent + correct_answers
        DB-->>UEA: Question data
        UEA->>UEA: Compare user_answer vs correct_answers
        
        alt Correct
            UEA->>UEA: is_correct = true<br/>score_earned = question_score
        else Incorrect
            UEA->>UEA: is_correct = false<br/>score_earned = 0
        end
        
        UEA->>DB: Update UserAnswer
        DB-->>UEA: Updated
        UEA->>UEA: Accumulate scores
    end
    
    UEA->>UEA: Calculate percentage<br/>(total_score / max_score) * 100
    UEA->>UEA: Update: completed_at, status='completed'<br/>total_score, percentage
    UEA->>DB: Save UserExamAttempt
    DB-->>UEA: Saved
    UEA-->>API: Scoring completed
    API-->>FE: JSON with results
    FE-->>User: Display results:<br/>Score, Percentage, Time
    User->>FE: Return to dashboard
```

### 3. Radical Character Writing Assessment Sequence

```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend
    participant API as API Controller
    participant RC as RadicalController
    participant WAS as WritingAssessmentService
    participant AI as OpenAI Vision API
    participant ULP as UserLevelProgress
    participant DB as Database

    Note over User,DB: Browse and Select Radical
    User->>FE: Open Radical Learning
    FE->>API: GET /api/radicals?level_id=1
    API->>RC: index()
    RC->>DB: Query radicals by level
    DB-->>RC: Radicals list
    RC-->>API: Formatted data
    API-->>FE: JSON response
    FE-->>User: Display radicals

    User->>FE: Select Radical (id: 50)
    FE->>API: GET /api/radicals/50
    API->>RC: show(50)
    RC->>DB: Query radical details
    DB-->>RC: hanzi, pinyin, stroke_count, etc
    RC-->>API: Complete radical info
    API-->>FE: JSON response
    FE-->>User: Display radical + stroke order animation

    Note over User,DB: Practice Writing
    User->>FE: Click "Practice Writing"
    FE->>FE: Initialize Canvas<br/>Set grid, drawing tools
    User->>FE: Draw character stroke by stroke
    FE->>FE: Capture stroke paths
    User->>FE: Click "Submit for Assessment"
    FE->>FE: Convert canvas to base64 image

    Note over User,DB: AI Assessment
    FE->>API: POST /api/radicals/50/assess-writing<br/>{image: base64, user_id}
    API->>WAS: assessWriting(radical_id, image, user_id)
    WAS->>DB: Get Radical details
    DB-->>WAS: Correct hanzi + metadata

    WAS->>AI: POST to OpenAI Vision API<br/>Prompt: Analyze character<br/>Compare with: {hanzi}<br/>Evaluate 4 criteria
    AI->>AI: Process image<br/>Analyze features<br/>Compare with reference
    AI-->>WAS: JSON response:<br/>- recognized_char<br/>- match_score: 85<br/>- stroke_order: 90<br/>- quality: 80<br/>- structure: 85<br/>- overall: 85<br/>- feedback

    Note over User,DB: Calculate Final Score
    WAS->>WAS: Aggregate AI results<br/>Calculate score (0-100) and Grade<br/>Generate detailed feedback

    Note over User,DB: Save Assessment Result
    WAS->>DB: Insert assessment_result<br/>(user_id, radical_id, image, score, grade, feedback)
    DB-->>WAS: Result saved (id: 777)

    alt Score >= 60 (Pass)
        WAS->>ULP: Update progress
        ULP->>DB: Get UserLevelProgress
        DB-->>ULP: Progress record
        ULP->>ULP: Mark radical completed<br/>Calculate progress and mastery
        ULP->>DB: Save UserLevelProgress
        DB-->>ULP: Updated
        ULP-->>WAS: Progress updated
    else Score < 60 (Fail)
        WAS->>WAS: Suggest retry
    end

    WAS-->>API: Assessment complete
    API-->>FE: JSON response:<br/>- result_id: 777<br/>- score: 85<br/>- grade: "Good"<br/>- feedback<br/>- comparison_image<br/>- progress_updated: true

    Note over User,DB: Display Results
    FE-->>User: Show assessment:<br/>- Score<br/>- Grade<br/>- Visual comparison<br/>- Detailed feedback
    User->>FE: Return to dashboard
```

---

## Giải thích Sequence Diagrams

### 1. Vocabulary Learning Sequence
**Actors/Components:**
- User → Frontend → API → Controllers (Topic, Vocabulary, UserTopicProgress, SavedVocabulary, UserStreak) → Database

**Key Flows:**
1. **Browse Topics**: User requests topics → System queries active topics → Returns with vocabulary counts
2. **Study Vocabulary**: Select vocab → Load details with translations → Play audio → Save to favorites (optional)
3. **Mark Completed**: Update UserTopicProgress → Calculate progress % → Determine mastery level
4. **Review Saved**: Load saved vocabularies → Review → Update review_count
5. **Update Streak**: Check-in → Validate consecutive days → Update streak_count → Save weekly data

**Database Operations:**
- Query: topics, vocabularies, translations, user progress
- Insert: saved_vocabulary records
- Update: UserTopicProgress (progress, mastery), UserStreak (streak data)

### 2. Taking Exam Sequence
**Actors/Components:**
- User → Frontend → API → ExamController → UserExamAttempt → UserAnswer → Database

**Key Flows:**
1. **Browse Exams**: Filter by level → Query active exams → Return with parts/questions count
2. **Start Exam**: Create UserExamAttempt → Set status='in_progress' → Start timer
3. **Answer Questions**: Loop through questions → For each: Display → User answers → Save UserAnswer
4. **Submit Exam**: Stop timer → Calculate time_spent
5. **Scoring**: Loop through answers → Compare with correct_answers → Calculate is_correct and score_earned → Accumulate total_score → Calculate percentage
6. **View Results**: Show score summary → Optionally view detailed results with explanations

**Database Operations:**
- Query: exams, exam_parts, questions, question_contents, correct_answers
- Insert: UserExamAttempt, UserAnswer records
- Update: UserAnswer (is_correct, score_earned), UserExamAttempt (completed_at, status, scores)

### 3. Radical Writing Assessment Sequence
**Actors/Components:**
- User → Frontend → API → RadicalController → WritingAssessmentService → OpenAI Vision API → UserLevelProgress → Database

**Key Flows:**
1. **Browse Radicals**: Filter by HSK level → Query radicals → Display with details
2. **Practice Writing**: Initialize canvas → User draws character → Capture strokes → Allow redo
3. **Submit for Assessment**: Convert canvas to base64 image → Send to backend
4. **AI Assessment**: 
   - Send image + prompt to OpenAI Vision API
   - AI analyzes 4 criteria: Recognition, Stroke Order, Quality, Structure
   - Returns scores and feedback
5. **Calculate Final Score**: Aggregate AI results → Determine grade (Excellent/Good/Fair/Needs Improvement)
6. **Generate Feedback**: Create detailed feedback with specific improvement suggestions
7. **Save Result**: Store assessment_result with image, score, grade, feedback
8. **Update Progress**: If pass (≥60%) → Mark radical completed → Update UserLevelProgress → Calculate mastery level
9. **Display Results**: Show visual comparison (user's vs correct) → Detailed feedback → Scores breakdown

**External Service:**
- **OpenAI Vision API**: Analyzes handwritten character image, provides recognition and quality assessment

**Database Operations:**
- Query: radicals (by level), UserLevelProgress
- Insert: assessment_result record
- Update: UserLevelProgress (completed_radicals, progress %, mastery_level)

---

## Key Patterns in All Sequences

### 1. **Request-Response Flow**
- User action → Frontend → API → Controller → Service/Model → Database → Response chain

### 2. **Progress Tracking**
- All 3 features update user progress automatically
- Calculate completion percentage
- Determine mastery level based on thresholds

### 3. **Multi-language Support**
- Translations loaded from separate translation tables
- User's preferred language determines which translation is shown

### 4. **Validation & Error Handling**
- Check permissions (is_active, user authentication)
- Validate data before processing
- Return appropriate error responses

### 5. **Timestamps**
- Record action timestamps (answered_at, last_studied_at, completed_at)
- Track time spent on activities

### 6. **Scoring Logic**
- Vocabulary: Binary (completed or not)
- Exam: Compare answers, calculate score/percentage
- Radical Writing: AI-based scoring with multiple criteria (0-100 scale)
