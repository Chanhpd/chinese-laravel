# Vocabulary Table Structure

Giúp tôi thực hiện cấu trúc bảng vocab cho ứng dụng học tiếng Trung của tôi.
Giữ nghĩa tiếng Anh làm mặc định (default) để truy cập nhanh, hiển thị khi chưa có bản dịch.

Nhưng vẫn có khả năng mở rộng sang 6 ngôn ngữ khác thông qua bảng translations.


Bảng topic sẽ chứa list các vocab theo từng chủ đề như : Chào hỏi, Gia đình, Mua sắm, Du lịch, v.v.
Topic(
    topicId: 1,
    name: 'Chào hỏi cơ bản',
    nameZh: '问候语',
    description: 'Các cách chào hỏi và giới thiệu bản thân trong tiếng Trung',
    imageUrl: 'https://example.com/image/greetings.jpg',
    isActive: true,
    sortOrder: 1,
    createdAt: DateTime.now().subtract(const Duration(days: 30)),
    updatedAt: DateTime.now().subtract(const Duration(days: 1)),
  ),


Tôi có cấu trúc tương đối của bảng vocab như sau : 
Vocabulary(
    id: 'v2',
    word: '你',
    phonetic: 'nǐ',
    pinyin: 'nǐ',
    simplified: '你',
    traditional: '你',
    partOfSpeech: 'pronoun',
    meaning: 'You (singular, informal)',
    meaningVi: 'Bạn',
    meaningZh: '你',
    exampleSentence: '你是学生吗？',
    exampleTranslation: 'Bạn là học sinh phải không?',
    exampleHighlight: '你',
    definition: 'Second person singular pronoun',
    radicalInfo: '亻(người)',
    strokeCount: 7,
    tonePattern: '3',
    relatedWords: ['您', '你们', '你的'],
    similarChars: ['妳', '尔'],
    pronunciationAudio: 'https://example.com/audio/ni.mp3',
    imageUrl: 'https://example.com/image/ni.jpg',
    level: 'HSK1',
    topic: mockChineseTopics[0],
    createdAt: DateTime.now().subtract(const Duration(days: 20)),
    updatedAt: DateTime.now().subtract(const Duration(days: 1)),
  ),