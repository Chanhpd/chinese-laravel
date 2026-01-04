<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamPart extends Model
{
    protected $fillable = [
        'exam_id',
        'name',
        'time',
        'order',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
