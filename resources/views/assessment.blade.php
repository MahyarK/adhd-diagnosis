@extends('layouts.app')

@php
    $screening = $ui['screening'];
    $nextLinks = [
        ['route' => 'dashboard', 'label' => __('assessment.ui.open_dashboard')],
        ['route' => 'tools.appointment', 'label' => __('assessment.ui.prepare_appointment')],
        ['route' => 'tools.care', 'label' => __('assessment.ui.care_notes')],
        ['route' => 'tools.tracker', 'label' => __('assessment.ui.track_symptoms')],
        ['route' => 'tools.providers', 'label' => __('assessment.ui.provider_shortlist')],
        ['route' => 'tools.access', 'label' => __('assessment.ui.access_plan')],
        ['route' => 'tools.reminders', 'label' => __('assessment.ui.set_reminder')],
    ];
@endphp

@section('title', __('assessment.meta.eyebrow'))

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
    <main id="main" class="shell" data-locale="{{ $locale }}" data-sections='@json($sections)' data-ui='@json($ui)' data-score-url="{{ route('assessment.score') }}">
        <section class="screening-intro" id="screeningIntro" aria-labelledby="page-title">
            <div>
                <p class="kicker">{{ __('assessment.meta.eyebrow') }}</p>
                <h1 id="page-title">{{ __('assessment.meta.heading') }}</h1>
                <p class="lede">{{ __('assessment.meta.lede') }}</p>
                <div class="hero-actions">
                    <button type="button" class="primary-button" id="beginScreening">{{ $screening['begin'] }}</button>
                    <button type="button" class="secondary-button" id="resumeScreening" hidden>{{ $screening['resume'] }}</button>
                </div>
            </div>
            <aside class="card expect" aria-labelledby="expectTitle">
                <h2 id="expectTitle">{{ $screening['expect_title'] }}</h2>
                <ol>
                    @foreach ($screening['expect'] as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ol>
            </aside>
        </section>

        <section class="assessment-layout" hidden>
            <section class="question-panel" aria-labelledby="questionText">
                <div class="question-meta">
                    <div class="section-rail" id="sectionRail"></div>
                    <span id="progressLabel" aria-live="polite"></span>
                </div>
                <div class="progress-track" aria-hidden="true"><div id="progressBar"></div></div>
                <p class="section-intro" id="sectionIntro"></p>
                <h2 id="questionText" tabindex="-1"></h2>
                <div class="answer-grid" id="answerGrid" role="group" aria-labelledby="questionText"></div>
                <div class="navigation-row">
                    <button type="button" class="secondary-button" id="backButton">{{ __('assessment.ui.back') }}</button>
                    <span id="motivationLabel" aria-live="polite">{{ __('assessment.ui.pick_closest') }}</span>
                    <button type="button" class="primary-button" id="nextButton">{{ __('assessment.ui.next') }}</button>
                </div>
                <p class="keys-hint">{{ $screening['keys_hint'] }}</p>
            </section>
        </section>

        <section class="results-panel" id="resultsPanel" aria-labelledby="resultTitle" hidden>
            <header class="result-hero printable-tool">
                <p class="kicker">{{ __('assessment.ui.your_result') }}</p>
                <h2 id="resultTitle" tabindex="-1"></h2>
                <p id="resultSummary"></p>
                <div class="hero-actions">
                    <button type="button" class="secondary-button" id="printResultButton">{{ __('assessment.ui.print_result') }}</button>
                    <button type="button" class="secondary-button" id="downloadResultButton">{{ __('assessment.ui.download_result') }}</button>
                </div>
            </header>

            <div class="profile-report" id="profileReport"></div>

            <section class="clinician-cta" id="clinicianCta">
                <div>
                    <p class="kicker">{{ __('assessment.ui.clinician_cta_eyebrow') }}</p>
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

            <section class="result-block">
                <h3>{{ __('assessment.ui.next_steps') }}</h3>
                <ul id="nextSteps"></ul>
            </section>

            <details class="disclosure score-details">
                <summary>
                    <span>
                        <strong>{{ __('assessment.ui.score_breakdown') }}</strong>
                        <em id="scoreSummary"></em>
                    </span>
                </summary>
                <div class="disclosure-body">
                    <div class="result-metrics">
                        <div><span>{{ __('assessment.ui.inattentive_signs') }}</span><strong id="inattentiveCount"></strong></div>
                        <div><span>{{ __('assessment.ui.hyperactive_signs') }}</span><strong id="hyperactiveCount"></strong></div>
                        <div><span>{{ __('assessment.ui.threshold_used') }}</span><strong id="thresholdUsed"></strong></div>
                    </div>
                    <div class="context-grid" id="contextGrid"></div>
                </div>
            </details>

            <section class="result-block action-panel" aria-labelledby="keepGoingTitle">
                <h3 id="keepGoingTitle">{{ $screening['keep_going'] }}</h3>
                <p>{{ $screening['keep_going_body'] }}</p>
                <div class="action-buttons">
                    @foreach ($nextLinks as $link)
                        <a href="{{ route($link['route'], ['lang' => $locale]) }}">{{ $link['label'] }}</a>
                    @endforeach
                    <a class="browse-all" href="{{ route('toolkit', ['lang' => $locale]) }}">{{ $screening['browse_all'] }}</a>
                </div>
            </section>

            <p class="disclaimer" id="resultDisclaimer"></p>
            <button type="button" class="secondary-button" id="restartButton">{{ __('assessment.ui.start_over') }}</button>
        </section>
    </main>
@endsection
