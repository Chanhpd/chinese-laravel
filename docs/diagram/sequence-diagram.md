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
participant "API Controller" as API
participant "TopicController" as TC
participant "VocabularyController" as VC
participant "UserTopicProgress" as UTP
participant "SavedVocabulary" as SV
participant "UserStreak" as US
database Database as DB

== Browse and Select Topic ==
User -> FE: Open Learning Section
FE -> API: GET /api/topics?is_active=1
API -> TC: index()
TC -> DB: Query active topics
DB --> TC: Return topics list
TC --> API: Topics with vocabularies count
API --> FE: JSON response
FE --> User: Display topics list

User -> FE: Select Topic (id: 5)
FE -> API: GET /api/topics/5
API -> TC: show(5)
TC -> DB: Query topic with vocabularies
DB --> TC: Topic details + vocabularies
TC -> DB: Check UserTopicProgress for user
DB --> TC: User progress data
TC --> API: Complete topic data
API --> FE: JSON response
FE --> User: Display topic details\nand vocabulary list

== Study Vocabulary ==
User -> FE: Select Vocabulary (id: 123)
FE -> API: GET /api/vocabularies/123
API -> VC: show(123)
VC -> DB: Query vocabulary with translations
DB --> VC: Vocabulary details
VC --> API: Full vocabulary data
API --> FE: JSON response
FE --> User: Display word, translations,\nexamples, audio

User -> FE: Click "Play Audio"
FE -> FE: Play pronunciation_audio

User -> FE: Click "Save Vocabulary"
FE -> API: POST /api/saved-vocabularies\n{vocabulary_id: 123, notes: "..."}
API -> SV: store()
SV -> DB: Insert saved_vocabulary record
DB --> SV: Record created
SV --> API: Success response
API --> FE: JSON response
FE --> User: Show "Saved successfully"

== Mark as Completed ==
User -> FE: Click "Mark as Completed"
FE -> API: POST /api/user-topic-progress/mark-completed\n{topic_id: 5, vocabulary_id: 123}
API -> UTP: markWordCompleted()

UTP -> DB: Get or create UserTopicProgress\nfor user_id and topic_id
DB --> UTP: Progress record

UTP -> UTP: Increment completed_words
UTP -> UTP: Calculate progress percentage\n(completed_words / total_words * 100)

alt Progress >= 90%
    UTP -> UTP: Set mastery_level = 'mastered'
else Progress >= 70%
    UTP -> UTP: Set mastery_level = 'advanced'
else Progress >= 40%
    UTP -> UTP: Set mastery_level = 'intermediate'
else
    UTP -> UTP: Set mastery_level = 'beginner'
end

UTP -> UTP: Update last_studied_at = now()
UTP -> DB: Save UserTopicProgress
DB --> UTP: Record updated
UTP --> API: Progress updated
API --> FE: JSON response
FE --> User: Show updated progress

== Review Saved Vocabularies ==
User -> FE: Click "Review Saved Words"
FE -> API: GET /api/saved-vocabularies
API -> SV: index()
SV -> DB: Query saved vocabularies\nwith vocabulary details
DB --> SV: Saved vocabularies list
SV --> API: Formatted data
API --> FE: JSON response
FE --> User: Display saved words list

User -> FE: Review a saved word
FE -> API: POST /api/saved-vocabularies/{id}/review
API -> SV: markAsReviewed(id)
SV -> DB: Increment review_count\nUpdate last_reviewed_at
DB --> SV: Record updated
SV --> API: Success response
API --> FE: JSON response
FE --> User: Show review recorded

== Update Streak ==
User -> FE: Complete study session
FE -> API: POST /api/streak/check-in
API -> US: performCheckIn()

US -> DB: Get UserStreak for user
DB --> US: Streak record

US -> US: Check if already checked in today
alt Not checked in today
    US -> US: Check if consecutive day
    alt Consecutive day
        US -> US: Increment streak_count
    else Not consecutive
        US -> US: Reset streak_count = 1
    end
    US -> US: Update last_check_in_date = today
    US -> US: Increment total_check_in_days
    US -> US: Update weekly_check_ins JSON
    US -> US: Check if new longest_streak
    US -> DB: Save UserStreak
    DB --> US: Record updated
    US --> API: Streak updated
