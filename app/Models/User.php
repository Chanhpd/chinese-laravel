<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'blocked_at',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'blocked_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the user's topic progress records.
     */
    public function topicProgress()
    {
        return $this->hasMany(UserTopicProgress::class);
    }

    /**
     * Get the user's saved vocabularies.
     */
    public function savedVocabularies()
    {
        return $this->hasMany(SavedVocabulary::class);
    }

    /**
     * Get progress for a specific topic.
     */
    public function getProgressForTopic($topicId)
    {
        return $this->topicProgress()->where('topic_id', $topicId)->first();
    }

    /**
     * Check if user has saved a vocabulary.
     */
    public function hasSavedVocabulary($vocabularyId)
    {
        return $this->savedVocabularies()->where('vocabulary_id', $vocabularyId)->exists();
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is blocked.
     */
    public function isBlocked()
    {
        return $this->status === 'blocked';
    }

    /**
     * Block the user.
     */
    public function block()
    {
        $this->update([
            'status' => 'blocked',
            'blocked_at' => now(),
        ]);
    }

    /**
     * Unblock the user.
     */
    public function unblock()
    {
        $this->update([
            'status' => 'active',
            'blocked_at' => null,
        ]);
    }

    /**
     * Update last login time.
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}
