<?php

namespace App\Http\Controllers;

use App\Models\UserLevelProgress;
use App\Models\UserTopicProgress;
use App\Models\SavedVocabulary;
use App\Models\UserStreak;
use App\Models\UserExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressDashboardController extends Controller
{
    /**
     * Get comprehensive learning dashboard for user.
     */
    public function index()
    {
        $user = Auth::user();

        $dashboard = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'streak' => $this->getStreakData($user->id),
            'topic_progress' => $this->getTopicProgressSummary($user->id),
            'level_progress' => $this->getLevelProgressSummary($user->id),
            'saved_vocabularies' => $this->getSavedVocabularySummary($user->id),
            'exam_progress' => $this->getExamProgressSummary($user->id),
            'recent_activities' => $this->getRecentActivities($user->id),
        ];

        return response()->json([
            'success' => true,
            'data' => $dashboard,
        ]);
    }

    /**
     * Get streak data.
     */
    private function getStreakData($userId)
    {
        $streak = UserStreak::where('user_id', $userId)->first();

        if (!$streak) {
            return [
                'current_streak' => 0,
                'longest_streak' => 0,
                'total_check_in_days' => 0,
                'last_check_in' => null,
                'weekly_check_ins' => [],
            ];
        }

        return [
            'current_streak' => $streak->streak_count,
            'longest_streak' => $streak->longest_streak,
            'total_check_in_days' => $streak->total_check_in_days,
            'last_check_in' => $streak->last_check_in_date?->toDateString(),
            'weekly_check_ins' => $streak->weekly_check_ins ?? [],
        ];
    }

    /**
     * Get topic progress summary.
     */
    private function getTopicProgressSummary($userId)
    {
        $progress = UserTopicProgress::with('topic')
            ->where('user_id', $userId)
            ->get();

        return [
            'total_topics' => $progress->count(),
            'mastered' => $progress->where('mastery_level', 'mastered')->count(),
            'advanced' => $progress->where('mastery_level', 'advanced')->count(),
            'intermediate' => $progress->where('mastery_level', 'intermediate')->count(),
            'beginner' => $progress->where('mastery_level', 'beginner')->count(),
            'total_words_completed' => $progress->sum('completed_words'),
            'total_words' => $progress->sum('total_words'),
            'overall_percentage' => $progress->sum('total_words') > 0 
                ? round(($progress->sum('completed_words') / $progress->sum('total_words')) * 100, 2) 
                : 0,
        ];
    }

    /**
     * Get level progress summary.
     */
    private function getLevelProgressSummary($userId)
    {
        $progress = UserLevelProgress::with('level')
            ->where('user_id', $userId)
            ->get();

        $totalItems = $progress->sum('total_words') + $progress->sum('total_radicals');
        $completedItems = $progress->sum('completed_words') + $progress->sum('completed_radicals');

        return [
            'total_levels' => $progress->count(),
            'mastered' => $progress->where('mastery_level', 'mastered')->count(),
            'advanced' => $progress->where('mastery_level', 'advanced')->count(),
            'intermediate' => $progress->where('mastery_level', 'intermediate')->count(),
            'beginner' => $progress->where('mastery_level', 'beginner')->count(),
            'words_completed' => $progress->sum('completed_words'),
            'total_words' => $progress->sum('total_words'),
            'radicals_completed' => $progress->sum('completed_radicals'),
            'total_radicals' => $progress->sum('total_radicals'),
            'overall_percentage' => $totalItems > 0 
                ? round(($completedItems / $totalItems) * 100, 2) 
                : 0,
        ];
    }

    /**
     * Get saved vocabulary summary.
     */
    private function getSavedVocabularySummary($userId)
    {
        $saved = SavedVocabulary::where('user_id', $userId)->get();

        return [
            'total_saved' => $saved->count(),
            'reviewed' => $saved->where('review_count', '>', 0)->count(),
            'need_review' => $saved->filter(function($item) {
                return !$item->last_reviewed_at || 
                       $item->last_reviewed_at->lte(now()->subDays(3));
            })->count(),
            'total_reviews' => $saved->sum('review_count'),
            'recent_saved' => $saved->where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * Get exam progress summary.
     */
    private function getExamProgressSummary($userId)
    {
        $attempts = UserExamAttempt::with('exam')
            ->where('user_id', $userId)
            ->get();

        $completed = $attempts->where('status', 'completed');

        return [
            'total_attempts' => $attempts->count(),
            'completed_exams' => $completed->count(),
            'in_progress' => $attempts->where('status', 'in_progress')->count(),
            'average_score' => $completed->count() > 0 
                ? round($completed->avg('percentage'), 2) 
                : 0,
            'highest_score' => $completed->count() > 0 
                ? $completed->max('percentage') 
                : 0,
            'total_time_spent' => $completed->sum('time_spent'), // minutes
        ];
    }

    /**
     * Get recent learning activities.
     */
    private function getRecentActivities($userId)
    {
        $activities = [];

        // Recent topic progress
        $recentTopics = UserTopicProgress::with('topic')
            ->where('user_id', $userId)
            ->whereNotNull('last_studied_at')
            ->orderBy('last_studied_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentTopics as $progress) {
            $activities[] = [
                'type' => 'topic_study',
                'title' => $progress->topic->name,
                'timestamp' => $progress->last_studied_at->toIso8601String(),
                'details' => "Studied {$progress->topic->name} - {$progress->progress_percentage}% complete",
            ];
        }

        // Recent level progress
        $recentLevels = UserLevelProgress::with('level')
            ->where('user_id', $userId)
            ->whereNotNull('last_studied_at')
            ->orderBy('last_studied_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentLevels as $progress) {
            $activities[] = [
                'type' => 'level_study',
                'title' => $progress->level->level_name,
                'timestamp' => $progress->last_studied_at->toIso8601String(),
                'details' => "Practiced {$progress->level->level_name} - {$progress->overall_progress_percentage}% complete",
            ];
        }

        // Recent exams
        $recentExams = UserExamAttempt::with('exam')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentExams as $attempt) {
            $activities[] = [
                'type' => 'exam_completed',
                'title' => $attempt->exam->title,
                'timestamp' => $attempt->completed_at->toIso8601String(),
                'details' => "Completed {$attempt->exam->title} - Score: {$attempt->percentage}%",
            ];
        }

        // Sort all activities by timestamp
        usort($activities, function($a, $b) {
            return strcmp($b['timestamp'], $a['timestamp']);
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Get detailed learning statistics.
     */
    public function statistics()
    {
        $user = Auth::user();

        $stats = [
            'overview' => [
                'topics_started' => UserTopicProgress::where('user_id', $user->id)->count(),
                'levels_started' => UserLevelProgress::where('user_id', $user->id)->count(),
                'words_saved' => SavedVocabulary::where('user_id', $user->id)->count(),
                'exams_taken' => UserExamAttempt::where('user_id', $user->id)->where('status', 'completed')->count(),
                'current_streak' => UserStreak::where('user_id', $user->id)->value('streak_count') ?? 0,
            ],
            'by_mastery_level' => [
                'topics' => [
                    'mastered' => UserTopicProgress::where('user_id', $user->id)->where('mastery_level', 'mastered')->count(),
                    'advanced' => UserTopicProgress::where('user_id', $user->id)->where('mastery_level', 'advanced')->count(),
                    'intermediate' => UserTopicProgress::where('user_id', $user->id)->where('mastery_level', 'intermediate')->count(),
                    'beginner' => UserTopicProgress::where('user_id', $user->id)->where('mastery_level', 'beginner')->count(),
                ],
                'levels' => [
                    'mastered' => UserLevelProgress::where('user_id', $user->id)->where('mastery_level', 'mastered')->count(),
                    'advanced' => UserLevelProgress::where('user_id', $user->id)->where('mastery_level', 'advanced')->count(),
                    'intermediate' => UserLevelProgress::where('user_id', $user->id)->where('mastery_level', 'intermediate')->count(),
                    'beginner' => UserLevelProgress::where('user_id', $user->id)->where('mastery_level', 'beginner')->count(),
                ],
            ],
            'time_spent' => [
                'total_exam_time' => UserExamAttempt::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->sum('time_spent'),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
