<?php

namespace App\Http\Controllers;

use App\Support\AdhdAssessment;
use Illuminate\Support\Facades\App;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function show(): View
    {
        $locale = $this->setLocale(request());

        return view('assessment', [
            'sections' => AdhdAssessment::sections(),
            'locale' => $locale,
            'isRtl' => $locale === 'fa',
            'locales' => $this->locales(),
            'ui' => __('assessment.ui'),
        ]);
    }

    public function score(Request $request): JsonResponse
    {
        $this->setLocale($request);

        $validated = $request->validate([
            'answers' => ['required', 'array'],
        ]);

        return response()->json(AdhdAssessment::score($validated['answers']));
    }

    private function setLocale(Request $request): string
    {
        $locale = $request->query('lang', $request->session()->get('locale', config('app.locale')));
        $locale = in_array($locale, AdhdAssessment::SUPPORTED_LOCALES, true) ? $locale : 'en';

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        return $locale;
    }

    private function locales(): array
    {
        return [
            'en' => 'English',
            'nl' => 'Nederlands',
            'fr' => 'Français',
            'fa' => 'فارسی',
        ];
    }
}
