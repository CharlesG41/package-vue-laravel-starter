<?php

namespace Charlesg\App\Http\Controllers;

use Illuminate\Http\Request;
use Charlesg\App\Services\TranslationService;
use Illuminate\Routing\Controller;

class LocaleController extends Controller
{
    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    public function changeLanguage(Request $request)
    {
        $locale = $request->input('locale');
        $translations = $this->translationService->setLocale($locale);
        
        // Set a cookie with the user's language preference
        cookie()->forever('user_language', $locale);
        
        return response()->json([
            'locale' => $locale,
            'translations' => $translations
        ])->withCookie(cookie()->forever('user_language', $locale));
    }
}