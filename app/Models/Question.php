<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'exam_part_id',
        'question_type_id',
        'order',
        'g_text',
        'g_text_translate',
        'g_text_audio',
        'g_text_audio_translate',
        'g_audio',
        'g_image',
        'total_score',
    ];

    protected $casts = [
        'g_text' => 'array',
        'g_text_translate' => 'array',
        'g_text_audio_translate' => 'array',
        'g_audio' => 'array',
        'g_image' => 'array',
    ];

    public function examPart()
    {
        return $this->belongsTo(ExamPart::class);
    }

    public function questionType()
    {
        return $this->belongsTo(QuestionType::class);
    }

    public function contents()
    {
        return $this->hasMany(QuestionContent::class);
    }
}
