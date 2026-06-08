<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('assessment.ui.sleep_wind_down') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar')

    <main class="tool-shell" data-tool="sleep" data-locale="{{ $locale }}" data-tools='@json($tools)'>
        <section class="tool-hero">
            <p class="eyebrow">{{ $tools['sleep']['eyebrow'] }}</p>
            <h1>{{ $tools['sleep']['title'] }}</h1>
            <p>{{ $tools['sleep']['intro'] }}</p>
        </section>

        <section class="tool-layout">
            <form class="tool-form" id="sleepForm">
                <label>
                    <span>{{ $tools['sleep']['fields']['mode'] }}</span>
                    <select id="sleepMode">
                        @foreach ($tools['sleep']['modes'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>{{ $tools['sleep']['fields']['wake_time'] }}</span>
                    <input id="sleepWakeTime" type="text" placeholder="{{ $tools['sleep']['placeholders']['wake_time'] }}">
                </label>
                <label>
                    <span>{{ $tools['sleep']['fields']['blocker'] }}</span>
                    <input id="sleepBlocker" type="text" placeholder="{{ $tools['sleep']['placeholders']['blocker'] }}">
                </label>
                <label>
                    <span>{{ $tools['sleep']['fields']['tomorrow'] }}</span>
                    <input id="sleepTomorrow" type="text" placeholder="{{ $tools['sleep']['placeholders']['tomorrow'] }}">
                </label>
                <label>
                    <span>{{ $tools['sleep']['fields']['screen_rule'] }}</span>
                    <input id="sleepScreenRule" type="text" placeholder="{{ $tools['sleep']['placeholders']['screen_rule'] }}">
                </label>
                <label>
                    <span>{{ $tools['sleep']['fields']['comfort'] }}</span>
                    <input id="sleepComfort" type="text" placeholder="{{ $tools['sleep']['placeholders']['comfort'] }}">
                </label>
                <div class="tool-actions">
                    <button type="submit" class="primary-button">{{ $tools['sleep']['buttons']['build'] }}</button>
                    <button type="button" class="secondary-button" id="sleepPrintButton">{{ $tools['shared']['print'] }}</button>
                    <button type="button" class="secondary-button" id="sleepDownloadButton">{{ $tools['shared']['download'] }}</button>
                    <button type="button" class="secondary-button" id="sleepClearButton">{{ $tools['shared']['clear'] }}</button>
                </div>
            </form>

            <section class="tool-output printable-tool" aria-live="polite">
                <p class="eyebrow">{{ $tools['sleep']['output_eyebrow'] }}</p>
                <h2 id="sleepOutputTitle">{{ $tools['sleep']['empty_title'] }}</h2>
                <div class="output-grid">
                    <article class="wide">
                        <h3>{{ $tools['sleep']['sections']['first'] }}</h3>
                        <p id="sleepFirstText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['sleep']['sections']['steps'] }}</h3>
                        <ol id="sleepStepsList"></ol>
                    </article>
                    <article>
                        <h3>{{ $tools['sleep']['sections']['screen'] }}</h3>
                        <p id="sleepScreenText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['sleep']['sections']['tomorrow'] }}</h3>
                        <p id="sleepTomorrowText"></p>
                    </article>
                    <article>
                        <h3>{{ $tools['sleep']['sections']['rules'] }}</h3>
                        <ul id="sleepRulesList"></ul>
                    </article>
                    <article>
                        <h3>{{ $tools['sleep']['sections']['stop'] }}</h3>
                        <p id="sleepStopText"></p>
                    </article>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
