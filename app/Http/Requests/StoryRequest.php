<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $storyId = $this->route('id');
        
        return [
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:stories,slug,' . $storyId,
            ],
            'title_english' => 'required|string|max:255',
            'title_chinese' => 'nullable|string|max:255',
            'audio_url' => 'nullable|string',
            'image_url' => 'nullable|string',
            'tags' => 'nullable|string|max:500',
            'hsk_level' => 'nullable|string|max:10|in:HSK1,HSK2,HSK3,HSK4,HSK5,HSK6',
            'story_url' => 'nullable|string',
            'chinese_text' => 'nullable|string',
            'pinyin_text' => 'nullable|string',
            'content_html' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title_english.required' => 'English title is required',
            'slug.unique' => 'This slug is already in use',
            'hsk_level.in' => 'HSK level must be one of: HSK1, HSK2, HSK3, HSK4, HSK5, HSK6',
        ];
    }
}
