<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class TranslationService
{
    public static function getTranslations($locale)
    {
        return Cache::remember("translations.{$locale}", 60 * 24, function () use ($locale) {
            $path = resource_path("lang/{$locale}.json");
            if (File::exists($path)) {
                return json_decode(File::get($path), true);
            }
            return [];
        });
    }

    public static function clearCache()
    {
        $locales = config('app.available_locales', ['en']);
        foreach ($locales as $locale) {
            Cache::forget("translations.{$locale}");
        }
    }
} 