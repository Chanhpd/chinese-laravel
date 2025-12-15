<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Vocabulary;
use App\Models\User;
use App\Models\UserTopicProgress;
use App\Models\SavedVocabulary;
use App\Models\AdminLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get admin dashboard statistics.
     */
    public function index(Request $request)
    {
        // If request expects JSON (API call), return JSON response
        if ($request->expectsJson()) {
            // User statistics
            $userStats = [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'blocked_users' => User::where('status', 'blocked')->count(),
                'new_users_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
                'users_by_role' => [
                    'user' => User::where('role', 'user')->count(),
                    'admin' => User::where('role', 'admin')->count(),
                    'super_admin' => User::where('role', 'super_admin')->count(),
                ],
            ];

            // Content statistics
            $contentStats = [
                'total_topics' => Topic::count(),
                'total_vocabularies' => Vocabulary::count(),
                'vocabularies_by_level' => Vocabulary::select('level', DB::raw('count(*) as count'))
                    ->groupBy('level')
                    ->orderBy('level')
                    ->get()
                    ->pluck('count', 'level'),
                'topics_by_level' => Topic::select('level', DB::raw('count(*) as count'))
                    ->groupBy('level')
                    ->orderBy('level')
                    ->get()
                    ->pluck('count', 'level'),
            ];

            // Learning statistics
            $learningStats = [
                'total_progress_records' => UserTopicProgress::count(),
                'total_saved_vocabularies' => SavedVocabulary::count(),
                'average_words_per_user' => UserTopicProgress::avg('completed_words'),
                'mastery_distribution' => [
                    'beginner' => UserTopicProgress::where('mastery_level', 'beginner')->count(),
                    'intermediate' => UserTopicProgress::where('mastery_level', 'intermediate')->count(),
                    'advanced' => UserTopicProgress::where('mastery_level', 'advanced')->count(),
                    'mastered' => UserTopicProgress::where('mastery_level', 'mastered')->count(),
                ],
            ];

            // Recent activity
            $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();
            $recentAdminLogs = AdminLog::with('admin')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Active users (users who studied recently)
            $activeLearnersToday = UserTopicProgress::whereDate('last_studied_at', today())
                ->distinct('user_id')
                ->count('user_id');

            $activeLearnersWeek = UserTopicProgress::where('last_studied_at', '>=', now()->startOfWeek())
                ->distinct('user_id')
                ->count('user_id');

            return response()->json([
                'success' => true,
                'data' => [
                    'users' => $userStats,
                    'content' => $contentStats,
                    'learning' => $learningStats,
                    'activity' => [
                        'active_learners_today' => $activeLearnersToday,
                        'active_learners_week' => $activeLearnersWeek,
                        'recent_users' => $recentUsers->map(function($user) {
                            return [
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'role' => $user->role,
                                'created_at' => $user->created_at,
                            ];
                        }),
                        'recent_admin_logs' => $recentAdminLogs->map(function($log) {
                            return [
                                'id' => $log->id,
                                'admin' => $log->admin->name,
                                'action' => $log->action,
                                'description' => $log->description,
                                'created_at' => $log->created_at,
                            ];
                        }),
                    ],
                ],
            ]);
        }

        // For web requests, return view with data
        $totalTopics = Topic::count();
        $activeTopics = Topic::count(); // Adjust if you have active/inactive status
        $totalVocabularies = Vocabulary::count();
        
        // Vocabularies by level
        $vocabByLevel = Vocabulary::select('level', DB::raw('count(*) as count'))
            ->groupBy('level')
            ->orderBy('level')
            ->get();
        
        // Top topics by vocabulary count
        $topTopics = Topic::withCount('vocabularies')
            ->orderBy('vocabularies_count', 'desc')
            ->limit(10)
            ->get();
        
        // Recent vocabularies
        $recentVocabularies = Vocabulary::with('topic')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalTopics',
            'activeTopics',
            'totalVocabularies',
            'vocabByLevel',
            'topTopics',
            'recentVocabularies'
        ));
    }

    /**
     * Get admin logs with filters.
     */
    public function logs(Request $request)
    {
        $query = AdminLog::with('admin');

        // Filter by admin
        if ($request->has('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        // Filter by action
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Get user growth statistics.
     */
    public function userGrowth(Request $request)
    {
        $days = $request->get('days', 30);

        $growth = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subDays($days))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $growth,
        ]);
    }

    /**
     * Get learning activity statistics.
     */
    public function learningActivity(Request $request)
    {
        $days = $request->get('days', 30);

        $activity = UserTopicProgress::select(
            DB::raw('DATE(last_studied_at) as date'),
            DB::raw('COUNT(DISTINCT user_id) as active_users'),
            DB::raw('SUM(completed_words) as words_learned')
        )
        ->where('last_studied_at', '>=', now()->subDays($days))
        ->whereNotNull('last_studied_at')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $activity,
        ]);
    }

    /**
     * Get top learners.
     */
    public function topLearners(Request $request)
    {
        $limit = $request->get('limit', 10);

        $topLearners = User::withCount([
            'topicProgress as total_words_learned' => function($query) {
                $query->select(DB::raw('SUM(completed_words)'));
            }
        ])
        ->orderBy('total_words_learned', 'desc')
        ->limit($limit)
        ->get();

        return response()->json([
            'success' => true,
            'data' => $topLearners->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'total_words_learned' => $user->total_words_learned ?? 0,
                ];
            }),
        ]);
    }
}
