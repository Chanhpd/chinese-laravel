<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $table = 'level';

    public $timestamps = false;

    protected $fillable = [
        'test_type',
        'level_number',
        'level_name',
    ];

    /**
     * Get the words for this level.
     */
    public function words()
    {
        return $this->hasMany(Word::class, 'level_id');
    }

    /**
     * Get the radicals for this level.
     */
    public function radicals()
    {
        return $this->hasMany(Radical::class, 'level_id');
    }

    /**
     * Get the user progress records for this level.
     */
    public function userProgress()
    {
        return $this->hasMany(UserLevelProgress::class, 'level_id');
    }
}
