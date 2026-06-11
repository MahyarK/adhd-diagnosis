<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('assessment.meta.title') }}</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="shell" data-locale="{{ $locale }}" data-sections='@json($sections)' data-ui='@json($ui)' data-score-url="{{ route('assessment.score') }}">
        <section class="top-bar" aria-labelledby="page-title">
            <div>
                <p class="eyebrow">{{ __('assessment.meta.eyebrow') }}</p>
                <h1 id="page-title">{{ __('assessment.meta.heading') }}</h1>
                <p class="lede">{{ __('assessment.meta.lede') }}</p>
            </div>
            <div class="score-strip" aria-live="polite">
                <div><span>{{ __('assessment.ui.focus_points') }}</span><strong id="pointsLabel">0</strong></div>
                <div><span>{{ __('assessment.ui.checkpoint') }}</span><strong id="sectionLabel">{{ __('assessment.ui.start') }}</strong></div>
                <div><span id="progressLabel">1 / 30</span><strong id="streakLabel">{{ __('assessment.ui.ready') }}</strong></div>
            </div>
        </section>

        <section class="assessment-layout">
            <section class="question-panel" aria-live="polite">
                <div class="progress-track" aria-hidden="true"><div id="progressBar"></div></div>
                <div class="section-rail" id="sectionRail" aria-label="Assessment sections"></div>
                <p class="section-intro" id="sectionIntro"></p>
                <h2 id="questionText"></h2>
                <div class="answer-grid" id="answerGrid"></div>
                <div class="navigation-row">
                    <button type="button" class="secondary-button" id="backButton">{{ __('assessment.ui.back') }}</button>
                    <span id="motivationLabel">{{ __('assessment.ui.pick_closest') }}</span>
                    <button type="button" class="primary-button" id="nextButton">{{ __('assessment.ui.next') }}</button>
                </div>
            </section>
        </section>

        <section class="results-panel" id="resultsPanel" hidden>
            <div>
                <p class="eyebrow">{{ __('assessment.ui.your_result') }}</p>
                <h2 id="resultTitle"></h2>
                <p id="resultSummary"></p>
            </div>
            <section class="action-panel">
                <div>
                    <p class="eyebrow">{{ __('assessment.ui.action_panel_eyebrow') }}</p>
                    <h3>{{ __('assessment.ui.action_panel_title') }}</h3>
                    <p>{{ __('assessment.ui.action_panel_body') }}</p>
                </div>
                <div class="action-buttons">
                    <button type="button" class="secondary-button" id="printResultButton">{{ __('assessment.ui.print_result') }}</button>
                    <button type="button" class="secondary-button" id="downloadResultButton">{{ __('assessment.ui.download_result') }}</button>
                    <a class="primary-link" href="{{ route('dashboard', ['lang' => $locale]) }}">{{ __('assessment.ui.open_dashboard') }}</a>
                    <a class="primary-link" href="{{ route('tools.navigator', ['lang' => $locale]) }}">{{ __('assessment.ui.support_navigator') }}</a>
                    <a class="primary-link" href="{{ route('tools.goal', ['lang' => $locale]) }}" id="createGoalLink">{{ __('assessment.ui.create_goal') }}</a>
                    <a class="primary-link" href="{{ route('tools.planner', ['lang' => $locale]) }}" id="buildPlanLink">{{ __('assessment.ui.build_plan') }}</a>
                    <a class="primary-link" href="{{ route('tools.time', ['lang' => $locale]) }}">{{ __('assessment.ui.time_block') }}</a>
                    <a class="primary-link" href="{{ route('tools.body', ['lang' => $locale]) }}">{{ __('assessment.ui.body_needs') }}</a>
                    <a class="primary-link" href="{{ route('tools.food', ['lang' => $locale]) }}">{{ __('assessment.ui.food_rescue') }}</a>
                    <a class="primary-link" href="{{ route('tools.sleep', ['lang' => $locale]) }}">{{ __('assessment.ui.sleep_wind_down') }}</a>
                    <a class="primary-link" href="{{ route('tools.motivation', ['lang' => $locale]) }}">{{ __('assessment.ui.motivation_menu') }}</a>
                    <a class="primary-link" href="{{ route('tools.accountability', ['lang' => $locale]) }}">{{ __('assessment.ui.accountability') }}</a>
                    <a class="primary-link" href="{{ route('tools.routine', ['lang' => $locale]) }}">{{ __('assessment.ui.routine_builder') }}</a>
                    <a class="primary-link" href="{{ route('tools.weekly', ['lang' => $locale]) }}">{{ __('assessment.ui.weekly_reset') }}</a>
                    <a class="primary-link" href="{{ route('tools.communication', ['lang' => $locale]) }}">{{ __('assessment.ui.communication_repair') }}</a>
                    <a class="primary-link" href="{{ route('tools.energy', ['lang' => $locale]) }}">{{ __('assessment.ui.energy_crash') }}</a>
                    <a class="primary-link" href="{{ route('tools.decision', ['lang' => $locale]) }}">{{ __('assessment.ui.decision_priority') }}</a>
                    <a class="primary-link" href="{{ route('tools.focus', ['lang' => $locale]) }}">{{ __('assessment.ui.focus_sprint') }}</a>
                    <a class="primary-link" href="{{ route('tools.emotion', ['lang' => $locale]) }}">{{ __('assessment.ui.emotional_reset') }}</a>
                    <a class="primary-link" href="{{ route('tools.transition', ['lang' => $locale]) }}">{{ __('assessment.ui.transition_rescue') }}</a>
                    <a class="primary-link" href="{{ route('tools.meds', ['lang' => $locale]) }}">{{ __('assessment.ui.medication_refill') }}</a>
                    <a class="primary-link" href="{{ route('tools.appointment', ['lang' => $locale]) }}">{{ __('assessment.ui.prepare_appointment') }}</a>
                    <a class="primary-link" href="{{ route('tools.care', ['lang' => $locale]) }}">{{ __('assessment.ui.care_notes') }}</a>
                    <a class="primary-link" href="{{ route('tools.support', ['lang' => $locale]) }}">{{ __('assessment.ui.support_request') }}</a>
                    <a class="primary-link" href="{{ route('tools.workschool', ['lang' => $locale]) }}">{{ __('assessment.ui.work_school_support') }}</a>
                    <a class="primary-link" href="{{ route('tools.home', ['lang' => $locale]) }}">{{ __('assessment.ui.home_reset') }}</a>
                    <a class="primary-link" href="{{ route('tools.laundry', ['lang' => $locale]) }}">{{ __('assessment.ui.laundry_rescue') }}</a>
                    <a class="primary-link" href="{{ route('tools.digital', ['lang' => $locale]) }}">{{ __('assessment.ui.digital_clutter') }}</a>
                    <a class="primary-link" href="{{ route('tools.money', ['lang' => $locale]) }}">{{ __('assessment.ui.money_admin') }}</a>
                    <a class="primary-link" href="{{ route('tools.lost', ['lang' => $locale]) }}">{{ __('assessment.ui.lost_item') }}</a>
                    <a class="primary-link" href="{{ route('tools.errand', ['lang' => $locale]) }}">{{ __('assessment.ui.errand_launch') }}</a>
                    <a class="primary-link" href="{{ route('tools.providers', ['lang' => $locale]) }}">{{ __('assessment.ui.provider_shortlist') }}</a>
                    <a class="primary-link" href="{{ route('tools.access', ['lang' => $locale]) }}">{{ __('assessment.ui.access_plan') }}</a>
                    <a class="primary-link" href="{{ route('tools.task', ['lang' => $locale]) }}">{{ __('assessment.ui.break_task') }}</a>
                    <a class="primary-link" href="{{ route('tools.followup', ['lang' => $locale]) }}">{{ __('assessment.ui.follow_up_rescue') }}</a>
                    <a class="primary-link" href="{{ route('tools.reminders', ['lang' => $locale]) }}">{{ __('assessment.ui.set_reminder') }}</a>
                    <a class="primary-link" href="{{ route('tools.tracker', ['lang' => $locale]) }}">{{ __('assessment.ui.track_symptoms') }}</a>
                </div>
            </section>
            <details class="score-details">
                <summary>
                    <span>{{ __('assessment.ui.score_breakdown') }}</span>
                    <strong id="scoreSummary"></strong>
                </summary>
                <div class="result-metrics">
                    <div><span>{{ __('assessment.ui.inattentive_signs') }}</span><strong id="inattentiveCount"></strong></div>
                    <div><span>{{ __('assessment.ui.hyperactive_signs') }}</span><strong id="hyperactiveCount"></strong></div>
                    <div><span>{{ __('assessment.ui.threshold_used') }}</span><strong id="thresholdUsed"></strong></div>
                </div>
                <div class="context-grid" id="contextGrid"></div>
            </details>
            <div class="profile-report" id="profileReport"></div>
            <section class="clinician-cta" id="clinicianCta">
                <div>
                    <p class="eyebrow">{{ __('assessment.ui.clinician_cta_eyebrow') }}</p>
                    <h3>{{ __('assessment.ui.clinician_cta_title') }}</h3>
                    <p>{{ __('assessment.ui.clinician_cta_body') }}</p>
                </div>
                <button type="button" class="primary-button" id="findCliniciansButton">{{ __('assessment.ui.find_clinicians') }}</button>
            </section>
            <section class="clinician-map-panel" id="clinicianMapPanel" hidden>
                <div class="map-header">
                    <div>
                        <h3>{{ __('assessment.ui.nearby_clinicians') }}</h3>
                        <p id="clinicianStatus">{{ __('assessment.ui.map_waiting') }}</p>
                    </div>
                    <a id="fallbackMapLink" href="https://www.google.com/maps/search/ADHD+clinician+near+me" target="_blank" rel="noreferrer">{{ __('assessment.ui.open_maps') }}</a>
                </div>
                <div id="clinicianMap" class="clinician-map" aria-label="{{ __('assessment.ui.nearby_clinicians') }}"></div>
                <div id="clinicianList" class="clinician-list"></div>
                <p class="map-note">{{ __('assessment.ui.map_note') }}</p>
            </section>
            <div>
                <h3>{{ __('assessment.ui.next_steps') }}</h3>
                <ul id="nextSteps"></ul>
            </div>
            <p class="disclaimer" id="resultDisclaimer"></p>
            <button type="button" class="secondary-button" id="restartButton">{{ __('assessment.ui.start_over') }}</button>
        </section>
    </main>
</body>
</html>
