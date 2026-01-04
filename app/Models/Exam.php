<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'title',
        'time',
        'level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parts()
    {
        return $this->hasMany(ExamPart::class);
    }

    public function attempts()
    {
        return $this->hasMany(UserExamAttempt::class);
    }
}
