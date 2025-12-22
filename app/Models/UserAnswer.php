<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_content_id',
        'user_answer',
        'is_correct',
        'score_earned',
        'answered_at',
    ];

    protected $casts = [
        'user_answer' => 'array',
        'is_correct' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function attempt()
    {
        return $this->belongsTo(UserExamAttempt::class, 'attempt_id');
    }

    public function questionContent()
    {
        return $this->belongsTo(QuestionContent::class);
    }
}
