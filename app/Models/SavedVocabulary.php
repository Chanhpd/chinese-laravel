<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedVocabulary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vocabulary_id',
        'notes',
        'review_count',
        'last_reviewed_at',
    ];

    protected $casts = [
        'last_reviewed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the saved vocabulary.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vocabulary that was saved.
     */
    public function vocabulary()
    {
        return $this->belongsTo(Vocabulary::class);
    }

    /**
     * Increment review count and update last reviewed time.
     */
    public function markAsReviewed()
    {
        $this->increment('review_count');
        $this->last_reviewed_at = now();
        $this->save();
    }

    /**
     * Scope to get saved vocabularies for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get recently saved vocabularies.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get vocabularies that need review.
     */
    public function scopeNeedReview($query, $days = 3)
    {
        return $query->where(function($q) use ($days) {
            $q->whereNull('last_reviewed_at')
              ->orWhere('last_reviewed_at', '<=', now()->subDays($days));
        });
    }

    /**
     * Scope to filter by topic.
     */
    public function scopeByTopic($query, $topicId)
    {
        return $query->whereHas('vocabulary', function($q) use ($topicId) {
            $q->where('topic_id', $topicId);
        });
    }
}
