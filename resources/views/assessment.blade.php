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
    <header class="navbar" role="banner">
        <a class="navbar-brand" href="{{ route('assessment.show') }}">
            <span class="navbar-eyebrow">{{ __('assessment.meta.eyebrow') }}</span>
            <span class="navbar-title">{{ __('assessment.meta.heading') }}</span>
        </a>
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
                        <a href="{{ route('assessment.show', ['lang' => $code]) }}" @class(['active' => $locale === $code])>{{ $label }}</a>
                    @endforeach
                </div>
            </details>
        </nav>
    </header>

    <main class="shell" data-sections='@json($sections)' data-ui='@json($ui)' data-score-url="{{ route('assessment.score') }}">
        <section class="top-bar" aria-labelledby="page-title">
            <div>
                <p class="eyebrow">{{ __('assessment.meta.eyebrow') }}</p>
                <h1 id="page-title">{{ __('assessment.meta.heading') }}</h1>
                <p class="lede">{{ __('assessment.meta.lede') }}</p>
            </div>
            <div class="score-strip" aria-live="polite">
                <div><span>{{ __('assessment.ui.focus_points') }}</span><strong id="pointsLabel">0</strong></div>
                <div><span>{{ __('assessment.ui.checkpoint') }}</span><strong id="sectionLabel">{{ __('assessment.ui.start') }}</strong></div>
                <div><span id="progressLabel">1 / 30</span><strong id="streakLabel">Ready</strong></div>
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
