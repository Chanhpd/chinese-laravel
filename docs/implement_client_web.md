# Web Client Development Specification

## 🎯 Mục tiêu
Xây dựng web client đa nền tảng sử dụng Laravel dựa trên ứng dụng Flutter hiện tại.

---

## 🔐 1. Xác thực người dùng
✅ **Đã hoàn thành**
- Đăng ký tài khoản
- Đăng nhập
- Quản lý tài khoản
- Quên mật khẩu

---

## 📚 2. Danh sách đầy đủ các chức năng hệ thống

### 🏠 **Homepage / Dashboard**
- **Welcome Card**: Hiển thị thông tin người dùng, lời chào
- **Streak Card**: Hệ thống điểm danh hàng ngày (Daily Check-in)
- **Practice Zone**: Khu vực luyện tập với 4 chức năng chính
  - Characters (Ký tự/Bộ thủ)
  - Vocabulary (Từ vựng)
  - Quiz (Trắc nghiệm)
  - Exam (Thi thử)
- **Learning Categories**: Danh mục học tập

### 📖 **Vocabulary Learning (Học từ vựng)**
#### A. Radical Learning (Học bộ thủ/ký tự Hán)
- Danh sách bộ thủ với filter theo:
  - Stroke count (Số nét)
  - Frequency (Tần suất)
  - Category (Danh mục)
- Chi tiết từng ký tự:
  - Stroke order animation (Hoạt ảnh thứ tự nét)
  - Pronunciation (Phát âm)
  - Examples (Ví dụ)
  - Handwriting practice (Luyện viết tay)
  - Related characters (Ký tự liên quan)
- Swipe navigation giữa các ký tự

#### B. HSK Vocabulary (Từ vựng theo cấp HSK)
- 6 levels HSK (1-6)
- Mỗi level chia thành units (15 từ/unit)
- Chức năng mỗi unit:
  - **View vocabulary list**: Danh sách từ
  - **Flashcard practice**: Luyện tập với thẻ
  - **Review quiz**: Trắc nghiệm ôn tập
  - **Unit detail**: Xem chi tiết từ

#### C. TOCFL Vocabulary (Từ vựng TOCFL - Taiwan)
- 5 levels TOCFL (1-5/6)
- Cấu trúc tương tự HSK
- Unit-based learning

#### D. Topic-based Vocabulary (Từ vựng theo chủ đề)
- Phân loại theo topics: Food, Travel, Family, Work, etc.
- Filter by level (A1, A2, B1, B2)
- Word detail với:
  - Chinese + Pinyin + Translation
  - Example sentences
  - Audio pronunciation
  - Images/illustrations

#### E. Category Vocabulary (Từ vựng phân loại)
- Browsing by categories
- Search functionality
- Flashcard mode
- Quiz mode
- Writing practice
- Word detail view

#### F. User Vocabulary Management
- **My List**: Danh sách từ của tôi
- **Bookmarked**: Từ đã đánh dấu
- **Daily Vocabulary**: Từ vựng hàng ngày
- **Review Schedule**: Lịch ôn tập theo SRS (Spaced Repetition System)
- **Statistics**: Thống kê học tập

### 📝 **Exams (Thi thử)**
#### A. HSK Exams
- 6 levels HSK (1-6)
- Danh sách đề thi theo level
- Chi tiết đề thi:
  - Listening section (Nghe)
  - Reading section (Đọc)
  - Time limit
  - Question count
- Exam session với timer
- Result page với:
  - Score
  - Correct/Wrong answers
  - Time spent
  - Review answers

#### B. Reading Practice
- Danh sách bài đọc
- Chi tiết bài đọc với câu hỏi
- Automatic grading

#### C. Listening Practice
- Danh sách bài nghe
- Audio player
- Questions & answers
- Result tracking

#### D. Mock Exams
- Đề thi mô phỏng
- Real mock exams
- Complete exam flow

### 🤖 **AI Chatbot (Trợ lý AI)**
- Chat interface với AI tutor
- Send messages (có/không đăng nhập)
- Chat history (yêu cầu đăng nhập):
  - View history với pagination
  - Delete single conversation
  - Clear all history
