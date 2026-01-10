// Learning state
let vocabularies = [];
let currentIndex = 0;
let currentMode = 'review';
let quizScore = 0;
let quizAnswered = 0;
let flashcardFlipped = false;
let knownWords = [];
let unknownWords = [];

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadVocabularies();
    setupModeNavigation();
    setupReviewMode();
    setupFlashcardsMode();
    setupSpellingMode();
    setupQuizMode();
});

// Load vocabularies from API
async function loadVocabularies() {
    try {
        const response = await fetch(`/api/topics/${topicId}/vocabularies?level=${level}`);
        const result = await response.json();
        
        // API returns success wrapper -> pagination -> data array
        // Structure: {success: true, data: {data: [...], total: X}}
        if (result.success && result.data && result.data.data) {
            vocabularies = result.data.data;
        } else if (result.data && Array.isArray(result.data)) {
            vocabularies = result.data;
        } else if (Array.isArray(result)) {
            vocabularies = result;
        } else {
            console.error('Unexpected API response structure:', result);
            vocabularies = [];
        }
        
        console.log('Loaded vocabularies:', vocabularies.length, vocabularies);
        
        if (vocabularies.length === 0) {
            alert('No vocabularies found for this topic and level');
            window.location.href = '/client/vocabulary';
            return;
        }

        // Update topic info
        updateTopicInfo();
        updateProgress();
        
        // Display first item
        displayCurrentVocab();
    } catch (error) {
        console.error('Error loading vocabularies:', error);
        alert('Failed to load vocabularies');
    }
}

function updateTopicInfo() {
    const topicName = vocabularies[0]?.topic_name || 'Vocabulary';
    document.getElementById('topicName').textContent = topicName;
    document.getElementById('vocabCount').textContent = `${vocabularies.length} words`;
    document.getElementById('levelBadge').textContent = level;
}

function updateProgress() {
    const progress = ((currentIndex + 1) / vocabularies.length) * 100;
    document.getElementById('progressFill').style.width = `${progress}%`;
    document.getElementById('progressText').textContent = `${currentIndex + 1}/${vocabularies.length}`;
}

// Mode Navigation
function setupModeNavigation() {
    document.querySelectorAll('.mode-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const mode = this.dataset.mode;
            switchMode(mode);
        });
    });
}

function switchMode(mode) {
    currentMode = mode;
    currentIndex = 0;
    
    // Update active button
    document.querySelectorAll('.mode-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.mode === mode);
    });
    
    // Update active content
    document.querySelectorAll('.mode-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(`${mode}Mode`).classList.add('active');
    
    // Reset mode-specific state
    if (mode === 'quiz') {
        quizScore = 0;
        quizAnswered = 0;
        updateQuizScore();
    } else if (mode === 'flashcards') {
        knownWords = [];
        unknownWords = [];
    }
    
    updateProgress();
    displayCurrentVocab();
}

// Display current vocabulary
function displayCurrentVocab() {
    if (!vocabularies[currentIndex]) return;
    
    const vocab = vocabularies[currentIndex];
    
    switch (currentMode) {
        case 'review':
            displayReview(vocab);
            break;
        case 'flashcards':
            displayFlashcard(vocab);
            break;
        case 'spelling':
            displaySpelling(vocab);
            break;
        case 'quiz':
            displayQuiz(vocab);
            break;
    }
}

// Review Mode
function setupReviewMode() {
    document.getElementById('prevBtn').addEventListener('click', () => navigateVocab(-1));
    document.getElementById('nextBtn').addEventListener('click', () => navigateVocab(1));
    document.getElementById('reviewAudioBtn').addEventListener('click', (e) => {
        e.stopPropagation();
        playAudioForCurrent();
    });
}

function displayReview(vocab) {
    document.getElementById('cardNumber').textContent = `${currentIndex + 1} / ${vocabularies.length}`;
    document.getElementById('reviewHanzi').textContent = vocab.simplified || vocab.word || vocab.chinese || vocab.hanzi || '';
    document.getElementById('reviewPinyin').textContent = vocab.pinyin || '';
    document.getElementById('reviewMeaning').textContent = vocab.meaning || vocab.english || '';
    
    // Load image if available
    const imageContainer = document.getElementById('reviewImageContainer');
    const imageEl = document.getElementById('reviewImage');
    if (vocab.image_url) {
        imageEl.src = vocab.image_url;
        imageContainer.style.display = 'block';
    } else {
        imageContainer.style.display = 'none';
    }
    
    const exampleEl = document.getElementById('reviewExample');
    if (vocab.example_sentence || vocab.example) {
        exampleEl.style.display = 'block';
        exampleEl.querySelector('.example-zh').textContent = vocab.example_sentence || vocab.example || '';
        exampleEl.querySelector('.example-en').textContent = vocab.example_translation || '';
    } else {
        exampleEl.style.display = 'none';
    }
    
    // Update button states
    document.getElementById('prevBtn').disabled = currentIndex === 0;
    document.getElementById('nextBtn').disabled = currentIndex === vocabularies.length - 1;
}

