<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    protected $frontendLangPath;

    public function __construct()
    {
        $this->frontendLangPath = base_path('lang/frontend');
    }

    public function getTranslations($locale)
    {
        $path = $this->frontendLangPath . "/{$locale}.json";
        
        // Check cache first
        $cacheKey = "frontend_translations.{$locale}";
        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        if (File::exists($path)) {
            $translations = json_decode(File::get($path), true);
            Cache::put($cacheKey, $translations, now()->addHours(24));
            return response()->json($translations);
        }

        return response()->json(['error' => 'Translation file not found'], 404);
    }

    public function updateTranslation(Request $request, $locale)
    {
        if (!in_array($locale, ['en', 'ro'])) {
            return response()->json(['error' => 'Invalid locale'], 400);
        }

        $path = $this->frontendLangPath . "/{$locale}.json";

        // Create directory if it doesn't exist
        if (!File::isDirectory($this->frontendLangPath)) {
            File::makeDirectory($this->frontendLangPath, 0755, true);
        }

        // Create or update translations
        $translations = [];
        if (File::exists($path)) {
            $translations = json_decode(File::get($path), true) ?? [];
        }

        $translations = array_merge($translations, $request->translations);
        File::put($path, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        // Clear cache
        Cache::forget("frontend_translations.{$locale}");

        return response()->json(['message' => 'Translations updated successfully']);
    }

    public function deleteTranslation(Request $request, $locale)
    {
        $path = $this->frontendLangPath . "/{$locale}.json";
        
        if (!File::exists($path)) {
            return response()->json(['error' => 'Translation file not found'], 404);
        }

        $translations = json_decode(File::get($path), true);
        $key = $request->key;

        if (isset($translations[$key])) {
            unset($translations[$key]);
            File::put($path, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            Cache::forget("frontend_translations.{$locale}");
            return response()->json(['message' => 'Translation deleted successfully']);
        }

        return response()->json(['error' => 'Translation key not found'], 404);
    }
} 