- Language selection (CN/EN)
- Real-time conversation

### ✍️ **Handwriting Practice (Luyện viết tay)**
- Handwriting canvas
- Stroke recognition
- AI grading/scoring
- Offline support
- Practice modes:
  - Free writing
  - Guided writing
  - Character writing
- Scoring feedback

### 🎯 **Quiz Hub (Trung tâm trắc nghiệm)**
- HSK Levels Quiz
- Reading Quiz
- Listening Quiz
- Mock Exams
- Real Mock Exams
- Quick quiz từ vocabulary

### 📊 **Profile & Statistics**
- User profile information
- Learning statistics:
  - Words learned
  - Exams taken
  - Study time
  - Streak count
- Achievement/badges
- Settings

### 📓 **Notebook (Sổ tay)**
- Save notes for words
- Personal vocabulary list
- Custom tags
- Search & filter

### 📜 **Word History**
- Lịch sử từ đã học
- Recently viewed words
- Study progress tracking

### 🔍 **Search Features**
- Global search across all content
- Vocabulary search
- Character search
- Filter & sort options

---

## 🎨 3. Style giao diện & Màu sắc chủ đạo

### **Color Palette**

#### Primary Colors (Xanh ngọc nhẹ nhàng, dịu mắt)
```
Primary: #62bfba (Xanh ngọc chủ đạo)
Primary Light: #B0E0DC (Xanh ngọc sáng)
Primary Dark: #5FA9A3 (Xanh ngọc đậm)
```

#### Secondary Colors (Xanh lá nhạt, hài hòa)
```
Secondary: #95D5B2 (Xanh lá mint)
Secondary Light: #B8E6D5 (Xanh lá sáng)
Secondary Dark: #74C69D (Xanh lá đậm)
```

#### Accent Colors (Cam đào nhẹ cho điểm nhấn)
```
Accent: #FFB088 (Cam đào ấm)
Accent Light: #FFCBA8 (Cam đào nhạt)
Accent Dark: #FF9A68 (Cam đào đậm)
```

#### Background Colors (Trắng ngà và xanh nhạt)
```
Background: #D4F1F4 (Nền trắng xanh nhẹ)
Surface: #FFFFFF (Mặt card trắng tinh)
Surface Variant: #E8F5F4 (Biến thể nền xanh nhạt)
```

#### Text Colors (Xám xanh đậm)
```
Text Primary: #2D3E3F (Xám xanh đậm)
Text Secondary: #5F7172 (Xám xanh vừa)
Text Disabled: #ABBDBE (Xám xanh nhạt)
Text On Primary: #FFFFFF (Trắng)
```

#### Status Colors
```
Success: #52C688 (Xanh lá tươi)
Warning: #FFBD5A (Vàng cam nhẹ)
Error: #FF8787 (Đỏ hồng nhẹ)
Info: #6BAED6 (Xanh dương pastel)
```

#### Border Colors
```
Border: #D9ECEB (Xanh ngọc rất nhạt)
Border Focus: #89CEC9 (Xanh ngọc chủ đạo)
```

### **Typography**
```
Font Family: Nunito (Primary), NotoSansSC (Chinese)
Font Weights:
- Light: 300
- Regular: 400
- Medium: 500
- Semi Bold: 600
- Bold: 700

Font Sizes:
- Heading 1: 32px
- Heading 2: 24px
- Heading 3: 20px
- Heading 4: 18px
- Body Large: 16px
- Body Medium: 14px
- Body Small: 12px
- Button: 16px
```

### **Spacing**
```
2px, 4px, 6px, 8px, 12px, 16px, 20px, 24px, 32px
```

### **Border Radius**
```
Small: 8px
Medium: 12px
Large: 16px
XLarge: 24px
```

### **Elevation/Shadows**
```
Low: 2px
Medium: 4px
High: 8px
Shadow: rgba(0, 0, 0, 0.08)
Shadow Light: rgba(0, 0, 0, 0.03)
```

