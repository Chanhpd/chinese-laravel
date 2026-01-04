<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserStreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'streak_count',
        'last_check_in_date',
        'weekly_check_ins',
        'total_check_in_days',
        'longest_streak',
    ];

    protected $casts = [
        'last_check_in_date' => 'date',
        'weekly_check_ins' => 'array',
    ];

    /**
     * Get the user that owns the streak.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Perform daily check-in
     */
    public function performCheckIn()
    {
        $today = Carbon::today();
        $todayStr = $today->toDateString();

        // If already checked in today, return current data
        if ($this->last_check_in_date && $this->last_check_in_date->isSameDay($today)) {
            return [
                'already_checked_in' => true,
                'message' => 'You have already checked in today',
                'data' => $this->toApiFormat(),
            ];
        }

        // Check if this continues the streak
        $isConsecutive = false;
        if ($this->last_check_in_date) {
            $daysSinceLastCheckIn = $this->last_check_in_date->diffInDays($today);
            $isConsecutive = ($daysSinceLastCheckIn === 1);
        }

        // Update streak count
        if ($isConsecutive) {
            $this->streak_count++;
        } else {
            // Reset streak if not consecutive (unless it's the first check-in)
            $this->streak_count = $this->last_check_in_date ? 1 : 1;
        }

        // Update longest streak
        if ($this->streak_count > $this->longest_streak) {
            $this->longest_streak = $this->streak_count;
        }

        // Update total check-in days
        $this->total_check_in_days++;

        // Update weekly check-ins
        $this->updateWeeklyCheckIns($todayStr);

        // Update last check-in date
        $this->last_check_in_date = $today;

        $this->save();

        return [
            'already_checked_in' => false,
            'message' => 'Check-in recorded successfully',
            'data' => $this->toApiFormat(),
        ];
    }

    /**
     * Update weekly check-ins array
     */
    private function updateWeeklyCheckIns($dateStr)
    {
        $weeklyCheckIns = $this->weekly_check_ins ?? [];
        $today = Carbon::parse($dateStr);

        // Get start of current week (Monday)
        $weekStart = $today->copy()->startOfWeek();

        // Remove dates from previous weeks
        $weeklyCheckIns = array_filter($weeklyCheckIns, function ($date) use ($weekStart) {
            $checkInDate = Carbon::parse($date);
            return $checkInDate->greaterThanOrEqualTo($weekStart);
        });

        // Add today if not already in array
        if (!in_array($dateStr, $weeklyCheckIns)) {
            $weeklyCheckIns[] = $dateStr;
        }

        // Sort dates
        sort($weeklyCheckIns);

        $this->weekly_check_ins = array_values($weeklyCheckIns);
    }

    /**
     * Format data for API response
     */
    public function toApiFormat()
    {
        return [
            'streak_count' => $this->streak_count,
            'last_check_in_date' => $this->last_check_in_date ? $this->last_check_in_date->toDateString() : null,
            'weekly_check_ins' => $this->weekly_check_ins ?? [],
            'total_check_in_days' => $this->total_check_in_days,
            'longest_streak' => $this->longest_streak,
            'can_check_in_today' => $this->canCheckInToday(),
        ];
    }

    /**
     * Check if user can check-in today
     */
    public function canCheckInToday()
    {
        if (!$this->last_check_in_date) {
            return true;
        }

        return !$this->last_check_in_date->isToday();
    }

    /**
     * Get or create streak for user
     */
    public static function getOrCreateForUser($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'streak_count' => 0,
                'total_check_in_days' => 0,
                'longest_streak' => 0,
                'weekly_check_ins' => [],
            ]
        );
    }

    /**
     * Sync data from client (for offline support)
     */
    public function syncFromClient($clientData)
    {
        // Use server data as source of truth
        // But merge weekly check-ins if needed
        
        $clientWeeklyCheckIns = $clientData['weekly_check_ins'] ?? [];
        $serverWeeklyCheckIns = $this->weekly_check_ins ?? [];

        // Merge and deduplicate
        $mergedCheckIns = array_unique(array_merge($serverWeeklyCheckIns, $clientWeeklyCheckIns));
        
        // Filter to current week only
        $today = Carbon::today();
        $weekStart = $today->copy()->startOfWeek();
        
        $mergedCheckIns = array_filter($mergedCheckIns, function ($date) use ($weekStart) {
            $checkInDate = Carbon::parse($date);
            return $checkInDate->greaterThanOrEqualTo($weekStart);
        });

        sort($mergedCheckIns);
        $this->weekly_check_ins = array_values($mergedCheckIns);

        // Use server's streak count and total days as source of truth
        // Client data is just for reference
        
        $this->save();

        return [
            'message' => 'Data synced successfully',
            'data' => $this->toApiFormat(),
        ];
    }
}