function navigateVocab(direction) {
    const newIndex = currentIndex + direction;
    if (newIndex >= 0 && newIndex < vocabularies.length) {
        currentIndex = newIndex;
        updateProgress();
        displayCurrentVocab();
    }
}

// Flashcards Mode
function setupFlashcardsMode() {
    const flashcard = document.getElementById('flashcard');
    flashcard.addEventListener('click', function() {
        this.classList.toggle('flipped');
        flashcardFlipped = !flashcardFlipped;
    });
    
    document.getElementById('knowBtn').addEventListener('click', () => markFlashcard(true));
    document.getElementById('dontKnowBtn').addEventListener('click', () => markFlashcard(false));
    document.getElementById('flashcardAudioBtn').addEventListener('click', (e) => {
        e.stopPropagation();
        playAudioForCurrent();
    });
}

function displayFlashcard(vocab) {
    const flashcard = document.getElementById('flashcard');
    flashcard.classList.remove('flipped');
    flashcardFlipped = false;
    
    document.getElementById('flashcardHanzi').textContent = vocab.simplified || vocab.word || vocab.chinese || vocab.hanzi || '';
    document.getElementById('flashcardPinyin').textContent = vocab.pinyin || '';
    document.getElementById('flashcardMeaning').textContent = vocab.meaning || vocab.english || '';
    document.getElementById('flashcardExample').textContent = vocab.example_sentence || vocab.example || '';
    
    // Load image if available
    const imageContainer = document.getElementById('flashcardImageContainer');
    const imageEl = document.getElementById('flashcardImage');
    if (vocab.image_url) {
        imageEl.src = vocab.image_url;
        imageContainer.style.display = 'block';
    } else {
        imageContainer.style.display = 'none';
    }
}

function markFlashcard(known) {
    if (known) {
        knownWords.push(vocabularies[currentIndex]);
    } else {
        unknownWords.push(vocabularies[currentIndex]);
    }
    
    if (currentIndex < vocabularies.length - 1) {
        currentIndex++;
        updateProgress();
        displayCurrentVocab();
    } else {
        showCompletionModal('flashcards');
    }
}

// Spelling Mode
function setupSpellingMode() {
    document.getElementById('playAudioBtn').addEventListener('click', playAudio);
    document.getElementById('checkSpellingBtn').addEventListener('click', checkSpelling);
    document.getElementById('spellingInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            checkSpelling();
        }
    });
}

function displaySpelling(vocab) {
    document.getElementById('spellingMeaning').textContent = vocab.meaning || vocab.english || '';
    document.getElementById('spellingInput').value = '';
    document.getElementById('spellingFeedback').textContent = '';
    document.getElementById('spellingFeedback').className = 'spelling-feedback';
    document.getElementById('checkSpellingBtn').disabled = false;
}

function playAudio() {
    const vocab = vocabularies[currentIndex];
    const text = vocab.simplified || vocab.word || vocab.chinese || vocab.hanzi || '';
    
    // Use Web Speech API for Chinese TTS
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'zh-CN';
        utterance.rate = 0.8;
        speechSynthesis.speak(utterance);
    } else {
        alert('Text-to-speech not supported in your browser');
    }
}

function playAudioForCurrent() {
    const vocab = vocabularies[currentIndex];
    const text = vocab.simplified || vocab.word || vocab.chinese || vocab.hanzi || '';
    
    // Use Web Speech API for Chinese TTS
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'zh-CN';
        utterance.rate = 0.8;
        speechSynthesis.speak(utterance);
    } else {
        alert('Text-to-speech not supported in your browser');
    }
}

function checkSpelling() {
    const input = document.getElementById('spellingInput').value.trim().toLowerCase();
    const correct = (vocabularies[currentIndex].pinyin || '').toLowerCase().replace(/\s+/g, '');
    const inputNormalized = input.replace(/\s+/g, '');
    
    const feedback = document.getElementById('spellingFeedback');
    
    if (inputNormalized === correct) {
        feedback.textContent = '✅ Correct! Well done!';
        feedback.className = 'spelling-feedback correct';
        document.getElementById('checkSpellingBtn').disabled = true;
        
        setTimeout(() => {
            if (currentIndex < vocabularies.length - 1) {
                currentIndex++;
                updateProgress();
                displayCurrentVocab();
            } else {
                showCompletionModal('spelling');
            }
        }, 1500);
    } else {
        feedback.textContent = `❌ Incorrect. The correct answer is: ${vocabularies[currentIndex].pinyin}`;
        feedback.className = 'spelling-feedback incorrect';
    }
}

