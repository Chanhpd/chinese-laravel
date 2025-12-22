<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    use HasFactory;

    protected $table = 'word';

    public $timestamps = false;

    protected $fillable = [
        'word',
        'pinyin',
        'meaning_vi',
        'meaning_en',
        'meaning_ru',
        'meaning_th',
        'meaning_ms',
        'meaning_ko',
        'meaning_ja',
        'meaning_id',
        'level_id',
    ];

    /**
     * Get the level that owns the word.
     */
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    /**
     * Format word data to match JSON structure (hsk_1.json format)
     */
    public function toJsonFormat()
    {
        return [
            'w' => $this->word,
            'p' => $this->pinyin,
            'm' => $this->meaning_vi,
            'm_en' => $this->meaning_en,
            'm_ru' => $this->meaning_ru,
            'm_th' => $this->meaning_th,
            'm_ms' => $this->meaning_ms,
            'm_ko' => $this->meaning_ko,
            'm_ja' => $this->meaning_ja,
            'm_id' => $this->meaning_id,
        ];
    }
}
