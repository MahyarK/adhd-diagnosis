@php
    $toolGroups = [
        [
            'label' => __('assessment.ui.tools_group_start'),
            'links' => [
                ['route' => 'tools.goal', 'label' => __('assessment.ui.goal_builder')],
                ['route' => 'tools.planner', 'label' => __('assessment.ui.daily_planner')],
                ['route' => 'tools.time', 'label' => __('assessment.ui.time_block')],
                ['route' => 'tools.body', 'label' => __('assessment.ui.body_needs')],
                ['route' => 'tools.food', 'label' => __('assessment.ui.food_rescue')],
                ['route' => 'tools.sleep', 'label' => __('assessment.ui.sleep_wind_down')],
                ['route' => 'tools.motivation', 'label' => __('assessment.ui.motivation_menu')],
                ['route' => 'tools.accountability', 'label' => __('assessment.ui.accountability')],
                ['route' => 'tools.routine', 'label' => __('assessment.ui.routine_builder')],
                ['route' => 'tools.focus', 'label' => __('assessment.ui.focus_sprint')],
            ],
        ],
        [
            'label' => __('assessment.ui.tools_group_rescue'),
            'links' => [
                ['route' => 'tools.energy', 'label' => __('assessment.ui.energy_crash')],
                ['route' => 'tools.decision', 'label' => __('assessment.ui.decision_priority')],
                ['route' => 'tools.emotion', 'label' => __('assessment.ui.emotional_reset')],
                ['route' => 'tools.transition', 'label' => __('assessment.ui.transition_rescue')],
                ['route' => 'tools.communication', 'label' => __('assessment.ui.communication_repair')],
                ['route' => 'tools.home', 'label' => __('assessment.ui.home_reset')],
                ['route' => 'tools.money', 'label' => __('assessment.ui.money_admin')],
                ['route' => 'tools.lost', 'label' => __('assessment.ui.lost_item')],
            ],
        ],
        [
            'label' => __('assessment.ui.tools_group_care'),
            'links' => [
                ['route' => 'tools.appointment', 'label' => __('assessment.ui.appointment_prep')],
                ['route' => 'tools.care', 'label' => __('assessment.ui.care_notes')],
                ['route' => 'tools.support', 'label' => __('assessment.ui.support_request')],
                ['route' => 'tools.providers', 'label' => __('assessment.ui.provider_shortlist')],
                ['route' => 'tools.access', 'label' => __('assessment.ui.access_plan')],
                ['route' => 'tools.weekly', 'label' => __('assessment.ui.weekly_reset')],
                ['route' => 'tools.task', 'label' => __('assessment.ui.task_breakdown')],
                ['route' => 'tools.reminders', 'label' => __('assessment.ui.reminders')],
                ['route' => 'tools.tracker', 'label' => __('assessment.ui.symptom_tracker')],
            ],
        ],
    ];
@endphp

<header class="navbar" role="banner">
    <a class="navbar-brand" href="{{ route('assessment.show') }}">
        <span class="navbar-eyebrow">{{ __('assessment.meta.eyebrow') }}</span>
        <span class="navbar-title">{{ __('assessment.meta.heading') }}</span>
    </a>
    <nav class="primary-nav" aria-label="{{ __('assessment.ui.tools') }}">
        <a href="{{ route('dashboard', ['lang' => $locale]) }}" @class(['active' => request()->routeIs('dashboard')])>{{ __('assessment.ui.dashboard') }}</a>
        <a href="{{ route('resources', ['lang' => $locale]) }}" @class(['active' => request()->routeIs('resources')])>{{ __('assessment.ui.resource_library') }}</a>
    </nav>
    <nav class="tool-menu" aria-label="{{ __('assessment.ui.tools') }}">
        <details>
            <summary @class(['active' => request()->routeIs('tools.*')])>
                <span>{{ __('assessment.ui.tools') }}</span>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                    <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </summary>
            <div class="tool-menu-panel">
                @foreach ($toolGroups as $group)
                    <section class="tool-menu-group">
                        <p>{{ $group['label'] }}</p>
                        @foreach ($group['links'] as $link)
                            <a href="{{ route($link['route'], ['lang' => $locale]) }}" @class(['active' => request()->routeIs($link['route'])])>{{ $link['label'] }}</a>
                        @endforeach
                    </section>
                @endforeach
            </div>
        </details>
    </nav>
    <nav class="lang-dropdown" aria-label="{{ __('assessment.ui.language') }}">
        <details>
            <summary>
                <span>{{ $locales[$locale] }}</span>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                    <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </summary>
            <div class="lang-menu">
                @foreach ($locales as $code => $label)
                    <a href="{{ request()->url() }}?lang={{ $code }}" @class(['active' => $locale === $code])>{{ $label }}</a>
                @endforeach
            </div>
        </details>
    </nav>
</header>
