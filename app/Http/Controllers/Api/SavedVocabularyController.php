<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavedVocabulary;
use App\Models\Vocabulary;
use Illuminate\Http\Request;

class SavedVocabularyController extends Controller
{
    /**
     * Get all saved vocabularies for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = SavedVocabulary::forUser($user->id)
            ->with('vocabulary.topic');

        // Filter by topic if provided
        if ($request->has('topic_id')) {
            $query->byTopic($request->topic_id);
        }

        // Filter by review status
        if ($request->has('need_review') && $request->need_review) {
            $query->needReview();
        }

        // Filter by recent
        if ($request->has('recent_days')) {
            $query->recent($request->recent_days);
        }

        $savedVocabularies = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $savedVocabularies->map(function ($item) {
                return [
                    'id' => $item->id,
                    'vocabulary' => [
                        'id' => $item->vocabulary->id,
                        'word' => $item->vocabulary->word,
                        'pinyin' => $item->vocabulary->pinyin,
                        'simplified' => $item->vocabulary->simplified,
                        'traditional' => $item->vocabulary->traditional,
                        'meaning' => $item->vocabulary->meaning,
                        'meaning_zh' => $item->vocabulary->meaning_zh,
                        'part_of_speech' => $item->vocabulary->part_of_speech,
                        'example_sentence' => $item->vocabulary->example_sentence,
                        'example_translation' => $item->vocabulary->example_translation,
                        'topic' => [
                            'id' => $item->vocabulary->topic->id,
                            'name' => $item->vocabulary->topic->name,
                            'name_zh' => $item->vocabulary->topic->name_zh,
                            'level' => $item->vocabulary->topic->level,
                        ],
                    ],
                    'notes' => $item->notes,
                    'review_count' => $item->review_count,
                    'last_reviewed_at' => $item->last_reviewed_at,
                    'created_at' => $item->created_at,
                ];
            }),
            'meta' => [
                'current_page' => $savedVocabularies->currentPage(),
                'last_page' => $savedVocabularies->lastPage(),
                'per_page' => $savedVocabularies->perPage(),
                'total' => $savedVocabularies->total(),
            ],
        ]);
    }

    /**
     * Save a vocabulary for later study.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vocabulary_id' => 'required|exists:vocabularies,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();

        // Check if already saved
        $exists = SavedVocabulary::where('user_id', $user->id)
            ->where('vocabulary_id', $request->vocabulary_id)
            ->first();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Vocabulary already saved',
            ], 409);
        }

        $saved = SavedVocabulary::create([
            'user_id' => $user->id,
            'vocabulary_id' => $request->vocabulary_id,
            'notes' => $request->notes,
        ]);

        $saved->load('vocabulary.topic');

        return response()->json([
            'success' => true,
            'message' => 'Vocabulary saved successfully',
            'data' => [
                'id' => $saved->id,
                'vocabulary' => [
                    'id' => $saved->vocabulary->id,
                    'word' => $saved->vocabulary->word,
                    'simplified' => $saved->vocabulary->simplified,
                    'meaning' => $saved->vocabulary->meaning,
                ],
                'notes' => $saved->notes,
                'created_at' => $saved->created_at,
            ],
        ], 201);
    }

    /**
     * Update notes for a saved vocabulary.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        
        $saved = SavedVocabulary::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $saved->update([
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notes updated successfully',
            'data' => $saved,
        ]);
    }

    /**
     * Mark a saved vocabulary as reviewed.
     */
    public function markReviewed(Request $request, $id)
    {
        $user = $request->user();
        
        $saved = SavedVocabulary::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $saved->markAsReviewed();

        return response()->json([
            'success' => true,
            'message' => 'Marked as reviewed',
            'data' => [
                'review_count' => $saved->review_count,
                'last_reviewed_at' => $saved->last_reviewed_at,
            ],
        ]);
    }

    /**
     * Remove a saved vocabulary.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        $saved = SavedVocabulary::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $saved->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vocabulary removed from saved list',
        ]);
    }

    /**
     * Bulk save vocabularies.
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'vocabulary_ids' => 'required|array',
            'vocabulary_ids.*' => 'exists:vocabularies,id',
        ]);

        $user = $request->user();
        $saved = [];
        $alreadySaved = [];

        foreach ($request->vocabulary_ids as $vocabularyId) {
            $exists = SavedVocabulary::where('user_id', $user->id)
                ->where('vocabulary_id', $vocabularyId)
                ->exists();

            if (!$exists) {
                $saved[] = SavedVocabulary::create([
                    'user_id' => $user->id,
                    'vocabulary_id' => $vocabularyId,
                ]);
            } else {
                $alreadySaved[] = $vocabularyId;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Vocabularies saved successfully',
            'data' => [
                'saved_count' => count($saved),
                'already_saved_count' => count($alreadySaved),
            ],
        ], 201);
    }

    /**
     * Get statistics for saved vocabularies.
     */
    public function statistics(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total_saved' => SavedVocabulary::forUser($user->id)->count(),
            'need_review' => SavedVocabulary::forUser($user->id)->needReview()->count(),
            'reviewed_today' => SavedVocabulary::forUser($user->id)
                ->whereDate('last_reviewed_at', today())
                ->count(),
            'saved_this_week' => SavedVocabulary::forUser($user->id)
                ->where('created_at', '>=', now()->startOfWeek())
                ->count(),
            'by_topic' => SavedVocabulary::forUser($user->id)
                ->with('vocabulary.topic')
                ->get()
                ->groupBy('vocabulary.topic.name')
                ->map(function ($items) {
                    return $items->count();
                }),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
