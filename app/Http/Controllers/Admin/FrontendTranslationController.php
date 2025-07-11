<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FrontendTranslationController extends Controller
{
    protected $frontendLangPath;

    public function __construct()
    {
        $this->frontendLangPath = base_path('lang/frontend');
    }

    public function index()
    {
        $locales = ['en', 'ro'];
        $translations = [];

        foreach ($locales as $locale) {
            $path = $this->frontendLangPath . "/{$locale}.json";
            if (File::exists($path)) {
                $translations[$locale] = json_decode(File::get($path), true);
            }
        }

        return view('admin.frontend-translations.index', compact('translations', 'locales'));
    }

    public function edit($locale)
    {
        $path = $this->frontendLangPath . "/{$locale}.json";
        $translations = [];

        if (File::exists($path)) {
            $translations = json_decode(File::get($path), true);
        }

        return view('admin.frontend-translations.edit', compact('translations', 'locale'));
    }

    public function update(Request $request, $locale)
    {
        $path = $this->frontendLangPath . "/{$locale}.json";

        if (!File::isDirectory($this->frontendLangPath)) {
            File::makeDirectory($this->frontendLangPath, 0755, true);
        }

        File::put($path, json_encode($request->translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return redirect()->route('admin.frontend-translations.edit', $locale)
            ->withSuccess(__('Frontend translations updated successfully'));
    }
} 