else Already checked in
    US --> API: Already checked in today
end

API --> FE: JSON response
FE --> User: Show streak status

@enduml
```

### 2. Taking Exam Sequence

```plantuml
@startuml Taking Exam Sequence

actor User
participant "Frontend" as FE
participant "API Controller" as API
participant "ExamController" as EC
participant "UserExamAttempt" as UEA
participant "UserAnswer" as UA
participant "Exam" as E
participant "Question" as Q
participant "QuestionContent" as QC
database Database as DB

== Browse and Select Exam ==
User -> FE: Open Exam Section
FE -> API: GET /api/exams?level=HSK1&is_active=1
API -> EC: index()
EC -> DB: Query active exams\nwith parts and questions count
DB --> EC: Exams list
EC --> API: Formatted exam data
API --> FE: JSON response
FE --> User: Display available exams

User -> FE: Select Exam (id: 1)
FE -> API: GET /api/exams/1
API -> EC: show(1)
EC -> DB: Query exam with all details:\n- exam_parts\n- questions\n- question_types\n- question_contents
DB --> EC: Complete exam structure
EC --> API: Full exam data
API --> FE: JSON response
FE --> User: Display exam overview\n(title, time, parts, questions count)

== Start Exam ==
User -> FE: Click "Start Exam"
FE -> API: POST /api/exams/1/start
API -> EC: startExam(1)
EC -> UEA: Create new attempt

UEA -> UEA: Set exam_id = 1\nSet user_id = auth()->id()\nSet status = 'in_progress'\nSet started_at = now()
UEA -> DB: Insert UserExamAttempt
DB --> UEA: Attempt created (id: 999)
UEA --> EC: Attempt ID
EC --> API: Attempt created
API --> FE: JSON response\n{attempt_id: 999, started_at, ...}
FE -> FE: Start timer
FE --> User: Show exam questions

== Answer Questions ==
loop For each question
    User -> FE: View question (Listening/Reading)
    
    alt Question has audio
        FE -> FE: Load and play Q_audio
        User -> User: Listen to audio
    end
    
    User -> User: Read question and options
    User -> FE: Select answer(s)
    
    FE -> API: POST /api/exam-attempts/999/answers\n{question_content_id: 45,\nuser_answer: ["2"],\nanswered_at: "..."}
    API -> UA: store()
    
    UA -> UA: Set attempt_id = 999\nSet question_content_id = 45\nSet user_answer = ["2"]\nSet answered_at = now()
    UA -> DB: Insert UserAnswer
    DB --> UA: Answer saved
    UA --> API: Success
    API --> FE: Answer recorded
    FE --> User: Show answer selected
end

== Submit Exam ==
User -> FE: Click "Submit Exam"
FE -> FE: Confirm submission
User -> FE: Confirm "Yes"

FE -> API: POST /api/exam-attempts/999/submit
API -> UEA: submitExam(999)

UEA -> DB: Get UserExamAttempt with all answers
DB --> UEA: Attempt + answers

UEA -> UEA: Stop timer\nCalculate time_spent

== Scoring Process ==
UEA -> UEA: Initialize:\ntotal_score = 0\nmax_score = 0

loop For each UserAnswer
    UEA -> DB: Get QuestionContent\nwith correct_answers
    DB --> UEA: Question data
    
    UEA -> UEA: Compare user_answer\nwith correct_answers
    
    alt Answer is correct
        UEA -> UEA: is_correct = true\nscore_earned = question_score
    else Answer is incorrect
        UEA -> UEA: is_correct = false\nscore_earned = 0
    end
    
    UEA -> DB: Update UserAnswer:\nis_correct, score_earned
    DB --> UEA: Updated
    
    UEA -> UEA: total_score += score_earned\nmax_score += question_score
end

UEA -> UEA: Calculate percentage:\n(total_score / max_score) * 100

UEA -> UEA: Update attempt:\ncompleted_at = now()\nstatus = 'completed'\ntotal_score\npercentage\ntime_spent

UEA -> DB: Save UserExamAttempt
DB --> UEA: Saved

UEA --> API: Scoring completed
API --> FE: JSON response with results
FE --> User: Display exam results:\n- Total score: X/Y\n- Percentage: Z%\n- Time spent: MM:SS\n- Status: Pass/Fail

