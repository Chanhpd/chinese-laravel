# Activity Diagram - Chinese Learning Platform

## Main Features Activity Flows

### 1. Vocabulary Learning (Học từ vựng)
### 2. Taking Exam (Làm bài thi)
### 3. Radical Character Writing Assessment (Chấm điểm viết Radical)

---

## PlantUML Diagrams

### 1. Vocabulary Learning Flow

```plantuml
@startuml Vocabulary Learning

|User|
start
:Open Learning Section;
:Browse Topics;
:Select Topic;

|System|
:Load Topic with Vocabularies;
:Check User Progress;

|User|
:View Vocabulary List;

while (More vocabularies?) is (yes)
  :Select Vocabulary;
  
  |System|
  :Display Word Details
  (translations, examples, audio);
  
  |User|
  :Study Vocabulary;
  
  if (Save vocabulary?) then (yes)
    |System|
    :Add to Saved Vocabularies;
  endif
  
  :Mark as Completed;
  
  |System|
  :Update UserTopicProgress;
  :Calculate progress %
  and mastery level;

endwhile (done)

|System|
:Update User Streak;

|User|
:View Progress Summary;

stop

@enduml
```

### 2. Taking Exam Flow

```plantuml
@startuml Taking Exam

|User|
start
:Open Exam Section;
:Browse Available Exams;
:Select Exam;

|System|
:Load Exam Details
(parts, questions);

|User|
:Review Exam Info;
:Click "Start Exam";

|System|
:Create UserExamAttempt;
:Set status = 'in_progress';
:Start timer;

|User|
partition "Answer Questions" {
  while (More questions?) is (yes)
    |System|
    :Display Question
    (audio/text/image);
    
    |User|
    :Select Answer;
    
    |System|
    :Record UserAnswer;
  endwhile (complete)
}

:Click "Submit Exam";

|System|
partition "Scoring" {
  :Compare answers with
  correct answers;
  :Calculate total score
  and percentage;
  :Update attempt status
  = 'completed';
}

|User|
:View Exam Results;

if (View details?) then (yes)
  |System|
  :Show questions, answers,
  explanations;
endif

:Return to Dashboard;

stop

@enduml
```

### 3. Radical Character Writing Assessment Flow

```plantuml
@startuml Radical Writing Assessment

|User|
start
:Open Radical Learning;
:Browse Radicals by Level;
:Select Radical;

|System|
:Load Radical Details;
:Show stroke order animation;

|User|
:Study Radical Structure;
:Click "Practice Writing";

|System|
:Initialize Drawing Canvas;

|User|
:Write Character on Canvas;

:Submit Written Character;

|System|
partition "AI Assessment" {
  :Capture Canvas Image;
  :Send to AI Model
  (OpenAI Vision API);
  
  :AI Analyzes:
  - Character recognition
  - Stroke order
  - Stroke quality
  - Overall structure;
  
  :Calculate Score (0-100)
  and Grade;
  
  :Generate Feedback;
}

:Save Assessment Result;

if (Score >= 60?) then (pass)
  :Update UserLevelProgress;
  :Mark Radical Completed;
else (fail)
  :Suggest retry;
endif

|User|
:View Results
(score, feedback, comparison);

if (Try again?) then (yes)
  |System|
  :Reset canvas;
else (no)
  :Return to Dashboard;
endif

stop

@enduml
```

---

## Mermaid Diagrams

### 1. Vocabulary Learning Flow

```mermaid
flowchart TD
    Start([User Opens Learning Section]) --> Browse[Browse Topics]
    Browse --> Select[Select Topic]
    Select --> Load[System: Load Topic with<br/>Vocabularies and Progress]
    Load --> ViewList[View Vocabulary List]
    
    ViewList --> More{More<br/>vocabularies?}
    More -->|Yes| SelectVocab[Select Vocabulary]
    
    SelectVocab --> Display[System: Display Word Details<br/>translations, examples, audio]
    Display --> Study[Study Vocabulary]
    
    Study --> Save{Save<br/>vocabulary?}
    Save -->|Yes| SaveSys[System: Add to Saved]
    Save -->|No| Mark
    SaveSys --> Mark[Mark as Completed]
    
    Mark --> Update[System: Update UserTopicProgress<br/>Calculate progress % and mastery level]
    Update --> More
    
    More -->|Done| Streak[System: Update User Streak]
    Streak --> Summary[View Progress Summary]
    Summary --> End([End])
    
    style Start fill:#90EE90
    style End fill:#FFB6C1
    style Update fill:#87CEEB
    style Streak fill:#F0E68C
```

### 2. Taking Exam Flow

