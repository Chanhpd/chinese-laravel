<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'topic_id',
        'language_code',
        'name',
        'description',
    ];

    /**
     * Get the topic that owns the translation.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Scope a query to filter by language.
     */
    public function scopeByLanguage($query, $languageCode)
    {
        return $query->where('language_code', $languageCode);
    }
}
