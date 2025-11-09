<?php

namespace App\Helpers;

class LanguageHelper
{
    /**
     * Supported languages
     * Matches the Language enum from Flutter app
     */
    const SUPPORTED_LANGUAGES = [
        'de' => 'German',
        'en' => 'English',
        'es' => 'Spanish',
        'fr' => 'French',
        'it' => 'Italian',
        'ja' => 'Japanese',
        'ko' => 'Korean',
        'ru' => 'Russian',
        'vi' => 'Vietnamese',
        'zh' => 'Chinese',
    ];

    const DEFAULT_LANGUAGE = 'en';

    /**
     * Validate if language code is supported
     */
    public static function isSupported(string $languageCode): bool
    {
        return array_key_exists($languageCode, self::SUPPORTED_LANGUAGES);
    }

    /**
     * Get language code from request or return default
     */
    public static function getLanguageCode($request): string
    {
        $lang = $request->input('lang') ?? 
                $request->header('Accept-Language') ?? 
                $request->header('X-Language') ?? 
                self::DEFAULT_LANGUAGE;

        // Extract just the language code (e.g., 'en' from 'en-US')
        $lang = strtolower(substr($lang, 0, 2));

        return self::isSupported($lang) ? $lang : self::DEFAULT_LANGUAGE;
    }

    /**
     * Get language name by code
     */
    public static function getLanguageName(string $languageCode): string
    {
        return self::SUPPORTED_LANGUAGES[$languageCode] ?? self::SUPPORTED_LANGUAGES[self::DEFAULT_LANGUAGE];
    }

    /**
     * Get all supported languages
     */
    public static function getAllLanguages(): array
    {
        return self::SUPPORTED_LANGUAGES;
    }
}
