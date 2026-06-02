<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADHD Screening Companion</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="shell" data-sections='@json($sections)' data-score-url="{{ route('assessment.score') }}">
        <section class="top-bar" aria-labelledby="page-title">
            <div>
                <p class="eyebrow">Gentle ADHD screening</p>
                <h1 id="page-title">One calm question at a time.</h1>
                <p class="lede">DSM-5-informed symptoms, ASRS-style adult wording, and clinical context checks. Screening only, not a diagnosis.</p>
            </div>
            <div class="score-strip" aria-live="polite">
                <div><span>Focus points</span><strong id="pointsLabel">0</strong></div>
                <div><span>Checkpoint</span><strong id="sectionLabel">Start</strong></div>
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
                    <button type="button" class="secondary-button" id="backButton">Back</button>
                    <span id="motivationLabel">Pick the answer that feels closest.</span>
                    <button type="button" class="primary-button" id="nextButton">Next</button>
                </div>
            </section>
        </section>

        <section class="results-panel" id="resultsPanel" hidden>
            <div>
                <p class="eyebrow">Your screening result</p>
                <h2 id="resultTitle"></h2>
                <p id="resultSummary"></p>
            </div>
            <div class="result-metrics">
                <div><span>Inattentive signs</span><strong id="inattentiveCount"></strong></div>
                <div><span>Hyperactive/impulsive signs</span><strong id="hyperactiveCount"></strong></div>
                <div><span>Threshold used</span><strong id="thresholdUsed"></strong></div>
            </div>
            <div class="context-grid" id="contextGrid"></div>
            <div>
                <h3>Thoughtful next steps</h3>
                <ul id="nextSteps"></ul>
            </div>
            <p class="disclaimer" id="resultDisclaimer"></p>
            <button type="button" class="secondary-button" id="restartButton">Start over</button>
        </section>
    </main>
</body>
</html>
