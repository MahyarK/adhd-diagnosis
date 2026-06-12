@php
    $checkInLinks = [
        'planner' => route('tools.planner', ['lang' => $locale]),
        'body' => route('tools.body', ['lang' => $locale]),
        'food' => route('tools.food', ['lang' => $locale]),
        'sleep' => route('tools.sleep', ['lang' => $locale]),
        'task' => route('tools.task', ['lang' => $locale]),
        'focus' => route('tools.focus', ['lang' => $locale]),
        'motivation' => route('tools.motivation', ['lang' => $locale]),
        'accountability' => route('tools.accountability', ['lang' => $locale]),
        'decision' => route('tools.decision', ['lang' => $locale]),
        'energy' => route('tools.energy', ['lang' => $locale]),
        'emotion' => route('tools.emotion', ['lang' => $locale]),
        'safety' => route('tools.safety', ['lang' => $locale]),
        'money' => route('tools.money', ['lang' => $locale]),
        'followup' => route('tools.followup', ['lang' => $locale]),
        'home' => route('tools.home', ['lang' => $locale]),
        'transition' => route('tools.transition', ['lang' => $locale]),
        'time' => route('tools.time', ['lang' => $locale]),
        'digital' => route('tools.digital', ['lang' => $locale]),
        'communication' => route('tools.communication', ['lang' => $locale]),
        'goal' => route('tools.goal', ['lang' => $locale]),
        'routine' => route('tools.routine', ['lang' => $locale]),
        'wins' => route('tools.wins', ['lang' => $locale]),
        'navigator' => route('tools.navigator', ['lang' => $locale]),
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('assessment.ui.dashboard') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell dashboard-shell" data-tool="dashboard" data-locale="{{ $locale }}" data-tools='@json($tools)' data-links='@json($checkInLinks)' data-guide-url="{{ route('dashboard.ai', ['lang' => $locale]) }}">
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['dashboard']['eyebrow'] }}</p>
            <h1>{{ $tools['dashboard']['title'] }}</h1>
            <p>{{ $tools['dashboard']['intro'] }}</p>
        </section>

        <section class="dashboard-grid">
            <section class="dashboard-checkin" aria-labelledby="dashboardCheckInTitle">
                <div>
                    <p class="eyebrow">{{ $tools['dashboard']['checkin']['eyebrow'] }}</p>
                    <h2 id="dashboardCheckInTitle">{{ $tools['dashboard']['checkin']['title'] }}</h2>
                    <p>{{ $tools['dashboard']['checkin']['body'] }}</p>
                </div>
                <div class="dashboard-checkin-panel" aria-live="polite">
                    <span id="dashboardCheckInState">{{ $tools['dashboard']['checkin']['today']['empty_state'] }}</span>
                    <strong id="dashboardCheckInMood">{{ $tools['dashboard']['checkin']['today']['empty_mood'] }}</strong>
                    <p id="dashboardCheckInFirst">{{ $tools['dashboard']['checkin']['today']['empty_first'] }}</p>
                    <div class="dashboard-checkin-tools" id="dashboardCheckInTools" aria-label="{{ $tools['dashboard']['checkin']['today']['tools'] }}"></div>
                    <a class="primary-link" id="dashboardCheckInLink" href="{{ route('tools.checkin', ['lang' => $locale]) }}">{{ $tools['dashboard']['checkin']['action'] }}</a>
                </div>
            </section>

            <section class="dashboard-guide" aria-labelledby="dashboardGuideTitle">
                <div>
                    <p class="eyebrow">{{ $tools['dashboard']['guide']['eyebrow'] }}</p>
                    <h2 id="dashboardGuideTitle">{{ $tools['dashboard']['guide']['title'] }}</h2>
                    <p>{{ $tools['dashboard']['guide']['body'] }}</p>
                    <label>
                        <span>{{ $tools['dashboard']['guide']['field'] }}</span>
                        <textarea id="dashboardGuideMessage" rows="3" placeholder="{{ $tools['dashboard']['guide']['placeholder'] }}"></textarea>
                    </label>
                    <div class="tool-actions">
                        <button type="button" class="primary-button" id="dashboardGuideButton">{{ $tools['dashboard']['guide']['button'] }}</button>
                    </div>
                    <p class="map-note">{{ $tools['dashboard']['guide']['privacy'] }}</p>
                </div>
                <div class="dashboard-guide-output" aria-live="polite">
                    <span id="dashboardGuideStatus">{{ $tools['dashboard']['guide']['idle'] }}</span>
                    <strong id="dashboardGuideResponse">{{ $tools['dashboard']['guide']['empty_response'] }}</strong>
                    <p id="dashboardGuideFirst">{{ $tools['dashboard']['guide']['empty_first'] }}</p>
                    <a class="primary-link" id="dashboardGuideLink" href="{{ route('tools.navigator', ['lang' => $locale]) }}">{{ $tools['dashboard']['guide']['empty_link'] }}</a>
                </div>
            </section>

            <article class="dashboard-next printable-tool">
                <p class="eyebrow">{{ $tools['dashboard']['next_eyebrow'] }}</p>
                <h2 id="dashboardNextTitle">{{ $tools['dashboard']['empty_title'] }}</h2>
                <p id="dashboardNextBody">{{ $tools['dashboard']['empty_body'] }}</p>
                <div class="tool-actions">
                    <a class="primary-link" id="dashboardNextLink" href="{{ route('tools.checkin', ['lang' => $locale]) }}">{{ $tools['dashboard']['start_link'] }}</a>
                    <button type="button" class="secondary-button" id="dashboardExportButton">{{ $tools['dashboard']['export'] }}</button>
                    <button type="button" class="secondary-button" id="dashboardPrintButton">{{ $tools['dashboard']['print'] }}</button>
                    <button type="button" class="secondary-button" id="dashboardJsonButton">{{ $tools['dashboard']['json_export'] }}</button>
                    <button type="button" class="secondary-button" id="dashboardClearButton">{{ $tools['dashboard']['clear'] }}</button>
                </div>
            </article>

            <section class="dashboard-triage" aria-labelledby="dashboardTriageTitle">
                <div>
                    <p class="eyebrow">{{ $tools['dashboard']['triage']['eyebrow'] }}</p>
                    <h2 id="dashboardTriageTitle">{{ $tools['dashboard']['triage']['title'] }}</h2>
                    <p>{{ $tools['dashboard']['triage']['intro'] }}</p>
                </div>
                <div class="dashboard-triage-grid">
                    @foreach ($tools['dashboard']['triage']['items'] as $item)
                        <a href="{{ route($item['route'], ['lang' => $locale]) }}">
                            <span>{{ $item['label'] }}</span>
                            <strong>{{ $item['action'] }}</strong>
                        </a>
                    @endforeach
                </div>
            </section>

            <details class="dashboard-saved">
                <summary>
                    <span>{{ $tools['dashboard']['saved_title'] }}</span>
                    <strong>{{ $tools['dashboard']['saved_intro'] }}</strong>
                </summary>
                <section class="dashboard-cards" aria-label="{{ $tools['dashboard']['saved_title'] }}">
                    @foreach ($tools['dashboard']['cards'] as $key => $card)
                        <article class="dashboard-card" data-dashboard-card="{{ $key }}">
                            <span class="dashboard-status" data-dashboard-status="{{ $key }}">{{ $tools['dashboard']['not_saved'] }}</span>
                            <h3>{{ $card['title'] }}</h3>
                            <p data-dashboard-summary="{{ $key }}">{{ $card['empty'] }}</p>
                            <a href="{{ route($card['route'], ['lang' => $locale]) }}">{{ $card['action'] }}</a>
                        </article>
                    @endforeach
                </section>
            </details>
        </section>

        <p class="dashboard-note">{{ $tools['dashboard']['privacy'] }}</p>
    </main>
</body>
</html>
