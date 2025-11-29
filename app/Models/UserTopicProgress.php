<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTopicProgress extends Model
{
    use HasFactory;

    protected $table = 'user_topic_progress';

    protected $fillable = [
        'user_id',
        'topic_id',
        'completed_words',
        'total_words',
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
     * Get the topic that this progress belongs to.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Calculate and update progress percentage.
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->total_words == 0) {
            return 0;
        }
        return round(($this->completed_words / $this->total_words) * 100, 2);
    }

    /**
     * Update mastery level based on progress percentage.
     */
    public function updateMasteryLevel()
    {
        $percentage = $this->progress_percentage;
        
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
        $this->increment('completed_words');
        $this->last_studied_at = now();
        $this->save();
        $this->updateMasteryLevel();
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
}
