@extends('layouts.app')

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
    $copy = $tools['dashboard'];
    $today = $tools['toolkit']['today'];
    $tips = $tools['resources']['cards'];
@endphp

@section('title', __('assessment.ui.dashboard'))

@section('content')
    <main id="main" class="tool-shell dashboard-shell" data-tool="dashboard" data-locale="{{ $locale }}" data-tools='@json($tools)' data-links='@json($checkInLinks)' data-guide-url="{{ route('dashboard.ai', ['lang' => $locale]) }}">
        <section class="today-hero" aria-labelledby="todayTitle">
            <p class="today-date" id="todayDate"></p>
            <h1 id="todayTitle">{{ $copy['title'] }}</h1>
            <p class="lede">{{ $copy['intro'] }}</p>
            <div class="hero-actions">
                <a class="primary-link" href="{{ route('tools.checkin', ['lang' => $locale]) }}">{{ $copy['checkin']['action'] }}</a>
                <a class="secondary-link" href="{{ route('assessment.show', ['lang' => $locale]) }}" id="heroScreeningLink">{{ $today['screening_start'] }}</a>
            </div>
        </section>

        <section class="today-bento" aria-label="{{ $copy['eyebrow'] }}">
            <article class="bento-card bento-wide bento-checkin dashboard-checkin" aria-labelledby="dashboardCheckInTitle">
                <div>
                    <p class="card-kicker"><span class="pebble" aria-hidden="true"></span>{{ $copy['checkin']['eyebrow'] }}</p>
                    <h2 id="dashboardCheckInTitle">{{ $copy['checkin']['title'] }}</h2>
                    <p>{{ $copy['checkin']['body'] }}</p>
                </div>
                <div class="dashboard-checkin-panel" aria-live="polite">
                    <span id="dashboardCheckInState">{{ $copy['checkin']['today']['empty_state'] }}</span>
                    <strong id="dashboardCheckInMood">{{ $copy['checkin']['today']['empty_mood'] }}</strong>
                    <p id="dashboardCheckInFirst">{{ $copy['checkin']['today']['empty_first'] }}</p>
                    <div class="dashboard-checkin-tools" id="dashboardCheckInTools" aria-label="{{ $copy['checkin']['today']['tools'] }}"></div>
                    <a class="primary-link" id="dashboardCheckInLink" href="{{ route('tools.checkin', ['lang' => $locale]) }}">{{ $copy['checkin']['action'] }}</a>
                </div>
            </article>

            <article class="bento-card bento-screening" aria-labelledby="dashboardScreeningHeading">
                <p class="card-kicker"><span class="pebble pebble-feelings" aria-hidden="true"></span>{{ __('assessment.meta.eyebrow') }}</p>
                <h2 id="dashboardScreeningHeading">{{ $today['screening_title'] }}</h2>
                <strong class="screening-result" id="dashboardScreeningResult" hidden></strong>
                <p id="dashboardScreeningBody" data-saved="{{ $today['screening_saved'] }}">{{ $today['screening_empty'] }}</p>
                <a class="secondary-link" id="dashboardScreeningLink" href="{{ route('assessment.show', ['lang' => $locale]) }}" data-result-label="{{ $today['screening_view'] }}">{{ $today['screening_start'] }}</a>
            </article>

            <article class="bento-card bento-next dashboard-next printable-tool">
                <p class="card-kicker"><span class="pebble pebble-body" aria-hidden="true"></span>{{ $copy['next_eyebrow'] }}</p>
                <h2 id="dashboardNextTitle">{{ $copy['empty_title'] }}</h2>
                <p id="dashboardNextBody">{{ $copy['empty_body'] }}</p>
                <div class="tool-actions">
                    <a class="primary-link" id="dashboardNextLink" href="{{ route('tools.checkin', ['lang' => $locale]) }}">{{ $copy['start_link'] }}</a>
                </div>
            </article>

            <article class="bento-card bento-wide bento-guide dashboard-guide" aria-labelledby="dashboardGuideTitle">
                <div>
                    <p class="card-kicker"><span class="pebble pebble-life" aria-hidden="true"></span>{{ $copy['guide']['eyebrow'] }}</p>
                    <h2 id="dashboardGuideTitle">{{ $copy['guide']['title'] }}</h2>
                    <p>{{ $copy['guide']['body'] }}</p>
                    <label>
                        <span>{{ $copy['guide']['field'] }}</span>
                        <textarea id="dashboardGuideMessage" rows="3" placeholder="{{ $copy['guide']['placeholder'] }}"></textarea>
                    </label>
                    <div class="tool-actions">
                        <button type="button" class="primary-button" id="dashboardGuideButton">{{ $copy['guide']['button'] }}</button>
                    </div>
                    <p class="map-note">{{ $copy['guide']['privacy'] }}</p>
                </div>
                <div class="dashboard-guide-output" aria-live="polite">
                    <span id="dashboardGuideStatus">{{ $copy['guide']['idle'] }}</span>
                    <strong id="dashboardGuideResponse">{{ $copy['guide']['empty_response'] }}</strong>
                    <p id="dashboardGuideFirst">{{ $copy['guide']['empty_first'] }}</p>
                    <a class="secondary-link" id="dashboardGuideLink" href="{{ route('tools.navigator', ['lang' => $locale]) }}">{{ $copy['guide']['empty_link'] }}</a>
                </div>
            </article>
        </section>

        <section class="today-section reveal dashboard-triage" aria-labelledby="dashboardTriageTitle">
            <div class="section-head">
                <p class="kicker">{{ $copy['triage']['eyebrow'] }}</p>
                <h2 id="dashboardTriageTitle">{{ $copy['triage']['title'] }}</h2>
                <p>{{ $copy['triage']['intro'] }}</p>
            </div>
            <div class="dashboard-triage-grid">
                @foreach ($copy['triage']['items'] as $item)
                    <a href="{{ route($item['route'], ['lang' => $locale]) }}">
                        <span>{{ $item['label'] }}</span>
                        <strong>{{ $item['action'] }}</strong>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="today-section reveal tips" aria-roledescription="carousel" aria-labelledby="tipsTitle" data-tips>
            <div class="tips-art" aria-hidden="true"><span></span><span></span><span></span></div>
            <div>
                <div class="tips-head">
                    <h2 id="tipsTitle">{{ $tools['resources']['try_today'] }}</h2>
                    <div class="tips-controls">
                        <button type="button" class="round-button" data-tip-step="-1" aria-label="{{ $today['previous'] }}">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 3 5 8l5 5"/></svg>
                        </button>
                        <span id="tipCount" aria-live="polite" data-template="{{ $today['tip_count'] }}">{{ str_replace([':current', ':total'], [1, count($tips)], $today['tip_count']) }}</span>
                        <button type="button" class="round-button" data-tip-step="1" aria-label="{{ $today['next'] }}">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 3 5 5-5 5"/></svg>
                        </button>
                    </div>
                </div>
                @foreach ($tips as $index => $tip)
                    <figure class="tip" aria-roledescription="slide" @if ($index > 0) hidden @endif>
                        <blockquote>{{ $tip['try'] }}</blockquote>
                        <figcaption>{{ $tip['title'] }} · <a href="{{ route('resources', ['lang' => $locale]) }}">{{ __('assessment.ui.resource_library') }}</a></figcaption>
                    </figure>
                @endforeach
            </div>
        </section>

        <section class="today-section reveal your-space print-keep" id="yourSpace" aria-labelledby="dashboardUsageTitle">
            <article class="card dashboard-usage">
                @auth
                    <div>
                        <p class="card-kicker">{{ $copy['usage']['eyebrow'] }}</p>
                        <h2 id="dashboardUsageTitle">{{ $copy['usage']['title'] }}</h2>
                        <p>{{ $copy['usage']['body'] }}</p>
                    </div>
                    <div class="dashboard-usage-summary">
                        <article>
                            <span>{{ $copy['usage']['total'] }}</span>
                            <strong>{{ $usageStats['total'] }}</strong>
                        </article>
                        <article>
                            <span>{{ $copy['usage']['tools'] }}</span>
                            <strong>{{ $usageStats['tool_count'] }}</strong>
                        </article>
                        <article>
                            <span>{{ $copy['usage']['most_used'] }}</span>
                            @if ($usageStats['most_used']['href'] ?? null)
                                <a href="{{ $usageStats['most_used']['href'] }}">{{ $usageStats['most_used']['tool_label'] }}</a>
                            @else
                                <strong>{{ $usageStats['most_used']['tool_label'] ?? $copy['usage']['none'] }}</strong>
                            @endif
                        </article>
                    </div>
                    <ol class="dashboard-usage-list">
                        @forelse ($usageStats['recent'] as $usage)
                            <li>
                                @if ($usage['href'])
                                    <a href="{{ $usage['href'] }}">{{ $usage['tool_label'] }}</a>
                                @else
                                    <span>{{ $usage['tool_label'] }}</span>
                                @endif
                                <strong>{{ $usage['created_at']->diffForHumans() }}</strong>
                            </li>
                        @empty
                            <li>
                                <span>{{ $copy['usage']['empty'] }}</span>
                                <strong>{{ $copy['usage']['start'] }}</strong>
                            </li>
                        @endforelse
                    </ol>
                @else
                    <div>
                        <p class="card-kicker">{{ $copy['usage']['eyebrow'] }}</p>
                        <h2 id="dashboardUsageTitle">{{ $copy['usage']['signed_out_title'] }}</h2>
                        <p>{{ $copy['usage']['signed_out_body'] }}</p>
                    </div>
                    <a class="secondary-link" href="{{ route('register', ['lang' => $locale]) }}">{{ $copy['usage']['create_account'] }}</a>
                @endauth
            </article>

            <details class="disclosure dashboard-saved">
                <summary>
                    <span>
                        <strong>{{ $copy['saved_title'] }}</strong>
                        <em>{{ $copy['saved_intro'] }}</em>
                    </span>
                </summary>
                <div class="disclosure-body">
                    <details class="dashboard-extra-actions">
                        <summary>{{ $copy['extra_actions'] }}</summary>
                        <div>
                            <button type="button" class="secondary-button" id="dashboardExportButton">{{ $copy['export'] }}</button>
                            <button type="button" class="secondary-button" id="dashboardPrintButton">{{ $copy['print'] }}</button>
                            <button type="button" class="secondary-button" id="dashboardJsonButton">{{ $copy['json_export'] }}</button>
                            <button type="button" class="secondary-button" id="dashboardClearButton">{{ $copy['clear'] }}</button>
                        </div>
                    </details>
                    <section class="dashboard-cards" aria-label="{{ $copy['saved_title'] }}">
                        @foreach ($copy['cards'] as $key => $card)
                            <article class="dashboard-card" data-dashboard-card="{{ $key }}">
                                <span class="dashboard-status" data-dashboard-status="{{ $key }}">{{ $copy['not_saved'] }}</span>
                                <h3>{{ $card['title'] }}</h3>
                                <p data-dashboard-summary="{{ $key }}">{{ $card['empty'] }}</p>
                                <a href="{{ route($card['route'], ['lang' => $locale]) }}">{{ $card['action'] }}</a>
                            </article>
                        @endforeach
                    </section>
                </div>
            </details>

            <p class="dashboard-note">{{ $copy['privacy'] }}</p>
        </section>

        <div class="drift" aria-hidden="true">
            <div class="drift-track">
                @foreach ([false, true] as $copyOfTrack)
                    @foreach (__('assessment.ui.encouragement') as $line)
                        <span @if ($copyOfTrack) aria-hidden="true" @endif>{{ $line }}</span>
                    @endforeach
                @endforeach
            </div>
        </div>
    </main>
@endsection
