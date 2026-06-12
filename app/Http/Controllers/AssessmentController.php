<?php

namespace App\Http\Controllers;

use App\Support\AdhdAssessment;
use App\Support\CheckInAiGuide;
use App\Support\DashboardAiGuide;
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

    public function dashboardAi(Request $request, DashboardAiGuide $guide): JsonResponse
    {
        $locale = $this->setLocale($request);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:800'],
            'checkin' => ['nullable', 'array'],
            'checkin.feeling' => ['nullable', 'string', 'max:40'],
            'checkin.energy' => ['nullable', 'string', 'max:40'],
            'checkin.pressure' => ['nullable', 'string', 'max:40'],
            'checkin.message' => ['nullable', 'string', 'max:500'],
        ]);

        return response()->json($guide->guide([
            ...$validated,
            'locale' => $locale,
        ]));
    }

    public function resources(Request $request): View
    {
        return $this->toolView($request, 'resources');
    }

    public function supportNavigator(Request $request): View
    {
        return $this->toolView($request, 'tools.support-navigator');
    }

    public function dailyCheckIn(Request $request): View
    {
        return $this->toolView($request, 'tools.daily-check-in');
    }

    public function dailyCheckInAi(Request $request, CheckInAiGuide $guide): JsonResponse
    {
        $locale = $this->setLocale($request);

        $validated = $request->validate([
            'feeling' => ['required', 'string', 'max:40'],
            'energy' => ['required', 'string', 'max:40'],
            'pressure' => ['required', 'string', 'max:40'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        return response()->json($guide->guide([
            ...$validated,
            'locale' => $locale,
        ]));
    }

    public function goalBuilder(Request $request): View
    {
        return $this->toolView($request, 'tools.goal-builder');
    }

    public function dailyPlanner(Request $request): View
    {
        return $this->toolView($request, 'tools.daily-planner');
    }

    public function timeBlock(Request $request): View
    {
        return $this->toolView($request, 'tools.time-block');
    }

    public function bodyNeeds(Request $request): View
    {
        return $this->toolView($request, 'tools.body-needs');
    }

    public function medicationRefill(Request $request): View
    {
        return $this->toolView($request, 'tools.medication-refill');
    }

    public function foodRescue(Request $request): View
    {
        return $this->toolView($request, 'tools.food-rescue');
    }

    public function sleepWindDown(Request $request): View
    {
        return $this->toolView($request, 'tools.sleep-wind-down');
    }

    public function motivationMenu(Request $request): View
    {
        return $this->toolView($request, 'tools.motivation-menu');
    }

    public function winsLog(Request $request): View
    {
        return $this->toolView($request, 'tools.wins-log');
    }

    public function accountability(Request $request): View
    {
        return $this->toolView($request, 'tools.accountability');
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

    public function decisionPriority(Request $request): View
    {
        return $this->toolView($request, 'tools.decision-priority');
    }

    public function focusSprint(Request $request): View
    {
        return $this->toolView($request, 'tools.focus-sprint');
    }

    public function emotionalReset(Request $request): View
    {
        return $this->toolView($request, 'tools.emotional-reset');
    }

    public function safetyPause(Request $request): View
    {
        return $this->toolView($request, 'tools.safety-pause');
    }

    public function transitionRescue(Request $request): View
    {
        return $this->toolView($request, 'tools.transition-rescue');
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

    public function workSchoolSupport(Request $request): View
    {
        return $this->toolView($request, 'tools.work-school-support');
    }

    public function jobHunt(Request $request): View
    {
        return $this->toolView($request, 'tools.job-hunt');
    }

    public function homeReset(Request $request): View
    {
        return $this->toolView($request, 'tools.home-reset');
    }

    public function laundryRescue(Request $request): View
    {
        return $this->toolView($request, 'tools.laundry-rescue');
    }

    public function digitalClutter(Request $request): View
    {
        return $this->toolView($request, 'tools.digital-clutter');
    }

    public function moneyAdmin(Request $request): View
    {
        return $this->toolView($request, 'tools.money-admin');
    }

    public function aidApplication(Request $request): View
    {
        return $this->toolView($request, 'tools.aid-application');
    }

    public function lostItem(Request $request): View
    {
        return $this->toolView($request, 'tools.lost-item');
    }

    public function errandLaunch(Request $request): View
    {
        return $this->toolView($request, 'tools.errand-launch');
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

    public function followUpRescue(Request $request): View
    {
        return $this->toolView($request, 'tools.follow-up-rescue');
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
