<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExamAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'exam_id',
        'started_at',
        'completed_at',
        'total_score',
        'max_score',
        'percentage',
        'status',
        'time_spent',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'percentage' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class, 'attempt_id');
    }
}
