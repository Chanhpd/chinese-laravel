<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserStreak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StreakController extends Controller
{
    /**
     * Get current user's streak data
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     * GET /api/user/streak
     */
    public function getStreakData()
    {
        try {
            $user = Auth::user();
            $streak = UserStreak::getOrCreateForUser($user->id);

            return response()->json([
                'success' => true,
                'data' => $streak->toApiFormat(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get streak data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Perform daily check-in
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * POST /api/user/streak/check-in
     * Body: {
     *   "checkInDate": "2024-12-17",  // Optional
     *   "timestamp": "2024-12-17T14:30:00Z"  // Optional
     * }
     */
    public function checkIn(Request $request)
    {
        try {
            $user = Auth::user();
            $streak = UserStreak::getOrCreateForUser($user->id);

            // Validate request (optional fields for client tracking)
            $validator = Validator::make($request->all(), [
                'checkInDate' => 'nullable|date|date_format:Y-m-d',
                'timestamp' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request data',
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Perform check-in
            $result = $streak->performCheckIn();

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data'],
                'already_checked_in' => $result['already_checked_in'],
            ], $result['already_checked_in'] ? 200 : 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform check-in',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync streak data from client (for offline support)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * PUT /api/user/streak/sync
     * Body: {
     *   "streakData": {
     *     "streakCount": 15,
     *     "lastCheckInDate": "2024-12-17",
     *     "weeklyCheckIns": ["2024-12-11", "2024-12-12", ...],
     *     "totalCheckInDays": 120
     *   },
     *   "localTimestamp": "2024-12-17T14:30:00Z"
     * }
     */
    public function syncData(Request $request)
    {
        try {
            $user = Auth::user();
            $streak = UserStreak::getOrCreateForUser($user->id);

            // Validate request
            $validator = Validator::make($request->all(), [
                'streakData' => 'required|array',
                'streakData.weeklyCheckIns' => 'nullable|array',
                'localTimestamp' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid sync data',
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Sync data (server is source of truth)
            $result = $streak->syncFromClient($request->streakData);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get streak statistics (optional - for analytics)
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     * GET /api/user/streak/statistics
     */
    public function getStatistics()
    {
        try {
            $user = Auth::user();
            $streak = UserStreak::getOrCreateForUser($user->id);

            $data = $streak->toApiFormat();

            // Add additional statistics
            $statistics = [
                'current_streak' => $data['streak_count'],
                'longest_streak' => $data['longest_streak'],
                'total_check_in_days' => $data['total_check_in_days'],
                'weekly_check_ins_count' => count($data['weekly_check_ins']),
                'can_check_in_today' => $data['can_check_in_today'],
                'last_check_in_date' => $data['last_check_in_date'],
                
                // Calculate streaks
                'streaks' => [
                    'current' => $data['streak_count'],
                    'longest' => $data['longest_streak'],
                    'this_week' => count($data['weekly_check_ins']),
                ],

                // Achievements (example)
                'achievements' => $this->calculateAchievements($streak),
            ];

            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset streak (for testing or user request)
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     * DELETE /api/user/streak/reset
     */
    public function resetStreak()
    {
        try {
            $user = Auth::user();
            $streak = UserStreak::where('user_id', $user->id)->first();

            if ($streak) {
                // Keep total_check_in_days and longest_streak for history
                $streak->update([
                    'streak_count' => 0,
                    'last_check_in_date' => null,
                    'weekly_check_ins' => [],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Streak reset successfully',
                    'data' => $streak->toApiFormat(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'No streak data to reset',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset streak',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate achievements based on streak data
     */
    private function calculateAchievements($streak)
    {
        $achievements = [];

        // Streak milestones
        $streakMilestones = [
            ['days' => 7, 'name' => 'Week Warrior', 'icon' => '🔥'],
            ['days' => 30, 'name' => 'Month Master', 'icon' => '⭐'],
            ['days' => 100, 'name' => 'Century Champion', 'icon' => '🏆'],
            ['days' => 365, 'name' => 'Year Legend', 'icon' => '👑'],
        ];

        foreach ($streakMilestones as $milestone) {
            if ($streak->longest_streak >= $milestone['days']) {
                $achievements[] = [
                    'name' => $milestone['name'],
                    'icon' => $milestone['icon'],
                    'description' => "{$milestone['days']} days streak achieved",
                    'unlocked' => true,
                ];
            }
        }

        // Total check-in milestones
        $totalMilestones = [
            ['days' => 50, 'name' => 'Beginner', 'icon' => '🌱'],
            ['days' => 100, 'name' => 'Intermediate', 'icon' => '🌿'],
            ['days' => 500, 'name' => 'Advanced', 'icon' => '🌳'],
            ['days' => 1000, 'name' => 'Expert', 'icon' => '🌲'],
        ];

        foreach ($totalMilestones as $milestone) {
            if ($streak->total_check_in_days >= $milestone['days']) {
                $achievements[] = [
                    'name' => $milestone['name'],
                    'icon' => $milestone['icon'],
                    'description' => "{$milestone['days']} total check-ins",
                    'unlocked' => true,
                ];
            }
        }

        return $achievements;
    }
}
