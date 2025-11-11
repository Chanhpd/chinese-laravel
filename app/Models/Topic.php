<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'name_zh',
        'description',
        'image_url',
        'is_active',
        'sort_order',
        'level',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all vocabularies for this topic.
     */
    public function vocabularies()
    {
        return $this->hasMany(Vocabulary::class);
    }

    /**
     * Get all translations for this topic.
     */
    public function translations()
    {
        return $this->hasMany(TopicTranslation::class);
    }

    /**
     * Get translation for a specific language.
     */
    public function getTranslation($languageCode)
    {
        return $this->translations()->where('language_code', $languageCode)->first();
    }

    /**
     * Get localized name based on language code.
     * Falls back to English if translation not found.
     */
    public function getLocalizedName($languageCode = 'en')
    {
        if ($languageCode === 'en') {
            return $this->name;
        }

        if ($languageCode === 'zh') {
            return $this->name_zh ?? $this->name;
        }

        $translation = $this->getTranslation($languageCode);
        return $translation ? $translation->name : $this->name;
    }

    /**
     * Get localized description based on language code.
     * Falls back to English if translation not found.
     */
    public function getLocalizedDescription($languageCode = 'en')
    {
        if ($languageCode === 'en') {
            return $this->description;
        }

        $translation = $this->getTranslation($languageCode);
        return $translation ? $translation->description : $this->description;
    }

    /**
     * Scope a query to only include active topics.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
