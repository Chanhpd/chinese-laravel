<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Word;
use Illuminate\Http\Request;

class WordController extends Controller
{
    /**
     * Get words by test type and level number
     * 
     * @param string $testType HSK or TOCFL
     * @param int $levelNumber Level number (1-9 for HSK, 1-6 for TOCFL)
     * @return \Illuminate\Http\JsonResponse
     * 
     * Example: GET /api/words/hsk/1
     * Example: GET /api/words/tocfl/2
     */
    public function getWordsByLevel($testType, $levelNumber)
    {
        try {
            // Validate test type
            $testType = strtoupper($testType);
            if (!in_array($testType, ['HSK', 'TOCFL'])) {
                return response()->json([
                    'error' => 'Invalid test type. Use HSK or TOCFL.',
                ], 400);
            }

            // Find the level
            $level = Level::where('test_type', $testType)
                ->where('level_number', $levelNumber)
                ->first();

            if (!$level) {
                return response()->json([
                    'error' => "Level not found for {$testType} level {$levelNumber}",
                ], 404);
            }

            // Get all words for this level
            $words = Word::where('level_id', $level->id)->get();

            // Format words to match JSON structure
            $formattedWords = $words->map(function ($word) {
                return $word->toJsonFormat();
            });

            return response()->json($formattedWords);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching words.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all levels with word count
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     * Example: GET /api/words/levels
     */
    public function getLevels()
    {
        try {
            $levels = Level::withCount('words')
                ->orderBy('test_type')
                ->orderBy('level_number')
                ->get()
                ->map(function ($level) {
                    return [
                        'id' => $level->id,
                        'test_type' => $level->test_type,
                        'level_number' => $level->level_number,
                        'level_name' => $level->level_name,
                        'word_count' => $level->words_count,
                    ];
                });

            return response()->json($levels);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching levels.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all words for a specific test type
     * 
     * @param string $testType HSK or TOCFL
     * @return \Illuminate\Http\JsonResponse
     * 
     * Example: GET /api/words/hsk
     * Example: GET /api/words/tocfl
     */
    public function getWordsByTestType($testType)
    {
        try {
            // Validate test type
            $testType = strtoupper($testType);
            if (!in_array($testType, ['HSK', 'TOCFL'])) {
                return response()->json([
                    'error' => 'Invalid test type. Use HSK or TOCFL.',
                ], 400);
            }

            // Get all levels for this test type
            $levelIds = Level::where('test_type', $testType)
                ->pluck('id');

            if ($levelIds->isEmpty()) {
                return response()->json([
                    'error' => "No levels found for {$testType}",
                ], 404);
            }

            // Get all words for these levels, grouped by level
            $levels = Level::where('test_type', $testType)
                ->with('words')
                ->orderBy('level_number')
                ->get()
                ->map(function ($level) {
                    return [
                        'level' => $level->level_name,
                        'level_number' => $level->level_number,
                        'words' => $level->words->map(function ($word) {
                            return $word->toJsonFormat();
                        }),
                    ];
                });

            return response()->json($levels);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching words.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search words by keyword
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * Example: GET /api/words/search?q=你好&test_type=HSK&level=1
     */
    public function searchWords(Request $request)
    {
        try {
            $query = Word::query();

            // Search by keyword
            if ($request->has('q') && $request->q) {
                $keyword = $request->q;
                $query->where(function ($q) use ($keyword) {
                    $q->where('word', 'LIKE', "%{$keyword}%")
                        ->orWhere('pinyin', 'LIKE', "%{$keyword}%")
                        ->orWhere('meaning_vi', 'LIKE', "%{$keyword}%")
                        ->orWhere('meaning_en', 'LIKE', "%{$keyword}%");
                });
            }

            // Filter by test type
            if ($request->has('test_type') && $request->test_type) {
                $testType = strtoupper($request->test_type);
                $levelIds = Level::where('test_type', $testType)->pluck('id');
                $query->whereIn('level_id', $levelIds);
            }

            // Filter by level number
            if ($request->has('level') && $request->level) {
                $testType = strtoupper($request->test_type ?? 'HSK');
                $level = Level::where('test_type', $testType)
                    ->where('level_number', $request->level)
                    ->first();
                
                if ($level) {
                    $query->where('level_id', $level->id);
                }
            }

            $words = $query->limit(100)->get();

            // Format words to match JSON structure
            $formattedWords = $words->map(function ($word) {
                return array_merge(
                    $word->toJsonFormat(),
                    [
                        'level_name' => $word->level->level_name ?? null,
                    ]
                );
            });

            return response()->json($formattedWords);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while searching words.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