== View Detailed Results ==
User -> FE: Click "View Details"
FE -> API: GET /api/exam-attempts/999/details
API -> UEA: getDetails(999)

UEA -> DB: Query attempt with:\n- All user_answers\n- Question contents\n- Correct answers\n- Explanations (translations)
DB --> UEA: Complete results data

UEA --> API: Detailed results
API --> FE: JSON response
FE --> User: Display:\n- Each question\n- User's answer (highlight)\n- Correct answer (highlight)\n- Explanation in user's language\n- Score earned per question

User -> FE: Review mistakes
User -> FE: Read explanations
User -> FE: Return to dashboard

@enduml
```

### 3. Radical Character Writing Assessment Sequence

```plantuml
@startuml Radical Writing Assessment Sequence

actor User
participant "Frontend" as FE
participant "API Controller" as API
participant "RadicalController" as RC
participant "WritingAssessmentService" as WAS
participant "OpenAI Vision API" as AI
participant "UserLevelProgress" as ULP
participant "AssessmentResult" as AR
database Database as DB

== Browse and Select Radical ==
User -> FE: Open Radical Learning
FE -> API: GET /api/radicals?level_id=1
API -> RC: index()
RC -> DB: Query radicals by level
DB --> RC: Radicals list
RC --> API: Formatted data
API --> FE: JSON response
FE --> User: Display radicals by HSK level

User -> FE: Select Radical (id: 50)
FE -> API: GET /api/radicals/50
API -> RC: show(50)
RC -> DB: Query radical details
DB --> RC: Radical data:\n- hanzi, pinyin, meaning\n- stroke_count, radical\n- stroke order data
RC --> API: Complete radical info
API --> FE: JSON response
FE --> User: Display radical details\nand stroke order animation

== Practice Writing ==
User -> FE: Click "Practice Writing"
FE -> FE: Initialize HTML5 Canvas\nSet grid guidelines\nEnable drawing tools

User -> FE: Draw character stroke by stroke
FE -> FE: Capture stroke paths\n(coordinates, timestamps)

alt Need to redo
    User -> FE: Click "Clear"
    FE -> FE: Clear canvas
    User -> FE: Draw again
end

User -> FE: Click "Submit for Assessment"
FE -> FE: Convert canvas to image\n(base64 PNG/JPEG)

== AI Assessment ==
FE -> API: POST /api/radicals/50/assess-writing\n{image: "data:image/png;base64,...",\nuser_id: 10}
API -> WAS: assessWriting(radical_id, image, user_id)

WAS -> DB: Get Radical details
DB --> WAS: Correct hanzi and metadata

WAS -> AI: POST to OpenAI Vision API\nPrompt: "Analyze this Chinese character\nCompare with: {hanzi}\nEvaluate:\n1. Character recognition\n2. Stroke order\n3. Stroke quality\n4. Overall structure\nProvide score 0-100 and detailed feedback"

AI -> AI: Process image\nAnalyze character features\nCompare with reference

AI --> WAS: JSON response:\n{\n  recognized_char: "字",\n  match_score: 85,\n  stroke_order_accuracy: 90,\n  stroke_quality: 80,\n  structure_score: 85,\n  overall_score: 85,\n  feedback: {...}\n}

== Calculate Final Score ==
WAS -> WAS: Aggregate AI results:\n- Character match: 85/100\n- Stroke order: 90/100\n- Stroke quality: 80/100\n- Structure: 85/100\n\nFinal Score: 85/100

alt Score >= 90
    WAS -> WAS: Grade = "Excellent"\nFeedback = "Perfect! Well done!"
else Score >= 75
    WAS -> WAS: Grade = "Good"\nFeedback = "Great job! Minor improvements needed"
else Score >= 60
    WAS -> WAS: Grade = "Fair"\nFeedback = "Keep practicing, focus on stroke order"
else Score < 60
    WAS -> WAS: Grade = "Needs Improvement"\nFeedback = "Practice more, review stroke order"
end

WAS -> WAS: Generate detailed feedback:\n- Recognized character: X\n- Correct strokes: Y/Z\n- Stroke order accuracy: N%\n- Proportion: Good/Fair/Poor\n- Balance: Centered/Off-center

