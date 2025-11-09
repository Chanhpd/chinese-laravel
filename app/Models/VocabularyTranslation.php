<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VocabularyTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vocabulary_id',
        'language_code',
        'meaning',
        'example_translation',
    ];

    /**
     * Get the vocabulary that owns the translation.
     */
    public function vocabulary()
    {
        return $this->belongsTo(Vocabulary::class);
    }

    /**
     * Scope a query to filter by language.
     */
    public function scopeByLanguage($query, $languageCode)
    {
        return $query->where('language_code', $languageCode);
    }
}
