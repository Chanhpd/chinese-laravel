<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radical extends Model
{
    use HasFactory;

    protected $table = 'radical';

    public $timestamps = false;

    protected $fillable = [
        'hanzi',
        'traditional',
        'pinyin',
        'radical',
        'stroke_count',
        'frequency_rank',
        'general_standard',
        'level_id',
        'meaning',
        'meaning_vi',
        'meaning_cn',
        'meaning_en',
        'meaning_jp',
        'meaning_kr',
        'meaning_th',
        'meaning_de',
        'meaning_fr',
        'meaning_es',
        'meaning_it',
        'meaning_br',
        'meaning_tr',
        'is_favorite',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'stroke_count' => 'integer',
        'frequency_rank' => 'integer',
    ];

    /**
     * Get the level that owns the radical.
     */
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    /**
     * Format radical data to JSON structure
     */
    public function toJsonFormat()
    {
        return [
            'hanzi' => $this->hanzi,
            'traditional' => $this->traditional,
            'pinyin' => $this->pinyin,
            'radical' => $this->radical,
            'stroke_count' => $this->stroke_count,
            'frequency_rank' => $this->frequency_rank,
            'general_standard' => $this->general_standard,
            'meaning' => $this->meaning,
            'meaning_vi' => $this->meaning_vi,
            'meaning_cn' => $this->meaning_cn,
            'meaning_en' => $this->meaning_en,
            'meaning_jp' => $this->meaning_jp,
            'meaning_kr' => $this->meaning_kr,
            'meaning_th' => $this->meaning_th,
            'meaning_de' => $this->meaning_de,
            'meaning_fr' => $this->meaning_fr,
            'meaning_es' => $this->meaning_es,
            'meaning_it' => $this->meaning_it,
            'meaning_br' => $this->meaning_br,
            'meaning_tr' => $this->meaning_tr,
            'is_favorite' => $this->is_favorite,
        ];
    }
}
