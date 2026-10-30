<?php

namespace App\Http\Controllers;

use App\Models\ToolUsage;
use App\Support\AdhdAssessment;
use App\Support\BodyNeedsAiGuide;
use App\Support\CheckInAiGuide;
use App\Support\DailyPlannerAiGuide;
use App\Support\DashboardAiGuide;
use App\Support\DecisionPriorityAiGuide;
use App\Support\EmotionalResetAiGuide;
use App\Support\EnergyCrashAiGuide;
use App\Support\FocusSprintAiGuide;
use App\Support\GoalBuilderAiGuide;
use App\Support\MedicationRefillAiGuide;
use App\Support\MoneyAdminAiGuide;
use App\Support\TaskBreakdownAiGuide;
use App\Support\TimeBlockAiGuide;
use App\Support\Toolkit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
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
            'ui' => [
                ...$this->localizedArray('assessment.ui'),
                'screening' => $this->localizedArray('assessment.tools.toolkit.screening'),
            ],
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

    public function toolkit(Request $request): View
    {
        // Locale must be set before the groups are translated.
        $locale = $this->setLocale($request);

        return $this->toolView($request, 'toolkit', ['groups' => Toolkit::groups($locale)]);
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

    public function goalBuilderAi(Request $request, GoalBuilderAiGuide $guide): JsonResponse
    {
        $locale = $this->setLocale($request);

        $validated = $request->validate([
            'goal' => ['required', 'string', 'max:260'],
            'why' => ['nullable', 'string', 'max:220'],
            'blocker' => ['nullable', 'string', 'max:220'],
            'energy' => ['nullable', 'string', 'max:40'],
        ]);

        return response()->json($guide->guide([
            ...$validated,
            'locale' => $locale,
        ]));
    }

    public function dailyPlanner(Request $request): View
    {
        return $this->toolView($request, 'tools.daily-planner');
    }

    public function dailyPlannerAi(Request $request, DailyPlannerAiGuide $guide): JsonResponse
    {
        $locale = $this->setLocale($request);

        $validated = $request->validate([
            'must' => ['nullable', 'string', 'max:1000'],
            'should' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string', 'max:220'],
            'scary' => ['nullable', 'string', 'max:220'],
            'recovery' => ['nullable', 'string', 'max:220'],
        ]);

        return response()->json($guide->guide([
            ...$validated,
            'locale' => $locale,
        ]));
    }

    public function timeBlock(Request $request): View
    {
        return $this->toolView($request, 'tools.time-block');
    }

    public function timeBlockAi(Request $request, TimeBlockAiGuide $guide): JsonResponse
    {
        $locale = $this->setLocale($request);

        $validated = $request->validate([
            'start' => ['nullable', 'string', 'max:20'],
            'end' => ['nullable', 'string', 'max:20'],
            'must' => ['nullable', 'string', 'max:1200'],
            'fixed' => ['nullable', 'string', 'max:1200'],
            'buffer' => ['nullable', 'string', 'max:40'],
            'energy' => ['nullable', 'string', 'max:40'],
            'recovery' => ['nullable', 'string', 'max:220'],
        ]);

        return response()->json($guide->guide([
            ...$validated,
            'locale' => $locale,
        ]));
    }

    public function bodyNeeds(Request $request): View
    {
        return $this->toolView($request, 'tools.body-needs');
    }

    public function bodyNeedsAi(Request $request, BodyNeedsAiGuide $guide): JsonResponse
    {
        $locale = $this->setLocale($request);

        $validated = $request->validate([
            'food' => ['nullable', 'string', 'max:40'],
            'water' => ['nullable', 'string', 'max:40'],
            'meds' => ['nullable', 'string', 'max:40'],
            'sleep' => ['nullable', 'string', 'max:40'],
            'signal' => ['nullable', 'string', 'max:40'],
            'task' => ['nullable', 'string', 'max:220'],
        ]);

        return response()->json($guide->guide([
            ...$validated,
            'locale' => $locale,
        ]));
    }

    public function medicationRefill(Request $request): View
    {
        return $this->toolView($request, 'tools.medication-refill');
    }

    public function medicationRefillAi(Request $request, MedicationRefillAiGuide $guide): JsonResponse
    {
        $locale = $this->setLocale($request);

        $validated = $request->validate([
            'mode' => ['nullable', 'string', 'max:40'],
            'medicine' => ['nullable', 'string', 'max:220'],
            'supply' => ['nullable', 'string', 'max:120'],
            'blocker' => ['nullable', 'string', 'max:220'],
            'contact' => ['nullable', 'string', 'max:220'],
            'deadline' => ['nullable', 'string', 'max:120'],
        ]);

        return response()->json($guide->guide([
            ...$validated,
            'locale' => $locale,
        ]));
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

    public function energyCrashAi(Request $request, EnergyCrashAiGuide $guide): JsonResponse
    {
        $locale = $this->setLocale($request);

        $validated = $request->validate([
            'state' => ['nullable', 'string', 'max:40'],
            'must' => ['nullable', 'string', 'max:220'],
            'body' => ['nullable', 'string', 'max:40'],
            'drop' => ['nullable', 'string', 'max:220'],
            'support' => ['nullable', 'string', 'max:220'],
            'time' => ['nullable', 'string', 'max:10'],
        ]);

        return response()->json($guide->guide([
            ...$validated,
            'locale' => $locale,
        ]));
    }

    public function decisionPriority(Request $request): View
    {
        return $this->toolView($request, 'tools.decision-priority');
    }

    public function decisionPriorityAi(Request $request, DecisionPriorityAiGuide $guide): JsonResponse
    {
        $locale = $this->setLocale($request);

        $validated = $request->validate([
            'options' => ['nullable', 'string', 'max:1200'],
            'urgency' => ['nullable', 'string', 'max:40'],
            'energy' => ['nullable', 'string', 'max:40'],
            'consequence' => ['nullable', 'string', 'max:40'],
            'relief' => ['nullable', 'string', 'max:220'],
            'support' => ['nullable', 'string', 'max:220'],
        ]);

        return response()->json($guide->guide([
            ...$validated,
            'locale' => $locale,
        ]));
    }

    public function focusSprint(Request $request): View
    {
        return $this->toolView($request, 'tools.focus-sprint');
    }

    public function focusSprintAi(Request $request, FocusSprintAiGuide $guide): JsonResponse
    {
        $locale = $this->setLocale($request);

        $validated = $request->validate([
            'task' => ['nullable', 'string', 'max:220'],
            'mode' => ['nullable', 'string', 'max:40'],
            'minutes' => ['nullable', 'string', 'max:10'],
            'distraction' => ['nullable', 'string', 'max:220'],
            'support' => ['nullable', 'string', 'max:40'],
            'reward' => ['nullable', 'string', 'max:220'],
        ]);

        return response()->json($guide->guide([
            ...$validated,
            'locale' => $locale,
        ]));
    }

    public function emotionalReset(Request $request): View
    {
        return $this->toolView($request, 'tools.emotional-reset');
    }

    public function emotionalResetAi(Request $request, EmotionalResetAiGuide $guide): JsonResponse
    {
        $locale = $this->setLocale($request);

        $validated = $request->validate([
            'trigger' => ['nullable', 'string', 'max:220'],
            'intensity' => ['nullable', 'string', 'max:40'],
            'body' => ['nullable', 'string', 'max:40'],
            'story' => ['nullable', 'string', 'max:220'],
            'next' => ['nullable', 'string', 'max:220'],
            'support' => ['nullable', 'string', 'max:220'],
        ]);

        return response()->json($guide->guide([
            ...$validated,
            'locale' => $locale,
        ]));
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

    public function moneyAdminAi(Request $request, MoneyAdminAiGuide $guide): JsonResponse
    {
        $locale = $this->setLocale($request);

        $validated = $request->validate([
            'task' => ['nullable', 'string', 'max:220'],
            'category' => ['nullable', 'string', 'max:40'],
            'urgency' => ['nullable', 'string', 'max:40'],
            'blocker' => ['nullable', 'string', 'max:220'],
            'contact' => ['nullable', 'string', 'max:220'],
            'outcome' => ['nullable', 'string', 'max:220'],
        ]);

        return response()->json($guide->guide([
            ...$validated,
            'locale' => $locale,
        ]));
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

    public function taskBreakdownAi(Request $request, TaskBreakdownAiGuide $guide): JsonResponse
    {
        $locale = $this->setLocale($request);

        $validated = $request->validate([
            'dump' => ['nullable', 'string', 'max:1200'],
            'focus' => ['required', 'string', 'max:220'],
            'stuck' => ['nullable', 'string', 'max:40'],
            'done' => ['nullable', 'string', 'max:220'],
        ]);

        return response()->json($guide->guide([
            ...$validated,
            'locale' => $locale,
        ]));
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

    private function toolView(Request $request, string $view, array $extra = []): View
    {
        $locale = $this->setLocale($request);
        $tools = $this->localizedArray('assessment.tools');

        $this->recordToolUsage($request, $view, $tools, $locale);

        return view($view, [
            'locale' => $locale,
            'isRtl' => $locale === 'fa',
            'locales' => $this->locales(),
            'ui' => $this->localizedArray('assessment.ui'),
            'tools' => $tools,
            'usageStats' => $this->usageStats($request),
            ...$extra,
        ]);
    }

    private function recordToolUsage(Request $request, string $view, array $tools, string $locale): void
    {
        $user = $request->user();

        if (! $user || ! str_starts_with($view, 'tools.')) {
            return;
        }

        $key = $this->toolKeyFromView($view);

        ToolUsage::create([
            'user_id' => $user->id,
            'tool_key' => $key,
            'tool_label' => $this->toolLabel($key, $tools),
            'route_name' => $request->route()?->getName(),
            'locale' => $locale,
        ]);
    }

    private function usageStats(Request $request): ?array
    {
        $user = $request->user();

        if (! $user) {
            return null;
        }

        $query = ToolUsage::query()->where('user_id', $user->id);
        $popular = (clone $query)
            ->selectRaw('tool_key, tool_label, route_name, count(*) as visits')
            ->groupBy('tool_key', 'tool_label', 'route_name')
            ->orderByDesc('visits')
            ->orderBy('tool_label')
            ->first();

        return [
            'total' => (clone $query)->count(),
            'tool_count' => (clone $query)->distinct('tool_key')->count('tool_key'),
            'most_used' => $this->usageLink($popular),
            'recent' => (clone $query)
                ->latest()
                ->limit(5)
                ->get(['tool_key', 'tool_label', 'route_name', 'created_at'])
                ->map(fn (ToolUsage $usage): array => $this->usageLink($usage)),
        ];
    }

    /**
     * @return array{tool_key: string|null, tool_label: string, route_name: string|null, href: string|null, created_at?: mixed, visits?: mixed}|null
     */
    private function usageLink(?ToolUsage $usage): ?array
    {
        if (! $usage) {
            return null;
        }

        $routeName = is_string($usage->route_name) && $usage->route_name !== '' ? $usage->route_name : null;
        $href = $routeName && Route::has($routeName)
            ? route($routeName, ['lang' => App::currentLocale()])
            : null;

        return [
            'tool_key' => $usage->tool_key,
            'tool_label' => $usage->tool_label,
            'route_name' => $routeName,
            'href' => $href,
            'created_at' => $usage->created_at,
            'visits' => $usage->visits ?? null,
        ];
    }

    private function toolKeyFromView(string $view): string
    {
        return match (str_replace('tools.', '', $view)) {
            'support-navigator' => 'navigator',
            'daily-check-in' => 'checkin',
            'goal-builder' => 'goal',
            'daily-planner' => 'planner',
            'time-block' => 'time',
            'body-needs' => 'body',
            'medication-refill' => 'meds',
            'sleep-wind-down' => 'sleep',
            'motivation-menu' => 'motivation',
            'wins-log' => 'wins',
            'routine-builder' => 'routine',
            'weekly-reset' => 'weekly',
            'communication-repair' => 'communication',
            'energy-crash' => 'energy',
            'decision-priority' => 'decision',
            'focus-sprint' => 'focus',
            'emotional-reset' => 'emotion',
            'safety-pause' => 'safety',
            'transition-rescue' => 'transition',
            'appointment-prep' => 'appointment',
            'care-notes' => 'care',
            'support-request' => 'support',
            'work-school-support' => 'workschool',
            'job-hunt' => 'job',
            'home-reset' => 'home',
            'laundry-rescue' => 'laundry',
            'digital-clutter' => 'digital',
            'money-admin' => 'money',
            'aid-application' => 'aid',
            'lost-item' => 'lost',
            'errand-launch' => 'errand',
            'provider-shortlist' => 'providers',
            'access-plan' => 'access',
            'task-breakdown' => 'task',
            'follow-up-rescue' => 'followup',
            'symptom-tracker' => 'tracker',
            default => str_replace('-', '_', str_replace('tools.', '', $view)),
        };
    }

    private function toolLabel(string $key, array $tools): string
    {
        return $tools['dashboard']['cards'][$key]['title']
            ?? $tools[$key]['title']
            ?? str($key)->replace('_', ' ')->title()->toString();
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
