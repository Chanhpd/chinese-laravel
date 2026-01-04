<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLevelProgress extends Model
{
    use HasFactory;

    protected $table = 'user_level_progress';

    protected $fillable = [
        'user_id',
        'level_id',
        'completed_words',
        'total_words',
        'completed_radicals',
        'total_radicals',
        'mastery_level',
        'last_studied_at',
    ];

    protected $casts = [
        'last_studied_at' => 'datetime',
    ];

    /**
     * Get the user that owns the progress.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the level that this progress belongs to.
     */
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    /**
     * Calculate and get progress percentage for words.
     */
    public function getWordProgressPercentageAttribute()
    {
        if ($this->total_words == 0) {
            return 0;
        }
        return round(($this->completed_words / $this->total_words) * 100, 2);
    }

    /**
     * Calculate and get progress percentage for radicals.
     */
    public function getRadicalProgressPercentageAttribute()
    {
        if ($this->total_radicals == 0) {
            return 0;
        }
        return round(($this->completed_radicals / $this->total_radicals) * 100, 2);
    }

    /**
     * Calculate overall progress percentage.
     */
    public function getOverallProgressPercentageAttribute()
    {
        $totalItems = $this->total_words + $this->total_radicals;
        if ($totalItems == 0) {
            return 0;
        }
        $completedItems = $this->completed_words + $this->completed_radicals;
        return round(($completedItems / $totalItems) * 100, 2);
    }

    /**
     * Update mastery level based on overall progress percentage.
     */
    public function updateMasteryLevel()
    {
        $percentage = $this->overall_progress_percentage;
        
        if ($percentage >= 90) {
            $this->mastery_level = 'mastered';
        } elseif ($percentage >= 70) {
            $this->mastery_level = 'advanced';
        } elseif ($percentage >= 40) {
            $this->mastery_level = 'intermediate';
        } else {
            $this->mastery_level = 'beginner';
        }
        
        $this->save();
    }

    /**
     * Mark a word as completed and update progress.
     */
    public function markWordCompleted()
    {
        if ($this->completed_words < $this->total_words) {
            $this->increment('completed_words');
        }
        $this->last_studied_at = now();
        $this->save();
        $this->updateMasteryLevel();
    }

    /**
     * Mark a radical as completed and update progress.
     */
    public function markRadicalCompleted()
    {
        if ($this->completed_radicals < $this->total_radicals) {
            $this->increment('completed_radicals');
        }
        $this->last_studied_at = now();
        $this->save();
        $this->updateMasteryLevel();
    }

    /**
     * Initialize or update total counts based on level data.
     */
    public function syncTotals()
    {
        $this->total_words = $this->level->words()->count();
        $this->total_radicals = $this->level->radicals()->count();
        $this->save();
    }

    /**
     * Scope to get progress for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get progress by mastery level.
     */
    public function scopeByMasteryLevel($query, $level)
    {
        return $query->where('mastery_level', $level);
    }

    /**
     * Scope to get recently studied levels.
     */
    public function scopeRecentlyStudied($query, $days = 7)
    {
        return $query->where('last_studied_at', '>=', now()->subDays($days));
    }

    /**
     * Format progress data for API response.
     */
    public function toApiFormat()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'level_id' => $this->level_id,
            'level' => $this->level ? [
                'id' => $this->level->id,
                'test_type' => $this->level->test_type,
                'level_number' => $this->level->level_number,
                'level_name' => $this->level->level_name,
            ] : null,
            'words' => [
                'completed' => $this->completed_words,
                'total' => $this->total_words,
                'percentage' => $this->word_progress_percentage,
            ],
            'radicals' => [
                'completed' => $this->completed_radicals,
                'total' => $this->total_radicals,
                'percentage' => $this->radical_progress_percentage,
            ],
            'overall_percentage' => $this->overall_progress_percentage,
            'mastery_level' => $this->mastery_level,
            'last_studied_at' => $this->last_studied_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
