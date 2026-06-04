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
            'ui' => $this->localizedArray('assessment.ui'),
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

    public function dashboard(Request $request): View
    {
        return $this->toolView($request, 'dashboard');
    }

    public function resources(Request $request): View
    {
        return $this->toolView($request, 'resources');
    }

    public function goalBuilder(Request $request): View
    {
        return $this->toolView($request, 'tools.goal-builder');
    }

    public function dailyPlanner(Request $request): View
    {
        return $this->toolView($request, 'tools.daily-planner');
    }

    public function routineBuilder(Request $request): View
    {
        return $this->toolView($request, 'tools.routine-builder');
    }

    public function weeklyReset(Request $request): View
    {
        return $this->toolView($request, 'tools.weekly-reset');
    }

    public function communicationRepair(Request $request): View
    {
        return $this->toolView($request, 'tools.communication-repair');
    }

    public function energyCrash(Request $request): View
    {
        return $this->toolView($request, 'tools.energy-crash');
    }

    public function appointmentPrep(Request $request): View
    {
        return $this->toolView($request, 'tools.appointment-prep');
    }

    public function careNotes(Request $request): View
    {
        return $this->toolView($request, 'tools.care-notes');
    }

    public function supportRequest(Request $request): View
    {
        return $this->toolView($request, 'tools.support-request');
    }

    public function homeReset(Request $request): View
    {
        return $this->toolView($request, 'tools.home-reset');
    }

    public function moneyAdmin(Request $request): View
    {
        return $this->toolView($request, 'tools.money-admin');
    }

    public function providerShortlist(Request $request): View
    {
        return $this->toolView($request, 'tools.provider-shortlist');
    }

    public function accessPlan(Request $request): View
    {
        return $this->toolView($request, 'tools.access-plan');
    }

    public function taskBreakdown(Request $request): View
    {
        return $this->toolView($request, 'tools.task-breakdown');
    }

    public function reminders(Request $request): View
    {
        return $this->toolView($request, 'tools.reminders');
    }

    public function symptomTracker(Request $request): View
    {
        return $this->toolView($request, 'tools.symptom-tracker');
    }

    private function toolView(Request $request, string $view): View
    {
        $locale = $this->setLocale($request);

        return view($view, [
            'locale' => $locale,
            'isRtl' => $locale === 'fa',
            'locales' => $this->locales(),
            'ui' => $this->localizedArray('assessment.ui'),
            'tools' => $this->localizedArray('assessment.tools'),
        ]);
    }

    private function localizedArray(string $key): array
    {
        $fallback = trans($key, [], 'en');
        $current = trans($key);

        if (! is_array($fallback)) {
            return [];
        }

        if (! is_array($current)) {
            return $fallback;
        }

        return array_replace_recursive($fallback, $current);
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