```mermaid
flowchart TD
    Start([User Opens Exam Section]) --> Browse[Browse Available Exams]
    Browse --> Select[Select Exam]
    Select --> Load[System: Load Exam Details<br/>parts, questions]
    
    Load --> Review[Review Exam Info]
    Review --> StartExam[Click 'Start Exam']
    StartExam --> Create[System: Create UserExamAttempt<br/>status = 'in_progress'<br/>Start timer]
    
    Create --> Answer[=== ANSWER QUESTIONS ===]
    Answer --> More{More<br/>questions?}
    More -->|Yes| Display[System: Display Question<br/>audio/text/image]
    Display --> SelectAns[Select Answer]
    SelectAns --> Record[System: Record UserAnswer]
    Record --> More
    
    More -->|Complete| Submit[Click 'Submit Exam']
    Submit --> Score[=== SCORING ===]
    Score --> Compare[System: Compare answers<br/>with correct answers]
    Compare --> Calc[Calculate total score<br/>and percentage]
    Calc --> UpdateAttempt[Update attempt<br/>status = 'completed']
    
    UpdateAttempt --> ViewResults[View Exam Results]
    ViewResults --> Details{View<br/>details?}
    Details -->|Yes| ShowDetails[System: Show questions,<br/>answers, explanations]
    Details -->|No| Return
    ShowDetails --> Return[Return to Dashboard]
    
    Return --> End([End])
    
    style Start fill:#90EE90
    style End fill:#FFB6C1
    style Create fill:#87CEEB
    style Score fill:#FFD700
    style UpdateAttempt fill:#DDA0DD
```

### 3. Radical Character Writing Assessment Flow

```mermaid
flowchart TD
    Start([User Opens Radical Learning]) --> Browse[Browse Radicals by Level]
    Browse --> Select[Select Radical]
    Select --> Load[System: Load Radical Details<br/>Show stroke order animation]
    
    Load --> Study[Study Radical Structure]
    Study --> Click[Click 'Practice Writing']
    Click --> Init[System: Initialize Canvas]
    
    Init --> Write[Write Character on Canvas]
    Write --> Submit[Submit Written Character]
    
    Submit --> AI[=== AI ASSESSMENT ===]
    AI --> Capture[System: Capture Image]
    Capture --> Send[Send to AI Model<br/>OpenAI Vision API]
    
    Send --> Analyze[AI Analyzes:<br/>- Character recognition<br/>- Stroke order<br/>- Stroke quality<br/>- Overall structure]
    
    Analyze --> CalcScore[Calculate Score 0-100<br/>and Grade]
    CalcScore --> Feedback[Generate Feedback]
    
    Feedback --> Save[Save Assessment Result]
    Save --> Pass{Score >= 60?}
    Pass -->|Yes| Update[Update UserLevelProgress<br/>Mark Radical Completed]
    Pass -->|No| Suggest[Suggest retry]
    
    Update --> ViewResult
    Suggest --> ViewResult[View Results<br/>score, feedback, comparison]
    
    ViewResult --> Again{Try<br/>again?}
    Again -->|Yes| Reset[System: Reset canvas]
    Reset --> Write
    Again -->|No| Return[Return to Dashboard]
    
    Return --> End([End])
    
    style Start fill:#90EE90
    style End fill:#FFB6C1
    style AI fill:#FFD700
    style Send fill:#87CEEB
    style CalcScore fill:#DDA0DD
    style Save fill:#F0E68C
```

---

## Tóm tắt các luồng chính

### 1. Vocabulary Learning (Học từ vựng)
**Luồng chính**: Browse Topics → Select Topic → Study Vocabularies → Mark Completed → Update Progress & Streak

**Điểm chính**:
- Học từ vựng theo chủ đề (Topic-based)
- Lưu từ yêu thích (optional)
- Tự động tính progress % và mastery level
- Cập nhật streak hàng ngày

### 2. Taking Exam (Làm bài thi)
**Luồng chính**: Browse Exams → Select Exam → Start Exam → Answer Questions → Submit → Auto Scoring → View Results

**Điểm chính**:
- Tạo UserExamAttempt khi bắt đầu
- Ghi nhận từng câu trả lời
- Chấm điểm tự động so với correct_answers
- Hiển thị kết quả với explanations (optional)

### 3. Radical Character Writing Assessment (Chấm điểm viết Radical)
**Luồng chính**: Browse Radicals → Select Radical → Study Stroke Order → Write on Canvas → Submit → AI Assessment → View Results

**Điểm chính**:
- Học cấu trúc và thứ tự nét của radical
- Vẽ character trên canvas
- AI phân tích 4 tiêu chí: Recognition, Stroke Order, Quality, Structure
- Chấm điểm 0-100 với detailed feedback
- Cập nhật progress nếu pass (≥60%)

## Notes
- Các sơ đồ đã được rút gọn, tập trung vào luồng chính
- Progress tracking tự động cho cả 3 chức năng
- Mastery levels: beginner (0-39%), intermediate (40-69%), advanced (70-89%), mastered (90-100%)