== Save Assessment Result ==
WAS -> AR: Create result record
AR -> DB: Insert assessment_result:\n- user_id\n- radical_id\n- written_image (base64)\n- score: 85\n- grade: "Good"\n- feedback: JSON\n- ai_response: JSON\n- created_at
DB --> AR: Result saved (id: 777)

alt Score >= 60 (Pass)
    WAS -> ULP: Update progress
    ULP -> DB: Get UserLevelProgress\nfor user and level
    DB --> ULP: Progress record
    
    ULP -> ULP: Mark radical as completed\nIncrement completed_radicals\nCalculate progress %
    
    alt Progress >= 90%
        ULP -> ULP: mastery_level = 'mastered'
    else Progress >= 70%
        ULP -> ULP: mastery_level = 'advanced'
    else Progress >= 40%
        ULP -> ULP: mastery_level = 'intermediate'
    else
        ULP -> ULP: mastery_level = 'beginner'
    end
    
    ULP -> ULP: Update last_studied_at
    ULP -> DB: Save UserLevelProgress
    DB --> ULP: Updated
    ULP --> WAS: Progress updated
else Score < 60 (Fail)
    WAS -> WAS: Keep radical as incomplete\nSuggest retry
end

WAS --> API: Assessment complete
API --> FE: JSON response:\n{\n  result_id: 777,\n  score: 85,\n  grade: "Good",\n  feedback: {...},\n  comparison_image: "...",\n  progress_updated: true,\n  mastery_level: "advanced"\n}

== Display Results ==
FE --> User: Show assessment results:\n- Score: 85/100\n- Grade: Good\n- Visual comparison:\n  Left: User's writing\n  Right: Correct character\n- Detailed feedback:\n  ✓ Character recognized correctly\n  ✓ 9/10 strokes correct\n  ✓ Stroke order: 90% accurate\n  ⚠ Improve: Proportion slightly off\n  ⚠ Tip: Practice stroke 5 angle

User -> FE: Review feedback

alt Satisfied with result
    User -> FE: Click "Next Radical"
    FE -> API: GET /api/radicals?level_id=1&after=50
    API --> FE: Next radical
else Want to retry
    User -> FE: Click "Try Again"
    FE -> FE: Clear canvas\nReset for new attempt
    User -> FE: Draw again
