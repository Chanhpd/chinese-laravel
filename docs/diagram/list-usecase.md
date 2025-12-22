2.1.2. List of Use Cases
The functional requirements are mapped into the following use cases:

Group 1: Authentication & Account Management
•	Register: Create new user account with email, username, and password
•	Login: Authenticate users with email and password
•	Logout: Securely log out from the application 
•	Manage Profile: Learners can update personal information and view their learning statistics.

Group 2: Learning Progress Tracking (Learner)
•	Daily Check-in: Perform daily check-in to maintain learning streak
•	View Streak Statistics: View current streak count, longest streak, total check-in days, and weekly check-in calendar
•	Track Topic Progress: View learning progress for each topic including completed words, total words, and mastery level (beginner, intermediate, advanced, mastered)
•	Track Level Progress: View HSK level progress including completed words, completed radicals, and overall mastery level
•	View Learning Dashboard: See overall learning statistics, progress by topics and levels

Group 3: Vocabulary Learning by Topic (Learner)
•	Browse Topics: Explore vocabulary organized by learning topics (e.g., Family, Food, Travel)
•	View Topic Details: See topic information with multi-language support (English, Chinese, Vietnamese, etc.)
•	Browse Vocabulary by Topic: View all vocabulary words within a selected topic
•	View Vocabulary Details: View comprehensive word information (simplified/traditional Chinese, pinyin, phonetic, part of speech, meanings in multiple languages, example sentences with translations, related words, similar characters, pronunciation audio)
•	Save Vocabulary: Save important/difficult words to personal saved list with custom notes
•	Review Saved Vocabulary: Review saved words, add notes, track review count
•	Mark Word as Completed: Update progress when mastering a vocabulary word

Group 4: HSK Level-Based Learning (Learner)
•	Browse HSK Levels: View HSK levels 1-6 with level information
•	Browse Words by Level: Explore word lists for each HSK level with multi-language meanings
•	Browse Radicals by Level: View Chinese character radicals organized by HSK level
•	View Radical Details: See radical information (hanzi, traditional form, pinyin, stroke count, meanings in multiple languages)
•	Mark Favorite Radicals: Save frequently referenced radicals as favorites
•	Track Level Progress: Monitor progress for words and radicals completed in each level
•	Practice writing character: Draw and practice Chinese characters with stroke order guidance

Group 5: Chat bot AI Tutor (Learner) 
•	Chat with AI Bot: Engage in real-time conversations with AI assistant for learning Chinese or help support focus in Chinese
•	Select Chat Language: Choose conversation language (Chinese, English, Vietnamese, etc.)
•	View Chat History: View and manage past conversations with AI bot with full message and response history

Group 6: Exam & Assessment (Learner)
•	Browse Available Exams: View list of HSK exams (HSK 1-6) with exam details (title, level, time, total score)
•	Start Exam: Begin a new exam attempt and record start time
•	View Exam Structure: See exam parts (Listening, Reading, Writing) with allocated time for each part
•	View Question Types: See different question type instructions within each exam part
•	Answer Questions: Submit answers to questions (multiple choice, true/false, fill-in-blank, matching)
•	View Question Content: See question text, audio, images, and answer options
•	Listen to Audio: Play audio for listening comprehension questions
•	View Images: See visual aids for image-based questions
•	Select Preferred Language: Choose language for translations and explanations (Vietnamese, English, French, Japanese, Korean, Russian, Thai, Indonesian, German, Hindi, Malay, Portuguese, Khmer, Dutch, Spanish)
•	Track Time: Monitor time remaining for exam and individual parts
•	Save Progress: Save current exam progress (in-progress status)
•	Submit Exam: Complete and submit exam for grading
•	View Exam Results: See total score, percentage, time spent after completing exam
•	Review Answers: View submitted answers with correct/incorrect status
•	Check Answer Explanations: Read detailed explanations for answers in preferred language
•	View Score Breakdown: See score earned per question
•	View Exam History: Access all past exam attempts with results
•	Retake Exam: Start a new attempt for previously taken exams
•	Compare Attempts: View progress across multiple attempts for the same exam