### **Gradients**
```css
/* Primary Gradient: Xanh ngọc -> Xanh lá mint */
background: linear-gradient(135deg, #89CEC9 0%, #95D5B2 100%);

/* Secondary Gradient */
background: linear-gradient(135deg, #B0E0DC 0%, #B8E6D5 100%);

/* Accent Gradient */
background: linear-gradient(135deg, #FFB088 0%, #FFCBA8 100%);
```

### **Design Principles**
- Clean & minimalist design
- Soft, rounded corners (8-24px)
- Subtle shadows for depth
- Pastel color scheme for easy on eyes
- Clear visual hierarchy
- Consistent spacing system
- Mobile-first responsive design

---

## 💾 4. Database & Content Assets

### **SQLite Databases**
```
assets/db/
├── hsk_radicalsv1.db       # Database bộ thủ HSK với đầy đủ thông tin
```

### **JSON Data Files**
```
assets/db/json/
├── HSK1_all_exams.json     # Đề thi HSK Level 1
├── HSK2_all_exams.json     # Đề thi HSK Level 2
├── tocfl_1.json            # Từ vựng TOCFL Level 1
├── tocfl_2.json            # Từ vựng TOCFL Level 2
├── tocfl_3.json            # Từ vựng TOCFL Level 3
├── tocfl_4.json            # Từ vựng TOCFL Level 4
├── tocfl_5-6.json          # Từ vựng TOCFL Level 5-6
├── all_exams_list.json     # Danh sách tất cả đề thi
├── mock_exam_data.json     # Dữ liệu đề thi mô phỏng
```

### **Asset Structure**
```
assets/
├── db/                     # Databases
│   ├── hsk_radicalsv1.db
│   └── json/              # JSON data files
├── fonts/                  # Font files (Nunito, NotoSansSC, MaShanZheng)
├── icons/                  # App icons
├── images/                 # Image assets
├── sounds/                 # Audio files
├── models/                 # ML models (TensorFlow Lite)
└── json/                   # Additional JSON configs
```

### **Content Types Available**

#### HSK Radicals Database (hsk_radicalsv1.db)
- Bảng radicals với:
  - Hanzi (Ký tự)
  - Pinyin (Phiên âm)
  - Meaning (Nghĩa)
  - Stroke count (Số nét)
  - Stroke order data (Dữ liệu thứ tự nét)
  - Examples (Ví dụ)
  - Components (Thành phần)
  - Related characters

#### Exam Data (JSON)
- Complete exam structure:
  - Exam metadata (ID, title, level, type)
  - Sections (Listening, Reading)
  - Questions with options
  - Correct answers
  - Audio references
  - Difficulty levels

#### TOCFL Vocabulary (JSON)
- Vocabulary organized by level
- Chinese + Pinyin + English translation
- Example sentences
- Audio pronunciation references
- Categories/tags

### **API Endpoints Laravel cần implement**

#### Vocabulary APIs
```
GET /api/vocabulary/hsk/{level}           # Lấy từ vựng HSK theo level
GET /api/vocabulary/tocfl/{level}         # Lấy từ vựng TOCFL theo level
GET /api/vocabulary/topics                # Lấy danh sách topics
GET /api/vocabulary/topic/{id}            # Lấy từ vựng theo topic
GET /api/vocabulary/search                # Tìm kiếm từ vựng
GET /api/vocabulary/{id}                  # Chi tiết từ vựng
```

#### User Vocabulary APIs
```
POST /api/user/vocabulary/favorite        # Thêm từ yêu thích
DELETE /api/user/vocabulary/favorite/{id} # Xóa từ yêu thích
GET /api/user/vocabulary/favorites        # Lấy danh sách từ yêu thích
POST /api/user/vocabulary/bookmark        # Đánh dấu từ
GET /api/user/vocabulary/bookmarks        # Lấy từ đã đánh dấu
GET /api/user/vocabulary/daily            # Từ vựng hàng ngày
GET /api/user/vocabulary/review           # Lịch ôn tập (SRS)
POST /api/user/vocabulary/update-progress # Cập nhật tiến trình học
```