end

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
    participant SV as SavedVocabulary
    participant US as UserStreak
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

    User->>FE: Click "Save Vocabulary"
    FE->>API: POST /api/saved-vocabularies
    API->>SV: store()
    SV->>DB: Insert saved_vocabulary
    DB-->>SV: Record created
    SV-->>API: Success
    API-->>FE: JSON response
    FE-->>User: Show "Saved successfully"

    Note over User,DB: Mark as Completed
    User->>FE: Click "Mark as Completed"
    FE->>API: POST /api/user-topic-progress/mark-completed
    API->>UTP: markWordCompleted()
    UTP->>DB: Get/Create UserTopicProgress
    DB-->>UTP: Progress record
    
    UTP->>UTP: Increment completed_words
    UTP->>UTP: Calculate progress %
    
    alt Progress >= 90%
        UTP->>UTP: mastery_level = 'mastered'
    else Progress >= 70%
        UTP->>UTP: mastery_level = 'advanced'
    else Progress >= 40%
        UTP->>UTP: mastery_level = 'intermediate'
    else
        UTP->>UTP: mastery_level = 'beginner'
    end
    
    UTP->>UTP: Update last_studied_at
    UTP->>DB: Save UserTopicProgress
    DB-->>UTP: Updated
    UTP-->>API: Progress updated
    API-->>FE: JSON response
    FE-->>User: Show updated progress

    Note over User,DB: Review Saved Vocabularies
    User->>FE: Click "Review Saved Words"
    FE->>API: GET /api/saved-vocabularies
    API->>SV: index()
    SV->>DB: Query saved vocabularies
    DB-->>SV: Saved vocabularies list
    SV-->>API: Formatted data
    API-->>FE: JSON response
    FE-->>User: Display saved words

    User->>FE: Review a saved word
    FE->>API: POST /api/saved-vocabularies/{id}/review
    API->>SV: markAsReviewed(id)
    SV->>DB: Update review_count, last_reviewed_at
    DB-->>SV: Updated
    SV-->>API: Success
    API-->>FE: JSON response
    FE-->>User: Show review recorded

    Note over User,DB: Update Streak
    User->>FE: Complete study session
    FE->>API: POST /api/streak/check-in
    API->>US: performCheckIn()
    US->>DB: Get UserStreak
    DB-->>US: Streak record
    
    US->>US: Check if already checked in today
    
    alt Not checked in today
        US->>US: Check if consecutive day
        alt Consecutive
            US->>US: Increment streak_count
        else Not consecutive
            US->>US: Reset streak_count = 1
        end
        US->>US: Update last_check_in_date<br/>total_check_in_days<br/>weekly_check_ins
        US->>DB: Save UserStreak
        DB-->>US: Updated
        US-->>API: Streak updated
    else Already checked in
        US-->>API: Already checked in today
    end
    
    API-->>FE: JSON response
    FE-->>User: Show streak status
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

    Note over User,DB: View Detailed Results
    User->>FE: Click "View Details"
    FE->>API: GET /api/exam-attempts/999/details
    API->>UEA: getDetails(999)
    UEA->>DB: Query attempt with all data<br/>(answers, questions, explanations)
    DB-->>UEA: Complete results
    UEA-->>API: Detailed results
    API-->>FE: JSON response
    FE-->>User: Display:<br/>- Each question<br/>- User's answer<br/>- Correct answer<br/>- Explanation<br/>- Score per question
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

    opt Need to redo
        User->>FE: Click "Clear"
        FE->>FE: Clear canvas
        User->>FE: Draw again
    end

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
    WAS->>WAS: Aggregate AI results<br/>Final Score: 85/100

    alt Score >= 90
        WAS->>WAS: Grade = "Excellent"
    else Score >= 75
        WAS->>WAS: Grade = "Good"
    else Score >= 60
        WAS->>WAS: Grade = "Fair"
    else
        WAS->>WAS: Grade = "Needs Improvement"
    end

    WAS->>WAS: Generate detailed feedback:<br/>- Recognized: X<br/>- Correct strokes: Y/Z<br/>- Accuracy: N%<br/>- Proportion, Balance

    Note over User,DB: Save Assessment Result
    WAS->>DB: Insert assessment_result<br/>(user_id, radical_id, image, score, grade, feedback)
    DB-->>WAS: Result saved (id: 777)

    alt Score >= 60 (Pass)
        WAS->>ULP: Update progress
        ULP->>DB: Get UserLevelProgress
        DB-->>ULP: Progress record
        ULP->>ULP: Mark radical completed<br/>Increment completed_radicals<br/>Calculate progress %
        
        alt Progress >= 90%
            ULP->>ULP: mastery_level = 'mastered'
        else Progress >= 70%
            ULP->>ULP: mastery_level = 'advanced'
        else Progress >= 40%
            ULP->>ULP: mastery_level = 'intermediate'
        else
            ULP->>ULP: mastery_level = 'beginner'
        end
        
        ULP->>ULP: Update last_studied_at
        ULP->>DB: Save UserLevelProgress
        DB-->>ULP: Updated
        ULP-->>WAS: Progress updated
    else Score < 60 (Fail)
        WAS->>WAS: Keep incomplete<br/>Suggest retry
    end

    WAS-->>API: Assessment complete
    API-->>FE: JSON response:<br/>- result_id: 777<br/>- score: 85<br/>- grade: "Good"<br/>- feedback<br/>- comparison_image<br/>- progress_updated: true

    Note over User,DB: Display Results
    FE-->>User: Show assessment:<br/>- Score: 85/100<br/>- Grade: Good<br/>- Visual comparison<br/>- Detailed feedback:<br/>  ✓ Character recognized<br/>  ✓ 9/10 strokes correct<br/>  ✓ Stroke order: 90%<br/>  ⚠ Improve proportion

    User->>FE: Review feedback

    alt Satisfied
        User->>FE: Click "Next Radical"
        FE->>API: GET /api/radicals (next)
    else Want retry
        User->>FE: Click "Try Again"
        FE->>FE: Clear canvas, reset
    end
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
