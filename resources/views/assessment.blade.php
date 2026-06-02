<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('assessment.meta.title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="shell" data-sections='@json($sections)' data-ui='@json($ui)' data-score-url="{{ route('assessment.score') }}">
        <section class="top-bar" aria-labelledby="page-title">
            <div>
                <p class="eyebrow">{{ __('assessment.meta.eyebrow') }}</p>
                <h1 id="page-title">{{ __('assessment.meta.heading') }}</h1>
                <p class="lede">{{ __('assessment.meta.lede') }}</p>
            </div>
            <nav class="language-switcher" aria-label="{{ __('assessment.ui.language') }}">
                @foreach ($locales as $code => $label)
                    <a href="{{ route('assessment.show', ['lang' => $code]) }}" @class(['active' => $locale === $code])>{{ $label }}</a>
                @endforeach
            </nav>
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
            <div class="result-metrics">
                <div><span>{{ __('assessment.ui.inattentive_signs') }}</span><strong id="inattentiveCount"></strong></div>
                <div><span>{{ __('assessment.ui.hyperactive_signs') }}</span><strong id="hyperactiveCount"></strong></div>
                <div><span>{{ __('assessment.ui.threshold_used') }}</span><strong id="thresholdUsed"></strong></div>
            </div>
            <div class="context-grid" id="contextGrid"></div>
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
