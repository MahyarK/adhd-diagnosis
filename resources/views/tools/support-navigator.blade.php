@php
    $navigatorLinks = [
        'planner' => route('tools.planner', ['lang' => $locale]),
        'task' => route('tools.task', ['lang' => $locale]),
        'focus' => route('tools.focus', ['lang' => $locale]),
        'motivation' => route('tools.motivation', ['lang' => $locale]),
        'wins' => route('tools.wins', ['lang' => $locale]),
        'body' => route('tools.body', ['lang' => $locale]),
        'food' => route('tools.food', ['lang' => $locale]),
        'sleep' => route('tools.sleep', ['lang' => $locale]),
        'money' => route('tools.money', ['lang' => $locale]),
        'followup' => route('tools.followup', ['lang' => $locale]),
        'reminders' => route('tools.reminders', ['lang' => $locale]),
        'access' => route('tools.access', ['lang' => $locale]),
        'aid' => route('tools.aid', ['lang' => $locale]),
        'home' => route('tools.home', ['lang' => $locale]),
        'laundry' => route('tools.laundry', ['lang' => $locale]),
        'lost' => route('tools.lost', ['lang' => $locale]),
        'appointment' => route('tools.appointment', ['lang' => $locale]),
        'care' => route('tools.care', ['lang' => $locale]),
        'providers' => route('tools.providers', ['lang' => $locale]),
        'emotion' => route('tools.emotion', ['lang' => $locale]),
        'energy' => route('tools.energy', ['lang' => $locale]),
        'support' => route('tools.support', ['lang' => $locale]),
        'workschool' => route('tools.workschool', ['lang' => $locale]),
        'communication' => route('tools.communication', ['lang' => $locale]),
        'errand' => route('tools.errand', ['lang' => $locale]),
        'transition' => route('tools.transition', ['lang' => $locale]),
        'decision' => route('tools.decision', ['lang' => $locale]),
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.support_navigator') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="navigator" data-locale="{{ $locale }}" data-tools='@json($tools)' data-links='@json($navigatorLinks)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['navigator']['eyebrow'] }}</p>
            <h1>{{ $tools['navigator']['title'] }}</h1>
            <p>{{ $tools['navigator']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="navigatorForm">
                <label>
                    <span>{{ $tools['navigator']['fields']['topic'] }}</span>
                    <select id="navigatorTopic">
                        @foreach ($tools['navigator']['topics'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['navigator']['fields']['urgency'] }}</span>
                    <select id="navigatorUrgency">
                        @foreach ($tools['navigator']['urgencies'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['navigator']['fields']['energy'] }}</span>
                    <select id="navigatorEnergy">
                        @foreach ($tools['navigator']['energies'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['navigator']['fields']['known_next'] }}</span>
                    <input id="navigatorKnownNext" type="text" placeholder="{{ $tools['navigator']['placeholders']['known_next'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['navigator']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="navigatorPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="navigatorDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="navigatorClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['navigator']['output_eyebrow'] }}</p>
                <h2 id="navigatorOutputTitle">{{ $tools['navigator']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide">
                        <h3>{{ $tools['navigator']['sections']['first'] }}</h3>
                        <p id="navigatorFirstText"></p>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['navigator']['sections']['recommended'] }}</h3>
                        <div id="navigatorRecommendationList" class="tool-link-list"></div>
                    </article>
                    <article>
                        <h3>{{ $tools['navigator']['sections']['why'] }}</h3>
                        <p id="navigatorWhyText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['navigator']['sections']['stop'] }}</h3>
                        <p id="navigatorStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
