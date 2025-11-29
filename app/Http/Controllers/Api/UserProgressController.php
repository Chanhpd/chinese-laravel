<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserTopicProgress;
use App\Models\Topic;
use App\Models\Vocabulary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserProgressController extends Controller
{
    /**
     * Get all progress for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $progress = UserTopicProgress::forUser($user->id)
            ->with('topic')
            ->orderBy('last_studied_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'topic' => [
                        'id' => $item->topic->id,
                        'name' => $item->topic->name,
                        'name_zh' => $item->topic->name_zh,
                        'level' => $item->topic->level,
                        'description' => $item->topic->description,
                    ],
                    'completed_words' => $item->completed_words,
                    'total_words' => $item->total_words,
                    'progress_percentage' => $item->progress_percentage,
                    'mastery_level' => $item->mastery_level,
                    'last_studied_at' => $item->last_studied_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $progress,
        ]);
    }

    /**
     * Get progress for a specific topic.
     */
    public function show(Request $request, $topicId)
    {
        $user = $request->user();
        
        $topic = Topic::findOrFail($topicId);
        
        // Get or create progress for this topic
        $progress = UserTopicProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'topic_id' => $topicId,
            ],
            [
                'completed_words' => 0,
                'total_words' => Vocabulary::where('topic_id', $topicId)->count(),
                'mastery_level' => 'beginner',
            ]
        );

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $progress->id,
                'topic' => [
                    'id' => $topic->id,
                    'name' => $topic->name,
                    'name_zh' => $topic->name_zh,
                    'level' => $topic->level,
                    'description' => $topic->description,
                ],
                'completed_words' => $progress->completed_words,
                'total_words' => $progress->total_words,
                'progress_percentage' => $progress->progress_percentage,
                'mastery_level' => $progress->mastery_level,
                'last_studied_at' => $progress->last_studied_at,
            ],
        ]);
    }

    /**
     * Update progress for a topic (mark word as completed).
     */
    public function update(Request $request, $topicId)
    {
        $request->validate([
            'vocabulary_id' => 'nullable|exists:vocabularies,id',
            'action' => 'required|in:increment,decrement,reset',
        ]);

        $user = $request->user();
        
        // Get or create progress
        $progress = UserTopicProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'topic_id' => $topicId,
            ],
            [
                'completed_words' => 0,
                'total_words' => Vocabulary::where('topic_id', $topicId)->count(),
                'mastery_level' => 'beginner',
            ]
        );

        // Update based on action
        switch ($request->action) {
            case 'increment':
                $progress->markWordCompleted();
                break;
            case 'decrement':
                if ($progress->completed_words > 0) {
                    $progress->decrement('completed_words');
                    $progress->updateMasteryLevel();
                }
                break;
            case 'reset':
                $progress->update([
                    'completed_words' => 0,
                    'mastery_level' => 'beginner',
                ]);
                break;
        }

        $progress->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully',
            'data' => [
                'completed_words' => $progress->completed_words,
                'total_words' => $progress->total_words,
                'progress_percentage' => $progress->progress_percentage,
                'mastery_level' => $progress->mastery_level,
            ],
        ]);
    }

    /**
     * Get statistics for user's learning progress.
     */
    public function statistics(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total_topics_started' => UserTopicProgress::forUser($user->id)->count(),
            'total_words_learned' => UserTopicProgress::forUser($user->id)->sum('completed_words'),
            'mastery_breakdown' => [
                'beginner' => UserTopicProgress::forUser($user->id)->byMasteryLevel('beginner')->count(),
                'intermediate' => UserTopicProgress::forUser($user->id)->byMasteryLevel('intermediate')->count(),
                'advanced' => UserTopicProgress::forUser($user->id)->byMasteryLevel('advanced')->count(),
                'mastered' => UserTopicProgress::forUser($user->id)->byMasteryLevel('mastered')->count(),
            ],
            'recent_activity' => UserTopicProgress::forUser($user->id)
                ->whereNotNull('last_studied_at')
                ->orderBy('last_studied_at', 'desc')
                ->limit(5)
                ->with('topic')
                ->get()
                ->map(function ($item) {
                    return [
                        'topic_name' => $item->topic->name,
                        'topic_name_zh' => $item->topic->name_zh,
                        'last_studied_at' => $item->last_studied_at,
                        'progress_percentage' => $item->progress_percentage,
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get progress by HSK level.
     */
    public function byLevel(Request $request, $level)
    {
        $user = $request->user();
        
        $progress = UserTopicProgress::forUser($user->id)
            ->whereHas('topic', function ($query) use ($level) {
                $query->where('level', strtoupper($level));
            })
            ->with('topic')
            ->get()
            ->map(function ($item) {
                return [
                    'topic_id' => $item->topic_id,
                    'topic_name' => $item->topic->name,
                    'topic_name_zh' => $item->topic->name_zh,
                    'progress_percentage' => $item->progress_percentage,
                    'mastery_level' => $item->mastery_level,
                ];
            });

        return response()->json([
            'success' => true,
            'level' => strtoupper($level),
            'data' => $progress,
        ]);
    }
}