#### Radicals APIs
```
GET /api/radicals                         # Lấy danh sách bộ thủ
GET /api/radicals/{hanzi}                 # Chi tiết bộ thủ
GET /api/radicals/search                  # Tìm kiếm bộ thủ
```

#### Exams APIs
```
GET /api/exams/hsk/{level}                # Lấy đề thi HSK theo level
GET /api/exams/{id}                       # Chi tiết đề thi
POST /api/exams/{id}/submit               # Nộp bài thi
GET /api/exams/{id}/result                # Kết quả thi
GET /api/exams/reading                    # Danh sách bài đọc
GET /api/exams/listening                  # Danh sách bài nghe
```

#### Chat APIs
```
✅ POST /api/chat/send                    # Gửi tin nhắn (đã có)
✅ GET /api/chat/history                  # Lịch sử chat (đã có)
✅ DELETE /api/chat/history/{id}          # Xóa chat (đã có)
✅ DELETE /api/chat/history/clear         # Xóa tất cả (đã có)
```

#### Profile & Statistics APIs
```
GET /api/user/profile                     # Thông tin profile
PUT /api/user/profile                     # Cập nhật profile
GET /api/user/statistics                  # Thống kê học tập
GET /api/user/streak                      # Streak data
POST /api/user/checkin                    # Daily check-in
```

#### Handwriting APIs
```
POST /api/handwriting/grade               # Chấm điểm viết tay
POST /api/handwriting/recognize           # Nhận diện chữ viết
```

---

## 📋 5. Technical Requirements

### **Backend (Laravel)**
- PHP 8.1+
- Laravel 10+
- MySQL/PostgreSQL database
- JWT Authentication
- RESTful API
- File storage for audio/images
- Queue jobs for background tasks
- Cache layer (Redis)

### **Frontend Recommendations**
- Vue.js 3 / React / Next.js
- Tailwind CSS (matching color scheme)
- Axios for API calls
- State management (Vuex/Pinia or Redux)
- Audio player library
- Canvas drawing library (for handwriting)
- Responsive design (mobile-first)

### **Features to Implement**
- Progressive Web App (PWA) support
- Offline capability (Service Workers)
- Audio playback
- Search with autocomplete
- Pagination
- Infinite scroll
- Modal dialogs
- Toast notifications
- Loading states
- Error handling
- Form validation
- Image optimization
- Lazy loading

---

## 🚀 6. Implementation Priority

### Phase 1 - Core Features (MVP)
1. ✅ Authentication (Đã xong)
2. Homepage/Dashboard
3. HSK Vocabulary Learning
4. Basic Quiz functionality
5. User Profile

### Phase 2 - Enhanced Learning
1. Radical/Character Learning
2. TOCFL Vocabulary
3. Topic-based Vocabulary
4. Flashcard & Writing Practice
5. Complete Exam System

### Phase 3 - Advanced Features
1. AI Chatbot Integration
2. Handwriting Recognition
3. Advanced Statistics
4. SRS System

### Phase 4 - Polish & Optimization
1. Performance optimization
2. SEO optimization
3. PWA features
4. Advanced analytics
5. Social features

---

## 📝 7. Notes for Laravel Development

### Database Migration Strategy
1. Import JSON data vào MySQL
2. Normalize data structure
3. Create proper indexes
4. Setup relationships
5. Seed initial data

### File Structure Recommendation
```
laravel-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── VocabularyController.php
│   │   │   ├── ExamController.php
│   │   │   ├── ChatController.php
│   │   │   └── ProfileController.php
│   ├── Models/
│   │   ├── Vocabulary.php
│   │   ├── Radical.php
│   │   ├── Exam.php
│   │   └── UserProgress.php
│   ├── Services/
│   │   ├── VocabularyService.php
│   │   ├── SRSService.php
│   │   └── ChatService.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── json_imports/
├── public/
│   ├── audio/
│   └── images/
└── resources/
    └── views/
```

### Caching Strategy
- Cache vocabulary lists by level
- Cache exam data
- Cache user statistics
- Use Redis for session & queue
- Implement ETags for API responses

---

**Document Version**: 1.0  
**Last Updated**: January 6, 2026  
**Status**: Ready for Laravel Development 