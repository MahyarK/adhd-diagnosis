<header class="navbar" role="banner">
    <a class="navbar-brand" href="{{ route('assessment.show') }}">
        <span class="navbar-eyebrow">{{ __('assessment.meta.eyebrow') }}</span>
        <span class="navbar-title">{{ __('assessment.meta.heading') }}</span>
    </a>
    <nav class="primary-nav" aria-label="{{ __('assessment.ui.tools') }}">
        <a href="{{ route('dashboard', ['lang' => $locale]) }}">{{ __('assessment.ui.dashboard') }}</a>
        <a href="{{ route('resources', ['lang' => $locale]) }}">{{ __('assessment.ui.resource_library') }}</a>
    </nav>
    <nav class="tool-menu" aria-label="{{ __('assessment.ui.tools') }}">
        <details>
            <summary>
                <span>{{ __('assessment.ui.tools') }}</span>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                    <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </summary>
            <div class="tool-menu-panel">
                <a href="{{ route('tools.goal', ['lang' => $locale]) }}">{{ __('assessment.ui.goal_builder') }}</a>
                <a href="{{ route('tools.planner', ['lang' => $locale]) }}">{{ __('assessment.ui.daily_planner') }}</a>
                <a href="{{ route('tools.time', ['lang' => $locale]) }}">{{ __('assessment.ui.time_block') }}</a>
                <a href="{{ route('tools.body', ['lang' => $locale]) }}">{{ __('assessment.ui.body_needs') }}</a>
                <a href="{{ route('tools.motivation', ['lang' => $locale]) }}">{{ __('assessment.ui.motivation_menu') }}</a>
                <a href="{{ route('tools.routine', ['lang' => $locale]) }}">{{ __('assessment.ui.routine_builder') }}</a>
                <a href="{{ route('tools.weekly', ['lang' => $locale]) }}">{{ __('assessment.ui.weekly_reset') }}</a>
                <a href="{{ route('tools.communication', ['lang' => $locale]) }}">{{ __('assessment.ui.communication_repair') }}</a>
                <a href="{{ route('tools.energy', ['lang' => $locale]) }}">{{ __('assessment.ui.energy_crash') }}</a>
                <a href="{{ route('tools.decision', ['lang' => $locale]) }}">{{ __('assessment.ui.decision_priority') }}</a>
                <a href="{{ route('tools.focus', ['lang' => $locale]) }}">{{ __('assessment.ui.focus_sprint') }}</a>
                <a href="{{ route('tools.emotion', ['lang' => $locale]) }}">{{ __('assessment.ui.emotional_reset') }}</a>
                <a href="{{ route('tools.transition', ['lang' => $locale]) }}">{{ __('assessment.ui.transition_rescue') }}</a>
                <a href="{{ route('tools.appointment', ['lang' => $locale]) }}">{{ __('assessment.ui.appointment_prep') }}</a>
                <a href="{{ route('tools.care', ['lang' => $locale]) }}">{{ __('assessment.ui.care_notes') }}</a>
                <a href="{{ route('tools.support', ['lang' => $locale]) }}">{{ __('assessment.ui.support_request') }}</a>
                <a href="{{ route('tools.home', ['lang' => $locale]) }}">{{ __('assessment.ui.home_reset') }}</a>
                <a href="{{ route('tools.money', ['lang' => $locale]) }}">{{ __('assessment.ui.money_admin') }}</a>
                <a href="{{ route('tools.providers', ['lang' => $locale]) }}">{{ __('assessment.ui.provider_shortlist') }}</a>
                <a href="{{ route('tools.access', ['lang' => $locale]) }}">{{ __('assessment.ui.access_plan') }}</a>
                <a href="{{ route('tools.task', ['lang' => $locale]) }}">{{ __('assessment.ui.task_breakdown') }}</a>
                <a href="{{ route('tools.reminders', ['lang' => $locale]) }}">{{ __('assessment.ui.reminders') }}</a>
                <a href="{{ route('tools.tracker', ['lang' => $locale]) }}">{{ __('assessment.ui.symptom_tracker') }}</a>
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