Group 7: Settings & Preferences (Learner)
•	Change App Language: Switch between supported interface languages (English, Chinese, Vietnamese, etc.)
•	View Preferred Languages: See available content languages for vocabulary and topic translations
•	Update Learning Preferences: Configure learning goals and notification settings

Group 8: Content Management - Topics & Vocabulary (Staff)
•	Manage Topics: Create, edit, delete learning topics with multi-language support
•	Add Topic Translations: Add topic names and descriptions in multiple languages (English, Chinese, Vietnamese, etc.)
•	Manage Vocabularies: Create, edit, delete vocabulary entries within topics
•	Add Vocabulary Details: Input word information (simplified/traditional forms, pinyin, phonetic, part of speech, stroke count, tone pattern, radical info)
•	Add Vocabulary Translations: Add meanings and example translations in multiple languages
•	Upload Audio Files: Add pronunciation audio files for vocabulary words
•	Upload Images: Add visual aids/images for vocabulary words
•	Manage Related Words: Link related words and similar characters
•	Add Example Sentences: Create example sentences with translations and highlights
•	Set Vocabulary Level: Assign HSK level to vocabulary (HSK 1-6)

Group 9: Content Management - Levels & Core Data (Staff)
•	Manage HSK Levels: Configure HSK level information (level number, level name, test type)
•	Manage Words by Level: Add, edit, delete words for each HSK level
•	Add Word Translations: Input word meanings in multiple languages (Vietnamese, English, Russian, Thai, Malay, Korean, Japanese, Indonesian)
•	Manage Radicals: Add, edit, delete Chinese radicals in the system database
•	Add Radical Details: Input radical information (hanzi, traditional, pinyin, stroke count, frequency rank, general standard)
•	Add Radical Translations: Add radical meanings in multiple languages (Vietnamese, Chinese, English, Japanese, Korean, Thai, German, French, Spanish, Italian, Brazilian Portuguese, Turkish)

Group 10: Exam Management (Staff)
•	Manage Exams: Create, edit, delete HSK exams (HSK 1-6)
•	Set Exam Details: Configure exam title, level, total time, total score, description
•	Activate/Deactivate Exams: Control which exams are available to learners
•	Manage Exam Parts: Create, edit, delete exam parts (Listening, Reading, Writing)
•	Set Part Time: Allocate time for each exam part
•	Order Exam Parts: Set display order for exam parts
•	Manage Question Types: Create, edit, delete question type sections within parts
•	Set Question Type Details: Configure type kind code (110001, 110002, etc.), order, and instructions
•	Manage Questions: Create, edit, delete questions within question types
•	Set Question Details: Input general section (text, audio, image), question order, score
•	Add Question Translations: Translate general section text to multiple languages
•	Manage Question Content: Create, edit, delete question content items (sub-questions)
•	Set Content Details: Input question text, audio, image, and content order
•	Configure Answer Options: Set answer texts, audios, images (support JSON format)
•	Set Correct Answers: Define correct answer(s) and alternative correct answers
•	Add Explanations: Write detailed answer explanations in multiple languages (15+ languages)
•	Upload Media Files: Upload audio files (MP3) and images (JPG, PNG, WebP) for questions
•	Preview Exam: Test exam flow and question display before publishing
•	Clone Exam: Duplicate existing exam structure for creating similar tests

Group 11: System Administration (Admin)
•	Manage Users: Create, edit, block/unblock, delete user accounts (Learners, Staff, Admin)
•	Manage User Roles: Assign and modify user roles (user, staff, admin, super_admin)
•	View User Status: Monitor user status, blocked status, and last login information
•	View Admin Logs: Access comprehensive audit trail of all administrative actions
•	Review Action History: See detailed logs including action type, target entity, old/new values, IP address, user agent
•	View System Analytics: Overview of system usage, user growth, learning progress statistics
•	Monitor User Progress: View aggregated learning statistics (topic completion, level progress, streak data)
•	Export Reports: Generate reports on user activity and learning progress 
View Exam Statistics: Monitor exam attempts, completion rates, average scores by exam and user
•	Analyze Learning Patterns: Review user engagement with topics, levels, and exams
•	Export Reports: Generate reports on user activity, learning progress, and exam performance