// Quiz Mode
function setupQuizMode() {
    // Quiz navigation handled in displayQuiz
}

function displayQuiz(vocab) {
    document.getElementById('quizQuestion').textContent = 'What does this word mean?';
    document.getElementById('quizHanzi').textContent = vocab.simplified || vocab.word || vocab.chinese || vocab.hanzi || '';
    document.getElementById('quizFeedback').textContent = '';
    document.getElementById('quizFeedback').className = 'quiz-feedback';
    document.getElementById('nextQuestionBtn').classList.add('hidden');
    
    // Generate options
    const correctAnswer = vocab.meaning || vocab.english || '';
    const options = generateQuizOptions(correctAnswer);
    
    const optionsContainer = document.getElementById('quizOptions');
    optionsContainer.innerHTML = '';
    
    options.forEach(option => {
        const btn = document.createElement('button');
        btn.className = 'quiz-option';
        btn.textContent = option;
        btn.addEventListener('click', () => selectQuizOption(btn, option, correctAnswer));
        optionsContainer.appendChild(btn);
    });
}

function generateQuizOptions(correctAnswer) {
    const options = [correctAnswer];
    const allMeanings = vocabularies
        .map(v => v.meaning || v.english)
        .filter(m => m && m !== correctAnswer);
    
    // Shuffle and get 3 random wrong answers
    const shuffled = allMeanings.sort(() => Math.random() - 0.5);
    options.push(...shuffled.slice(0, 3));
    
    // Shuffle options
    return options.sort(() => Math.random() - 0.5);
}

function selectQuizOption(btn, selected, correct) {
    // Disable all options
    document.querySelectorAll('.quiz-option').forEach(opt => {
        opt.disabled = true;
    });
    
    const feedback = document.getElementById('quizFeedback');
    
    if (selected === correct) {
        btn.classList.add('correct');
        feedback.textContent = '✅ Correct!';
        feedback.className = 'quiz-feedback correct';
        quizScore++;
    } else {
        btn.classList.add('incorrect');
        // Highlight correct answer
        document.querySelectorAll('.quiz-option').forEach(opt => {
            if (opt.textContent === correct) {
                opt.classList.add('correct');
            }
        });
        feedback.textContent = `❌ Incorrect. The correct answer is: ${correct}`;
        feedback.className = 'quiz-feedback incorrect';
    }
    
    quizAnswered++;
    updateQuizScore();
    
    // Show next button
    const nextBtn = document.getElementById('nextQuestionBtn');
    nextBtn.classList.remove('hidden');
    nextBtn.onclick = () => {
        if (currentIndex < vocabularies.length - 1) {
            currentIndex++;
            updateProgress();
            displayCurrentVocab();
        } else {
            showCompletionModal('quiz');
        }
    };
}

function updateQuizScore() {
    document.getElementById('scoreValue').textContent = `${quizScore}/${quizAnswered}`;
}

// Completion Modal
function showCompletionModal(mode) {
    const modal = document.getElementById('completionModal');
    const messageEl = document.getElementById('completionMessage');
    const statsEl = document.getElementById('modalStats');
    
    let message = '';
    let stats = '';
    
    switch (mode) {
        case 'review':
            message = `You've reviewed all ${vocabularies.length} words!`;
            break;
        case 'flashcards':
            message = `You've completed all flashcards!`;
            stats = `
                <div>✅ Known: ${knownWords.length}</div>
                <div>❌ Need Practice: ${unknownWords.length}</div>
            `;
            break;
        case 'spelling':
            message = `You've completed the spelling practice!`;
            break;
        case 'quiz':
            const percentage = Math.round((quizScore / quizAnswered) * 100);
            message = `Quiz Complete!`;
            stats = `
                <div style="font-size: 48px; font-weight: 700; color: #667eea;">${percentage}%</div>
                <div style="margin-top: 16px;">Score: ${quizScore}/${quizAnswered}</div>
            `;
            break;
    }
    
    messageEl.textContent = message;
    statsEl.innerHTML = stats;
    modal.classList.remove('hidden');
    
    // Setup restart button
    document.getElementById('restartBtn').onclick = () => {
        modal.classList.add('hidden');
        currentIndex = 0;
        if (mode === 'quiz') {
            quizScore = 0;
            quizAnswered = 0;
        } else if (mode === 'flashcards') {
            knownWords = [];
            unknownWords = [];
        }
        updateProgress();
        displayCurrentVocab();
    };
}
