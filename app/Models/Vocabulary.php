<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vocabulary extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'topic_id',
        'word',
        'phonetic',
        'pinyin',
        'simplified',
        'traditional',
        'part_of_speech',
        'meaning',
        'meaning_vi',
        'meaning_zh',
        'example_sentence',
        'example_translation',
        'example_highlight',
        'definition',
        'radical_info',
        'stroke_count',
        'tone_pattern',
        'related_words',
        'similar_chars',
        'pronunciation_audio',
        'image_url',
        'level',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'related_words' => 'array',
        'similar_chars' => 'array',
        'stroke_count' => 'integer',
    ];

    /**
     * Get the topic that owns the vocabulary.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get all translations for this vocabulary.
     */
    public function translations()
    {
        return $this->hasMany(VocabularyTranslation::class);
    }

    /**
     * Get translation for a specific language.
     */
    public function getTranslation($languageCode)
    {
        return $this->translations()->where('language_code', $languageCode)->first();
    }

    /**
     * Scope a query to filter by topic.
     */
    public function scopeByTopic($query, $topicId)
    {
        return $query->where('topic_id', $topicId);
    }

    /**
     * Scope a query to filter by level.
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope a query to search by word.
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('word', 'like', "%{$searchTerm}%")
              ->orWhere('simplified', 'like', "%{$searchTerm}%")
              ->orWhere('pinyin', 'like', "%{$searchTerm}%")
              ->orWhere('meaning', 'like', "%{$searchTerm}%")
              ->orWhere('meaning_vi', 'like', "%{$searchTerm}%");
        });
    }
}
