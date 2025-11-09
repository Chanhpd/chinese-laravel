<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * Lấy danh sách tất cả topics
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $lang = LanguageHelper::getLanguageCode($request);
            $query = Topic::query();

            // Filter by active status
            if ($request->has('active')) {
                $query->where('is_active', $request->boolean('active'));
            }

            // Include vocabulary count
            if ($request->boolean('with_count')) {
                $query->withCount('vocabularies');
            }

            // Include vocabularies
            if ($request->boolean('with_vocabularies')) {
                $query->with('vocabularies');
            }

            // Load translations
            $query->with('translations');

            $topics = $query->ordered()->get();

            // Transform data based on language
            $topics = $topics->map(function ($topic) use ($lang) {
                return $this->transformTopic($topic, $lang);
            });

            return response()->json([
                'success' => true,
                'language' => $lang,
                'data' => $topics,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách topics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy chi tiết một topic
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        try {
            $lang = LanguageHelper::getLanguageCode($request);
            $query = Topic::query();

            // Include vocabularies
            if ($request->boolean('with_vocabularies')) {
                $query->with('vocabularies.translations');
            }

            // Load translations
            $query->with('translations');

            $topic = $query->findOrFail($id);

            // Transform data
            $topicData = $this->transformTopic($topic, $lang);

            return response()->json([
                'success' => true,
                'language' => $lang,
                'data' => $topicData,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Topic không tồn tại',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Lấy danh sách vocabularies theo topic
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function vocabularies($id, Request $request)
    {
        try {
            $lang = LanguageHelper::getLanguageCode($request);
            $topic = Topic::findOrFail($id);

            $query = $topic->vocabularies();

            // Include translations
            if ($request->boolean('with_translations')) {
                $query->with('translations');
            }

            // Filter by level
            if ($request->has('level')) {
                $query->byLevel($request->level);
            }

            // Paginate
            $perPage = $request->get('per_page', 20);
            $vocabularies = $query->paginate($perPage);

            // Transform vocabularies
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
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Transform topic data based on language
     */
    private function transformTopic($topic, $lang)
    {
        $data = $topic->toArray();
        
        // Replace name and description with localized version
        $data['name'] = $topic->getLocalizedName($lang);
        $data['description'] = $topic->getLocalizedDescription($lang);
        
        // Remove translations array from response (keep it clean)
        unset($data['translations']);
        
        return $data;
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
                $data['example_translation'] = $translation->example_translation;
            }
        }
        
        // Remove translations array from response
        unset($data['translations']);
        
        return $data;
    }
}
