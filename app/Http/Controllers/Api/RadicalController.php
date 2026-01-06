<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Radical;
use Illuminate\Http\Request;

class RadicalController extends Controller
{
    /**
     * Get radicals by HSK level number
     * 
     * @param int $levelNumber Level number (1-9 for HSK)
     * @return \Illuminate\Http\JsonResponse
     * 
     * Example: GET /api/radicals/hsk/1
     * Example: GET /api/radicals/hsk/2
     */
    public function getRadicalsByLevel($levelNumber)
    {
        try {
            // Find the HSK level
            $level = Level::where('test_type', 'HSK')
                ->where('level_number', $levelNumber)
                ->first();

            if (!$level) {
                return response()->json([
                    'error' => "HSK level {$levelNumber} not found",
                ], 404);
            }

            // Get all radicals for this level, ordered by frequency rank
            $radicals = Radical::where('level_id', $level->id)
                ->orderBy('frequency_rank', 'asc')
                ->get();

            // Format radicals to match JSON structure
            $formattedRadicals = $radicals->map(function ($radical) {
                return $radical->toJsonFormat();
            });

            return response()->json($formattedRadicals);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching radicals.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all radicals for HSK, grouped by level
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     * Example: GET /api/radicals/hsk
     */
    public function getAllHSKRadicals()
    {
        try {
            // Get all HSK levels with radicals
            $levels = Level::where('test_type', 'HSK')
                ->with(['radicals' => function ($query) {
                    $query->orderBy('frequency_rank', 'asc');
                }])
                ->orderBy('level_number')
                ->get()
                ->map(function ($level) {
                    return [
                        'level' => $level->level_name,
                        'level_number' => $level->level_number,
                        'radicals' => $level->radicals->map(function ($radical) {
                            return $radical->toJsonFormat();
                        }),
                    ];
                });

            return response()->json($levels);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching radicals.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all levels with radical count
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     * Example: GET /api/radicals/levels
     */
    public function getLevels()
    {
        try {
            $levels = Level::where('test_type', 'HSK')
                ->withCount('radicals')
                ->orderBy('level_number')
                ->get()
                ->map(function ($level) {
                    return [
                        'id' => $level->id,
                        'test_type' => $level->test_type,
                        'level_number' => $level->level_number,
                        'level_name' => $level->level_name,
                        'radical_count' => $level->radicals_count,
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
     * Search radicals by keyword
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * Example: GET /api/radicals/search?q=的&level=1
     */
    public function searchRadicals(Request $request)
    {
        try {
            $query = Radical::query();

            // Search by keyword
            if ($request->has('q') && $request->q) {
                $keyword = $request->q;
                $query->where(function ($q) use ($keyword) {
                    $q->where('hanzi', 'LIKE', "%{$keyword}%")
                        ->orWhere('traditional', 'LIKE', "%{$keyword}%")
                        ->orWhere('pinyin', 'LIKE', "%{$keyword}%")
                        ->orWhere('meaning', 'LIKE', "%{$keyword}%")
                        ->orWhere('meaning_vi', 'LIKE', "%{$keyword}%")
                        ->orWhere('meaning_en', 'LIKE', "%{$keyword}%")
                        ->orWhere('meaning_cn', 'LIKE', "%{$keyword}%");
                });
            }

            // Filter by level number
            if ($request->has('level') && $request->level) {
                $level = Level::where('test_type', 'HSK')
                    ->where('level_number', $request->level)
                    ->first();
                
                if ($level) {
                    $query->where('level_id', $level->id);
                }
            }

            // Filter by stroke count
            if ($request->has('strokes') && $request->strokes) {
                $query->where('stroke_count', $request->strokes);
            }

            // Filter by favorites
            if ($request->has('favorite') && $request->favorite) {
                $query->where('is_favorite', 1);
            }

            // Order by frequency rank or stroke count
            $orderBy = $request->get('order_by', 'frequency_rank');
            if (in_array($orderBy, ['frequency_rank', 'stroke_count'])) {
                $query->orderBy($orderBy, 'asc');
            }

            $radicals = $query->limit(100)->get();

            // Format radicals to match JSON structure
            $formattedRadicals = $radicals->map(function ($radical) {
                return array_merge(
                    $radical->toJsonFormat(),
                    [
                        'level_name' => $radical->level->level_name ?? null,
                    ]
                );
            });

            return response()->json($formattedRadicals);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while searching radicals.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get radical statistics
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     * Example: GET /api/radicals/statistics
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total_radicals' => Radical::count(),
                'favorite_count' => Radical::where('is_favorite', 1)->count(),
                'by_level' => Level::where('test_type', 'HSK')
                    ->withCount('radicals')
                    ->orderBy('level_number')
                    ->get()
                    ->map(function ($level) {
                        return [
                            'level' => $level->level_name,
                            'count' => $level->radicals_count,
                        ];
                    }),
                'by_stroke_count' => Radical::selectRaw('stroke_count, COUNT(*) as count')
                    ->whereNotNull('stroke_count')
                    ->groupBy('stroke_count')
                    ->orderBy('stroke_count')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'strokes' => $item->stroke_count,
                            'count' => $item->count,
                        ];
                    }),
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching statistics.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle favorite status
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * 
     * Example: POST /api/radicals/1/favorite
     */
    public function toggleFavorite($id)
    {
        try {
            $radical = Radical::findOrFail($id);
            $radical->is_favorite = !$radical->is_favorite;
            $radical->save();

            return response()->json([
                'success' => true,
                'is_favorite' => $radical->is_favorite,
                'message' => $radical->is_favorite ? 'Added to favorites' : 'Removed from favorites',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating favorite status.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Score user's handwriting against reference
     * This is a simple placeholder - you can integrate with Python ML model
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * Example: POST /api/score-writing
     * Body: { "image_reference": "base64...", "image_user": "base64...", "character": "好" }
     */
    public function scoreWriting(Request $request)
    {
        try {
            $request->validate([
                'image_reference' => 'required|string',
                'image_user' => 'required|string',
                'character' => 'nullable|string',
            ]);

            // For now, return a mock score
            // In production, you would call your Python ML model API here
            // Example: use Guzzle to call Python Flask/FastAPI endpoint
            
            $mockScore = rand(60, 95);
            $mockDistance = rand(100, 1000) / 10000;

            return response()->json([
                'success' => true,
                'score' => $mockScore,
                'distance' => $mockDistance,
                'interpretation' => $this->getScoreInterpretation($mockScore),
                'message' => 'Writing scored successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while scoring writing.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get score interpretation
     */
    private function getScoreInterpretation($score)
    {
        if ($score >= 90) return 'Excellent!';
        if ($score >= 80) return 'Very Good!';
        if ($score >= 70) return 'Good!';
        if ($score >= 60) return 'Not Bad!';
        if ($score >= 50) return 'Keep Practicing!';
        return 'Try Again!';
    }
}
