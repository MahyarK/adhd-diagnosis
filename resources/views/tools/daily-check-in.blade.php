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
    <title>{{ __('assessment.ui.daily_checkin') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="checkin" data-locale="{{ $locale }}" data-tools='@json($tools)' data-links='@json($checkInLinks)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['checkin']['eyebrow'] }}</p>
            <h1>{{ $tools['checkin']['title'] }}</h1>
            <p>{{ $tools['checkin']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="checkInForm">
                <label>
                    <span>{{ $tools['checkin']['fields']['feeling'] }}</span>
                    <select id="checkInFeeling">
                        @foreach ($tools['checkin']['feelings'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['checkin']['fields']['energy'] }}</span>
                    <select id="checkInEnergy">
                        @foreach ($tools['checkin']['energies'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['checkin']['fields']['pressure'] }}</span>
                    <select id="checkInPressure">
                        @foreach ($tools['checkin']['pressures'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['checkin']['fields']['message'] }}</span>
                    <textarea id="checkInMessage" rows="4" placeholder="{{ $tools['checkin']['placeholders']['message'] }}"></textarea>
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['checkin']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="checkInPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="checkInDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="checkInClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['checkin']['output_eyebrow'] }}</p>
                <h2 id="checkInOutputTitle">{{ $tools['checkin']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide conversation-card">
                        <h3>{{ $tools['checkin']['sections']['response'] }}</h3>
                        <p id="checkInResponseText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['checkin']['sections']['first'] }}</h3>
                        <p id="checkInFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['checkin']['sections']['tools'] }}</h3>
                        <div id="checkInToolList" class="tool-link-list"></div>
                    </article>
                    <article class="wide">
                        <h3>{{ $tools['checkin']['sections']['ai_note'] }}</h3>
                        <p id="checkInAiNote"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
