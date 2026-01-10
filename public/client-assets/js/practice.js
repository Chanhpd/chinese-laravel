let currentChar = '';
let referenceWriter, guideWriter;
let isDrawing = false;
let userCanvas, userCtx;
let allCharacters = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeCanvas();
    loadCharacters();
});

// Initialize canvas
function initializeCanvas() {
    userCanvas = document.getElementById('user-canvas');
    userCtx = userCanvas.getContext('2d');
    
    // Set canvas size
    const container = document.getElementById('user-container');
    const size = Math.min(container.offsetWidth, 400);
    userCanvas.width = size;
    userCanvas.height = size;
    
    // Drawing settings
    userCtx.strokeStyle = '#333';
    userCtx.lineWidth = 3;
    userCtx.lineCap = 'round';
    userCtx.lineJoin = 'round';
    
    clearUserCanvas();
    setupDrawing();
}

// Clear canvas
function clearUserCanvas() {
    userCtx.fillStyle = 'white';
    userCtx.fillRect(0, 0, userCanvas.width, userCanvas.height);
}

// Load characters from API
async function loadCharacters() {
    const selector = document.getElementById('charSelector');
    
    try {
        const response = await fetch('/api/radicals/hsk/1', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error('Failed to load characters');
        
        const radicals = await response.json();
        allCharacters = radicals;
        
        selector.innerHTML = '';
        
        radicals.slice(0, 20).forEach((radical, index) => {
            const char = radical.hanzi || radical.character || radical.simplified;
            if (!char) return;
            
            const btn = document.createElement('button');
            btn.className = 'char-btn' + (index === 0 ? ' active' : '');
            btn.textContent = char;
            btn.dataset.char = char;
            btn.dataset.pinyin = radical.pinyin || '';
            btn.dataset.meaning = radical.meaning || radical.english || '';
            btn.dataset.strokes = radical.stroke_count || radical.strokes || '';
            
            btn.addEventListener('click', function() {
                selectCharacter(this);
            });
            
            selector.appendChild(btn);
        });
        
        // Load first character
        if (radicals.length > 0) {
            const firstChar = radicals[0].hanzi || radicals[0].character || radicals[0].simplified;
            if (firstChar) {
                loadCharacter(firstChar, radicals[0]);
            }
        }
        
    } catch (error) {
        console.error('Error loading characters:', error);
        selector.innerHTML = '<p style="color: red; padding: 1rem;">Failed to load characters</p>';
    }
}

// Select character
function selectCharacter(btn) {
    document.querySelectorAll('.char-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    
    const charData = {
        hanzi: btn.dataset.char,
        pinyin: btn.dataset.pinyin,
        meaning: btn.dataset.meaning,
        stroke_count: btn.dataset.strokes
    };
    
    loadCharacter(btn.dataset.char, charData);
}

// Load character with HanziWriter
async function loadCharacter(char, charData) {
    currentChar = char;
    
    // Update character info
    document.getElementById('charPinyin').textContent = charData.pinyin || '-';
    document.getElementById('charMeaning').textContent = charData.meaning || '-';
    document.getElementById('charStrokes').textContent = charData.stroke_count || '-';
    
    // Clear previous writers
    document.getElementById('reference-canvas').innerHTML = '';
    document.getElementById('user-guide').innerHTML = '';
    
    // Hide result
    document.getElementById('resultCard').classList.remove('show');
    
    try {
        // Check if character data exists
        const dataUrl = `https://cdn.jsdelivr.net/npm/hanzi-writer-data@latest/${char}.json`;
        const checkResponse = await fetch(dataUrl);
        
        if (!checkResponse.ok) {
            throw new Error('Character data not available');
        }
        
        // Create reference writer (animated)
        referenceWriter = HanziWriter.create('reference-canvas', char, {
            width: 400,
            height: 400,
            padding: 20,
            showOutline: true,
            strokeAnimationSpeed: 1,
            delayBetweenStrokes: 200,
            strokeColor: '#62bfba',
            outlineColor: '#ddd',
            radicalColor: '#62bfba'
        });
        
        // Create guide writer (outline only)
        guideWriter = HanziWriter.create('user-guide', char, {
            width: userCanvas.width,
            height: userCanvas.height,
            padding: 20,
            showOutline: true,
            showCharacter: false,
            outlineColor: '#62bfba'
        });
        
        // Animate reference
        referenceWriter.animateCharacter();
        
        // Clear user canvas
        clearUserCanvas();
        
    } catch (error) {
        console.error('Error loading character:', error);
        alert(`Cannot load character "${char}". It may not be available in the HanziWriter database.`);
    }
}

// Setup drawing events
function setupDrawing() {
    let lastX = 0, lastY = 0;
    
    function getCoordinates(e, canvas) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        
        if (e.touches) {
            return {
                x: (e.touches[0].clientX - rect.left) * scaleX,
                y: (e.touches[0].clientY - rect.top) * scaleY
            };
        } else {
            return {
                x: (e.clientX - rect.left) * scaleX,
                y: (e.clientY - rect.top) * scaleY
            };
        }
    }
    
    // Mouse events
    userCanvas.addEventListener('mousedown', (e) => {
        isDrawing = true;
        const coords = getCoordinates(e, userCanvas);
        lastX = coords.x;
        lastY = coords.y;
    });
    
    userCanvas.addEventListener('mousemove', (e) => {
        if (!isDrawing) return;
        const coords = getCoordinates(e, userCanvas);
        
        userCtx.beginPath();
        userCtx.moveTo(lastX, lastY);
        userCtx.lineTo(coords.x, coords.y);
        userCtx.stroke();
        
        lastX = coords.x;
        lastY = coords.y;
    });
    
    userCanvas.addEventListener('mouseup', () => {
        isDrawing = false;
    });
    
    userCanvas.addEventListener('mouseleave', () => {
        isDrawing = false;
    });
    
    // Touch events
    userCanvas.addEventListener('touchstart', (e) => {
        e.preventDefault();
        isDrawing = true;
        const coords = getCoordinates(e, userCanvas);
        lastX = coords.x;
        lastY = coords.y;
    });
    
    userCanvas.addEventListener('touchmove', (e) => {
        e.preventDefault();
        if (!isDrawing) return;
        const coords = getCoordinates(e, userCanvas);
        
        userCtx.beginPath();
        userCtx.moveTo(lastX, lastY);
        userCtx.lineTo(coords.x, coords.y);
        userCtx.stroke();
        
        lastX = coords.x;
        lastY = coords.y;
    });
    
    userCanvas.addEventListener('touchend', () => {
        isDrawing = false;
    });
}

// Clear button
document.getElementById('clearBtn')?.addEventListener('click', () => {
    clearUserCanvas();
    document.getElementById('resultCard').classList.remove('show');
});

// Hint button - show/hide guide
document.getElementById('hintBtn')?.addEventListener('click', () => {
    const guide = document.getElementById('user-guide');
    guide.classList.toggle('show');
});

// Animate button
document.getElementById('animateBtn')?.addEventListener('click', () => {
    if (referenceWriter) {
        referenceWriter.animateCharacter();
    }
});

// Try again button
document.getElementById('tryAgainBtn')?.addEventListener('click', () => {
    clearUserCanvas();
    document.getElementById('resultCard').classList.remove('show');
});

// Submit button - check writing
document.getElementById('submitBtn')?.addEventListener('click', async () => {
    if (!currentChar) {
        alert('Please select a character first');
        return;
    }
    
    try {
        // Get reference canvas as image
        const refCanvas = await svgToCanvas(document.getElementById('reference-canvas'));
        const refImage = refCanvas.toDataURL('image/png');
        
        // Get user canvas
        const userImage = userCanvas.toDataURL('image/png');
        
        // Show loading
        document.getElementById('loadingOverlay').classList.add('show');
        document.getElementById('resultCard').classList.remove('show');
        
        // Call scoring API
        const response = await fetch('/api/score-writing', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                image_reference: refImage,
                image_user: userImage,
                character: currentChar
            })
        });
        
        const data = await response.json();
        
        // Hide loading
        document.getElementById('loadingOverlay').classList.remove('show');
        
        if (data.success || data.score !== undefined) {
            // Show result
            const score = data.score || 0;
            document.getElementById('scoreDisplay').textContent = score.toFixed(1);
            document.getElementById('scoreInterpretation').textContent = getInterpretation(score);
            document.getElementById('scoreDistance').textContent = data.distance ? data.distance.toFixed(4) : 'N/A';
            document.getElementById('resultCard').classList.add('show');
            
            // Scroll to result
            document.getElementById('resultCard').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            throw new Error(data.error || 'Failed to score writing');
        }
        
    } catch (error) {
        document.getElementById('loadingOverlay').classList.remove('show');
        console.error('Error:', error);
        alert('Error: ' + error.message + '. The scoring API might not be available.');
    }
});

// Convert SVG to Canvas
async function svgToCanvas(svgElement) {
    return new Promise((resolve) => {
        const svgData = new XMLSerializer().serializeToString(svgElement);
        const canvas = document.createElement('canvas');
        canvas.width = 400;
        canvas.height = 400;
        const ctx = canvas.getContext('2d');
        
        const img = new Image();
        img.onload = () => {
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, 400, 400);
            ctx.drawImage(img, 0, 0);
            resolve(canvas);
        };
        img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
    });
}

// Get score interpretation
function getInterpretation(score) {
    if (score >= 90) return '🎉 Excellent!';
    if (score >= 80) return '👍 Very Good!';
    if (score >= 70) return '😊 Good!';
    if (score >= 60) return '🙂 Not Bad!';
    if (score >= 50) return '😐 Keep Practicing!';
    return '💪 Try Again!';
}
