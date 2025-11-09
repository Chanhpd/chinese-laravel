<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vocabulary;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;

class VocabularyController extends Controller
{
    /**
     * Lấy danh sách vocabularies
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $lang = LanguageHelper::getLanguageCode($request);
            $query = Vocabulary::with(['topic.translations', 'translations']);

            // Search
            if ($request->has('search')) {
                $query->search($request->search);
            }

            // Filter by topic
            if ($request->has('topic_id')) {
                $query->byTopic($request->topic_id);
            }

            // Filter by level
            if ($request->has('level')) {
                $query->byLevel($request->level);
            }

            // Paginate
            $perPage = $request->get('per_page', 20);
            $vocabularies = $query->paginate($perPage);

            // Transform data
            $vocabularies->getCollection()->transform(function ($vocab) use ($lang) {
                return $this->transformVocabulary($vocab, $lang);
            });

            return response()->json([
                'success' => true,
                'language' => $lang,
                'data' => $vocabularies,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách vocabularies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy chi tiết một vocabulary
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        try {
            $lang = LanguageHelper::getLanguageCode($request);
            $query = Vocabulary::with(['topic.translations', 'translations']);

            $vocabulary = $query->findOrFail($id);

            // Transform data
            $vocabData = $this->transformVocabulary($vocabulary, $lang);

            return response()->json([
                'success' => true,
                'language' => $lang,
                'data' => $vocabData,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vocabulary không tồn tại',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Lấy vocabulary ngẫu nhiên
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function random(Request $request)
    {
        try {
            $lang = LanguageHelper::getLanguageCode($request);
            $query = Vocabulary::with(['topic.translations', 'translations']);

            // Filter by topic
            if ($request->has('topic_id')) {
                $query->byTopic($request->topic_id);
            }

            // Filter by level
            if ($request->has('level')) {
                $query->byLevel($request->level);
            }

            // Get random count
            $count = $request->get('count', 1);
            $vocabularies = $query->inRandomOrder()->limit($count)->get();

            // Transform data
            $vocabularies = $vocabularies->map(function ($vocab) use ($lang) {
                return $this->transformVocabulary($vocab, $lang);
            });

            return response()->json([
                'success' => true,
                'language' => $lang,
                'data' => $vocabularies,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy translation cho một vocabulary theo ngôn ngữ
     * 
     * @param int $id
     * @param string $languageCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function translation($id, $languageCode)
    {
        try {
            // Validate language code
            if (!LanguageHelper::isSupported($languageCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Language code không hợp lệ',
                ], 400);
            }

            $vocabulary = Vocabulary::with(['translations' => function ($query) use ($languageCode) {
                $query->where('language_code', $languageCode);
            }])->findOrFail($id);

            $translation = $vocabulary->translations->first();

            if (!$translation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Translation không tồn tại cho ngôn ngữ này',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'vocabulary' => $vocabulary,
                    'translation' => $translation,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Transform vocabulary data based on language
     */
    private function transformVocabulary($vocab, $lang)
    {
        $data = $vocab->toArray();
        
        if ($lang !== 'en') {
            // Find translation for this language
            $translation = $vocab->translations->where('language_code', $lang)->first();
            
            if ($translation) {
                $data['meaning'] = $translation->meaning;
                $data['example_translation'] = $translation->example_translation ?? $data['example_translation'];
            }
        }
        
        // Transform nested topic if exists
        if (isset($data['topic']) && is_array($data['topic'])) {
            if ($lang === 'zh') {
                // For Chinese, use name_zh if available
                $data['topic']['name'] = $data['topic']['name_zh'] ?? $data['topic']['name'];
            } elseif ($lang !== 'en') {
                // Find topic translation
                if (isset($vocab->topic) && $vocab->topic->translations) {
                    $topicTranslation = $vocab->topic->translations->where('language_code', $lang)->first();
                    if ($topicTranslation) {
                        $data['topic']['name'] = $topicTranslation->name;
                        $data['topic']['description'] = $topicTranslation->description ?? $data['topic']['description'];
                    }
                }
            }
            // Remove translations from nested topic
            unset($data['topic']['translations']);
        }
        
        // Remove translations array from response
        unset($data['translations']);
        
        return $data;
    }
}
