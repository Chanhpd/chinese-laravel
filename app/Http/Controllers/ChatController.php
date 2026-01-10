<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ChatHistory;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'message' => 'required|string',
        ]);

        $apiKey = env('GEMINI_API_KEY');
        
        // Kiểm tra API key
        if (empty($apiKey)) {
            return response()->json([
                'status' => 'error',
                'message' => 'GEMINI_API_KEY chưa được cấu hình trong file .env',
            ], 500);
        }
        
        // Build URL đúng cách
        $baseUrl = env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent');
        $url = $baseUrl . '?key=' . $apiKey;

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
        // 3. Chuẩn bị Payload với lịch sử chat của user
        $conversationHistory = [];
        
        // Nếu user đã đăng nhập, lấy lịch sử chat gần nhất của CHÍNH user đó
        if ($request->user()) {
            $recentChats = ChatHistory::where('user_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->limit(10) // Lấy 10 câu gần nhất
                ->get()
                ->reverse(); // Đảo ngược để thứ tự cũ -> mới
            
            // Xây dựng conversation history từ lịch sử
            foreach ($recentChats as $chat) {
                $conversationHistory[] = [
                    "role" => "user",
                    "parts" => [["text" => $chat->message]]
                ];
                $conversationHistory[] = [
                    "role" => "model",
                    "parts" => [["text" => $chat->response]]
                ];
            }
        }
        
        // Thêm system instruction vào đầu conversation (nếu chưa có lịch sử)
        if (empty($conversationHistory)) {
            $fullMessage = "System Instructions:\n" . $systemPrompt . "\n\nUser Question:\n" . $request->message;
            $conversationHistory[] = [
                "parts" => [["text" => $fullMessage]]
            ];
        } else {
            // Nếu đã có lịch sử, chỉ thêm system instruction nhẹ hơn
            $conversationHistory[0]["parts"][0]["text"] = 
                "System: " . trim(explode("\n\n", $systemPrompt)[0]) . "\n\n" . 
                $conversationHistory[0]["parts"][0]["text"];
            
            // Thêm câu hỏi hiện tại
            $conversationHistory[] = [
                "role" => "user",
                "parts" => [["text" => $request->message]]
            ];
        }
        
        $payload = [
            "contents" => $conversationHistory,
            "generationConfig" => [
                "temperature" => 0.3, // Giữ thấp để AI nghiêm túc tuân thủ luật
                "maxOutputTokens" => 1000,
            ]
        ];

        // 4. Gửi Request
        // Note: withoutVerifying() chỉ dùng cho development, production nên dùng SSL certificate đúng cách
        $response = Http::withoutVerifying()
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->timeout(30) // Add timeout 30 seconds
            ->post($url, $payload);

        // 5. Xử lý kết quả
        if ($response->successful()) {
            $data = $response->json();
            $botReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I\'m having a processing error.';
            
            // Debug log
            \Log::info('Chat successful', [
                'user_id' => $request->user() ? $request->user()->id : 'guest',
                'has_user' => $request->user() ? 'yes' : 'no',
                'message' => $request->message,
                'reply_length' => strlen($botReply)
            ]);
            
            // 6. Lưu lịch sử chat nếu user đã đăng nhập
            if ($request->user()) {
                try {
                    $chatHistory = ChatHistory::create([
                        'user_id' => $request->user()->id,
                        'message' => $request->message,
                        'response' => $botReply,
                        'language' => $this->detectLanguage($request->message),
                    ]);
                    \Log::info('Chat history saved', ['id' => $chatHistory->id]);
                } catch (\Exception $e) {
                    \Log::error('Failed to save chat history: ' . $e->getMessage());
                }
            } else {
                \Log::info('Chat not saved - user not authenticated');
            }
            
            return response()->json([
                'success' => true,
                'status' => 'success',
                'response' => $botReply,
                'bot_reply' => $botReply // backward compatibility
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

    /**
     * Lấy lịch sử chat của user (Yêu cầu authentication)
     */
    public function history(Request $request)
    {
        $limit = $request->get('limit', 50);
        $page = $request->get('page', 1);

        $histories = ChatHistory::forUser($request->user()->id)
            ->recent($limit)
            ->paginate($limit);

        return response()->json([
            'status' => 'success',
            'data' => $histories->items(),
            'pagination' => [
                'current_page' => $histories->currentPage(),
                'last_page' => $histories->lastPage(),
                'per_page' => $histories->perPage(),
                'total' => $histories->total(),
            ]
        ]);
    }

    /**
     * Xóa một chat history
     */
    public function deleteHistory(Request $request, $id)
    {
        $history = ChatHistory::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$history) {
            return response()->json([
                'status' => 'error',
                'message' => 'Chat history not found',
            ], 404);
        }

        $history->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Chat history deleted successfully',
        ]);
    }

    /**
     * Xóa toàn bộ lịch sử chat của user
     */
    public function clearHistory(Request $request)
    {
        $deleted = ChatHistory::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Deleted {$deleted} chat histories",
        ]);
    }

    /**
     * Phát hiện ngôn ngữ đơn giản
     */
    private function detectLanguage($text)
    {
        // Phát hiện tiếng Việt
        if (preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/u', $text)) {
            return 'vi';
        }
        
        // Phát hiện tiếng Trung
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $text)) {
            return 'zh';
        }
        
        // Mặc định là English
        return 'en';
    }
}
