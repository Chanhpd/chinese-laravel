<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'title_english',
        'title_chinese',
        'audio_url',
        'image_url',
        'tags',
        'hsk_level',
        'story_url',
        'chinese_text',
        'pinyin_text',
        'content_html',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Scope a query to filter by HSK level.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $level
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByHskLevel($query, $level)
    {
        return $query->where('hsk_level', $level);
    }

    /**
     * Scope a query to search stories.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title_english', 'like', "%{$search}%")
              ->orWhere('title_chinese', 'like', "%{$search}%")
              ->orWhere('chinese_text', 'like', "%{$search}%");
        });
    }
}
