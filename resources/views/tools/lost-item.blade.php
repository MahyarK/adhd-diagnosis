<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.lost_item') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="lost" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['lost']['eyebrow'] }}</p>
            <h1>{{ $tools['lost']['title'] }}</h1>
            <p>{{ $tools['lost']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="lostForm">
                <label>
                    <span>{{ $tools['lost']['fields']['item'] }}</span>
                    <input id="lostItem" type="text" placeholder="{{ $tools['lost']['placeholders']['item'] }}">
                </label>
                <label>
                    <span>{{ $tools['lost']['fields']['type'] }}</span>
                    <select id="lostType">
                        @foreach ($tools['lost']['types'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['lost']['fields']['urgency'] }}</span>
                    <select id="lostUrgency">
                        @foreach ($tools['lost']['urgencies'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['lost']['fields']['last_seen'] }}</span>
                    <input id="lostLastSeen" type="text" placeholder="{{ $tools['lost']['placeholders']['last_seen'] }}">
                </label>
                <label>
                    <span>{{ $tools['lost']['fields']['search_area'] }}</span>
                    <input id="lostSearchArea" type="text" placeholder="{{ $tools['lost']['placeholders']['search_area'] }}">
                </label>
                <label>
                    <span>{{ $tools['lost']['fields']['landing_spot'] }}</span>
                    <input id="lostLandingSpot" type="text" placeholder="{{ $tools['lost']['placeholders']['landing_spot'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['lost']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="lostPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="lostDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="lostClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['lost']['output_eyebrow'] }}</p>
                <h2 id="lostOutputTitle">{{ $tools['lost']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide">
                        <h3>{{ $tools['lost']['sections']['first'] }}</h3>
                        <p id="lostFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['lost']['sections']['search'] }}</h3>
                        <ol id="lostSearchList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['lost']['sections']['backup'] }}</h3>
                        <ul id="lostBackupList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['lost']['sections']['prevention'] }}</h3>
                        <p id="lostPreventionText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['lost']['sections']['stop'] }}</h3>
                        <p id="lostStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
