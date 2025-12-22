<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\UserLevelProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserLevelProgressController extends Controller
{
    /**
     * Get all level progress for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        
        $progress = UserLevelProgress::with('level')
            ->where('user_id', $user->id)
            ->orderBy('level_id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $progress->map(fn($p) => $p->toApiFormat()),
        ]);
    }

    /**
     * Get progress for a specific level.
     */
    public function show($levelId)
    {
        $user = Auth::user();

        $progress = UserLevelProgress::with('level')
            ->where('user_id', $user->id)
            ->where('level_id', $levelId)
            ->first();

        if (!$progress) {
            // Create initial progress if not exists
            $level = Level::findOrFail($levelId);
            $progress = $this->createInitialProgress($user->id, $level);
        }

        return response()->json([
            'success' => true,
            'data' => $progress->toApiFormat(),
        ]);
    }

    /**
     * Mark a word as completed.
     */
    public function markWordCompleted(Request $request, $levelId)
    {
        $request->validate([
            'word_id' => 'required|exists:word,id',
        ]);

        $user = Auth::user();

        $progress = UserLevelProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'level_id' => $levelId,
            ],
            [
                'completed_words' => 0,
                'total_words' => 0,
                'completed_radicals' => 0,
                'total_radicals' => 0,
            ]
        );

        // Sync totals if needed
        if ($progress->total_words == 0) {
            $progress->syncTotals();
        }

        $progress->markWordCompleted();

        return response()->json([
            'success' => true,
            'message' => 'Word marked as completed',
            'data' => $progress->toApiFormat(),
        ]);
    }

    /**
     * Mark a radical as completed.
     */
    public function markRadicalCompleted(Request $request, $levelId)
    {
        $request->validate([
            'radical_id' => 'required|exists:radical,id',
        ]);

        $user = Auth::user();

        $progress = UserLevelProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'level_id' => $levelId,
            ],
            [
                'completed_words' => 0,
                'total_words' => 0,
                'completed_radicals' => 0,
                'total_radicals' => 0,
            ]
        );

        // Sync totals if needed
        if ($progress->total_radicals == 0) {
            $progress->syncTotals();
        }

        $progress->markRadicalCompleted();

        return response()->json([
            'success' => true,
            'message' => 'Radical marked as completed',
            'data' => $progress->toApiFormat(),
        ]);
    }

    /**
     * Get statistics for all levels.
     */
    public function statistics()
    {
        $user = Auth::user();

        $allProgress = UserLevelProgress::with('level')
            ->where('user_id', $user->id)
            ->get();

        $stats = [
            'total_levels' => $allProgress->count(),
            'mastered_levels' => $allProgress->where('mastery_level', 'mastered')->count(),
            'advanced_levels' => $allProgress->where('mastery_level', 'advanced')->count(),
            'intermediate_levels' => $allProgress->where('mastery_level', 'intermediate')->count(),
            'beginner_levels' => $allProgress->where('mastery_level', 'beginner')->count(),
            'total_words_completed' => $allProgress->sum('completed_words'),
            'total_words' => $allProgress->sum('total_words'),
            'total_radicals_completed' => $allProgress->sum('completed_radicals'),
            'total_radicals' => $allProgress->sum('total_radicals'),
            'last_studied_level' => $allProgress->sortByDesc('last_studied_at')->first()?->level?->level_name,
            'last_studied_at' => $allProgress->sortByDesc('last_studied_at')->first()?->last_studied_at?->toIso8601String(),
        ];

        // Calculate overall percentage
        $totalItems = $stats['total_words'] + $stats['total_radicals'];
        $completedItems = $stats['total_words_completed'] + $stats['total_radicals_completed'];
        $stats['overall_percentage'] = $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Initialize progress for a level.
     */
    public function initializeProgress($levelId)
    {
        $user = Auth::user();
        $level = Level::findOrFail($levelId);

        $progress = UserLevelProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'level_id' => $levelId,
            ],
            [
                'completed_words' => 0,
                'total_words' => 0,
                'completed_radicals' => 0,
                'total_radicals' => 0,
            ]
        );

        $progress->syncTotals();

        return response()->json([
            'success' => true,
            'message' => 'Level progress initialized',
            'data' => $progress->toApiFormat(),
        ]);
    }

    /**
     * Reset progress for a level.
     */
    public function resetProgress($levelId)
    {
        $user = Auth::user();

        $progress = UserLevelProgress::where('user_id', $user->id)
            ->where('level_id', $levelId)
            ->first();

        if (!$progress) {
            return response()->json([
                'success' => false,
                'message' => 'Progress not found',
            ], 404);
        }

        $progress->completed_words = 0;
        $progress->completed_radicals = 0;
        $progress->mastery_level = 'beginner';
        $progress->last_studied_at = null;
        $progress->save();

        return response()->json([
            'success' => true,
            'message' => 'Progress reset successfully',
            'data' => $progress->toApiFormat(),
        ]);
    }

    /**
     * Helper method to create initial progress.
     */
    private function createInitialProgress($userId, $level)
    {
        $progress = UserLevelProgress::create([
            'user_id' => $userId,
            'level_id' => $level->id,
            'completed_words' => 0,
            'total_words' => $level->words()->count(),
            'completed_radicals' => 0,
            'total_radicals' => $level->radicals()->count(),
        ]);

        $progress->load('level');
        return $progress;
    }
}
