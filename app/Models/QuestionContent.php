<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionContent extends Model
{
    protected $fillable = [
        'question_id',
        'sub_order',
        'q_text',
        'q_audio',
        'q_image',
        'a_text',
        'a_audio',
        'a_image',
        'a_correct',
        'a_more_correct',
        'explain',
        'advance_explain',
        'lang_explain_advance',
        'score',
    ];

    protected $casts = [
        'a_text' => 'array',
        'a_audio' => 'array',
        'a_image' => 'array',
        'a_correct' => 'array',
        'a_more_correct' => 'array',
        'explain' => 'array',
        'advance_explain' => 'array',
        'lang_explain_advance' => 'array',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
