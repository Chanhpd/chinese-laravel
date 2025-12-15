<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title_english' => $this->title_english,
            'title_chinese' => $this->title_chinese,
            'audio_url' => $this->audio_url,
            'image_url' => $this->image_url,
            'tags' => $this->tags,
            'hsk_level' => $this->hsk_level,
            'story_url' => $this->story_url,
            'chinese_text' => $this->chinese_text,
            'pinyin_text' => $this->pinyin_text,
            'content_html' => $this->content_html,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
