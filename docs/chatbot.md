

```php 
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'message' => 'required|string',
        ]);

        $apiKey = env('GEMINI_API_KEY');
        $url = env('GEMINI_API_URL') . '?key=' . $apiKey;

        // 2. Cấu hình System Instruction (Phiên bản "Hardcore")
        $systemPrompt = <<<'EOT'
You are a professional Mandarin Chinese Tutor AI.
Your goal is to help users learn Chinese based on their native language.

**CRITICAL RULES:**

1. **LANGUAGE DETECTION (Quan trọng):**
   - If the user asks in **Vietnamese**, your explanation and response MUST be in **Vietnamese**.
   - If the user asks in **English**, your explanation and response MUST be in **English**.
   - If the user asks in any other language (e.g., Japanese, Korean), respond in **that specific language**.
   - If the user speaks **only Chinese**, respond in simple Chinese suitable for a learner.

2. **SCOPE:**
   - Answer ONLY questions related to Chinese language (Vocab, Grammar, Culture, HSK).
   - If the user asks about unrelated topics (Coding, Math, Weather...), refuse politely **in the same language** the user used.
     - (Example VN: "Xin lỗi, tôi chỉ giúp bạn học tiếng Trung.")
     - (Example EN: "Sorry, I can only help you learn Chinese.")

3. **FORMAT:**
   - Always provide: Hanzi (Chữ Hán) -> Pinyin -> Meaning (in user's language).

EOT;
        // 3. Chuẩn bị Payload
        $payload = [
            "system_instruction" => [
                "parts" => [
                    ["text" => $systemPrompt]
                ]
            ],
            "contents" => [
                [
                    "parts" => [
                        ["text" => $request->message]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.3, // Giữ thấp để AI nghiêm túc tuân thủ luật
                "maxOutputTokens" => 1000,
            ]
        ];

        // 4. Gửi Request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        // 5. Xử lý kết quả
        if ($response->successful()) {
            $data = $response->json();
            $botReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I\'m having a processing error.';
            
            return response()->json([
                'status' => 'success',
                'bot_reply' => $botReply
            ]);
        } else {
            // Log lỗi để debug (xem trong storage/logs/laravel.log)
            \Log::error('Gemini API Error: ' . $response->body());

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to connect to AI tutor',
            ], 500);
        }
    }
}
```